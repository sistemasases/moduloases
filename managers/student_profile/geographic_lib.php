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