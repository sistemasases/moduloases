<?php

require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

$array_conditions = array();

$new_condition = new stdClass();

array_push($array_conditions,"1. Indígena (I.N.)", "2. Los más altos puntajes en el Examen de Estado (M.A.P.)", "3. Comunidades Afrocolombianas (C.A.)", "4. Cupo (C.U.)"
                                , "5. Programa de Reinserción (P.R.)","6. Los más altos puntajes en el Examen de Estado, de los colegios oficiales en los municipios del Departamento del Valle del Cauca (M.P.M.)"
                                , "7. Aspirantes que estén realizando actualmente su último año de bachillerato, provenientes de Departamentos donde no existen sedes ni seccionales de Instituciones de Educación Superior (D.N.I)"
                                , "8. Aspirantes que estén realizando actualmente su último año de bachillerato, que provengan de Municipios de difícil acceso o con problemas de orden público (M.D.P.)"
                                ,  "9. Población Desplazada. (P.D.)") ;


foreach($array_conditions as $condition){
$new_condition->condicion_excepcion = $condition;
if($DB->insert_record("talentospilos_cond_excepcion", $new_condition, true)){
    echo "Éxito!";
}else {
    echo "Error.";}
 echo "Nada pendiente.";

};

?>
