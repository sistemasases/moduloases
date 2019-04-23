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
require_once dirname(__FILE__) . '/../../../config.php';

require_once 'permissions_management/permissions_lib.php';
require_once 'validate_profile_action.php';

/**
 * Creates each menu options
 * @see create_menu_options($userid, $blockid, $courseid)
 * @param $userid --> user id
 * @param $blockid --> block id
 * @param $courseid --> course id
 * @return string
 */

function create_menu_options($userid, $blockid, $courseid)
{

    $menu_options = '';
    $dropdown_close_tags = '</div>
                            </div>';
    $academic_dropdown = '<div id="academic_dropdown" class="dropdown">
                            <button class="dropbtn">Académico <i class="fa fa-caret-down"></i>
                            </button>
                            <div class="dropdown-content">';
    $academic_options = array();
    $reports_dropdown = '<div id="reports_dropdown" class="dropdown">
                            <button class="dropbtn">Reportes <i class="fa fa-caret-down"></i>
                            </button>
                            <div class="dropdown-content">';
    $reports_options = array();
    $icetex_men_dropdown = '<div id="icetex_men_dropdown" class="dropdown">
                            <button class="dropbtn">ICETEX/MEN <i class="fa fa-caret-down"></i>
                            </button>
                            <div class="dropdown-content">';
    $icetex_options = array();                        
    $soc_ed_dropdown = '<div id="soc_ed_dropdown" class="dropdown">
                            <button class="dropbtn">Socioeducativo <i class="fa fa-caret-down"></i>
                            </button>
                            <div class="dropdown-content">';
    $soc_ed_options = array();                        
    $admin_dropdown = '<div id="admin_dropdown" class="dropdown">
                            <button class="dropbtn">Sistemas <i class="fa fa-caret-down"></i>
                            </button>
                            <div class="dropdown-content">'; 
    $admin_options = array();
    $discapacity_dropdown = '<div id="discapacity_dropdown" class="dropdown">
                                <button class="dropbtn">Discapacidad <i class="fa fa-caret-down"></i>
                                </button>
                                <div class="dropdown-content">';
    $discapacity_options = array();
    $menu_return = "";
    $id_role = get_id_rol($userid, $blockid);
     
    if($id_role != ""){
        $functions = get_functions_by_role_id($id_role);        

        foreach ($functions as $function) {

            if ($function == 'ases_report') {
                $url = new moodle_url("/blocks/ases/view/ases_report.php", array(
                    'courseid' => $courseid,
                    'instanceid' => $blockid,
                ));

                $menu_options = '<a id="menu_ases_report" href= "' . $url . '"> Reporte general </a>';
                $soc_ed_options['Reporte general'] = $menu_options;
            }

            if ($function == 'create_action') {
                $url = new moodle_url("/blocks/ases/view/create_action.php", array(
                    'courseid' => $courseid,
                    'instanceid' => $blockid,
                ));

                $menu_options = '<a id="menu_create_action" href= "' . $url . '"> Gestión de permisos </a>';
                $admin_options['Gestión de permisos'] = $menu_options;

            }

            if ($function == 'grade_categories') {
                $url = new moodle_url("/blocks/ases/view/grade_categories.php", array(
                    'courseid' => $courseid,
                    'instanceid' => $blockid,
                ));

                $menu_options = '<a id="menu_grade_categories" href= "' . $url . '"> Registro de notas </a>';
                $academic_options['Registro de notas'] = $menu_options;

            }

            if ($function == 'upload_historical_files') {
                $url = new moodle_url("/blocks/ases/view/upload_historical_files.php", array(
                    'courseid' => $courseid,
                    'instanceid' => $blockid,
                ));

                $menu_options = '<a id="menu_upload_historical_files" href= "' . $url . '"> Carga de históricos </a>';
                $admin_options['Carga de históricos'] = $menu_options;

            }


            if ($function == 'instance_configuration') {
                $url = new moodle_url("/blocks/ases/view/instance_configuration.php", array(
                    'courseid' => $courseid,
                    'instanceid' => $blockid,
                ));

                $menu_options = '<a id="menu_instance_configuration" href= "' . $url . '"> Gestión de instancia </a>';
                $admin_options['Gestión de instancia'] = $menu_options;

            }

            if ($function == 'massive_upload') {
                $url = new moodle_url("/blocks/ases/view/massive_upload.php", array(
                    'courseid' => $courseid,
                    'instanceid' => $blockid,
                ));

                $menu_options = '<a id="menu_massive_upload" href= "' . $url . '"> Carga masiva datatables </a>';
                $admin_options['Carga masiva datatables'] = $menu_options;

            }
            if ($function == 'mass_role_management') {
                $url = new moodle_url("/blocks/ases/view/mass_role_management.php", array(
                    'courseid' => $courseid,
                    'instanceid' => $blockid,
                ));

                $menu_options = '<a id="menu_mass_role_management" href= "' . $url . '"> Carga masiva </a>';
                $admin_options['Carga masiva'] = $menu_options;

            }

            if ($function == 'periods_management') {
                $url = new moodle_url("/blocks/ases/view/periods_management.php", array(
                    'courseid' => $courseid,
                    'instanceid' => $blockid,
                ));

                $menu_options = '<a id="menu_periods_management" href= "' . $url . '"> Gestión de períodos </a>';
                $admin_options["Gestión de períodos"] = $menu_options;

            }

            if ($function == 'groupal_tracking') {
                $url = new moodle_url("/blocks/ases/view/groupal_tracking.php", array(
                    'courseid' => $courseid,
                    'instanceid' => $blockid,
                ));

                $menu_options = '<a id="menu_groupal_tracking" href= "' . $url . '"> Seguimiento grupal </a>';
                $soc_ed_options["Seguimiento grupal"] = $menu_options;

            }

            if ($function == 'report_trackings') {
                $url = new moodle_url("/blocks/ases/view/report_trackings.php", array(
                    'courseid' => $courseid,
                    'instanceid' => $blockid,
                ));
                
                $menu_options = '<a id="menu_report_trackings" href= "' . $url . '"> Reportes de seguimientos </a>';
                $soc_ed_options["Reportes de seguimientos"] = $menu_options;

            }

            if ($function == 'user_management') {
                $url = new moodle_url("/blocks/ases/view/user_management.php", array(
                    'courseid' => $courseid,
                    'instanceid' => $blockid,
                ));

                $menu_options = '<a id="menu_user_management" href= "' . $url . '"> Gestión de usuarios </a>';
                $admin_options['Gestión de usuarios'] = $menu_options;

            }

            if ($function == 'student_profile') {
                $url = new moodle_url("/blocks/ases/view/student_profile.php", array(
                    'courseid' => $courseid,
                    'instanceid' => $blockid,
                ));

                $menu_options = '<a id="menu_student_profile" href= "' . $url . '"> Ficha de estudiantes </a>';
                $soc_ed_options['Ficha de estudiantes'] = $menu_options;

            }

            if ($function == 'upload_files_form') {
                $url = new moodle_url("/blocks/ases/view/upload_files_form.php", array(
                    'courseid' => $courseid,
                    'instanceid' => $blockid,
                ));

                $menu_options = '<a id="menu_upload_files_form" href= "' . $url . '"> Carga de archivos </a>';
                $admin_options['Carga de archivos'] = $menu_options;

            }

            if ($function == 'historical_icetex_reports') {
                $url = new moodle_url("/blocks/ases/view/historical_icetex_reports.php", array(
                    'courseid' => $courseid,
                    'instanceid' => $blockid,
                ));

                $menu_options = '<a id="menu_historical_icetex_reports" href= "' . $url . '"> Reportes ICETEX </a>';
                $icetex_options['Reportes ICETEX'] = $menu_options;

            }

            if ($function == 'teachers_reports') {
                $url = new moodle_url("/blocks/ases/view/course_and_teacher_report.php", array(
                    'courseid' => $courseid,
                    'instanceid' => $blockid,
                ));

                $menu_options = '<a id="menu_teachers_reports" href= "' . $url . '"> Reportes por docente </a>';
                $academic_options['Reportes por docente'] = $menu_options;

            }
            if ($function == 'student_item_grades_report') {
                $url = new moodle_url("/blocks/ases/view/student_item_grades_report.php", array(
                    'courseid' => $courseid,
                    'instanceid' => $blockid,
                ));

                $menu_options = '<a id="menu_student_item_grades_report" href= "' . $url . '"> Reportes notas por items </a>';
                $academic_options['Reportes notas por items'] = $menu_options;

            }
            if ($function == 'report_active_semesters') {
                $url = new moodle_url("/blocks/ases/view/report_active_semesters.php", array(
                    'courseid' => $courseid,
                    'instanceid' => $blockid,
                ));

                $menu_options = '<a id="menu_report_active_semesters" href= "' . $url . '"> Reporte deserción </a>';
                $soc_ed_options['Reporte deserción'] = $menu_options;

            }
            if ($function == 'historic_academic_reports') {
                $url = new moodle_url("/blocks/ases/view/historic_academic_reports.php", array(
                    'courseid' => $courseid,
                    'instanceid' => $blockid,
                ));

                $menu_options = '<a id="menu_historic_academic_reports" href= "' . $url . '"> Reportes históricos académicos </a>';
                $academic_options['Reportes históricos académicos'] = $menu_options;

            }

            if ($function == 'dphpforms_form_editor') {
                $url = new moodle_url("/blocks/ases/view/dphpforms_form_editor.php", array(
                    'courseid' => $courseid,
                    'instanceid' => $blockid,
                ));

                $menu_options = '<a id="menu_dphpforms_form_editor" href= "' . $url . '"> Administrador de formularios </a>';
                $admin_options['Administrador de formularios'] = $menu_options;

            }

            if ($function == 'not_assigned_students') {
                $url = new moodle_url("/blocks/ases/view/not_assigned_students.php", array(
                    'courseid' => $courseid,
                    'instanceid' => $blockid,
                ));

                $menu_options = '<a id="menu_not_assigned_students" href= "' . $url . '"> Estudiantes sin asignar </a>';
                $academic_options['Estudiantes sin asignar'] = $menu_options;

            }

            if ($function == 'dphpforms_reports') {
                $url = new moodle_url("/blocks/ases/view/dphpforms_reports.php", array(
                    'courseid' => $courseid,
                    'instanceid' => $blockid,
                ));

                $menu_options = '<a id="menu_dphpforms_reports" href= "' . $url . '"> Reporte de formularios </a>';
                $soc_ed_options['Reporte de formularios'] = $menu_options;

            }

            if ($function == 'monitor_assignments') {
                $url = new moodle_url("/blocks/ases/view/monitor_assignments.php", array(
                    'courseid' => $courseid,
                    'instanceid' => $blockid,
                ));

                $menu_options = '<a id="menu_monitor_assignments" href= "' . $url . '"> Gestión de asignaciones </a>';
                $soc_ed_options['Gestión de asignaciones'] = $menu_options;

            }

            if ($function == 'assigned_students_no_trackings_report') {
                $url = new moodle_url("/blocks/ases/view/assigned_students_no_trackings_report.php", array(
                    'courseid' => $courseid,
                    'instanceid' => $blockid,
                ));

                $menu_options = '<a id="menu_assigned_students_no_trackings_report" href= "' . $url . '"> Estudiantes sin seguimientos </a>';
                $soc_ed_options['Estudiantes sin seguimientos '] = $menu_options;

            }


            if ($function == 'backup_forms') {
                $url = new moodle_url("/blocks/ases/view/backup_forms.php", array(
                    'courseid' => $courseid,
                    'instanceid' => $blockid,
                ));

                $menu_options = '<a id="menu_backup_forms" href= "' . $url . '">Reporte backup </a>';                
                $admin_options['Reporte backup'] = $menu_options;
            }

            if ($function == 'discapacity_reports') {
                $url = new moodle_url("/blocks/ases/view/discapacity_reports.php", array(
                    'courseid' => $courseid,
                    'instanceid' => $blockid,
                ));

                $menu_options = '<a id="menu_discapacity_reports" href= "' . $url . '">Reporte discapacidad e inclusión<span class="badge badge-secondary">New</span> </a>';
                $discapacity_options['Reporte discapacidad e inclusión'] = $menu_options;

            }

            if ($function == 'incidents_manager') {
                $url = new moodle_url("/blocks/ases/view/ases_incidents.php", array(
                    'courseid' => $courseid,
                    'instanceid' => $blockid,
                ));

                $menu_options = '<a id="menu_incidents_manager" class="menu_a" href= "' . $url . '">Gestión de incidencias</a>';
                $admin_options['Gestión de incidencias'] = $menu_options;

            }

            if ($function == 'men_report') {
                $url = new moodle_url("/blocks/ases/view/men_report.php", array(
                    'courseid' => $courseid,
                    'instanceid' => $blockid,
                ));

                $menu_options = '<a id="menu_men_report" class="menu_a" href= "' . $url . '">Reporte MEN</a>';
                $icetex_options['Reporte MEN'] = $menu_options;

            }

        }

        //ORDENA
        if (sizeof($admin_options) > 0){
            ksort($admin_options);
            foreach ($admin_options as $value) {
                $admin_dropdown .= $value;
            }
            $admin_dropdown .= $dropdown_close_tags;
            $menu_return .= $admin_dropdown;
        }
        
        if (sizeof($soc_ed_options) > 0) {
            ksort($soc_ed_options);
            foreach ($soc_ed_options as $value) {
                $soc_ed_dropdown .= $value;
            }
            $soc_ed_dropdown .= $dropdown_close_tags;
            $menu_return .= $soc_ed_dropdown;
        }

        if (sizeof($academic_options) > 0) {
            ksort($academic_options);
            foreach ($academic_options as $value) {
                $academic_dropdown .= $value;
            }
            $academic_dropdown .= $dropdown_close_tags;
            $menu_return .= $academic_dropdown;
        }

        if (sizeof($icetex_options) > 0) {
            ksort($icetex_options);
            foreach ($icetex_options as $value) {
                $icetex_men_dropdown .= $value;
            }
            $icetex_men_dropdown .= $dropdown_close_tags;
            $menu_return .= $icetex_men_dropdown;
        }

        if (sizeof($discapacity_options) > 0) {
            ksort($discapacity_options);
            foreach ($discapacity_options as $value) {
                $discapacity_dropdown .= $value;
            }
            $discapacity_dropdown .= $dropdown_close_tags;
            $menu_return .= $discapacity_dropdown;
        }
    }

    return $menu_return;

}