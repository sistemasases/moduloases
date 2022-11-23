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
 * Estrategia ASES
 *
 * @author     Camilo José Cruz Rivera
 * @package    block_ases
 * @copyright  2017 Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/*
 * Consultas modulo listado de docentes.
 */


// Queries from module grades record (registro de notas)

require_once(__DIR__ . '/../../../../config.php');
require_once(__DIR__ . '/../../core/module_loader.php');
require_once $CFG->libdir.'/gradelib.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/user/lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/lib/student_lib.php'; 
require_once $CFG->dirroot.'/blocks/ases/managers/periods_management/periods_lib.php'; 

module_loader("periods");

///******************************************///
///*** Get info grade_categories methods ***///
///******************************************///


    /**
     * Obtains all courses organized by their teacher where there are students from an instance
     *
     * @see get_courses_pilos($instanceid)
     * @param $instanceid id of an instance
     * @return array filled with courses
     */

function get_courses_pilos($instanceid){
    global $DB;

    
    $inicio_periodo_actual = (core_periods_get_current_period($instanceid))->fecha_inicio;    
    $inicio_periodo_actual = substr($inicio_periodo_actual, 0, 10);
    //$semestre = substr($inicio_periodo_actual,0,4) . substr($inicio_periodo_actual, 5, 2);
    //print_r($inicio_periodo_actual); die(); // DONOTCOMMIT))
    $fecha_short_name = substr($inicio_periodo_actual, 0, 7);
    $fecha_short_name = str_replace("-", "", $fecha_short_name);

    $query_courses = " SELECT DISTINCT course.id,
                                       course.fullname,
                                       course.shortname,
                                       concat_ws(' ',usuario.firstname,usuario.lastname) AS nombre
                       FROM mdl_course course
                       JOIN mdl_context cont ON cont.instanceid = course.id
                       JOIN mdl_role_assignments rol ON cont.id = rol.contextid
                       JOIN mdl_user usuario ON rol.userid = usuario.id
                       JOIN mdl_enrol enrole ON course.id = enrole.courseid
                       JOIN mdl_user_enrolments userenrol ON (enrole.id = userenrol.enrolid AND usuario.id = userenrol.userid)
                       WHERE SUBSTRING (course.shortname FROM 15 FOR 6) >= '$fecha_short_name' 
                       AND SUBSTRING(course.shortname FROM 4 FOR 7) IN (SELECT codigo_materia FROM mdl_talentospilos_materias_criti)
                       AND rol.roleid = 3	
                       AND cont.contextlevel = 50";
    $result = $DB->get_records_sql($query_courses);
    
    $result = processInfo($result);
    //print_r($result); die(); // DONOTCOMMIT))
    return $result;
}


/**
 * Obtains all teacher given a certain information
 * @see processInfo($info)
 * @param $info --> Object containing a teacher name, shortname, fullname, id 
 * @return array with syntaxis: array("$nomProfesor" => array(array("id" => $id_curso, "nombre"=>$nom_curso,"shortname"=>$shortname_curso), array(...)))
 */
function processInfo($info){
    $profesores = [];
    
    foreach ($info as $course) {
        if (isset($course->nombre)) {
            
            $profesor = $course->nombre;
            $id = $course->id;
            $nombre = $course->fullname;
            $shortname = $course->shortname;
            $curso=["id"=>$id,"nombre"=>$nombre,"shortname"=>$shortname];
            if(!isset($profesores[$profesor])){
                $profesores[$profesor] = [];
            }
            array_push($profesores[$profesor],$curso) ;
        }
    }
    return $profesores;
}
