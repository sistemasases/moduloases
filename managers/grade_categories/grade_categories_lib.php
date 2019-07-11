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
require_once $CFG->libdir.'/gradelib.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/user/lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/lib/student_lib.php'; 
require_once $CFG->dirroot.'/blocks/ases/managers/periods_management/periods_lib.php'; 


///******************************************///
///*** Get info grade_categories methods ***///
///******************************************///

    /**
     * Función que retorna la fecha de inicio del semestre actual
     * @see get_current_semester()
     * @return cadena de texto que representa la fecha de inicio del semestre actual
     */
function get_current_semester_start(){
    global $DB;
    $sql_query = "SELECT fecha_inicio AS fecha FROM {talentospilos_semestre} WHERE id = (SELECT MAX(id) FROM {talentospilos_semestre})";
    $current_semester = $DB->get_record_sql($sql_query);
    return $current_semester;
}
    /**
     * Obtains all courses organized by their teacher where there are students from an instance
     *
     * @see get_courses_pilos($instanceid)
     * @param $instanceid id of an instance
     * @return array filled with courses
     */

function get_courses_pilos($instanceid){
    global $DB;

    $sem = get_current_semester_start();
    $semestre = $sem->fecha;

    //print_r("raw: ");
    print_r($semestre);

    $año = substr($semestre,0,4);
    $mes = substr($semestre,5,2);
    //print_r("año: ");
    //print_r($año);
    //print_r("mes: ");
    //print_r($mes);

    $semestre = $año.$mes;

    //print_r("semestre: ");
    //print_r($semestre);
    $query_courses = "
        SELECT DISTINCT curso.id,
                        curso.fullname,
                        curso.shortname,

          (SELECT concat_ws(' ',firstname,lastname) AS fullname
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
                AND cursoP.id = curso.id
              ORDER BY userenrol.timecreated ASC
              LIMIT 1) AS subc) AS nombre
        FROM {course} curso
        INNER JOIN {enrol} ROLE ON curso.id = role.courseid
        INNER JOIN {user_enrolments} enrols ON enrols.enrolid = role.id
        WHERE SUBSTRING(curso.shortname FROM 4 FOR 7) IN (SELECT codigo_materia FROM {talentospilos_materias_criti}) AND SUBSTRING(curso.shortname FROM 15 FOR 6) = '$semestre' AND enrols.userid IN
            (SELECT user_m.id
            FROM {user} user_m
            INNER JOIN {talentospilos_user_extended} extended ON user_m.id = extended.id_moodle_user
            INNER JOIN {talentospilos_est_estadoases} estado_u ON extended.id_ases_user = estado_u.id_estudiante
            INNER JOIN {talentospilos_estados_ases} estados ON estados.id = estado_u.id_estado_ases
            WHERE estados.nombre = 'seguimiento'
            
            INTERSECT

            SELECT user_m.id
            FROM {user} user_m 
            INNER JOIN {cohort_members} memb ON user_m.id = memb.userid
            WHERE memb.cohortid IN (SELECT id_cohorte
                                    FROM   {talentospilos_inst_cohorte}
                                    WHERE  id_instancia = $instanceid))";
    $result = $DB->get_records_sql($query_courses);
    
    $result = processInfo($result);
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
    return $profesores;
}
