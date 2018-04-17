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

    require_once(dirname(__FILE__). '/../../../../../config.php');
    require_once(dirname(__FILE__). '/../../lib/student_lib.php');

    global $DB;

    $alias = 'seguimiento_pares_id_estudiante';
    $sql_find_preg = "SELECT id_pregunta FROM {talentospilos_df_alias} WHERE alias = '$alias'";
    $preg_record = $DB->get_record_sql( $sql_find_preg );

    $id_pregunta = $preg_record->id_pregunta;
    
    
    $sql_find_records = "SELECT * FROM {talentospilos_df_respuestas} WHERE id_pregunta = '$id_pregunta'";
    $records = $DB->get_records_sql( $sql_find_records );

    $cambios = array();
    $registros_validos = 0;

    foreach( $records as $record ){

        if( is_numeric( $record->respuesta ) ){
            $registros_validos++;
            $ases_user = get_ases_user_by_code( $record->respuesta );
            $tmp = array(
                'old_value' => $record->respuesta,
                'new_value' => $ases_user->id,
                'record_id' => $record->id
            );
            array_push( $cambios, $tmp );
            echo 'Cambio: ' . $record->respuesta . ' → ' . $ases_user->id . ' RID: ' . $record->id . '<br>';
        }else{
            echo 'NO VALIDO!: ' . $record->respuesta . '<br>';
        }
        
    }

    echo  '<br>Registros totales:' . count( $records ) . ' Registros válidos:' . $registros_validos . ' Registros actualizables: ' . count( $cambios ) . '<br><br>';

    print_r( json_encode( $cambios ) );

    die();

?>