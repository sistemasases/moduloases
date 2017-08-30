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
 * Talentos Pilos
 *
 * @author     Esteban Aguirre Martinez
 * @package    block_ases
 * @copyright  2017 Esteban Aguirre Martinez <estebanaguirre1997@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
// Standard GPL and phpdocs
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('../managers/seguimiento_pilos/seguimiento_functions.php');
require_once('../managers/instance_management/instance_lib.php');


include('../lib.php');
include("../classes/output/renderer.php");

global $PAGE, $USER;

$title = "estudiantes";
$pagetitle = $title;
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);

require_login($courseid, false);

//se culta si la instancia ya está registrada
if(!consult_instance($blockid)){
    header("Location: /blocks/ases/view/instanceconfiguration.php?courseid=$courseid&instanceid=$blockid");
}

$contextcourse = context_course::instance($courseid);
$contextblock =  context_block::instance($blockid);

//Se obtiene el rol del usuario que se encuentra conectado, username y su correo electronico respectivo.

$userrole = get_id_rol($USER->id,$blockid);
$usernamerole= get_name_rol($userrole);
$username = $USER->username;
$email = $USER->email;

$seguimientotable ="";
$globalArregloPares = [];
$globalArregloGrupal =[];
//$table=semesterUser($globalArregloPares,$globalArregloGrupal,$USER->id,$blockid,$usernamerole);
//print_r($semesters);
//Se muestra la interfaz correspondiente al usuario.
if($usernamerole=='monitor_ps'){

	//Se recupera los estudiantes de un monitor en la instancia y se organiza el array que será transformado en el toogle.
	$table=monitorUser($globalArregloPares,$globalArregloGrupal,$USER->id,0,$blockid,$userrole);

}elseif($usernamerole=='practicante_ps'){
    
    //Se recupera los estudiantes de un practicante en la instancia y se organiza el array que será transformado en el toogle.
	$table=practicanteUser($globalArregloPares,$globalArregloGrupal,$USER->id,$blockid,$userrole);

}elseif($usernamerole=='profesional_ps'){
	//Se recupera los estudiantes de un profesional en la instancia y se organiza el array que será transformado en el toogle.

	$table=profesionalUser($globalArregloPares,$globalArregloGrupal,$USER->id,$blockid,$userrole);


}elseif($usernamerole=='sistemas' or $username == "administrador" or $username == "sistemas1008" or $username == "Administrador"){

	//Obtiene los periodos existentes y los roles que contengan "_ps".
    $periods = get_semesters();
    $roles = get_rol_ps();

    //Obtiene las personas que se encuentran en el último semestre añadido y cuyos roles terminen en "_ps.
    $people = get_people_onsemester(reset($periods)->id,$roles,$blockid);


    //organiza el select de periodos.
    $table.='<div class="container"><form class="form-inline">';
    $table.='<div class="form-group"><label for="persona">Periodo</label><select class="form-control" id="periodos">';
    foreach($periods as $period){
   		$table.='<option value="'.$period->id.'">'.$period->nombre.'</option>';
     }
    $table.='</select></div>'; 


    //organiza el select de personas.
    $table.='<div class="form-group"><label for="persona">Persona</label><select class="form-control" id="personas">';
    foreach($people as $person){
    		$table.='<option value="'.$person->id_usuario.'">'.$person->username." - ".$person->firstname." ".$person->lastname.'</option>';
     }
    $table.='</select></div>';
    $table.='<span class="btn btn-info" id="consultar_persona" type="button">Consultar</span></form></div>';
}


$data = 'data';    
$data = new stdClass;
$data->table = $table;
$contextcourse = context_course::instance($courseid);
$contextblock =  context_block::instance($blockid);
$url = new moodle_url("/blocks/ases/view/seguimiento_pilos.php",array('courseid' => $courseid, 'instanceid' => $blockid));

//Navigation setup
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create($title,$url, null, 'block', $blockid);
$coursenode->add_node($blocknode);
$blocknode->make_active();

$PAGE->set_url($url);
$PAGE->set_title($title);

//$PAGE->requires->css('/blocks/ases/style/grade_categories.css', true);
$PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/datepicker.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.min.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/round-about_pilos.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.foundation.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.foundation.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.jqueryui.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.jqueryui.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables_themeroller.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.tableTools.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/NewCSSExport/buttons.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.tableTools.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/js/select2/css/select2.css', true);

//$PAGE->requires->css('/theme/base/style/core.css',true);

$output = $PAGE->get_renderer('block_ases');

echo $output->header();
//echo $output->standard_head_html(); 
$seguimiento_pilos_page = new \block_ases\output\seguimiento_pilos_page($data);
echo $output->render($seguimiento_pilos_page);
echo $output->footer();