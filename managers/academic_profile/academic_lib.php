<?php

require_once(dirname(__FILE__). '/../../../../config.php');
//require_once('../managers/student_profile/studentprofile_lib.php');
//require_once $CFG->dirroot.'/grade/report/user/lib.php'; 
//require('../../../grade/querylib.php');
// require_once $CFG->dirroot.'/grade/lib.php'; 

/**
 * Return final grade of a course for a single student
 *
 * @param string $username_student Is te username of moodlesite 
 * @return array() of stdClass object representing courses and grades for single student
 */

function get_grades_courses_student_by_semester($id_student, $coursedescripctions){
    //print_r("<br><hr>".$id_student."<hr><br>");
    global $DB;
    
    // var_dump($id_student);
    
    $id_first_semester = get_id_first_semester($id_student);
    
    //var_dump($id_first_semester);
    
    $semesters = get_semesters_stud($id_first_semester);
    
    // var_dump($semesters);
    
    // print_r($semesters);
    
    $courses = get_courses_by_student($id_student, $coursedescripctions);
    $array_semesters_courses =  array();
   
    $counter = 0;
    foreach ($semesters as $semester){
        
        $semester_object = new stdClass;
        
        $semester_object->id_semester = $semester->id;
        $semester_object->name_semester = $semester->nombre;
        $array_courses = array();
        
        $coincide =false;
        
        if ($courses){
            while($coincide = compare_date(strtotime($semester->fecha_inicio), strtotime($semester->fecha_fin), strtotime( $courses[$counter]->time_created))){
                array_push($array_courses, $courses[$counter]);
                $counter+=1;
                
                if ($counter == count($courses)){
                    break;
                }
                
            }
        }
        if($coincide || $counter != 0){
            $semester_object->courses = $array_courses;
            array_push($array_semesters_courses, $semester_object);
        }
    }
    // print_r($array_semesters_courses);
    return $array_semesters_courses; 
}

//print_r(get_grades_courses_student_by_semester(144,true));



function get_courses_by_student($id_student, $coursedescripction = false){
   
    global $DB;

    $query = "SELECT DISTINCT curso.id,
			                curso.fullname,
			                curso.shortname,
			                to_timestamp(curso.timecreated)::DATE AS time_created
			FROM mdl_course curso
			INNER JOIN mdl_enrol role ON curso.id = role.courseid
			INNER JOIN mdl_user_enrolments enrols ON enrols.enrolid = role.id
			WHERE enrols.userid = $id_student
			ORDER BY time_created DESC";
    
    $sql_query = "SELECT subcourses.id_course, name_course, tgcategories.fullname, to_timestamp(subcourses.time_created)::DATE AS time_created
                  FROM {grade_categories} as tgcategories INNER JOIN
                     (SELECT tcourse.id AS id_course, tcourse.fullname AS name_course, tcourse.timecreated AS time_created 
                     FROM {user}  AS tuser INNER JOIN {user_enrolments}  AS tenrolments ON tuser.id = tenrolments.userid
                          INNER JOIN {enrol}  AS tenrol ON  tenrolments.enrolid = tenrol.id
                          INNER JOIN {course}  AS tcourse ON tcourse.id = tenrol.courseid
                     WHERE tuser.id = $id_student) AS subcourses
                     ON subcourses.id_course = tgcategories.courseid
                  ORDER BY subcourses.time_created DESC;";
    $result_query = $DB->get_records_sql($query);
    
    if($coursedescripction){
        
        $courses_array = array();
        foreach ($result_query as $result){
            
            $result->grade = number_format (grade_get_course_grade($id_student, $result->id_course)->grade,2);
            $result->descriptions = getCoursegradelib($result->id_course, $id_student);
            array_push($courses_array, $result);
        }
        return $courses_array;
        
    }else{
        //print_r($result_query);
        return $result_query;
    }
}

//print_r(get_courses_by_student(144,false));

function getCoursegradelib($courseid, $userid){
    /// return tracking object
    //$courseid = 98;
    //$userid = 5;
    
    $context = context_course::instance($courseid);
    
    $gpr = new grade_plugin_return(array('type'=>'report', 'plugin'=>'user', 'courseid'=>$courseid, 'userid'=>$userid));
    $report = new grade_report_user($courseid, $gpr, $context, $userid);
    reduce_table($report);
    //echo "si";
    //print_grade_page_head($courseid, 'report', 'user', get_string('pluginname', 'gradereport_user'). ' - '.fullname($report->user));

     if ($report->fill_table()) {
        // print_r($report->gtree->top_element['object']->courseid);
        return $report->print_table(true);
        //return input_print_table($report);
    }
    return null;
}


/**
 * Reduce course information to display 
 *
 * @param &$report
 * @return null
 */
 function reduce_table(&$report) {
	
	$report->showpercentage = false;
	$report->showrange = false; 
	$report->showfeedback = false;
	$report->showcontributiontocoursetotal = false;
// 	$report->showgrade = false;	
	$report->setup_table();
}


/**
 * Generate the html table with de information of a grade_report_user, making input the grades
 *
 * @param $report
 * @return html
 */
 function input_print_table($report) {
         $maxspan = $report->maxdepth;
         $id_c = $report->gtree->top_element['object']->courseid ;
         $id_usuario = $report->user->id; 
           /// Build table structure
           $html = "
               <table id = '$id_c-$id_usuario'  cellspacing='0'
                      cellpadding='0'
                      summary='" . s($report->get_lang_string('tablesummary', 'gradereport_user')) . "'
                      class='boxaligncenter generaltable user-grade'>
               <thead>
                   <tr>
                       <th id='".$report->tablecolumns[0]."' class=\"header column-{$report->tablecolumns[0]}\" colspan='$maxspan'>".$report->tableheaders[0]."</th>\n";
   
           for ($i = 1; $i < count($report->tableheaders); $i++) {
               $html .= "<th id='".$report->tablecolumns[$i]."' class=\"header column-{$report->tablecolumns[$i]}\">".$report->tableheaders[$i]."</th>\n";
           }
   
           $html .= "
                   </tr>
               </thead>
               <tbody>\n";
   
           /// Print out the table data
           for ($i = 0; $i < count($report->tabledata); $i++) {
               $html .= "<tr>\n";
               if (isset($report->tabledata[$i]['leader'])) {
                   $rowspan = $report->tabledata[$i]['leader']['rowspan'];
                   $class = $report->tabledata[$i]['leader']['class'];
                   $html .= "<td class='$class' rowspan='$rowspan'></td>\n";
               }
               for ($j = 0; $j < count($report->tablecolumns); $j++) {
                   $name = $report->tablecolumns[$j];
				   if($name == 'grade'){
					   $class = (isset($report->tabledata[$i][$name]['class'])) ? $report->tabledata[$i][$name]['class'] : '';
					   $colspan = (isset($report->tabledata[$i][$name]['colspan'])) ? "colspan='".$report->tabledata[$i][$name]['colspan']."'" : '2';
					   $content = (isset($report->tabledata[$i][$name]['content'])) ? $report->tabledata[$i][$name]['content'] : null;
					   $celltype = (isset($report->tabledata[$i][$name]['celltype'])) ? $report->tabledata[$i][$name]['celltype'] : 'td';
					   $id_item = explode("_", ($report->tabledata[$i]['itemname']['id']))[1];
					   $weight = getweightofItem($id_item);
					   $id1 = "id = '" . $id_item ."-$weight'";
					   
					  
					   $headers = (isset($report->tabledata[$i][$name]['headers'])) ? "headers='{$report->tabledata[$i][$name]['headers']}'" : '';
					   
			    		   if (isset($content)) {
					       
                            if (!isTotal($report->tabledata[$i]['itemname']['content'])) {
					          $aggregation = getAggregationofItem($id_item,$id_c);
					          $id2 = "id = '" . $aggregation ."'";
    						  $html .= "<$celltype $id2 $headers class='$class' $colspan> <input  $id1 onkeypress='return pulsar(event)' class='item' value=$content readonly/></$celltype>\n";//INPUT
    						}else{
    						  $aggregation = getAggregationofTotal($id_item,$id_c);
    						  $id2 = "id = '" . $aggregation ."'";
    						  $html .= "<$celltype $id2 $headers class='$class' $colspan> <input  $id1 onkeypress='return pulsar(event)' class='total' value=$content readonly/></$celltype>\n";//INPUT
    						   //$html .= "<$celltype $id2 $headers class='$class' $colspan >$content</$celltype>\n";//INPUT
						}}
				   }else{
					   $class = (isset($report->tabledata[$i][$name]['class'])) ? $report->tabledata[$i][$name]['class'] : '';
					   $colspan = (isset($report->tabledata[$i][$name]['colspan'])) ? "colspan='".$report->tabledata[$i][$name]['colspan']."'" : '';
					   $content = (isset($report->tabledata[$i][$name]['content'])) ? $report->tabledata[$i][$name]['content'] : null;
					   $celltype = (isset($report->tabledata[$i][$name]['celltype'])) ? $report->tabledata[$i][$name]['celltype'] : 'td';
					   $id = (isset($report->tabledata[$i][$name]['id'])) ? "id='{$report->tabledata[$i][$name]['id']}'" : '';
					   $headers = (isset($report->tabledata[$i][$name]['headers'])) ? "headers='{$report->tabledata[$i][$name]['headers']}'" : '';
					   if (isset($content)) {
						   $html .= "<$celltype $id $headers class='$class' $colspan>$content</$celltype>\n"; 
						}
				   }
               }
               $html .= "</tr>\n";
           }
   
           $html .= "</tbody></table>";
   
       
               return $html;
           
       }


function isTotal($string){
    if(stripos($string, "Total") === false){
        return false;
    }else{
        return true;
    }
    
}

function getweightofItem($itemid){
    global $DB;
    
    $sql_query = "SELECT aggregationcoef as weight 
                  FROM {grade_items}
                  WHERE id = ".$itemid;
                  
    $output = $DB->get_record_sql($sql_query);
    $weight = $output->weight;
    
    return $weight;
}

function getAggregationofItem($itemid,$courseid){
    global $DB;
    
    
    $sql_query = "
        SELECT cat.aggregation as aggregation, cat.id as id
        FROM {grade_items} as items INNER JOIN {grade_categories} as cat ON (items.categoryid = cat.id)
        WHERE items.courseid = '$courseid' AND items.id = '$itemid';";

    $output = $DB->get_record_sql($sql_query);
    // print_r($output);
    $aggregation = $output->aggregation ;
    $id = $output->id;

    
    
    $respuesta = $aggregation."-".$id;
    
    return $respuesta;
}
// getAggregationofItem('64','100');

function getAggregationofTotal($itemid,$courseid){
    global $DB;
    
    $sql_query = "
        SELECT cat.aggregation as aggregation, cat.id as id
        FROM {grade_items} as items INNER JOIN {grade_categories} as cat ON (items.iteminstance = cat.id)
        WHERE items.courseid = '$courseid' AND items.id = '$itemid';";
    $output = $DB->get_record_sql($sql_query);
    // print_r($output);

    $aggregation = $output->aggregation ;
    $id = $output->id;

    
    
    $respuesta = $aggregation."-".$id;
    
    return $respuesta;
}


