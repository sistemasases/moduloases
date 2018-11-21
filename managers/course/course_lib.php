<?php

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