<?php

require_once(dirname(__FILE__). '/../../../config.php');

if (!($handle = fopen("../files/semestre.csv", 'r'))) throw new MyException("Error al cargar el archivo: 'semestre.csv'. Es posible que el archivo se encuentre dañado");

pg_query("BEGIN") or die("No es posible iniciar la transacción en la base de datos\n");

global $DB;
$record = new stdClass();

while($data = fgetcsv($handle, 100, ",")){
    $record->nombre = $data[0];
    $record->fecha_inicio = $data[1];
    $record->fecha_fin = $data[2];
    $DB->insert_record('talentospilos_semestre', $record);
}

//se termina la transaccion
pg_query("COMMIT") or die("La transacción ha fallado\n");
fclose($handle);