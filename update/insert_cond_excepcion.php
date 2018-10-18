<?php

require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

 $sql_query = "ALTER TABLE  {talentospilos_cond_excepcion} AUTO_INCREMENT = 1";
   
    echo $DB->execute($sql_query);

// $array_conditions = array();
// $array_alias = array();

// $new_condition = new stdClass();

// array_push($array_conditions,"1. Indígena (I.N.)", "2. Los más altos puntajes en el Examen de Estado (M.A.P.)", "3. Comunidades Afrocolombianas (C.A.)", "4. Cupo (C.U.)"
//                                 , "5. Programa de Reinserción (P.R.)","6. Los más altos puntajes en el Examen de Estado, de los colegios oficiales en los municipios del Departamento del Valle del Cauca (M.P.M.)"
//                                 , "7. Aspirantes que estén realizando actualmente su último año de bachillerato, provenientes de Departamentos donde no existen sedes ni seccionales de Instituciones de Educación Superior (D.N.I)"
//                                 , "8. Aspirantes que estén realizando actualmente su último año de bachillerato, que provengan de Municipios de difícil acceso o con problemas de orden público (M.D.P.)"
//                                 ,  "9. Población Desplazada. (P.D.)") ;
// array_push($array_alias,"1. I.N.", "2. M.A.P.", "3. C.A.", "4. C.U."
//                                 , "5. P.R.","6. M.P.M."
//                                 , "7. D.N.I"
//                                 , "8. M.D.P."
//                                 ,  "9. P.D.") ;

// for  ($i = 0; $i < count($array_conditions); $i++){


// $new_condition->condicion_excepcion = $array_conditions[$i];  
// $new_condition->alias = $array_alias[$i];
    

// if($DB->insert_record("talentospilos_cond_excepcion", $new_condition, true)){
//     echo "Éxito!";
// }else {
//     echo "Error.";}
//  echo "Nada pendiente.";

// };

?>
