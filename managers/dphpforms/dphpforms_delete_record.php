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
 * Dynamic PHP Forms
 *
 * @author     Jeison Cardona Gómez
 * @package    block_ases
 * @copyright  2018 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs

    require_once(dirname(__FILE__). '/../../../../config.php');
        
    if( isset( $_GET['record_id'] ) ){
        header('Content-Type: application/json');
        echo dphpforms_delete_record( $_GET['record_id'] );
    }

    function dphpforms_delete_record( $record_id ){

        global $DB;

        if(!is_numeric( $record_id )){
            return json_encode(
                array(
                    'status' => '-1',
                    'message' => 'Invalid record id',
                    'data' => ''
                )
            );
        }

        $sql = "SELECT * FROM {talentospilos_df_form_resp} WHERE id = '$record_id' AND estado = '1'";
        $result = $DB->get_record_sql($sql);
        
        if($result){
            
            $deleted_record = new stdClass();
            $deleted_record->id = $result->id;
            $deleted_record->id_formulario = $result->id_formulario; 
            $deleted_record->id_monitor = $result->id_monitor;
            $deleted_record->id_estudiante = $result->id_estudiante;
            $deleted_record->fecha_hora_registro = $result->fecha_hora_registro;
            $deleted_record->estado = '0';
            $DB->update_record('talentospilos_df_form_resp', $deleted_record, $bulk=false);
            return json_encode(
                array(
                    'status' => '0',
                    'message' => 'Deleted',
                    'data' => ''
                )
            );
            
        }else{
            return json_encode(
                array(
                    'status' => '-1',
                    'message' => 'Record does not exist',
                    'data' => ''
                )
            );
        }

    }


?>