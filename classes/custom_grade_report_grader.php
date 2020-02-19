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
 * Custom grader report for ASES utitlities
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;
require_once $CFG->dirroot . '/grade/report/grader/lib.php';
require_once (__DIR__ .'/ases_interface.php');
use local_customgrader\ases_interface;
class custom_grade_report_grader extends grade_report_grader {
    /**
     * Users array of the report grader
     * This function is only initialized when the method *$this->load_users* is called
     * @see $this->load_users
     * @var $users array
     */
    public $users;
    public $baseurl;
    public $allgrades;
    public $userselect_params;

    public function load_users($include_usernames = true, $include_is_ases= true) {
        global $CFG, $DB;

        if (!empty($this->users)) {
            return;
        }
        $this->setup_users();

        // Limit to users with a gradeable role.
        list($gradebookrolessql, $gradebookrolesparams) = $DB->get_in_or_equal(explode(',', $this->gradebookroles), SQL_PARAMS_NAMED, 'grbr0');

        // Check the status of showing only active enrolments.
        $coursecontext = $this->context->get_course_context(true);
        $defaultgradeshowactiveenrol = !empty($CFG->grade_report_showonlyactiveenrol);
        $showonlyactiveenrol = get_user_preferences('grade_report_showonlyactiveenrol', $defaultgradeshowactiveenrol);
        $showonlyactiveenrol = $showonlyactiveenrol || !has_capability('moodle/course:viewsuspendedusers', $coursecontext);

        // Limit to users with an active enrollment.
        list($enrolledsql, $enrolledparams) = get_enrolled_sql($this->context, '', 0, $showonlyactiveenrol);

        // Fields we need from the user table.
        $userfields = user_picture::fields('u', get_extra_user_fields($this->context));
        if($include_usernames) {
            $userfields.=',u.username';
        }
        // We want to query both the current context and parent contexts.
        list($relatedctxsql, $relatedctxparams) = $DB->get_in_or_equal($this->context->get_parent_context_ids(true), SQL_PARAMS_NAMED, 'relatedctx');

        // If the user has clicked one of the sort asc/desc arrows.
        if (is_numeric($this->sortitemid)) {
            $params = array_merge(array('gitemid' => $this->sortitemid), $gradebookrolesparams, $this->userwheresql_params,
                $this->groupwheresql_params, $enrolledparams, $relatedctxparams);

            $sortjoin = "LEFT JOIN {grade_grades} g ON g.userid = u.id AND g.itemid = $this->sortitemid";
            $sort = "g.finalgrade $this->sortorder, u.idnumber, u.lastname, u.firstname, u.email";
        } else {
            $sortjoin = '';
            switch($this->sortitemid) {
                case 'lastname':
                    $sort = "u.lastname $this->sortorder, u.firstname $this->sortorder, u.idnumber, u.email";
                    break;
                case 'firstname':
                    $sort = "u.firstname $this->sortorder, u.lastname $this->sortorder, u.idnumber, u.email";
                    break;
                case 'email':
                    $sort = "u.email $this->sortorder, u.firstname, u.lastname, u.idnumber";
                    break;
                case 'idnumber':
                default:
                    $sort = "u.idnumber $this->sortorder, u.firstname, u.lastname, u.email";
                    break;
            }

            $params = array_merge($gradebookrolesparams, $this->userwheresql_params, $this->groupwheresql_params, $enrolledparams, $relatedctxparams);
        }

        $sql = "SELECT $userfields
                  FROM {user} u
                  JOIN ($enrolledsql) je ON je.id = u.id
                       $this->groupsql
                       $sortjoin
                  JOIN (
                           SELECT DISTINCT ra.userid
                             FROM {role_assignments} ra
                            WHERE ra.roleid IN ($this->gradebookroles)
                              AND ra.contextid $relatedctxsql
                       ) rainner ON rainner.userid = u.id
                   AND u.deleted = 0
                   $this->userwheresql
                   $this->groupwheresql
              ORDER BY $sort";
        $studentsperpage = $this->get_students_per_page();
        $this->users = $DB->get_records_sql($sql, $params, $studentsperpage * $this->page, $studentsperpage);

        if (empty($this->users)) {
            $this->userselect = '';
            $this->users = array();
            $this->userselect_params = array();
        } else {
            list($usql, $uparams) = $DB->get_in_or_equal(array_keys($this->users), SQL_PARAMS_NAMED, 'usid0');
            $this->userselect = "AND g.userid $usql";
            $this->userselect_params = $uparams;

            // First flag everyone as not suspended.
            foreach ($this->users as $user) {
                $this->users[$user->id]->is_ases = ases_interface::is_ases_by_mdl_id($user->id);
                $this->users[$user->id]->suspendedenrolment = false;
            }

            // If we want to mix both suspended and not suspended users, let's find out who is suspended.
            if (!$showonlyactiveenrol) {
                $sql = "SELECT ue.userid
                          FROM {user_enrolments} ue
                          JOIN {enrol} e ON e.id = ue.enrolid
                         WHERE ue.userid $usql
                               AND ue.status = :uestatus
                               AND e.status = :estatus
                               AND e.courseid = :courseid
                               AND ue.timestart < :now1 AND (ue.timeend = 0 OR ue.timeend > :now2)
                      GROUP BY ue.userid";

                $time = time();
                $params = array_merge($uparams, array('estatus' => ENROL_INSTANCE_ENABLED, 'uestatus' => ENROL_USER_ACTIVE,
                    'courseid' => $coursecontext->instanceid, 'now1' => $time, 'now2' => $time));
                $useractiveenrolments = $DB->get_records_sql($sql, $params);

                foreach ($this->users as $user) {
                    $this->users[$user->id]->suspendedenrolment = !array_key_exists($user->id, $useractiveenrolments);
                }
            }
        }
        return $this->users;
    }

    /**
     * Return all student grades, if $itemid is given grades are filtered by item
     * @param null|number $itemid Item id for filter the grades if is given
     * @return array
     * @throws dml_exception
     *
     */
    public function get_all_grades($itemid=null, $userid=null) {
        global $DB;
        if(!$this->users) {
            $this->load_users();
        }
        // please note that we must fetch all grade_grades fields if we want to construct grade_grade object from it!
        $params = array_merge(array('courseid'=>$this->courseid), $this->userselect_params);
        if($itemid!==null) {
            $params['itemid']=$itemid;
        }
        if($userid!==null) {
            $params['userid']=$userid;
        }
        $sql = "SELECT g.*
                  FROM {grade_items} gi,
                       {grade_grades} g
                 WHERE g.itemid = gi.id 
                 AND gi.courseid = :courseid" .
                ($itemid!==null? 'AND gi.id=:itemid' : '' ).
                ($userid!==null? 'AND g.userid=:userid' : '' ).
                "{$this->userselect}";
        $allgradeitems = $this->get_allgradeitems();
        $userids = array_keys($this->users);
        $grades = [];
        if ($grades = $DB->get_records_sql($sql, $params)) {
            foreach ($grades as $graderec) {
                if (!empty($allgradeitems[$graderec->itemid])) {
                    $grade = new grade_grade($graderec, false);
                    array_push($grades, $grade);
                }
                if (in_array($graderec->userid, $userids) and array_key_exists($graderec->itemid, $this->gtree->get_items())) { // some items may not be present!!
                    array_push($grades, $grade);
                }
            }
        }
        return $grades;

    }
}