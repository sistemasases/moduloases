<?php

require_once(dirname(__FILE__). '/../../../config.php');

if (!($handle = fopen("../files/municipios.csv", 'r'))) throw new MyException("Error al cargar el archivo: 'departamentos.csv'. Es posible que el archivo se encuentre dañado");

pg_query("BEGIN") or die("No es posible iniciar la transacción en la base de datos\n");

global $DB;
$record = new stdClass();
$count = 0;
$array_id = array();
$array_data = array();
$line_count =1;

while($data = fgetcsv($handle, 100, ",")){  
    array_push($array_data, $data);
    
    $query = "SELECT id FROM {talentospilos_departamento} WHERE codigodivipola = ".intval($data[1]).";";
    
    $result = $DB->get_record_sql($query);
    
    if(!$result){
       throw new MyException("Por favor Revisa la línea ".$line_count.".<br>El codigo de División Política del departamento ".$data[1]." asociado al  municipio ".$data[2]." no se encuentra en la base de datos");
    }
    array_push($array_id, $result->id);
    $line_count+=1;
}

foreach ($array_data as $dat){
    $record->codigodivipola = $dat[0];
    $record->cod_depto = $array_id[$count];
    $record->nombre = $dat[2];
    $DB->insert_record('talentospilos_municipio', $record, false);
    $count += 1;
}

//se termina la transaccion
pg_query("COMMIT") or die("La transacción ha fallado\n");
fclose($handle);