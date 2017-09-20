<?php

require_once(dirname(__FILE__). '/../../../config.php');

if (!($handle = fopen("../files/programa.csv", 'r'))) throw new MyException("Error al cargar el archivo: 'programa.csv'. Es posible que el archivo se encuentre dañado");

pg_query("BEGIN") or die("No es posible iniciar la transacción en la base de datos\n");

global $DB;
$record = new stdClass();
$count = 0;
$array_id_sede = array();
$array_id_fac = array();
$array_data = array();
$line_count = 1;

while($data = fgetcsv($handle, 1000, ",")){

    array_push($array_data, $data);
    
    $query = "SELECT id FROM {talentospilos_sede} WHERE cod_univalle ='".intval($data[3])."';";
    $result = $DB->get_record_sql($query);
    if(!$result){
       throw new MyException("Por favor revisa la linea ".$line_count.".<br>El codigo Univalle de la sede ".$data[0]." asociado al programa ".$data[2]." no se encuentra en la base de datos");
    }
    array_push($array_id_sede, $result->id);
    
    //se verifica el codigo de l facultad
    $query = "SELECT id FROM {talentospilos_facultad} WHERE cod_univalle ='".$data[4]."';";
    $result = $DB->get_record_sql($query);
    if(!$result){
       throw new MyException("El codigo Univalle de la facultad ".intval($data[4])." asociado al programa ".$data[2]." no se encuentra en la base de datos. linea ".$line_count." ".$data[0]."-".$data[1]."-".$data[2]."-".$data[3]."-".$data[4]."");
    }
    $line_count+=1;
    array_push($array_id_fac, $result->id);
}

foreach($array_data as $data){ 
   $record->codigosnies = $data[0];
   $record->cod_univalle = $data[1];
   $record->nombre = $data[2];
   $record->id_sede = $array_id_sede[$count];
   $record->id_facultad = $array_id_fac[$count];
   $record->jornada = $data[5];
   
   $DB->insert_record('talentospilos_programa', $record, false);
  $count+=1;
}

//se termina la transaccion
pg_query("COMMIT") or die("La transacción ha fallado\n");
fclose($handle);