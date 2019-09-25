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

    /*require_once(dirname(__FILE__). '/../../../../../config.php');
    require_once(dirname(__FILE__). '/../../lib/student_lib.php');*/

    //global $DB;

    //$fecha_hora_registro = "'2018-08-01'";

    //Missing VALUES{  id_monitor, id_practicante, id_profesional, username  } en seguimiento_pares e inasistencias


    /**
     * SEGUIMIENTO DE PARES
     */

    //////////////////////////////////
    // Records to update id_monitor //
    //////////////////////////////////

    /*$records_to_update = "SELECT id AS id_formulario_respuestas
    FROM {talentospilos_df_form_resp} 
    WHERE id_formulario = ( SELECT id FROM {talentospilos_df_formularios} WHERE alias = 'seguimiento_pares' AND estado = 1 ) 
    
    EXCEPT    
    
    SELECT FS.id_formulario_respuestas 
    FROM {talentospilos_df_form_solu} AS FS 
    INNER JOIN {talentospilos_df_respuestas} AS R 
    ON FS.id_respuesta = R.id 
    WHERE R.id_pregunta = 61 AND FS.fecha_hora_registro >= $fecha_hora_registro";

    $records = $DB->get_records_sql( $records_to_update );

    print_r( count( $records ) );
*/
    


?>