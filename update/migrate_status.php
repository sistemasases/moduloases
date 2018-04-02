<?php
require_once dirname(__FILE__) . '/../../../config.php';
global $DB;

// Buscar los estudiantes que pertenecen a más de una instancia
$sql_query = "SELECT ROW_NUMBER() OVER (ORDER BY user_extended.id_ases_user), user_moodle.id, user_moodle.firstname, user_moodle.lastname, 
                              user_extended.id_ases_user, cohorts.idnumber, instancia_cohorte.id_instancia 
              FROM {user} AS user_moodle
              INNER JOIN {talentospilos_user_extended} AS user_extended ON user_extended.id_moodle_user = user_moodle.id
              INNER JOIN {cohort_members} AS members_cohorts ON members_cohorts.userid = user_extended.id_moodle_user
              INNER JOIN {cohort} AS cohorts ON cohorts.id = members_cohorts.cohortid
              INNER JOIN {talentospilos_inst_cohorte} AS instancia_cohorte ON instancia_cohorte.id_cohorte = members_cohorts.cohortid

              WHERE user_extended.tracking_status = 1
              ";

$result = $DB->get_records_sql($sql_query);

$sql_query = "TRUNCATE TABLE {talentospilos_est_estadoases} RESTART IDENTITY";
$result_truncate_est_estado = $DB->execute($sql_query);

print_r($result_truncate_est_estado);
print_r("<br>");
if($result_truncate_est_estado){
    $sql_query = "TRUNCATE TABLE {talentospilos_estados_ases} RESTART IDENTITY";
    $result_truncate_estados_ases = $DB->execute($sql_query);
    print_r($result_truncate_estados_ases);
    print_r("<br>");
}

for($i = 1; $i < count($result); $i++){
    print_r($result[$i]);
    print_r("<br>");
}
