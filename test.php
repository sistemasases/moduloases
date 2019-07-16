<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(__FILE__). '/../../config.php');
require_once $CFG->dirroot.'/blocks/ases/managers/dphpforms/v2/dphpforms_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/monitor_assignments/monitor_assignments_lib.php';

//Seguimientos
$xQuery = new stdClass();
$xQuery->form = "seguimiento_pares"; // Can be alias(String) or identifier(Number)
$xQuery->filterFields = [
    ["id_estudiante",[ ["%%","LIKE"]], false],
    ["fecha",[ ["2018-08-01",">"], ["2019-05-21","<"] ], false],
    ["revisado_practicante",[ ["%%","LIKE"] ], false],
    ["revisado_profesional",[ ["%%","LIKE"] ], false],
    ["username",[ ["%%","LIKE"] ], false]
];
$xQuery->orderFields = [ ["fecha","DESC"] ];
$xQuery->orderByDatabaseRecordDate = false; // If true, 'orderField' is ignored. DESC
$xQuery->recordStatus = [ "!deleted" ]; // options "deleted" or "!deleted", can be both. Empty = both.
$xQuery->asFields = []; 
$xQuery->selectedFields = []; // Without support.

$trackings = dphpformsV2_find_records($xQuery);

//Inasistencias
$xQuery = new stdClass();
$xQuery->form = "inasistencia"; // Can be alias(String) or identifier(Number)
$xQuery->filterFields = [
    ["in_id_estudiante",[ ["%%","LIKE"]], false],
    ["in_fecha",[ ["2018-08-01",">"], ["2019-05-21","<"] ], false],
    ["in_revisado_practicante",[ ["%%","LIKE"] ], false],
    ["in_revisado_profesional",[ ["%%","LIKE"] ], false],
    ["in_username",[ ["%%","LIKE"] ], false]
];
$xQuery->orderFields = [ ["in_fecha","DESC"] ];
$xQuery->orderByDatabaseRecordDate = false; // If true, 'orderField' is ignored. DESC
$xQuery->recordStatus = [ "!deleted" ]; // options "deleted" or "!deleted", can be both. Empty = both.
$xQuery->asFields = []; 
$xQuery->selectedFields = []; // Without support.

$in_trackings = dphpformsV2_find_records($xQuery);

$asignation = monitor_assignments_get_practicants_monitors_and_students( "450299", "2019A" );

$index = [];

foreach ($asignation as $key => $asig){
    $index[ $asig->codigo_estudiante ] = $asig;
}

print_r( $asignation[0] );
print_r( $index['1929019-3749'] );
print_r( $trackings[0] );
print_r( $in_trackings[0] );

    $rev_pro = 0;
    $not_rev_pro = 0;
    $rev_prac = 0;
    $not_rev_prac = 0;

    foreach( $trackings as $track ){
        if( $track["revisado_profesional"] == 0 ){
            $rev_pro++;
        }else{
            $not_rev_pro++;
        }
        if( $track["revisado_practicante"] == 0 ){
            $rev_prac++;
        }else{
            $not_rev_prac++;
        }
        $count[ $index[$track['username']]->nombre_profesional ]['revisado_profesional'] = $rev_pro;
        $count[ $index[$track['username']]->nombre_profesional ]['not_revisado_profesional'] = $not_rev_pro;
        $count[ $index[$track['username']]->nombre_profesional ]['total_profesional'] = $rev_pro + $not_rev_pro;
        $count[ $index[$track['username']]->nombre_profesional ]['revisado_practicante'] = $rev_prac;
        $count[ $index[$track['username']]->nombre_profesional ]['not_revisado_practicante'] = $not_rev_prac;
        $count[ $index[$track['username']]->nombre_profesional ]['total_practicante'] = $rev_prac + $not_rev_prac;
    }

    $in_trackings = dphpformsV2_find_records( $xQuery );

    $in_rev_pro = 0;
    $in_not_rev_pro = 0;
    $in_rev_prac = 0;
    $in_not_rev_prac = 0;

    foreach( $in_trackings as $track ){
        if( $track["in_revisado_profesional"] == 0 ){
            $in_rev_pro++;
        }else{
            $in_not_rev_pro++;
        }
        if( $track["in_revisado_practicante"] == 0 ){
            $in_rev_prac++;
        }else{
            $in_not_rev_prac++;
        }
        
        $count[ $index[$track['username']]->nombre_profesional ]['in_revisado_profesional'] = $in_rev_pro;
        $count[ $index[$track['username']]->nombre_profesional ]['in_not_revisado_profesional'] = $in_not_rev_pro;
        $count[ $index[$track['username']]->nombre_profesional ]['in_total_profesional'] = $in_rev_pro + $in_not_rev_pro;
        $count[ $index[$track['username']]->nombre_profesional ]['in_revisado_practicante'] = $in_rev_prac;
        $count[ $index[$track['username']]->nombre_profesional ]['in_not_revisado_practicante'] = $in_not_rev_prac;
        $count[ $index[$track['username']]->nombre_profesional ]['in_total_practicante'] = $in_rev_prac + $in_not_rev_prac;
    }    

    print_r( $count );


