<<<<<<< HEAD
<?php

require_once(dirname(__FILE__). '/../../../../config.php');

/**
 * Función que recupera los mótivos por los cuales un estudiante abandona o aplaza sus estudios en la universidad.
 *
 * @see  get_reasons_dropout()
 * @param void
 * @return Array --> Motivos por los cuales un estudiante puede abandonar o aplazar sus estudios.
 */
 
 function get_reasons_dropout(){
     
     global $DB;
     
     $sql_query = "SELECT * FROM {talentospilos_motivos}";
     $reasons_array = $DB->get_records_sql($sql_query);
     
     return $reasons_array;
 }
 
 /**
 * Función que extrae el conjunto de estados Ases
 *
 * @see get_status_ases()
 * @param void
 * @return Array --> Conjunto de estados Ases
 */
 
 function get_status_ases(){
     
     global $DB;
     
     $sql_query = "SELECT * FROM {talentospilos_estados_ases}";
     $status_ases_array = $DB->get_records_sql($sql_query);
     
     return $status_ases_array;
 }
 
/**
 * Función que extrae el conjunto de estados Icetex
 *
 * @see get_status_icetex()
 * @param void
 * @return Array --> Conjunto de estados Icetex
 */
 function get_status_icetex(){
     
     global $DB;
     
     $sql_query = "SELECT * FROM {talentospilos_estados_icetex}";
     $status_icetex_array = $DB->get_records_sql($sql_query);
     
     return $status_icetex_array;
 }
 
 /**
 * Función que retorna un único seguimiento a partir de su id y el tipo de seguimiento
 *
 * @see load_tracking()
 * @param $id_tracking
 * @param $type_tracking
 * @return Array --> Datos del seguimiento
 */
function load_tracking($id_tracking, $type_tracking, $id_instance) {
 
     
 }
 
/**
 * Función que extrae los seguimientos de un estudiante dado el id ASES del estudiante
 * el tipo del seguimiento y la instancia asociada al seguimiento y al estudiante ASES
 *
 * @see get_trackings_student()
 * @param id_ases --> id relacionado en la tabla talentospilos_profile
 * @param tracking_type [PARES, GRUPAL]
 * @param id_instance --> Id asociado a la instancia del módulo
 * @return Trackings array
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
 * Función que retorna los seguimientos de un estudiante agrupados por semestre
 * 
 * @see get_tracking_group_by_semester()
 * @param id_ases --> id relacionado en la tabla talentospilos_profile
 * @param tracking_type [PARES, GRUPAL]
 * @param id_instance --> Id asociado a la instancia del módulo
 * @return Trackings array group by semester
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
                
                if($lastsemestreinfo && (strtotime($semester->fecha_inicio) <= strtotime($lastsemestreinfo->fecha_inicio))){ //se valida que solo se obtenga la info de los semestres en que se encutra matriculado el estudiante
                
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
 * Función que retorna el id del primer semestre cursado por el estudiante
 *
 * @param int --- id student 
 * @return int --- id first semester
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
 * Return array of semesters of a student
 *
 * @param string $username_student Is te username of moodlesite 
 * @return array() of stdClass object representing semesters of a student
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
 * Función que guarda un seguimiento realizado a un estudiante
 * Guarda información en la tabla tp_seguimientos y la relación de estudiante y sus
 * seguimientos (tp_seguimiento_estud)
 *
 * @param $object_tracking --> Objeto con toda la información correspondiente al seguimiento de pares a almacenar
 * @return $object_result --> Objeto que almacena el resultado de operación en la base de datos
 */
function save_tracking_peer($object_tracking){

    global $DB;

    pg_query("BEGIN") or die("Falló la conexión con la base de datos\n");
    $result_saving = new stdClass();

    // Inserción o actualización del seguimiento, en caso se registre un ID de seguimiento
    if($object_tracking->id != ""){

        unset($object_tracking->id_monitor);
        $result = $DB->update_record('talentospilos_seguimiento', $object_tracking);
        $result_insertion_tracking = -1;  // Este valor para esta variable indica que no se realizó inserción si no actualización del seguimientos
    }else{
        // Inserta el seguimiento
        unset($object_tracking->id);
        $result_insertion_tracking = $DB->insert_record('talentospilos_seguimiento', $object_tracking, true);
    }    

    // Inserta la relación de seguimiento_estudiante
    if($result_insertion_tracking != -1){
        $object_tracking_student = new stdClass();
        $object_tracking_student->id_estudiante = $object_tracking->id_estudiante_ases;
        $object_tracking_student->id_seguimiento = $result_insertion_tracking;
        $result_insertion_student_tracking = $DB->insert_record('talentospilos_seg_estudiante',  $object_tracking_student, true);
    }else{
        $result_insertion_tracking = 1;
        $result_insertion_student_tracking = 1;
    }
    

    // Se consultan ID riesgos

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

    // ID relación estudiante_riesgo individual
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
        

    // ID relación estudiante_riesgo familiar
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

    // ID relación estudiante_riesgo académico
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

    // ID relación estudiante_riesgo económico
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

    // ID relación estudiante_riesgo vida universitaria
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
 * Función que realiza un borrado lógico para un seguimiento cambiando su estado en la base de datos
 * en la tabla de seguimientos (talentospilos_seguimiento)
 *
 * @param $id_tracking --> Objeto con toda la información correspondiente al seguimiento de pares a almacenar
 * @return $object_result --> Objeto que almacena el resultado de operación en la base de datos
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
 * Función que guarda el cambio de estado Icetex de un estudiante
 * 
 *
 * @param $id_student --> ID correspondiente al estudiante Ases
 * @param $id_status --> ID correspondiente al estado Ases a almacenar
 * @param $id_reason --> ID correspondiente al motivo en caso de que el nuevo estado sea RETIRADO. Por defecto null.
 * @return $object_result --> Objeto que almacena el resultado de operación en la base de datos
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
 * Función que guarda el cambio de estado ASES de un estudiante
 * 
 *
 * @param $id_student --> ID correspondiente al estudiante Ases
 * @param $id_status --> ID correspondiente al estado Ases a almacenar
 * @param $id_reason --> ID correspondiente al motivo en caso de que el nuevo estado sea RETIRADO. Por defecto null.
 * @return $object_result --> Objeto que almacena el resultado de operación en la base de datos
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
 * Función que busca un estudiante a partir del código de estudiante Ases.
 *
 * @param $id_student --> ID correspondiente al estudiante Ases
 * @return $object_result --> Objeto que almacena el resultado de operación en la base de datos
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

=======
<?php

require_once(dirname(__FILE__). '/../../../../config.php');

/**
 * Función que recupera los mótivos por los cuales un estudiante abandona o aplaza sus estudios en la universidad.
 *
 * @see  get_reasons_dropout()
 * @param void
 * @return Array --> Motivos por los cuales un estudiante puede abandonar o aplazar sus estudios.
 */
 
 function get_reasons_dropout(){
     
     global $DB;
     
     $sql_query = "SELECT * FROM {talentospilos_motivos}";
     $reasons_array = $DB->get_records_sql($sql_query);
     
     return $reasons_array;
 }
 
 /**
 * Función que extrae el conjunto de estados Ases
 *
 * @see get_status_ases()
 * @param void
 * @return Array --> Conjunto de estados Ases
 */
 
 function get_status_ases(){
     
     global $DB;
     
     $sql_query = "SELECT * FROM {talentospilos_estados_ases}";
     $status_ases_array = $DB->get_records_sql($sql_query);
     
     return $status_ases_array;
 }
 
/**
 * Función que extrae el conjunto de estados Icetex
 *
 * @see get_status_icetex()
 * @param void
 * @return Array --> Conjunto de estados Icetex
 */
 function get_status_icetex(){
     
     global $DB;
     
     $sql_query = "SELECT * FROM {talentospilos_estados_icetex}";
     $status_icetex_array = $DB->get_records_sql($sql_query);
     
     return $status_icetex_array;
 }
 
 /**
 * Función que retorna un único seguimiento a partir de su id y el tipo de seguimiento
 *
 * @see load_tracking()
 * @param $id_tracking
 * @param $type_tracking
 * @return Array --> Datos del seguimiento
 */
function load_tracking($id_tracking, $type_tracking, $id_instance) {
 
     
 }
 
/**
 * Función que extrae los seguimientos de un estudiante dado el id ASES del estudiante
 * el tipo del seguimiento y la instancia asociada al seguimiento y al estudiante ASES
 *
 * @see get_trackings_student()
 * @param id_ases --> id relacionado en la tabla talentospilos_profile
 * @param tracking_type [PARES, GRUPAL]
 * @param id_instance --> Id asociado a la instancia del módulo
 * @return Trackings array
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
 * Función que retorna los seguimientos de un estudiante agrupados por semestre
 * 
 * @see get_tracking_group_by_semester()
 * @param id_ases --> id relacionado en la tabla talentospilos_profile
 * @param tracking_type [PARES, GRUPAL]
 * @param id_instance --> Id asociado a la instancia del módulo
 * @return Trackings array group by semester
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
                
                if($lastsemestreinfo && (strtotime($semester->fecha_inicio) <= strtotime($lastsemestreinfo->fecha_inicio))){ //se valida que solo se obtenga la info de los semestres en que se encutra matriculado el estudiante
                
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
 * Función que retorna el id del primer semestre cursado por el estudiante
 *
 * @param int --- id student 
 * @return int --- id first semester
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
 * Return array of semesters of a student
 *
 * @param string $username_student Is te username of moodlesite 
 * @return array() of stdClass object representing semesters of a student
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
 * Función que guarda un seguimiento realizado a un estudiante
 * Guarda información en la tabla tp_seguimientos y la relación de estudiante y sus
 * seguimientos (tp_seguimiento_estud)
 *
 * @param $object_tracking --> Objeto con toda la información correspondiente al seguimiento de pares a almacenar
 * @return $object_result --> Objeto que almacena el resultado de operación en la base de datos
 */
function save_tracking_peer($object_tracking){

    global $DB;

    pg_query("BEGIN") or die("Falló la conexión con la base de datos\n");
    $result_saving = new stdClass();

    // Inserción o actualización del seguimiento, en caso se registre un ID de seguimiento
    if($object_tracking->id != ""){

        unset($object_tracking->id_monitor);
        $result = $DB->update_record('talentospilos_seguimiento', $object_tracking);
        $result_insertion_tracking = -1;  // Este valor para esta variable indica que no se realizó inserción si no actualización del seguimientos
    }else{
        // Inserta el seguimiento
        unset($object_tracking->id);
        $result_insertion_tracking = $DB->insert_record('talentospilos_seguimiento', $object_tracking, true);
    }    

    // Inserta la relación de seguimiento_estudiante
    if($result_insertion_tracking != -1){
        $object_tracking_student = new stdClass();
        $object_tracking_student->id_estudiante = $object_tracking->id_estudiante_ases;
        $object_tracking_student->id_seguimiento = $result_insertion_tracking;
        $result_insertion_student_tracking = $DB->insert_record('talentospilos_seg_estudiante',  $object_tracking_student, true);
    }else{
        $result_insertion_tracking = 1;
        $result_insertion_student_tracking = 1;
    }
    

    // Se consultan ID riesgos

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

    // ID relación estudiante_riesgo individual
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
        

    // ID relación estudiante_riesgo familiar
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

    // ID relación estudiante_riesgo académico
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

    // ID relación estudiante_riesgo económico
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

    // ID relación estudiante_riesgo vida universitaria
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
 * Función que realiza un borrado lógico para un seguimiento cambiando su estado en la base de datos
 * en la tabla de seguimientos (talentospilos_seguimiento)
 *
 * @param $id_tracking --> Objeto con toda la información correspondiente al seguimiento de pares a almacenar
 * @return $object_result --> Objeto que almacena el resultado de operación en la base de datos
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
 * Función que guarda el cambio de estado Icetex de un estudiante
 * 
 *
 * @param $id_student --> ID correspondiente al estudiante Ases
 * @param $id_status --> ID correspondiente al estado Ases a almacenar
 * @param $id_reason --> ID correspondiente al motivo en caso de que el nuevo estado sea RETIRADO. Por defecto null.
 * @return $object_result --> Objeto que almacena el resultado de operación en la base de datos
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
 * Función que guarda el cambio de estado ASES de un estudiante
 * 
 *
 * @param $id_student --> ID correspondiente al estudiante Ases
 * @param $id_status --> ID correspondiente al estado Ases a almacenar
 * @param $id_reason --> ID correspondiente al motivo en caso de que el nuevo estado sea RETIRADO. Por defecto null.
 * @return $object_result --> Objeto que almacena el resultado de operación en la base de datos
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
 * Función que busca un estudiante a partir del código de estudiante Ases.
 *
 * @param $id_student --> ID correspondiente al estudiante Ases
 * @return $object_result --> Objeto que almacena el resultado de operación en la base de datos
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

>>>>>>> 97c7d23d80c7365c0b40027b0d4abac40b2e33b4
