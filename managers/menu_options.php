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
 * Estrategia ASES
 *
 * @author     Juan Pablo Moreno Muñoz
 * @package    block_ases
 * @copyright  2017 Juan Pablo Moreno Muñoz <moreno.juan@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once (dirname(__FILE__) . '/../../../config.php');

require_once ('permissions_management/permissions_lib.php');
require_once ('validate_profile_action.php');

/**
 * Creates each menu options
 * @see create_menu_options($userid, $blockid, $courseid)
 * @param $userid --> user id
 * @param $blockid --> block id
 * @param $courseid --> course id
 * @return string
 */

function create_menu_options($userid, $blockid, $courseid){

    $id_role = get_id_rol($userid, $blockid);
    $functions = get_functions_by_role_id($id_role);
    $menu_options = '';

    foreach($functions as $function){

        if($function == 'academic_reports'){
            $url = new moodle_url("/blocks/ases/view/academic_reports.php", array(
                'courseid' => $courseid,
                'instanceid' => $blockid
            ));

            $menu_options .= '<li><a href= "'. $url .'"> Reportes académicos </a><li>';
             
        }
        
        if($function == 'ases_report') {
            $url = new moodle_url("/blocks/ases/view/ases_report.php", array(
                'courseid' => $courseid,
                'instanceid' => $blockid
            ));

            $menu_options .= '<li><a href= "'. $url .'"> Reporte general </a><li>';
        }

        if($function == 'create_action'){
            $url = new moodle_url("/blocks/ases/view/create_action.php", array(
                'courseid' => $courseid,
                'instanceid' => $blockid
            ));

            $menu_options .= '<li><a href= "'. $url .'"> Gestión de permisos </a><li>';

        }

        if($function == 'grade_categories'){
            $url = new moodle_url("/blocks/ases/view/grade_categories.php", array(
                'courseid' => $courseid,
                'instanceid' => $blockid
            ));

            $menu_options .= '<li><a href= "'. $url .'"> Registro de notas </a><li>';

        }

        if($function == 'upload_historical_files'){
            $url = new moodle_url("/blocks/ases/view/upload_historical_files.php", array(
                'courseid' => $courseid,
                'instanceid' => $blockid
            ));

            $menu_options .= '<li><a href= "'. $url .'"> Carga de históricos </a><li>';

        }

        if($function == 'instance_configuration'){
            $url = new moodle_url("/blocks/ases/view/instance_configuration.php", array(
                'courseid' => $courseid,
                'instanceid' => $blockid
            ));

            $menu_options .= '<li><a href= "'. $url .'"> Gestión de instancia </a><li>';

        }

        if($function == 'mass_role_management'){
            $url = new moodle_url("/blocks/ases/view/mass_role_management.php", array(
                'courseid' => $courseid,
                'instanceid' => $blockid
            ));

            $menu_options .= '<li><a href= "'. $url .'"> Carga masiva </a><li>';

        }

        if($function == 'periods_management'){
            $url = new moodle_url("/blocks/ases/view/periods_management.php", array(
                'courseid' => $courseid,
                'instanceid' => $blockid
            ));

            $menu_options .= '<li><a href= "'. $url .'"> Gestión de períodos </a><li>';

        }

        if($function == 'groupal_tracking'){
            $url = new moodle_url("/blocks/ases/view/groupal_tracking.php", array(
                'courseid' => $courseid,
                'instanceid' => $blockid
            ));

            $menu_options .= '<li><a href= "'. $url .'"> Seguimiento grupal </a><li>';

        }

        if($function == 'report_trackings'){
            $url = new moodle_url("/blocks/ases/view/report_trackings.php", array(
                'courseid' => $courseid,
                'instanceid' => $blockid
            ));

            $menu_options .= '<li><a href= "'. $url .'"> Reportes de seguimientos </a><li>';

        }

        if($function == 'user_management'){
            $url = new moodle_url("/blocks/ases/view/user_management.php", array(
                'courseid' => $courseid,
                'instanceid' => $blockid
            ));

            $menu_options .= '<li><a href= "'. $url .'"> Gestión de usuarios </a><li>';

        }

        if($function == 'student_profile'){
            $url = new moodle_url("/blocks/ases/view/student_profile.php", array(
                'courseid' => $courseid,
                'instanceid' => $blockid
            ));

            $menu_options .= '<li><a href= "'. $url .'"> Ficha de estudiantes </a><li>';

        }

        if($function == 'upload_files_form'){
            $url = new moodle_url("/blocks/ases/view/upload_files_form.php", array(
                'courseid' => $courseid,
                'instanceid' => $blockid
            ));

            $menu_options .= '<li><a href= "'. $url .'"> Carga de archivos </a><li>';

        }
    
    }

    return $menu_options;

    

}