<?php

require_once(dirname(__FILE__). '/../../../../config.php');



/**
 * Función que obtiene el select organizado de los usuarios asociados al curso
 * @see get_course_users_select($courseusers
 * @param $courseusers ---> array con los usuarios asociados al curso
 * @return string
 **/
function get_course_users_select($courseusers){
	$table_courseuseres="";
	$table_courseuseres.='<option value=""> ---------------------------------------</option>';
	foreach ($courseusers as $courseuser) {
    	$table_courseuseres.='<option value="'.$courseuser->codigo.'">'.$courseuser->codigo.' - '.$courseuser->nombre.' '.$courseuser->apellido.'</option>';
	}
	return $table_courseuseres;


}

/**
 * Función que obtiene el select organizado de los estudiantes matriculados al 
 * curso
 * @see get_period_select($periods)
 * @param $periods ---> periodos existentes
 * @return Array
 **/
function get_students_select($students,$name){
	$table="";
    $table.='<div class="container"><form class="form-inline">';
    $table.='<div class="form-group"><select class="form-control" id="'.$name.'">';
    foreach($students as $student){
        $table.='<option value="'.$student->username.'">'.$student->firstname.' -'.''.$student->lastname.'</option>';
     }
    $table.='</select></div>';
    return $table;
}


/**
 * Función que obtiene las opciones del  select organizado de los estudiantes matriculados al 
 * curso
 * @see get_students_option($students)
 * @param $students ---> array de estudiantes
 * @return string
 **/
function get_students_option($students){
    $table="";
    foreach($students as $student){
        $table.='<option value="'.$student->username.'">'.$student->firstname.' -'.''.$student->lastname.'</option>';
     }
    return $table;
}