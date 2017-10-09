<?php
require_once(dirname(__FILE__).'/../../../../config.php');
require_once('../MyException.php');
require_once ('../lib/student_lib.php');
require_once('../periods_management/periods_lib.php');
require_once('../seguimiento_pilos/seguimientopilos_lib.php');



/**
 * Función que los estudiantes que están relacionados con un monitor especifico en una instancia.
 *
 * @see get_grupal_students($id_monitor, $idinstancia)
 * @param  $id_monitor --> id del monitor
 * @param  $id_monitor --> id de la instancia
 * @return Array 
 */

function get_grupal_students($id_monitor, $idinstancia){
    global $DB;
    $semestre_act = get_current_semester();
    $sql_query = "SELECT * FROM (SELECT * FROM 
                    (SELECT *, id AS id_user FROM {user}) AS userm 
                            INNER JOIN 
                            (SELECT * FROM {user_info_data} as d INNER JOIN {user_info_field} as f ON d.fieldid = f.id WHERE f.shortname ='idtalentos' AND data <> '') AS field 
                            ON userm. id_user = field.userid ) AS usermoodle 
                        INNER JOIN 
                        (SELECT *,id AS idtalentos FROM {talentospilos_usuario}) AS usuario 
                        ON usermoodle.data = CAST(usuario.id AS TEXT)
                    where  idtalentos in (select id_estudiante from {talentospilos_monitor_estud} where id_monitor =".$id_monitor." AND id_instancia=".$idinstancia." and id_semestre=".$semestre_act->max.")  ;";
    
   $result = $DB->get_records_sql($sql_query);
   return $result;
}


/**
 * Función que obtiene un seguimiento dado un id del monitor {talentospilos_seguimiento}, el tipo de seguimiento y la instancia
 * @see get_tracking_by_monitor($id_monitor, $id_seg= null, $tipo, $idinstancia)
 * @param $id_monitor = id de monitor
 * @param $id_seg = id del seguimiento
 * @param $tipo  = tipo del seguimiento
 * @param $idinstancia  = id de la instancia
 * @return array
 */
 
function get_tracking_by_monitor($id_monitor, $id_seg= null, $tipo, $idinstancia){
    global $DB;
    $current_semester = get_current_semester();
    $semester_interval = get_semester_interval($current_semester->max);

    $sql_query= "";
    $sql_query="SELECT seg.id as id_seg, to_timestamp(fecha) as fecha_formato,*  from {talentospilos_seguimiento} seg  where seg.id_monitor = ".$id_monitor." AND seg.tipo = '".$tipo."' AND seg.id_instancia=".$idinstancia." AND (fecha between ".strtotime($semester_interval->fecha_inicio)." and ".strtotime($semester_interval->fecha_fin).") AND status<>0 ORDER BY fecha_formato DESC;";

    if($id_seg != null){
      $sql_query = trim($sql_query,";");
      $sql_query.= " AND seg.id =".$id_seg.";";

   
    }
   return $DB->get_records_sql($sql_query);
}

/**
 * Función que elimina un registro grupal tanto en las tablas {talentospilos_seg_estudiante} {talentospilos_seguimientos} dado un id de seguimiento.
 * @see delete_seguimiento_grupal($id)
 * @param $id = id del seguimiento
 * @return boolean
 */

function delete_seguimiento_grupal($id){
    
    global $DB;

    $sql_query = "DELETE FROM {talentospilos_seg_estudiante} WHERE id_seguimiento ='$id'";
    $success = $DB->execute($sql_query);
    $sql_query = "DELETE FROM {talentospilos_seguimiento} WHERE id = $id";
    $success = $DB->execute($sql_query);
    return $success;
}

/**
 * Función que consulta el seguimiento de tipo GRUPAL dado un id {talentospilos_seg_estudiante}
 * @see getEstudiantesSegGrupal($id_seg)
 * @param $id_seg = id del seguimiento
 * @return Array
 */

function getEstudiantesSegGrupal($id_seg){
    global $DB;
    $sql_query = "SELECT id_estudiante FROM {talentospilos_seg_estudiante} WHERE id_seguimiento ='$id_seg'";
    return $DB->get_records_sql($sql_query);
}

/**
 * Función que elimina un seguimiento 
 * dado su id_seguimiento e id_estudiante
 * {talentospilos_seg_estudiante}
 * @see dropTalentosFromSeg($idSeg,$id_est)
 * @return 0 o 1
 */
function dropTalentosFromSeg($idSeg,$id_est){
    global $DB;
    $whereclause = "id_seguimiento =".$idSeg." AND id_estudiante=".$id_est;
    return $DB->delete_records_select('talentospilos_seg_estudiante',$whereclause);
}

/**
 * Función que inserta un seguimiento 
 * dado su id_seguimiento e id_estudiante
 * {talentospilos_seg_estudiante}
 * @see insertSegEst($id_seg,$id_est)
 * @return 0 o 1
 */
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


/**
 * Función que inserta un seguimiento 
 * {talentospilos_seguimiento}
 * @see insertSeguimiento($object)
 * @return true
 */

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

/**
 * Función que obtiene un seguimiento
 * ordenado por semestre {talentospilos_semestre}
 * {user_info_field} {user_info_data}
 * @see getSeguimientoOrderBySemester($id_est = null, $tipo,$idsemester = null, $idinstancia = null)
 * @return array
 */
 
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




/**
 * Función que obtiene el rol de un usuario
 * dado un id moodle y id instancia {talentospilos_user_rol} {talentospilos_rol}
 * @see get_role_user($id_moodle, $idinstancia)
 * @return object
 */
function get_role_user($id_moodle, $idinstancia)
{
    global $DB;
    $current_semester = get_current_semester(); 
    $sql_query = "select nombre_rol, rol.id as rolid from {talentospilos_user_rol} as ur inner join {talentospilos_rol} as rol on rol.id = ur.id_rol where  ur.estado = 1 AND ur.id_semestre =".$current_semester->max."  AND id_usuario = ".$id_moodle." AND id_instancia =".$idinstancia.";";
    return $DB->get_record_sql($sql_query);
}

/**
 * Función que obtiene permisos del rol
 * {talentospilos_permisos_rol} {talentospilos_funcionalidad}
 * @see get_permisos_role($idrol,$page)
 * @return array
 */
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

/**
 * Función que obtiene seguimientos dado
 * el id_estudiante, id_seguimiento, tipo_seguimiento y id_instancia
 * {talentospilos_seguimiento}{talentospilos_seg_estudiante} 
 * @see getSeguimiento($id_est, $id_seg, $tipo, $idinstancia)
 * @return array
 */
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

/**
 * Función que obtiene el usuario de Moodle
 * deacuerdo al ID
 * {user}
 * @see getUserMoodleByid($id)
 * @return object
 */
function getUserMoodleByid($id){
    global $DB;
    $sql_query = "SELECT * FROM {user} WHERE id =".$id.";";
    return $DB->get_record_sql($sql_query);
}


/**
 * Función que recupera la información de la tabla de seguimientos grupales (estudiantes
 * respectivos que asistieron a ella -firstname-lastname-username y id ).
 *
 * @see get_students_assistance($id,$tipo,$instancia)
 * @param id --> id correspondiente a la id del estudiante.
 * @param type--> tipo correspondiente a "GRUPAL".
 * @param instance --> instancia 
 * @return array con información de los nombres de los estudiantes que tuvieron un seguimiento grupal dado un idseguimiento.
 */

function get_students_assistance($id,$type,$instance){
    global $DB;
    $estudiantes=array();

    $sql_query = " SELECT * FROM {talentospilos_seguimiento} AS seguimiento INNER JOIN mdl_talentospilos_seg_estudiante AS seguimiento_estudiante ON (seguimiento.id=seguimiento_estudiante.id_seguimiento) where seguimiento.id='$id' and tipo='$type' and id_instancia='$instance'";
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
    //  print_r($fullname_pract);
     return $id_practicante;     
 }

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





?>