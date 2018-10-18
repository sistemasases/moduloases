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
require_once '../managers/periods_management/periods_lib.php';
require_once(__DIR__.'/../../vendor/autoload.php');
require_once (__DIR__.'/../../classes/DAO/BaseDAO.php');
require_once (__DIR__.'/../../classes/AsesUserExtended.php');
use function Latitude\QueryBuilder\{alias, on, fn, field, param, QueryInterface, express, criteria, identifyAll, listing};
use \Latitude\QueryBuilder\Query\SelectQuery;
function get_students_and_finalgrades($instance_id){
    global $DB;

    $students_finalgrades_array = array();

    $query = "SELECT DISTINCT row_number() over(), materias_criticas.materiacr_id, substring(courses.shortname from 0 for 14) AS course_code, courses.fullname, 
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
                            AND cursoP.id = courses.id
                        ORDER BY userenrol.timecreated ASC
                        LIMIT 1) AS subc) AS nombre_profe, 
                    student_id, student_name, student_lastname, student_code
                FROM
                {course} AS courses  

                INNER JOIN

                (SELECT DISTINCT enrols.courseid AS id_course, students.id AS student_id, students.firstname AS student_name, students.lastname AS student_lastname, 
                substring(students.username from 0 for 8) AS student_code
                FROM {cohort_members} AS members 
                INNER JOIN {cohort} AS cohorts ON cohorts.id = members.cohortid
                INNER JOIN {user_enrolments} AS enrolments ON  enrolments.userid = members.userid
                INNER JOIN {user} AS students ON enrolments.userid = students.id
                INNER JOIN {enrol} AS enrols ON enrols.id = enrolments.enrolid
                WHERE members.cohortid IN (SELECT id_cohorte
                                                FROM   {talentospilos_inst_cohorte}
                                                WHERE  id_instancia = $instance_id)) AS cursos_ases

                ON courses.id = cursos_ases.id_course  

                INNER JOIN  

                (SELECT id AS materiacr_id
                FROM {course} AS courses 
                INNER JOIN (SELECT codigo_materia 
                    FROM {talentospilos_materias_criti}) AS materias_criticas
                ON materias_criticas.codigo_materia = SUBSTR(courses.shortname, 4, 7)
                WHERE SUBSTR(courses.shortname, 15, 4) = '2018') AS materias_criticas

                ON cursos_ases.id_course = materias_criticas.materiacr_id";

    $records = $DB->get_records_sql($query);

    foreach ($records as $record) {
        $student_id = $record->student_id;
        $course_id = $record->materiacr_id;
        $record->finalgrade = get_finalgrade_by_student_and_course($student_id, $course_id);
        $record->grades = get_students_grades($student_id, $course_id);

        array_push($students_finalgrades_array, $record);
    }

    return $students_finalgrades_array;
}




function get_finalgrade_by_student_and_course($student_id, $course_id){
    global $DB;

    $query = "SELECT finalgrade 
                FROM {grade_grades} AS grades
                INNER JOIN {grade_items} items ON items.id = grades.itemid
                WHERE items.itemtype = 'course' AND grades.userid = $student_id 
                        AND items.courseid = $course_id";

    $finalgrade = $DB->get_record_sql($query)->finalgrade;

    return number_format($finalgrade, 1);
}

function get_students_grades($student_id, $course_id){
    global $DB;

    $grades = "";

    $query = "SELECT row_number() over(), substring(itemname from 0 for 5) AS it_name, finalgrade 
                FROM {grade_grades} AS grades
                INNER JOIN {grade_items} items ON items.id = grades.itemid
                WHERE items.itemtype = 'mod' AND grades.userid = $student_id 
                        AND items.courseid = $course_id AND grades.finalgrade IS NOT NULL";

    $records = $DB->get_records_sql($query);

    foreach ($records as $record){
        $formatted_grade = number_format($record->finalgrade, 1);
        $grades.= "$record->it_name : $formatted_grade ";
    }

    return $grades;
}

/**
 * Class ReporteDocente
 *
 * Dummy class for ReporteDocente
 *
 * @property $cod_materia Codigo de la materia univalle
 * @property $nomber_unidad_academica
 * @property $nombre_profesor
 * @property $numero_items_creados Numero de items creados con notas registradas
 * @property $numero_estudiantes_perdiendo Número de estudiantes ASES que van ganando la materia
 * @property $numero_estudiantes_ganando Número de estudiantes ASES que van perdiendo la materia
 * @property $numero_estudiantes_sin_nota Numero de estudiantes ASES que no tienen nota
 * @property $total_estudiantes_curso Total de estudiantes ASES que están en ese curso
 */
class ReporteDocente{};

/*
 *
 * SELECT concat_ws(' ',firstname,lastname) AS fullname
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
                            AND cursoP.id = courses.id
                        ORDER BY userenrol.timecreated ASC
                        LIMIT 1) AS subc) AS nombre_profe,
 */
/**
 * Tablas retornadas: cursoP, rol, usuario, enrole, userenrol, cont {context}
 * Columnas retornadas: usuario.id, usuario.firstname, usuario.lastname
 *
 * @return SelectQuery
 */
function _select_moodle_teachers(): SelectQuery {
    return BaseDAO::get_factory()
        ->select(
            'usuario.id',
            'usuario.firstname',
            'usuario.lastname',
            alias('cursoP.id', 'curso_profesor_id' ))
        ->from(alias('{course}', 'cursoP'))
        ->innerJoin(alias('{context}','cont'), on('cont.instanceid','cursoP.id'))
        ->innerJoin(alias('{role_assignments}','rol'), on('cont.id','rol.contextid'))
        ->innerJoin(alias('{user}','usuario'), on('rol.userid','usuario.id'))
        ->innerJoin(alias('{enrol}','enrole'), on('cursoP.id','enrole.courseid'))
        ->innerJoin(alias('{user_enrolments}','userenrol'),
            on('enrole.id', 'userenrol.enrolid')->and(
                    on('usuario.id', 'userenrol.userid')
                ))
        //->limit(1);
        ->where(field('cont.contextlevel')->eq(param(50)))
        ->andWhere(field('rol.roleid')->eq(param(3)))
        ->orderBy('userenrol.timecreated', 'ASC');
}

function _select_ases_teachers(): SelectQuery {
    return BaseDAO::get_factory()
        ->select()
        ->from(
            alias(
                subquery(_select_moodle_teachers()),
                'moodle_teachers'
            )
        ->where(field('moodle_teachers.curso_profesor_id')->in()));
}

function _select_teachers_name(): SelectQuery {
    return BaseDAO::get_factory()
        ->select(
            alias(
                fn('concat_ws', param(' '), 'firstname', 'lastname'),
        'fullname'))
        ->from(
            subquery()
        );
}



function _select_user_enrols(): SelectQuery {
    return BaseDAO::get_factory()
        ->select(
            'members.cohortid',
            alias('enrols.courseid', 'id_course'),
            alias('students.id', 'student_id') ,
            alias('students.firstname', 'student_name'),
            alias('students.lastname', 'student_lastname'),
            alias(
                express('substring(students.username from %s for %s)', param(0), param(8)),
                'student_code'
            ))
        ->from(alias('{cohort_members}', 'members'))
        ->innerJoin(alias('{cohort}', 'cohorts'), on('cohorts.id' , 'members.cohortid'))
        ->innerJoin(alias('{user_enrolments}','enrolments'), on('enrolments.userid', 'members.userid'))
        ->innerJoin(alias('{user}','students'), on('enrolments.userid', 'students.id'))
        ->innerJoin(alias('{enrol}', 'enrols'), on('enrols.id', 'enrolments.enrolid'));
}
/**
SELECT DISTINCT enrols.courseid AS id_course, students.id AS student_id, students.firstname AS student_name, students.lastname AS student_lastname,
substring(students.username from 0 for 8) AS student_code
FROM {cohort_members} AS members
INNER JOIN {cohort} AS cohorts ON cohorts.id = members.cohortid
INNER JOIN {user_enrolments} AS enrolments ON  enrolments.userid = members.userid
INNER JOIN {user} AS students ON enrolments.userid = students.id
INNER JOIN {enrol} AS enrols ON enrols.id = enrolments.enrolid
WHERE members.cohortid IN (SELECT id_cohorte
FROM   {talentospilos_inst_cohorte}
WHERE  id_instancia = $instance_id)) AS cursos_ases
 */
function _select_ids_cursos_ases($id_instancia): SelectQuery {
    $factory = BaseDAO::get_factory();
    return $factory
        ->selectDistinct()
        ->from(
            subquery(
                _select_user_enrols(),
                'user_enrols'
            ));
       /* ->where(
            field('user_enrols.cohortid')
            ->in(
                    $factory
                        ->select('id_instancia')
                        ->from(subquery(
                            _select_instancias_cohorte($id_instancia),
                            'instancias_cohorte'))
            )
        );*/

}


$sql = "
SELECT DISTINCT enrols.courseid AS id_course, students.id AS student_id, students.firstname AS student_name, students.lastname AS student_lastname,
substring(students.username from 0 for 8) AS student_code
FROM {cohort_members} AS members 
INNER JOIN {cohort} AS cohorts ON cohorts.id = members.cohortid
INNER JOIN {user_enrolments} AS enrolments ON  enrolments.userid = members.userid
INNER JOIN {user} AS students ON enrolments.userid = students.id
INNER JOIN {enrol} AS enrols ON enrols.id = enrolments.enrolid
WHERE members.cohortid IN (SELECT id_cohorte
FROM   {talentospilos_inst_cohorte}
WHERE  id_instancia =450299)";
echo '<pre>';
//print_r($DB->get_records_sql($sql));die;
echo '</pre>';

/**
 * Tablas retornadas: user_extended, ases_user, mdl_user
 *
 * Columnas retornadas:
 *  AsesUserExtended::get_column_names()
 *  AsesUser::get_column_names()
 *  firstname
 *  lastname
 *  mdl_user_id
 *
 * @return SelectQuery
 * @throws ErrorException
 */
function _select_estudiantes_ases(): SelectQuery {
    $column_names = [];
    $aditional_column_names = [
      'mdl_user.firstname',
      'mdl_user.lastname',
        alias('mdl_user.id', 'mdl_user_id')
    ];
    $column_names = array_merge($column_names, AsesUserExtended::get_column_names('user_extended'));
    $column_names = array_merge($column_names, AsesUser::get_column_names('ases_user'));
    $column_names = array_merge($column_names, $aditional_column_names);
    return BaseDAO::get_factory()
        ->select(
            listing(identifyAll($column_names)))
        ->from(
            alias('{user}', 'mdl_user'))
        ->innerJoin(
            alias(AsesUserExtended::get_table_name_for_moodle(), 'user_extended'),
            on('mdl_user.id', 'user_extended.'.AsesUserExtended::ID_MOODLE_USER))
        ->innerJoin(
            alias(AsesUser::get_table_name_for_moodle(), 'ases_user'),
            on('user_extended.'.AsesUserExtended::ID_ASES_USER, 'ases_user.'.AsesUser::ID))
        ;
}


/**
 * Return a select containing all the ases cohorts
 *
 * Tables returned:
 *  instancia_cohorte
 *
 * Columns returned:
 * All from talentospilos_inst_cohorte
 *
 * @param $id_instancia Id ASES instance
 * @param string $table_raname
 * @return SelectQuery
 */
function _select_instancias_cohorte($id_instancia): SelectQuery{
    $factory = BaseDAO::get_factory();
    $INSTANCIA_COHORTE = 'instancia_cohorte';
    return $factory
        ->select()
        ->from(alias('{talentospilos_inst_cohorte}', $INSTANCIA_COHORTE))
        ->where(field($INSTANCIA_COHORTE.'.id_instancia')->eq($id_instancia));
}

function get_datatable_array_for_finalgrade_report($instance_id){
    $columns = array();
		array_push($columns, array("title"=>"Código del curso", "name"=>"course_code", "data"=>"course_code"));
		array_push($columns, array("title"=>"Nombre del curso", "name"=>"fullname", "data"=>"fullname"));
		array_push($columns, array("title"=>"Docente", "name"=>"nombre_profe", "data"=>"nombre_profe"));
		array_push($columns, array("title"=>"Nombre del estudiante", "name"=>"student_name", "data"=>"student_name"));
        array_push($columns, array("title"=>"Apellido del estudiante", "name"=>"student_lastname", "data"=>"student_lastname"));
        array_push($columns, array("title"=>"Notas", "name"=>"grades", "data"=>"grades"));
        array_push($columns, array("title"=>"Nota final parcial", "name"=>"finalgrade", "data"=>"finalgrade"));

		$data = array(
					"bsort" => false,
					"columns" => $columns,
					"data" => get_students_and_finalgrades($instance_id),
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