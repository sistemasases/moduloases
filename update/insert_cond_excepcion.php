<?php

require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

     
//Insert register into database
$array_tables = array();
array_push($array_tables,"talentospilos_cond_excepcion","talentospilos_act_simultanea", 
            "talentospilos_sexo","talentospilos_identidad_gen","talentospilos_etnia", "talentospilos_estado_civil");



//Insert for each table
for($t=0; $t<count($array_tables);$t++){
$array_elements = array();
$array_aditional = array();
$new_register = new stdClass();

$table  = $array_tables[$t];
echo $table;
//Data switch table
if($table=="talentospilos_cond_excepcion"){
//Data to cond_excepcion

array_push($array_elements,"Indígena (I.N.)", "Los más altos puntajes en el Examen de Estado (M.A.P.)", "Comunidades Afrocolombianas (C.A.)", "Cupo (C.U.)"
                                , "Programa de Reinserción (P.R.)","Los más altos puntajes en el Examen de Estado, de los colegios oficiales en los municipios del Departamento del Valle del Cauca (M.P.M.)"
                                , "Aspirantes que estén realizando actualmente su último año de bachillerato, provenientes de Departamentos donde no existen sedes ni seccionales de Instituciones de Educación Superior (D.N.I)"
                                , "Aspirantes que estén realizando actualmente su último año de bachillerato, que provengan de Municipios de difícil acceso o con problemas de orden público (M.D.P.)"
                                ,  "Población Desplazada. (P.D.)", "Ninguna de las anteriores") ;
array_push($array_aditional,"I.N.", "M.A.P.", "C.A.", "C.U."
                                , "P.R.","M.P.M."
                                , "D.N.I"
                                , "M.D.P."
                                ,  "P.D.", "N.A") ;
}
if($table=="talentospilos_act_simultanea"){
//Data to act_simultanea
array_push($array_elements, "Monitor","Docente","Empleado","Ninguna") ;
echo $array_elements[0];
}
if($table == "talentospilos_sexo"){
//Data to sexo
array_push($array_elements, "Masculino","Femenino")  ;
echo $array_elements[0]; 
}
if($table == "talentospilos_identidad_gen"){
    //Data to genero
    array_push($array_elements, "Hombre", "Mujer","Transgénero femenino", "Transgénero masculino", "Persona sin género");
    echo $array_elements[0]; 
}
if($table == "talentospilos_etnia"){
        //Data to etnia
        array_push($array_elements, "Indígena","Rom", "Raizal del archipiélago de San Andres y Providencia", "Palenquero de San Basilo", "Negro(a), Mulato(a), Afrocolombiano(a), Afrodescendiente",
                                    "Blanco(a), Mestizo(a)", "Ninguno de los anteriores");
        echo $array_elements[0]; 
}
if($table == "talentospilos_estado_civil"){
    //Data to estado_civil
    array_push($array_elements, "Casado(a)", "Soltero(a)", "Divorciado(a)","Separado(a)","Unión libre","Viudo(a)");
    echo $array_elements[0]; 
}

//INSERT INTO DB
for  ($i = 0; $i < count($array_elements); $i++){

    //CREATE OBJECT FOR EACH ELEMENT
    if($table== "talentospilos_cond_excepcion"){
    $new_register->condicion_excepcion = $array_elements[$i];  
    $new_register->alias = $array_aditional[$i];
    $condition = 'condicion_excepcion';
    }
    if($table== "talentospilos_act_simultanea"){
        $new_register->actividad = $array_elements[$i];  
        $new_register->opcion_general = 1;
        $condition = 'actividad';
        }
    if($table=="talentospilos_sexo"){
        $new_register->sexo = $array_elements[$i];  
        $new_register->opcion_general = 1;
        $condition = 'sexo';
    }
    if($table=="talentospilos_identidad_gen"){
        $new_register->genero = $array_elements[$i];  
        $new_register->opcion_general = 1;
        $condition = 'genero';
    }
    if($table=="talentospilos_etnia"){
        $new_register->etnia = $array_elements[$i];  
        $new_register->opcion_general = 1;
        $condition = 'etnia';
    }
    if($table=="talentospilos_estado_civil"){
        $new_register->estado_civil = $array_elements[$i];  
        $condition = 'estado_civil';
    }

        echo $array_elements[$i];
    if( !$DB->record_exists($table,array($condition=> $array_elements[$i]))){
        if($DB->insert_record($table, $new_register, true)){
            echo "Éxito!";
        }else {
            echo "Error.";}
         echo "Nada pendiente.";
    }
    }

unset($array_elements,$array_aditional,$new_register, $table);
}

echo "----zz";
unset($array_conditions, $new_condition);
echo $array_conditions[1]."  ".$new_condition;

?>
