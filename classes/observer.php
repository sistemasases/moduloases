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
 * 
 *
 *
 * @package    block_ases
 * @copyright  2018 Iader E. García G.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();
require_once dirname(__FILE__) . '/../../../config.php';

class block_ases_observer{

    public static function user_graded(\core\event\user_graded $event)
    {
        global $DB;

        self::process_event($event);

    }

    protected static function process_event($event){

        global $DB;

        $eventData = $event->get_data();
        $eventData = json_encode($eventData);
///INICIO PRUEBA
        // $obj = new stdClass;
        // date_default_timezone_set("America/Bogota");
        // $today = time();
        // $obj->id_estudiante=$today;
        // $obj->id_semestre = 1;
        // $obj->id_programa = 0;
        // $obj->json_materias = $eventData;


        // $alerta->id_estudiante = 666;
        // $alerta->id_item = 666;
        // $alerta->id_user_registra = 666;
        // $alerta->nota = 0;
        // $alerta->fecha = $today;
        // // $DB->insert_record('talentospilos_alertas_academ', $alerta);
        // $DB->insert_record('talentospilos_history_academ', $obj);
    ////FIN PRUEBA    

    }
}
