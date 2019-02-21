<?php
require_once (__DIR__ . '/../../../config.php');
require_once (__DIR__ . '/user_management/user_lib.php');
require_once (__DIR__ . '/role_management/role_management_lib.php');
require_once (__DIR__ . '/course/course_lib.php');
require_once('MyException.php');
require_once $CFG->libdir.'/gradelib.php';
require_once $CFG->dirroot.'/grade/querylib.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/user/lib.php';
require_once $CFG->dirroot.'/grade/report/grader/lib.php';

require_once ('lib/student_lib.php');


/**
 * get_user_by_username()
 *
 * @param  $username Moodle username 
 * @return Array user
 */
function get_user_by_username($username){
    global $DB;
    
    $sql_query = "SELECT * FROM {user} WHERE username = '".$username."'";
    $user = $DB->get_record_sql($sql_query);
    
    return $user;
}



function getEnfasisFinal($idtalentos){
    global $DB;
    return $DB -> get_record_sql("SELECT * FROM (SELECT nombre AS nom_enfasis, * FROM {talentospilos_enfasis}) enf INNER JOIN {talentospilos_vocacional} voc ON enf.id = voc.final_enfasis  WHERE id_estudiante=".$idtalentos.";"); 
}

function getRiskString($val){
    if($val ==0){
        return '<span ><span style="color: red;">Sin Contacto</span></span>';
    }else if($val>0 && $val<2){
        return "Bajo";
    }else if($val>=2 && $val<3){
        return "Medio Bajo";
    }else if($val>=3 && $val<4){
        return "Medio";
    }else if($val>=4 && $val<5){
        return "Medio Alto";
    }else if($val == 5){
        return "Alto";
    }
}

function update_talentosusuario($column,$values,$id){
    global $DB;
    try{
        
        //se obtiene el id de  la tabla usario talentos
        $iduser = get_userById(array('idtalentos'),$id);
        //se define un arreglo que va a contener la info a actualizar
        $obj_updatable = array();
        //se inserta la info
        for($i = 0; $i < count($column); $i++){
            $obj_updatable[$column[$i]] = $values[$i];
        }
        $obj_updatable = (object) $obj_updatable;
        //se le asigna el id del usario a actualizar
        $obj_updatable->id = $iduser->idtalentos;
        
        return $DB->update_record('talentospilos_usuario', $obj_updatable);
    }catch(Exception $e){
       return false;
    }
}

/**
 * Función que genera el reporte de cantidad de estudiantes por cohorte por estado
 *
 * @see count_by_state($cohort){
 * @return array
 */
function count_by_state($cohort){
    
    global $DB;
    
    if($cohort == "TODOS"){
        $sql_query = "SELECT estado, COUNT(id) FROM {talentospilos_usuario} GROUP BY estado";
        $result = $DB->get_records_sql($sql_query);
    }else{
        $sql_query = "";
        $result = "";
    }
    
    print_r($result);
    die();
}
/*******
 Testing
 *******/
// count_by_state('TODOS');


/**
 * Update notes from a student
 *
 * @param   $userid: id of student
 *          $items: Array of item's id
 *          $old_n: Array of old notes
 *          $new_n: Array of new notes
 *          ---old_n[i] and new_n[i] are notes from items[i]        
 * @return A query result if is succesful and false if not.
 */
 
function update_notas($user_id, $items, $old_n, $new_n){
    global $DB;
    try{
        $sql_query = "";
        for($i = 0; $i < count($old_n); $i++){
            if(($old_n[$i] != $new_n[$i])){

                    if(are_register($user_id, $items[$i])){
                        $sql_query = "UPDATE {grade_grades} SET finalgrade = {$new_n[$i]} WHERE userid = {$user_id} AND itemid = {$items[$i]}";
                        $succes = $DB->execute($sql_query);
                    }else{
                        // $sql_query = "INSERT INTO {grade_grades}(userid, itemid, finalgrade) VALUES($new_n[$i], $user_id, $items[$i])";
                        $succes = $DB->insert_record('grade_grades',array('userid'=> $user_id, 'itemid' => $items[$i], 'finalgrade' => $new_n[$i]));
                    }
                }
        }
        
        return $succes;
        
    }catch(Exception $e){
           
      return false;
    }
}

function are_register($userid, $item){

    global $DB;
    $sql_query = "SELECT id FROM {grade_grades} WHERE itemid = $item AND userid = $userid;";
    $succes = $DB->get_record_sql( $sql_query);
    
    
    if(isset($succes->id)){
        // print_r("def");
        $bool = true;
    }else{
        // print_r("no def");
        $bool =  false;
    }
    // print($bool);
    return $bool;
}
    
// are_register(171,455);

/** 
 **************************************
 Funciones reculcular notas finales
 **************************************
**/

function make_categories($notas, $categs, $porcentajes){
    
    $categorias = array();
    $categoria = new stdClass();
    $ultimo = count($notas)-1;
    //Se llena el arreglo de categorias
    //Cada categoria tiene un id y un arreglo de items
    for($i = 0; $i < count($notas); $i++){
        
        if($i>=1 && $categs[$i] != $categs[$i - 1]){
            array_push($categorias, $categoria);
            $categoria->id = "";
            $categoria->items = array();
            $categoria->id = $categs[$i];
            $item = new stdClass();
            $item->nota = $notas[i];
            $item->porcentaje = $porcentajes[i];
            array_push($categoria->items, $item);    
        }else if($i != $ultimo){
            $categoria->id = $categs[$i];
            $item = new stdClass();
            $item->nota = $notas[i];
            $item->porcentaje = $porcentajes[i];
            array_push($categoria->items, $item);
            
        }
        
    }
    
    
    
    return $categorias;
}

function extract_notes($categorias){
    $notas = array();
    
    for($i = 0; $i < count($categorias); $i++){
        for($j = 0; $j < count($categorias[$i]->items);$j++){
            array_push($notas, $categorias[$i]->items[$j]->nota);
        }
    }
    
    return $notas;
}

function recalculate_percentages(&$categorias){
    //se recorren las categorias
    for($i = 0; $i < count($categorias); $i++){
        //numero de items calificados
        $items_calif = 0;
        //porcentaje de items que estan sin calificar
        $porcentaje_sin_nota = 0;
        //porcentaje que se debe repartir a cada item: $porcentaje_a_repart = $porcentaje_sin_nota / $items_calif
        $porcentaje_a_repart = 0;
        
        //se recorren los items para obtener los datos
        for($j = 0; $j < count($categorias[$i]->items)-1; $j++){
            if($categorias[$i]->items[$j]->nota == '-'){
               $porcentaje_sin_nota += $categorias[$i]->items[$j]->porcentaje;
               $categorias[$i]->items[$j]->porcentaje = 0;
            }else{
               $items_calif++; 
            }
        }
        
        $porcentaje_a_repart = $porcentaje_sin_nota / $items_calif;
        
        //se recorren los items modificando los porcentajes
        for($k = 0; $k < count($categorias[$i]->items); $k++){
           if($categorias[$i]->items[$k]->nota != '-'){
                $categorias[$i]->items[$k]->porcentaje += $porcentaje_a_repart;
           }
        }
    }
}

function recalculate_totals($notas, $categs, $porcentajes){
    
    $categorias = make_categories($items, $notas, $categs, $porcentajes);
    
    recalculate_percentages($categorias);//REVISAR SI ES REALMENTE NECESARIO
    
    for($i = 0; $i < count($categorias); $i++){
        $total = 0;
        $ultimo = count($categorias[$i]->items);
        for($j = 0; $j < count($categorias[$i]->items); $j++){
            
            if($j == $ultimo){
                $categorias[$i]->items[$j]->nota = total;
            }else{
                if($categorias[$i]->items[$j]->nota != '-'){
                    $total += ($categorias[$i]->items[$j]->nota*$categorias[$i]->items[$j]->porcentaje/100);
                }
            }
        }
    }
    
    $new_notas = extract_notes($categorias);
    
    return $new_notas;
}

/** 
 *****************************
 Funciones gestión de usuarios
 *****************************
**/

function get_role_user($id_moodle, $idinstancia)
{
    global $DB;
    $current_semester = get_current_semester(); 
    $sql_query = "select nombre_rol, rol.id as rolid from {talentospilos_user_rol} as ur inner join {talentospilos_rol} as rol on rol.id = ur.id_rol where  ur.estado = 1 AND ur.id_semestre =".$current_semester->max."  AND id_usuario = ".$id_moodle." AND id_instancia =".$idinstancia.";";
    return $DB->get_record_sql($sql_query);
}

function get_permisos_role($idrol,$page){
    global $DB;
    
    $fun_str ="";
    switch ($page) {
        case "ficha":
            $fun_str = " AND  substr(fun.nombre_func,1,2) = 'f_';";
            break;
        case 'archivos':
            $fun_str = " AND fun.nombre_func = 'carga_csv';";
            break;
        case 'index':
            $fun_str = " AND fun.nombre_func = 'reporte_general';";
            break;
        case 'gestion_roles':
            $fun_str = " AND fun.nombre_func = 'gestion_roles';";
            break;
        case 'v_seguimiento_pilos':
            $fun_str = "AND fun.nombre_func = 'v_seguimiento_pilos';";
            break;
            case 'v_general_reports':
            $fun_str = "AND fun.nombre_func = 'v_general_reports';";
            break;
        default:
            // code...
            break;
    }
    
    
    $sql_query = "select pr.id as prid , fun.id as funid,* from {talentospilos_permisos_rol} as pr inner join {talentospilos_funcionalidad} as fun on id_funcionalidad = fun.id inner join {talentospilos_permisos} p  on id_permiso = p.id inner join {talentospilos_rol} r on r.id = id_rol   where id_rol=".$idrol.$fun_str;
    //print_r($sql_query);
    $result_query = $DB->get_records_sql($sql_query);
    //print_r(json_encode($result_query));
    
    return $result_query;
}
//get_permisos_role(221,'role');


/**
 * Función que elimina un registro grupal tanto en 
 * las tablas {talentospilos_seg_estudiante}
 * {talentospilos_seguimientos} dado un id de seguimiento.
 * @see delete_seguimiento_grupal($id)
 * @return 0 o 1
 */
function delete_seguimiento_grupal($id){
    
    global $DB;

    $sql_query = "DELETE FROM {talentospilos_seg_estudiante} WHERE id_seguimiento ='$id'";
    $success = $DB->execute($sql_query);
    $sql_query = "DELETE FROM {talentospilos_seguimiento} WHERE id = $id";
    $success = $DB->execute($sql_query);
    return $success;
}




/*
*********************************************************************************
FUNCIONES RELACIONADAS CON EL ROL PROFESIONAL PSICOEDUCATIVO
*********************************************************************************
*/

 
 /**
 * Función que actualiza en l
 *
 * @see assign_professional_user($id_user, $professional)
 * @return Integer
 */
 
 

/**
 * Función que asigna el rol profesional psicoeducativo y el tipo de profesional 
 *
 * @see update_role_profesional_ps($username, $role, $professional)
 * @return booleano confirmando el éxito de la operación
 */

function assign_role_professional_ps($username, $role, $state = 1, $semester, $username_boss = null, $professional)
{
    global $DB;
    
    $sql_query = "SELECT id FROM {user} WHERE username ='$username';";
    $id_user = $DB->get_record_sql($sql_query);
    
    $sql_query = "SELECT id_rol FROM {talentospilos_user_rol} WHERE id_usuario = '$id_user->id';";
    $id_current_role = $DB->get_record_sql($sql_query);
    
    if(empty($id_current_role)){
        
        // Start db transaction
        pg_query("BEGIN") or die("Could not start transaction\n");
        
        assign_role_user($username, $role, $state, $semester->max, null);
        
        assign_professional_user($id_user->id, $professional);
        
        // End db transaction
        pg_query("COMMIT") or die("Transaction commit failed\n");
        
        //print_r("funcinoa");
    }
}

function getProfessionals($id = null, $idinstancia){
    global $DB;
    // $sql_query = "SELECT username, firstname, lastname, us.id, prof.nombre_profesional 
    //               FROM {user} us INNER JOIN  {talentospilos_usuario_prof} p 
    //                                     ON p.id_usuario = us.id INNER JOIN {talentospilos_profesional} prof on prof.id = p.id_profesional 
    //                              INNER JOIN {talentospilos_user_rol} ur ON ur.id_usuario = us.id WHERE ur.id_instancia =".$idinstancia;
    
    $sql_query = "SELECT username, firstname, lastname, id 
                  FROM {user} us  WHERE id IN 
                  (SELECT id_usuario FROM {talentospilos_user_rol} ur WHERE id_rol IN (3,7) AND ur.id_instancia =".$idinstancia.")";
    
    if($id != null) $sql_query .= " AND us.id =".$id.";";
    return $DB->get_records_sql($sql_query);
}



/**
 * Función que retorna el id del profesional asignado a un estudiante
 *
 * @see get_id_assigned_professional($id_student)
 * @parameters $id_student int Id relacionado en la tabla {talentospilos_usuario}
 * @return int Returns professional id or 0 if the student does not have a professional assigned
 */
 
 function get_id_assigned_professional($id_student){
     
    global $DB;
     
    $sql_query = "SELECT id_monitor FROM {talentospilos_monitor_estud} WHERE id_estudiante =".$id_student.";";
    $id_monitor = $DB->get_record_sql($sql_query);
    
    $id_professional = "";
    
    if($id_monitor){

        $sql_query = "SELECT id_jefe FROM {talentospilos_user_rol} WHERE id_usuario = ".$id_monitor->id_monitor.";";
        $id_practicante = $DB->get_record_sql($sql_query)->id_jefe; 
        
        $sql_query = "SELECT id_jefe FROM {talentospilos_user_rol} WHERE id_usuario = ".$id_practicante.";";
        $id_professional = $DB->get_record_sql($sql_query)->id_jefe;

        if($id_professional == ""){
            $id_professional = 0;
        }
    }else{
        $id_professional = 0;
    }
    
    return $id_professional;
 }
 


/**
 * Función que retorna el id de practicante asignado a un estudiante
 *
 * @see get_id_assigned_pract($id_student)
 * @parameters $id_student int Id relacionado en la tabla {talentospilos_usuario}
 * @return String Nombre completo del practicante asignado
 */

 function get_id_assigned_pract($id_student){
     global $DB;
     
     $sql_query = "SELECT id_monitor FROM {talentospilos_monitor_estud} WHERE id_estudiante =".$id_student.";";
     $id_monitor = $DB->get_record_sql($sql_query)->id_monitor;
     
     if($id_monitor){
         $sql_query = "SELECT id_jefe FROM {talentospilos_user_rol} WHERE id_usuario = ".$id_monitor.";";
         $id_practicante = $DB->get_record_sql($sql_query)->id_jefe; 
         
         if($id_practicante == ""){
             $id_practicante = 0;
         }
         
     }else{
         $id_practicante = 0;
     }
     return $id_practicante;     
 }



/*
*********************************************************************************
FIN FUNCIONES RELACIONADAS CON EL ROL PROFESIONAL PSICOEDUCATIVO
*********************************************************************************
*/

/**
 * Función que elimina el ultimo registro de una tabla
 *
 * @see delete_last_register($table_name)
 * @return booleano confirmando el éxito de la operación
 */

function delete_last_register($table_name){
    
    global $DB;
    
    $sql_query = "SELECT MAX(id) FROM {$table_name}";
    $max_id = get_record_sql($sql_query);
    
    $sql_query = "DELETE FROM {$table_name} WHERE id = $max_id->max";
    $success = $DB->execute($sql_query);
    
    return $success;
}


/**
 * Función que actualiza el tipo de profesional de un usuario
 *
 * @see update_professional_type()
 * @return booleano confirmando el éxito de la operación
 */
 
 function update_professional_type($id_user, $name_prof)
 {
     global $DB;
     
     $sql_query = "SELECT id FROM {talentospilos_profesional} WHERE nombre_profesional = $name_prof";
     $id_profesional = $DB->get_record_sql($sql_query);
     
     $object = new stdClass();
     $object->id_usuario = $id_user;
     $object->id_profesional = $id_profesional->id;
     
     $update = $DB->update_record('talentospilos_usuario_prof', $object);
     
     return $update;
 }
 
 /**
 * Función que verifica si un registro existe en la tabla usuario_profesional
 *
 * @see record_check_professional($id_user, $id_professional)
 * @return boolean
 */
 
 function record_check_professional($id_user)
 {
     global $DB;
     
     $sql_query = "SELECT id FROM {talentospilos_usuario_prof} WHERE id_usuario = $id_user";
     $check = $DB->get_record_sql($sql_query);
     
     //print_r(empty($check));
 }








/** 
 ***********************************
 Fin consultas gestión de  usuarios
 ***********************************
**/

/** 
 **********************
 Consultas asistencias 
 **********************
**/

/**
 * Función que retorna un arreglo con las faltas justificadas e injustificadas
 * de cada estudiante del plan Talentos Pilos
 *
 * @see general_attendance()
 * @return array de objetos con las faltas justificas e injustificadas de un estudiante
 */
function general_attendance($programa, $semestre)
{
    global $DB;

    $user_report = array();
    
    $sql_query = "SELECT id FROM {talentospilos_semestre} WHERE nombre ='$semestre';";
    $id_semestre = $DB->get_record_sql($sql_query);

    $sql_query = "SELECT id FROM {talentospilos_programa} WHERE cod_univalle = '$programa'";
    $id_program = $DB->get_record_sql($sql_query);
    
    $sql_query = "SELECT SUBSTRING(tuser.username FROM 1 FOR 7) AS codigoestudiante, tuser.lastname AS apellidos, tuser.firstname AS nombres, count(tattendancestatuses.description) AS faltasinjustificadas
                  FROM {user_info_field} AS tinfofield INNER JOIN {user_info_data} AS tinfodata ON tinfodata.fieldid = tinfofield.id
                                                       INNER JOIN {user} AS tuser ON tuser.id = tinfodata.userid
                                                       INNER JOIN {attendance_log} AS tattendancelog ON tuser.id = tattendancelog.studentid
                                                       INNER JOIN {attendance_statuses} AS tattendancestatuses ON tattendancestatuses.id = tattendancelog.statusid
                  WHERE tinfofield.shortname = 'idprograma' AND tinfodata.data = '$id_program->id' 
                                                            AND tattendancestatuses.description = 'Falta injustificada'
                                                            AND to_timestamp(tattendancelog.timetaken) > (SELECT fecha_inicio::DATE - INTERVAL '30 days' FROM {talentospilos_semestre} WHERE id = $id_semestre->id) 
                                                            AND to_timestamp(tattendancelog.timetaken) < (SELECT fecha_fin::DATE FROM {talentospilos_semestre} WHERE id = $id_semestre->id)
                  GROUP BY codigoestudiante, apellidos, nombres;";
                 
    $attendance_report = $DB->get_records_sql($sql_query, null);
    
    // print_r($attendance_report);
    
    $sql_query = "SELECT SUBSTRING(tuser.username FROM 1 FOR 7) AS codigoestudiante, tuser.lastname AS apellidos, tuser.firstname AS nombres, count(tattendancestatuses.description) AS faltasjustificadas
                  FROM {user_info_field} AS tinfofield INNER JOIN {user_info_data} AS tinfodata ON tinfodata.fieldid = tinfofield.id
                                                       INNER JOIN {user} AS tuser ON tuser.id = tinfodata.userid
                                                       INNER JOIN {attendance_log} AS tattendancelog ON tuser.id = tattendancelog.studentid
                                                       INNER JOIN {attendance_statuses} AS tattendancestatuses ON tattendancestatuses.id = tattendancelog.statusid
                  WHERE tinfofield.shortname = 'idprograma' AND tinfodata.data = '$id_program->id' 
                                                            AND tattendancestatuses.description = 'Falta justificada'
                                                            AND to_timestamp(tattendancelog.timetaken) > (SELECT fecha_inicio::DATE - INTERVAL '30 days' FROM {talentospilos_semestre} WHERE id = $id_semestre->id) 
                                                            AND to_timestamp(tattendancelog.timetaken) < (SELECT fecha_fin::DATE FROM {talentospilos_semestre} WHERE id = $id_semestre->id)
                  GROUP BY codigoestudiante, apellidos, nombres;";
                
    $attendance_report_justified = $DB->get_records_sql($sql_query, null);
    
    foreach ($attendance_report as $report)
    {
        $count = 0;
        foreach($attendance_report_justified as $justified)
        {
            if($report->codigoestudiante == $justified->codigoestudiante)
            {
                $report->faltasjustificadas = $justified->faltasjustificadas;
                unset($attendance_report_justified[$justified->codigoestudiante]);
                $count = $count + 1;
                break;
            }
        }
        if($count == 0)
        {
            $report->faltasjustificadas = 0;
        }
        
    }
    foreach($attendance_report_justified as $justified)
    {
        $justified->faltasinjustificadas = 0;
    }

    $result = array_merge($attendance_report, $attendance_report_justified);
    
    foreach($result as $val)
    {
        $val->totalfaltas = (int) $val->faltasjustificadas + (int)$val->faltasinjustificadas;
    }
    
    $array_result = array();
    
    foreach($result as $object){
        $array_result[] = $object;
    }
    
    
    //print_r($result);
    return $array_result;
}
//general_attendance('1008', '2016B');
//general_attendance('1008', '2017A');
// general_attendance('1008', '2016B');

/**
 * Función que retorna las faltas de cada en estudiante en cada curso 
 * monitoreado desde el Plan Talentos Pilos
 *
 * @see attendance_by_course()
 * @return array de objetos con las faltas justificas e injustificadas de un estudiante por curso matriculado
 */
function attendance_by_course($code_student)
{
    global $DB;
    
    $user_report = array();
    
    $sql_query = "SELECT id FROM {user} WHERE username LIKE '$code_student%'";
    $id_user_moodle = $DB->get_record_sql($sql_query);
    
    $sql_query = "SELECT id FROM {talentospilos_semestre} WHERE nombre ='".get_current_semester()->nombre."';";
    $id_current_semester = $DB->get_record_sql($sql_query);
    
    $sql_query = "SELECT fecha_inicio::DATE FROM {talentospilos_semestre} WHERE id = $id_current_semester->id";
    $start_date = $DB->get_record_sql($sql_query);
    
    $sql_query = "SELECT fecha_fin FROM {talentospilos_semestre} WHERE id = $id_current_semester->id";
    $end_date = $DB->get_record_sql($sql_query);
    
    
    // $sql_query = "SELECT courses.timecreated AS tcreated
    //               FROM {user_enrolments} AS userEnrolments INNER JOIN {enrol} AS enrols ON userEnrolments.enrolid = enrols.id 
    //                                                       INNER JOIN {course} AS courses ON enrols.courseid = courses.id 
    //               WHERE userEnrolments.userid = $id_user_moodle->id";
                          
    // $courses = $DB->get_record_sql($sql_query);

    $sql_query = "SELECT courses.id AS idcourse, courses.fullname AS coursename, COUNT(attendancestatuses.description) AS injustifiedabsence
                  FROM {user_enrolments} AS userEnrolments INNER JOIN {enrol} AS enrols ON userEnrolments.enrolid = enrols.id
                                              INNER JOIN {course} AS courses ON enrols.courseid = courses.id
                                              INNER JOIN {talentospilos_semestre} AS semesters ON  (to_timestamp(courses.timecreated) > semesters.fecha_inicio::DATE - INTERVAL '30 days'
                                                                                                    AND (to_timestamp(courses.timecreated) < semesters.fecha_fin::DATE))
                                              INNER JOIN {attendance_log} AS attendancelog ON userEnrolments.userid = attendancelog.studentid
                                              INNER JOIN {attendance_statuses} AS attendancestatuses ON attendancelog.statusid = attendancestatuses.id
                                              INNER JOIN {attendance} AS att ON attendancestatuses.attendanceid = att.id 
                  WHERE userEnrolments.userid = $id_user_moodle->id AND semesters.id = $id_current_semester->id
                                     AND attendancestatuses.description = 'Falta injustificada'
                                     AND courses.id = att.course
                  GROUP BY idcourse, coursename";
                    
    $attendance_report_injustified = $DB->get_records_sql($sql_query, null);

    $sql_query = "SELECT courses.id AS idcourse, courses.fullname AS coursename, COUNT(attendancestatuses.description) AS justifiedabsence
                  FROM {user_enrolments} AS userEnrolments INNER JOIN {enrol} AS enrols ON userEnrolments.enrolid = enrols.id
                                              INNER JOIN {course} AS courses ON enrols.courseid = courses.id
                                              INNER JOIN {talentospilos_semestre} AS semesters ON  (to_timestamp(courses.timecreated) > semesters.fecha_inicio::DATE - INTERVAL '30 days'
                                                                                                    AND (to_timestamp(courses.timecreated) < semesters.fecha_fin::DATE))
                                              INNER JOIN {attendance_log} AS attendancelog ON userEnrolments.userid = attendancelog.studentid
                                              INNER JOIN {attendance_statuses} AS attendancestatuses ON attendancelog.statusid = attendancestatuses.id
                                              INNER JOIN {attendance} AS att ON attendancestatuses.attendanceid = att.id 
                  WHERE userEnrolments.userid = $id_user_moodle->id AND semesters.id = $id_current_semester->id
                                     AND attendancestatuses.description = 'Falta justificada'
                                     AND courses.id = att.course
                  GROUP BY idcourse, coursename";

    $attendance_report_justified = $DB->get_records_sql($sql_query, null);
    
    foreach ($attendance_report_injustified as $report)
    {
        $count = 0;
        foreach($attendance_report_justified as $justified)
        {
            if($report->coursename == $justified->coursename)
            {
                $report->justifiedabsence = $justified->justifiedabsence;
                unset($attendance_report_justified[$justified->idcourse]);
                $count = $count + 1;
                break;
            }
        }
        if($count == 0)
        {
            $report->justifiedabsence = 0;
        }
        
    }
    foreach($attendance_report_justified as $justified)
    {
        $justified->injustifiedabsence = 0;
    }
    
    $result = array_merge($attendance_report_injustified, $attendance_report_justified);
    
    foreach($result as $val)
    {
        $val->total = (int)$val->justifiedabsence + (int)$val->injustifiedabsence;
    }
    
    // print_r($result);
    
    return $result;
}

// Testing
// attendance_by_course('1673003');

/**
 * Función que retorna las faltas de cada en estudiante en cada semestre cursado
 * exceptuando el semestre actual
 *
 * @see attendance_by_semester()
 * @return array de objetos con las faltas justificas e injustificadas de un estudiante por semestre cursado exceptuando el actual
 * 
 */
 function attendance_by_semester($code_student) 
 {
    global $DB;
    
    $user_report = array();
    
    $sql_query = "SELECT id FROM {user} WHERE username LIKE '$code_student%'";
    $id_user_moodle = $DB->get_record_sql($sql_query);
    
    $sql_query = "SELECT id FROM {talentospilos_semestre} WHERE nombre='".get_current_semester()->nombre."';";
    $id_current_semester = $DB->get_record_sql($sql_query);

    $sql_query = "SELECT coursesSemester.semesterid AS idsemester, coursesSemester.semestersname AS semestername, COUNT({attendance_statuses}.description) AS injustifiedabsence 
                  FROM ({attendance} INNER JOIN {attendance_sessions} ON {attendance}.id = {attendance_sessions}.attendanceid)
                                    INNER JOIN {attendance_log} ON {attendance_sessions}.id = {attendance_log}.sessionid INNER JOIN {attendance_statuses} ON {attendance_log}.statusid = {attendance_statuses}.id
                                    INNER JOIN {user} ON {attendance_log}.studentid = {user}.id
                                    INNER JOIN {course} ON {course}.id = {attendance}.course
                                    INNER JOIN (SELECT {user_info_data}.userid, {user_info_data}.data  
                                                FROM {user_info_data} INNER JOIN {user_info_field} ON {user_info_data}.fieldid = {user_info_field}.id 
                                                WHERE {user_info_field}.shortname = 'idtalentos') AS fieldsadd
                                        ON fieldsadd.userid = {user}.id
                                    INNER JOIN (SELECT courses.id AS idcourse, courses.timecreated AS tcreated, semesters.id AS semesterid, semesters.nombre AS semestersname
                                                FROM {user_enrolments} AS userEnrolments INNER JOIN {enrol} AS enrols ON userEnrolments.enrolid = enrols.id 
                                                                                         INNER JOIN {course} AS courses ON enrols.courseid = courses.id
                                                                                         INNER JOIN {talentospilos_semestre} AS semesters ON (to_timestamp(courses.timecreated) > semesters.fecha_inicio::DATE - INTERVAL '30 days'
                                                                                                                                              AND (to_timestamp(courses.timecreated) < semesters.fecha_fin::DATE)) 
                                                WHERE userEnrolments.userid = $id_user_moodle->id) AS coursesSemester ON coursesSemester.idcourse = {course}.id
                  WHERE {attendance_statuses}.description = 'Falta injustificada' AND coursesSemester.semesterid <> $id_current_semester->id 
                  GROUP BY idsemester, semestername;";
                    
    $attendance_report_injustified = $DB->get_records_sql($sql_query, null);
    
    $sql_query = "SELECT coursesSemester.semesterid AS idsemester, coursesSemester.semestersname AS semestername, COUNT({attendance_statuses}.description) AS justifiedabsence 
                  FROM ({attendance} INNER JOIN {attendance_sessions} ON {attendance}.id = {attendance_sessions}.attendanceid)
                                    INNER JOIN {attendance_log} ON {attendance_sessions}.id = {attendance_log}.sessionid INNER JOIN {attendance_statuses} ON {attendance_log}.statusid = {attendance_statuses}.id
                                    INNER JOIN {user} ON {attendance_log}.studentid = {user}.id
                                    INNER JOIN {course} ON {course}.id = {attendance}.course
                                    INNER JOIN (SELECT {user_info_data}.userid, {user_info_data}.data  
                                                FROM {user_info_data} INNER JOIN {user_info_field} ON {user_info_data}.fieldid = {user_info_field}.id 
                                                WHERE {user_info_field}.shortname = 'idtalentos') AS fieldsadd
                                        ON fieldsadd.userid = {user}.id
                                    INNER JOIN (SELECT courses.id AS idcourse, courses.timecreated AS tcreated, semesters.id AS semesterid, semesters.nombre AS semestersname
                                                FROM {user_enrolments} AS userEnrolments INNER JOIN {enrol} AS enrols ON userEnrolments.enrolid = enrols.id 
                                                                                         INNER JOIN {course} AS courses ON enrols.courseid = courses.id
                                                                                         INNER JOIN {talentospilos_semestre} AS semesters ON (to_timestamp(courses.timecreated) > semesters.fecha_inicio::DATE - INTERVAL '30 days'
                                                                                                                                              AND (to_timestamp(courses.timecreated) < semesters.fecha_fin::DATE)) 
                                                WHERE userEnrolments.userid = $id_user_moodle->id) AS coursesSemester ON coursesSemester.idcourse = {course}.id
                  WHERE {attendance_statuses}.description = 'Falta justificada' AND coursesSemester.semesterid <> $id_current_semester->id
                  GROUP BY idsemester, semestername;";
                    
    $attendance_report_justified = $DB->get_records_sql($sql_query, null);
    
    foreach ($attendance_report_injustified as $report)
    {
        $count = 0;
        foreach($attendance_report_justified as $justified)
        {
            if($report->idsemester == $justified->idsemester)
            {
                $report->justifiedabsence = $justified->justifiedabsence;
                unset($attendance_report_justified[$justified->idsemester]);
                $count = $count + 1;
                break;
            }
        }
        if($count == 0)
        {
            $report->justifiedabsence = 0;
        }
        
    }
    foreach($attendance_report_justified as $justified)
    {
        $justified->injustifiedabsence = 0;
    }
    
    $result = array_merge($attendance_report_injustified, $attendance_report_justified);
    
    foreach($result as $val)
    {
        $val->total = (int)$val->justifiedabsence + (int)$val->injustifiedabsence;
    }
    // /($result);
    return $result;
}

//Testing
// attendance_by_semester('1673003'); 

 /**
 * Función que retorna el semestre actual a partir de la fecha del sistema
 *
 * @see get_current_semester()
 * @return string cadena de texto que representa el semestre actual
 */
function get_current_semester_by_date()
{
  $time = time();
  $current_mont = date("m", $time);
  $current_year = date("Y", $time);
  
  if($current_mont > 1 && $current_mont < 7)
  {
      $current_semester = $current_year."A";
  }
  else if($current_mont > 6 && $current_mont <= 12)
  {
      $current_semester = $current_year."B";
  }
  else
  {
      $current_semester = "Error al calcular el semestre actual";
  }
  
  return $current_semester;
}

//  /**
//  * Función que retorna el semestre actual 
//  *
//  * @see get_current_semester()
//  * @return cadena de texto que representa el semestre actual
//  */
 
//  function get_current_semester(){
     
//      global $DB;
     
//      $sql_query = "SELECT id AS max, nombre FROM {talentospilos_semestre} WHERE id = (SELECT MAX(id) FROM {talentospilos_semestre})";
     
//      $current_semester = $DB->get_record_sql($sql_query);
     
//      return $current_semester;
//  }
/** 
 **********************
 Fin consultas asistencias 
 **********************
**/

function getConcurrentCohortsSPP($idinstancia){
    global $DB;
    $infoinstancia = consultInstance($idinstancia);
    $asescohorts = "";
    if($infoinstancia->cod_univalle == 1008){
        $asescohorts = "OR idnumber LIKE 'SP%'";
    }
    
    $sql_query="SELECT idnumber, name, timecreated FROM {cohort} WHERE idnumber LIKE '".$infoinstancia->cod_univalle."%' ".$asescohorts." ORDER BY timecreated DESC;";
    $result = $DB->get_records_sql($sql_query);
    return $result;
}

function getConcurrentEnfasisSPP(){
    global $DB;
    $sql_query="SELECT  nombre FROM {talentospilos_enfasis};";
    $result = $DB->get_records_sql($sql_query);
    return $result;
}

function insertSeguimiento($object, $id_est){
    global $DB;
    $id_seg = $DB->insert_record('talentospilos_seguimiento', $object,true);
    
    //se relaciona el seguimiento con el estudiant
    insertSegEst($id_seg, $id_est);
    
    //se actualiza el riesgo
    if($object->tipo == 'PARES'){
        foreach ($id_est as $idStudent) {
            updateRisks($object, $idStudent);
        }
    }
    
    return true;
}

function updateRisks($segObject, $idStudent){
    global $DB;
    
    //se crea un arraglo que contien la informacion a actualizar
    $array_student_risks = array();
    
    if($segObject->vida_uni_riesgo){
        update_array_risk($array_student_risks,'vida_universitaria', $segObject->vida_uni_riesgo,$idStudent);
    }
    
    if($segObject->economico_riesgo){
        update_array_risk($array_student_risks,'economico', $segObject->economico_riesgo,$idStudent);
    }
    
    if($segObject->academico_riesgo){
        update_array_risk($array_student_risks,'academico', $segObject->academico_riesgo,$idStudent);
    }
    
    if($segObject->familiar_riesgo){
        update_array_risk($array_student_risks,'familiar', $segObject->familiar_riesgo,$idStudent);
    }
    
    if($segObject->individual_riesgo){
        update_array_risk($array_student_risks,'individual', $segObject->individual_riesgo,$idStudent);
    }
    
    foreach($array_student_risks as $sr){
        $sql_query ="SELECT riesg_stud.id as id FROM {talentospilos_riesg_usuario} riesg_stud WHERE riesg_stud.id_usuario=".$idStudent." AND riesg_stud.id_riesgo=".$sr->id_riesgo;
        $exists = $DB->get_record_sql($sql_query);
        
        if($exists){
            $sr->id = $exists->id;
            $DB->update_record('talentospilos_riesg_usuario',$sr);
        }else{
            $DB->insert_record('talentospilos_riesg_usuario',$sr);
        }
    }
    return true;
}

function update_array_risk(&$array_student_risks, $name_risk, $calificacion, $idstudent){
    global $DB;
    //Se obtienen los riegos disponible
    $sql_query = "SELECT * FROM {talentospilos_riesgos_ases}";
    $array_risks = $DB->get_records_sql($sql_query);
    
    foreach($array_risks as $risk){
        if($name_risk == $risk->nombre){
            $object =  new stdClass();
            $object->id_usuario = $idstudent;
            $object->id_riesgo = $risk->id;
            $object->calificacion_riesgo = $calificacion;
            array_push($array_student_risks, $object);
        }
    }
}


function insertSegEst($id_seg, $id_est){
    global $DB;
    $object_seg_est = new stdClass();
    $id_seg_est = false;
    foreach ($id_est as $id){
        $object_seg_est->id_estudiante = $id;
        $object_seg_est->id_seguimiento = $id_seg;
        
        $id_seg_est= $DB->insert_record('talentospilos_seg_estudiante', $object_seg_est,true);
    }
    return $id_seg_est;
}

function getSeguimiento($id_est, $id_seg, $tipo, $idinstancia){
    global $DB;
    
    // print_r($id_est);
    // print_r($id_seg);
    // print_r($tipo);
    // print_r($idinstancia);
    
    $sql_query="SELECT *, seg.id as id_seg from {talentospilos_seguimiento} seg INNER JOIN {talentospilos_seg_estudiante} seges  on seg.id = seges.id_seguimiento  where seg.tipo ='".$tipo."' ;";
    
    if($idinstancia != null ){
        $sql_query =  trim($sql_query,";");    
        $sql_query .= " AND seg.id_instancia=".$idinstancia." ;";
    }
    
    if($id_est != null){
        $sql_query = trim($sql_query,";");
        $sql_query .= " AND seges.id_estudiante =".$id_est.";";
    }
    
    // if($id_est != null){
    //     $sql_query = trim($sql_query,";");
    //     $sql_query .= " AND seges.id_estudiante =".$id_est.";";
    // }
    
    if($id_seg != null){
      $sql_query = trim($sql_query,";");
      $sql_query.= " AND seg.id =".$id_seg.";";
   
    }
    
    // var_dump($DB->get_records_sql($sql_query));
    //print_r($sql_query);
    //print_r($DB->get_records_sql($sql_query));
    
   return $DB->get_records_sql($sql_query);
}

//getSeguimiento(169);

function getSeguimientoOrderBySemester($id_est = null, $tipo,$idsemester = null, $idinstancia = null){
    global $DB;
    $result = getSeguimiento($id_est, null,$tipo, $idinstancia );
    
    $seguimientos = array();
    foreach ($result as $r){
        array_push($seguimientos, $r);
    }
    
    $lastsemestre = false;
    $firstsemester=false;
    
    $sql_query = "select * from {talentospilos_semestre} ";
    if($idsemester != null){
        $sql_query .= " WHERE id = ".$idsemester;
    }else{
        $userid = $DB->get_record_sql("select userid from {user_info_data} d inner join {user_info_field} f on d.fieldid = f.id where f.shortname='idtalentos' and d.data='$id_est';");
        $firstsemester = getIdFirstSemester($userid->userid);
        $lastsemestre = getIdLastSemester($userid->userid);
        //print_r($firstsemester."-last:".$lastsemestre);
        
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
                $array_segumietos = array();
                
                while(compare_dates(strtotime($semester->fecha_inicio), strtotime($semester->fecha_fin),$seguimientos[$counter]->created)){
                    
                    array_push($array_segumietos, $seguimientos[$counter]);
                    $counter+=1;
                    
                    if ($counter == count($seguimientos)){
                        break;
                    }
                    
                }
                
                foreach($array_segumietos as $r){
                    $r->fecha = date('d-m-Y', $r->fecha);
                    $r->created = date('d-m-Y', $r->created);
                }

                // $semester_object->promedio = getPormStatus($id_est,$semester->id)->promedio;
                $semester_object->result = $array_segumietos;
                $semester_object->rows = count($array_segumietos);
                array_push($array_semesters_seguimientos, $semester_object);
            }
        }
        
    }
    
    $object_seguimientos =  new stdClass();
    
    $promedio = getPormStatus($id_est);
    $object_seguimientos->promedio = $promedio->promedio;
    $object_seguimientos->semesters_segumientos = $array_semesters_seguimientos;
    
    //print_r($object_seguimientos);
    return $object_seguimientos;
}

//getSeguimientoOrderBySemester(169,'PARES');




function getSegumientoByMonitor($id_monitor, $id_seg= null, $tipo, $idinstancia){
    global $DB;
    $sql_query= "";
        $sql_query="SELECT seg.id as id_seg, to_timestamp(fecha) as fecha_formato,*  from {talentospilos_seguimiento} seg  where seg.id_monitor = ".$id_monitor." AND seg.tipo = '".$tipo."' AND seg.id_instancia=".$idinstancia." ORDER BY fecha_formato DESC;";

    if($id_seg != null){
      $sql_query = trim($sql_query,";");
      $sql_query.= " AND seg.id =".$id_seg.";";

   
    }
   return $DB->get_records_sql($sql_query);
}

// getSegumientoByMonitor(120, null, 'PARES', 19);

function getPormStatus($id, $idsemester = null){
    global $DB;
    
    $seguimientos_pares = array();

    $sql_query ="select seg.id,  status, seg.created from {talentospilos_seguimiento} seg INNER JOIN {talentospilos_seg_estudiante}  seg_es on seg_es.id_seguimiento = seg.id where seg.tipo='PARES' AND seg_es.id_estudiante=$id;";
    
    $semester_result= null;
    if($idsemester){
        $semester_query = "SELECT * from {talentospilos_semestre} where id=".$idsemester;
        $semester_result = $DB->get_record_sql($semester_query);
        $seguimientos =  $DB->get_records_sql($sql_query);
        foreach($seguimientos as $seg){
            if(compare_dates(strtotime($semester_result->fecha_inicio), strtotime($semester_result->fecha_fin),$seg->created)){
                array_push($seguimientos_pares, $seg); 
            }
        }
    }
    
    $operadores_pares = new stdClass();
    $operadores_pares->counts = count($seguimientos_pares);
    
    $sum = 0;
    foreach($seguimientos_pares as $seg){
        $sum += $seg->status;
    }
    
    $operadores_pares->sum = $sum;
    
    $seguimientos_soc = array();
    //print_r($operadores_pares);
    $sql_query = "select id,  status, created from {talentospilos_seg_soc_educ} where id_estudiante =".$id.";";
    
    if($semester_result){
        $seguimientos =  $DB->get_records_sql($sql_query);
        foreach($seguimientos as $seg){
            if(compare_dates(strtotime($semester_result->fecha_inicio), strtotime($semester_result->fecha_fin),$seg->created)){
                array_push($seguimientos_soc, $seg); 
            }
        }
    }
    
    $operadores_socio = new stdClass();
    $operadores_socio->counts = count($seguimientos_soc);
    
    $sum = 0;
    foreach($seguimientos_soc as $seg){
        $sum += $seg->status;
    }
    
    $operadores_socio->sum = $sum;
    
    
    //print_r($operadores_socio);
    $result_pares = new stdClass();
    $result_socio = new stdClass();
    $total_promedio = new stdClass();
    $ponde_pares = 0.5;
    $ponde_socio = 0.5;
        
    if($operadores_pares->counts == 0){
        $operadores_pares->promedio = 1;
        $ponde_socio = 1;
        $ponde_pares = 0;
    }else{
        $promedio = $operadores_pares->sum / $operadores_pares->counts;
        $operadores_pares->promedio =  number_format($promedio,1);
    }
    
    
    if($operadores_socio->counts == 0){
        $operadores_socio->promedio = 1;
        $ponde_socio = 0;
        $ponde_pares = 1;
    }else{
        $promedio = $operadores_socio->sum / $operadores_socio->counts;
        $operadores_socio->promedio =  number_format($promedio,1);
    }    
        
        
    $promedio = $operadores_pares->promedio*$ponde_pares + $operadores_socio->promedio*$ponde_socio;
    $total_promedio->promedio =  number_format($promedio,1);
    

    
    if($operadores_socio->counts == 0 && $operadores_pares->counts == 0 ) $total_promedio->promedio = 0;
   
    //print_r($total_promedio);
    return $total_promedio;
}


function getEstudiantesSegGrupal($id_seg){
    global $DB;
    $sql_query = "SELECT id_estudiante FROM {talentospilos_seg_estudiante} WHERE id_seguimiento ='$id_seg'";
    return $DB->get_records_sql($sql_query);
}

function insertPrimerAcerca($object){
    global $DB;
    return $DB->insert_record('talentospilos_primer_acerca',$object);
}

function updatePrimerAcerca($object){
    global $DB;
    return $DB->update_record('talentospilos_primer_acerca', $object);
}

function getPrimerAcerca($idtalentos,$idinstancia){
    global $DB;
    $sql_query = "SELECT * FROM {talentospilos_primer_acerca} WHERE id_estudiante =".$idtalentos." AND id_instancia=".$idinstancia;
    return $DB->get_records_sql($sql_query);
}

function dropTalentosFromSeg($idSeg,$id_est){
    global $DB;
    $whereclause = "id_seguimiento =".$idSeg." AND id_estudiante=".$id_est;
    return $DB->delete_records_select('talentospilos_seg_estudiante',$whereclause);
}


function insertnewAcompaSocio($record){
    global $DB;
    return $DB->insert_record('talentospilos_socioeducativo',$record);
}

function insertInfoEconomica($infoEconomica){
    global $DB;
    $result = false;
    foreach ($infoEconomica as $object){
        $result = $DB->insert_record('talentospilos_economia', $object);
    }
    
    return $result; 
}
function insertInfoFamilia($infoFamilia){
    global $DB;
     $result = false;
    foreach ($infoFamilia as $object){
        $result = $DB->insert_record('talentospilos_familia', $object);
    }
    
    return $result; 
}

function getAcompaSocio($idtalentos,$idinstancia){
    global $DB;
    $sql_query = "SELECT * FROM {talentospilos_socioeducativo} WHERE id_estudiante =".$idtalentos." AND id_instancia=".$idinstancia.";";
    return $DB->get_records_sql($sql_query);
}

function getEconomia($idtalentos,$tipo ){
    global $DB;
    $sql_query = "SELECT * FROM {talentospilos_economia} WHERE id_estudiante =".$idtalentos." AND tipo='".$tipo."';";
    return $DB->get_records_sql($sql_query);
}

function getFamilia($idtalentos){
    global $DB;
    $sql_query = "SELECT * FROM {talentospilos_familia} WHERE id_estudiante =".$idtalentos.";";
    return $DB->get_records_sql($sql_query);
}

function updateAcompaSocio($object){
    global $DB;
    return $DB->update_record('talentospilos_socioeducativo', $object);
}

function updateInfoEconomica($object){
    global $DB;
    return $DB->update_record('talentospilos_economia', $object);
}

function updateInfoFamilia($object){
    global $DB;
    return $DB->update_record('talentospilos_familia', $object);
}

function dropInfoEconomica($idInfo){
    global $DB;
    $whereclause = "id =".$idInfo;
    //print_r($DB->delete_records_select('talentospilos_economia',$whereclause));
    return $DB->delete_records_select('talentospilos_economia',$whereclause);
}

function dropFamilia($idInfo){
    global $DB;
    $whereclause = "id =".$idInfo;
    //print_r($DB->delete_records_select('talentospilos_economia',$whereclause));
    return $DB->delete_records_select('talentospilos_familia',$whereclause);
}

function insertSegSocio($object){
    global $DB;
    return $DB->insert_record('talentospilos_seg_soc_educ',$object);
}

function updateSegSocio($object){
    global $DB;
    return $DB->update_record('talentospilos_seg_soc_educ', $object);
}

function getSegSocio($idtalentos,$idinstancia, $idseg = null){
    global $DB;
    $sql_query = "SELECT * FROM {talentospilos_seg_soc_educ} WHERE id_estudiante =".$idtalentos." AND id_instancia=".$idinstancia.";";
    if( $idseg != null){
        $sql_query = trim($sql_query,";");
        $sql_query.= " AND id =".$idseg.";";
    }
    //print_r($sql_query);
    return $DB->get_records_sql($sql_query);
}

function getSegSocioOrderBySemester($idtalentos,$idinstancia, $idseg = null, $idsemester= null){
    global $DB;
    $result = getSegSocio($idtalentos,$idinstancia, null);
    
    $seguimientos = array();
    
    foreach ($result as $r){
        array_push($seguimientos, $r);
    }
    
    foreach($seguimientos as $r){
        $r->fecha = date('d-m-Y', $r->fecha);
    }
    
    //print_r($seguimientos);
    
    $sql_query = "select * from {talentospilos_semestre} ";
    if($idsemester != null){
        $sql_query .= " WHERE id = ".$idsemester;
    }else{
        $userid = $DB->get_record_sql("select userid from {user_info_data} d inner join {user_info_field} f on d.fieldid = f.id where f.shortname='idtalentos' and d.data='$idtalentos';");
        $firstsemester = getIdFirstSemester($userid->userid);
        $sql_query .= " WHERE id >=".$firstsemester;
    }
    
    $sql_query.=" order by fecha_inicio DESC";
    
    $semesters = $DB->get_records_sql($sql_query);
    
    $object_seguimientos =  new stdClass();
    
    $array_semesters_seguimientos =  array();

    $counter = 0;
    foreach ($semesters as $semester){
        
        $semester_object = new stdClass;
        
        $semester_object->id_semester = $semester->id;
        $semester_object->name_semester = $semester->nombre;
        $array_segumietos = array();
        
        while(compare_dates(strtotime($semester->fecha_inicio), strtotime($semester->fecha_fin),$seguimientos[$counter]->created)){
            //print_r("fecha segumiento:".$seguimientos[$counter].)
            array_push($array_segumietos, $seguimientos[$counter]);
            $counter+=1;
            
            if ($counter == count($seguimientos)){
                break;
            }
            
        }
        
        $semester_object->result = $array_segumietos;
        $semester_object->rows = count($array_segumietos);
        array_push($array_semesters_seguimientos, $semester_object);
    }
    
    $promedio = getPormStatus($idtalentos);
    
    $object_seguimientos->promedio = $promedio->promedio;
    $object_seguimientos->semesters_segumientos = $array_semesters_seguimientos;
    //print_r("adaf<br>");
    //print_r($object_seguimientos);
    return $object_seguimientos;
}

//getSegSocioOrderBySemester(169);

function getUserMoodleByid($id){
    global $DB;
    $sql_query = "SELECT * FROM {user} WHERE id =".$id.";";
    return $DB->get_record_sql($sql_query);
}

/**
 * Return final grade of a course for a single student
 *
 * @param string $username_student Is te username of moodlesite 
 * @return array() of stdClass object representing courses and grades for single student
 */

function get_grades_courses_student_semester($id_student, $coursedescripctions){
    //print_r("<br><hr>".$id_student."<hr><br>");
    global $DB;
    
    // var_dump($id_student);
    
    $id_first_semester = getIdFirstSemester($id_student);
    
    // var_dump($id_first_semester);
    
    $semesters = get_semesters_student($id_first_semester);
    
    // var_dump($semesters);
    
    // print_r($semesters);
    
    $courses = \couse_lib\get_courses_by_student($id_student, $coursedescripctions);
    $array_semesters_courses =  array();
   
    $counter = 0;
    foreach ($semesters as $semester){
        
        $semester_object = new stdClass;
        
        $semester_object->id_semester = $semester->id;
        $semester_object->name_semester = $semester->nombre;
        $array_courses = array();
        
        $coincide =false;
        
        if ($courses){
            while($coincide = compare_dates(strtotime($semester->fecha_inicio), strtotime($semester->fecha_fin), strtotime( $courses[$counter]->time_created))){
                array_push($array_courses, $courses[$counter]);
                $counter+=1;
                
                if ($counter == count($courses)){
                    break;
                }
                
            }
        }
        if($coincide || $counter != 0){
            $semester_object->courses = $array_courses;
            array_push($array_semesters_courses, $semester_object);
        }
    }
    // print_r($array_semesters_courses);
    return $array_semesters_courses; 
}
 
// Test
// get_grades_courses_student_semester(10304);

function compare_dates($fecha_inicio, $fecha_fin, $fecha_comparar){
    
    $fecha_inicio = new DateTime(date('Y-m-d',$fecha_inicio));
    date_add($fecha_inicio, date_interval_create_from_date_string('-30 days'));
    
    // var_dump(strtotime($fecha_inicio->format('Y-m-d')));
    // var_dump($fecha_fin);
    // var_dump($fecha_comparar);
    //print_r(($fecha_comparar >= strtotime($fecha_inicio->format('Y-m-d'))) && ($fecha_comparar <= $fecha_fin));
    return (((int)$fecha_comparar >= strtotime($fecha_inicio->format('Y-m-d'))) && ((int)$fecha_comparar <= (int)$fecha_fin));
}

/**
 * Return array of semesters of a student
 *
 * @param string $username_student Is te username of moodlesite 
 * @return array() of stdClass object representing semesters of a student
 */
 function get_semesters_student($id_first_semester){
     
     global $DB;
     
     $sql_query = "SELECT id, nombre, fecha_inicio::DATE, fecha_fin::DATE FROM {talentospilos_semestre} WHERE id >= $id_first_semester ORDER BY {talentospilos_semestre}.fecha_inicio DESC";
     
     $result_query = $DB->get_records_sql($sql_query);
     
     $semesters_array = array();
     
     foreach ($result_query as $result){
       array_push($semesters_array, $result);
     }
    //print_r($semesters_array);
    return $semesters_array;
}


 /**
 * Return total of semesters 
 *
 * @param null
 * @return integer representing the total number of semesters registered in db
 */
 
 function get_total_numbers_semesters(){
     
     global $DB;
     
     $sql_query = "SELECT COUNT(id) FROM {talentospilos_semestre}";
     $total_semesters = $DB->get_record_sql($sql_query);

     return $total_semesters->count;
}

/**
 * Return id of first semester of a student
 *
 * @param int --- id student 
 * @return int --- id first semester
 */
function getIdFirstSemester($id){
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

// Testing
// getIdFirstSemester(103304);
// getIdFirstSemester(103268);
// print_r(getIdFirstSemester(171));
// getIdFirstSemester(171);
// getIdFirstSemester(10);

function getIdLastSemester($idmoodle){
    
    // print_r($idmoodle);
    // print_r("***");
    
    $id_first_semester = getIdFirstSemester($idmoodle);
    // print_r($id_first_semester);
    // print_r("*****************");
    $semesters = get_semesters_student($id_first_semester);
    // print_r($semesters);
    //$result = get_grades_courses_student_semester($idmoodle);
    if($semesters){
       return  $semesters[0]->id;
    }else{
        return false;
    }
    
}




//DATOS BASICOS
function getStudentInformation($idTalentos){
    global $DB;
    
    $sql_query = "SELECT usuario.id, usuario.firstname, infor_data.id, infor_data.data, infor_field.shortname, usuario_talentos.sexo, usuario_talentos.id_ciudad_ini, municipios_ini_talentos.nombre AS municipio_procedencia, departamentos_ini_talentos.nombre AS departamento_procedencia, usuario_talentos.id_ciudad_res, municipios_res_talentos.nombre AS municipio_residencia, departamentos_res_talentos.nombre AS departamento_residencia 
    FROM {user} AS usuario 
    INNER JOIN {user_info_data} AS infor_data 
    ON usuario.id = infor_data.userid 
    INNER JOIN {user_info}_field AS infor_field 
    ON infor_data.fieldid = infor_field.id 
    INNER JOIN {talentospilos_usuario} AS usuario_talentos 
    ON  cast(usuario_talentos.id AS varchar) = infor_data.data 
    INNER JOIN {talentospilos_municipio} AS municipios_res_talentos 
    ON municipios_res_talentos.id = usuario_talentos.id_ciudad_res 
    INNER JOIN (SELECT * FROM {talentospilos_municipio}) AS municipios_ini_talentos
    ON municipios_ini_talentos.id = usuario_talentos.id_ciudad_ini 
    INNER JOIN (SELECT * FROM {talentospilos_departamento}) AS departamentos_ini_talentos
    ON municipios_ini_talentos.cod_depto = departamentos_ini_talentos.id
    INNER JOIN (SELECT * FROM {talentospilos_departamento}) as departamentos_res_talentos
    ON municipios_res_talentos.cod_depto = departamentos_res_talentos.id
    WHERE infor_field.shortname = '"+$idTalentos+"';";
    
    $output = $DB->get_records_sql($sql_query);
    
    return $output;
}

/**
 * Realiza una consulta con el nombre de la categoria y nombre del curso para luego
 * generar un array con los nombres de las categorias
 *
 * @param $idCourse
 * @return Array
 */
function getCategories($idCourse)
{
    global $DB;
    
    $sql_query="SELECT {grade_categories}.id AS id,{grade_categories}.fullname AS nombre_categoria,{course}.fullname AS nombre_curso, {grade_categories}.aggregation AS tipo
                FROM {grade_categories} INNER JOIN {course} ON ({grade_categories}.courseid={course}.id) 
                WHERE courseid=".$idCourse.";";
    $output = $DB->get_records_sql($sql_query);
    
    $newArray=array();
    //print_r($output);
     foreach($output as &$categoria)
     {
         $arrayAuxiliar=array();
         array_push($arrayAuxiliar,$categoria->id);
         if($categoria->nombre_categoria=="?")
         {
             $ingresar=$categoria->nombre_curso;
             $tipoCalificacion=$categoria->tipo;
         }else
         {
            $ingresar= $categoria->nombre_categoria; 
            $tipoCalificacion=$categoria->tipo;
         }
         array_push($arrayAuxiliar,$ingresar);
         array_push($arrayAuxiliar,$tipoCalificacion);
         array_push($newArray,$arrayAuxiliar);
     }
    // print_r($newArray);
    return $newArray;
}

// getCategories(2);


/**
 * Realiza una consulta para encontrar el ultimo indice del elemento sort correspondiente
 * a la categoria que se esta ingresando
 *
 * @param $curso
 * @return int --- proximoIndice
 */
function sortItem($curso)
{
    global $DB;
    $sql_query = "SELECT max(sortorder) FROM {grade_items} WHERE courseid=".$curso.";";
    $output=$DB->get_record_sql($sql_query);
    $proximoIndice=($output->max)+1;
    //print_r($proximoIndice);
    return $proximoIndice;
}

/**
 * Realiza la insercion de una categoria considerando si es de tipo ponderado o no, luego de esto
 * inserta el item que representara a la categoria, este ultimo es necesario para que la categoria tenga un peso
 *
 * @param $curso
 * @param $padre
 * @param $nombre
 * @param $ponderado
 * @param $peso
 * @return String --- ok || error
 */
function insertarCategoria($curso,$padre,$nombre,$ponderado,$peso)
{
     global $DB;
    
    //se instancia un objeto y sus elementos para utilizar el metodo de insercion de moodle
    $object = new stdClass;
    $object ->courseid=$curso;
    $object ->fullname=$nombre;
    $object ->parent =$padre;
    $object ->aggregation=$ponderado;
    $object ->timecreated=time();
    $object ->timemodified=$object ->timecreated;
    $object->aggregateonlygraded = 0;
    $object->aggregateoutcomes = 0;

    //print_r($object);
    $succes=$DB->insert_record('grade_categories',$object);
    //print_r($succes);
    if($succes)
    {
      if(insertarItem($curso,$succes,$nombre,$peso,false)=="ok")
      {
        return "ok";    
      }else{
          return "error";
      }
    }
    return "error";
}

// insertarCategoria(105,126,'prueba insertar',10,60);



/**
 * Realiza la insercion de item, ya sea item plano o un item relacionado con una categoria,
 * este ultimo es necesario para poder asignarle un peso en caso de que la categoria
 * sea hija de otra categoria con calificacion ponderada
 *
 * @param $curso
 * @param $padre
 * @param $nombre
 * @param $ponderado
 * @param $peso
 * @return String --- ok || error
 */
function insertarItem($curso,$padre,$nombre,$valorEnviado,$item)
{
    global $DB;
    
    //se instancia un objeto y sus elementos para utilizar el metodo de insercion de moodle
    if($item)
    {
    $object = new stdClass;
    $object ->courseid=$curso;
    $object -> categoryid=$padre;
    $object ->itemname=$nombre;
    $object -> itemnumber=0;
    $object -> itemtype='manual';
    $object -> sortorder=sortItem($curso);
    $object -> aggregationcoef=$valorEnviado;
    $object -> grademax=5;
    }else{
    $object = new stdClass;
    $object ->courseid=$curso;
    $object -> itemtype='category';
    $object -> sortorder=sortItem($curso);
    $object -> aggregationcoef=$valorEnviado;
    $object -> iteminstance=$padre;
    $object -> grademax=5;
    }
    
    //print_r($object);
    
    $retorno=$DB->insert_record('grade_items',$object);
    //print_r($retorno);
    if($retorno)
    {
        return "ok";
    }else
    {
        return "error";
    }
    
}

/**
 * Funcion que retorna las categorias de un curso segun el shortname el cual 
 * se arma en funcion de el codigo de la asignatura,grupo,mes y año actual
 *
 * @param $Asignatura
 * @param $Grupo
 * @return Array
 */
function getCategoriesWithShortname($Asignatura,$Grupo)
{
    global $DB;
    $sql_query ="select max(id) FROM {talentospilos_semestre};";
    $maxid = $DB->get_record_sql($sql_query);
    $maxid= $maxid->max;
    
    //se extrae el mes del ultimo semestre registrado en la base de datos
    $sql_query ="select EXTRACT(month FROM fecha_inicio) AS mes FROM {talentospilos_semestre} WHERE id=$maxid;";
    $mes = $DB->get_record_sql($sql_query);
    $mes = $mes->mes;
    
    //se extrae el año del ultimo semestre registrado en la base de datos
    $sql_query ="select EXTRACT(year FROM fecha_inicio) AS anio FROM {talentospilos_semestre} WHERE id=$maxid;";
    $año = $DB->get_record_sql($sql_query);
    $año = $año->anio;
    
    //se arma el shortname
    $Shortname="'00-".$Asignatura."-".$Grupo."-".$año."0".$mes."041'";
    
    //se realiza la consulta
    $sql_query="SELECT {grade_categories}.id AS id,{course}.id as id_curso,{grade_categories}.fullname AS nombre_categoria,
                {course}.fullname AS nombre_curso,{grade_categories}.aggregation AS tipo,
                {course}.shortname AS shortname FROM {grade_categories} INNER JOIN {course} 
                ON ({grade_categories}.courseid={course}.id) 
                WHERE {course}.shortname=".$Shortname.";";
                
    $output = $DB->get_records_sql($sql_query);
    
    $newArray=array();
    
    //por cada elemento retornado de la consulta el cual es una categoria se 
     foreach($output as &$categoria)
     {
         $arrayAuxiliar=array();
         //se toma el id
         array_push($arrayAuxiliar,$categoria->id);
         //en caso que tenga un nombre de categoria asignado se toma dicho nombre, en caso contrario
         //significa que es el curso como tal y por ello se le da el nombre de este
         if($categoria->nombre_categoria=="?")
         {
             $ingresar=$categoria->nombre_curso;
             $tipoCalificacion=$categoria->tipo;
         }else
         {
            $ingresar= $categoria->nombre_categoria; 
            $tipoCalificacion=$categoria->tipo;
         }
         //se agregan los elementos a un array auxiliar y luego se añaden al array que se retornara
         array_push($arrayAuxiliar,$ingresar);
         array_push($arrayAuxiliar,$tipoCalificacion);
         array_push($arrayAuxiliar,$categoria->id_curso);
         array_push($newArray,$arrayAuxiliar);
     }
    //print_r($newArray);
    return $newArray;
}

//getCategoriesWithShortname(123456,"00");

/**
 * Funcion que extrae los ids de los usuarios perteneciente a un curso segun el codigo de la asignatura y el grupo
 *
 * @param $curso
 * @param $grupo
 * @param $usado
 * @return Array || String "error id"
 */
function idUsuariosCurso($curso,$grupo,$usado)
{   
    global $DB;
    $sql_query ="select max(id) FROM {talentospilos_semestre};";
    $maxid = $DB->get_record_sql($sql_query);
    $maxid= $maxid->max;
    
    //se extrae el mes del ultimo semestre registrado en la base de datos
    $sql_query ="select EXTRACT(month FROM fecha_inicio) AS mes FROM {talentospilos_semestre} WHERE id=$maxid;";
    $mes = $DB->get_record_sql($sql_query);
    $mes = $mes->mes;
    
    //se extrae el año del ultimo semestre registrado en la base de datos
    $sql_query ="select EXTRACT(year FROM fecha_inicio) AS anio FROM {talentospilos_semestre} WHERE id=$maxid;";
    $año = $DB->get_record_sql($sql_query);
    $año = $año->anio;
    
    //se arma el shortname
    $Shortname="'00-".$Asignatura."-".$Grupo."-".$año."0".$mes."041'";
    
    //se realiza la consulta
    $sql_query="SELECT u.id AS userid FROM {enrol} AS e INNER JOIN {user_enrolments} 
    ue ON (e.id=ue.enrolid) INNER JOIN {user} AS u ON (ue.userid = u.id) 
    INNER JOIN {course} AS c ON (e.courseid=c.id) WHERE c.shortname=".$Shortname."";
    
        
        //en caso que el metodo este siendo usado por otro se retorna el texto de la consulta
        //en caso contrario se realiza la consulta y se retorna el resultado
        $output=$DB->get_records_sql($sql_query);
        if($usado)
        {
            return $sql_query;
        }else
        {
            $output = $DB->get_records_sql($sql_query);
            if($output)
            {
                return $output;

            }else
            {
                return "error id";    
            }
        }
        
}

/**
 * Funcion que relaciona los cohortes con sus miembros
 * 
 * @param $usado
 * @return Array || String "error cohortes"
 */
function miembrosCohortes($usado)
{
    global $DB;
    //se realiza la consulta
    $sql_query="SELECT cm.userid AS userid FROM {cohort} AS c INNER JOIN {cohort_members} AS cm 
                ON (c.id=cm.cohortid)";
    
    //si esta siendo usado por otro metodo se retorna el texto de la consulta
    //en caso contrario se realiza la consulta y se retorna el resultado
    if($usado)
    {
        return $sql_query;
    }else{
        $output=$DB->get_records_sql($sql_query);
        if($output)
        {
            
            return $output;    
        }else
        {
            return "error cohortes";
        }
    }
}

/*
 * Funcion que verifica si en la asignatura y grupo ingresados existe almenos un miembro perteneciente
 * al programa ser pilo paga
 * 
 * @param $curso
 * @param $grupo
 * @return Array 
 */
function verificarSPPEnGrupo($curso,$grupo)
{
    global $DB;
    //se realizan las consultas auxiliares
    $idUsuarios=idUsuariosCurso($curso,$grupo,true);
    $idCohortes=miembrosCohortes(true);
    $newArray=array();
    
    //si se presento algun error al momento de hacer la consulta del id entonces se retorna el error
    //si no, se verifica algun error al momento de hacer la consulta de los miembros de los cohortes 
    //si es asi se retorna el error, en caso contrario significa que las consultas fueron exitosas
    if($idUsuarios=="error id")
    {
        return $idUsuarios;
    }else if($idCohortes=="error cohortes")
        {
            return $idCohortes;
        }else{
            
            //se realiza una interseccion para ver si existe algun miembro de ser pilo paga entre los estudiantes
            //matriculados en el grupo
            $sql_query="SELECT * FROM (".$idUsuarios.") AS a INTERSECT (".$idCohortes.")";
            
                
                $output= $DB->get_records_sql($sql_query);
                
                //si el resultado de la consulta no fue vacio
                //en caso que sea vacio entonces se retorna el correspondiente error
                if($output)
                {
                    //por cada resultado se añade el id del estudiante a un arreglo
                    foreach($output as &$estudiante)
                    {
                        $arrayAuxiliar=array();
                        array_push($newArray,$estudiante->userid);
                    }
                    
                    //si el conteo del array no es 0 entonces se retorna el primer elemento ya que no se 
                    //necesita mas informacion para confirmar la existencia de un ser pilo paga en el programa
                    //en caso contrario se retorna que no para presentar el correspondiente error
                    if(count($newArray)!=0)
                    {
                        return $newArray[0];
                    }else
                    {
                        return "no";    
                    }
                }else
                {
                    return "error spp"; 
                }
            
      
    }
}





//*****************************************************************************

/**
 * Return all course info(items and categories with grades) of a student
 *
 * @param $courseid, $userid
 * @return html table
 */

function getCoursegradelib_grade_categories($curso,$grupo, $userid){
    
    global $DB;
    $sql_query ="select max(id) FROM {talentospilos_semestre};";
    $maxid = $DB->get_record_sql($sql_query);
    $maxid= $maxid->max;
    
    $sql_query ="select EXTRACT(month FROM fecha_inicio) AS mes FROM {talentospilos_semestre} WHERE id=$maxid;";
    $mes = $DB->get_record_sql($sql_query);
    $mes = $mes->mes;
    
    $sql_query ="select EXTRACT(year FROM fecha_inicio) AS anio FROM {talentospilos_semestre} WHERE id=$maxid;";
    $año = $DB->get_record_sql($sql_query);
    $año = $año->anio;
    
    
    $Shortname="'00-".$curso."-".$grupo."-".$año."0".$mes."041'";
    
    $sql_query="SELECT c.id FROM {course} AS c 
    WHERE c.shortname=".$Shortname.";";
    
    $precourseid = $DB->get_record_sql($sql_query);
    $courseid=$precourseid->id;
    $context = context_course::instance($courseid);
    $gpr = new grade_plugin_return(array('type'=>'report', 'plugin'=>'user', 'courseid'=>$courseid, 'userid'=>$userid));
    $report = new grade_report_user($courseid, $gpr, $context, $userid);
    reduce_table_grade_categories($report);
     
     if ($report->fill_table()) {
       return $report->print_table(true);
    }
}
//getCoursegradelib_grade_categories(123456,"00",100);


/**
 * Reduce course information to display 
 *
 * @param &$report
 * @return null
 */
 function reduce_table_grade_categories(&$report) {
	
	$report->showpercentage = false;
	$report->showrange = false; 
	$report->showfeedback = false;
	$report->showcontributiontocoursetotal = false;
	$report->showgrade=false;
	$report->showweigth=false;
	$report->setup_table();
}


function retornarCantidadAparicionesMonitor($idmonitor)
{
    global $DB;
    $sql_query ="SELECT count(*) AS cantidad from {user} WHERE username LIKE '".$idmonitor."-%%%%';";
    $cantidad = $DB->get_record_sql($sql_query);
    $cantidadApariciones= $cantidad->cantidad;
    
    return $cantidadApariciones;
}


//*****************************************************************************




function createZip($patchFolder,$patchStorageZip){
    // Get real path for our folder
    $rootPath = realpath($patchFolder);
    
    // Initialize archive object
    $zip = new ZipArchive();
    $zip->open($patchStorageZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    
    // Create recursive directory iterator
    /** @var SplFileInfo[] $files */
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($rootPath),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    
    foreach ($files as $name => $file)
    {
        // Skip directories (they would be added automatically)
        if (!$file->isDir())
        {
            // Get real and relative path for current file
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($rootPath) + 1);
    
            // Add current file to archive
            $zip->addFile($filePath, $relativePath);
        }
    }
    
    // Zip archive will be created only after closing object
    $zip->close();
}

// ************************************
// Funciones para la gestión del riesgo
// ************************************



/**
 * Realiza una consulta en la base de datos para traer la lista de riesgos
 * posteriormente sera cargada en el arbol 
 * @return $array_risk la lista de riegos en la tabla talentospilos_riesgos_ases
 * @author Edgar Mauricio Ceron
 * */

function getRiskList(){
    
    global $DB;
    
    $sql_query = "SELECT * FROM {talentospilos_riesgos_ases}";
    $array_risk = $DB->get_records_sql($sql_query);
    
    return $array_risk;
}


//funcion para obtener los motivos de retiro

function getMotivosRetiros(){
    global $DB;
    $sql_query = "SELECT *  FROM {talentospilos_motivos}";
    return $DB->get_records_sql($sql_query);
}

function saveMotivoRetiro($talentosid, $motivoid,$detalle){
    global $DB;
    
    $record = new stdClass();
    $record->id_usuario = $talentosid;
    $record->id_motivo = $motivoid;
    $record->detalle = $detalle;
    
    
    $sql_query = "SELECT id FROM {talentospilos_retiros} WHERE id_usuario=".$talentosid;
    $exists = $DB->get_record_sql($sql_query);
    
    if($exists)
    {
        $record->id = $exists->id;
        return $DB->update_record('talentospilos_retiros', $record);
    }
    else
    {
        return $DB->insert_record('talentospilos_retiros', $record, false);    
    }
}

function getMotivoRetiroEstudiante($talentosid){
    global $DB;
    $sql_query = "SELECT * FROM {talentospilos_retiros} retiro INNER JOIN {talentospilos_motivos} motivo  ON motivo.id = retiro.id_motivo  WHERE id_usuario=".$talentosid;
    
    return $DB->get_record_sql($sql_query);
}

/*
 * Funcion que consulta informacion de los monitores asignados a un practicante
 * 
 * @param $id_practicante
 * @return Array 
 */
function get_monitores_practicante($id_practicante)
{
    global $DB;
    
    
    $sql_query = "SELECT DISTINCT usuario_rol.id_usuario,usuario.firstname,usuario.lastname  
                  FROM {talentospilos_user_rol} as usuario_rol INNER JOIN {user} AS usuario ON 
                  (usuario.id=usuario_rol.id_usuario) WHERE id_jefe='$id_practicante'";

    $consulta=$DB->get_records_sql($sql_query);
    
    $arreglo_retornar= array();
    
    //por cada registro retornado se toma la informacion necesaria, se añade a un arreglo auxiliar y este se agrega 
    //al areglo que sera retornado
    foreach($consulta as $monitores)
    {
        $array_auxiliar=array();
        //posicion 0
        array_push($array_auxiliar,$monitores->id_usuario);
        $nombre = $monitores->firstname ;
        $apellido = $monitores->lastname; 
        $unir = $nombre." ".$apellido;
        //posicion 1
        array_push($array_auxiliar,$unir);
        // array_push($array_auxiliar,get_estudiantes_monitor($id_practicante));
        //posicion n del arreglo que se retorna
        array_push($arreglo_retornar,$array_auxiliar);
    }
    
    return $arreglo_retornar;
}

/*
 * Funcion que consulta informacion de los practicantes asignados a un profesional
 * 
 * @param $id_profesional
 * @return Array 
 */
function get_practicantes_profesional($id_profesional,$id_instancia)
{
    global $DB;

    $sql_query = "SELECT DISTINCT usuario_rol.id_usuario,usuario.firstname AS nombre,usuario.lastname AS apellido 
                  FROM {talentospilos_user_rol} as usuario_rol INNER JOIN {user} AS usuario ON 
                  (usuario.id=usuario_rol.id_usuario) WHERE id_jefe='$id_profesional' and id_rol<>4";
                  

    $consulta=$DB->get_records_sql($sql_query);

    $arreglo_retornar= array();
    $arreglo_cantidades= array();
    $total_registros_no=[];


    //por cada registro retornado se toma la informacion necesaria, se añade a un arreglo auxiliar y este se agrega 
    //al areglo que sera retornado
    foreach($consulta as $practicantes)
    {
        
    $monitores = get_monitores_practicante($practicantes->id_usuario);

    foreach($monitores as $monitor){

    $sql_query= "SELECT count(DISTINCT mdl_talentospilos_seguimiento.id) FROM {talentospilos_seguimiento} INNER JOIN {talentospilos_seg_estudiante} ON ({talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento) where revisado_profesional='1' and id_monitor='$monitor[0]' and id_instancia='$id_instancia'";
    $valorRetorno[0]=$DB->get_record_sql($sql_query);
    $total_registros[0] +=$valorRetorno[0]->count;
    
    $sql_query= "SELECT count(DISTINCT mdl_talentospilos_seguimiento.id) FROM {talentospilos_seguimiento} INNER JOIN {talentospilos_seg_estudiante} ON ({talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento) where (revisado_profesional='0')and id_monitor='$monitor[0]' and id_instancia='$id_instancia'";
    $valorRetorno[1]=$DB->get_record_sql($sql_query);
    $total_registros[1]+=$valorRetorno[1]->count;
    
    $sql_query= "SELECT count(DISTINCT mdl_talentospilos_seguimiento.id) FROM {talentospilos_seguimiento} INNER JOIN {talentospilos_seg_estudiante} ON ({talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento) where  id_monitor='$monitor[0]' and id_instancia='$id_instancia'";
    $valorRetorno[2]=$DB->get_record_sql($sql_query);
    $total_registros[2] +=$valorRetorno[2]->count;
    }
    
        $array_auxiliar=array();
        //posicion 0
        array_push($array_auxiliar,$practicantes->id_usuario);

        $nombre = $practicantes->nombre ;
        $apellido = $practicantes->apellido; 
        $unir = $nombre." ".$apellido;
        //posicion 1
        array_push($array_auxiliar,$unir);
        array_push($array_auxiliar,$total_registros[0]);
        array_push($array_auxiliar,$total_registros[1]);
        array_push($array_auxiliar,$total_registros[2]);

        array_push($arreglo_retornar,$array_auxiliar);
    }

    print_r($arreglo_retornar);
    return ($arreglo_retornar);
    
}


//Funcion para eliminar registro de seguimiento deacuerdo a un id.

function eliminar_registro($id){

    global $DB;
    $whereclause = "id_seguimiento =".$id;
    $result= $DB->delete_records_select('talentospilos_seg_estudiante',$whereclause);
    $whereclause = "id =".$id;
    $result= $DB->delete_records_select('talentospilos_seguimiento',$whereclause);

    return $result;
}

function updateSeguimiento_pares($object){
     global $DB;
    $fecha_formato =str_replace( '/' , '-' , $object->fecha);
    date_default_timezone_set('America/Los_Angeles'); 
    $object->fecha=strtotime($fecha_formato);
    //se obtiene el id del estudiante al que pertene el seguimiento
    $sql_query = "select id_estudiante from {talentospilos_seg_estudiante}  where id_seguimiento=".$object->id;
    $seg_estud = $DB->get_record_sql($sql_query);
    
    //se obtiene el ultimo seguimeinto perteneciente al estudiante
    $lastSeg = $DB->get_record_sql('SELECT id_seguimiento,MAX(id) FROM {talentospilos_seg_estudiante} seg_est WHERE seg_est.id_estudiante='.$seg_estud->id_estudiante.'GROUP BY id_seguimiento ORDER BY id_seguimiento DESC limit 1');
   
      if($lastSeg->id_seguimiento == $object->id) updateRisks($object, $seg_estud->id_estudiante );
     $lastinsertid = $DB->update_record('talentospilos_seguimiento', $object);

     if($lastinsertid){
         return '1';
     }else{
         return '0';
     }

}

//Funcion para actualizar registro de seguimiento deacuerdo a un id.

function actualizar_registro($id,$lugar,$tema,$objetivos,$obindividual,$riesgoIndividual,$obfamiliar,$riesgoFamiliar,$obacademico,$riesgoAcademico,
$obeconomico,$riesgoEconomico,$obuniversitario,$riesgoUniversitario,$observacionesGeneral,$practicante,$profesional,$fecha,$h_inicial,$h_final){

    global $DB;

    $fecha_formato =str_replace( '/' , '-' , $fecha);
    $record->id = $id;
    $record->lugar = $lugar;
    $record->tema = $tema;
    $record->objetivos = $objetivos;
    $record->individual = $obindividual;
    $record->individual_riesgo = (int)$riesgoIndividual;
    $record->familiar_desc = $obfamiliar;
    $record->familiar_riesgo=(int)$riesgoFamiliar;
    $record->academico=$obacademico;
    $record->academico_riesgo=(int)$riesgoAcademico;
    $record->economico=$obeconomico;
    $record->economico_riesgo=(int)$riesgoEconomico;
    $record->vida_uni=$obuniversitario;
    $record->vida_uni_riesgo=(int)$riesgoUniversitario;
    $record->observaciones=$observacionesGeneral;
    $record->revisado_profesional=$profesional;
    $record->revisado_practicante=$practicante;
    $record->hora_ini=$h_inicial;
    $record->hora_fin=$h_final;
    date_default_timezone_set('America/Los_Angeles'); 
    $record->fecha = strtotime($fecha_formato);
    $lastinsertid = $DB->update_record('talentospilos_seguimiento', $record);
    if($lastinsertid){
        return '1';
    }else{
        return '0';
    }
}



/*
 * funcion que obtiene el ID dado el shortname de la tabla
 * user_info_field
 *
 * @param $shortname
 * @return number
 */


function get_id_info_field($shortname){
    global $DB;
    
    $sql_query = "select id from {user_info_field}  where shortname='$shortname'";
    $consulta=$DB->get_record_sql($sql_query);
    return $consulta;
    
}


//***************************************************
//***************************************************
//***************************************************

/*
 * funcion que trae la informacion necesaria para los seguimientos considerando el monitor actual, la instancia actual asi como
 * que el monitor este asignado como tal a esta instancia
 *
 * @param $id_monitor
 * @param $id_instance 
 * @return Array 
 */


function get_seguimientos_monitor($id_monitor,$id_instance){
    global $DB;
    

    $id_info_field=get_id_info_field("idtalentos");
    
    
    $sql_query = "SELECT ROW_NUMBER() OVER(ORDER BY seguimiento.id ASC) AS number_unique,seguimiento.id AS id_seguimiento,
                  seguimiento.tipo,usuario_monitor
                  .id AS id_monitor_creo,usuario_monitor.firstname AS nombre_monitor_creo,nombre_usuario_estudiante.firstname 
                  AS nombre_estudiante,nombre_usuario_estudiante.lastname AS apellido_estudiante,seguimiento.created,seguimiento.fecha,seguimiento.hora_ini,
                  seguimiento.hora_fin,seguimiento.lugar,seguimiento.tema,seguimiento.objetivos,seguimiento.actividades,seguimiento.individual,seguimiento.revisado_profesional AS profesional,
                  seguimiento.revisado_practicante AS practicante,seguimiento.individual_riesgo,seguimiento.familiar_desc,seguimiento.familiar_riesgo,seguimiento.academico,
                  seguimiento.academico_riesgo,seguimiento.economico,seguimiento.economico_riesgo, seguimiento.vida_uni,seguimiento.vida_uni_riesgo,
                  seguimiento.observaciones AS observaciones,seguimiento.id AS status,seguimiento.id AS sede, usuario_estudiante.id_tal AS id_estudiante,monitor_actual.id_monitor,
                  usuario_mon_actual.firstname AS nombre_monitor_actual,usuario_mon_actual.lastname AS apellido_monitor_actual, usuario_monitor.lastname AS apellido_monitor_creo
                  FROM {talentospilos_seg_estudiante} AS s_estudiante INNER JOIN {talentospilos_seguimiento} AS seguimiento ON 
                  (s_estudiante.id_seguimiento=seguimiento.id) INNER JOIN {user} AS usuario_monitor ON (seguimiento.id_monitor = usuario_monitor.id) 
                  INNER JOIN (SELECT DISTINCT MAX(data.userid) AS userid, data.data as id_tal FROM {talentospilos_usuario} AS usuarios_tal INNER JOIN mdl_user_info_data AS data 
                  ON (CAST(usuarios_tal.id AS varchar) = data.data) WHERE data.fieldid ='$id_info_field->id' GROUP BY id_tal) AS usuario_estudiante  ON 
                  (usuario_estudiante.id_tal=CAST(s_estudiante.id_estudiante AS varchar)) INNER JOIN {user} as nombre_usuario_estudiante ON 
                  (nombre_usuario_estudiante.id=usuario_estudiante.userid) INNER JOIN {talentospilos_monitor_estud} as monitor_actual 
                  ON (CAST(monitor_actual.id_estudiante AS text)=CAST(s_estudiante.id_estudiante AS text)) INNER JOIN {user} AS usuario_mon_actual ON (monitor_actual.id_monitor=usuario_mon_actual.id)
                  WHERE monitor_actual.id_monitor='$id_monitor' AND seguimiento.id_instancia='$id_instance' AND monitor_actual.id_instancia='$id_instance' ORDER BY usuario_monitor.firstname;
    ";
    
    $consulta=$DB->get_records_sql($sql_query);
    $array_cantidades =[];
    $array_estudiantes=[];

    foreach($consulta as $estudiante)
    {
      //Crea un nuevo array con los datos obtenidos en la consulta y luego agrega :
      //Número de registros del estudiante revisados por el profesional  no revisados por el mismo,Número total de registros del estudiante cuando son de tipo 'PARES'. 
      $sql = "SELECT count(*) FROM {talentospilos_seguimiento}  INNER JOIN {talentospilos_seg_estudiante} ON {talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento where revisado_profesional=1 and tipo='PARES' and id_estudiante='$estudiante->id_estudiante' and id_instancia='$id_instance'";
      $estudiante->registros_estudiantes_revisados=$DB->get_record_sql($sql)->count;
      $sql = "SELECT count(*) FROM {talentospilos_seguimiento}  INNER JOIN {talentospilos_seg_estudiante} ON {talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento where revisado_profesional=0 and tipo='PARES' and id_estudiante='$estudiante->id_estudiante' and id_instancia='$id_instance'";
      $estudiante->registros_estudiantes_norevisados=$DB->get_record_sql($sql)->count;
      $sql = "SELECT count(*) FROM {talentospilos_seguimiento}  INNER JOIN {talentospilos_seg_estudiante} ON {talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento where id_estudiante='$estudiante->id_estudiante'and tipo='PARES' and id_instancia='$id_instance'";
      $estudiante->registros_estudiantes_total=$DB->get_record_sql($sql)->count;
      
      //Número de registros del estudiante revisados por el profesional  no revisados por el mismo,Número total de registros del estudiante cuando son de tipo 'PARES'. 

      $sql = "SELECT count(*) FROM {talentospilos_seguimiento}  INNER JOIN {talentospilos_seg_estudiante} ON {talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento where revisado_profesional=1 and tipo='GRUPAL' and id_estudiante='$estudiante->id_estudiante' and id_instancia='$id_instance'";
      $estudiante->registros_estudiantes_revisados_grupal=$DB->get_record_sql($sql)->count;
      $sql = "SELECT count(*) FROM {talentospilos_seguimiento}  INNER JOIN {talentospilos_seg_estudiante} ON {talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento where revisado_profesional=0 and tipo='GRUPAL' and id_estudiante='$estudiante->id_estudiante' and id_instancia='$id_instance'";
      $estudiante->registros_estudiantes_norevisados_grupal=$DB->get_record_sql($sql)->count;
      $sql = "SELECT count(*) FROM {talentospilos_seguimiento}  INNER JOIN {talentospilos_seg_estudiante} ON {talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento where id_estudiante='$estudiante->id_estudiante'and tipo='GRUPAL' and id_instancia='$id_instance'";
      $estudiante->registros_estudiantes_total_grupal=$DB->get_record_sql($sql)->count;
      array_push($array_estudiantes,$estudiante);
    }
    
   return $array_estudiantes;
}
/* Obtiene la cantidad de seguimientos de cada monitor.
*/
function get_cantidad_seguimientos_monitor($id_monitor,$id_instance){
    global $DB;
    $valorRetorno=[];
    
    $sql_query= "SELECT count(DISTINCT mdl_talentospilos_seguimiento.id) FROM {talentospilos_seguimiento} INNER JOIN {talentospilos_seg_estudiante} ON ({talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento) where revisado_profesional='1' and id_monitor='$id_monitor' and id_instancia='$id_instance'";
    $valorRetorno[0]=$DB->get_record_sql($sql_query);
    
    $sql_query= "SELECT count(DISTINCT mdl_talentospilos_seguimiento.id) FROM {talentospilos_seguimiento} INNER JOIN {talentospilos_seg_estudiante} ON ({talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento) where (revisado_profesional='0')and id_monitor='$id_monitor' and id_instancia='$id_instance'";
    $valorRetorno[1]=$DB->get_record_sql($sql_query);
    
    $sql_query= "SELECT count(DISTINCT mdl_talentospilos_seguimiento.id) FROM {talentospilos_seguimiento} INNER JOIN {talentospilos_seg_estudiante} ON ({talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento) where  id_monitor='$id_monitor' and id_instancia='$id_instance'";
    $valorRetorno[2]=$DB->get_record_sql($sql_query);

    return $valorRetorno;
}

/**
 * Función que recupera la información de la tabla de seguimientos grupales (estudiantes
 *  respectivos que asistieron a ella -firstname-lastname-username y id ).
 *
 * @see get_seguimientos($id,$tipo,$instancia)
 * @param id --> id correspondiente a la id del estudiante.
 * @param tipo--> tipo correspondiente a "GRUPAL".
 * @param instancia --> instancia 
 * @return array con información de los nombres de los estudiantes que tuvieron un seguimiento grupal dado un idseguimiento.
 */

function get_estudiantes($id,$tipo,$instancia){
    global $DB;
    $estudiantes=array();

    $sql_query = " SELECT * FROM {talentospilos_seguimiento} AS seguimiento INNER JOIN mdl_talentospilos_seg_estudiante AS seguimiento_estudiante ON (seguimiento.id=seguimiento_estudiante.id_seguimiento) where seguimiento.id='$id' and tipo='$tipo' and id_instancia='$instancia'";
    $registros=$DB->get_records_sql($sql_query);
    
    foreach($registros as $registro){
        
        $estudiante->id = get_id_user_moodle($registro->id_estudiante); //obtiene el id del estudiante.
        $nombres_estudiantes = " SELECT id, username,firstname,lastname FROM {user} where id='$estudiante->id'"; //obtiene el nombre y el apellido dado el código del estudiante.
        $registros_nombres=$DB->get_records_sql($nombres_estudiantes);

        foreach($registros_nombres as $registro_nombre){
            
          $estudiante->username=$registro_nombre->username;
          $estudiante->firstname=$registro_nombre->firstname;
          $estudiante->lastname=$registro_nombre->lastname;
          $estudiante->idtalentos =$registro->id_estudiante;
          array_push($estudiantes,(array)$estudiante);
        }
    }
    return $estudiantes;    
}


/**
 * Función que recupera la información de la tabla de seguimientos grupales dado un id.
 *
 * @see get_seguimientos($id,$tipo,$instancia)
 * @param id --> id correspondiente a la id del estudiante.
 * @param tipo--> tipo correspondiente a "GRUPAL".
 * @param instancia --> instancia 
 * @return array con información de seguimiento grupal dado un idseguimiento.
 */

function get_seguimientos($id,$tipo,$instancia){
    global $DB;
    $estudiantes=array();

    $sql_query = " SELECT * FROM {talentospilos_seguimiento} where id='$id' and tipo='$tipo' and id_instancia='$instancia'";
    $registros=$DB->get_record_sql($sql_query);
    
    return $registros;    
}







//***************************************************
//***************************************************
//***************************************************


//funcion que retorna el rol de un usuario con el fin de mostrar al correspondiente interfaz en seguimiento_pilos
function get_name_rol($userid,$instanceid)
{
    global $DB;
    
    $sql_query = "SELECT id_rol FROM {talentospilos_user_rol} WHERE id_usuario='$userid' AND id_instancia='$instanceid'";
    $consulta=$DB->get_records_sql($sql_query);
    
    foreach($consulta as $tomarId)
    {

        $idretornar=$tomarId->id_rol;
    }
    // print_r($idretornar);
    return $idretornar;
}

//funcion que retorna el nombre de  rol de un usuario con el fin de mostrar al correspondiente interfaz en seguimiento_pilos
function get_name_of_rol($userid,$instanceid)
{
    global $DB;
    
    $sql_query = "SELECT nombre_rol FROM {talentospilos_user_rol} WHERE id_usuario='$userid' AND id_instancia='$instanceid'";
    $consulta=$DB->get_records_sql($sql_query);
    
    foreach($consulta as $tomarId)
    {

        $idretornar=$tomarId->id_rol;
    }
    // print_r($idretornar);
    return $idretornar;
}



function get_profesional_practicante($id,$instanceid)
{
    global $DB;

    $sql_query = "SELECT id_jefe FROM {talentospilos_user_rol} WHERE id_usuario=$id AND id_instancia=$instanceid";
    $consulta=$DB->get_records_sql($sql_query);
    
    foreach($consulta as $tomarId)
    {

        $idretornar=$tomarId->id_jefe;
    }
    // print_r($idretornar);
    return $idretornar;
}

// get_profesional_practicante(1113,534);
//get_name_rol("2","534");

/**
 * Función que recupera los datos adicionales de un estudiante
 *
 * @see get_additional_fields($id_student)
 * @param id_student --> id correspondiente a la tabla {user}
 * @return Array
 */
 

 
 //metodo apra borrar archivos de un folder
 
function deleteFilesFromFolder($folderPath){
    $files = glob($folderPath.'/*'); // get all file names
    foreach($files as $file){ // iterate files
          if(is_file($file))  unlink($file); // delete file
    }
}

/*
 * Geographic functions
 */



/**
 * Función que retorna un arreglo de barrios
 *
 * @see load_neighborhood(){
 * @return array
 */

function get_neighborhood(){
    global $DB;
    $sql_query = "SELECT id, nombre FROM {talentospilos_barrios} ORDER BY nombre";
    $array_neighborhood = $DB->get_records_sql($sql_query);
    
    return $array_neighborhood;
}

/**
 * Función que guarda el riesgo geográfico
 *
 * @see save_geographic_risk(){
 * @return bool
 */


function save_geographic_risk($id_student, $rate_risk){
    
    global $DB;
    
    $table = 'talentospilos_riesg_usuario';
    
    $sql_query = "SELECT id FROM {talentospilos_riesgos_ases} WHERE nombre = 'geografico'";
    $risk_id = $DB->get_record_sql($sql_query)->id;
    
    $sql_query = "SELECT id FROM {talentospilos_riesg_usuario} WHERE id_usuario = $id_student AND id_riesgo = $risk_id";
    $id_register = $DB->get_record_sql($sql_query)->id;
    
    if($id_register){
        
        $update_record = new stdClass();
        
        $update_record->id = (int)$id_register;
        $update_record->id_usuario = (int)$id_student;
        $update_record->id_riesgo = (int)$risk_id;
        $update_record->calificacion_riesgo = (int)$rate_risk;
        
        $update_result = $DB->update_record($table, $update_record);

        if($update_result){
            return '1';
        }else{
            return '0';
        }
        
    }else{
        $insert_record = new stdClass();
        
        $insert_record->id_usuario = (int)$id_student;
        $insert_record->id_riesgo = (int)$risk_id;
        $insert_record->calificacion_riesgo = (int)$rate_risk;
        
        $insert_result = $DB->insert_record($table, $insert_record);
        
        if($insert_result){
            return '1';
        }else{
            return '0';
        }
    }
    
}

/**
 * Función para obtener los periodos existentes
 *
 * @see get_periodos_semestrales(){
 * @return bool
 */
 
 function get_periodos_semestrales(){
     global $DB;
     $sql_query = "SELECT * FROM mdl_talentospilos_semestre";
     $result = $DB->get_records_sql($sql_query);
     return $result;
 }



// save_geographic_risk(1047, 3);

/**
 * Función que guarda los cambios sobre la ficha geográfica
 *
 * @see save_geographic_info(){
 * @return bool
 */
 
 function save_geographic_info($latitud, $longitud, $id_barrio, $id_student){
     
     global $DB;

     $sql_query = "SELECT id FROM {talentospilos_demografia} WHERE  id_usuario = $id_student";
     $id_record = $DB->get_record_sql($sql_query)->id;
     
     if($id_record){

        $table_demographic = "talentospilos_demografia";
        $table_risk_student = "talentospilos_riesg_usuario";
     
        $update_record = new stdClass();
        
        $update_record->id = $id_record;
        $update_record->longitud = (float)$longitud;
        $update_record->latitud = floatval($latitud);
        $update_record->id_usuario = $id_student;
        $update_record->barrio = $id_barrio;
         
        $result_demographic = $DB->update_record($table_demographic, $update_record);

        if($result_demographic){
            return '1';
        }else{
            return '0';
        }
     }else{
        $table_demographic = "talentospilos_demografia";
        
        $insert_record = new stdClass();
     
        $insert_record->longitud = $longitud;
        $insert_record->latitud = $latitud;
        $insert_record->id_usuario = $id_student;
        $insert_record->barrio = $id_barrio;
         
        $result = $DB->insert_record($table_demographic, $insert_record);
        
        if($result){
            return '1';
        }else{
            return '0';
        }
     }
 }

/*
 * End geographic functions
 */

// ***************
// Email functions
// ***************



function get_full_user_talentos($id){
    global $DB;
    
    $sql_query = "SELECT * FROM {talentospilos_usuario} WHERE id= ".$id;
    $user = $DB->get_record_sql($sql_query);
    
    return $user;
}


?>