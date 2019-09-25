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
 * ASES
 *
 * @author     Jeison Cardona Gómez.
 * @package    block_ases
 * @copyright  2018 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
require_once __DIR__ . '/../../../config.php';
require_once $CFG->libdir . '/adminlib.php';

require_once('../managers/lib/lib.php');
require_once('../managers/instance_management/instance_lib.php');
require_once('../managers/menu_options.php');
include_once("../managers/incident_manager/incident_lib.php");
include('../lib.php');


global $PAGE;
global $USER;

$incident_id = required_param('incident_id', PARAM_INT);
$incident = incident_get_incident( $incident_id );

if( $incident ){
    print_r( '<script>alert("Esto corresponde a una captura, no es una redirección a una vista específica del sistema. Clicks sobre botones pueden tener efectos sobre el sistema, explore con cuidado.")</script>' );
    print_r( $incident->info_sistema );
}else{
    print_r( '<h1>No se encontró el recurso solicitado.</h1>' );
}



?>