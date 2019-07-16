<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/*
require_once(dirname(__FILE__). '/../../config.php');
require_once $CFG->dirroot.'/blocks/ases/managers/dphpforms/v2/dphpforms_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/monitor_assignments/monitor_assignments_lib.php';


$xQuery = new stdClass();
$xQuery->form = "seguimiento_pares"; // Can be alias(String) or identifier(Number)
$xQuery->filterFields = [
    ["id_estudiante",[ ["%%","LIKE"]], false],
    ["fecha",[ ["2018-08-01",">"], ["2019-05-21","<"] ], false],
    ["revisado_practicante",[ ["%%","LIKE"] ], false],
    ["revisado_profesional",[ ["%%","LIKE"] ], false]
];
$xQuery->orderFields = [ ["fecha","DESC"] ];
$xQuery->orderByDatabaseRecordDate = false; // If true, 'orderField' is ignored. DESC
$xQuery->recordStatus = [ "!deleted" ]; // options "deleted" or "!deleted", can be both. Empty = both.
$xQuery->asFields = []; 
$xQuery->selectedFields = []; // Without support.

$trackings = dphpformsV2_find_records($xQuery);

$asignation = monitor_assignments_get_practicants_monitors_and_students( "450299", "2019A" );

print_r( $asignation[0] );
print_r( $trackings[0] );*/


