<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/*
require_once(dirname(__FILE__) . '/../../config.php');
require_once $CFG->dirroot . '/blocks/ases/managers/dphpforms/v2/dphpforms_lib.php';
require_once $CFG->dirroot . '/blocks/ases/managers/monitor_assignments/monitor_assignments_lib.php';

//Seguimientos
$xQuery = new stdClass();
$xQuery->form = "seguimiento_pares"; // Can be alias(String) or identifier(Number)
$xQuery->filterFields = [
    ["id_estudiante", [["%%", "LIKE"]], false],
    ["fecha", [["2019-05-20", ">="], ["2019-08-01", "<="]], false],
    ["revisado_practicante", [["%%", "LIKE"]], false],
    ["revisado_profesional", [["%%", "LIKE"]], false],
    ["id_estudiante", [["%%", "LIKE"]], false]
];
$xQuery->orderFields = [["fecha", "DESC"]];
$xQuery->orderByDatabaseRecordDate = false; // If true, 'orderField' is ignored. DESC
$xQuery->recordStatus = ["!deleted"]; // options "deleted" or "!deleted", can be both. Empty = both.
$xQuery->asFields = [];
$xQuery->selectedFields = []; // Without support.

$trackings = dphpformsV2_find_records($xQuery);

//Inasistencias
$xQuery = new stdClass();
$xQuery->form = "inasistencia"; // Can be alias(String) or identifier(Number)
$xQuery->filterFields = [
    ["in_id_estudiante", [["%%", "LIKE"]], false],
    ["in_fecha", [["2019-05-20", ">"], ["2019-08-01", "<"]], false],
    ["in_revisado_practicante", [["%%", "LIKE"]], false],
    ["in_revisado_profesional", [["%%", "LIKE"]], false],
    ["in_id_estudiante", [["%%", "LIKE"]], false]
];
$xQuery->orderFields = [["in_fecha", "DESC"]];
$xQuery->orderByDatabaseRecordDate = false; // If true, 'orderField' is ignored. DESC
$xQuery->recordStatus = ["!deleted"]; // options "deleted" or "!deleted", can be both. Empty = both.
$xQuery->asFields = [];
$xQuery->selectedFields = []; // Without support.

$in_trackings = dphpformsV2_find_records($xQuery);

$asignation = monitor_assignments_get_practicants_monitors_and_studentsV2("450299", "2019A");

$index = [];

foreach ($asignation as $key => $asig) {
    $index[$asig->codigo_ases] = $asig;
}

foreach ($trackings as $track) {
    if ($track["revisado_profesional"] == 0) {
        addToCounter1($track['id_estudiante'], 'revisado_profesional');
    } else {
        addToCounter1($track['id_estudiante'], 'not_revisado_profesional');
    }
    if ($track["revisado_practicante"] == 0) {
        addToCounter1($track['id_estudiante'], 'revisado_practicante');
    } else {
        addToCounter1($track['id_estudiante'], 'not_revisado_practicante');
    }
    addToCounter1($track['id_estudiante'], 'total_profesional');
    addToCounter1($track['id_estudiante'], 'total_practicante');
}

foreach ($in_trackings as $track) {
    if ($track["in_revisado_profesional"] == 0) {
        addToCounter1($track['in_id_estudiante'], 'in_revisado_profesional');
    } else {
        addToCounter1($track['in_id_estudiante'], 'in_not_revisado_profesional');
    }
    if ($track["in_revisado_practicante"] == 0) {
        addToCounter1($track['in_id_estudiante'], 'in_revisado_practicante');
    } else {
        addToCounter1($track['in_id_estudiante'], 'in_not_revisado_practicante');
    }
    
    addToCounter1($track['in_id_estudiante'], 'in_total_profesional');
    addToCounter1($track['in_id_estudiante'], 'in_total_practicante');
}


function addToCounter1( $username, $key ){
    
    global $count;
    global $index;
    
    if( is_null( $count[ $index[ $username ]->moodle_id_profesional][$key] ) ){
        $count[$index[$username]->moodle_id_profesional][$key] = 1;
    }else{
        $count[$index[$username]->moodle_id_profesional][$key]++;
    }
    
    if( is_null( $count[ $index[ $username ]->moodle_id_practicante][$key] ) ){
        $count[$index[$username]->moodle_id_practicante][$key] = 1;
    }else{
        $count[$index[$username]->moodle_id_practicante][$key]++;
    }
    
    if( is_null( $count[ $index[ $username ]->moodle_id_monitor][$key] ) ){
        $count[$index[$username]->moodle_id_monitor][$key] = 1;
    }else{
        $count[$index[$username]->moodle_id_monitor][$key]++;
    }
    
    if( is_null( $count[ "A".$index[ $username ]->codigo_ases][$key] ) ){
        $count["A".$index[$username]->codigo_ases][$key] = 1;
    }else{
        $count["A".$index[$username]->codigo_ases][$key]++;
    }
}

print_r($count);*/


