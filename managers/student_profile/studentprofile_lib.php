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
 * @author     Iader E. García Gómez
 * @package    block_ases
 * @copyright  2018 Iader E. García <iadergg@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use function core_db\execute;

require_once( dirname(__FILE__). '/../../../../config.php' );
require_once( dirname(__FILE__). '/../../core/module_loader.php' ); 
require_once (__DIR__ .'/../lib/student_lib.php');
require_once (__DIR__ .'/../dphpforms/dphpforms_forms_core.php');
require_once (__DIR__ .'/../dphpforms/v2/dphpforms_lib.php');
require_once (__DIR__ . '/../dphpforms/dphpforms_records_finder.php');
require_once (__DIR__ . '/../dphpforms/dphpforms_get_record.php');
require_once (__DIR__ . '/../periods_management/periods_lib.php');
require_once (__DIR__ . '/../validate_profile_action.php');
require_once (__DIR__ . '/../user_management/user_lib.php');
require_once (__DIR__ . '/../monitor_assignments/monitor_assignments_lib.php');

require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot.'/user/edit_form.php');
require_once($CFG->dirroot.'/user/editlib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot.'/user/lib.php');

require_once('student_graphic_dimension_risk.php');
require_once('academic_lib.php');
require_once('geographic_lib.php');
require_once('others_tab_lib.php');

module_loader("periods");

/**
 * Gets all reasons a student quit or delay studies
 *
 * @see  get_reasons_dropout()
 * @return array with all existent reasons a student quit or delays his studies
 */
 
 function get_reasons_dropout(){
     
     global $DB;
     
     $sql_query = "SELECT * FROM {talentospilos_motivos}";
     $reasons_array = $DB->get_records_sql($sql_query);
     
     return $reasons_array;
 }

/**
 * Get id switch event name 
 *
 * @see  getIdEventLogs()
 * @return int id event
 */
 function getIdEventLogs($eventname){

    global $DB;
    $sql_query = "SELECT id FROM {talentospilos_events_to_logs}  WHERE name_event = '$eventname'";
    return $DB->get_record_sql($sql_query);

 }

/**
 * Update the user image profile from php file by user id
 * @param $mdl_user_id Moodle user ID
 * @param $php_file PHP standard image
 * @return boolean
 */
function update_user_image_profile($mdl_user_id, $php_file) {
    global $CFG, $DB, $PAGE;
    $personalcontext = context_user::instance($mdl_user_id);

    $PAGE->set_context($personalcontext);

    // Prepare the editor and create form.
    $editoroptions = array(
        'maxfiles'   => EDITOR_UNLIMITED_FILES,
        'maxbytes'   => $CFG->maxbytes,
        'trusttext'  => false,
        'forcehttps' => false,
        'context'    => $personalcontext
    );
    $user = $DB->get_record('user', array('id' => $mdl_user_id));

    $user = file_prepare_standard_editor($user, 'description', $editoroptions, $personalcontext, 'user', 'profile', 0);
    // Prepare filemanager draft area.
    $draftitemid = 0;

    $filemanagercontext = $editoroptions['context'];
    $filemanageroptions = array('maxbytes'       => $CFG->maxbytes,
                                'subdirs'        => 0,
                                'maxfiles'       => 1,
                                'accepted_types' => 'web_image');
    file_prepare_draft_area($draftitemid, $filemanagercontext->id, 'user', 'newicon', 0, $filemanageroptions);
    $user->imagefile = $draftitemid;
    // Create form.
    $userform = new user_edit_form('', array(
        'editoroptions' => $editoroptions,
        'filemanageroptions' => $filemanageroptions,
        'user' => $user));

    print_r($userform->get_data());
}

/**
 * Gets a set of ASES status
 *
 * @see get_status_ases()
 * @return array with the ASUS status grouped
 */
 
 function get_status_ases(){
     
     global $DB;
     
     $sql_query = "SELECT * FROM {talentospilos_estados_ases}";
     $status_ases_array = $DB->get_records_sql($sql_query);
     
     return $status_ases_array;
 }

/**
 * Gets true or false if register economics_data exist
 *
 * @see get_exist_economics_data()
 * @return boolean
 */
 
function get_exist_economics_data($id_ases_user){
     
    global $DB;
    
    return $DB->record_exists('talentospilos_economics_data',array('id_ases_user'=> $id_ases_user));
}

/**
 * Gets record with student economics_data information
 *
 * @see get_economics_data($id_ases)
 * @param $id_ases id ases student 
 * @return object
 */
 
function get_economics_data($id_ases){
     
    global $DB;

    $sql_query = "SELECT estrato, prestacion_economica, beca, ayuda_transporte, ayuda_materiales, solvencia_econo, ocupacion_padres, 
                            nivel_educ_padres, situa_laboral_padres, expectativas_laborales FROM {talentospilos_economics_data} WHERE id_ases_user = '$id_ases'";
    $register = $DB->get_record_sql($sql_query);
    
    return $register;

    
}

/**
 * Gets record with student academics_data information
 *
 * @see get_academics_data($id_ases)
 * @param $id_ases id ases student 
 * @return object
 */
 
function get_academics_data($id_ases){
     
    global $DB;

    $sql_query = "SELECT * FROM {talentospilos_academics_data} WHERE id_ases_user = '$id_ases'";
    $register = $DB->get_record_sql($sql_query);
    
    return $register;

}


/**
 * Gets true or false if register academics_data exist
 *
 * @see get_exist_academics_data()
 * @return boolean
 */
 
function get_exist_academics_data($id_ases_user){
     
    global $DB;
    
    return $DB->record_exists('talentospilos_academics_data',array('id_ases_user'=> $id_ases_user));
}

/**
 * Gets true or false if register health_data exist
 *
 * @see get_exist_health_data()
 * @return boolean
 */
 
function get_exist_health_data($id_ases_user){
     
    global $DB;
    
    return $DB->record_exists('talentospilos_health_data',array('id_ases_user'=> $id_ases_user));
}

/**
 * Gets record with student health_data information
 *
 * @see get_health_data($id_ases)
 * @param $id_ases id ases student 
 * @return object
 */
 
function get_health_data($id_ases){
     
    global $DB;

    $sql_query = "SELECT regimen_salud, servicio_salud_vinculado, servicios_usados FROM {talentospilos_health_data} WHERE id_ases_user = '$id_ases'";
    $register = $DB->get_record_sql($sql_query);
    
    return $register;

    
}

/**
 * Return a moodle url for student profile given a student code by input
 * @param $courseid
 * @param $instanceid
 * @param $student_code
 * @return moodle_url
 */
function get_student_profile_url($courseid, $instanceid, $student_code): moodle_url {
    return new moodle_url('/blocks/ases/view/student_profile.php',
        array(
            'courseid' => $courseid,
            'instanceid'=> $instanceid,
            'student_code' => $student_code
            ));
}


/**
 * Get Condición de excepción registradas
 *
 * @see get_cond_excepcion()
 * @return object --> with CONDICIÓN DE EXCEPCIÓN information
 */
function get_cond_excepcion()
{
    global $DB; 
   $sql_query = "SELECT * FROM {talentospilos_cond_excepcion}";
   return $DB->get_records_sql($sql_query);
}


/**
 * Get Barrios
 *
 * @see get_barrios()
 * @return object --> with barrios
 */
function get_barrios(){
    global $DB; 
   $sql_query = "SELECT id, nombre, cod_comuna FROM {talentospilos_barrios} ORDER BY nombre ASC ";
   return $DB->get_records_sql($sql_query);
}

/**
 * Get Ciudades
 *
 * @see get_ciudades()
 * @return object --> with ciudades
 */
function get_ciudades(){
    global $DB; 
   $sql_query = "SELECT  id, nombre FROM {talentospilos_municipio} ORDER BY nombre ASC ";
   return $DB->get_records_sql($sql_query);
}

/**
 * Get programas academicos
 *
 * @see get_programas_academicos()
 * @return object --> with programas academicos
 */
function get_programas_academicos(){
    global $DB; 
   $sql_query = "SELECT id, nombre FROM {talentospilos_programa} ORDER BY nombre ASC ";
   return $DB->get_records_sql($sql_query);
}


/** 
 * Get Otros acompañamientos
 * 
 * @see get_otros_acompañamientos()
 * @return object -> with OTROS ACOMPAÑAMIENTOS information
 */
function get_otros_acompañamientos(){
    global $DB;
    $sql_query = "SELECT * FROM {talentospilos_otros_acom}";
    return $DB->get_records_sql($sql_query);
}

/** 
 * Get list discapacidades
 * 
 * @see get_discapacities
 * @return object -> with discapacidades information
 */
function get_discapacities(){
    global $DB;
    $sql_query = "SELECT  id, nombre FROM {talentospilos_discap_men}";
    return $DB->get_records_sql($sql_query);
}

/**
 * Get Condición de excepción segun id
 *
 * @see get_cond()
 * @return object --> with CONDICIÓN DE EXCEPCIÓN information
 */
function get_cond($id)
{
    global $DB; 
   $sql_query = "SELECT * FROM {talentospilos_cond_excepcion} WHERE id=$id";
   return $DB->get_record_sql($sql_query);
}

/**
 * Get estados civiles registrados
 *
 * @see get_estados_civiles($id_cond)
 * @return object --> with ESTADO CIVIL information
 */
function get_estados_civiles()
{
    global $DB;
    $sql_query = "SELECT * FROM {talentospilos_estado_civil}";
    return $DB->get_records_sql($sql_query);
}
/**
 * Get sedes registradas
 * 
 * @see get_sedes()
 * @return object --> with Sede information
 */

 function get_sedes(){
     global $DB;
     $sql_query = "SELECT id, nombre FROM {talentospilos_sede}";
     return $DB->get_records_sql($sql_query);
 }
/**
 * Get paises registrados
 *
 * @see get_paises()
 * @return object --> with PAIS information
 */
function get_paises()
{
    global $DB;
    $sql_query = "SELECT * FROM {talentospilos_pais}";
    return $DB->get_records_sql($sql_query);
}

/**
 * Get ocupaciones registradas
 *
 * @see get_ocupaciones()
 * @return object --> with OCUPACION information
 */
function get_ocupaciones()
{
    global $DB;
    $sql_query = "SELECT * FROM {talentospilos_ocupaciones}";
    return $DB->get_records_sql($sql_query);
}

/**
 * Update genero
 *
 * @see update_record_genero($genero)
 * @param $genero
 */
function update_record_act($act){
    global $DB;
    $act_M =strtoupper($act->actividad);
  
 if( !$DB->record_exists("talentospilos_act_simultanea",array('actividad'=> $act_M))){
     $act->actividad  = $act_M;
     if($act->id == 0){
        trigger_error('ASES Notificacion: actualizar act en la BD con id 0');
    }else{
    $DB->update_record("talentospilos_act_simultanea", $act);
    }
 }

}

/**
 * Insert genero
 *
 * @see add_record_genero($genero)
 * @param $genero
 */
function add_record_act($act){
    global $DB;
    $act_M =strtoupper($act);
  
 if( !$DB->record_exists("talentospilos_act_simultanea",array('actividad'=> $act_M))){
    $new_act = new stdClass();
    $new_act->actividad = $act_M;
    $new_act->opcion_general = 0;
    $DB->insert_record("talentospilos_act_simultanea", $new_act, true);
 }

}

/**
 * Get ID genero del parámetro
 *
 * @see get_id_genero()
 * @param $genero 
 * @return int --> with id genero
 */

function get_id_act($act){

    global $DB;
    $act_M =strtoupper($act);
    $sql_query = "SELECT id FROM {talentospilos_act_simultanea} WHERE actividad = '$act_M'";
    $id_act = $DB->get_record_sql($sql_query);
    $id_act = $id_act->id;
    return $id_act;
    
    }

/**
 * Update genero
 *
 * @see update_record_genero($genero)
 * @param $genero
 */
function update_record_genero($genero){
    global $DB;
    $genero_M =strtoupper($genero->genero);
  
 if( !$DB->record_exists("talentospilos_identidad_gen",array('genero'=> $genero_M))){
     $genero->genero  = $genero_M;
     if($genero->id == 0){
        trigger_error('ASES Notificacion: actualizar genero en la BD con id 0');
    }else{
    $DB->update_record("talentospilos_identidad_gen", $genero);
    }
 }

}

/**
 * Insert genero
 *
 * @see add_record_genero($genero)
 * @param $genero
 */
function add_record_genero($genero){
    global $DB;
    $genero_M =strtoupper($genero);
  
 if( !$DB->record_exists("talentospilos_identidad_gen",array('genero'=> $genero_M))){
    $new_genero = new stdClass();
    $new_genero->genero = $genero_M;
    $new_genero->opcion_general = 0;
    $DB->insert_record("talentospilos_identidad_gen", $new_genero, true);
 }

}

/**
 * Get ID genero del parámetro
 *
 * @see get_id_genero()
 * @param $genero 
 * @return int --> with id genero
 */

function get_id_genero($genero){

    global $DB;
    $genero_M =strtoupper($genero);
    $sql_query = "SELECT id FROM {talentospilos_identidad_gen} WHERE genero = '$genero_M'";
    $id_genero = $DB->get_record_sql($sql_query);
    $id_genero = $id_genero->id;
    return $id_genero;
    
    }

/**
 * Get generos registrados
 *
 * @see get_generos()
 * @return object --> with GENERO information
 */

function  get_generos()
{
    global $DB; 
   $sql_query = "SELECT * FROM {talentospilos_identidad_gen}";
   return $DB->get_records_sql($sql_query);
}


/**
 * Get opciones de sexo registradas en base de datos
 * 
 * @author Juan Pablo Castro
 * @see get_sex_options()
 * @return array --> con opciones sexo
 */

function  get_sex_options()
{
    global $DB; 
   $sql_query = "SELECT * FROM {talentospilos_sexo}";
   return $DB->get_records_sql($sql_query);
}


/**
 * Get etnias registrados
 *
 * @see get_etnias()
 * @return object --> with ETNIA information
 */

function  get_etnias()
{
    global $DB; 
   $sql_query = "SELECT * FROM {talentospilos_etnia}";
   return $DB->get_records_sql($sql_query);
}



/**
 * Get actividades simultaneas registrados
 *
 * @see get_act_simultaneas()
 * @return object --> with CONDICIÓN DE EXCEPCIÓN information
 */

function  get_act_simultaneas()
{
    global $DB; 
   $sql_query = "SELECT * FROM {talentospilos_act_simultanea}";
   return $DB->get_records_sql($sql_query);
}


 
/**
 * Gets a set of ICETEX status
 *
 * @see get_icetex_statuses()
 * @return array with the ICETEX status grouped
 */
 function get_icetex_statuses(){
     
     global $DB;
     
     $sql_query = "SELECT * FROM {talentospilos_estados_icetex}";
     $status_icetex_array = $DB->get_records_sql($sql_query);
     
     return $status_icetex_array;
 }

 /**
 * Gets a set of ICETEX status
 *
 * @see get_icetex_statuses()
 * @return array with the ICETEX status grouped
 */
 function get_icetex_status_student($ases_student_id){

    global $DB;

    $sql_query = "SELECT id_estado_icetex 
                  FROM {talentospilos_est_est_icetex} 
                  WHERE fecha = (SELECT MAX(fecha) 
                                 FROM {talentospilos_est_est_icetex} 
                                 WHERE id_estudiante = $ases_student_id) AND id_estudiante = $ases_student_id";
    
    $icetex_status_student = $DB->get_record_sql($sql_query);

    return $icetex_status_student;
 }
   
 /**
 * Update moodle user 
 *
 * @see studentprofile_update_email_moodle
 * @param $obj_updatable_moodle
 * @return bool is update?
 */

 function  studentprofile_update_email_moodle($obj_updatable_moodle){

    global $DB;

    preg_match("/((?:[a-z]+\.)*[a-z]+(?:@correounivalle\.edu\.co))/",$obj_updatable_moodle->email  , $array_match);


    if (!empty($array_match)) {

        if (strcmp($array_match[0],$obj_updatable_moodle->email) !== 0) {
   
            $result_cv_update = false;
        } else{
            if($obj_updatable_moodle->id == 0){
                trigger_error('ASES Notificacion: actualizar email en la BD con id 0');
                return false;
            }
            $result_cv_update = $DB->update_record('user', $obj_updatable_moodle);
        }
    }else {
   
        $result_cv_update = false;
    }

    return  $result_cv_update;

 }
  
 /**
 * Get moodle user from talentospilos_user_extended
 *
 * @see get_moodle_user
 * @param $ases_id
 * @return int id_moodle_user
 */
 function get_moodle_id($ases_id){

 global $DB;

 $sql_query = "SELECT id_moodle_user FROM {talentospilos_user_extended} WHERE id_ases_user = $ases_id AND tracking_status = 1";
 $status = $DB->get_record_sql($sql_query)->id_moodle_user;
 return $status;

}
 
/**
 * Gets the ASES status for a student
 *
 * @see get_ases_status
 * @param $ases_id
 * @return array ASES status in instances
 */
function get_ases_status($ases_id){

    global $DB;

    $sql_query = "SELECT id_moodle_user FROM {talentospilos_user_extended} WHERE id_ases_user = $ases_id AND tracking_status = 1";
    $id_moodle_user = $DB->get_record_sql($sql_query)->id_moodle_user;

    $sql_query = "SELECT DISTINCT inst_cohorts.id_instancia
                  FROM {cohort_members} AS cohorts
                  INNER JOIN {talentospilos_inst_cohorte} AS inst_cohorts ON inst_cohorts.id_cohorte = cohorts.cohortid
                  WHERE userid = $id_moodle_user";

    $array_instances = $DB->get_records_sql($sql_query);
    $array_instances_status = array();

    foreach($array_instances as $instance){

        $sql_query = "SELECT ases_statuses.id, st_status_ases.id_estado_ases, ases_statuses.nombre
                      FROM {talentospilos_est_estadoases} AS st_status_ases
                      INNER JOIN {talentospilos_estados_ases} AS ases_statuses ON st_status_ases.id_estado_ases = ases_statuses.id
                      WHERE id_instancia = $instance->id_instancia AND st_status_ases.id_estudiante = $ases_id AND st_status_ases.fecha = (SELECT MAX(fecha) 
                                                                                                                                           FROM {talentospilos_est_estadoases}
                                                                                                                                           WHERE id_instancia = $instance->id_instancia AND id_estudiante = $ases_id)
                      GROUP BY ases_statuses.id, st_status_ases.id_estado_ases, ases_statuses.nombre";

        $ases_status_instance = $DB->get_record_sql($sql_query);

        if($ases_status_instance){
            $instance->nombre = $ases_status_instance->nombre;
        }else{
            $instance->nombre = 'NO REGISTRA';
        }

        $array_instances_status[$instance->id_instancia] = $instance;
    }

    return $array_instances_status;
}

 /**
 * Verify ASES status
 *
 * @see verify_ases_status
 * @param $id_ases_student
 * @return int
 */
function verify_ases_status($id_ases_student){
    
    $array_status_instances = get_ases_status($id_ases_student);
    $result = 0;

    foreach($array_status_instances as $instance){
        if($instance->nombre == 'seguimiento'){
            return 1;
        }
    }

    return $result;
}

/**
 * Returns result of query at table 'talentospilos_retiros'
 *
 * @author  Juan Pablo Castro. GitHub: jpcv222
 * @see save_reason_dropout_student()
 * @param $code_student
 * @return string
 */

function getReasonDropoutStudent($code_student){

    global $DB;

    $result = "";
    $sql_query = "SELECT detalle, id_motivo FROM {talentospilos_retiros}  WHERE id_usuario=$code_student";
    $tmp_result = $DB->get_record_sql($sql_query);
    if(!empty($tmp_result)){
        $sql_query ="SELECT descripcion FROM {talentospilos_motivos} WHERE id = $tmp_result->id_motivo";
        $motivo =  $DB->get_record_sql($sql_query)->descripcion;
        $result = "Motivo retiro: " . $motivo . "\nDetalle: ".$tmp_result->detalle;
    }
    return $result;
}


/**
 * Returns result of the transaction at database: table 'talentospilos_retiros'
 *
 * @author  Juan Pablo Castro. GitHub: jpcv222
 *
 * @see save_reason_dropout_student()
 * @param $code_student
 * @param $reason
 * @param $observation
 * @return integer
 */

function save_reason_dropout_ases($code_student, $reason, $observation){
    global $DB;
    $id_ases_student = get_ases_user_by_code($code_student)->id;

    $record = new stdClass();
    $record->id_usuario = $id_ases_student;
    $record->id_motivo = $reason;
    $record->detalle = $observation;


    $sql_query = "SELECT id FROM {talentospilos_retiros} WHERE id_usuario= '$id_ases_student'";
    $exists = $DB->get_record_sql($sql_query);

    if($exists) {
        $record->id = $exists->id;
        if($record->id == 0){
            trigger_error('ASES Notificacion: actualizar retiro en la BD con id 0');
            return 0;
        }
        $result = $DB->update_record('talentospilos_retiros', $record);
    }
    else {
        $result = $DB->insert_record('talentospilos_retiros', $record, false);
    }

    if($result) {
        return 1;
    } else {
        return 0;
    }
}

/**
 * Update the ASES status for a student
 *
 * @see update_status_ases
 * @param $current_status
 * @param $new_status
 * @param $instance_id
 * @param $code_student
 * @return integer
 */
function update_status_ases($current_status, $new_status, $instance_id, $code_student, $reason=null, $observation=null){

    global $DB;

    date_default_timezone_set('America/Bogota');

    $sql_query = "SELECT id FROM {talentospilos_estados_ases} WHERE nombre = '$new_status'";
    $id_new_status = $DB->get_record_sql($sql_query)->id;

    $id_ases_student = get_ases_user_by_code($code_student)->id;

    $array_instances = get_ases_status($id_ases_student);

    $today_timestamp = time();
    $record = new stdClass();

    $sql_query = "SELECT id FROM {talentospilos_estados_ases} WHERE nombre = 'sinseguimiento'";
    $id_no_tracking_status = $DB->get_record_sql($sql_query)->id;

    // **************************************
    //Iniciar transacción en la base de datos
    // **************************************

    foreach($array_instances as $instance){

        $record->id_estudiante = $id_ases_student;
        $record->fecha = $today_timestamp;
        $record->id_instancia = $instance->id_instancia;
        $record->id_motivo_retiro = $reason;

        if($instance->id_instancia == $instance_id){
            $record->id_estado_ases = $id_new_status;
            $result = $DB->insert_record('talentospilos_est_estadoases', $record);
        }else{
            if($new_status == 'seguimiento' && ($instance->nombre == 'seguimiento' || $instance->nombre == 'NO REGISTRA')){
                $record->id_estado_ases = $id_no_tracking_status;
                $result = $DB->insert_record('talentospilos_est_estadoases', $record);
            }else if($instance->nombre == 'sinseguimiento'){
                $result = 1;
            }else{
                $result = 0;
            }
        }

        if(!$result){
            return 0;
        }
    }

    return ($result)?1:0;
}
 

/**
 * Gets every track of a student given his id, track type and instance associated to the track and the student
 *
 * @see get_trackings_student($id_ases, $tracking_type, $id_instance)
 * @param $id_ases --> {talentospilos_profile} table id
 * @param $tracking_type --> [PARES, GRUPAL]
 * @param $id_instance --> module associated instance
 * @return array of trackings
 */
 
function get_trackings_student($id_ases, $tracking_type, $id_instance){
     
    global $DB;

    $sql_query="SELECT *, seguimiento.id as id_seg 
                FROM {talentospilos_seguimiento} AS seguimiento INNER JOIN {talentospilos_seg_estudiante} AS seg_estudiante  
                                                ON seguimiento.id = seg_estudiante.id_seguimiento  where seguimiento.tipo ='".$tracking_type."' AND seguimiento.status <> 0;";
    
    if($id_instance != null ){
        $sql_query =  trim($sql_query,";");    
        $sql_query .= " AND seguimiento.id_instancia=".$id_instance." ;";
    }
    
    if($id_ases != null){
        $sql_query = trim($sql_query,";");
        $sql_query .= " AND seg_estudiante.id_estudiante =".$id_ases.";";
    }

    $sql_query = trim($sql_query,";");
    $sql_query .= "order by seguimiento.fecha desc;";
    
    $tracking_array = $DB->get_records_sql($sql_query);

    return $tracking_array;
}

/**
 * Latest version
 *
 * @see dphpformsV2_find_records, dphpformsV2_find_records
 * @param $student_id string -> ASES student id
 * @return array of trackings
 */
function get_tracking_current_semesterV4($student_id) {

    $all_trackings = null;

    $xQuery = new stdClass();
    $xQuery->form = "seguimiento_pares";
    $xQuery->filterFields = [["id_estudiante",[[$student_id,"="]], false],
        ["fecha",[["%%","LIKE"]], false],
        ["revisado_profesional",[["%%","LIKE"]], false],
        ["revisado_practicante",[["%%","LIKE"]], false],
        ["puntuacion_riesgo_individual",[["%%","LIKE"]], false],
        ["puntuacion_riesgo_academico",[["%%","LIKE"]], false],
        ["puntuacion_riesgo_economico",[["%%","LIKE"]], false],
        ["puntuacion_riesgo_familiar",[["%%","LIKE"]], false],
        ["puntuacion_vida_uni",[["%%","LIKE"]], false]
    ];
    $xQuery->orderFields = [["fecha","DESC"]];
    $xQuery->orderByDatabaseRecordDate = false;
    $xQuery->recordStatus = [ "!deleted" ];
    $xQuery->asFields = [ [ [ function( $_this ){ return "seguimiento_pares"; } ] , 'alias_form' ],
        [ [ function( $_this ){ return strtotime($_this['fecha']); } ] , 'fecha_timestamp' ] ];

    $trackings = dphpformsV2_find_records( $xQuery );

    $xQuery = new stdClass();
    $xQuery->form = "inasistencia";
    $xQuery->filterFields = [["in_id_estudiante",[[$student_id,"="]], false],
        ["in_fecha",[["%%","LIKE"]], false],
        ["in_revisado_profesional",[["%%","LIKE"]], false],
        ["in_revisado_practicante",[["%%","LIKE"]], false]
    ];
    $xQuery->orderFields = [["in_fecha","DESC"]];
    $xQuery->orderByDatabaseRecordDate = false;
    $xQuery->recordStatus = [ "!deleted" ];
    $xQuery->asFields = [ ['in_fecha', 'fecha'],
        [ [ function( $_this ){ return "inasistencia"; } ] , 'alias_form' ],
        [ [ function( $_this ){ return strtotime($_this['in_fecha']); } ], 'fecha_timestamp' ]
        ];

    $in_trackings = dphpformsV2_find_records( $xQuery );

    $all_trackings = array_merge( $trackings, $in_trackings );

    $trackings = NULL;
    $in_trackings = NULL;

    $fecha = array();
    foreach ($all_trackings as $key => $tracking){
        $fecha[$key] =  strtotime( $tracking['fecha'] );
    }
    array_multisort($fecha, SORT_DESC, $all_trackings);

    return $all_trackings;
}

function get_tracking_current_semesterV3($criterio,$student_id, $semester_id,$intervals=null){

    $fecha_inicio = null;
    $fecha_fin = null;

    if( $intervals ){

        $fecha_inicio = getdate(strtotime($intervals[0]));
        $fecha_fin = getdate(strtotime($intervals[1]));

    }else{

        $interval = core_periods_get_period_by_id($semester_id);
        $fecha_inicio = getdate(strtotime($interval->fecha_inicio));
        $fecha_fin = getdate(strtotime($interval->fecha_fin));
    }

    $mon_tmp = $fecha_inicio["mon"];
    $day_tmp = $fecha_inicio["mday"];
    if( $mon_tmp < 10 ){
        $mon_tmp = "0" . $mon_tmp;
    }
    if( $day_tmp < 10 ){
        $day_tmp = "0" . $day_tmp;
    }

    $fecha_inicio_str = $fecha_inicio["year"]."-".$mon_tmp."-".$day_tmp;

    $mon_tmp = $fecha_fin["mon"];
    $day_tmp = $fecha_fin["mday"];
    if( $mon_tmp < 10 ){
        $mon_tmp = "0" . $mon_tmp;
    }
    if( $day_tmp < 10 ){
        $day_tmp = "0" . $day_tmp;
    }

    $fecha_fin_str = $fecha_fin["year"]."-".$mon_tmp."-".$day_tmp;

    //$student [monitor or student]
    $all_trackings = null;

    if( $criterio == 'student' ){

        $xQuery = new stdClass();
        $xQuery->form = "seguimiento_pares";
        $xQuery->filterFields = [["id_estudiante",[[$student_id,"="]], false],
            ["fecha",[[$fecha_inicio_str,">="],[$fecha_fin_str,"<="]], false],
            ["revisado_profesional",[["%%","LIKE"]], false],
            ["revisado_practicante",[["%%","LIKE"]], false],
            ["puntuacion_riesgo_individual",[["%%","LIKE"]], false],
            ["puntuacion_riesgo_academico",[["%%","LIKE"]], false],
            ["puntuacion_riesgo_economico",[["%%","LIKE"]], false],
            ["puntuacion_riesgo_familiar",[["%%","LIKE"]], false],
            ["puntuacion_vida_uni",[["%%","LIKE"]], false]
        ];
        $xQuery->orderFields = [["fecha","DESC"]];
        $xQuery->orderByDatabaseRecordDate = false;
        $xQuery->recordStatus = [ "!deleted" ];
        $xQuery->asFields = [ [ [ function( $_this ){ return "seguimiento_pares"; } ] , 'alias_form' ] ];

        $trackings = dphpformsV2_find_records( $xQuery );

        $xQuery = new stdClass();
        $xQuery->form = "inasistencia";
        $xQuery->filterFields = [["in_id_estudiante",[[$student_id,"="]], false],
            ["in_fecha",[[$fecha_inicio_str,">="],[$fecha_fin_str,"<="]], false],
            ["in_revisado_profesional",[["%%","LIKE"]], false],
            ["in_revisado_practicante",[["%%","LIKE"]], false]
        ];
        $xQuery->orderFields = [["in_fecha","DESC"]];
        $xQuery->orderByDatabaseRecordDate = false;
        $xQuery->recordStatus = [ "!deleted" ];
        $xQuery->asFields = [ [ 'in_fecha', 'fecha' ], [ [ function( $_this ){ return "inasistencia"; } ] , 'alias_form' ] ];

        $in_trackings = dphpformsV2_find_records( $xQuery );

        $all_trackings = array_merge( $trackings, $in_trackings );

        $trackings = NULL;
        $in_trackings = NULL;

        $fecha = array();
        foreach ($all_trackings as $key => $tracking){
            $fecha[$key] =  strtotime( $tracking['fecha'] );
        }
        array_multisort($fecha, SORT_DESC, $all_trackings);

    }elseif( $criterio == 'monitor' ){

        $xQuery = new stdClass();
        $xQuery->form = "seguimiento_pares";
        $xQuery->filterFields = [["id_creado_por",[[$student_id,"="]], false],
            ["fecha",[[$fecha_inicio_str,">="],[$fecha_fin_str,"<="]], false],
            ["revisado_profesional",[["%%","LIKE"]], false],
            ["revisado_practicante",[["%%","LIKE"]], false],
            ["puntuacion_riesgo_individual",[["%%","LIKE"]], false],
            ["puntuacion_riesgo_academico",[["%%","LIKE"]], false],
            ["puntuacion_riesgo_economico",[["%%","LIKE"]], false],
            ["puntuacion_riesgo_familiar",[["%%","LIKE"]], false],
            ["puntuacion_vida_uni",[["%%","LIKE"]], false]
        ];
        $xQuery->orderFields = [["fecha","DESC"]];
        $xQuery->orderByDatabaseRecordDate = false;
        $xQuery->recordStatus = [ "!deleted" ];
        $xQuery->asFields = [ [ [ function( $_this ){ return "seguimiento_pares"; } ] , 'alias_form' ] ];

        $trackings = dphpformsV2_find_records( $xQuery );

        $xQuery = new stdClass();
        $xQuery->form = "inasistencia";
        $xQuery->filterFields = [["in_id_creado_por",[[$student_id,"="]], false],
            ["in_fecha",[[$fecha_inicio_str,">="],[$fecha_fin_str,"<="]], false],
            ["in_revisado_profesional",[["%%","LIKE"]], false],
            ["in_revisado_practicante",[["%%","LIKE"]], false]
        ];
        $xQuery->orderFields = [["in_fecha","DESC"]];
        $xQuery->orderByDatabaseRecordDate = false;
        $xQuery->recordStatus = [ "!deleted" ];
        $xQuery->asFields = [ [ 'in_fecha', 'fecha' ], [ [ function( $_this ){ return "inasistencia"; } ] , 'alias_form' ] ];

        $in_trackings = dphpformsV2_find_records( $xQuery );

        $all_trackings = array_merge( $trackings, $in_trackings );

        $trackings = NULL;
        $in_trackings = NULL;

        $fecha = array();
        foreach ($all_trackings as $key => $tracking){
            $fecha[$key] =  strtotime( $tracking['fecha'] );
        }
        array_multisort($fecha, SORT_DESC, $all_trackings);
    }
    return $all_trackings;
}

function get_tracking_current_semesterV2($criterio,$student_id, $semester_id,$intervals=null){

    $fecha_inicio = null;
    $fecha_fin = null;

    if( $intervals ){

        $fecha_inicio = getdate(strtotime($intervals[0]));
        $fecha_fin = getdate(strtotime($intervals[1]));

    }else{

        $interval = core_periods_get_period_by_id($semester_id);
        $fecha_inicio = getdate(strtotime($interval->fecha_inicio));
        $fecha_fin = getdate(strtotime($interval->fecha_fin));
    }

    $mon_tmp = $fecha_inicio["mon"];
    $day_tmp = $fecha_inicio["mday"];
    if( $mon_tmp < 10 ){
        $mon_tmp = "0" . $mon_tmp;
    }
    if( $day_tmp < 10 ){
        $day_tmp = "0" . $day_tmp;
    }

    $fecha_inicio_str = $fecha_inicio["year"]."-".$mon_tmp."-".$day_tmp;

    $mon_tmp = $fecha_fin["mon"];
    $day_tmp = $fecha_fin["mday"];
    if( $mon_tmp < 10 ){
        $mon_tmp = "0" . $mon_tmp;
    }
    if( $day_tmp < 10 ){
        $day_tmp = "0" . $day_tmp;
    }

    $fecha_fin_str = $fecha_fin["year"]."-".$mon_tmp."-".$day_tmp;

    //$student [monitor or student]
    $trackings = null;
    
    if( $criterio == 'student' ){

        $xQuery = new stdClass();
        $xQuery->form = "seguimiento_pares";
        $xQuery->filterFields = [["id_estudiante",[[$student_id,"="]], false],
                                 ["fecha",[[$fecha_inicio_str,">="],[$fecha_fin_str,"<="]], false],
                                 ["revisado_profesional",[["%%","LIKE"]], false],
                                 ["revisado_practicante",[["%%","LIKE"]], false]
                                ];
        $xQuery->orderFields = [["fecha","DESC"]];
        $xQuery->orderByDatabaseRecordDate = false; 
        $xQuery->recordStatus = [ "!deleted" ];
        $xQuery->selectedFields = [ ]; 

        $trackings = dphpformsV2_find_records( $xQuery );

   }elseif( $criterio == 'monitor' ){
        
        $xQuery = new stdClass();
        $xQuery->form = "seguimiento_pares";
        $xQuery->filterFields = [["id_creado_por",[[$student_id,"="]], false],
                                 ["fecha",[[$fecha_inicio_str,">="],[$fecha_fin_str,"<="]], false],
                                 ["revisado_profesional",[["%%","LIKE"]], false],
                                 ["revisado_practicante",[["%%","LIKE"]], false]
                                ];
        $xQuery->orderFields = [["fecha","DESC"]];
        $xQuery->orderByDatabaseRecordDate = false; 
        $xQuery->recordStatus = [ "!deleted" ];
        $xQuery->selectedFields = [  ]; 

        $trackings = dphpformsV2_find_records( $xQuery );

    } 
    
    return $trackings;

}

function get_tracking_current_semester($criterio,$student_id, $semester_id,$intervals=null){


    if($intervals!=null){


        $fecha_inicio = getdate(strtotime($intervals[0]));
        $fecha_fin = getdate(strtotime($intervals[1]));
        $ano_semester  = $fecha_inicio['year'];

    }else{
        $interval = core_periods_get_period_by_id($semester_id);
        $fecha_inicio = getdate(strtotime($interval->fecha_inicio));
        $fecha_fin = getdate(strtotime($interval->fecha_fin));
        $ano_semester  = $fecha_inicio['year'];
    }

    $array_peer_trackings_dphpforms = array();
    $array_inasistencia_peer_trackings_dphpforms = array();

   if($criterio=='student'){
        $array_peer_trackings_dphpforms = dphpforms_find_records('seguimiento_pares', 'seguimiento_pares_id_estudiante', $student_id, 'DESC');
        $array_inasistencia_peer_trackings_dphpforms = dphpforms_find_records('inasistencia', 'inasistencia_id_estudiante', $student_id, 'DESC');
   }else if($criterio=='monitor'){
        $array_peer_trackings_dphpforms = dphpforms_find_records('seguimiento_pares', 'seguimiento_pares_id_creado_por', $student_id, 'DESC');
        $array_inasistencia_peer_trackings_dphpforms = dphpforms_find_records('inasistencia', 'inasistencia_id_creado_por', $student_id, 'DESC');
    }

    $array_peer_trackings_dphpforms = json_decode($array_peer_trackings_dphpforms);
    $array_inasistencia_peer_trackings_dphpforms = json_decode($array_inasistencia_peer_trackings_dphpforms);

    $array_detail_peer_trackings_dphpforms = array();
    $array_detail_inasistencia_peer_trackings_dphpforms = array();

    foreach ($array_peer_trackings_dphpforms->results as &$peer_trackings_dphpforms) {
        array_push($array_detail_peer_trackings_dphpforms, json_decode(dphpforms_get_record($peer_trackings_dphpforms->id_registro, 'fecha')));
    };

    foreach ($array_inasistencia_peer_trackings_dphpforms->results as &$inasistencia_peer_trackings_dphpforms) {
        array_push($array_detail_inasistencia_peer_trackings_dphpforms, json_decode(dphpforms_get_record($inasistencia_peer_trackings_dphpforms->id_registro, 'in_fecha')));
    };

    $array_tracking_date = array();

    foreach ($array_detail_peer_trackings_dphpforms as &$peer_tracking) {
        foreach ($peer_tracking->record->campos as &$tracking) {
            if ($tracking->local_alias == 'fecha') {
                array_push($array_tracking_date, strtotime($tracking->respuesta));
            }
        }
    };

    foreach ($array_detail_inasistencia_peer_trackings_dphpforms as &$inasistencia_peer_tracking) {
        foreach ($inasistencia_peer_tracking->record->campos as &$tracking) {
            if ($tracking->local_alias == 'in_fecha') {
                array_push($array_tracking_date, strtotime($tracking->respuesta));
            };
        };
    };

    rsort($array_tracking_date);

    $seguimientos_ordenados = new stdClass();
    $seguimientos_ordenados->index = array();
    //Inicio de ordenamiento
    $periodo_actual = [];
    for($l = $fecha_inicio['mon']; $l <= $fecha_fin['mon']; $l++ ){
        array_push($periodo_actual, $l);
    }
    for ($x = 0; $x < count($array_tracking_date); $x++) {
        $string_date = $array_tracking_date[$x];
        $array_tracking_date[$x] = getdate($array_tracking_date[$x]);
        $year = $array_tracking_date[$x]['year'];
        if (property_exists($seguimientos_ordenados, $year)) {
            if (in_array($array_tracking_date[$x]['mon'], $periodo_actual)) {
                // Records where we expect to find
                for ($y = 0; $y < count($array_detail_peer_trackings_dphpforms); $y++) {
                    if ($array_detail_peer_trackings_dphpforms[$y]) {
                        foreach ($array_detail_peer_trackings_dphpforms[$y]->record->campos as &$tracking) {
                            if ($tracking->local_alias == 'fecha') {
                                if (strtotime($tracking->respuesta) == $string_date) {
                                    array_push($seguimientos_ordenados->$year->periodo, $array_detail_peer_trackings_dphpforms[$y]);
                                    $array_detail_peer_trackings_dphpforms[$y] = null;
                                    break;
                                }
                            }
                        }
                    }
                }
                //Inasistencia
                for ($y = 0; $y < count($array_detail_inasistencia_peer_trackings_dphpforms); $y++) {
                    if ($array_detail_inasistencia_peer_trackings_dphpforms[$y]) {
                        foreach ($array_detail_inasistencia_peer_trackings_dphpforms[$y]->record->campos as &$tracking) {
                            if ($tracking->local_alias == 'in_fecha') {
                                if (strtotime($tracking->respuesta) == $string_date) {
                                    array_push($seguimientos_ordenados->$year->periodo, $array_detail_inasistencia_peer_trackings_dphpforms[$y]);
                                    $array_detail_inasistencia_peer_trackings_dphpforms[$y] = null;
                                    break;
                                }
                            }
                        }
                    }
                }
            } 

        } else {
            array_push($seguimientos_ordenados->index, $year);
            $seguimientos_ordenados->$year->year = $year;
            $seguimientos_ordenados->$year->periodo = array();

            //$seguimientos_ordenados->$year->year = $year;
            if(in_array($array_tracking_date[$x]['mon'], $periodo_actual)){
                // Records where we expect to find
                for($y = 0; $y < count($array_detail_peer_trackings_dphpforms); $y++){
                    if($array_detail_peer_trackings_dphpforms[$y]){
                        foreach ($array_detail_peer_trackings_dphpforms[$y]->record->campos as &$tracking) {
                            if ($tracking->local_alias == 'fecha') {
                                if (strtotime($tracking->respuesta) == $string_date) {
                                    array_push($seguimientos_ordenados->$year->periodo, $array_detail_peer_trackings_dphpforms[$y]);
                                    $array_detail_peer_trackings_dphpforms[$y] = null;
                                    break;
                                }
                            }
                        }
                    }
                }
                // Inasistencia
                for($y = 0; $y < count($array_detail_inasistencia_peer_trackings_dphpforms); $y++){
                    if($array_detail_inasistencia_peer_trackings_dphpforms[$y]){
                        foreach ($array_detail_inasistencia_peer_trackings_dphpforms[$y]->record->campos as &$tracking) {
                            if ($tracking->local_alias == 'in_fecha') {
                                if (strtotime($tracking->respuesta) == $string_date) {
                                    array_push($seguimientos_ordenados->$year->periodo, $array_detail_inasistencia_peer_trackings_dphpforms[$y]);
                                    $array_detail_inasistencia_peer_trackings_dphpforms[$y] = null;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    $seguimientos_array = json_decode(json_encode($seguimientos_ordenados), true);
    $array_periodos = array();
    for ($x = 0; $x < count($seguimientos_array['index']); $x++) {
        array_push($array_periodos, $seguimientos_array[$seguimientos_array['index'][$x]]);
    }
    /*$peer_tracking_v2 = array(
        'index' => $seguimientos_array['index'],
        'periodos' => $array_periodos,
    );*/

    //return;
    return $array_periodos;

}

/**
 * Gets all of a student grouped by semester
 * 
 * @see get_tracking_group_by_semester($id_ases = null, $tracking_type, $id_semester = null, $id_instance = null)
 * @param $id_ases --> ASES id on talentospilos_profile table
 * @param $tracking_type --> [PARES, GRUPAL]
 * @param $id_instance --> module instance id
 * @return array --> trackings grouped by semester
 */
 
function get_tracking_group_by_semester($id_ases = null, $tracking_type, $id_semester = null, $id_instance = null){
     
    global $DB;
    
    $result = get_trackings_student($id_ases, $tracking_type, $id_instance );
    
    if(count($result) != 0){
        $trackings_array = array();
    
        foreach ($result as $r){
            array_push($trackings_array, $r);
        }
        
        $last_semestre = false;
        $first_semester = false;
        
        $sql_query = "SELECT * FROM {talentospilos_semestre} WHERE id_instancia=$id_instance OR id_instancia IS NULL";
        
        if($id_semester != null){
            $sql_query .= " AND id = ".$id_semester;
        }else{
            // TO DO: $id_ases should not reach this as null
            if($id_ases == null){
                return 1;
            }else{
                $userid = $DB->get_record_sql("SELECT id_moodle_user AS userid FROM {talentospilos_user_extended} WHERE id_ases_user = $id_ases AND tracking_status=1");
                $firstsemester = get_id_first_semester($userid->userid, $id_instance);
                $lastsemestre = get_id_last_semester($userid->userid, $id_instance);

                $sql_query .= " AND id >=".$firstsemester . " AND fecha_inicio < '2017-12-31 00:00:00'";
            }

        }
        $sql_query.=" order by fecha_inicio DESC";
    
        $array_semesters_seguimientos =  array();
        
        if($lastsemestre && $firstsemester){
            
            $semesters = $DB->get_records_sql($sql_query);
            $counter = 0;
    
            $sql_query ="select * from {talentospilos_semestre} where id = ".$lastsemestre;
            $lastsemestreinfo = core_periods_get_period_by_id($lastsemestre);
            
            foreach ($semesters as $semester){
                
                if($lastsemestreinfo && (strtotime($semester->fecha_inicio) <= strtotime($lastsemestreinfo->fecha_inicio))){ //Gets info from semesters the student is registered
                    $semester_object = new stdClass;
                    
                    $semester_object->id_semester = $semester->id;
                    $semester_object->name_semester = $semester->nombre;
                    $group_tracking_array = array();
                    
                    while(compare_date(strtotime($semester->fecha_inicio), strtotime($semester->fecha_fin),$trackings_array[$counter]->created)){
                        
                        array_push($group_tracking_array, $trackings_array[$counter]);
                        $counter+=1;
                        
                        if ($counter == count($trackings_array)){
                            break;
                        }
                        
                    }
                    
                    foreach($group_tracking_array as $r){
                        $r->fecha = date('d-m-Y', $r->fecha);
                        $r->created = date('d-m-Y', $r->created);
                    }
    
                    $semester_object->result = $group_tracking_array;
                    $semester_object->rows = count($group_tracking_array);
                    array_push($array_semesters_seguimientos, $semester_object);
                }
            }
            
        }        
        
        $object_seguimientos =  new stdClass();
        
        $object_seguimientos->semesters_segumientos = $array_semesters_seguimientos;
        return $object_seguimientos;
    }else{
        return null;
    }
}

/**
 * Gets the student first semester
 * 
 * @see get_id_first_semester($id)
 * @param $id --> student id
 * @param $id_instance --> instance id
 * @return string --> first semester id
 */
function get_id_first_semester($id, $id_instance){
    try {
        global $DB;
        
        $sql_query = "SELECT username, timecreated from {user} where id = ".$id;
        $result = $DB->get_record_sql($sql_query);
        
        $year_string = substr($result->username, 0, 2);
        $date_start = strtotime('01-01-20'.$year_string);

        if (!$date_start) throw new Exception('Error formando fecha de inicio con año ' . $year_string);

        if(!$result) throw new Exception('error al consultar fecha de creación');

        $timecreated = $result->timecreated;
        
        if($timecreated <= 0){
            
            $sql_query = "SELECT MIN(courses.timecreated)
                          FROM {user_enrolments} AS userEnrolments INNER JOIN {enrol} AS enrols ON userEnrolments.enrolid = enrols.id 
                                                                   INNER JOIN {course} AS courses ON enrols.courseid = courses.id 
                          WHERE userEnrolments.userid = $id AND courses.timecreated >= ".$date_start;

            $courses = $DB->get_record_sql($sql_query);

            $timecreated = $courses->min;
        }

        //$semesters = $DB->get_records_sql($sql_query);
        $semesters = core_periods_get_all_periods($id_instance); 
        $id_first_semester = 1; 

        foreach ($semesters as $semester){
            $fecha_inicio = new DateTime($semester->fecha_inicio);

            date_add($fecha_inicio, date_interval_create_from_date_string('-60 days'));
            
            if((strtotime($fecha_inicio->format('Y-m-d')) <= $timecreated) && ($timecreated <= strtotime($semester->fecha_fin))){
                return ($semester->id);
            }

            if ($semester->id == 1) {
                if (!(strtotime($fecha_inicio->format('Y-m-d')) <= $timecreated) && $timecreated <= strtotime($semester->fecha_fin)) {
                    return 1;
                } 
            }
        }


    }catch(Exeption $e){
        return "Error en la consulta primer semestre";
    }
}

/**
 * Returns an array of semesters of a student
 *
 * @param $username_student --> moodle student username
 * @param $instance_id --> moodle course instance id
 * @return array --> stdClass object representing semesters of a student
 */
function get_semesters_stud($id_first_semester, $instance_id){
     
    global $DB;
    if (is_null($id_first_semester) || is_null($instance_id)) {
        Throw new Exception('Uno o varios argumentos son nulos.');
    }
     
    $sql_query = 
        "SELECT id, nombre, fecha_inicio, fecha_fin 
        FROM {talentospilos_semestre} 
        WHERE id_instancia=$instance_id 
        OR id_instancia IS NULL
        AND id >= $id_first_semester 
        ORDER BY fecha_inicio DESC";

    $result_query = $DB->get_records_sql($sql_query);
     
    $semesters_array = array();
     
    foreach ($result_query as $result){
      array_push($semesters_array, $result);
    }
    return $semesters_array;
}

function compare_date($fecha_inicio, $fecha_fin, $fecha_comparar){
    
    $fecha_inicio = new DateTime(date('Y-m-d',$fecha_inicio));
    date_add($fecha_inicio, date_interval_create_from_date_string('-30 days'));
    
    return (((int)$fecha_comparar >= strtotime($fecha_inicio->format('Y-m-d'))) && ((int)$fecha_comparar <= (int)$fecha_fin));
}

 /**
  * Gets last semester id given a moodle id
  * 
  * @see get_id_last_semester($idmoodle)
  * @param $idmoodle --> moodle student id
  * @param $instance_id --> moodle course instance id
  * @return string|boolean --> string containing the last semster id or false in case there weren't semesters related with the student
  */
 function get_id_last_semester($idmoodle, $instance_id){

     $id_first_semester = get_id_first_semester($idmoodle, $instance_id);
     $semesters = get_semesters_stud($id_first_semester, $instance_id);
     if($semesters){
        return  $semesters[0]->id;
     }else{
         return false;
     }
 }

/**
 * Saves a track realized by a student on {tabla tp_seguimientos} and {tp_seguimiento_estud} tables 
 *
 * @see save_tracking_peer($object_tracking)
 * @param $object_tracking --> Object containing the information that'll be stored
 * @return object --> Object containing the query result
 */
function save_tracking_peer($object_tracking){

    global $DB;

    pg_query("BEGIN") or die("Falló la conexión con la base de datos\n");
    $result_saving = new stdClass();

    // track Insertion or update  in case there's an id track
    if($object_tracking->id != ""){

        unset($object_tracking->id_monitor);
        if($object_tracking->id == 0){
            trigger_error('ASES Notificacion: actualizar retiro en la BD con id 0');
        }else{
        $result = $DB->update_record('talentospilos_seguimiento', $object_tracking);
        $result_insertion_tracking = -1;  // This variable value indicates it wasn't an insertion but an update
        }
    }else{
        // Inserts track
        unset($object_tracking->id);
        $result_insertion_tracking = $DB->insert_record('talentospilos_seguimiento', $object_tracking, true);
    }    

    // Inserts ins student-track (seguimiento-estudiante) relation 
    if($result_insertion_tracking != -1){
        $object_tracking_student = new stdClass();
        $object_tracking_student->id_estudiante = $object_tracking->id_estudiante_ases;
        $object_tracking_student->id_seguimiento = $result_insertion_tracking;
        $result_insertion_student_tracking = $DB->insert_record('talentospilos_seg_estudiante',  $object_tracking_student, true);
    }else{
        $result_insertion_tracking = 1;
        $result_insertion_student_tracking = 1;
    }
    

    // Risks are consulted

    $sql_query = "SELECT id FROM {talentospilos_riesgos_ases} WHERE nombre = 'individual'";
    $id_individual_risk = $DB->get_record_sql($sql_query)->id;

    $sql_query = "SELECT id FROM {talentospilos_riesgos_ases} WHERE nombre = 'familiar'";
    $id_familiar_risk = $DB->get_record_sql($sql_query)->id;

    $sql_query = "SELECT id FROM {talentospilos_riesgos_ases} WHERE nombre = 'academico'";
    $id_academic_risk = $DB->get_record_sql($sql_query)->id;

    $sql_query = "SELECT id FROM {talentospilos_riesgos_ases} WHERE nombre = 'economico'";
    $id_economic_risk = $DB->get_record_sql($sql_query)->id;

    $sql_query = "SELECT id FROM {talentospilos_riesgos_ases} WHERE nombre = 'vida_universitaria'";
    $id_life_u_risk = $DB->get_record_sql($sql_query)->id;

    // {estudiante_riesgo - individual} relation id
    $sql_query = "SELECT id 
                  FROM {talentospilos_riesg_usuario} AS riesgo_usuario
                  WHERE riesgo_usuario.id_usuario = $object_tracking->id_estudiante_ases
                    AND riesgo_usuario.id_riesgo = $id_individual_risk";
    $id_ind_risk_student = $DB->get_record_sql($sql_query)->id;

    if($id_ind_risk_student){
        if($object_tracking->individual_riesgo != 0){
            $object_risk_individual = new stdClass();
            $object_risk_individual->id = $id_ind_risk_student;
            $object_risk_individual->id_usuario = $object_tracking->id_estudiante_ases;
            $object_risk_individual->id_riesgo = $id_individual_risk;
            $object_risk_individual->calificacion_riesgo = (int)$object_tracking->individual_riesgo;
            if($object_risk_individual->id == 0){
                trigger_error('ASES Notificacion: actualizar riesgo en la BD con id 0');
            }else{
                $DB->update_record('talentospilos_riesg_usuario', $object_risk_individual);
            }
        }
    }else{
        $object_risk_individual = new stdClass();
        $object_risk_individual->id_usuario = $object_tracking->id_estudiante_ases;
        $object_risk_individual->id_riesgo = $id_individual_risk;
        $object_risk_individual->calificacion_riesgo = 0;
        // Bandaid fix
        // id_usuario sometimes reaches as 0
        if($object_risk_individual->id_usuario != 0 && $object_risk_individual->id_usuario != null){
            $DB->insert_record('talentospilos_riesg_usuario', $object_risk_individual);
        }

    }
        

    // {estudiante_riesgo - familiar} relation id
    $sql_query = "SELECT id 
                  FROM {talentospilos_riesg_usuario} AS riesgo_usuario
                  WHERE riesgo_usuario.id_usuario = $object_tracking->id_estudiante_ases
                    AND riesgo_usuario.id_riesgo = $id_familiar_risk";
    $id_fam_risk_student = $DB->get_record_sql($sql_query)->id;

    if($id_fam_risk_student){
        if($object_tracking->familiar_riesgo != 0){
            $object_risk_familiar = new stdClass();
            $object_risk_familiar->id = $id_fam_risk_student;
            $object_risk_familiar->id_usuario = $object_tracking->id_estudiante_ases;
            $object_risk_familiar->id_riesgo = $id_familiar_risk;
            $object_risk_familiar->calificacion_riesgo = (int)$object_tracking->familiar_riesgo;
            if($object_risk_familiar->id == 0){
                trigger_error('ASES Notificacion: actualizar riesgo en la BD con id 0');
            }else{
                $DB->update_record('talentospilos_riesg_usuario', $object_risk_familiar);
            }
        }
    }else{
        $object_risk_familiar = new stdClass();
        $object_risk_familiar->id_usuario = $object_tracking->id_estudiante_ases;
        $object_risk_familiar->id_riesgo = $id_familiar_risk;
        $object_risk_familiar->calificacion_riesgo = 0;
        // Bandaid fix
        // id_usuario sometimes reaches as 0
        if($object_risk_familiar->id_usuario != 0 && $object_risk_familiar->id_usuario != null){
            $DB->insert_record('talentospilos_riesg_usuario', $object_risk_familiar);
        }

    }

    // {estudiante_riesgo - académico} relation id
    $sql_query = "SELECT id 
                  FROM {talentospilos_riesg_usuario} AS riesgo_usuario
                  WHERE riesgo_usuario.id_usuario = $object_tracking->id_estudiante_ases
                    AND riesgo_usuario.id_riesgo = $id_academic_risk";
    
    $id_acad_risk_student = $DB->get_record_sql($sql_query)->id;

    if($id_acad_risk_student){
        if($object_tracking->academico_riesgo != 0){
            $object_risk_academic = new stdClass();
            $object_risk_academic->id = $id_acad_risk_student;
            $object_risk_academic->id_usuario = $object_tracking->id_estudiante_ases;
            $object_risk_academic->id_riesgo = $id_academic_risk;
            $object_risk_academic->calificacion_riesgo = (int)$object_tracking->academico_riesgo;
            if($object_risk_academic->id == 0){
                trigger_error('ASES Notificacion: actualizar riesgo en la BD con id 0');
            }else{
                $DB->update_record('talentospilos_riesg_usuario', $object_risk_academic);
            }
        }
    }else{
        $object_risk_academic = new stdClass();
        $object_risk_academic->id_usuario = $object_tracking->id_estudiante_ases;
        $object_risk_academic->id_riesgo = $id_academic_risk;
        $object_risk_academic->calificacion_riesgo = 0;
        // Bandaid fix
        // id_usuario sometimes reaches as 0
        if($object_risk_academic->id_usuario != 0 && $object_risk_academic->id_usuario != null){
            $DB->insert_record('talentospilos_riesg_usuario', $object_risk_academic);
        }
    }

    // {estudiante_riesgo - económico} relation id
    $sql_query = "SELECT id 
                  FROM {talentospilos_riesg_usuario} AS riesgo_usuario
                  WHERE riesgo_usuario.id_usuario = $object_tracking->id_estudiante_ases
                    AND riesgo_usuario.id_riesgo = $id_economic_risk";
    
    $id_econ_risk_student = $DB->get_record_sql($sql_query)->id;

    if($id_econ_risk_student){
        if($object_tracking->economico_riesgo != 0){
            $object_risk_economic = new stdClass();
            $object_risk_economic->id = $id_econ_risk_student;
            $object_risk_economic->id_usuario = $object_tracking->id_estudiante_ases;
            $object_risk_economic->id_riesgo = $id_economic_risk;
            $object_risk_economic->calificacion_riesgo = (int)$object_tracking->economico_riesgo;
            if($object_risk_economic->id == 0){
                trigger_error('ASES Notificacion: actualizar riesgo en la BD con id 0');
            }else{
            $DB->update_record('talentospilos_riesg_usuario', $object_risk_economic);
            }
        }
    }else{
        $object_risk_economic = new stdClass();
        $object_risk_economic->id_usuario = $object_tracking->id_estudiante_ases;
        $object_risk_economic->id_riesgo = $id_economic_risk;
        $object_risk_economic->calificacion_riesgo = 0;
        if($object_risk_economic->id_usuario != 0 && $object_risk_economic->id_usuario != null){
            $DB->insert_record('talentospilos_riesg_usuario', $object_risk_economic);
        }

    }

    // {estudiante_riesgo vida universitaria} relation id
    $sql_query = "SELECT id 
                  FROM {talentospilos_riesg_usuario} AS riesgo_usuario
                  WHERE riesgo_usuario.id_usuario = $object_tracking->id_estudiante_ases
                    AND riesgo_usuario.id_riesgo = $id_life_u_risk";
    
    $id_life_risk_student = $DB->get_record_sql($sql_query)->id;

    if($id_life_risk_student){
        if($object_tracking->vida_uni_riesgo != 0){
            $object_risk_life = new stdClass();
            $object_risk_life->id = $id_life_risk_student;
            $object_risk_life->id_usuario = $object_tracking->id_estudiante_ases;
            $object_risk_life->id_riesgo = $id_life_u_risk;
            $object_risk_life->calificacion_riesgo = (int)$object_tracking->vida_uni_riesgo;
            if($object_risk_life->id == 0){
                trigger_error('ASES Notificacion: actualizar riesgo en la BD con id 0');
            }else{
            $DB->update_record('talentospilos_riesg_usuario', $object_risk_life);
            }
        }
    }else{
        $object_risk_life = new stdClass();
        $object_risk_life->id_usuario = $object_tracking->id_estudiante_ases;
        $object_risk_life->id_riesgo = $id_life_u_risk;
        $object_risk_life->calificacion_riesgo = 0;
        if($object_risk_life->id_usuario != 0 && $object_risk_life->id_usuario != null){
            $DB->insert_record('talentospilos_riesg_usuario', $object_risk_life);
        }
    }


    pg_query("COMMIT") or die("Falló la inserción en la base datos\n");

    $result_saving = new stdClass();

    if($result_insertion_tracking > 0 && $result_insertion_student_tracking > 0){
        $result_saving->title = "Éxito";
        $result_saving->msg = "El seguimiento ha sido almacenado correctamente";
        $result_saving->type = "success";
    }else{
        $result_saving->title = "Error";
        $result_saving->msg = "El seguimiento no ha sido almacenado";
        $result_saving->type = "error";
    }

    return $result_saving;
}

/** 
 * Executes a logical delete changing a track status on {talentospilos_seguimiento} table
 *
 * @see delete_tracking_peer($id_tracking)
 * @param $id_tracking --> object containing track information
 * @return object --> object representing the database operation result
 */

function delete_tracking_peer($id_tracking){

    global $DB;

    $object_updatable = new stdClass();
    $msg_result = new stdClass();

    $object_updatable->id = $id_tracking;
    $object_updatable->status = 0;

    if($object_updatable->id == 0){
        trigger_error('ASES Notificacion: actualizar riesgo en la BD con id 0');
        $result_query = false;
    }else{
    $result_query = $DB->update_record('talentospilos_seguimiento', $object_updatable);
    }

    if($result_query){
        $msg_result->title = "Éxito";
        $msg_result->msg = "El seguimiento ha sido borrado con éxito.";
        $msg_result->type = "success";
    }else{
        $msg_result->title = "Error";
        $msg_result->msg = "Ha ocurrido un error al conectarse con la base de datos.";
        $msg_result->type = "error";
    }

    return $msg_result;

}

/**
 * Saves the Icetex change of status of a student
 *
 * @see save_status_icetex($id_status, $id_student, $id_reason=null, $observations=null)
 * @param $id_student --> ASES student id
 * @param $id_status --> status id that'll be saved
 * @param $id_reason --> In case the status is retired, this is de id reason. null by default
 * @param $observations --> Observation of change
 * @return object --> object representing the database operation result
 */

function save_status_icetex($id_status, $id_student, $id_reason=null, $observations=null){

    global $DB;

    $msg_result = new stdClass();
    $object_status = new stdClass();

    date_default_timezone_set('America/Bogota');

    $today_timestamp = time();

    $object_status->fecha = $today_timestamp;
    $object_status->id_estado_icetex = $id_status;
    $object_status->id_estudiante = $id_student;

    if($id_reason){
        $object_status->id_motivo_retiro = $id_reason;
    }

    if($observations){
        $sql_query = "SELECT observacion FROM {talentospilos_usuario} WHERE id = $id_student";
        $user_observations = $DB->get_record_sql($sql_query)->observacion;

        $user_observations = date('d-m-y', $today_timestamp).": Mótivo de retiro Icetex:  $observations"."\n".$user_observations;

        $object_updatable = new stdClass();
        $object_updatable->id = $id_student;
        $object_updatable->observacion = $user_observations;
        if($object_updatable->id == 0){
            trigger_error('ASES Notificacion: actualizar observaciones usuario en la BD con id 0');
        }else{
            $DB->update_record('talentospilos_usuario', $object_updatable);
        }
    }

    $result_insertion = $DB->insert_record('talentospilos_est_est_icetex', $object_status);

    if($result_insertion){

        $msg_result->title = "Éxito";
        $msg_result->msg = "El estado ha sido cambiado con éxito";
        $msg_result->type = "success";

    }else{

        $msg_result->title = "Error";
        $msg_result->msg = "Error al almacenar el seguimiento en la base de datos";
        $msg_result->type = "error";
    }

    return $msg_result;
}

/**
 * Gets an student given his ASES id
 *
 * @see validate_student($code_student)
 * @param $code_student --> ASES student id
 * @return integer --> 1 if it's successful, 0 otherwise
 */

function validate_student($code_student){

    global $DB;

    $sql_query = "SELECT id FROM {user} WHERE username = '".$code_student."'";
    $result_moodle_database = $DB->get_record_sql($sql_query);

    if($result_moodle_database){
        return "1";
    }else{
        return "0";
    }
}

/**
 * Gets name of student by username
 *
 * @see get_name_by_username($username)
 * @param $username --> code of student without program
 * @return object representing the user
 */

function get_name_by_username($username){
    global $DB;

    $sql_query = "SELECT * FROM {user} WHERE username LIKE '$username%'";
    $user = $DB->get_record_sql($sql_query);

    return $user;
}

/**
 * Retorna el conjunto de estados para ser puestos en la ficha general del estudiante
 *
 * @see get_academic_program_statuses($username)
 * @return object array with academic program statuses
 */
function get_status_program_for_profile($id_ases_user){

    global $DB;

    $sql_query = "SELECT user_extended.id_moodle_user, 
                         academic_program.id AS academic_program_id, 
                         academic_program.cod_univalle, 
                         academic_program.nombre AS nombre_programa, 
                         academic_program.jornada, 
                         faculty.nombre AS nombre_facultad,
                         user_extended.program_status, 
                         user_extended.tracking_status
                  FROM {talentospilos_user_extended} AS user_extended
                       INNER JOIN {talentospilos_programa} AS academic_program ON user_extended.id_academic_program = academic_program.id
                       INNER JOIN {talentospilos_facultad} AS faculty ON academic_program.id_facultad = faculty.id
                  WHERE id_ases_user = $id_ases_user";
    
    $academic_program_student = $DB->get_records_sql($sql_query);

    $sql_query = "SELECT *
                  FROM {talentospilos_estad_programa}";
    
    $academic_program_statuses = $DB->get_records_sql($sql_query);

    $array_result = array();

    foreach($academic_program_student as $academic_program){

        $array_statuses = array();

        foreach($academic_program_statuses as $status){
            
            if($status->id == $academic_program->program_status){
                $status->selected = 'selected';
            }else{
                unset($status->selected);
            }
            array_push($array_statuses, $status);
        }

        $academic_program->statuses = $array_statuses; 

        array_push($array_result, $academic_program);
    }

    return $array_result;
}


/**
 * Retorna el conjunto de estados para ser puestos en la ficha general del estudiante
 *
 * @see get_status_program_for_profile_aditional($username)
 * @return object array with academic program statuses
 */
function get_status_program_for_profile_aditional($id_ases_user){

    global $DB;

    $sql_query = "SELECT user_extended.id_moodle_user, 
                         academic_program.id AS academic_program_id, 
                         academic_program.cod_univalle, 
                         academic_program.nombre AS nombre_programa, 
                         academic_program.jornada, 
                         sede.nombre AS nombre_sede,
                         faculty.nombre AS nombre_facultad,
                         user_extended.program_status, 
                         user_extended.tracking_status
                  FROM {talentospilos_user_extended} AS user_extended
                       INNER JOIN {talentospilos_programa} AS academic_program ON user_extended.id_academic_program = academic_program.id
                       INNER JOIN {talentospilos_facultad} AS faculty ON academic_program.id_facultad = faculty.id
                       INNER JOIN {talentospilos_sede}     AS sede    ON academic_program.id_sede = sede.id     
                  WHERE id_ases_user = $id_ases_user";
    
    $academic_program_student = $DB->get_records_sql($sql_query);

    $sql_query = "SELECT *
                  FROM {talentospilos_estad_programa}";
    
    $academic_program_statuses = $DB->get_records_sql($sql_query);

    $array_result = array();

    foreach($academic_program_student as $academic_program){

        $array_statuses = array();

        foreach($academic_program_statuses as $status){
            
            if($status->id == $academic_program->program_status){
            
                $status->selected = 'selected';
            }else{
                unset($status->selected);
            }
            array_push($array_statuses, $status);
        }

        $academic_program->statuses = $array_statuses; 

        array_push($array_result, $academic_program);
    }

    return $array_result;
}

/**
 * Get the student codes
 *
 * @see get_student_codes()
 * @param $document
 * @return object array with student codes.
 */
function get_student_codes($document){

    global $DB;

    $sql_query = "SELECT username AS code
                  FROM {user} AS U
                  INNER JOIN {talentospilos_user_extended} AS UE on U.id = UE.id_moodle_user
                  INNER JOIN {talentospilos_usuario} AS usuario on UE.id_ases_user = usuario.id
                  WHERE usuario.num_doc = '$document'";
    
    $student_codes = $DB->get_records_sql($sql_query);

    return $student_codes;
 }
/**
 * Retorna el conjunto de posibles tipos de documento de identidad
 *
 * @see get_document_types()
 * @return object array with document types
 */
function get_document_types(){
    global $DB;
    $sql_query = "SELECT *
    FROM {talentospilos_tipo_documento}";
    return $DB->get_records_sql($sql_query);
}

/**
 * Retorna el conjunto de posibles tipos de documento de identidad para un estudiante en particular
 * marcando cual figura en su registro en la tabla talentospilos_usuario asociado al campo num_doc
 *
 * @see get_document_types_for_profile($username)
 * @return object array with academic program statuses
 */
function get_document_types_for_profile($id_ases_user){
    
    global $DB;

    $array_result = array();

    $sql_query = "SELECT *
                  FROM {talentospilos_tipo_documento}";

    $result_types = $DB->get_records_sql($sql_query);

    $sql_query = "SELECT tipo_doc
                  FROM {talentospilos_usuario}
                  WHERE id = $id_ases_user";
    
    $result_doc_user = $DB->get_record_sql($sql_query);

    foreach($result_types as &$type){
        if($type->id == $result_doc_user->tipo_doc){
            $type->selected = "selected";
        }else{
            $type->selected = "";
        }
        
        array_push($array_result, $type);
    }

    return $array_result;
}


/**
 * Update the user profile image 
 * @param $newUser [id, newfile]
 * @return $
 */

/**
 * Actualiza el tracking status * 
 *
 * @see get_document_types_for_profile($username)
 * @return object array with academic program statuses
 */
function update_tracking_status($id_ases_user, $id_academic_program){

    global $DB;

    $sql_query = "SELECT id
                  FROM {talentospilos_user_extended}
                  WHERE id_ases_user = $id_ases_user AND id_academic_program = $id_academic_program";

    $id_reg_user_extended = $DB->get_record_sql($sql_query)->id;

    $sql_query = "SELECT id
                  FROM {talentospilos_user_extended}
                  WHERE id_ases_user = $id_ases_user";

    $array_reg_user_extended = $DB->get_records_sql($sql_query);

    $record = new stdClass();

    foreach($array_reg_user_extended as $reg){
        $record->id = $reg->id;
        $record->tracking_status = 0;
        if($record->id == 0){
            trigger_error('ASES Notificacion: actualizar usuario en la BD con id 0');
        }else{
        $DB->update_record('talentospilos_user_extended', $record);
        }
    }

    $record->id = $id_reg_user_extended;
    $record->tracking_status = 1;
    if($record->id == 0){
        trigger_error('ASES Notificacion: actualizar usuario en la BD con id 0');
        $result = false;
    }else{
    $result = $DB->update_record('talentospilos_user_extended', $record);
    }

    return $result;
}


/**
 * Insert filed on {talentospilos_usuario} table
 * 
 * 
 * @see save_data($data)
 * @param $data --> Array containing the fields to Insert
 * @return object in a json format
 */
function save_data($data, $deportes, $f, $programa, $id_moodle){
    global $DB;

    $student_object = new stdClass();
    $student_extended = new stdClass();
    for($i = 0; $i < count($data); $i++){

        $name = $data[$i]->name;
        $value = $data[$i]->value;

        $student_object->$name = $value;

    }
  
    
    
    $student_object->actividades_ocio_deporte = $deportes;
    $student_object->vive_con = $f;
    $student_object->grupo = 1;
    $student_object->estado = "ACTIVO";
    $student_object->estado_ases = "ACTIVO";

    $res = $DB->insert_record('talentospilos_usuario', $student_object);

    $student_extended->id_moodle_user = $id_moodle;
    $student_extended->id_ases_user = $res;
    $student_extended->id_academic_program = $programa;
    $student_extended->tracking_status = 1;
    $student_extended->program_status = 1;

    $result = $DB->insert_record('talentospilos_user_extended', $student_extended);
   
        
    return $result;
}

/**
 * Insert missing fields of step2 form on {talentospilos_usuario} table
 *
 *
 * @see save_profile($id_ases, $estrato, $hijos)
 * @param $id_ases --> id del usuario a insertar los datos
 * @param $estrato --> estrato del hogar 
 * @param $hijos --> numero de hijos
 * @param $vive_con --> Json containing the information of
 *                      the people that lives with the student
 * @return object in a json format
 */

function save_mdl_user($username, $nombre, $apellido, $emailI, $pass){

    global $DB;
    $mdl_user = new stdClass();
    $mdl_user->auth = 'manual';
    $mdl_user->confirmed = 1;
    $mdl_user->mnethostid = 1;

    $mdl_user->username = $username;
    $mdl_user->firstname = $nombre;
    $mdl_user->lastname = $apellido;
    $mdl_user->email = $emailI;
    $mdl_user->password = password_hash($pass, PASSWORD_DEFAULT);

    $result = $DB->insert_record('user', $mdl_user);

    return $result;
}


/**
 * Insert missing fields of step2 form on {talentospilos_usuario} table
 *
 *
 * @see save_profile($id_ases, $estrato, $hijos)
 * @param $id_ases --> id del usuario a insertar los datos
 * @param $estrato --> estrato del hogar 
 * @param $hijos --> numero de hijos
 * @param $vive_con --> Json containing the information of
 *                      the people that lives with the student
 * @return object in a json format
 */

function save_data_user_step2($id_ases,$estrato, $hijos){

    global $DB;
    $student_ases = new stdClass();
    $student_ases->id = $id_ases;
    $student_ases->hijos = $hijos;
    $student_ases->estrato = $estrato;

    $result = $DB->update_record('talentospilos_usuario', $student_ases);

    return $result;
}

/**
 * Updates every field on {talentospilos_usuario} table
 *
 *
 * @see save_profile($form)
 * @param $form --> Array containing the fields to update
 * @param $option1 --> string
 * @param $option2 --> string
 * @param $live_with --> Json containing the information of
 *                      the people that lives with the student
 * @return object in a json format
 */
function save_profile($form, $option1, $option2, $live_with){

    global $DB;

    try{
        $id_ases = $form[0]->value;
        $msg = new stdClass();

        //Info to update will be added here
        $obj_updatable = array();

        // Required fields are inserted
        for($i = 0; $i < count($form); $i++){

            $name = $form[$i]->name;
            $value = $form[$i]->value;

            switch($name){
                case 'tipo_doc':
                case 'tipo_doc_ini':
                    $sql_query = "SELECT id FROM {talentospilos_tipo_documento} WHERE nombre = '".$value."'";
                    $id_doc_type = $DB->get_record_sql($sql_query)->id;
                    $obj_updatable[$name] = $id_doc_type;
                    break;

                case 'pais':
                    $obj_updatable[$name] = $value;
                    $pais = $value;
                    break;

                case 'genero':
                    $obj_updatable[$name] = $value;
                    $genero = $value;
                    break;

                case 'etnia':
                    $obj_updatable[$name] = $value;
                    $etnia = $value;
                    break;

                case 'otro_genero':
                    $obj_updatable[$name] = $value;
                    $otro = $value;
                    break;

                case 'estado_civil':
                    $obj_updatable[$name] = $value;
                    $estado_civil = $value;
                    break;

                case 'ingreso':
                    $obj_updatable[$name] = $value;
                    $anio_ingreso = $value;
                    break;

                case 'puntaje_icfes':
                    $obj_updatable[$name] = $value;
                    $puntaje_icfes = $value;
                    break;

                case 'estrato':
                    $obj_updatable[$name] = $value;
                    $estrato = $value;
                    break;

                case 'act_simultanea':
                    $obj_updatable[$name] = $value;
                    $act_sim = $value;
                    break;

                case 'otro_act_simultanea':
                    $obj_updatable[$name] = $value;
                    $otro_act_sim = $value;
                    break;

                case 'email':
                    $obj_updatable[$name] = $value;
                    $email = $value;
                    break;

                case 'sexo':
                    $obj_updatable[$name] = $value;
                    $sexo = $value;
                    break;

                case 'fecha_nac':
                    $obj_updatable[$name] = $value;
                    $fecha_nac = $value;
                    break;

                default:
                    $obj_updatable[$name] = $value;
                    break;
            }
        }

        $obj_updatable = (object) $obj_updatable;
        //an id is assigned to update
        $obj_updatable->id = $id_ases;

        $sql_query = "SELECT observacion, json_detalle FROM {talentospilos_usuario} WHERE id = $id_ases";

        $result = $DB->get_record_sql($sql_query);
        $observations = $result->observacion;
        $json_detalle = json_decode($result->json_detalle);

        //Agregar campos nuevos

        if($act_sim == 0){

            if($option2 == ""){
                //Agregar otra actividad a la base de datos de act_simultanea (si no existe), y guardar en usuario
                add_record_act($otro_act_sim);
            } else {
                //Actualizar actividad en base de datos y guardar en usuario
                $id_otro_act = get_id_act($option2);
                $act = new stdClass();
                $act->id = $id_otro_act;
                $act->actividad = $otro_act_sim;
                $act->opcion_general = 0;
                update_record_act($act);
            }
            $id_otro_act= get_id_act($otro_act_sim);

            $obj_updatable->id_act_simultanea = $id_otro_act;
        } else {
            //Guardar con género existente en opciones generales
            $obj_updatable->id_act_simultanea = $act_sim;
        }

        if($genero == 0){
            if($option1 == ""){
                //Agregar otro género a la base de datos de generos (si no existe), y guardar en usuario
                add_record_genero($otro);
            } else {
                //Actualizar género en base de datos y guardar en usuario
                $id_otro_genero = get_id_genero($option1);
                $gen = new stdClass();
                $gen->id = $id_otro_genero;
                $gen->genero = $otro;
                $gen->opcion_general = 0;
                update_record_genero($gen);
            }
            $id_otro_genero = get_id_genero($otro);

            $obj_updatable->id_identidad_gen = $id_otro_genero;
        } else {
            //Guardar con género existente en opciones generales
            $obj_updatable->id_identidad_gen = $genero;
        }

        $json_detalle->tratamiento_datos_personales_doc = $obj_updatable->link_doc_dtddp;
        $obj_updatable->json_detalle = json_encode($json_detalle);

        $obj_updatable->vive_con = $live_with;
        $obj_updatable->id_estado_civil = $estado_civil;
        $obj_updatable->id_pais = $pais;
        $obj_updatable->id_etnia = $etnia;
        $obj_updatable->estrato = $estrato;
        $obj_updatable->anio_ingreso = $anio_ingreso;
        $obj_updatable->puntaje_icfes = $puntaje_icfes;
        $obj_updatable->sexo = $sexo;
        $obj_updatable->fecha_nac = $fecha_nac;

        //____________________________________________
        $conc_observations = $obj_updatable->observacion."\n".$observations;

        $obj_updatable->observacion = $conc_observations;

        //----------------------------------------------
        //Data object to update record in mdl_user
        //----------------------------------------------

        $obj_updatable_moodle = new stdClass();
        $obj_updatable_moodle->id = get_moodle_id($id_ases);

        //Test: email validation
        //$email = "insert email test";

        $obj_updatable_moodle->email = $email;

        if($obj_updatable->id == 0){
            trigger_error('ASES Notificacion: actualizar usuario en la BD con id 0');
            $result= false;
        }else{
            $result = $DB->update_record('talentospilos_usuario', $obj_updatable);
        }

        $result_cv_update = studentprofile_update_email_moodle($obj_updatable_moodle);

        if($result && $result_cv_update){
            $msg->title = "Éxito";
            $msg->status = "success";
            $msg->msg = "Se ha actualizado toda la información.";
        }
        else{
            $msg->title = "Error";
            $msg->status = "error";
            $msg->msg = "Error al guardar la información en el servidor.";
        }

        return $msg;

    }catch(Exception $e){

        $msg->title = $e->getMessage();
        $msg->status = "error";
        $msg->msg = "Error al guardar la información. 
                        Contacte al componente de sistemas.";

        return $msg;
    }
}

/**
 * @desc Process a given tracking and pushed it into the array that follows
 *       the pointer.
 * @param $array_of_trackings array -> Pointer to the array on which will
 *                                     be stored the tracking
 * @param $tracking array -> tracking to push
 * @return void
 */
function student_profile_process_tracking(&$array_of_trackings, $tracking) {

    $special_date_interval = [
        'start' => strtotime( "2019-01-01" ),
        'end' => strtotime( "2019-04-30" )
    ];

    $_fecha = null;
    if( array_key_exists("fecha", $tracking) ) {
        $_fecha = strtotime( $tracking['fecha'] );
    }else{
        $_fecha = strtotime( $tracking['in_fecha'] );
    }

    if( ( $_fecha >= $special_date_interval['start'] ) && ( $_fecha <= $special_date_interval['end'] ) ){
        $tracking['custom_extra']['special_tracking'] = true;
    }

    $tracking['custom_extra'][$tracking['alias_form']] = true;
    $tracking['custom_extra']['rev_pro'] = false;
    $tracking['custom_extra']['rev_pract'] = false;

    if( array_key_exists("revisado_profesional", $tracking) ) {
        if( $tracking['revisado_profesional'] === "0" ){
            $tracking['custom_extra']['rev_pro'] = true;
        }
    }
    if( array_key_exists("revisado_practicante", $tracking) ) {
        if( $tracking['revisado_practicante'] === "0" ){
            $tracking['custom_extra']['rev_pract'] = true;
        }
    }
    if( array_key_exists("in_revisado_profesional", $tracking) ) {
        if( $tracking['in_revisado_profesional'] === "0" ){
            $tracking['custom_extra']['rev_pro'] = true;
        }
    }
    if( array_key_exists("in_revisado_practicante", $tracking) ) {
        if( $tracking['in_revisado_practicante'] === "0" ){
            $tracking['custom_extra']['rev_pract'] = true;
        }
    }

    array_push( $array_of_trackings, $tracking );
}

/**
 * @see get_tracking_current_semesterV4, student_profile_process_tracking
 * @desc Constructs the peer tracking of an student.
 *          This is the latest version.
 * @param $id_ases string -> ASES student id
 * @param $instance_id int -> ID de la instancia
 * @return array
 */
function student_profile_get_peer_tracking($id_ases, $instance_id){

    $peer_tracking_v3 = [];
    $trackings_out_of_range = [];

    $periods = core_periods_get_all_periods($instance_id);
    $number_of_periods = count($periods);
    $id_period = $number_of_periods;

    $trackings = get_tracking_current_semesterV4($id_ases);
    $number_of_trackings = count($trackings);
    $id_tracking = 0;

    for(;$id_period>0;$id_period--) {

        $period = $periods[$id_period];

        if( ($number_of_trackings - $id_tracking) > 0 ) {

            $fecha_inicio_periodo = strtotime($period->fecha_inicio);
            $fecha_fin_periodo = strtotime($period->fecha_fin);

            if( $trackings[$id_tracking]['fecha_timestamp'] >= $fecha_inicio_periodo &&
                $trackings[$id_tracking]['fecha_timestamp'] <= $fecha_fin_periodo ) {

                $peer_tracking['period_name'] = $period->nombre;
                $tracking_modified = [];

                for(;$id_tracking<$number_of_trackings;$id_tracking++) {

                    $tracking = $trackings[$id_tracking];
                    $timestamp_tracking = $trackings[$id_tracking]['fecha_timestamp'];

                    if($timestamp_tracking < $fecha_inicio_periodo || $timestamp_tracking > $fecha_fin_periodo ) {
                        break;
                    }
                    student_profile_process_tracking( $tracking_modified, $tracking );
                }
                $peer_tracking['trackings'] = $tracking_modified;
                array_push( $peer_tracking_v3, $peer_tracking );
            } elseif($id_period == 1 || $trackings[$id_tracking]['fecha_timestamp'] > strtotime($periods[$id_period-1]->fecha_fin)) {

                //student_profile_process_tracking( $trackings_out_of_range, $trackings[$id_tracking] );
                $id_tracking++;
                $id_period++;
            }
        }
    }

    if(count($trackings_out_of_range) > 0) {
        $peer_tracking = [];
        $peer_tracking['period_name'] = 'Seguimientos fuera de rango';
        $peer_tracking['trackings'] = $trackings_out_of_range;
        array_push( $peer_tracking_v3, $peer_tracking );
    }
    return $peer_tracking_v3;
}

/**
 * @see get_html_tracking_peer($id_ases)
 * @desc Constructs the peer tracking by the old form
 * @param $id_ases string -> ASES student id
 * @param $id_block string -> Block id
 * @return string
 */
function student_profile_get_html_peer_tracking($id_ases, $id_block){

    $enum_risk = array();
    array_push($enum_risk, "");
    array_push($enum_risk, "Bajo");
    array_push($enum_risk, "Medio");
    array_push($enum_risk, "Alto");


    $html_tracking_peer = "";
    $array_peer_trackings = get_tracking_group_by_semester($id_ases, 'PARES', null, $id_block);

    if ($array_peer_trackings != null) {

        $panel = "<div class='panel-group' id='accordion_semesters'>";

        foreach ($array_peer_trackings->semesters_segumientos as $key_semester => $array_semester) {

            if (strpos($array_semester->name_semester, '2018') !== false) {
                continue;
            }

            $panel .= "<div class='panel panel-default'>";
            $panel .= "<a data-toggle='collapse' class='collapsed' data-parent='#accordion_semesters' style='text-decoration:none' href='#semester" . $array_semester->id_semester . "'>";
            $panel .= "<div class='panel-heading heading_semester_tracking'>";
            $panel .= "<h4 class='panel-title'>";
            $panel .= "$array_semester->name_semester";
            $panel .= "<span class='glyphicon glyphicon-chevron-left'></span>";
            $panel .= "</h4>"; //End panel-title
            $panel .= "</div>"; //End panel-heading
            $panel .= "</a>";

            $panel .= "<div id='semester$array_semester->id_semester' class='panel-collapse collapse'>";
            $panel .= "<div class='panel-body'>";

            // $panel .= "<div class=\"container well col-md-12\">";
            // $panel .= "<div class=\"container-fluid col-md-10\" name=\"info\">";
            // $panel .= "<div class=\"row\">";

            $panel .= "<div class='panel-group' id='accordion_trackings_semester'>";

            foreach ($array_semester->result as $tracking) {

                $monitor_object = get_moodle_user($tracking->id_monitor);

                // Date format (Formato de fecha)
                $date = date_parse_from_format('d-m-Y', $tracking->fecha);
                $months = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

                $panel .= "<div class='panel panel-default'>";
                $panel .= "<div class='panel-heading'>";
                $panel .= "<h4 class='panel-title'>";

                $panel .= "<a data-toggle='collapse' data-parent='#accordion_trackings_semester' href='#" . $tracking->id_seg . "'>";
                $panel .= " Registro " . $months[(int) $date["month"] - 1] . "-" . $date["day"] . "-" . $date["year"] . "</a>";

                $panel .= "</h4>"; // h4 div panel-title
                $panel .= "</div>"; // End div panel-heading

                $panel .= "<div id='$tracking->id_seg' class='panel-collapse collapse'>";
                $panel .= "<div class='panel-body'>";

                // Date, Place, time  (Fecha, lugar, hora)
                $panel .= "<div class='panel panel-default'>";
                $panel .= "<div class='panel-body'>";

                $panel .= "<div class='col-sm-3'>";
                $panel .= "<b>Fecha:</b>";
                $panel .= "</div>";
                $panel .= "<div class='col-sm-6'>";
                $panel .= "<b>Lugar:</b>";
                $panel .= "</div>";
                $panel .= "<div class='col-sm-3'>";
                $panel .= "<b>Hora:</b>";
                $panel .= "</div>";

                $panel .= "<div class='col-sm-3'>";
                $panel .= "<span class='date_tracking_peer'>" . $date["month"] . "-" . $date["day"] . "-" . $date["year"] . "</span>";
                $panel .= "</div>";
                $panel .= "<div class='col-sm-6'>";
                $panel .= "<span class='place_tracking_peer'>" . $tracking->lugar . "</span>";
                $panel .= "</div>";
                $panel .= "<div class='col-sm-3'>";
                $panel .= "<span class='init_time_tracking_peer'>" . $tracking->hora_ini . "</span> - <span class='ending_time_tracking_peer'>" . $tracking->hora_fin . "</span>";
                $panel .= "</div>";

                $panel .= "</div>"; // End panel-body
                $panel .= "</div>"; // End div panel panel-default

                // Created by (Creado por)

                $panel .= "<div class='panel panel-default'>";
                $panel .= "<div class='panel-body'>";

                $panel .= "<div class='col-sm-12'>";
                $panel .= "<b>Creado por: </b>";
                $panel .= $monitor_object->firstname . " " . $monitor_object->lastname;
                $panel .= "</div>";

                $panel .= "</div>"; // End panel-body
                $panel .= "</div>"; // End div panel panel-default

                // Subject (Tema)
                $panel .= "<div class='panel panel-default'>";
                $panel .= "<div class='panel-body'>";

                $panel .= "<div class='col-sm-12'>";
                $panel .= "<b>Tema:</b>";
                $panel .= "</div>";

                $panel .= "<div class='col-sm-12'>";
                $panel .= "<span class='topic_tracking_peer'>" . $tracking->tema . "</span>";
                $panel .= "</div>";

                $panel .= "</div>"; // End panel-body
                $panel .= "</div>"; // End div panel panel-default

                // Objectives (Objetivos)
                $panel .= "<div class='panel panel-default'>";
                $panel .= "<div class='panel-body'>";

                $panel .= "<div class='col-sm-12'>";
                $panel .= "<b>Objetivos:</b>";
                $panel .= "</div>";

                $panel .= "<div class='col-sm-12'>";
                $panel .= "<span class='objectives_tracking_peer'>" . $tracking->objetivos . "</span>";
                $panel .= "</div>";

                $panel .= "</div>"; // End panel-body
                $panel .= "</div>"; // End div panel panel-default

                if ($tracking->individual != "") {

                    if ($tracking->individual_riesgo == '1') {
                        $panel .= "<div class='panel panel-default riesgo_bajo'>";
                    } else if ($tracking->individual_riesgo == '2') {
                        $panel .= "<div class='panel panel-default riesgo_medio'>";
                    } else if ($tracking->individual_riesgo == '3') {
                        $panel .= "<div class='panel panel-default riesgo_alto'>";
                    } else {
                        $panel .= "<div class='panel panel-default'>";
                    }

                    $panel .= "<div class='panel-body'>";
                    $panel .= "<div class='col-sm-12'>";
                    $panel .= "<b>Individual:</b><br>";
                    $panel .= "<span class='individual_tracking_peer'>$tracking->individual</span><br><br>";
                    $panel .= "<b>Riesgo individual: </b>";
                    $panel .= "<span class='ind_risk_tracking_peer'>" . $enum_risk[(int) $tracking->individual_riesgo] . "</span><br><br>";
                    $panel .= "</div>"; // End div col-sm-12
                    $panel .= "</div>"; // End panel-body
                    $panel .= "</div>"; // End div panel panel-default
                }

                if ($tracking->familiar_desc != "") {

                    if ($tracking->familiar_riesgo == '1') {
                        $panel .= "<div class='panel panel-default riesgo_bajo'>";
                    } else if ($tracking->familiar_riesgo == '2') {
                        $panel .= "<div class='panel panel-default riesgo_medio'>";
                    } else if ($tracking->familiar_riesgo == '3') {
                        $panel .= "<div class='panel panel-default riesgo_alto'>";
                    } else {
                        $panel .= "<div class='panel panel-default'>";
                    }

                    $panel .= "<div class='panel-body'>";
                    $panel .= "<div class='col-sm-12'>";
                    $panel .= "<b>Familiar:</b><br>";
                    $panel .= "<span class='familiar_tracking_peer'>$tracking->familiar_desc</span><br><br>";
                    $panel .= "<b>Riesgo familiar: </b>";
                    $panel .= "<span class='fam_risk_tracking_peer'>" . $enum_risk[(int) $tracking->familiar_riesgo] . "</span><br><br>";
                    $panel .= "</div>"; // End div col-sm-12
                    $panel .= "</div>"; // End panel-body
                    $panel .= "</div>"; // End div panel panel-default
                }

                if ($tracking->academico != "") {

                    if ($tracking->academico_riesgo == '1') {
                        $panel .= "<div class='panel panel-default riesgo_bajo'>";
                    } else if ($tracking->academico_riesgo == '2') {
                        $panel .= "<div class='panel panel-default riesgo_medio'>";
                    } else if ($tracking->academico_riesgo == '3') {
                        $panel .= "<div class='panel panel-default riesgo_alto'>";
                    } else {
                        $panel .= "<div class='panel panel-default'>";
                    }

                    $panel .= "<div class='panel-body'>";
                    $panel .= "<div class='col-sm-12'>";
                    $panel .= "<b>Académico:</b><br>";
                    $panel .= "<span class='academico_tracking_peer'>$tracking->academico</span><br><br>";
                    $panel .= "<b>Riesgo académico: </b>";
                    $panel .= "<span class='aca_risk_tracking_peer'>" . $enum_risk[(int) $tracking->academico_riesgo] . "</span><br><br>";
                    $panel .= "</div>"; // End div col-sm-12
                    $panel .= "</div>"; // End panel-body
                    $panel .= "</div>"; // End div panel panel-default
                }

                if ($tracking->economico != "") {

                    if ($tracking->economico_riesgo == '1') {
                        $panel .= "<div class='panel panel-default riesgo_bajo'>";
                    } else if ($tracking->economico_riesgo == '2') {
                        $panel .= "<div class='panel panel-default riesgo_medio'>";
                    } else if ($tracking->economico_riesgo == '3') {
                        $panel .= "<div class='panel panel-default riesgo_alto'>";
                    } else {
                        $panel .= "<div class='panel panel-default'>";
                    }

                    $panel .= "<div class='panel-body'>";
                    $panel .= "<div class='col-sm-12'>";
                    $panel .= "<b>Económico:</b><br>";
                    $panel .= "<span class='economico_tracking_peer'>$tracking->economico</span><br><br>";
                    $panel .= "<b>Riesgo económico: </b>";
                    $panel .= "<span class='econ_risk_tracking_peer'>" . $enum_risk[(int) $tracking->economico_riesgo] . "</span><br><br>";
                    $panel .= "</div>"; // End div col-sm-12
                    $panel .= "</div>"; // End panel-body
                    $panel .= "</div>"; // End div panel panel-default
                }

                if ($tracking->vida_uni != "") {

                    if ($tracking->vida_uni_riesgo == '1') {
                        $panel .= "<div class='panel panel-default riesgo_bajo'>";
                    } else if ($tracking->vida_uni_riesgo == '2') {
                        $panel .= "<div class='panel panel-default riesgo_medio'>";
                    } else if ($tracking->vida_uni_riesgo == '3') {
                        $panel .= "<div class='panel panel-default riesgo_alto'>";
                    } else {
                        $panel .= "<div class='panel panel-default'>";
                    }

                    $panel .= "<div class='panel-body'>";
                    $panel .= "<div class='col-sm-12'>";
                    $panel .= "<b>Vida universitaria:</b><br>";
                    $panel .= "<span class='lifeu_tracking_peer'>$tracking->vida_uni</span><br><br>";
                    $panel .= "<b>Riesgo vida universitaria: </b>";
                    $panel .= "<span class='lifeu_risk_tracking_peer'>" . $enum_risk[(int) $tracking->vida_uni_riesgo] . "</span><br><br>";
                    $panel .= "</div>"; // End div col-sm-12
                    $panel .= "</div>"; // End panel-body
                    $panel .= "</div>"; // End div panel panel-default
                }

                // Observations (observaciones)
                $panel .= "<div class='panel panel-default'>";
                $panel .= "<div class='panel-body'>";

                $panel .= "<div class='col-sm-12'>";
                $panel .= "<b>Observaciones:</b>";
                $panel .= "</div>";

                $panel .= "<div class='col-sm-12'>";
                $panel .= "<span class='observations_tracking_peer'>" . $tracking->observaciones . "</span>";
                $panel .= "</div>";

                $panel .= "</div>"; // End panel-body
                $panel .= "</div>"; // End div panel panel-default

                // Edit and delete buttons
                $panel .= "<div class='row'>";
                $panel .= "<div class='col-sm-4 row-buttons-tracking'>";
                $panel .= "<button type='button' class='btn-primary edit_peer_tracking' id='edit_tracking_" . $tracking->id_seg . "'>Editar seguimiento</button>";
                $panel .= "</div>";
                $panel .= "<div class='col-sm-3 col-sm-offset-5 row-buttons-tracking'>";
                $panel .= "<button type='button' class='btn-danger delete_peer_tracking col-sm-10' id='delete_tracking_peer_" . $tracking->id_seg . "'>";
                $panel .= "Borrar <span class='glyphicon glyphicon-trash'></span>";
                $panel .= "</button>";
                $panel .= "</div>";
                $panel .= "</div>";

                $panel .= "</div>"; // End panel-body tracking
                $panel .= "</div>"; // End div panel-collapse tracking
                $panel .= "</div>"; // End div panel-default
            }

            $panel .= "</div>"; // End panel accordion_trackings_semester

            $panel .= "</div>"; // End panel-body
            $panel .= "</div>"; // End panel-collapse

            $panel .= "</div>"; //End panel panel-default
        }

        $panel .= "</div>"; //End panel group accordion_semesters

        $html_tracking_peer .= $panel;

    } else {
        $html_tracking_peer .= "<div class='col-sm-12'><center><h4>No registra seguimientos</h4></center></div>";
    }

    return $html_tracking_peer;
}

/**
 * @see student_profile_generate_risk_entries($risk_status)
 * @param $risk_status
 * @return array
 */
function student_profile_generate_risk_entries($risk_status){

    $DIMENSIONS_KEY = ["individual", "familiar", "academico", "economico", "vida_uni"];

    $get_risk_lvl_color = function( $risk_lvl ){
        $colors = [
            1 => "green",
            2 => "orange",
            3 => "red",
            -1 => ""
        ];
        return $colors[ $risk_lvl ];
    };

    $first_entry = [];
    foreach( $DIMENSIONS_KEY as $key => $dimension ){
        $risk_value = $risk_status["start_risk_lvl_fist_semester"][$dimension];
        if( $risk_value === -1 ){
            $risk_value = 0;
        }
        $first_entry[$dimension] = [
            "id_seguimiento" => -1,
            "datos" => [
                //"fecha" => date('Y-M-d', $risk_status["start_risk_lvl_fist_semester"]["semester_info"]["start_time"] ),
                "fecha" => "Inicial",
                "color" => $get_risk_lvl_color( $risk_status["start_risk_lvl_fist_semester"][$dimension] ),
                "riesgo" => $risk_value,
                "end" => 'false'
            ]
        ];
    }

    $other_entries = [];
    $other_risk_semesters = array_reverse( $risk_status["end_risk_lvl_semesters"] );
    array_shift ( $other_risk_semesters ); // Current semester must be not included
    foreach( $other_risk_semesters as $key => $semester_risk ){
        $entry = [];
        foreach( $DIMENSIONS_KEY as $_key => $dimension ){
            $risk_value = $semester_risk[$dimension];
            if( $risk_value === -1 ){
                $risk_value = 0;
            }
            $entry[$dimension] = [
                "id_seguimiento" => -1,
                "datos" => [
                    //"fecha" => date('Y-M-d', $semester_risk["semester_info"]["end_time"] ),
                    "fecha" => $semester_risk["semester_info"]["name"],
                    "color" => $get_risk_lvl_color( $semester_risk[$dimension] ),
                    "riesgo" => $risk_value,
                    "end" => 'false'
                ]
            ];
        }
        array_push( $other_entries, $entry );
    }

    return [
        "first" =>  $first_entry,
        "others" => $other_entries
    ];
}

/**
 * @see student_profile_load_risk_info()
 * @desc Loads the student's risk information
 * @param $id_ases string -> ASES student id
 * @param $peer_tracking object -> social-educative tracking
 * @return object
 */
function student_profile_load_risk_info($id_ases, $peer_tracking){

    $record = new stdClass();

    if($peer_tracking == null) {
        $peer_tracking = student_profile_get_peer_tracking($id_ases);
    } else {
        $peer_tracking = json_decode(json_encode($peer_tracking), true);
    }

    $periodo_actual = getPeriodoActual();
    $nombre_periodo = $periodo_actual['nombre_periodo'];

    $datos_seguimientos_periodo_actual = null;

    foreach ($peer_tracking as $key => $trackings_by_periods) {
        if( $trackings_by_periods['period_name'] ==  $nombre_periodo ){
            $datos_seguimientos_periodo_actual = $trackings_by_periods;
            break;
        }
    }

    /*
        In this block, we use the local_alias defined with the field in the dynamic form
        to filter the fields
    */

    $risks = array();

    foreach ($datos_seguimientos_periodo_actual[ 'trackings' ] as $key => $tmp_track ){

        if( !$tmp_track['custom_extra']['seguimiento_pares'] ){
            continue;
        }

        $risk_date = null;

        $individual_dimension_risk_lvl = null;
        $academic_dimension_risk_lvl = null;
        $economic_dimension_risk_lvl = null;
        $familiar_dimension_risk_lvl = null;
        $universitary_life_risk_lvl = null;

        $risk_date = $tmp_track['fecha'];

        if( ($tmp_track['puntuacion_riesgo_individual'] != '-#$%-')&&($tmp_track['puntuacion_riesgo_individual'] != '0') ){
            $individual_dimension_risk_lvl = $tmp_track['puntuacion_riesgo_individual'] ;
        }

        if( ($tmp_track['puntuacion_riesgo_academico'] != '-#$%-')&&($tmp_track['puntuacion_riesgo_academico'] != '0') ){
            $academic_dimension_risk_lvl = $tmp_track['puntuacion_riesgo_academico'] ;
        }

        if( ($tmp_track['puntuacion_riesgo_economico'] != '-#$%-')&&($tmp_track['puntuacion_riesgo_economico'] != '0') ){
            $economic_dimension_risk_lvl = $tmp_track['puntuacion_riesgo_economico'] ;
        }

        if( ($tmp_track['puntuacion_riesgo_familiar'] != '-#$%-')&&($tmp_track['puntuacion_riesgo_familiar'] != '0') ){
            $familiar_dimension_risk_lvl = $tmp_track['puntuacion_riesgo_familiar'] ;
        }

        if( ($tmp_track['puntuacion_vida_uni'] != '-#$%-')&&($tmp_track['puntuacion_vida_uni'] != '0') ){
            $universitary_life_risk_lvl = $tmp_track['puntuacion_vida_uni'] ;
        }

        $risk_by_dimensions = array();

        if( $individual_dimension_risk_lvl ){
            array_push(
                $risk_by_dimensions,
                array(
                    'dimension' => 'individual',
                    'risk_lvl' => $individual_dimension_risk_lvl
                )
            );
        }

        if( $academic_dimension_risk_lvl ){
            array_push(
                $risk_by_dimensions,
                array(
                    'dimension' => 'academica',
                    'risk_lvl' => $academic_dimension_risk_lvl
                )
            );
        }

        if( $economic_dimension_risk_lvl ){
            array_push(
                $risk_by_dimensions,
                array(
                    'dimension' => 'economica',
                    'risk_lvl' => $economic_dimension_risk_lvl
                )
            );
        }

        if( $familiar_dimension_risk_lvl ){
            array_push(
                $risk_by_dimensions,
                array(
                    'dimension' => 'familiar',
                    'risk_lvl' => $familiar_dimension_risk_lvl
                )
            );
        }

        if( $universitary_life_risk_lvl ){
            array_push(
                $risk_by_dimensions,
                array(
                    'dimension' => 'vida_universitaria',
                    'risk_lvl' => $universitary_life_risk_lvl
                )
            );
        }

        $risk_data = array(
            'date' => $risk_date,
            'information' => $risk_by_dimensions,
            'record_id' => $tmp_track['id_registro']
        );

        array_push( $risks, $risk_data );
    }

    $risk_individual_dimension = array();
    $risk_academic_dimension = array();
    $risk_economic_dimension = array();
    $risk_familiar_dimension = array();
    $risk_uni_life_dimension = array();

    $risks = array_reverse( $risks );

    for( $p = 0; $p < count( $risks ); $p++ ){

        for( $q = 0; $q < count( $risks[ $p ][ 'information' ] ); $q++  ){

            $isIndividual = false;
            $isAcademic = false;
            $isEconomic = false;
            $isFamiliar = false;
            $isUniLife = false;

            if( $risks[ $p ][ 'information' ][ $q ][ 'dimension' ] == 'individual' ){
                $isIndividual = true;
                $color = null;
                if( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '1'){
                    $color = 'green';
                }elseif ( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '2') {
                    $color = 'orange';
                }elseif ( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '3') {
                    $color = 'red';
                }
            }

            if( $risks[ $p ][ 'information' ][ $q ][ 'dimension' ] == 'academica' ){
                $isAcademic = true;
                $color = null;
                if( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '1'){
                    $color = 'green';
                }elseif ( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '2') {
                    $color = 'orange';
                }elseif ( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '3') {
                    $color = 'red';
                }
            }

            if( $risks[ $p ][ 'information' ][ $q ][ 'dimension' ] == 'economica' ){
                $isEconomic = true;
                $color = null;
                if( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '1'){
                    $color = 'green';
                }elseif ( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '2') {
                    $color = 'orange';
                }elseif ( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '3') {
                    $color = 'red';
                }
            }

            if( $risks[ $p ][ 'information' ][ $q ][ 'dimension' ] == 'familiar' ){
                $isFamiliar = true;
                $color = null;
                if( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '1'){
                    $color = 'green';
                }elseif ( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '2') {
                    $color = 'orange';
                }elseif ( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '3') {
                    $color = 'red';
                }
            }

            if( $risks[ $p ][ 'information' ][ $q ][ 'dimension' ] == 'vida_universitaria' ){
                $isUniLife = true;
                $color = null;
                if( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '1'){
                    $color = 'green';
                }elseif ( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '2') {
                    $color = 'orange';
                }elseif ( $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ] == '3') {
                    $color = 'red';
                }
            }

            $data = array(
                'fecha' => $risks[ $p ][ 'date' ],
                'color' => $color,
                'riesgo' => $risks[ $p ][ 'information' ][ $q ][ 'risk_lvl' ],
                'end' => 'false'
            );

            $tmp_risk = array(
                'id_seguimiento' => $risks[ $p ][ 'record_id' ],
                'datos' => $data
            );

            if( $isIndividual ){
                array_push( $risk_individual_dimension, $tmp_risk );
            }else if( $isAcademic ){
                array_push( $risk_academic_dimension, $tmp_risk );
            }else if( $isEconomic ){
                array_push( $risk_economic_dimension, $tmp_risk );
            }else if( $isFamiliar ){
                array_push( $risk_familiar_dimension, $tmp_risk );
            }else if( $isUniLife ){
                array_push( $risk_uni_life_dimension, $tmp_risk );
            }
        };
    }

    array_push( $risk_individual_dimension, array('datos' => array( 'end' => 'true' ) ) );
    array_push( $risk_academic_dimension, array('datos' => array( 'end' => 'true' ) ) );
    array_push( $risk_economic_dimension, array('datos' => array( 'end' => 'true' ) ) );
    array_push( $risk_familiar_dimension, array('datos' => array( 'end' => 'true' ) ) );
    array_push( $risk_uni_life_dimension, array('datos' => array( 'end' => 'true' ) ) );

    $record->individual = $risk_individual_dimension;
    $record->familiar = $risk_familiar_dimension;
    $record->academico = $risk_academic_dimension;
    $record->economico = $risk_economic_dimension;
    $record->vida_universitaria = $risk_uni_life_dimension;

    $historical_risk_lvl = student_lib_get_full_risk_status($id_ases);
    $risk_entries = student_profile_generate_risk_entries($historical_risk_lvl);

    foreach( $risk_entries["others"] as $key => $entry ){

        array_unshift( $record->individual, $entry["individual"] );
        array_unshift( $record->familiar, $entry["familiar"] );
        array_unshift( $record->academico, $entry["academico"] );
        array_unshift( $record->economico, $entry["economico"] );
        array_unshift( $record->vida_universitaria, $entry["vida_uni"] );
    }

    array_unshift( $record->individual, $risk_entries["first"]["individual"] );
    array_unshift( $record->familiar, $risk_entries["first"]["familiar"] );
    array_unshift( $record->academico, $risk_entries["first"]["academico"] );
    array_unshift( $record->economico, $risk_entries["first"]["economico"] );
    array_unshift( $record->vida_universitaria, $risk_entries["first"]["vida_uni"] );

    return $record;
}

/**
 * @see student_profile_load_socioed_tab($id_ases)
 * @desc Gets all the social-educative information of an student
 * @param $id_ases string -> ASES student id
 * @param $id_block string -> Block id
 * @return Object
 */
function student_profile_load_socioed_tab($id_ases, $id_block){

    global $USER;

    $id_user = $USER->id;
    $id_block = (int)$id_block;

    $actions = authenticate_user_view($id_user, $id_block);
    $record = $actions;
/*
    $record->peer_tracking_v3 = student_profile_get_peer_tracking($id_ases, $id_block);
    $record->peer_tracking_v3_string = json_encode($record->peer_tracking_v3);
    $record->peer_tracking = student_profile_get_html_peer_tracking($id_ases, $id_block);

    $record->registro_primer_acercamient = null;
    $record->editor_registro_primer_acercamiento = null;
    $primer_acercamiento = json_decode( dphpforms_find_records('primer_acercamiento', 'primer_acercamiento_id_estudiante', $id_ases, 'DESC') )->results;

    if($primer_acercamiento){
        $record->actualizar_primer_acercamiento = true;
        $record->id_primer_acercamiento = array_values( $primer_acercamiento )[0]->id_registro;
    }else{
        $record->registro_primer_acercamiento = true;
    }
    */
    return $record;
}
/**
 * @see student_profile_carga_historico($id_ases)
 * @desc Gets all the social-educative information of an student
 * @param $id_ases string -> ASES student id
 * @param $id_block string -> Block id
 * @return Object
 */

function student_profile_carga_historico($id_ases, $id_block){

    global $USER;

    if (!is_numeric($id_ases)) {
        return false;
    }

    $id_user = $USER->id;
    $id_block = (int)$id_block;

    $actions = authenticate_user_view($id_user, $id_block);
    $record = $actions;

    $record->peer_tracking_v3 = student_profile_get_peer_tracking($id_ases, $id_block);
    $record->peer_tracking_v3_string = json_encode($record->peer_tracking_v3);
    $record->peer_tracking = student_profile_get_html_peer_tracking($id_ases, $id_block);
      
    return $record;
}

/**
 * @see student_profile_load_academic_tab($id_ases)
 * @desc Gets all the academic information of an student
 * @param $id_ases string -> ASES student id
 * @return Object
 */
function student_profile_load_academic_tab($id_ases, $instance_id){

    $record = new stdClass();

    $academic_programs = get_status_program_for_profile_aditional($id_ases);
    $id_user_moodle = get_id_user_moodle($id_ases);

    foreach ($academic_programs as $program) {
        if($program->tracking_status == 1){
            $academic_program_id = $program->academic_program_id;
            break;
        }
    }

    //Current data
    //weighted average
    $promedio = get_promedio_ponderado($id_ases, $academic_program_id);
    $record->promedio = $promedio;

    //num bajos
    $bajos = get_bajos_rendimientos($id_ases, $academic_program_id);
    $record->bajos = $bajos;

    //num estimulos
    $estimulos = get_estimulos($id_ases, $academic_program_id);
    $record->estimulos = $estimulos;

    //Current semester
    $html_academic_table = get_grades_courses_student_last_semester($id_user_moodle, $instance_id);
    $record->academic_semester_act = $html_academic_table;

    //historic academic
    $html_historic_academic = get_historic_academic_by_student($id_ases);
    $record->historic_academic = $html_historic_academic;

    return $record;
}

/**
 * @see student_profile_load_geographic_tab($id_ases)
 * @desc Gets all the geographic information of an student
 * @param $id_ases string -> ASES student id
 * @return Object
 */
function student_profile_load_geographic_tab($id_ases){

    $record = new stdClass();

    $geographic_object = get_geographic_info($id_ases);

    $student_city = $geographic_object->id_city;
    $student_neighborhood = $geographic_object->neighborhood;

    $record->options_municipio_act = student_profile_get_options_municipios($student_city);
    $record->select_neighborhoods = student_profile_get_options_neighborhoods($student_neighborhood);

    $native = $geographic_object->native;
    $live_far_away = $geographic_object->live_far_away;
    $live_risk_zone = $geographic_object->live_risk_zone;
    $geographic_risk_level = $geographic_object->risk_level;

    $record->live_far_away = ($live_far_away)?true:false;
    $record->live_risk_zone = ($live_risk_zone)?true:false;
    $record->observations = $geographic_object->observations;
    $record->res_address = $geographic_object->res_address;
    $record->geographic_risk_level  = $geographic_risk_level;

    if($live_risk_zone && $native == 1)
        $record->native_origin = true;

    switch ($geographic_risk_level) {
        case 1:
            $record->low_level = true;
            $record->mid_level = $record->high_level = false;
            break;
        case 2:
            $record->mid_level = true;
            $record->low_level = $record->high_level = false;
            break;
        case 3:
            $record->high_level = true;
            $record->low_level = $record->mid_level = false;
            break;
        default:
            $record->low_level = $record->mid_level = $record->high_level = false;
            break;
    }

    $record->obj = $geographic_object;

    return $record;
}

/**
 * @see student_profile_load_others_tab($id_ases)
 * @desc Gets all the additional information of an student
 * @param $id_ases string -> ASES student id
 * @return Object
 */
function student_profile_load_tracing_others_tab($id_ases){

    $record = new stdClass();

    return $record;
}

/**
 * @see student_profile_load_discapacity_tracking_tab($id_ases)
 * @desc Gets all the discapacity tracking information of an student
 * @param $id_ases string -> ASES student id
 * @return Object
 */
function student_profile_load_discapacity_tracking_tab($id_ases){

    $record = new stdClass();
    return $record;
}
