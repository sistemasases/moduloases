<?php

require_once(dirname(__FILE__). '/../../../config.php');

if (!($handle = fopen("../files/sede.csv", 'r'))) throw new MyException("Error al cargar el archivo: 'sede.csv'. Es posible que el archivo se encuentre dañado");

pg_query("BEGIN") or die("No es posible iniciar la transacción en la base de datos\n");

 global $DB;
 $record = new stdClass();
 $count = 0;
 $array_id = array();
 $array_data = array();
 $line_count=1;
 
 while($data = fgetcsv($handle, 100, ",")){
    array_push($array_data, $data);
    
    $query = "SELECT id FROM {talentospilos_municipio} WHERE codigodivipola ='".intval($data[0])."';";
    $result = $DB->get_record_sql($query);
    if(!$result){
       throw new MyException("Por favor revisa la linea".$line_count.".<br>El codigo de División Política de la ciudad ".$data[0]." asociado a la sede".$data[2]." no se encuentra en la base de datos");
    }
    array_push($array_id, $result->id);
    $line_count+=1;
 }

 foreach ($array_data as $data){ 
    $record->id_ciudad = $array_id[$count];
    $record->cod_univalle = $data[1];
    $record->nombre = $data[2];
    
    $DB->insert_record('talentospilos_sede', $record, false);
   $count+=1;
 }

//se termina la transaccion
pg_query("COMMIT") or die("La transacción ha fallado\n");
fclose($handle);