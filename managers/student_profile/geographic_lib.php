<?php

/**
 * Función que extrae la información geográfica asociada a un estudiante
 *
 * @see get_geographic_info($id_ases)
 * @param $id_ases --> ID correspondiente a la tabla {talentospilos_ases}
 * @return Array con las coordenadas de la residencia y la calificación del riesgo geográfico
 */
 
function get_geographic_info($id_ases){
    global $DB;
    $sql_query = "SELECT id_usuario AS id_user, latitud AS latitude, longitud AS longitude, barrio AS neighborhood 
                  FROM {talentospilos_demografia} AS demographic_t
                  WHERE demographic_t.id_usuario=".$id_ases;
                  
    $result = $DB->get_record_sql($sql_query);
    
    if(!$result) return false;
    
    $sql_query = "SELECT calificacion_riesgo AS risk FROM {talentospilos_riesgos_ases} AS ases_risk 
                                                     INNER JOIN {talentospilos_riesg_usuario} AS user_risk ON user_risk.id_riesgo = ases_risk.id 
                  WHERE ases_risk.nombre = 'geografico' AND user_risk.id_usuario =".$id_ases;
                  
    $risk_grade_object =  $DB->get_record_sql($sql_query);
    
    if($risk_grade_object){
        $result->risk = $object->risk;
    }else{
        $result->risk = 0;
    }
    
    return $result;
}

/**
 * Función 
 *
 * @see 
 * @param 
 * @return
 */

function get_neighborhoods(){
    
    global $DB;

    $sql_query = "SELECT * FROM {talentospilos_barrios}";

    $array_neighborhoods = $DB->get_records_sql($sql_query);

    return $array_neighborhoods;
    
}

/**
 * Función que carga la información geográfica de un estudiante ASES 
 *
 * @see 
 * @param 
 * @return
 */

function load_geographic_info($id_ases){
    
    global $DB;

    $sql_query = "SELECT * FROM {talentospilos_demografia} WHERE id_usuario = $id_ases";
    $result = $DB->get_record_sql($sql_query);

    $sql_query = "SELECT id FROM {talentospilos_riesgos_ases} WHERE nombre = 'geografico'";
    $id_risk = $DB->get_record_sql($sql_query)->id;

    $sql_query = "SELECT * FROM {talentospilos_riesg_usuario} WHERE id_usuario = $id_ases AND id_riesgo = $id_risk";
    $register_risk = $DB->get_record_sql($sql_query);

    $result->risk = $register_risk->calificacion_riesgo;

    return $result;

}

/**
 * Función que guarda la información geográfica de un estudiante ASES
 *
 * @see 
 * @param 
 * @return
 */

function save_geographic_info($id_ases, $latitude, $longitude, $neighborhood, $geographic_risk){

    global $DB;

    $sql_query = "SELECT id FROM {talentospilos_demografia} WHERE id_usuario = $id_ases";
    $id_register = $DB->get_record_sql($sql_query)->id;

    $sql_query = "SELECT id FROM {talentospilos_riesgos_ases} WHERE nombre = 'geografico'";
    $id_risk = $DB->get_record_sql($sql_query)->id;

    $sql_query = "SELECT id FROM {talentospilos_riesg_usuario} WHERE id_usuario = $id_ases AND id_riesgo = $id_risk";
    $id_register_risk = $DB->get_record_sql($sql_query)->id;

    if($id_register_risk){
        $data_object_risk = new stdClass();
        $data_object_risk->id = (int)$id_register_risk;
        $data_object_risk->id_usuario = (int)$id_ases;
        $data_object_risk->id_riesgo = (int)$id_risk;
        $data_object_risk->calificacion_riesgo = (int)$geographic_risk;

        $result_geographic_risk = $DB->update_record('talentospilos_riesg_usuario', $data_object_risk);

    }else{
        $data_object_risk = new stdClass();
        $data_object_risk->id = (int)$id_register_risk;
        $data_object_risk->id_usuario = (int)$id_ases;
        $data_object_risk->id_riesgo = (int)$id_risk;
        $data_object_risk->calificacion_riesgo = (int)$geographic_risk;

        $result_geographic_risk = $DB->insert_record('talentospilos_riesg_usuario', $data_object_risk, true);
    }

    if($id_register){
        $data_object = new stdClass();
        $data_object->id = $id_register;
        $data_object->id_usuario = $id_ases;
        $data_object->latitud = $latitude;
        $data_object->longitud = $longitude;
        $data_object->barrio = $neighborhood;
    
        $result_geographic_info = $DB->update_record('talentospilos_demografia', $data_object);

    }else{
        $data_object = new stdClass();
        $data_object->id_usuario = $id_ases;
        $data_object->latitud = $latitude;
        $data_object->longitud = $longitude;
        $data_object->barrio = $neighborhood;

        $result_geographic_info = $DB->insert_record('talentospilos_demografia', $data_object, true);
    }

    if($result_geographic_info && $result_geographic_risk){
        return 1;
    }else{
        return 0;
    }    
}