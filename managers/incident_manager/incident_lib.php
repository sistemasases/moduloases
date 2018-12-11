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
require_once $CFG->dirroot.'/blocks/ases/managers/incident_manager/incident_lib.php';

function incident_create_incident( $user_id, $details, $system_info ){

    global $DB;

    $obj_incident = new stdClass();
    $obj_incident->id_usuario_registra = (int) $user_id;
    $obj_incident->id_usuario_cierra = null;
    $obj_incident->estados = '[ { "change_order":"0", "status":"waiting" } ]';
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


?>
