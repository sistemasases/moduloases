<?php

require_once(__DIR__ . '/../../vendor/autoload.php');
require_once(__DIR__ . '/../../vendor/autoload.php');
use function Latitude\QueryBuilder\{literal, criteria, alias, on, field};

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