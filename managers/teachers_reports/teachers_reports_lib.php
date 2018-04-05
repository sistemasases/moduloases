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
 * @author     Juan Pablo Moreno Muñoz
 * @package    block_ases
 * @copyright  2018 Juan Pablo Moreno Muñoz <moreno.juan@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__). '/../../../../config.php');


function get_created_items_per_course($instance_id){
    global $DB;

    $count_created_items = array();

    $query = "SELECT DISTINCT materias_criticas.id, SUBSTR(courses.shortname, 0, 14) AS cod_asignatura, courses.fullname, 
                users.firstname, users.lastname, COUNT(items.id) AS count_items
                FROM
                    (SELECT id
                    FROM {course} AS courses 
                        INNER JOIN (SELECT codigo_materia 
                                    FROM {talentospilos_materias_criti}) AS materias_criticas
                                    ON materias_criticas.codigo_materia = SUBSTR(courses.shortname, 4, 7)
                        WHERE SUBSTR(courses.shortname, 15, 4) = '2018') AS materias_criticas

                        INNER JOIN

                            (SELECT DISTINCT enrols.courseid
                            FROM {cohort_members} AS members 
                            INNER JOIN {cohort} AS cohorts ON cohorts.id = members.cohortid
                            INNER JOIN {user_enrolments} AS enrolments ON  enrolments.userid = members.userid
                            INNER JOIN {enrol} AS enrols ON enrols.id = enrolments.enrolid
                            WHERE members.cohortid IN (SELECT id_cohorte
                                    FROM   {talentospilos_inst_cohorte}
                                    WHERE  id_instancia = $instance_id)) AS cursos_ases

                            ON cursos_ases.courseid = materias_criticas.id

                            INNER JOIN {course} AS courses ON courses.id = materias_criticas.id
                            INNER JOIN {grade_items} AS items ON items.courseid = courses.id
                            INNER JOIN {context} AS context ON courses.id = context.instanceid
                            INNER JOIN {role_assignments} AS role_assignments ON role_assignments.contextid = context.id
                            INNER JOIN {user} AS users ON users.id = role_assignments.userid
                            INNER JOIN {role} AS roles ON roles.id = role_assignments.roleid

                            WHERE role_assignments.roleid = 3

                            GROUP BY materias_criticas.id, courses.shortname, courses.fullname, users.firstname, users.lastname";

    $results = $DB->get_records_sql($query);

    foreach($results as $result){
        $result->count_grades = get_graded_items_by_course_id($result->id);
        array_push($count_created_items, $result);
    }

    return $count_created_items;    
}

//print_r(get_created_items_per_course());


function get_graded_items_by_course_id($course_id){
    global $DB;

    $query = "SELECT count(grades.finalgrade) AS count_grades FROM 
    {course} AS courses
    INNER JOIN {grade_items} AS items ON items.courseid = courses.id
    INNER JOIN {grade_grades} AS grades ON grades.itemid = items.id
    WHERE courses.id = $course_id AND grades.finalgrade IS NOT NULL";

    $result = $DB->get_record_sql($query);

    $count_grades = $result->count_grades;

    return $count_grades;
}

function get_teachers_last_name($instance_id){
    global $DB;

    $teachers_lastnames_options = "<select><option value=''></option>";

    $query = "SELECT DISTINCT users.lastname
    FROM
      (SELECT id
      FROM {course} AS courses 
      INNER JOIN (SELECT codigo_materia 
            FROM {talentospilos_materias_criti}) AS materias_criticas
      ON materias_criticas.codigo_materia = SUBSTR(courses.shortname, 4, 7)
      WHERE SUBSTR(courses.shortname, 15, 4) = '2018') AS materias_criticas
    
      INNER JOIN
    
      (SELECT DISTINCT enrols.courseid
      FROM {cohort_members} AS members 
      INNER JOIN {cohort} AS cohorts ON cohorts.id = members.cohortid
      INNER JOIN {user_enrolments} AS enrolments ON  enrolments.userid = members.userid
      INNER JOIN {enrol} AS enrols ON enrols.id = enrolments.enrolid
      WHERE members.cohortid IN (SELECT id_cohorte
                                        FROM   {talentospilos_inst_cohorte}
                                        WHERE  id_instancia = $instance_id)) AS cursos_ases
    
      ON cursos_ases.courseid = materias_criticas.id
    
    INNER JOIN {course} AS courses ON courses.id = materias_criticas.id
    INNER JOIN {context} AS context ON courses.id = context.instanceid
    INNER JOIN {role_assignments} AS role_assignments ON role_assignments.contextid = context.id
    INNER JOIN {user} AS users ON users.id = role_assignments.userid
    INNER JOIN {role} AS roles ON roles.id = role_assignments.roleid
    
    WHERE role_assignments.roleid = 3
    
    GROUP BY users.lastname";

    $results = $DB->get_records_sql($query);

    foreach($results as $result){
        $teachers_lastnames_options.= "<option value='$result->lastname'>$result->lastname</option>";
    }

    $teachers_lastnames_options.= "</select>";

    return $teachers_lastnames_options;
}

function get_datatable_array_for_report(){
    $columns = array();
    $teachers_lastnames = get_teachers_last_name($instance_id);
		array_push($columns, array("title"=>"Código del curso", "name"=>"cod_asignatura", "data"=>"cod_asignatura"));
		array_push($columns, array("title"=>"Nombre del curso", "name"=>"fullname", "data"=>"fullname"));
		array_push($columns, array("title"=>"Nombre del docente", "name"=>"firstname", "data"=>"firstname"));
		array_push($columns, array("title"=>"Apellido del docente".$teachers_lastnames, "name"=>"lastname", "data"=>"lastname"));
        array_push($columns, array("title"=>"Cantidad de ítems", "name"=>"count_items", "data"=>"count_items"));
        array_push($columns, array("title"=>"Cantidad de notas registradas", "name"=>"count_grades", "data"=>"count_grades"));

		$data = array(
					"bsort" => false,
					"columns" => $columns,
					"data" => get_created_items_per_course($instance_id),
					"language" => 
                	 array(
                    	"search"=> "Buscar:",
                    	"oPaginate" => array(
                        	"sFirst"=>    "Primero",
                        	"sLast"=>     "Último",
                        	"sNext"=>     "Siguiente",
                        	"sPrevious"=> "Anterior"
                    		),
                		"sProcessing"=>     "Procesando...",
                		"sLengthMenu"=>     "Mostrar _MENU_ registros",
                    	"sZeroRecords"=>    "No se encontraron resultados",
                    	"sEmptyTable"=>     "Ningún dato disponible en esta tabla",
                    	"sInfo"=>           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    	"sInfoEmpty"=>      "Mostrando registros del 0 al 0 de un total de 0 registros",
                    	"sInfoFiltered"=>   "(filtrado de un total de _MAX_ registros)",
                    	"sInfoPostFix"=>    "",
                    	"sSearch"=>         "Buscar:",
                    	"sUrl"=>            "",
                    	"sInfoThousands"=>  ",",
                    	"sLoadingRecords"=> "Cargando...",
                 	),
					"order"=> array(0, "desc")

                );
    return $data;
}