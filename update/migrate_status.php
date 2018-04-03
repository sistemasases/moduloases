<?php
require_once dirname(__FILE__) . '/../../../config.php';
global $DB;

// Buscar los estudiantes que pertenecen a más de una instancia
// $sql_query = "SELECT ROW_NUMBER() OVER (ORDER BY user_extended.id_ases_user), user_moodle.id, user_moodle.firstname, user_moodle.lastname, 
//                               user_extended.id_ases_user, cohorts.idnumber, instancia_cohorte.id_instancia 
//               FROM {user} AS user_moodle
//               INNER JOIN {talentospilos_user_extended} AS user_extended ON user_extended.id_moodle_user = user_moodle.id
//               INNER JOIN {cohort_members} AS members_cohorts ON members_cohorts.userid = user_extended.id_moodle_user
//               INNER JOIN {cohort} AS cohorts ON cohorts.id = members_cohorts.cohortid
//               INNER JOIN {talentospilos_inst_cohorte} AS instancia_cohorte ON instancia_cohorte.id_cohorte = members_cohorts.cohortid

//               WHERE user_extended.tracking_status = 1
//               ";

// $result = $DB->get_records_sql($sql_query);

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

// for($i = 1; $i < count($result); $i++){
//     print_r($result[$i]);
//     print_r("<br>");
// }

// Carga de tabla de estados ASES
$status_array = ["SEGUIMIENTO", "SIN SEGUIMIENTO"];
$description_status_array = ["Se le realiza seguimiento en la estrategia ASES", "No se le realiza seguimiento en la estrategia ASES"];

$object_record = new stdClass();
for($i = 0; $i < count($status_array); $i++){
    $object_record->nombre = $status_array[$i];
    $object_record->descripcion = $description_status_array[$i];

    $DB->insert_record('talentospilos_estados_ases', $object_record);
}

// **********
// Migración
// **********
$sql_query = "SELECT id FROM {talentospilos_usuario}";
$students_array = $DB->get_records_sql($sql_query);

for($j = 1; $j <= count($students_array); $j++){

    $id = $students_array[$j]->id;

    $sql_query = "SELECT ROW_NUMBER() OVER (ORDER BY user_extended.id_ases_user), user_extended.id_moodle_user, members_cohorts.cohortid
                  FROM {talentospilos_user_extended} AS user_extended
                  INNER JOIN {cohort_members} AS members_cohorts ON members_cohorts.userid = user_extended.id_moodle_user
                  WHERE id_ases_user = $id";
    
    $result = $DB->get_records_sql($sql_query);

    print_r($result);
}