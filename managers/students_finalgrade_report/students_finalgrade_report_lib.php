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
use function Latitude\QueryBuilder\{alias, on, fn, field, param, literal, QueryInterface, express, criteria, identify, identifyAll, listing};
use \Latitude\QueryBuilder\Query\SelectQuery;
const TEACHER_ROLE_ID = 3;
const COURSE_CONTEXT_ID = 50;
/**
 * @param $instance_id
 * @return array
 * @throws dml_exception
 */

function get_students_and_finalgrades($instance_id){
    global $DB;

    $students_finalgrades_array = array();

    $query =
        /** @lang SQL */
        <<<SQL
SELECT DISTINCT row_number() over(), materias_criticas.materiacr_id, substring(courses.shortname from 0 for 14) AS course_code, courses.fullname, 
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

                ON cursos_ases.id_course = materias_criticas.materiacr_id
SQL;

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

/**
 * @param $courseid
 * @return bool
 * @throws dml_exception
 */
function get_cantidad_items_con_notas($courseid){
    global $DB;
    $sql = <<<SQL
SELECT COUNT(*) FROM (
    SELECT DISTINCT  ON (mdl_grade_items.id) *
    from {grade_items} AS mdl_grade_items inner join {grade_grades} AS mdl_grade_grades
    on mdl_grade_items.id = mdl_grade_grades.itemid
        WHERE mdl_grade_items.courseid = $courseid
        and mdl_grade_items.itemtype != 'category'
        and mdl_grade_items.itemtype != 'course'
    and finalgrade is not null) AS mdl_grade_items_with_at_least_one_grade
SQL;
return $DB->get_record_sql($sql);
}


/**
 * @param $courseid
 * @return bool
 * @throws dml_exception
 */
function get_cantidad_items($courseid){
    global $DB;
    $sql = <<<SQL
    SELECT COUNT(*)
    from {grade_items} AS mdl_grade_items 
        WHERE mdl_grade_items.courseid = $courseid
        and mdl_grade_items.itemtype != 'course'
        and mdl_grade_items.itemtype != 'category'
SQL;
    return $DB->get_record_sql($sql);
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



/**
 * Tablas consultadas: mdl_user, mdl_context, mdl_role_assignments, mdl_course
 * Columnas retornadas: mdl_user_id, mdl_context_id
 *
 * @return SelectQuery
 */



function _select_user_enrols(): SelectQuery {
    return BaseDAO::get_factory()
        ->select(
            'mdl_cohort_members.cohortid',
            alias('mdl_enrol.courseid', 'courseid'),
            alias('mdl_user.id', 'user_id') ,
            alias('mdl_user.firstname', 'user_name'),
            alias('mdl_user.lastname', 'user_lastname'),
            alias(
                express('substring(mdl_user.username from %s for %s)', param(0), param(8)),
                'user_code_univalle'
            ))
        ->from(alias('{cohort_members}', 'mdl_cohort_members'))
        ->innerJoin(alias('{cohort}', 'mdl_cohort'), on('mdl_cohort.id' , 'mdl_cohort_members.cohortid'))
        ->innerJoin(alias('{user_enrolments}','mdl_user_enrolments'), on('mdl_user_enrolments.userid', 'mdl_cohort_members.userid'))
        ->innerJoin(alias('{user}','mdl_user'), on('mdl_user_enrolments.userid', 'mdl_user.id'))
        ->innerJoin(alias('{enrol}', 'mdl_enrol'), on('mdl_enrol.id', 'mdl_user_enrolments.enrolid'));
}



function _select_mdl_courses() {
    return BaseDAO::get_factory()
        ->select(alias('{course}', 'mdl_course'));
}
function _select_cantidad_cursos_ases($semestre = null) {
    if(!$semestre){
        $semestre_object = get_current_semester();
        $sem = $semestre_object->nombre;
        $id_semestre = $semestre_object->max;
        $año = substr($sem,0,4);

        if(substr($sem,4,1) == 'A'){
            $semestre = $año.'02';
        }else if(substr($sem,4,1) == 'B'){
            $semestre = $año.'08';
        }
    }
    $factory = BaseDAO::get_factory();
    return $factory
        ->select(fn('count', literal('*')))
        ->from(
            subquery(
                _select_cursos_ases(),
                'mdl_courses'
            )
        );

}

function _select_moodle_teachers(): SelectQuery {
    return BaseDAO::get_factory()
        ->select(
            literal('mdl_user.*'),
            alias('mdl_user.id', 'mdl_user_id'),
            alias('mdl_context.id', 'mdl_context_id'),
            alias('mdl_role_assignments.id', 'mdl_role_assignments_id'),
            alias('mdl_course.id', 'mdl_course_id'))
        ->from(alias('{course}', 'mdl_course'))
        ->innerJoin(alias('{context}','mdl_context'), on('mdl_context.instanceid','mdl_course.id'))
        ->innerJoin(alias('{role_assignments}','mdl_role_assignments'), on('mdl_context.id','mdl_role_assignments.contextid'))
        ->innerJoin(alias('{user}','mdl_user'), on('mdl_role_assignments.userid','mdl_user.id'))
        ->innerJoin(alias('{enrol}','mdl_enrol'), on('mdl_course.id','mdl_enrol.courseid'))
        ->innerJoin(alias('{user_enrolments}','mdl_user_enrolments'),
            on('mdl_enrol.id', 'mdl_user_enrolments.enrolid')->and(
                on('mdl_user.id', 'mdl_user_enrolments.userid')
            ))
        ->where(field('mdl_context.contextlevel')->eq(param(COURSE_CONTEXT_ID)))
        ->andWhere(field('mdl_role_assignments.roleid')->eq(param(TEACHER_ROLE_ID)));
}

function _select_cursos_ases_with_teacher($semestre=null)
{
    if (!$semestre) {
        $semestre_object = get_current_semester();
        $sem = $semestre_object->nombre;
        $id_semestre = $semestre_object->max;
        $año = substr($sem, 0, 4);

        if (substr($sem, 4, 1) == 'A') {
            $semestre = $año . '02';
        } else if (substr($sem, 4, 1) == 'B') {
            $semestre = $año . '08';
        }
    }
    return _select_cursos_ases()
        ->innerJoin(
            alias('{context}', 'mdl_context'),
            on('mdl_context.instanceid', 'mdl_course.id'))
        ->addColumns(literal('mdl_context.*'))
        ->addColumns('moodle_teachers.firstname','moodle_teachers.lastname', 'moodle_teachers.mdl_context_id', 'mdl_context.id')
        ->innerJoin(
            subquery(
                _select_moodle_teachers(),
                'moodle_teachers'),
          on('moodle_teachers.mdl_context_id','mdl_context.id')
        )->limit(5);


}
function _select_cursos_ases($semestre=null): SelectQuery {
    if(!$semestre){
        $semestre_object = get_current_semester();
        $sem = $semestre_object->nombre;
        $id_semestre = $semestre_object->max;
        $año = substr($sem,0,4);

        if(substr($sem,4,1) == 'A'){
            $semestre = $año.'02';
        }else if(substr($sem,4,1) == 'B'){
            $semestre = $año.'08';
        }
    }

    $factory = BaseDAO::get_factory();
    return $factory
        ->select(
            literal('DISTINCT ON(mdl_course.fullname) mdl_course.fullname'),
            'mdl_course.fullname',
            'mdl_course.shortname',
            alias('mdl_course.id', 'mdl_course_id'))
        ->from(alias('{course}','mdl_course'))
        ->innerJoin(alias('{enrol}','mdl_enrol'), on('mdl_course.id','mdl_enrol.courseid'))
        ->innerJoin(
            alias('{user_enrolments}','mdl_user_enrolments'),
            on ('mdl_user_enrolments.enrolid','mdl_enrol.id'))
        ->where(
            criteria(
                '%s = %s ',
                express('SUBSTRING(mdl_course.shortname FROM 15 FOR 6)'),
                param($semestre)))
        ->andWhere(
            field('mdl_user_enrolments.userid')
            ->in(
                    $factory
                        ->select('usuarios.mdl_user_id')
                        ->from(
                            subquery(
                                _select_estudiantes_ases_estado()
                                ->where(field('estados_ases.nombre')->eq(literal("'seguimiento'"))),
                                'usuarios')
                        )))
        ->orderBy('mdl_course.fullname', 'ASC');

}


function _select_estudiantes_ases_estado() {
    return _select_estudiantes_ases()
        ->innerJoin(alias('{talentospilos_est_estadoases}','est_estadoases'), on('usuario.id','est_estadoases.id_estudiante'))
        ->innerJoin(alias('{talentospilos_estados_ases}','estados_ases'), on('estados_ases.id','est_estadoases.id_estado_ases'));

}

/**
 * Class ItemReporteCursoProfesores
 *
 * @property string $curso Codigo del curso seguid de el Nombre completo de el curso moodle
 *  ejemplo: PSICOLOGÍA EDUCATIVA I  402200M
 * @property 'SI'|'NO' $critica String indicando si la materia es critica o no
 * @property string $nombre_profesor Nombre completo de el profesor (nombres y apellidos)
 * @property int $estudiantes_sin_ninguna_nota  Estudiantes que no han recibido ninguna nota en el curso, ninguna nota
 *  en una actividad en la cual almenos un estudiante halla recibido nota
 * @property int $estudiantes_perdiendo Estudiantes que van perdido la mitad o más de la cantidad total de items
 *  calificados hasta el momento
 * @property int $estudiantes_ganando Estudiantes que van ganando la mitad o más de la cantidad total de items que se
 *  han calificado hasta el momento
 * @property int $cantidad_estudiantes_ases Cantidad de estudiatnes ASES con seguimiento activo matriculados en el curso
 * @property int $items_con_almenos_una_nota Cantidad de items los cuales se le han calificado para almenos un usuario ASES
 * @property int $cantidad_items Cantidad de items para calificación creados en el curso
 *  Se excluyen los totales
 */
class ItemReporteCursoProfesores {};

/**
 * Return all courses with teacher and aditional info about students course status based in moodle grade items
 * @see https://docs.moodle.org/all/es/%C3%8Dtems_de_calificaci%C3%B3n
 * @see Tables: mdl_grade_items, mdl_user, mdl_course, mdl_grade_grades
 * @return array Array of ItemReporteCursoProfesores
 * @throws dml_exception
 */

function get_reporte_curso_profesores($id_instancia) {
    global $DB;
    $semestre_object = get_current_semester();
    $sem = $semestre_object->nombre;
    $año = substr($sem,0,4);

    if(substr($sem,4,1) == 'A'){
        $semestre = $año.'02';
    }else if(substr($sem,4,1) == 'B'){
        $semestre = $año.'08';
    }

    $sql = <<<SQL
 SELECT DISTINCT ON ( moodle_course.curso_id ) 
                concat_ws(' ',  moodle_course.fullname, substring(moodle_course.shortname from 4 for 7)) as curso,
                Concat_ws(' ', mdl_user.firstname, mdl_user.lastname) AS nombre_profesor,
                case
      when (select id from {course} mdl_course
            where mdl_course.id = moodle_course.curso_id
            and substring(mdl_course.shortname from 4 for 7)
            in (
            select mdl_talentospilos_materias_criti.codigo_materia
            from mdl_talentospilos_materias_criti

            ) limit 1) is null
      then 'NO'
      else
          'SI'
      END AS  critica,
                (
                    select count(*) filter (where first_note is null)  from (
SELECT distinct  on (id) *
  FROM   (
         SELECT     firstname, _mdl_user.id,
                    (
                    SELECT DISTINCT  ON( mdl_grade_grades.finalgrade) finalgrade
                    FROM            mdl_grade_grades
             INNER JOIN       mdl_user
             ON              mdl_user.id = mdl_grade_grades.userid
             INNER JOIN      mdl_grade_items
             ON              mdl_grade_items.id = mdl_grade_grades.itemid
             INNER JOIN       mdl_course
             ON              mdl_course.id = mdl_grade_items.courseid
             WHERE           mdl_user.id = _mdl_user.id
             AND             mdl_course.id = _mdl_course.id
             ORDER BY        mdl_grade_grades.finalgrade ASC limit 1) AS first_note --if at least one grade is not null the first is this, otherwise the first note is null
      FROM       mdl_user as _mdl_user
      INNER JOIN  mdl_talentospilos_user_extended
      ON         _mdl_user.id = mdl_talentospilos_user_extended.id_moodle_user
      AND        mdl_talentospilos_user_extended.tracking_status = 1

      INNER JOIN mdl_role_assignments
      ON         _mdl_user.id = mdl_role_assignments.userid
      INNER JOIN  mdl_talentospilos_est_estadoases
      ON mdl_talentospilos_est_estadoases.id_estudiante = mdl_talentospilos_user_extended.id_ases_user
      inner join mdl_talentospilos_estados_ases
      on mdl_talentospilos_est_estadoases.id_estado_ases = mdl_talentospilos_estados_ases.id
      INNER JOIN  mdl_context
      ON         mdl_context.id = mdl_role_assignments.contextid
      INNER JOIN mdl_course as _mdl_course
      ON         _mdl_course.id = mdl_context.instanceid
      WHERE      _mdl_course.id = moodle_course.curso_id
      AND mdl_talentospilos_estados_ases.nombre = 'seguimiento'
      AND        mdl_role_assignments.roleid = 5)as notas_mas_bajas_estudiantes
     where  notas_mas_bajas_estudiantes.first_note IS NULL) as estudiantes_sin_ninguna_nota ) AS estudiantes_sin_ninguna_nota,
                -- Cantidad de estudiantes con mas de el 50% de las notas calificadas perdidas o no entregadas
                (
                       SELECT count(*)
                       FROM   (
                                         --Usuarios  con mas de el 50% de items perdidos en una materia
                                         SELECT     count(finalgrade) filter (WHERE finalgrade < grademax * 0.6 ) AS cantidad_notas_perdidas ,
                                                    _mdl_user.*,
                                                    (
                                                           SELECT count(*)
                                                           FROM   (
                                                                                  SELECT DISTINCT ON (mdl_grade_items.id) mdl_grade_items.id
                                                                                  FROM            {grade_items} mdl_grade_items
                                                                                  INNER JOIN      {grade_grades} mdl_grade_grades
                                                                                  ON              mdl_grade_items.id = mdl_grade_grades.itemid
                                                                                  WHERE           mdl_grade_items.courseid = moodle_course.curso_id
                                                                                  AND             mdl_grade_items.itemtype != 'category'
                                                                                  AND             mdl_grade_items.itemtype != 'course'
                                                                                  AND             finalgrade IS NOT NULL
                                                                                  EXCEPT
                                                                                  SELECT     mdl_grade_items.id
                                                                                  FROM       {grade_items} mdl_grade_items
                                                                                  INNER JOIN {grade_grades} mdl_grade_grades
                                                                                  ON         mdl_grade_items.id = mdl_grade_grades.itemid
                                                                                  INNER JOIN {user} mdl_user
                                                                                  ON         mdl_grade_grades.userid = mdl_user.id
                                                                                  WHERE      mdl_grade_items.courseid = moodle_course.curso_id
                                                                                  AND        mdl_user.id = _mdl_user.id
                                                                                  AND        mdl_grade_grades.finalgrade IS NOT NULL) AS cantidad_notas_calificadas_y_no_entregadas) AS cantidad_notas_calificadas_y_no_entregadas
                                         FROM       {grade_grades} mdl_grade_grades
                                         INNER JOIN {grade_items} mdl_grade_items
                                         ON         mdl_grade_items.id = mdl_grade_grades.itemid
                                         INNER JOIN {user} AS _mdl_user
                                         ON         _mdl_user.id = mdl_grade_grades.userid
                                         INNER JOIN {talentospilos_user_extended} mdl_talentospilos_user_extended
                                         ON         _mdl_user.id = mdl_talentospilos_user_extended.id_moodle_user
                                                     inner join mdl_talentospilos_est_estadoases
            on mdl_talentospilos_est_estadoases.id_estudiante = mdl_talentospilos_user_extended.id_ases_user
            inner join mdl_talentospilos_estados_ases
            on mdl_talentospilos_est_estadoases.id_estado_ases = mdl_talentospilos_estados_ases.id
                                         WHERE      mdl_grade_items.courseid = moodle_course.curso_id
                                         AND        mdl_grade_items.itemtype != 'category'
                                         AND        mdl_talentospilos_user_extended.tracking_status = 1
                                         AND        mdl_grade_items.itemtype != 'course'
                                         and mdl_talentospilos_estados_ases.nombre = 'seguimiento'
                                         AND        mdl_grade_grades.finalgrade IS NOT NULL
                                         GROUP BY   _mdl_user.id) AS usuarios_y_notas_perdidas
                       WHERE  (
                                     usuarios_y_notas_perdidas.cantidad_notas_perdidas + usuarios_y_notas_perdidas.cantidad_notas_calificadas_y_no_entregadas) >= --usuarios perdiendo la mitad o mas de las notas
                              (
                                     --Piso de el 50% de items de un curso
                                     SELECT ceil(count(*) * 0.5)
                                     FROM   (
                                                            SELECT DISTINCT ON (mdl_grade_items.id) *
                                                            FROM            {grade_items} mdl_grade_items
                                                            INNER JOIN      {grade_grades} mdl_grade_grades
                                                            ON              mdl_grade_items.id = mdl_grade_grades.itemid
                                                            WHERE           mdl_grade_items.courseid = moodle_course.curso_id
                                                            AND             mdl_grade_items.itemtype != 'category'
                                                            AND             mdl_grade_items.itemtype != 'course' 
                                                            AND             finalgrade IS NOT NULL) AS a )) AS estudiantes_perdiendo ,
                (
                     SELECT count(*)
                       FROM   (
                                         --Usuarios  con mas de el 50% de items perdidos en una materia
                                         SELECT     count(finalgrade) filter (WHERE finalgrade < grademax * 0.6 ) AS cantidad_notas_perdidas ,
                                                    _mdl_user.*,
                                                    (
                                                           SELECT count(*)
                                                           FROM   (
                                                                                  SELECT DISTINCT ON (mdl_grade_items.id) mdl_grade_items.id
                                                                                  FROM            {grade_items} mdl_grade_items
                                                                                  INNER JOIN      {grade_grades} mdl_grade_grades
                                                                                  ON              mdl_grade_items.id = mdl_grade_grades.itemid
                                                                                  WHERE           mdl_grade_items.courseid = moodle_course.curso_id
                                                                                  AND             mdl_grade_items.itemtype != 'category'
                                                                                  AND             mdl_grade_items.itemtype != 'course'
                                                                                  AND             finalgrade IS NOT NULL
                                                                                  EXCEPT
                                                                                  SELECT     mdl_grade_items.id
                                                                                  FROM       {grade_items} mdl_grade_items
                                                                                  INNER JOIN {grade_grades} mdl_grade_grades
                                                                                  ON         mdl_grade_items.id = mdl_grade_grades.itemid
                                                                                  INNER JOIN {user} mdl_user
                                                                                  ON         mdl_grade_grades.userid = mdl_user.id
                                                                                  WHERE      mdl_grade_items.courseid = moodle_course.curso_id
                                                                                  AND        mdl_user.id = _mdl_user.id
                                                                                  AND        mdl_grade_grades.finalgrade IS NOT NULL) AS cantidad_notas_calificadas_y_no_entregadas) AS cantidad_notas_calificadas_y_no_entregadas
                                         FROM       {grade_grades} mdl_grade_grades
                                         INNER JOIN {grade_items} mdl_grade_items
                                         ON         mdl_grade_items.id = mdl_grade_grades.itemid
                                         INNER JOIN {user} AS _mdl_user
                                         ON         _mdl_user.id = mdl_grade_grades.userid
                                         INNER JOIN {talentospilos_user_extended} mdl_talentospilos_user_extended
                                         ON         _mdl_user.id = mdl_talentospilos_user_extended.id_moodle_user
                                                     inner join mdl_talentospilos_est_estadoases
            on mdl_talentospilos_est_estadoases.id_estudiante = mdl_talentospilos_user_extended.id_ases_user
            inner join mdl_talentospilos_estados_ases
            on mdl_talentospilos_est_estadoases.id_estado_ases = mdl_talentospilos_estados_ases.id
                                         WHERE      mdl_grade_items.courseid = moodle_course.curso_id
                                         AND        mdl_grade_items.itemtype != 'category'
                                         AND        mdl_talentospilos_user_extended.tracking_status = 1
                                         AND        mdl_grade_items.itemtype != 'course'
                                         and mdl_talentospilos_estados_ases.nombre = 'seguimiento'
                                         AND        mdl_grade_grades.finalgrade IS NOT NULL
                                         GROUP BY   _mdl_user.id) AS usuarios_y_notas_perdidas
                       WHERE  (
                                     usuarios_y_notas_perdidas.cantidad_notas_perdidas + usuarios_y_notas_perdidas.cantidad_notas_calificadas_y_no_entregadas)< --usuarios perdiendo la mitad o mas de las notas
                              (
                                     --Piso de el 50% de items de un curso
                                     SELECT ceil(count(*) * 0.5)
                                     FROM   (
                                                            SELECT DISTINCT ON (mdl_grade_items.id) *
                                                            FROM            {grade_items} mdl_grade_items
                                                            INNER JOIN      {grade_grades} mdl_grade_grades
                                                            ON              mdl_grade_items.id = mdl_grade_grades.itemid
                                                            WHERE           mdl_grade_items.courseid = moodle_course.curso_id
                                                            AND             mdl_grade_items.itemtype != 'category'
                                                            AND             mdl_grade_items.itemtype != 'course' 
                                                            AND             finalgrade IS NOT NULL) AS a )) AS estudiantes_ganando,
                (
                select count(*) from (
                          SELECT    distinct  on (mdl_user.id) mdl_user.id
FROM       mdl_user
             INNER JOIN mdl_role_assignments
               ON         mdl_user.id = mdl_role_assignments.userid
             INNER JOIN mdl_context
               ON         mdl_context.id = mdl_role_assignments.contextid
             INNER JOIN mdl_course
               ON         mdl_course.id = mdl_context.instanceid
             INNER JOIN mdl_talentospilos_user_extended
               ON         mdl_user.id = mdl_talentospilos_user_extended.id_moodle_user
            inner join mdl_talentospilos_est_estadoases
            on mdl_talentospilos_est_estadoases.id_estudiante = mdl_talentospilos_user_extended.id_ases_user
            inner join mdl_talentospilos_estados_ases
            on mdl_talentospilos_est_estadoases.id_estado_ases = mdl_talentospilos_estados_ases.id
WHERE      mdl_role_assignments.roleid = 5
  AND        mdl_talentospilos_user_extended.tracking_status = 1
  AND        mdl_course.id = moodle_course.curso_id
and mdl_talentospilos_estados_ases.nombre = 'seguimiento' ) AS id_estudiantes_ases ) AS cantidad_estudiantes_ases,
                (
                       SELECT count(*)
                       FROM   (
                                              SELECT DISTINCT  ON ( mdl_grade_items.id) *
                                              FROM            {grade_items}  AS mdl_grade_items
                                              INNER JOIN      {grade_grades} AS mdl_grade_grades
                                              ON              mdl_grade_items.id = mdl_grade_grades.itemid
                                              WHERE           mdl_grade_items.courseid = moodle_course.curso_id
                                              AND             mdl_grade_items.itemtype != 'category'
                                              AND             mdl_grade_items.itemtype != 'course'
                                              AND             finalgrade IS NOT NULL) a )AS items_con_almenos_una_nota,
                (
                       SELECT count(*)
                       FROM   {grade_items} AS mdl_grade_items
                       WHERE  mdl_grade_items.courseid = moodle_course.curso_id
                       AND    mdl_grade_items.itemtype != 'course'
                       AND    mdl_grade_items.itemtype != 'category' ) AS cantidad_items
FROM            {user}                                                 AS mdl_user
INNER JOIN      {role_assignments}                                     AS mdl_role_assignments
ON              mdl_user.id = mdl_role_assignments.userid
INNER JOIN      {role} AS mdl_role
ON              mdl_role.id = mdl_role_assignments.roleid
INNER JOIN      {context} AS mdl_context
ON              mdl_context.id = mdl_role_assignments.contextid
INNER JOIN
                (
          select distinct  on (mdl_course.id)
                            mdl_course.id AS curso_id,
                            mdl_course.fullname,
                            mdl_course.shortname
        from {user} mdl_user
        inner join {talentospilos_user_extended} mdl_talentospilos_user_extended
            on mdl_user.id = mdl_talentospilos_user_extended.id_moodle_user
        inner join {cohort_members} mdl_cohort_members
            on mdl_cohort_members.userid = mdl_user.id
        inner join {talentospilos_inst_cohorte} mdl_talentospilos_inst_cohorte
            on mdl_talentospilos_inst_cohorte.id_cohorte = mdl_cohort_members.cohortid
        inner join {role_assignments} mdl_role_assignments
            on mdl_role_assignments.userid = mdl_user.id
        inner join {context} mdl_context
            on mdl_role_assignments.contextid = mdl_context.id
        inner join {course} mdl_course
            on mdl_course.id = mdl_context.instanceid
        inner join {talentospilos_est_estadoases} mdl_talentospilos_est_estadoases
            on mdl_talentospilos_user_extended.id_ases_user = mdl_talentospilos_est_estadoases.id_estudiante
        inner join {talentospilos_estados_ases} mdl_talentospilos_estados_ases
            on mdl_talentospilos_est_estadoases.id_estado_ases = mdl_talentospilos_estados_ases.id
        where mdl_talentospilos_estados_ases.nombre='seguimiento'
        and mdl_talentospilos_inst_cohorte.id_instancia = $id_instancia
        and substring(mdl_course.shortname from 15 for 6) = '$semestre' ) AS moodle_course
        ON              moodle_course.curso_id = mdl_context.instanceid
        WHERE           mdl_role_assignments.roleid = 3


SQL;

    return $DB->get_records_sql($sql);
}

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
        alias('mdl_user.id', 'mdl_user_id'),
        alias('user_extended.id', 'user_extended_id'),
        alias('usuario.id', 'usuario_id')
    ];
    $column_names = array_merge($column_names, AsesUserExtended::get_column_names('user_extended'));
    $column_names = array_merge($column_names, AsesUser::get_column_names('usuario'));
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
            alias(AsesUser::get_table_name_for_moodle(), 'usuario'),
            on('user_extended.'.AsesUserExtended::ID_ASES_USER, 'usuario.'.AsesUser::ID))
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
    $INSTANCIA_COHORTE = 'inst_cohorte';
    return $factory
        ->select()
        ->from(alias('{talentospilos_inst_cohorte}', $INSTANCIA_COHORTE))
        ->where(field($INSTANCIA_COHORTE.'.id_instancia')->eq($id_instancia));
}

/**
 * Return the common language cnofiguration for ASES datatables
 * @return array
 */
function get_datatable_common_language_config(): array {
    return array(
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
    );
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
        $common_language_config = get_datatable_common_language_config();
		$data = array(
					"bsort" => false,
					"columns" => $columns,
					"data" => get_students_and_finalgrades($instance_id),
					"language" => $common_language_config,
					"order"=> array(0, "desc")

                );
    return $data;
}

/**
 * Return the column for datatables for detail data, in other words, the column with '+' symbol in
 * the datatable
 * @see https://datatables.net/examples/api/row_details.html
 * @return array
 */
function get_datatable_class_column(): array {
    return array(
        "class"=> "details-control",
        "orderable"=>      false,
        "data"=>           null,
        "defaultContent"=> "");
}

function get_datatable_array_for_course_teacher_report($instance_id) {
    $common_language_config = get_datatable_common_language_config();
    $columns = array();
    /* Index of column  'Est < 50' (starting from 0)*/
    $est_lt_50_colum_index = 2;

    $data = array_values(get_reporte_curso_profesores($instance_id));
    array_push($columns, array("title"=>"Curso", "name"=>'curso', "data"=>"curso"));
    array_push($columns, array(
        "title"=>"Profesor",
        "name"=>"nombre_profesor",
        "data"=>"nombre_profesor",
        "description"=>'Nombre del profesor'));
    array_push($columns, array(
        "title"=>"Est < 50%",
        "name"=>"estudiantes_perdiendo",
        "data"=>"estudiantes_perdiendo",
        'description'=>'Estudiantes perdiendo más de la mitad de los items calificados',
        'className'=>'estudiantes_perdiendo'));
    array_push($columns, array("title"=>"Est >=50%", "name"=>"estudiantes_ganando", "data"=>"estudiantes_ganando", "description"=>"Estudiantes ganando más de la mitad de los items calificados"));
    array_push($columns, array("title"=>"Est. sin notas", "name"=>"estudiantes_sin_ninguna_nota", "data"=>"estudiantes_sin_ninguna_nota", "description"=>"Estudiantes sin ningún item calificado"));
    array_push($columns, array("title"=>"Estudiantes", "name"=>"cantidad_estudiantes_ases", "data"=>"cantidad_estudiantes_ases", 'description'=>'Cantidad de estudiantes ASES'));
    array_push($columns, array(
        "title"=>"Items  calificados",
        "name"=>"items_con_almenos_una_nota",
        "data"=>"items_con_almenos_una_nota",
        'description'=>'Cantidad de items en los cuales almenos un estudiante tiene una nota',
        'className'=>'items_con_almenos_una_nota'));
    array_push($columns, array(
        "title"=>"Cantidad de items",
        "name"=>"cantidad_items",
        "data"=>"cantidad_items",
        'description'=>'Cantidad de items calificables de el curso',
        'className'=>'cantidad_items'));
    array_push($columns, array("title"=>"Es critica", "name"=>"critica", "data"=>"critica", "description"=>'Indica si la materia ha sido marcada como critica por ASES'));

    $data = array(
        "bsort" => false,
        "columns" => $columns,
        "data" => $data,
        "language" => $common_language_config,
        "order"=> array($est_lt_50_colum_index, "desc")

    );
    return $data;
}