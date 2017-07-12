<?php

function consult_instance($instanceid){
    global $DB;
    $sql_query = "SELECT instancia.id as id_talentosinstancia ,id_instancia id_director, id_programa, prog.nombre, prog.cod_univalle FROM {talentospilos_instancia} instancia INNER JOIN  {talentospilos_programa} prog ON prog.id = instancia.id_programa   WHERE id_instancia = ".$instanceid.";";
    $consult = $DB->get_record_sql($sql_query);
    // print_r($consult);
    return $consult;
}