<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 25/04/19
 * Time: 04:15 PM
 */
require_once (__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../classes/CondicionExcepcion.php');
$alias =  'N.A';
try{
    /** @var CondicionExcepcion $condicion_excepcion */
$condicion_excepcion = CondicionExcepcion::get_one_by(array(CondicionExcepcion::ALIAS=>$alias));
if(!$condicion_excepcion) {
    echo "No existe una condición de excepción con alias '$alias'";
}
} catch(Exception $e) {
    print_r($e);
    die;
}

if(!$condicion_excepcion) {
    echo "No existe la condición de exepción con alias $alias";
} else {
    $condicion_excepcion->alias = 'N.A.';
    if($condicion_excepcion->save()) {
        echo "Se ha actualizado la condición de excepcion con alis '$alias' a alias 'N.A.'";

    }else {
       echo "No se ha podido actualizar la condición de excepción";
    }
}