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
 * @author     Iader E. García G.
 * @author     Jeison Cardona Gómez
 * @author     Juan Pablo Castro
 * @author     Carlos M. Tovar Parra
 * @package    block_ases
 * @copyright  2016 Iader E. García <iadergg@gmail.com>
 * @copyright  2019 Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @copyright  2019 Juan Pablo Castro <juan.castro.vasquez@correounivalle.edu.co>
 * @copyright  2021 Carlos M. Tovar Parra <carlos.mauricio.tovar@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
require_once __DIR__ . '/../../../config.php';
require_once $CFG->libdir . '/adminlib.php';
require_once("$CFG->libdir/formslib.php");

require_once '../managers/lib/student_lib.php';
require_once '../managers/lib/lib.php';
require_once '../managers/user_management/user_lib.php';
require_once '../managers/student_profile/geographic_lib.php';
require_once '../managers/student_profile/studentprofile_lib.php';
require_once '../managers/student_profile/academic_lib.php';
require_once '../managers/student_profile/student_graphic_dimension_risk.php';
require_once '../managers/instance_management/instance_lib.php';
require_once '../managers/dateValidator.php';
require_once '../managers/permissions_management/permissions_lib.php';
require_once '../managers/validate_profile_action.php';
require_once '../managers/menu_options.php';
require_once '../managers/dphpforms/dphpforms_forms_core.php';
require_once '../managers/dphpforms/dphpforms_records_finder.php';
require_once '../managers/dphpforms/dphpforms_get_record.php';
require_once '../managers/user_management/user_management_lib.php';
require_once '../managers/monitor_assignments/monitor_assignments_lib.php';
//require_once '../managers/periods_management/periods_lib.php';
require_once '../classes/AsesUser.php';
require_once '../classes/mdl_forms/user_image_form.php';
require_once '../core/module_loader.php';
include '../lib.php';

module_loader('periods');

global $PAGE;
global $USER;

include "../classes/output/student_profile_page.php";
include "../classes/output/renderer.php";


$new_forms_date =strtotime('2018-01-01 00:00:00');

//$initial_date = date('H:i:s.u');
// Set up the page.
$title = "Ficha estudiante";
$pagetitle = $title;
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);
$student_code = optional_param('student_code', '0', PARAM_TEXT);

require_login($courseid, false);

// Set up the page.
if (!consult_instance($blockid)) {
    header("Location: instanceconfiguration.php?courseid=$courseid&instanceid=$blockid");
}

$contextcourse = context_course::instance($courseid);
$contextblock = context_block::instance($blockid);

$url = new moodle_url("/blocks/ases/view/student_profile.php", array('courseid' => $courseid, 'instanceid' => $blockid, 'student_code' => $student_code));

// Nav configuration
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create('Reporte general', new moodle_url("/blocks/ases/view/ases_report.php", array('courseid' => $courseid, 'instanceid' => $blockid)), null, 'block', $blockid);
$coursenode->add_node($blocknode);
$node = $blocknode->add('Ficha estudiante', new moodle_url("/blocks/ases/view/student_profile.php", array('courseid' => $courseid, 'instanceid' => $blockid, 'student_code' => $student_code)));
$blocknode->make_active();
$node->make_active();

// Load information of student's file
// Initialize context variable
$record = new stdClass;
$actions = authenticate_user_view($USER->id, $blockid);
$record = $actions;

// Security system, blocks defined on mustache files won't show if there is no call to core_secure_render
// @see core_secure_render on core/security/security.php
//core_secure_render($record, $USER->id);

$data_init = array();

$rol = lib_get_rol_name_ases($USER->id, $blockid);
$html_profile_image = "";
$id_user_moodle_ = null;
$ases_student = null;
if ($student_code != '0') {

    $ases_student = get_ases_user_by_code($student_code);
    $student_id = $ases_student->id;

    $matches = array();
    $birthdate = $ases_student->fecha_nac;
    preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])/", $birthdate, $matches);
    $record->birthdate = isset($matches[0])?$matches[0]:'1920-01-01';

    // Student information to display on file header (ficha)
    $id_user_moodle = get_id_user_moodle($ases_student->id);
    $id_user_moodle_ = $id_user_moodle;

    $user_moodle = get_moodle_user($id_user_moodle);

    $html_profile_image = AsesUser::get_HTML_img_profile_image($contextblock->id, $ases_student->id);
    //$academic_programs = get_status_program_for_profile($student_id);
    $academic_programs = get_status_program_for_profile_aditional($student_id);
    $student_cohorts = get_cohorts_by_student($id_user_moodle);
    $status_ases_array = get_ases_status($ases_student->id, $blockid);

    $document_type = get_document_types_for_profile($ases_student->id);

    //Get economics data of student
    if(get_exist_economics_data($ases_student->id)){
        $record->economics_data      = "1";
        $record->economics_data_json = json_encode(get_economics_data($ases_student->id));
    }else{
        $record->economics_data = "0";
    }

    //Get health data of student
    if(get_exist_health_data($ases_student->id)){
        $record->health_data      = "1";
        $record->health_data_json = json_encode(get_health_data($ases_student->id));
    }else{
        $record->health_data = "0";
    }

    //Get academics data of student
    $body_table_others_institutions = '';
    if(get_exist_academics_data($ases_student->id)){
        $record->academics_data      = "1";
        $record->academics_data_json = json_encode(get_academics_data($ases_student->id));
    
        //Get aditional academics existing data

        $aditional_academics_data = get_academics_data($ases_student->id);

        //Extraer json y decodificar datos de otras instituciones del estudiante
        $objeto_json_institutions = json_decode($aditional_academics_data->otras_instituciones);

        //Recorrer el objeto json (array) y contruir los tr y td de la tabla
        foreach($objeto_json_institutions as $objeto){

            $body_table_others_institutions .= "<tr><td> <input name='name_institucion' class='input_fields_general_tab'  type='text' value='$objeto->name_institution'/></td>
        <td> <input name='nivel_academico_institucion' class='input_fields_general_tab'  type='text' value='$objeto->academic_level'/></td>
        <td> <input name='apoyos_institucion' class='input_fields_general_tab'  type='text' value='$objeto->supports'/></td>
        <td> <button type ='button' id='bt_delete_institucion' title='Eliminar institución' name='btn_delete_institucion' style='visibility:visible;'> </button></td> </tr>";

        }

        $record->current_resolution         =$aditional_academics_data->resolucion_programa;
        $record->total_time                 =$aditional_academics_data->creditos_totales;
        $record->previous_academic_title    =$aditional_academics_data->titulo_academico_colegio;
        $record->info_others_institutions   =$body_table_others_institutions;
        $record->academics_observations     =$aditional_academics_data->observaciones;
        $record->academics_dificults        =$aditional_academics_data->dificultades;
        $record->academics_data_json        =json_encode($aditional_academics_data);

    }else{
        $record->academics_data = "0";
        $record->current_resolution         ='No encontrado';
        $record->total_time                 ='No encontrado';
        $record->previous_academic_title    ='No encontrado';
        $record->info_others_institutions   ='No encontrado';
        $record->academics_observations     ='No encontrado';
        $record->academics_dificults        ='No encontrado';
        $record->academics_data_json        ='No encontrado';
    }

    //Faculty name foreach academic program


    $record->id_moodle = $id_user_moodle;
    $record->id_ases = $student_id;
    $record->email_moodle = $user_moodle->email_moodle;
    $record->age = substr($ases_student->age, 0, 2);
    
    $num_doc = $ases_student->num_doc;
    $student_codes = get_student_codes($num_doc);
    foreach($academic_programs as $program) {
        $cod_programa = $program->cod_univalle;
        foreach($student_codes as $codes){
            $moodle_username = $codes->code;
            $student_program = substr($moodle_username,-4);
            if($cod_programa == $student_program){
                $cod_programa = $moodle_username;
                break;
            }      
        }

        if($program->tracking_status == 1) {
            $sede = $program->nombre_sede;
            $nombre_programa = $program->nombre_programa;
            $program->nombre_sede = "<b>".$sede."</b>";
            $program->cod_univalle = "<b>".$cod_programa."</b>";
            $program->nombre_programa = "<b>".$nombre_programa."</b>";
            $record->cod_programa_activo = $cod_programa;
        }else{
            $program->cod_univalle = $cod_programa;
        }
    }

    $record->estamento = $ases_student->estamento;
    $record->colegio = $ases_student->colegio;
    $record->name_current_semester = core_periods_get_current_period($blockid)->nombre;
    $record->academic_programs = $academic_programs;
    $record->student_cohorts = $student_cohorts;

    //Traer el nombre de la condición de excepcion del estudiante
    $record->condition_excepcion_alias = "NO REGISTRA";
    $record->condition_excepcion = "NO REGISTRA CONDICIÓN DE EXCEPCIÓN EN BASE DE DATOS";
    $param = $ases_student->id_cond_excepcion;
    foreach($student_cohorts as $cohort){
        if(substr($cohort->name, 0, 24) == "Condición de Excepción"  && $param != null ){
            $condition = get_cond($param);
            $record->condition_excepcion_alias = $condition->alias;
            $record->condition_excepcion = $condition->condicion_excepcion;
        }
    }

    $record->document_type = $document_type;

    array_push($data_init, $academic_programs);



    // General file (ficha general) information
    $record->puntaje_icfes = $ases_student->puntaje_icfes;
    $record->ingreso = $ases_student->anio_ingreso;
    $record->estrato = $ases_student->estrato;

    $record->init_tel = $ases_student->tel_ini;
    $record->res_tel = $ases_student->tel_res;
    $record->cell_phone = $ases_student->celular;
    $record->emailpilos = $ases_student->emailpilos;
    $record->attendant = $ases_student->acudiente;
    $record->attendant_tel = $ases_student->tel_acudiente;
    $record->num_doc = $num_doc;
    $record->json_detalle_discapacity  =$ases_student->json_detalle;



    $personas = '';
    $pos = 1;

    //Extraer json y decodificar datos de personas con quien vive
    $objeto_json = json_decode($ases_student->vive_con);
    //Recorrer el objeto json (array) y contruir los tr y td de la tabla
    foreach($objeto_json as $objeto){
        $personas  .= "<tr> <td>  <input   name = 'name_person'class= 'input_fields_general_tab' readonly type='text' value='$objeto->name' /></td>
       <td><input name = 'parentesco_person'  class= 'input_fields_general_tab' readonly type='text' value='$objeto->parentesco' /></td> <td>
       <button type = 'button' class='bt_delete_person' title='Eliminar persona' name  = 'btn_delete_person' style= 'visibility:hidden;' value='$pos'></button></td></tr>";
        $pos ++;
    }


    $record->personas_con_quien_vive = $personas;
    //TRAE ESTADOS CIVILES
    $estados= get_estados_civiles();
    $options_estado_civil = '';

    $estado_student->id_estado_civil = $ases_student->id_estado_civil;
    //Buscar la posición del estado civil soltero, colocar al inicio del select

    $i = 0;
    foreach($estados as $estado){
        if($estado->estado_civil=='Soltero(a)'){
            $options_estado_civil .= "<option value='$estado->id'>$estado->estado_civil</option>";
            $pose = $i;
            break;
        }
        $i++;
    }
    //Eliminar estado civil agregado al inicio del select del array
    array_splice($estados,$pose,1);


    foreach($estados as $estado){
        if($estado_student->id_estado_civil == $estado->id){
            $options_estado_civil .= "<option value='$estado->id' selected='selected'>$estado->estado_civil</option>";
        }else{
            $options_estado_civil .= "<option value='$estado->id'>$estado->estado_civil</option>";
        }
    }

    $record->options_estado_civil = $options_estado_civil;



    //TRAE OCUPACIONES
    $ocupaciones= get_ocupaciones();
    $options_ocupaciones= '';

    $i=0;

    $options_ocupaciones .= "<option selected= 'selected' value='option_ninguna' title='NINGUNA DE LAS ANTERIORES'>NINGUNA DE LAS ANTERIORES</option>";

    foreach($ocupaciones as $ocupacion){
        $options_ocupaciones .= "<option value='$ocupacion->value' title='$ocupacion->ocupacion'>$ocupacion->alias...</option>";
    }

    $record->ocupaciones = $options_ocupaciones;

    //TRAE PAISES
    $paises= get_paises();
    $options_pais = '';

    $pais_student->id_pais = $ases_student->id_pais;
    //Buscar la posición del pais Colombia
    $i=0;
    foreach($paises as $pais){
        if($pais->pais=="Colombia"){
            $posp=$i;
            $options_pais .= "<optgroup label='Populares'> <option value='$pais->id'>$pais->pais</option> </optgroup>" ;
            break; }
        $i++;
    }

    $coordenates = student_profile_get_coordenates($student_id);
    $record->latitude = $coordenates->latitude;
    $record->longitude = $coordenates->longitude;

    //Eliminar país Colombia puesto al inicio
    array_splice($paises,$posp,1);

    $options_pais .= "<optgroup label = 'Otros'>";
    foreach($paises as $pais){
        if($pais_student->id_pais == $pais->id){
            $options_pais .= "<option value='$pais->id' selected='selected'>$pais->pais</option>";
        }else{
            $options_pais .= "<option value='$pais->id'>$pais->pais</option>";
        }
    }
    $options_pais .= "</optgroup>";

    $record->options_pais = $options_pais;

    $record->municipio_act = student_profile_get_ciudad_res($student_id);
    $record->res_address = student_profile_get_res_address($student_id);
    $record->neighborhood = student_profile_get_neighborhood($student_id);
/*
    //TRAE LAS CONDICIONES DE EXCEPCIÒN
    
    $cond_excep= get_cond_excepcion();
    $options_excep = '';
    foreach($cond_excep as $cond){
        $options_excep .=" <option value='$cond->id'>$cond->condicion_excepcion</option>"  ;
    }  

    $record->options_excep = $options_excep;
    
    //TRAE LOS OTROS ACOMPAÑAMIENTOS
    $acompañamientos = get_otros_acompañamientos();
    $options_acompañamientos = '';
    foreach($acompañamientos as $ac){
        $options_acompañamientos .= "<option value='$ac->id'>$ac->acompanamiento</option>" ;
    }

    $record->options_acompañamientos = $options_acompañamientos;
   */ 

    //TRAE ETNIAS
    $etnias= get_etnias();
    $options_etnia = '';

    $etnia_student = $ases_student->id_etnia;

    //Buscar la posición de NO DEFINIDO
    $i=0;
    foreach($etnias as $etnia){
        if($etnia->etnia=="NO DEFINIDO"){
            $posp=$i;
            $options_etnia .= " <option value='$etnia->id'>$etnia->etnia</option>" ;
            break; }
        $i++;
    }

    //Eliminar NO DEFINIDO puesto al inicio
    array_splice($etnias,$posp,1);

    $otro ="";
    $control = true;
    foreach($etnias as $etnia){
        if($etnia_student == $etnia->id){
            if($etnia->opcion_general == 1){
                $options_etnia .= "<option value='$etnia->id' selected='selected'>$etnia->etnia</option>";
                $control = false;}


        }else{
            if($etnia->opcion_general == 1){
                $options_etnia .= "<option value='$etnia->id'>$etnia->etnia</option>";}

        }
    }

    $record->options_etnia = $options_etnia;

    //TRAE GENEROS
    $generos= get_generos();
    $options_generos = '';

    $genero_student->id_identidad_gen = $ases_student->id_identidad_gen;

    //Buscar la posición de la actividad Ninguna
    $i=0;
    foreach($generos as $genero){
        if($genero->genero=="NO DEFINIDO"){
            $posa=$i;
            $options_generos .= "<option value='$genero->id'>$genero->genero</option>" ;
            break; }
        $i++;
    }

    //Eliminar genero 'NO DEFINIDO' puesto al inicio
    array_splice($generos,$posa,1);

    $otro ="";
    $control = true;

    //$options_generos .= "<option selected='selected' disabled='disabled'>NO DEFINIDO</option>";
    foreach($generos as $genero){
        if($genero_student->id_identidad_gen == $genero->id){
            if($genero->opcion_general == 1){
                $options_generos .= "<option value='$genero->id' selected='selected'>$genero->genero</option>";}
            else {
                //Seleccionar otro y mostrar en textfield cual
                $otro= $genero->genero;
                $options_generos .= "<option selected='selected' value='0'>Otro</option>";
                $control = false;
            }
        }else{
            if($genero->opcion_general == 1){
                $options_generos .= "<option value='$genero->id'>$genero->genero</option>";
            }
        }
    }

    if($control){$options_generos .= "<option value='0'>Otro</option>"; }

    $record->options_genero = $options_generos;
    $record->otro = $otro;

    //TRAE OPCIONES DE SEXO

    $options_sex= get_sex_options();
    $sex_options = '';

    $option_sex_student = $ases_student->sexo;
    //Buscar la posición del sexo NO REGISTRA
    $i=0;
    foreach($options_sex as $option){
        if($option->sexo=="NO REGISTRA"){
            $posa=$i;
            $sex_options .= "<option value='$option->id'>$option->sexo</option>" ;
            break; }
        $i++;
    }

    //Eliminar sexo NO REGISTRA puesta al inicio
    array_splice($options_sex,$posa,1);

    foreach($options_sex as $option){
        if($option_sex_student == $option->id){

            $sex_options .= "<option value='$option->id' selected='selected'>$option->sexo</option>";

        }else{

            $sex_options .= "<option value='$option->id'>$option->sexo</option>";

        }
    }

    $record->sex_options = $sex_options;

    //TRAE ACTIVIDADES SIMULTANEAS
    $act_simultaneas= get_act_simultaneas();
    $options_act_simultaneas = '';

    $act_simultanea_student->id_act_simultanea= $ases_student->id_act_simultanea;
    //Buscar la posición de la actividad Ninguna
    $i=0;
    foreach($act_simultaneas as $act){
        if($act->actividad=="Ninguna"){
            $posa=$i;
            $options_act_simultaneas .= "<option value='$act->id'>$act->actividad</option>" ;
            break; }
        $i++;
    }

    //Eliminar actividad Ninguna puesta al inicio
    array_splice($act_simultaneas,$posa,1);
    $control = true;
    $otro="";
    foreach($act_simultaneas as $act){
        if($act_simultanea_student->id_act_simultanea == $act->id){
            if($act->opcion_general == 1){
                $options_act_simultaneas .= "<option value='$act->id' selected='selected'>$act->actividad</option>";}
            else {
                //Seleccionar otro y mostrar en textfield cual
                $otro = $act->actividad;
                $options_act_simultaneas .= "<option selected='selected' value='0'>Otro</option>";
                $control = false;
            }
        }else{
            if($act->opcion_general == 1){
                $options_act_simultaneas .= "<option value='$act->id'>$act->actividad</option>";
            }
        }
    }


    if($control){$options_act_simultaneas .= "<option value='0'>Otro</option>"; }

    $record->options_act_simultanea = $options_act_simultaneas;
    $record->otro_act = $otro;

    //Código temporal vive_con
    if($ases_student->vive_con == null){
        $record->vive_con = "NO DEFINIDO";
    }else{
        $record->vive_con = $ases_student->vive_con;}


    //Código temporal hijos

    if($ases_student->hijos == null){
        $record->sons = 0;
    }else{
        $record->sons = $ases_student->hijos;
    }

    // traer enlace a documento de autorización para el tratamiento de datos personales
    $record->tratamiento_datos_personales_doc = json_decode($ases_student->json_detalle)->tratamiento_datos_personales_doc;


    $reasons_dropout_observations = getReasonDropoutStudent ($ases_student->id);
    $record->observations = $reasons_dropout_observations."\n".$ases_student->observacion;

    // Estado ASES

    $id_current_semester = core_periods_get_current_period($blockid)->id;
    $last_monitor_assignment = monitor_assignments_get_last_monitor_student_assignment($student_id, $blockid);
    $id_instance_last_assignment = $last_monitor_assignment->id_instancia;
    $id_semester_last_assignment = $last_monitor_assignment->id_semestre;

    $record->ases_status = "sinseguimiento";
    $record->ases_status_description = "No se realiza seguimiento en esta instancia";

    if($id_current_semester == $id_semester_last_assignment) {
        if($id_instance_last_assignment == $blockid) {
            $record->ases_status = "seguimiento";
            $record->ases_status_on_tracking = 'true';
            $record->ases_status_description = "Se realiza seguimiento en esta instancia";
        }
    }

    /*@deprecated
    if($status_ases_array){
        if($status_ases_array[$blockid]->nombre == "seguimiento"){
            $record->ases_status_t = "seguimiento";
            $record->ases_status_description = "Se realiza seguimiento en esta instancia";
        }else if($status_ases_array[$blockid]->nombre == "sinseguimiento"){

            $has_ases_status = verify_ases_status($ases_student->id);

            if($has_ases_status){
                $record->ases_status_f = "sinseguimiento";
                $record->ases_status_description = "Se realiza seguimiento en otra instancia";
            }else{
                $record->ases_status_n = "noasignado";
                $record->ases_status_description = "No se realiza seguimiento";
            }
        }else{
            $record->ases_status_n = "noasignado";
            $record->ases_status_description = "No se realiza seguimiento";
        }
    }else{
        $record->ases_status_n = "noasignado";
        $record->ases_status_description = "No se realiza seguimiento";
    }*/

    // Estado ICETEX
    $icetex_statuses = get_icetex_statuses();
    $options_status_icetex = '';

    $status_icetex_student = get_icetex_status_student($student_id);

    foreach($icetex_statuses as $status){
        if($status_icetex_student->id_estado_icetex == $status->id){
            $options_status_icetex .= "<option value='$status->id' selected='selected'>$status->nombre</option>";
        }else{
            $options_status_icetex .= "<option value='$status->id'>$status->nombre</option>";
        }
    }

    $record->options_status_icetex = $options_status_icetex;
    $record->icetex_status_description = $icetex_statuses[$status_icetex_student->id_estado_icetex]->descripcion;
    $record->icetex_status_name = substr($icetex_statuses[$status_icetex_student->id_estado_icetex]->nombre, 3);

    $monitor_object = new stdClass();
    $trainee_object = new stdClass();
    $professional_object = new stdClass();


    $record->id_dphpforms_creado_por = $USER->id;

    $monitor_object = get_assigned_monitor($student_id, $blockid);
    $trainee_object = get_assigned_pract($student_id, $blockid);
    $professional_object = get_assigned_professional($student_id, $blockid);

    $flag_with_assignation = false;

    if ($monitor_object) {
        $flag_with_assignation = true;
        $record->monitor_fullname = "$monitor_object->firstname $monitor_object->lastname";
        $record->id_dphpforms_monitor = '-1';
    } else {
        $record->monitor_fullname = "NO REGISTRA";
    }

    if ($trainee_object) {
        $record->trainee_fullname = "$trainee_object->firstname $trainee_object->lastname";
    } else {
        $record->trainee_fullname = "NO REGISTRA";
    }

    if ($professional_object) {
        $record->professional_fullname = "$professional_object->firstname $professional_object->lastname";
    } else {
        $record->professional_fullname = "NO REGISTRA";
    }

    // Geographic information

    $geographic_object = student_profile_load_geographic_info($student_id);

    $geographic_object = get_geographic_info($student_id);

    $geographic_risk_level = $geographic_object->risk_level;

    $record->geographic_risk_level  = $geographic_risk_level;

    switch ($geographic_risk_level) {
        case 1:
            $record->low_level = true;
            $record->mid_level = $record->high_level = false;
            $record->geographic_class = 'div_low_risk';
            break;
        case 2:
            $record->mid_level = true;
            $record->low_level = $record->high_level = false;
            $record->geographic_class = 'div_medium_risk';
            break;
        case 3:
            $record->high_level = true;
            $record->low_level = $record->mid_level = false;
            $record->geographic_class = 'div_high_risk';
            break;
        default:
            $record->low_level = $record->mid_level = $record->high_level = false;
            $record->geographic_class = 'div_no_risk';
            break;
    }

    // Students risks

    $risk_object = get_risk_by_student($student_id);

    $record->individual_risk = $risk_object['individual']->calificacion_riesgo;
    $record->familiar_risk = $risk_object['familiar']->calificacion_riesgo;
    $record->academic_risk = $risk_object['academico']->calificacion_riesgo;
    $record->life_risk = $risk_object['vida_universitaria']->calificacion_riesgo;
    $record->economic_risk = $risk_object['economico']->calificacion_riesgo;

    switch ($risk_object['individual']->calificacion_riesgo) {
        case 1:
            $record->individual_class = 'div_low_risk';
            $record->level_individual = 1;
            break;
        case 2:
            $record->individual_class = 'div_medium_risk';
            $record->level_individual = 2;
            break;
        case 3:
            $record->individual_class = 'div_high_risk';
            $record->level_individual = 3;
            break;
        default:
            $record->individual_class = 'div_no_risk';
            $record->level_individual = 0;
            break;
    }

    switch ($risk_object['familiar']->calificacion_riesgo) {
        case 1:
            $record->familiar_class = 'div_low_risk';
            $record->level_familiar = 1;
            break;
        case 2:
            $record->familiar_class = 'div_medium_risk';
            $record->level_familiar = 2;
            break;
        case 3:
            $record->familiar_class = 'div_high_risk';
            $record->level_familiar = 3;
            break;
        default:
            $record->familiar_class = 'div_no_risk';
            $record->level_familiar = 0;
            break;
    }

    switch ($risk_object['economico']->calificacion_riesgo) {
        case 1:
            $record->economic_class = 'div_low_risk';
            $record->level_economic = 1;
            break;
        case 2:
            $record->economic_class = 'div_medium_risk';
            $record->level_economic = 2;
            break;
        case 3:
            $record->economic_class = 'div_high_risk';
            $record->level_economic = 3;
            break;
        default:
            $record->economic_class = 'div_no_risk';
            $record->level_economic = 0;
            break;
    }

    switch ($risk_object['vida_universitaria']->calificacion_riesgo) {
        case 1:
            $record->life_class = 'div_low_risk';
            $record->level_life = 1;
            break;
        case 2:
            $record->life_class = 'div_medium_risk';
            $record->level_life = 2;
            break;
        case 3:
            $record->life_class = 'div_high_risk';
            $record->level_life = 3;
            break;
        default:
            $record->life_class = 'div_no_risk';
            $record->level_life = 0;
            break;
    }

    switch ($risk_object['academico']->calificacion_riesgo) {
        case 1:
            $record->academic_class = 'div_low_risk';
            break;
        case 2:
            $record->academic_class = 'div_medium_risk';
            break;
        case 3:
            $record->academic_class = 'div_high_risk';
            break;
        default:
            $record->academic_class = 'div_no_risk';
            break;
    }

    $select = make_select_ficha($USER->id, $rol, $student_code, $blockid, $actions);
    $record->code = $select;

    $dphpforms_ases_user = get_ases_user_by_code( $student_code )->id;

    // Loading desertion reasons or studies postponement

    $reasons_dropout = get_reasons_dropout();

    $html_select_reasons = "<option value='' id='no_reason_option'>Seleccione el motivo</option>";

    foreach ($reasons_dropout as $reason) {
        $html_select_reasons .= "<option value=" . $reason->id . ">";
        $html_select_reasons .= $reason->descripcion;
        $html_select_reasons .= "</option>";
    }

    $record->reasons_options = $html_select_reasons;

    $record->form_seguimientos = null;
    $record->primer_acercamiento = null;
    $record->form_seguimientos = dphpforms_render_recorder('seguimiento_pares', $rol);
    $record->form_inasistencia = dphpforms_render_recorder('inasistencia', $rol);

    if ($record->form_seguimientos == '') {
        $record->form_seguimientos = "<strong><h3>Oops!: No se ha encontrado un formulario con el alias: <code>seguimiento_pares</code>.</h3></strong>";
    }

    $record->primer_acercamiento = dphpforms_render_recorder('primer_acercamiento', $rol);
    if ($record->primer_acercamiento == '') {
        $record->primer_acercamiento = "<strong><h3>Oops!: No se ha encontrado un formulario con el alias: <code>primer_acercamiento</code>.</h3></strong>";
    }

    $record->form_seguimientos_geograficos = dphpforms_render_recorder('seguimiento_geografico', $rol);
    $record->geo_tracking =  dphpforms_render_recorder('seguimiento_geografico', $rol);
    if ($record->form_seguimientos_geograficos == '') {
        $record->form_seguimientos_geograficos = "<strong><h3>Oops!: No se ha encontrado un formulario con el alias: <code>seguimientos_geograficos</code>.</h3></strong>";
    }
    $seguimiento_geografico = json_decode( dphpforms_find_records('seguimiento_geografico', 'seg_geo_id_estudiante', $dphpforms_ases_user, 'DESC') )->results;
    if($seguimiento_geografico){
        $record->actualizar_seguimiento_geografico = true;
        $record->id_seguimiento_geografico = array_values( $seguimiento_geografico )[0]->id_registro;
        //$record->geo_tracking =  dphpforms_render_updater('seguimiento_geografico', $rol, $record->id_seguimiento_geografico);
    }else{
        $record->registro_seguimiento_geografico = true;
    }

    //geo_tracking
    /**
     * {{{fix_mustache_bug}}}{{{geo_tracking}}}
     */

} else {

    $student_id = -1;
    $select = make_select_ficha($USER->id, $rol, null, $blockid, $actions);
    $record->code = $select;

}

if ($rol == 'sistemas') {
    $record->add_peer_tracking_lts = true;
    $record->sistemas = true;
}

if ($rol == 'dir_socioeducativo') {
    $record->dir_socioeducativo = true;
}

if ($rol == 'monitor_ps') {
    $record->monitor_ps = true;
}

if($rol == 'discapacidad' || $rol == 'sistemas'){
    $record->show_discapacity = true;
}else{
    $record->show_discapacity = false;
}
/** Update user image */
$show_html_elements_update_user_profile_image = false;
if (isset($actions->update_user_profile_image)) {
    $show_html_elements_update_user_profile_image = true;
}
$record->show_html_elements_update_user_profile_image = $show_html_elements_update_user_profile_image;


$url_user_edit_image_form_manager        = new moodle_url("/blocks/ases/view/edit_user_image.php", array(
    'courseid' => $courseid,
    'instanceid' => $blockid,
    'ases_user_id' => $ases_student->id,
    'url_return' => $url
));
$_user_image_edit_form = new user_image_form($url_user_edit_image_form_manager,null,'post',null,array('id'=>'update_user_profile_image'));
$_user_image_edit_form->set_data($toform);
$record->update_profile_image_form = $_user_image_edit_form->render(null);
/** End of Update user image  */
$record->ases_student_code = $dphpforms_ases_user;

$moodle_user = user_management_get_moodle_user_with_tracking_status_1( $dphpforms_ases_user );

$record->student_username = $moodle_user->username;
$record->student_fullname = $moodle_user->firstname . " " . $moodle_user->lastname;
$record->instance = $blockid;
$record->html_profile_image = $html_profile_image;

// Url for update user image profile
$url_update_user_image           = new moodle_url("/blocks/ases/view/edit_user_image.php", array(
    'courseid' => $courseid,
    'instanceid' => $blockid,
    'userid' => $id_user_moodle_,
    'url_return' => $url
));
$record->update_profile_image_url = $url_update_user_image;

// periods_lib.php contains get_current_semester()
$record->current_semester = core_periods_get_current_period($blockid)->id;

$stud_mon_prac_prof = user_management_get_stud_mon_prac_prof( $record->ases_student_code, $record->instance, $record->current_semester );
$record->monitor_id = $stud_mon_prac_prof->monitor->id;
$record->practicing_id = $stud_mon_prac_prof->practicing->id;
$record->professional_id = $stud_mon_prac_prof->professional->id;

//Last student assignment

$record->flag_with_assignation = $flag_with_assignation;

if( $dphpforms_ases_user ){
    if( !$flag_with_assignation ){
        $last_assignment = monitor_assignments_get_last_student_assignment( $dphpforms_ases_user, $blockid );
        foreach ($last_assignment as $i => $e) {
            if(is_null($e)){
                $last_assignment[$i]->firstname = 'No se encontraron asignaciones';
                $last_assignment[$i]->lastname = '';
            }
        }
        $record->last_assignment_monitor = $last_assignment['monitor_obj']->firstname . " " . $last_assignment['monitor_obj']->lastname;
        $record->last_assignment_practicant = $last_assignment['pract_obj']->firstname . " " . $last_assignment['pract_obj']->lastname;
        $record->last_assignment_professional = $last_assignment['prof_obj']->firstname . " " . $last_assignment['prof_obj']->lastname;
    }
}


$record->last_date_change_profile = $ases_student->ult_modificacion;

//Menu items are created
$menu_option = create_menu_options($USER->id, $blockid, $courseid);
$record->menu = $menu_option;

$record->fix_mustache_bug = '<form style="display:none;"></form>';
$record->courseid = $courseid;
$record->blockid = $blockid;

$PAGE->set_context($contextcourse);
$PAGE->set_context($contextblock);
$PAGE->set_url($url);
$PAGE->set_title($title);

$PAGE->requires->css('/blocks/ases/style/base_ases.css', true);
$PAGE->requires->css('/blocks/ases/style/jqueryui.css', true);
$PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap.min.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert2.css', true);
$PAGE->requires->css('/blocks/ases/style/sugerenciaspilos.css', true);
$PAGE->requires->css('/blocks/ases/style/forms_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/c3.css', true);
$PAGE->requires->css('/blocks/ases/style/student_profile_risk_graph.css', true);
$PAGE->requires->css('/blocks/ases/js/select2/css/select2.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);
$PAGE->requires->css('/blocks/ases/style/student_profile.css', true);
$PAGE->requires->css('/blocks/ases/style/discapacity_tab.css', true);
$PAGE->requires->css('/blocks/ases/style/others_tab.css', true);
$PAGE->requires->css('/blocks/ases/style/switch.css', true);
$PAGE->requires->css('/blocks/ases/style/fontawesome550.min.css', true);



//Pendiente para cambiar el idioma del nombre del archivo junto con la estructura de
//su nombramiento.
$PAGE->requires->css('/blocks/ases/style/creadorFormulario.css', true);

$PAGE->requires->js_call_amd('block_ases/ases_incident_system', 'init');
$PAGE->requires->js_call_amd('block_ases/student_profile_main', 'init', $data_init);
$PAGE->requires->js_call_amd('block_ases/student_profile_main', 'equalize');
$PAGE->requires->js_call_amd('block_ases/dphpforms_form_renderer', 'init');
$PAGE->requires->js_call_amd('block_ases/dphpforms_form_discapacity', 'init');
$PAGE->requires->js_call_amd('block_ases/students_profile_others_tab_sp', 'init');
$PAGE->requires->js_call_amd('block_ases/academic_profile_main', 'init');

$output = $PAGE->get_renderer('block_ases');

echo $output->header();
$student_profile_page = new \block_ases\output\student_profile_page($record);
echo $output->render($student_profile_page);
echo $output->footer();
