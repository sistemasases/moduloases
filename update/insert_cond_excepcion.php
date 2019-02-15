<?php

require_once (__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../classes/CondicionExcepcion.php');

$condicion_excepcion = new CondicionExcepcion();
$condicion_excepcion->condicion_excepcion = "Condición de excepción V.C";
$condicion_excepcion->alias = "V.C.";
try {
    if (CondicionExcepcion::exists(array(CondicionExcepcion::ALIAS => $condicion_excepcion->alias))) {
        echo "Una condición de excepción con el alias $condicion_excepcion->alias ya existe";
        die;
    }
    if (CondicionExcepcion::exists(array(CondicionExcepcion::CONDICION_EXCEPCION => $condicion_excepcion->condicion_excepcion))) {
        echo "Una condición de excepción con el campo 'condicion_excepcion' con el valor $condicion_excepcion->condicion_excepcion ya existe";
        die;
    }
    $condicion_excepcion->save();
    echo "La condición de excepción se ha guardado con exito, datos guardados: ";
    echo '<pre>',print_r($condicion_excepcion,1),'</pre>';
} catch (dml_exception $e) {
    print_r($e);
}
?>
