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
require_once(dirname(__FILE__). '/../../../../config.php');
require_once $CFG->dirroot.'/blocks/ases/managers/dphpforms/dphpforms_forms_core.php';
require_once $CFG->dirroot.'/blocks/ases/managers/dphpforms/dphpforms_records_finder.php';
require_once $CFG->dirroot.'/blocks/ases/managers/dphpforms/dphpforms_get_record.php';
require_once $CFG->dirroot.'/blocks/ases/managers/periods_management/periods_lib.php';

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
 * Gets a set of ICETEX status
 *
 * @see get_status_icetex()
 * @return array with the ICETEX status grouped
 */ 
 function get_status_icetex(){
     
     global $DB;
     
     $sql_query = "SELECT * FROM {talentospilos_estados_icetex}";
     $status_icetex_array = $DB->get_records_sql($sql_query);
     
     return $status_icetex_array;
 }
 
 /**
 * Get an unique track given its id and track type
 *
 * @see load_tracking()
 * @param $id_tracking
 * @param $type_tracking
 * @return array --> Datos del seguimiento
 */
function load_tracking($id_tracking, $type_tracking, $id_instance) {
 
     
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
 
function get_tracking_peer_student_current_semester($student_id, $semester_id){

    $interval = get_semester_interval($semester_id);
    $fecha_inicio = getdate(strtotime($interval->fecha_inicio));
    $fecha_fin = getdate(strtotime($interval->fecha_fin));
    $ano_semester  = $fecha_inicio['year'];
   
    $array_peer_trackings_dphpforms = dphpforms_find_records('seguimiento_pares', 'seguimiento_pares_id_estudiante', $student_id, 'DESC');
    $array_peer_trackings_dphpforms = json_decode($array_peer_trackings_dphpforms);
    $array_detail_peer_trackings_dphpforms = array();
    foreach ($array_peer_trackings_dphpforms->results as &$peer_trackings_dphpforms) {
        array_push($array_detail_peer_trackings_dphpforms, json_decode(dphpforms_get_record($peer_trackings_dphpforms->id_registro, 'fecha')));
    }

    $array_tracking_date = array();
    foreach ($array_detail_peer_trackings_dphpforms as &$peer_tracking) {
        foreach ($peer_tracking->record->campos as &$tracking) {
            if ($tracking->local_alias == 'fecha') {
                array_push($array_tracking_date, strtotime($tracking->respuesta));
            }
        }
    }

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
        if (property_exists($seguimientos_ordenados, $array_tracking_date[$x]['year'])) {
            if (in_array($array_tracking_date[$x]['mon'], $periodo_actual)) {
                for ($y = 0; $y < count($array_detail_peer_trackings_dphpforms); $y++) {
                    if ($array_detail_peer_trackings_dphpforms[$y]) {
                        foreach ($array_detail_peer_trackings_dphpforms[$y]->record->campos as &$tracking) {
                            if ($tracking->local_alias == 'fecha') {
                                if (strtotime($tracking->respuesta) == $string_date) {
                                    array_push($seguimientos_ordenados->$array_tracking_date[$x]['year']->periodo, $array_detail_peer_trackings_dphpforms[$y]);
                                    $array_detail_peer_trackings_dphpforms[$y] = null;
                                    break;
                                }
                            }
                        }
                    }
                }
            } 

        } else {
            array_push($seguimientos_ordenados->index, $array_tracking_date[$x]['year']);
            $seguimientos_ordenados->$array_tracking_date[$x]['year']->year = $array_tracking_date[$x]['year'];
            $seguimientos_ordenados->$array_tracking_date[$x]['year']->periodo = array();

            $seguimientos_ordenados->$array_tracking_date[$x]['year']->year = $array_tracking_date[$x]['year'];
            if(in_array($array_tracking_date[$x]['mon'], $periodo_actual)){
                
                for($y = 0; $y < count($array_detail_peer_trackings_dphpforms); $y++){
                    if($array_detail_peer_trackings_dphpforms[$y]){
                        foreach ($array_detail_peer_trackings_dphpforms[$y]->record->campos as &$tracking) {
                            if ($tracking->local_alias == 'fecha') {
                                if (strtotime($tracking->respuesta) == $string_date) {
                                    array_push($seguimientos_ordenados->$array_tracking_date[$x]['year']->periodo, $array_detail_peer_trackings_dphpforms[$y]);
                                    $array_detail_peer_trackings_dphpforms[$y] = null;
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
        
        $sql_query = "SELECT * FROM {talentospilos_semestre}";
        
        if($id_semester != null){
            $sql_query .= " WHERE id = ".$id_semester;
        }else{
            $userid = $DB->get_record_sql("SELECT userid FROM {user_info_data} AS data 
                                                         INNER JOIN {user_info_field} AS field on data.fieldid = field.id 
                                                         WHERE field.shortname='idtalentos' AND data.data='$id_ases';");
            $firstsemester = get_id_first_semester($userid->userid);
            $lastsemestre = get_id_last_semester($userid->userid);
    
            $sql_query .= " WHERE id >=".$firstsemester;
            
        }
        $sql_query.=" order by fecha_inicio DESC";
    
        $array_semesters_seguimientos =  array();
        
        if($lastsemestre && $firstsemester){
            
            $semesters = $DB->get_records_sql($sql_query);
            $counter = 0;
    
            $sql_query ="select * from {talentospilos_semestre} where id = ".$lastsemestre;
            $lastsemestreinfo = $DB->get_record_sql($sql_query);
            
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
 * @return string --> first semester id
 */
function get_id_first_semester($id){
    try {
        global $DB;
        
        $sql_query = "SELECT username, timecreated from {user} where id = ".$id;
        $result = $DB->get_record_sql($sql_query);
        
        $year_string = substr($result->username, 0, 2);
        $date_start = strtotime('01-01-20'.$year_string);

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

        $sql_query = "select id, nombre ,fecha_inicio::DATE, fecha_fin::DATE from {talentospilos_semestre} ORDER BY fecha_fin ASC;";
        
        $semesters = $DB->get_records_sql($sql_query);
        
        $id_first_semester = 0; 

        foreach ($semesters as $semester){
            $fecha_inicio = new DateTime($semester->fecha_inicio);

            date_add($fecha_inicio, date_interval_create_from_date_string('-60 days'));
            
            if((strtotime($fecha_inicio->format('Y-m-d')) <= $timecreated) && ($timecreated <= strtotime($semester->fecha_fin))){
                
                return $semester->id;
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
 * @return array --> stdClass object representing semesters of a student
 */
function get_semesters_stud($id_first_semester){
     
    global $DB;
     
    $sql_query = "SELECT id, nombre, fecha_inicio::DATE, fecha_fin::DATE FROM {talentospilos_semestre} WHERE id >= $id_first_semester ORDER BY {talentospilos_semestre}.fecha_inicio DESC";
     
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
    
    // var_dump(strtotime($fecha_inicio->format('Y-m-d')));
    // var_dump($fecha_fin);
    // var_dump($fecha_comparar);
    //print_r(($fecha_comparar >= strtotime($fecha_inicio->format('Y-m-d'))) && ($fecha_comparar <= $fecha_fin));
    return (((int)$fecha_comparar >= strtotime($fecha_inicio->format('Y-m-d'))) && ((int)$fecha_comparar <= (int)$fecha_fin));
 }

 /**
  * Gets last semester id given a moodle id
  * 
  * @see get_id_last_semester($idmoodle)
  * @param $idmoodle --> moodle student id
  * @return string|boolean --> string containing the last semster id or false in case there weren't semesters related with the student
  */

 function get_id_last_semester($idmoodle){

     $id_first_semester = get_id_first_semester($idmoodle);
     $semesters = get_semesters_stud($id_first_semester);
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
        $result = $DB->update_record('talentospilos_seguimiento', $object_tracking);
        $result_insertion_tracking = -1;  // This variable value indicates it wasn't an insertion but an update
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
            $DB->update_record('talentospilos_riesg_usuario', $object_risk_individual);
        }
    }else{
        $object_risk_individual = new stdClass();
        $object_risk_individual->id_usuario = $object_tracking->id_estudiante_ases;
        $object_risk_individual->id_riesgo = $id_individual_risk;
        $object_risk_individual->calificacion_riesgo = 0;
        $DB->insert_record('talentospilos_riesg_usuario', $object_risk_individual);
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
            $DB->update_record('talentospilos_riesg_usuario', $object_risk_familiar);
        }
    }else{
        $object_risk_familiar = new stdClass();
        $object_risk_familiar->id_usuario = $object_tracking->id_estudiante_ases;
        $object_risk_familiar->id_riesgo = $id_familiar_risk;
        $object_risk_familiar->calificacion_riesgo = 0;
        $DB->insert_record('talentospilos_riesg_usuario', $object_risk_familiar);
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
            $DB->update_record('talentospilos_riesg_usuario', $object_risk_academic);
        }
    }else{
        $object_risk_academic = new stdClass();
        $object_risk_academic->id_usuario = $object_tracking->id_estudiante_ases;
        $object_risk_academic->id_riesgo = $id_academic_risk;
        $object_risk_academic->calificacion_riesgo = 0;
        $DB->insert_record('talentospilos_riesg_usuario', $object_risk_academic);
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
            $DB->update_record('talentospilos_riesg_usuario', $object_risk_economic);
        }
    }else{
        $object_risk_economic = new stdClass();
        $object_risk_economic->id_usuario = $object_tracking->id_estudiante_ases;
        $object_risk_economic->id_riesgo = $id_economic_risk;
        $object_risk_economic->calificacion_riesgo = 0;
        $DB->insert_record('talentospilos_riesg_usuario', $object_risk_economic);
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
            $DB->update_record('talentospilos_riesg_usuario', $object_risk_life);
        }
    }else{
        $object_risk_life = new stdClass();
        $object_risk_life->id_usuario = $object_tracking->id_estudiante_ases;
        $object_risk_life->id_riesgo = $id_life_u_risk;
        $object_risk_life->calificacion_riesgo = 0;
        $DB->insert_record('talentospilos_riesg_usuario', $object_risk_life);
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

    $result_query = $DB->update_record('talentospilos_seguimiento', $object_updatable);

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
 * @param $observations --> Observaton of change
 * @return object --> object representing the database operation result
 */

function save_status_icetex($id_status, $id_student, $id_reason=null, $observations=null){

    global $DB;
    $msg_result = new stdClass();

    date_default_timezone_set('America/Bogota');

    $today_timestamp = time();

    $object_status = new stdClass();

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

        $DB->update_record('talentospilos_usuario', $object_updatable);
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
 * Saves the ASES change of status of a student
 * 
 * @see save_status_ases($id_status, $id_student, $id_reason=null, $observations=null)
 * @param $id_student --> ASES student id
 * @param $id_status --> status id that'll be saved
 * @param $id_reason --> In case the status is retired, this is de id reason. null by default
 * @return object --> object representing the database operation result
 */

function save_status_ases($id_status, $id_student, $id_reason=null, $observations=null){

    global $DB;
    $msg_result = new stdClass();

    date_default_timezone_set('America/Bogota');

    $today_timestamp = time();

    $object_status = new stdClass();

    $object_status->fecha = $today_timestamp;
    $object_status->id_estado_ases = $id_status;
    $object_status->id_estudiante = $id_student;

    if($id_reason){
        $object_status->id_motivo_retiro = $id_reason;
    }

    if($observations){
        $sql_query = "SELECT observacion FROM {talentospilos_usuario} WHERE id = $id_student";
        $user_observations = $DB->get_record_sql($sql_query)->observacion;

        $user_observations = date('d-m-y', $today_timestamp).": Mótivo de retiro ASES:  $observations"."\n".$user_observations;

        $object_updatable = new stdClass();
        $object_updatable->id = $id_student;
        $object_updatable->observacion = $user_observations;

        $DB->update_record('talentospilos_usuario', $object_updatable);
    }

    $result_insertion = $DB->insert_record('talentospilos_est_estadoases', $object_status);

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

    $sql_query = "SELECT id FROM {user} WHERE username LIKE '".$code_student."%'";
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

function get_name_by_username($username)
{
    global $DB;

    $sql_query = "SELECT * FROM {user} WHERE username LIKE '$username%'";
    $user = $DB->get_record_sql($sql_query);

    return $user;
}


