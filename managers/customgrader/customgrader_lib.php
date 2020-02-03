<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Grader Lib
 *
 * @author     Camilo José Cruz Rivera
 * @package    custom_grader
 * @copyright  2018 Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// Queries from module grades record (registro de notas)
require_once (__DIR__ . '/../../../../config.php');
global $CFG;
require_once (__DIR__ . '/../../classes/custom_grade_report_grader.php');
require_once $CFG->libdir . '/gradelib.php';
require_once $CFG->libdir . '/datalib.php';
require_once $CFG->dirroot . '/grade/lib.php';
require_once $CFG->dirroot . '/grade/report/user/lib.php';
require_once $CFG->dirroot . '/blocks/ases/managers/lib/student_lib.php';
require_once $CFG->dirroot . '/blocks/ases/managers/lib/lib.php';
require_once $CFG->dirroot . '/grade/report/grader/lib.php';
require_once $CFG->dirroot . '/grade/edit/tree/lib.php'; // grade_edit_tree
require_once $CFG->libdir . '../../lib/gradelib.php';
use \local_customgrader\custom_grade_category;



const CATEGORY_ELEMENT = 'cat';
const ITEM_ELEMENT = 'row';
const PROMEDIO_PONDERADO = 10;
const PROMEDIO_SIMPLE = 1;

/**
 * Gets course information given its id
 * @see get_info_students($id_curso)
 * @param $id_curso --> course id
 * @return array Containing all ases students in the course
 */
function get_info_students($id_curso)
{
    global $DB;
    $query_students = "SELECT usuario.id, usuario.firstname, usuario.lastname, usuario.username
    FROM {user} usuario INNER JOIN {user_enrolments} enrols ON usuario.id = enrols.userid
    INNER JOIN {enrol} enr ON enr.id = enrols.enrolid
    INNER JOIN {course} curso ON enr.courseid = curso.id
    WHERE curso.id= $id_curso AND usuario.id IN (SELECT user_m.id
                                                FROM {user} user_m
                                                INNER JOIN {talentospilos_user_extended} extended ON user_m.id = extended.id_moodle_user
                                                INNER JOIN {talentospilos_usuario} user_t ON extended.id_ases_user = user_t.id
                                                INNER JOIN {talentospilos_est_estadoases} estado_u ON user_t.id = estado_u.id_estudiante
                                                INNER JOIN {talentospilos_estados_ases} estados ON estados.id = estado_u.id_estado_ases
                                                WHERE estados.nombre = 'seguimiento')";

    $estudiantes = $DB->get_records_sql($query_students);
    return $estudiantes;
}
////////////////////////////////////////////////////////////////////////////////////////////



///******************************************///
///*** Get info global_grade_book methods ***///
///******************************************///

/**
 * Returns a string with the teacher from a course.
 *

 * @see getTeacher($id_curso)
 * @param $id_curso --> course id
 * @return string $teacher_name
 **/

function getTeacher($id_curso)
{
    global $DB;
    $query_teacher = "SELECT concat_ws(' ',firstname,lastname) AS fullname
    FROM
      (SELECT usuario.firstname,
              usuario.lastname,
              userenrol.timecreated
       FROM {course} cursoP
       INNER JOIN {context} cont ON cont.instanceid = cursoP.id
       INNER JOIN {role_assignments} rol ON cont.id = rol.contextid
       INNER JOIN {user} usuario ON rol.userid = usuario.id
       INNER JOIN {enrol} enrole ON cursoP.id = enrole.courseid
       INNER JOIN {user_enrolments} userenrol ON (enrole.id = userenrol.enrolid
                                                    AND usuario.id = userenrol.userid)
       WHERE cont.contextlevel = 50
         AND rol.roleid = 3
         AND cursoP.id = $id_curso
       ORDER BY userenrol.timecreated ASC
       LIMIT 1) AS subc";
    $profesor = $DB->get_record_sql($query_teacher);
    return $profesor->fullname;
}

/**
 * Return the grade report for a given course id
 * @param $course_id
 * @return custom_grade_report_grader
 */
function get_grade_report($course_id,  $load_final_grades=true, $load_users=true) {
    global $USER;
    $USER->gradeediting[$course_id] = 1;

    $context = context_course::instance($course_id);

    $gpr = new grade_plugin_return(array('type' => 'report', 'plugin' => 'user', 'courseid' => $course_id));
    $report = new custom_grade_report_grader($course_id, $gpr, $context);
    $report->get_right_rows(true);
    if($load_users) $report->load_users();
    if($load_final_grades) $report->load_final_grades();
    return $report;
}

/**
 * Returns a string html table with the students, categories and their notes.
 *

 * @see get_categories_global_grade_book($id_curso)
 * @param $id_curso --> course id
 * @return string HTML table
 **/
function get_categories_global_grade_book($id_curso)
{

    $grade_book = get_grade_report($id_curso);
    return $grade_book->get_grade_table();
}

///**********************************///
///***    Update grades methods   ***///
///**********************************///

/**
 * update all grades from a course which needsupdate
 * @see update_grade_items_by_course($course_id)
 * @param $course_id --> id from course to update grade_items
 * @return integer --> 1 if Ok 0 if not
 */

function update_grade_items_by_course($course_id)
{
    $grade_items = grade_item::fetch_all(array('courseid' => $course_id, 'needsupdate' => 1));
    foreach ($grade_items as $item) {
        if ($item->needsupdate === 1) {
            $item->regrading_finished();
        }
    }
    return '1';
}

class GraderInfo {
    public $course;
    public $items;
    public $students;
    public $categories;
    public $grades;
    public $levels;
}

/**
 * Return all info of grades in a course normalized
 * @param $courseid
 * @param bool $fillers
 * @return GraderInfo
 * @throws dml_exception
 */
function get_normalized_all_grade_info($courseid){

    $grade_info = new GraderInfo();
    $grade_tree_fills  = new grade_tree($courseid, true, true);
    $grade_report = get_grade_report($courseid);
    $grade_tree = new grade_tree($courseid, false);
    $items = $grade_tree->items;
    $categories = custom_grade_category::fetch_all(array('courseid'=>$courseid));
    $students =  $grade_report->users;
    $student_grades = $grade_report->get_all_grades();
    $course = get_course($courseid);
    $grade_info->course = $course;
    $grade_info->items = array_values($items);
    $grade_info->categories = array_values($categories);
    $grade_info->students = array_values($students);
    $grade_info->levels = $grade_tree_fills->get_levels();
    $grade_info->grades = array_values($student_grades);
    return $grade_info;

}

/**
 * Get student grades for a course
 * @param $courseid number
 * @return array List of student grades for a course
 */
function get_student_grades($courseid, $itemid=null, $userid=null){
    $grade_report = get_grade_report($courseid, false);
    $student_grades = $grade_report->get_all_grades($itemid, $userid);
    return array_values($student_grades);
}
/**
 * Get student grades for a item
 * @param $course_id number
 * @param $item_id number
 * @return array List of student grades for a course
 * @throws dml_exception
 */
function get_student_grades_for_item($course_id, $item_id){
    $grade_report = get_grade_report($course_id, false);
    $student_grades = $grade_report->get_all_grades($item_id);
    return array_values($student_grades);
}

/**
 * It performs the insertion of 'parcial'
 *
 * @param $course --> course id
 * @param $father --> category parent
 * @param $name --> category name
 * @param $weighted --> type of qualification(aggregation)
 * @param $weight --> weighted value
 * @return array|false --- ok-> 1 || error-> 0
 **/
function insertParcial($course, $father, $name, $weighted, $weight)
{
    global $DB;
    $transaction = $DB->start_delegated_transaction();
    /** @var grade_category|false $category_or_false */
    $category_or_false = insertCategoryParcial($course, $father, $name, $weighted, $weight);

    if ($category_or_false !== false) {
        $partial_item_or_false = insertItem($course, $category_or_false->id, $name, 0) ;
        if ($partial_item_or_false !== false) {
            $optional_item_or_false = insertItem($course, $category_or_false->id, "Opcional de " . $name, 0) ;
            if ($optional_item_or_false !== false) {
                $DB->commit_delegated_transaction($transaction);
                return array(
                    'category'=>$category_or_false,
                    'partial_item'=>$partial_item_or_false,
                    'optional_item'=>$optional_item_or_false
                );
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/**
 * Performs the insertion of a category 'parcial'. Returns the id  the created category if it's successful, 0 otherwise
 *
 * @see insertCategoryParcial($course,$father,$name,$weighted,$weight)
 * @param $course --> course id
 * @param $father --> category parent
 * @param $name --> category name
 * @param $weighted --> type of qualification(aggregation)
 * @param $weight --> weighted value
 * @return false|grade_category --- ok->id_cat || error->0
 **/
function insertCategoryParcial($course, $father, $name, $weighted, $weight)
{
    global $DB;

    //Instance an object category to use insert_record
    $object = new stdClass;
    $object->courseid = $course;
    $object->fullname = $name;
    $object->parent = $father;
    $object->aggregation = $weighted;
    $object->timecreated = time();
    $object->timemodified = $object->timecreated;
    $object->aggregateonlygraded = 0;
    $object->aggregateoutcomes = 0;

    $succes = $DB->insert_record('grade_categories', $object);

    if ($succes) {
        if (insertItem($course, $succes, $name, $weight, true) !== false) {
            return grade_category::fetch(array('id'=>$succes));
        } else {
            return false;
        }
    }

    return false;
}

/**
 * Edit category
 * @param grade_category $category
 * @return bool|grade_category
 */
function editCategory($category) {
    $edited =  edit_category(
        $category->courseid,
        $category->id,
        $category->aggregationcoef,
        $category->fullname,
        $category->parent_category,
        $category->aggregation,
        $category->courseid);
    if ( $edited ) {
        return custom_grade_category::fetch(array('id'=>$category->id));
    } else {
        return false;
    }
}

/**
 * Deprecated method Instead use insert_category($params)
 *
 * It performs the insertion of a category considering whether it is of weighted type or not,
 * then it inserts the item that represents the category. The last one is needed for the category to have a weight.
 *
 * @param $course --> course id
 * @param $father --> category parent
 * @param $name --> category name
 * @param $weighted --> type of qualification(aggregation)
 * @param $weight --> weighetd value
 * @return array|false --- ['category'=> g: grade_category, 'category_item'=>i: grade_item] || false
 **/

function insertCategory($course, $father, $name, $weighted, $weight)
{
    global $DB;

    //Instance a category object to use insert_record
    $object = new stdClass;
    $object->courseid = $course;
    $object->fullname = $name;
    $object->parent = $father;
    $object->aggregation = $weighted;
    $object->timecreated = time();
    $object->timemodified = $object->timecreated;
    $object->aggregateonlygraded = 0;
    $object->aggregateoutcomes = 0;
    $transaction = $DB->start_delegated_transaction();
    $category_id = $DB->insert_record('grade_categories', $object);

    if ($category_id) {
        /** @var grade_category|false $category_item_or_false */
        $category_item_or_false = insertItem($course, $category_id, $name, $weight, true);
        $category_item = grade_item::fetch(array('id'=>$category_item_or_false->id));
        $category = grade_category::fetch(array('id'=>$category_id));
        $category_grade_item = $category->get_grade_item();
        $category->grade_item = $category_grade_item->id;
        if ($category_item !== false) {
            $DB->commit_delegated_transaction($transaction);
            return array('category'=> $category, 'category_item'=> $category_item);
        } else {
            return false;
        }
    }
    return false;
}


/**
 * Inserts an item, either flat item or an item related to a category, the last one is needed to assign a weight in case the category were a
 * daughter of another category with weighted rating
 *
 * @see insertItem($course,$father,$name,$valsend,$item)
 * @param $course --> course id
 * @param $father --> category parent
 * @param $name --> category name
 * @param $aggregationcoef --> $aggregationcoef value
 * @param $is_category_item --> Item that'll be added
 * @return bool|int true or new id
 * @throws dml_exception
 */
function insertItem($course, $father, $name, $aggregationcoef, $is_category_item=false)
{
    global $DB;
    //Instance an object item to use insert_record
    if (!$is_category_item) {
        $object = new stdClass;
        $object->courseid = $course;
        $object->categoryid = $father;
        $object->itemname = $name;
        $object->itemnumber = 0;
        $object->itemtype = 'manual';
        $object->sortorder = getNextIndex($course);
        $object->aggregationcoef = $aggregationcoef;
        $object->grademax = 5;
    } else {
        $object = new stdClass;
        $object->courseid = $course;
        $object->itemtype = 'category';
        $object->sortorder = getNextIndex($course);
        $object->aggregationcoef = $aggregationcoef;
        $object->iteminstance = $father;
        $object->grademax = 5;
    }

    $item_id_or_false = $DB->insert_record('grade_items', $object);
    if($item_id_or_false) {
        return grade_item::fetch(array('id'=>$item_id_or_false));
    } else {
        return false;
    }
}


/**
 * @param $category grade_category
 * @return $category object
 */
function _append_category_grade_item($category) {
    $_category = (object) $category;
    $category_item =  $category->get_grade_item();
    $_category->grade_item = $category_item->id;
    return $_category;
}
function _append_category_grade_item_for_array(array $categories): array {
    return array_map(function($c) {return _append_category_grade_item($c);}, $categories);
}
function get_table_levels($courseid, $fillers = true, $category_grade_last=true){
    $grade_tree = new grade_tree($courseid, $fillers, $category_grade_last);
    return $grade_tree->get_levels();
}
//update_grade_items_by_course(9);

/**
 * Updates grades from a student
 *

 * @see update_grades_moodle($userid, $itemid, $finalgrade,$courseid)
 * @param $userid --> user id
 * @param $item --> item id
 * @param $finalgrade --> grade value
 * @param $courseid --> course id
 *
 * @return boolean --> true if there's a successful update, false otherwise.

 */

function update_grades_moodle($userid, $itemid, $finalgrade, $courseid)
{
    if (!$grade_item = grade_item::fetch(array('id' => $itemid, 'courseid' => $courseid))) { // we must verify course id here!
        return false;
    }

    if ($grade_item->update_final_grade($userid, $finalgrade, 'gradebook', false, FORMAT_MOODLE)) {
        $resp = new stdClass;
        $resp->nota = true;
        return $resp;
    } else {

        $resp = new stdClass;
        $resp->nota = false;

        return $resp;
    }

}

/**
 * Updates grades from a student
 *

 * @see update_grades_moodle($userid, $itemid, $finalgrade,$courseid)
 * @param $userid --> user id
 * @param $item --> item id
 * @param $finalgrade --> grade value
 * @param $courseid --> course id
 *
 * @return bool Return true if the grade exist and was updated, false otherwise

 */

function update_grades_moodle_($userid, $itemid, $finalgrade, $courseid)
{
    if (!$grade_item = grade_item::fetch(array('id' => $itemid, 'courseid' => $courseid))) { // we must verify course id here!
        return false;
    }
    $updated  = $grade_item->update_final_grade($userid, $finalgrade, 'gradebook', false, FORMAT_MOODLE);
    return $updated;
}






/** INSERTION METHODS **/

/**
 *  Insert category using grade core
 *
 * @param $father --> category parent
 * @param $name --> category name
 * @param $weighted --> type of qualification(aggregation)
 * @param $weight --> weighetd value
 * @return integer --- ok->1 || error->0
 */

function insert_category($course, $father, $name, $weighted, $weight)
{
    $params = array(
        'courseid' => $course,
        'fullname' => $name,
        'parent' => $father,
        'aggregation' => $weighted,
    );

    $category = grade_category::fetch($params);
    if($category->insert()){
        return 1;
    }else{
        return 0;
    }
}







/** EDITING METHODS **/

/**
 * Edit a category
 * @see edit_category($courseid, $categoryid, $weight, $name, $parentid,$aggregation)
 * @param $courseid --> course id
 * @param $categoryid --> category id
 * @param $weight --> weighted value
 * @param $name --> category name
 * @param $parentid --> parent id
 * @param $aggregation --> qualification type id
 * @return boolean true if category and item were both updated, false otherwise
 */
function edit_category($courseid, $categoryid, $aggregationcoef, $name, $parentid, $aggregation, $course_cat)
{
    if ($grade_category = grade_category::fetch(array('id' => $categoryid, 'courseid' => $courseid))) {

        $grade_item = $grade_category->get_grade_item();

        if (!$grade_category->is_course_category()) {

            if ($grade_category->fullname != $name) {
                $grade_category->fullname = $name;
            }

            if ($grade_category->parent != $parentid) {
                $grade_category->set_parent($parentid);
            }

            $parent_category = $grade_category->get_parent_category();

            if ($parent_category->aggregation != 10) {
                $grade_item->aggregationcoef = 0;
            } else if ($grade_item->aggregationcoef != $aggregationcoef) {
                $grade_item->aggregationcoef = $aggregationcoef;
            }

            if ($grade_item->aggregationcoef == 0 and $parent_category->aggregation == 10) {
                $grade_item->aggregationcoef = 1;
            }

            if ($grade_item->update()) {
                $grade_item->regrading_finished();
                $item_update = true;
            } else {
                $item_update = false;
            }
        } else {
            $item_update = true;
        }

        if ($grade_category->aggregation != $aggregation and !($aggregation === false)) {
            $grade_category->aggregation = $aggregation;
            $new_agg = true;
        }
        if ($new_agg and $grade_category->aggregation == 10) {
            // weight value = 1 to children
            $children = $grade_category->get_children();

            foreach ($children as $child) {
                $item = $child['object'];
                if ($child['type'] == 'category') {
                    $item = $item->load_grade_item();
                }

                // Set the new aggregation fields.
                $item->aggregationcoef = 1;
                $item->update();
                $item->regrading_finished();

            }

        } else if ($new_agg and $grade_category->aggregation != 10) {
            //weight value = 0 to children
            $children = $grade_category->get_children();

            foreach ($children as $child) {
                $item = $child['object'];
                if ($child['type'] == 'category') {
                    $item = $item->load_grade_item();
                }

                // Set the new aggregation fields.
                $item->aggregationcoef = 0;
                $item->update();
                $item->regrading_finished();

            }
        }

        if ($grade_category->update()) {
            $grade_item->regrading_finished();
            $category_update = true;
            $course_item = grade_item::fetch_course_item($courseid);
            $course_item->regrading_finished();
        } else {

            $category_update = false;
        }

        if ($category_update and $item_update) {
            return true;
        } else {
            return false;
        }
    }
}

/**
 * Edits an item given some specifications, including course id and weight value
 *
 * @see edit_item($courseid, $itemid, $weight, $name, $parentid)
 * @param $courseid --> course id
 * @param $itemid --> item id
 * @param $aggregationcoef --> weighted value
 * @param $name --> item name
 * @param $parentid --> parent id
 * @return boolean true if grade item was updated, false otherwise
 */
function edit_item($courseid, $itemid, $aggregationcoef, $name, $parentid)
{
    if ($grade_item = grade_item::fetch(array('id' => $itemid, 'courseid' => $courseid))) {

        if ($grade_item->itemname != $name and $name != "Enlazar a la actividad Tarea") {
            $grade_item->itemname = $name;
        }

        if ($grade_item->parentcategory != $parentid) {
            $grade_item->set_parent($parentid, false);
        }

        $parent_category = $grade_item->get_parent_category();
        if ($grade_item->itemtype != 'category' && $parent_category->aggregation != PROMEDIO_PONDERADO) {
            $grade_item->aggregationcoef = 0;
        } else if ($grade_item->aggregationcoef != $aggregationcoef) {
            $grade_item->aggregationcoef = $aggregationcoef;
        }

        if ($grade_item->aggregationcoef == 0 and $parent_category->aggregation == PROMEDIO_PONDERADO) {
            $grade_item->aggregationcoef = 1;
        }

        if ($grade_item->update()) {
            $grade_item->regrading_finished();
            $course_item = grade_item::fetch_course_item($courseid);
            $course_item->regrading_finished();

            return true;
        } else {
            return false;
        }
    }
}


/**
 * Edit item
 * @param grade_item $item
 * @return bool|grade_item
 */
function editItem($item) {
    $edited =  edit_item(
        $item->courseid,
        $item->id,
        $item->aggregationcoef,
        $item->itemname,
        $item->categoryid);
    if ( $edited ) {
        $_item = grade_item::fetch(array('id'=>$item->id));
        return $_item;
    } else {
        return false;
    }
}

/** DELETING METHODS **/


function delete_item($item_id, $courseid) {
    return delete_element($item_id, $courseid, ITEM_ELEMENT);
}
function delete_category($category_id, $courseid) {
    return delete_element($category_id, $courseid, CATEGORY_ELEMENT);
}


/**
 * Deletes an element of grading. (item or category)
 * @see delete_element($id, $courseid,$type)
 * @param $id --> element id to delete
 * @param $courseid --> course id
 * @param $type --> element type. "cat" if it's category, "row" if it's item
 * @return boolean true if it was deleted, false otherwise
 */
function delete_element($id, $courseid, $type)
{
    global $DB;
    $gpr = new grade_plugin_return(array('type' => 'edit', 'plugin' => 'tree', 'courseid' => $courseid));
    $gtree = new grade_tree($courseid, false, false);

    if ($type === 'cat') {
        $eid = "cg$id";
    } elseif ($type === 'row') {
        $eid = "ig$id";
    }

    if (!$element = $gtree->locate_element($eid)) {
        return false;
    }
    $object = $element['object'];
    $object->delete();
    //sleep(5);
    $query = "SELECT id FROM {grade_items} WHERE needsupdate = 1 AND courseid = $courseid";
    $result = $DB->get_records_sql($query);

    foreach ($result as $itemid) {
        $grade_item = grade_item::fetch(array('id' => $itemid->id, 'courseid' => $courseid));
        if (!$grade_item->is_course_item()) {
            $grade_item->aggregationcoef = 1;
            $grade_item->update();
        }
        $grade_item->regrading_finished();
    }

    return true;
}

/** AUXILIARY METHODS OF WIZARD **/

//
/**
 * Makes a query to find the last index of the sorted element corresponding to the category that is being inserted
 *
 * @see getNextIndex($course)
 * @param $course --> course id
 * @return integer
 **/
function getNextIndex($course)
{
    global $DB;
    $sql_query = "SELECT max(sortorder) FROM {grade_items} WHERE courseid=" . $course . ";";
    $output = $DB->get_record_sql($sql_query);
    $nextindex = ($output->max) + 1;
    return $nextindex;
}

/**
 * Makes an html_string with the categories tree of a course identified by $courseid
 *
 * @see getCategoriesandItems($courseid)
 * @param $courseid --> course id
 * @return string html
 **/
function getCategoriesandItems($courseid)
{

    global $DB;

    $sql_query = "SELECT {user_enrolments}.userid AS id
                FROM {enrol} INNER JOIN {user_enrolments} ON ({user_enrolments}.enrolid ={enrol}.id)
                WHERE courseid=" . $courseid . "
                LIMIT 1;";

    $userid = $DB->get_record_sql($sql_query)->id;
    $context = context_course::instance($courseid);
    //print_r($userid);

    $gpr = new grade_plugin_return(array('type' => 'report', 'plugin' => 'user', 'courseid' => $courseid, 'userid' => $userid));
    $report = new grade_report_user($courseid, $gpr, $context, $userid);
    reduce_table_categories($report);
    if ($report->fill_table()) {
        return print_table_categories($report);
    }
}

/**
 * Function that reduces grade information to display in categories tree
 *
 * @see reduce_table_categories(&$report)
 * @param &$report --> object containing grade information
 * @return null
 */
function reduce_table_categories(&$report)
{
    $report->showpercentage = false;
    $report->showrange = false;
    $report->showfeedback = false;
    $report->showcontributiontocoursetotal = false;
    $report->showweight = false;
    $report->showgrade = false;
    $report->showtotalsifcontainhidden = false;
    $report->setup_table();
}

/**
 * Returns the id of the parent category of an item
 *
 * @see get_id_parent_item($id, $courseid)
 * @param $id --> item id
 * @param $courseid --> course id
 * @return boolean
 **/
function get_id_parent_item($id, $courseid)
{
    $grade_item = grade_item::fetch(array('id' => $id, 'courseid' => $courseid));
    return ($grade_item->get_parent_category()->id);
}

/**
 * Returns the category parent id
 *
 * @see get_id_parent_category($id)
 * @param $id --> category id
 * @return boolean|string --> false if there's no id, string with the id otherwise
 */
function get_id_parent_category($id)
{
    if ($grade_category = grade_category::fetch(array('id' => $id))) {
        if (!$grade_category->is_course_category()) {
            return ($grade_category->get_parent_category()->id);
        }
    } else {
        return false;
    }
}

/**
 * Returns true if an item is a Mod type, false otherwise
 *
 * @see isItemMod($id, $courseid)
 * @param $id --> item id
 * @param $courseid --> course id
 * @return boolean
 **/
function isItemMod($id, $courseid)
{
    $grade_item = grade_item::fetch(array('id' => $id, 'courseid' => $courseid));
    return ($grade_item->is_external_item());
}

/**
 * Returns true if a category is a course type
 *
 * @see isCourseCategorie($id, $courseid)
 * @param $id --> category id
 * @param $courseid --> course id
 * @return boolean
 **/
function isCourseCategorie($id, $courseid)
{
    $grade_categorie = grade_category::fetch(array('id' => $id, 'courseid' => $courseid));
    return ($grade_categorie->is_course_category());
}

/**
 * Gets the max weight that a new item can have in a category.
 *
 * @see getMaxWeight($categoryid)
 * @param $categoryid --> category id
 * @return integer
 */
function getMaxWeight($categoryid)
{
    global $DB;
    $maxweight = 100;

    $query = "SELECT sum(peso) as total
            FROM
              (SELECT id,
                      SUM(aggregationcoef) AS peso
               FROM {grade_items}
               WHERE categoryid = $categoryid
               GROUP BY id
               UNION SELECT item.id,
                            SUM(item.aggregationcoef) AS peso
               FROM {grade_items} item
               INNER JOIN {grade_categories} cat ON item.iteminstance=cat.id
               WHERE cat.parent = $categoryid
               GROUP BY item.id)AS pesos";
    $result = $DB->get_record_sql($query);

    if ($result) {
        $weight = $result->total;
    } else {
        $weight = 0;
    }

    $maxweight = $maxweight - $weight;

    return $maxweight;
}

/**
 * Function that searches into $string "gradeitemdescriptionfiller" to determine if it's a total item
 *
 * @see isCategoryTotal($string)
 * @param $string --> Describes an item
 * @return boolean false if it's not a total item, true otherwise
 */
function isCategoryTotal($string)
{
    if (stripos($string, "gradeitemdescriptionfiller") === false && stripos($string, "Total") == false) {
        return false;
    } else {
        return true;
    }

}

/**
 * Function that searches into $string "Categoria" or "Category"
 *
 * @see isCategory($string)
 * @param $string --> Describes a potential category
 * @return boolean
 **/

function isCategory($string)
{
    if ((stripos($string, "Categoría") === false) && (stripos($string, "Category") === false)) {
        return false;
    } else {
        return true;
    }

}

/**
 * Gets an item weight.
 *
 * @see getweightofItem($itemid)
 * @param $itemid --> item id
 * @return integer
 **/
function getweightofItem($itemid)
{
    global $DB;

    $sql_query = "SELECT aggregationcoef as weight
                  FROM {grade_items}
                  WHERE id = " . $itemid;

    $output = $DB->get_record_sql($sql_query);
    if ($output) {
        $weight = $output->weight;
        return $weight;
    }
    return false;
}

/**
 * Gets a category weight.
 *
 * @param $itemid
 * @return int weight
 **/
function getweightofCategory($id)
{
    global $DB;

    $sql_query = "SELECT aggregationcoef as weight
                  FROM {grade_items} item INNER JOIN {grade_categories} cat on item.iteminstance=cat.id
                  WHERE cat.id = " . $id . " AND itemtype = 'category'";

    $output = $DB->get_record_sql($sql_query);
    if ($output) {
        $weight = $output->weight;
        return $weight;
    }
    return false;
}

/**
 * Gets element name
 *
 * @see getElementName($elementid, $type)
 * @param $elementid --> element id
 * @param $type --> 'cat' or 'it'
 * @return string
 */

 function getElementName($element, $type)
 {
    if($type == 'cat'){
         $consulta = "SELECT fullname as name from {grade_categories} where id = $element";
    }elseif($type == 'it'){
        $consulta = "SELECT itemname as name from {grade_items} where id = $element";
    }
     global $DB;
     
     $result = $DB->get_record_sql($consulta)->name;
     
     
     return $result;
 }


 


/**
 * Gets a category aggregation value
 *
 * @see getAggregationofCategory($categoryid)
 * @param $categoryid --> category id
 * @return integer
 */

function getAggregationofCategory($categoryid)
{
    global $DB;

    $sql_query = "
        SELECT aggregation
        FROM {grade_categories}
        WHERE id = '$categoryid'";
    $output = $DB->get_record_sql($sql_query);

    $aggregation = $output->aggregation;

    return $aggregation;
}

/**
 * Gets a category aggregation type.
 *
 * @see getParentCategories($id_course,$id_element,$type)
 * @param $id_course --> course id
 * @param $id_element --> element id
 * @param $type --> Category type
 * @return integer
 */

function getParentCategories($id_course, $id_element, $type)
{
    global $DB;
    if ($type == "it") {
        $query = "SELECT categoryid FROM {grade_items} WHERE id = $id_element";
        $id_parent = $DB->get_record_sql($query)->categoryid;
    } else {
        $query = "SELECT parent FROM {grade_categories} WHERE id = $id_element";
        $id_parent = $DB->get_record_sql($query)->parent; // NULL WHEN TOTAL COURSE CATEGORY
    }
    $record = new stdClass;
    if (!$id_parent) {
        $record->total = true;
        return $record;
    }
    $query_categories = "SELECT cat.id as id, cat.fullname as cat_name, cur.fullname as cur_name
                         FROM {grade_categories} cat INNER JOIN {course} cur
                         ON cat.courseid = cur.id
                         WHERE cat.courseid = $id_course";
    $output = $DB->get_records_sql($query_categories);
    $html_string = "";
    foreach ($output as $categorie) {
        if ($categorie->cat_name == '?') {
            $categorie->cat_name = $categorie->cur_name;
        }
        if ($categorie->id == $id_parent) {
            $html_string .= "<option value = '$categorie->id' selected> $categorie->cat_name </option>";
        } else {
            $html_string .= "<option value = '$categorie->id'> $categorie->cat_name </option>";
        }

    }

    $record->html = $html_string;
    return $record;
}

/**
 * Sends an email alert in case a student final grade is less than 3.0
 *
 * @see send_email_alert($userid, $itemid,$grade,$courseid)
 * @param $userid --> user id
 * @param $itemid --> item id
 * @param $grade --> grade value
 * @param $courseid --> course id
 *
 * @return boolean --> true if there's a successful update, false otherwise.
 */

function send_email_alert($userid, $itemid, $grade, $courseid)
{
    global $USER;
    global $DB;

    $resp = new stdClass;
    $resp->nota = true;

    $sending_user = $DB->get_record_sql("SELECT * FROM {user} WHERE username = 'sistemas1008'");

    $userFromEmail = new stdClass;

    $userFromEmail->email = $sending_user->email;
    $userFromEmail->firstname = $sending_user->firstname;
    $userFromEmail->lastname = $sending_user->lastname;
    $userFromEmail->maildisplay = true;
    $userFromEmail->mailformat = 1;
    $userFromEmail->id = $sending_user->id;
    $userFromEmail->alternatename = '';
    $userFromEmail->middlename = '';
    $userFromEmail->firstnamephonetic = '';
    $userFromEmail->lastnamephonetic = '';

    $user_moodle = get_full_user($userid);
    $nombre_estudiante = $user_moodle->firstname . " " . $user_moodle->lastname;

    $subject = "ALERTA ACADÉMICA $nombre_estudiante";

    $curso = $DB->get_record_sql("SELECT fullname, shortname FROM {course} WHERE id = $courseid");
    $nombre_curso = $curso->fullname . " " . $curso->shortname;
    $query_teacher = "SELECT concat_ws(' ',firstname,lastname) AS fullname
           FROM
             (SELECT usuario.firstname,
                     usuario.lastname,
                     userenrol.timecreated
              FROM {course} cursoP
              INNER JOIN {context} cont ON cont.instanceid = cursoP.id
              INNER JOIN {role_assignments} rol ON cont.id = rol.contextid
              INNER JOIN {user} usuario ON rol.userid = usuario.id
              INNER JOIN {enrol} enrole ON cursoP.id = enrole.courseid
              INNER JOIN {user_enrolments} userenrol ON (enrole.id = userenrol.enrolid
                                                           AND usuario.id = userenrol.userid)
              WHERE cont.contextlevel = 50
                AND rol.roleid = 3
                AND cursoP.id = $courseid
              ORDER BY userenrol.timecreated ASC
              LIMIT 1) AS subc";
    $profesor = $DB->get_record_sql($query_teacher)->fullname;
    $item = $DB->get_record_sql("SELECT itemname FROM {grade_items} WHERE id = $itemid");
    $itemname = $item->itemname;
    $nota = number_format($grade, 2);
    $nom_may = strtoupper($nombre_curso);
    $titulo = "<b>ALERTA ACADÉMICA CURSO $nom_may <br> PROFESOR: $profesor</b><br> ";
    $mensaje = "Se le informa que se ha presentado una alerta académica del estudiante $nombre_estudiante en el curso $nombre_curso<br>
        El estudiante ha obtenido la siguiente calificación:<br> <br> <b>$itemname: <b> $nota <br><br>
        Cordialmente<br>
        <b>Oficina TIC<br>
        Estrategia ASES<br>
        Universidad del Valle</b>";

    $user_ases = get_adds_fields_mi($userid);
    $id_tal = $user_ases->idtalentos;

    $monitor = get_assigned_monitor($id_tal);
    $nombre_monitor = $monitor->firstname . " " . $monitor->lastname;
    $saludo_mon = "Estimado monitor $nombre_monitor<br><br>";

    $monitorToEmail = new stdClass;
    $monitorToEmail->email = $monitor->email;
    $monitorToEmail->firstname = $monitor->firstname;
    $monitorToEmail->lastname = $monitor->lastname;
    $monitorToEmail->maildisplay = true;
    $monitorToEmail->mailformat = 1;
    $monitorToEmail->id = $monitor->id;
    $monitorToEmail->alternatename = '';
    $monitorToEmail->middlename = '';
    $monitorToEmail->firstnamephonetic = '';
    $monitorToEmail->lastnamephonetic = '';

    $messageHtml_mon = $titulo . $saludo_mon . $mensaje;
    $messageText_mon = html_to_text($messageHtml_mon);

    $email_result = email_to_user($monitorToEmail, $userFromEmail, $subject, $messageText_mon, $messageHtml_mon, ", ", true);

    if ($email_result != 1) {
        $resp->monitor = false;
    } else {
        $resp->monitor = true;

        $practicante = get_assigned_pract($id_tal);
        $nombre_practicante = $practicante->firstname . " " . $practicante->lastname;
        $saludo_prac = "Estimado practicante $nombre_practicante<br><br>";

        $practicanteToEmail = new stdClass;
        $practicanteToEmail->email = $practicante->email;
        $practicanteToEmail->firstname = $practicante->firstname;
        $practicanteToEmail->lastname = $practicante->lastname;
        $practicanteToEmail->maildisplay = true;
        $practicanteToEmail->mailformat = 1;
        $practicanteToEmail->id = $practicante->id;
        $practicanteToEmail->alternatename = '';
        $practicanteToEmail->middlename = '';
        $practicanteToEmail->firstnamephonetic = '';
        $practicanteToEmail->lastnamephonetic = '';

        $messageHtml_prac = $titulo . $saludo_prac . $mensaje;
        $messageText_prac = html_to_text($messageHtml_prac);

        $email_result_prac = email_to_user($practicanteToEmail, $userFromEmail, $subject, $messageText_prac, $messageHtml_prac, ", ", true);

        if ($email_result_prac != 1) {
            $resp->practicante = false;
        } else {
            $resp->practicante = true;

            $profesional = get_assigned_professional($id_tal);
            $nombre_profesional = $profesional->firstname . " " . $profesional->lastname;
            $saludo_prof = "Estimado profesional $nombre_profesional<br><br>";

            $profesionalToEmail = new stdClass;
            $profesionalToEmail->email = $profesional->email;
            $profesionalToEmail->firstname = $profesional->firstname;
            $profesionalToEmail->lastname = $profesional->lastname;
            $profesionalToEmail->maildisplay = true;
            $profesionalToEmail->mailformat = 1;
            $profesionalToEmail->id = $profesional->id;
            $profesionalToEmail->alternatename = '';
            $profesionalToEmail->middlename = '';
            $profesionalToEmail->firstnamephonetic = '';
            $profesionalToEmail->lastnamephonetic = '';

            $messageHtml_prof = $titulo . $saludo_prof . $mensaje;
            $messageText_prof = html_to_text($messageHtml_prof);

            $email_result_prof = email_to_user($profesionalToEmail, $userFromEmail, $subject, $messageText_prof, $messageHtml_prof, ", ", true);

            if ($email_result_prof != 1) {
                $resp->profesional = false;
            } else {
                $resp->profesional = true;
            }

        }
    }

    return $resp;

}
