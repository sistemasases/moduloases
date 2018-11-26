<?php
namespace course_lib;
require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->dirroot. '/lib/gradelib.php');
require_once($CFG->dirroot. '/grade/querylib.php');
require_once(__DIR__ . '/../../vendor/autoload.php');
require_once(__DIR__ . '/../../classes/DAO/BaseDAO.php');
require_once(__DIR__ . '/../../managers/student_profile/academic_lib.php');
use function grade_force_full_regrading;
use function grade_regrade_final_grades_if_required;
use function Latitude\QueryBuilder\{literal, criteria, alias, on, field};
use Latitude\QueryBuilder\Query\SelectQuery;

/**
 * Betha: ases course and teacher summary
 */

function _select_ases_courses($semestre, $id_instancia): SelectQuery {


    return BaseDAO::get_factory()
        ->select(
            literal('DISTINCT ON (mdl_course.id) *')
        )
        ->from(alias('{talentospilos_user_extended}', 'user_extended'))
        ->innerJoin(
            alias('{user}', 'mdl_user'),
            on('mdl_user.id', 'user_extended.id_moodle_user'))
        ->innerJoin(
            alias('{cohort_members}', 'mdl_cohort_members'),
            on('mdl_cohort_members.userid', 'mdl_user.id'))
        ->innerJoin(
            alias('{talentospilos_inst_cohorte}', 'inst_cohorte'),
            on('inst_cohorte.id_cohorte', 'mdl_cohort_members.cohortid'))
        ->innerJoin(
            alias('{role_assignments}', 'mdl_role_assignments'),
            on('mdl_role_assignments.userid', 'mdl_user.id'))
        ->innerJoin(
            alias('{context}', 'mdl_context'),
            on('mdl_role_assignments.contextid', 'mdl_context.id'))
        ->innerJoin(
            alias('{course}', 'mdl_course'),
            on('mdl_course.id', 'mdl_context.instanceid'))
        ->innerJoin(
            alias('{talentospilos_est_estadoases}', 'est_estadoases'),
            on('user_extended.id_ases_user', 'est_estadoases.id_estudiante'))
        ->innerJoin(
            alias('{talentospilos_estados_ases}', 'estados_ases'),
            on('est_estadoases.id_estado_ases', 'estados_ases.id'))
        ->where(field('estados_ases.nombre')->eq('seguimiento'))
        ->andWhere(field('inst_cohorte.id_instancia')->eq($id_instancia))
        ->andWhere(field('user_extended.tracking_status')->eq(1))
        ->andWhere(field('mdl_role_assignments.roleid')->eq(5))
        ->andWhere(criteria("substring(mdl_course.shortname from 15 for 6) = %s", $semestre));
}


/**
 * Betha: ases course and teacher summary
 */
function _select_count_ases_courses($id_instancia = 450299, $semestre = '201808') {
    return BaseDAO::get_factory()
        ->select(literal('count(*)'))
        ->from(
            subquery(
                _select_ases_courses('201808', 450299)
                    ->innerJoin(
                        alias('',''),
                        on('','')
                    )
                    ->andWhere(field('estados_ases.nombre')->eq('seguimiento'))
                    ->andWhere(field('inst_cohorte.id_instancia')->eq($id_instancia))
                    ->andWhere(field('user_extended.tracking_status')->eq(1))
                    ->andWhere(field('mdl_role_assignments.roleid')->eq(5))
                    ->andWhere(criteria("substring(mdl_course.shortname from 15 for 6) = %s", $semestre))
                    ->addColumns('mdl_course.id'),
                'some'

            ));
}

/**
 * Betha: ases course and teacher summary
 */
function _select_count_courses_with_at_least_one_item_calif($id_instancia = 450299, $semestre= '201808') {
    return BaseDAO::get_factory()
        ->select(literal(' count(mdl_course_id)'))
        ->from(
            subquery(
                _select_ases_courses('201808', 450299)
                    ->innerJoin(
                        alias('{grade_items}', 'mdl_grade_items'),
                        on('mdl_grade_items.courseid', 'mdl_course.id'))
                    ->innerJoin(
                        alias('{grade_grades}','mdl_grade_grades'),
                        on('mdl_grade_grades.itemid','mdl_grade_items.id'))
                    ->where(field('mdl_grade_items.itemtype')->notEq('course'))
                    ->andWhere(field('mdl_grade_items.itemtype')->notEq('category'))
                    ->andWhere(field('mdl_grade_grades.finalgrade')->isNotNull())


                    ->andWhere(field('estados_ases.nombre')->eq('seguimiento'))
                    ->andWhere(field('inst_cohorte.id_instancia')->eq($id_instancia))
                    ->andWhere(field('user_extended.tracking_status')->eq(1))
                    ->andWhere(field('mdl_role_assignments.roleid')->eq(5))
                    ->andWhere(criteria("substring(mdl_course.shortname from 15 for 6) = %s", $semestre))
                    ->addColumns(alias('mdl_course.id', 'mdl_course_id')),
                'some'

            ));
}
/**
 * Betha: ases course and teacher summary
 */
function _select_count_courses_with_at_least_one_item($id_instancia = 450299, $semestre= '201808') {
    return BaseDAO::get_factory()
        ->select(literal(' count(mdl_course_id)'))
        ->from(
            subquery(
                _select_ases_courses('201808', 450299)
                    ->innerJoin(
                        alias('{grade_items}', 'mdl_grade_items'),
                        on('mdl_grade_items.courseid', 'mdl_course.id'))
                    ->where(field('mdl_grade_items.itemtype')->notEq('course'))
                    ->andWhere(field('mdl_grade_items.itemtype')->notEq('category'))


                    ->andWhere(field('estados_ases.nombre')->eq('seguimiento'))
                    ->andWhere(field('inst_cohorte.id_instancia')->eq($id_instancia))
                    ->andWhere(field('user_extended.tracking_status')->eq(1))
                    ->andWhere(field('mdl_role_assignments.roleid')->eq(5))
                    ->andWhere(criteria("substring(mdl_course.shortname from 15 for 6) = %s", $semestre))
                    ->addColumns(alias('mdl_course.id', 'mdl_course_id')),
                'some'

            ));
}


/**
 * Return courses filtred by student and semester, only if the course have at least one item graded,
 *  this grade can be graded to any student included the student with id given
 *
 * ## Fields:
 * - mdl_course.*
 * - id_course
 * - time_created
 *
 * @see get_courses_by_student($id_student, $last_semester)
 * @param $id_student --> student id
 * @return array --> filled with stdClass objects representing courses and grades for a single student
 */

function get_courses_with_grades($id_student, $semester)
{

    global $DB;

    $query = "SELECT DISTINCT on(curso.id) curso.id as id_course,
			                curso.*,
			                to_timestamp(curso.timecreated)::DATE AS time_created
			FROM {course} curso
			INNER JOIN {enrol} role ON curso.id = role.courseid
			INNER JOIN {user_enrolments} enrols ON enrols.enrolid = role.id
			INNER JOIN {grade_items} mdl_grade_items
			  ON mdl_grade_items.courseid = curso.id
			INNER JOIN {grade_grades} mdl_grade_grades
			 ON mdl_grade_grades.itemid = mdl_grade_items.id
			WHERE enrols.userid = $id_student AND SUBSTRING(curso.shortname FROM 15 FOR 6) = '$semester'
			 AND mdl_grade_grades.finalgrade is not null
			AND   mdl_grade_items.itemtype != 'category'
             AND   mdl_grade_items.itemtype != 'course'";

    $courses = $DB->get_records_sql($query);
    if (!$courses) {
        return false;
    }
    return $courses;

}



/**
 * Return courses for a student in the last semester, optionally can have the final grade and grades descriptions
 *
 * ## Fields:
 * - mdl_course.*
 * - id_course
 * - time_created
 *
 *  ## Optional fields
 *  - grade string Course grade
 *  - descriptions html All course grades in a printable format, included the final grade
 * @see get_courses_by_student($id_student, $last_semester)
 * @param $id_student --> student id
 * @param $semester string Semester for get the query, examples: '201808', '201802'
 * @return array --> filled with stdClass objects representing courses and grades for a single student
 */

function get_courses_by_student($id_student, $semester, $append_grade_descriptions = true)
{

    global $DB;

    $query = "SELECT DISTINCT curso.id as id_course,
			                curso.*,
			                to_timestamp(curso.timecreated)::DATE AS time_created
			FROM {course} curso
			INNER JOIN {enrol} role ON curso.id = role.courseid
			INNER JOIN {user_enrolments} enrols ON enrols.enrolid = role.id
			WHERE enrols.userid = $id_student AND SUBSTRING(curso.shortname FROM 15 FOR 6) = '$semester'
            ORDER BY time_created DESC";

    $courses = $DB->get_records_sql($query);
    if (!$courses) {
        return false;
    }

    if ($append_grade_descriptions) {
        foreach ($courses as &$result) {
            $result->grade = number_format(\grade_get_course_grade($id_student, $result->id_course)->grade, 2);

            $result->descriptions = \getCoursegradelib($result->id_course, $id_student);
        }
    }
    return $courses;

}



/**
 * Return all the Ases courses than have not teacher, objects returned have all the properties of table
 * {course}
 *
 * @param $instance_id Block instance id
 * @param $semester String than represents the semester in CampusVirtual format, example: '201808'
 * @return array  Ases courses than have not teacher,
 * @throws dml_exception
 */
function get_ases_courses_without_teachers($instance_id, $semester) {
    global $DB;
    $sql = <<<SQL
     select  distinct  on (ases_course.course_id) * from
                 (
                 select distinct  on (mdl_course.id) mdl_course.id as course_id, mdl_course.*
                 from  {user} mdl_user
                     inner join {talentospilos_user_extended} mdl_talentospilos_user_extended
                     on mdl_user.id = mdl_talentospilos_user_extended.id_moodle_user
                     inner join  {cohort_members} mdl_cohort_members
                     on mdl_cohort_members.userid = mdl_user.id
                     inner join  {talentospilos_inst_cohorte} mdl_talentospilos_inst_cohorte
                     on mdl_talentospilos_inst_cohorte.id_cohorte = mdl_cohort_members.cohortid
                     inner join {role_assignments} mdl_role_assignments
                     on mdl_role_assignments.userid = mdl_user.id
                     inner join {context} mdl_context
                     on mdl_role_assignments.contextid = mdl_context.id
                     inner join {course} mdl_course
                     on mdl_course.id = mdl_context.instanceid
                     inner join  {talentospilos_est_estadoases} mdl_talentospilos_est_estadoases
                     on mdl_talentospilos_user_extended.id_ases_user = mdl_talentospilos_est_estadoases.id_estudiante
                     inner join {talentospilos_estados_ases} mdl_talentospilos_estados_ases
                     on mdl_talentospilos_est_estadoases.id_estado_ases = mdl_talentospilos_estados_ases.id
                     where mdl_talentospilos_estados_ases.nombre='seguimiento'
                     and mdl_talentospilos_inst_cohorte.id_instancia = $instance_id
                     AND mdl_talentospilos_user_extended.tracking_status = 1
                     and mdl_role_assignments.roleid = 5
                     and substring(mdl_course.shortname from 15 for 6) = '$semester'

                     ) as ases_course
  inner join mdl_context
      on mdl_context.instanceid = ases_course.course_id
  inner join mdl_role_assignments
      on mdl_role_assignments.contextid = mdl_context.id
  inner join mdl_user
      on mdl_role_assignments.userid = mdl_user.id
  and ases_course.course_id not in (
  select  distinct  on (ases_course.course_id) ases_course.course_id from
                                                     (
                                                     select distinct  on (mdl_course.id) mdl_course.id as course_id, mdl_course.*
                                                     from  {user} mdl_user
                                                             inner join {talentospilos_user_extended} mdl_talentospilos_user_extended
                                                               on mdl_user.id = mdl_talentospilos_user_extended.id_moodle_user
                                                             inner join  {cohort_members} mdl_cohort_members
                                                               on mdl_cohort_members.userid = mdl_user.id
                                                             inner join {talentospilos_inst_cohorte} mdl_talentospilos_inst_cohorte
                                                               on mdl_talentospilos_inst_cohorte.id_cohorte = mdl_cohort_members.cohortid
                                                             inner join {role_assignments} mdl_role_assignments
                                                               on mdl_role_assignments.userid = mdl_user.id
                                                             inner join {context} mdl_context
                                                               on mdl_role_assignments.contextid = mdl_context.id
                                                             inner join {course} mdl_course
                                                               on mdl_course.id = mdl_context.instanceid
                                                             inner join  {talentospilos_est_estadoases} mdl_talentospilos_est_estadoases
                                                               on mdl_talentospilos_user_extended.id_ases_user = mdl_talentospilos_est_estadoases.id_estudiante
                                                             inner join {talentospilos_estados_ases} mdl_talentospilos_estados_ases
                                                               on mdl_talentospilos_est_estadoases.id_estado_ases = mdl_talentospilos_estados_ases.id
                                                     where mdl_talentospilos_estados_ases.nombre='seguimiento'
                                                       and mdl_talentospilos_inst_cohorte.id_instancia = $instance_id
                                                       AND mdl_talentospilos_user_extended.tracking_status = 1
                                                       and mdl_role_assignments.roleid = 5
                                                       and substring(mdl_course.shortname from 15 for 6) = '$semester'

                                                     ) as ases_course
                                                       inner join {context} dl_context
                                                         on mdl_context.instanceid = ases_course.course_id
                                                       inner join mdl_role_assignments
                                                         on mdl_role_assignments.contextid = mdl_context.id
                                                       inner join mdl_user
                                                         on mdl_role_assignments.userid = mdl_user.id
  where mdl_role_assignments.roleid = 3)


     
SQL;
    $records = $DB->get_records_sql($sql);
    return $records;

}

/**
 * Return the final grade for a student, the final grade is recalculated before returned
 * @param $student_id string|int Moodle user id
 * @param $course_id string|int Moodle course id
 * @return string Final grade in a course if exist
 * @throws \dml_exception
 */
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
 * Returns a string with the teacher from a course.
 *

 * @see getTeacher($id_curso)
 * @param $id_curso --> course id
 * @return string $teacher_name
 **/

function getTeacherName($id_curso)
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
 * Return the standard regex for recognize a well formed Univalle course shortname
 * @return string Regex with standard regex
 */
function get_course_shortname_standar_regex() {
    return '/(?J)^(?<sede>[0-9]{2})-(?<codigo_curso>[a-zA-Z0-9]{5,7})-(?<grupo>[0-9]{2})-(?<periodo>[0-9]{6})[0-9]{0,3}$/';
}

/**
 * Retorna un string normalizado que representa el nombre corto standard para los cursos de Univalle
 * @param $sede string Codigo standard de la sede de el curso, debe tener dos caracteres Ejemplo: 00 (Codigo para cali)
 * @param $codigo_curso string Codigo de cursos standard, debe tener 6 caracteres (materias maestria, ej: 700000) o
 *  tener 7 caracteres (materias pregrado, ej: 7000000M)
 * @param $periodo string Periodo en el que se dicta el curso, tiene el formato(AÑO[02|08][0-9]{0,3}) donde si el año es seguido
 *  de 02, indica que es materia de el primer semestre, si es seguido de 08 es una materia de el segundo semestre de el
 *  año, los siguientes digitos pueden ser entre 0 y 3 y son arbitrarios. ejemplos: 201708555 201802000 2018020 201802
 * @return string|false Retorna el nombre corto de curso normalizado, si alguno de los parametros es erroneo a lo descrito en
 *  la documentacion de cada uno, se retorna false. Ejemplos de retorno: 00-710100M-01-201808041,
 *  00-710100-01-201808041, 00-710100M-01-201708041
 */
function get_normalized_course_shortname($sede, $codigo_curso, $grupo, $periodo): string {
    $normalized_course_shortname = $sede.'-'.$codigo_curso.'-'.$grupo.'-'.$periodo;
    $standard_shortname_regex = get_course_shortname_standar_regex();
    if(preg_match($standard_shortname_regex, $normalized_course_shortname)){
        return $normalized_course_shortname;
    } else {
        return false;
    }
}
/**
 * Normalize the short name of the course
 * @param $course_shortname string Complete course name from standard shortname format (ej: 00-710100M-01-201808041)
 * @return string Format normalized (SEDE-CODIGO_CURSO-GRUPO)
 */
function normalize_short_name(string $course_shortname) {
    $standard_shortname_regex = get_course_shortname_standar_regex();
    $course_name_elements = array();
    if(preg_match($standard_shortname_regex, $course_shortname, $course_name_elements)) {
        return implode('-', array($course_name_elements['sede'], $course_name_elements['codigo_curso'], $course_name_elements['grupo']));
    } else {
        return $course_shortname;
    }

}