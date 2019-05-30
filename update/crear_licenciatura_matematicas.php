<?php
/*

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once(__DIR__ .'/../classes/module.php');

$programa = new Programa();
$nombre_sede ='CALI';
$nombre_facultad = 'INSTITUTO DE EDUCACIÓN Y PEDAGOGÍA';
$programa->nombre = 'LICENCIATURA EN MATEMÁTICAS';
//@var Sede $sede
$sede = Sede::get_one_by(array(Sede::NOMBRE=>$nombre_sede));
//@var Facultad $facultad
$facultad = Facultad::get_one_by(array(Facultad::NOMBRE=>$nombre_facultad));
if(!$sede) die("No existe la sede con nombre $nombre_sede");
if(!$facultad) die("No existe la facultad con nombre $nombre_facultad");
$programa->id_sede = $sede->id;
$programa->jornada = Jornada::DIURNA;
$programa->id_facultad = $facultad->id;
$programa->cod_univalle = '3492';
$programa->codigosnies = '101333';

if($programa->save()) {
    echo "El programa $programa->nombre se ha guardado";
} else {
    echo "No se ha podido guardar el programa $programa->nombre";
}

*/
