<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Ases block
 *
 * @author     Jeison Cardona Gómez
 * @package    block_ases
 * @copyright  2018 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__). '/../../../../config.php');

function incident_create_incident( $user_id, $details, $system_info ){

    global $DB;

    if( !$DB->get_record_sql( "SELECT id FROM {user} WHERE id = '$user_id'" ) ){
        return null;
    }

    $obj_incident = new stdClass();
    $obj_incident->id_usuario_registra = (int) $user_id;
    $obj_incident->id_usuario_cierra = null;
    $obj_incident->estados = '[{"change_order":0,"status":"waiting"}]';
    $obj_incident->info_sistema = $system_info;
    $obj_incident->comentarios = json_encode([ 
        [ 
            "message_number" => 0,
            "user_id" => $user_id,
            "message" => $details
        ]
     ]);
     $obj_incident->cerrada = 0;

     return $DB->insert_record( 'talentospilos_incidencias', $obj_incident, $returnid=true, $bulk=false);
};


function incident_get_user_incidents( $user_id ){
    
    global $DB;

    if( !$user_id || !$DB->get_record_sql( "SELECT id FROM {user} WHERE id = '$user_id'" ) ){
        return null;
    }

    $sql = "SELECT * 
    FROM {talentospilos_incidencias} 
    WHERE id_usuario_registra = '$user_id'
    ORDER BY cerrada ASC, fecha_hora_registro DESC";

    return $DB->get_records_sql( $sql );
}

function incident_get_logged_user_incidents(){

    global $USER;

    return incident_get_user_incidents( $USER->id );
}

function incident_close_incident( $id, $closed_by_user_id ){
    
    global $DB;
    
    if( !$id || !$closed_by_user_id || !$DB->get_record_sql( "SELECT id FROM {user} WHERE id = '$closed_by_user_id'" ) ){
        return null;
    }
  
    $sql = "SELECT * 
    FROM {talentospilos_incidencias} 
    WHERE id = '$id'";

    $record = $DB->get_record_sql( $sql );
    
    if( $record ){

        $status = json_decode( $record->estados );
        $new_status = new stdClass();
        $new_status->change_order = -1;
        $new_status->status = "solved";
        
        foreach( $status as $key => $element ){
            if( $new_status->change_order <= $element->change_order ){
                $new_status->change_order = $element->change_order;
            }
        }
        $new_status->change_order++;

        array_push( $status, $new_status );

        $record->estados = json_encode( $status );
        $record->id_usuario_cierra = $closed_by_user_id;
        $record->cerrada = 1;
        return $DB->update_record( 'talentospilos_incidencias', $record, $bulk=false );

    }else{
        return null;
    }

}

function incident_close_logged_user_incident( $id ){

    global $USER;

    return incident_close_incident( $id, $USER->id );

}

function incident_get_all_incidents(){
    
    global $DB;

    $sql = "SELECT * 
    FROM {talentospilos_incidencias}
    ORDER BY cerrada ASC, fecha_hora_registro DESC";
    
    $records = $DB->get_records_sql( $sql );
    foreach( $records as $key => $record ){

        $info = json_decode( $record->comentarios )[0];

        $record->title = $info->message->title;
        $record->detail = $info->message->commentary;
    }

    return $records;
}

function incident_get_incident( $id ){
    
    global $DB;

    if( !is_numeric( $id ) ){
        return null;
    }

    $sql = "SELECT * 
    FROM {talentospilos_incidencias}
    WHERE id = '$id'";

    $record = $DB->get_record_sql( $sql );
    

    if( $record ){

        $record->fecha_hora_registro = strtotime($record->fecha_hora_registro);
        $record->usuario_registra = $DB->get_record_sql( "SELECT id, username, firstname, lastname FROM {user} WHERE id = '$record->id_usuario_registra'" );
        $record->usuario_cierra = null;

        if( $record->cerrada == 1 ){
            $record->usuario_cierra = $DB->get_record_sql( "SELECT id, username, firstname, lastname FROM {user} WHERE id = '$record->id_usuario_cierra'" );
        }
        
        return $record;

    }else{
        return null;
    }

}

function incident_get_ids_open_incidents(){

    global $DB;
    return $DB->get_records_sql( "SELECT id FROM {talentospilos_incidencias} WHERE cerrada = 0" );

}

?>
