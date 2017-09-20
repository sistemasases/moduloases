<?php

require_once(dirname(__FILE__). '/../../../config.php');

if (!($handle = fopen("../files/departamentos.csv", 'r'))) throw new MyException("Error al cargar el archivo: 'departamentos.csv'. Es posible que el archivo se encuentre dañado");

pg_query("BEGIN") or die("No es posible iniciar la transacción en la base de datos\n");

global $DB;
// esta tabla no depende de otra
$record = new stdClass();
$count = 0;
     
while($data = fgetcsv($handle, 1000, ",")){
    $record->codigodivipola = $data[0];
    $record->nombre = $data[1];
    
    $DB->insert_record('talentospilos_departamento', $record, false);
    $count+=1;
}

//se termina la transaccion
pg_query("COMMIT") or die("La transacción ha fallado\n");
fclose($handle);