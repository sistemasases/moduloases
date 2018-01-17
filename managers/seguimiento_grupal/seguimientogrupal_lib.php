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
 * @author     Isabella Serna Ramírez
 * @package    block_ases
 * @copyright  2017 Isabella Serna Ramírez <isabella.serna@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../../../config.php');
require_once('../MyException.php');
require_once ('../lib/student_lib.php');
require_once('../periods_management/periods_lib.php');
require_once('../pilos_tracking/pilos_tracking_lib.php');



/**
 * Obtains all students related with a given monitor monitor 
 *
 * @see get_grupal_students($id_monitor, $idinstancia)
 * @param  $id_monitor --> monitor id
 * @param  $idinstancia --> instance id
 * @return array with students 
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
 * Obtains a track given the monitor id, track type (seguimiento) and instance
 * 
 * @see get_tracking_by_monitor($id_monitor, $id_seg= null, $tipo, $idinstancia)
 * @param $id_monitor --> monitor id
 * @param $id_seg --> track id
 * @param $tipo --> track type
 * @param $idinstancia --> instance type
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
 * Deletes a groupal tracking on {talentospilos_seg_estudiante} {talentospilos_seguimientos} tables given a track id
 * @see delete_seguimiento_grupal($id)
 * @param $id = track id
 * @return boolean --> True if it's successul, false otherwise
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
 * Gets a groupal track given its id
 * @see getEstudiantesSegGrupal($id_seg)
 * @param $id_seg --> track id
 * @return array
 */

function getEstudiantesSegGrupal($id_seg){
    global $DB;
    $sql_query = "SELECT id_estudiante FROM {talentospilos_seg_estudiante} WHERE id_seguimiento ='$id_seg'";
    return $DB->get_records_sql($sql_query);
}

/**
 * Deletes a track given its id and student id
 * 
 * @see dropTalentosFromSeg($idSeg,$id_est)
 * @param $idSeg --> track id
 * @param $id_est --> Student id
 * @return integer --> 1 if it's successful, 0 otherwise
 */
function dropTalentosFromSeg($idSeg,$id_est){
    global $DB;
    $whereclause = "id_seguimiento =".$idSeg." AND id_estudiante=".$id_est;
    return $DB->delete_records_select('talentospilos_seg_estudiante',$whereclause);
}

/**
 * Inserts a track given its id and student id
 * @see insertSegEst($id_seg,$id_est)
 * @param $id_seg --> track id
 * @param $id_est --> student id
 * @return integer --> 1 if it's successful, 0 otherwise
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
 * Inserts a track given the track object and student id
 * @see insertSeguimiento($object, $id_est)
 * @param $object --> Track object
 * @param $id_est --> student id
 * @return true
 */

function insertSeguimiento($object, $id_est){
    global $DB;
    $id_seg = $DB->insert_record('talentospilos_seguimiento', $object,true);
    
    // Track is related with the student 
    insertSegEst($id_seg, $id_est);
    
    //Risk is updated
    if($object->tipo == 'PARES'){
        foreach ($id_est as $idStudent) {
            updateRisks($object, $idStudent);
        }
    }
    
    return true;
}

/**
 * Gets a track sorted by semester
 * @see getSeguimientoOrderBySemester($id_est = null, $tipo,$idsemester = null, $idinstancia = null)
 * @param $id_est = null --> student id
 * @param $tipo --> track type
 * @param $idsemester = null --> semester id
 * @param $idinstancia = null --> instance id
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
 * Gets an user role given a moodle id and instance id
 * @see get_role_user($id_moodle, $idinstancia)
 * @param $id_moodle --> moodle user id
 * @param $idinstancia --> instance id
 * @return object representing the user role
 */
function get_role_user($id_moodle, $idinstancia)
{
    global $DB;
    $current_semester = get_current_semester(); 
    $sql_query = "select nombre_rol, rol.id as rolid from {talentospilos_user_rol} as ur inner join {talentospilos_rol} as rol on rol.id = ur.id_rol where  ur.estado = 1 AND ur.id_semestre =".$current_semester->max."  AND id_usuario = ".$id_moodle." AND id_instancia =".$idinstancia.";";
    return $DB->get_record_sql($sql_query);
}

/**
 * Gets role permissions given a role id and page
 * @see get_permisos_role($idrol,$page)
 * @param $idrol --> role id
 * @param $page
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
 * Gets a track given its id, type, student id and instance id
 * @see getSeguimiento($id_est, $id_seg, $tipo, $idinstancia)
 * @param $id_est --> student id
 * @param $id_seg --> track id
 * @param $tipo --> track type
 * @param $idinstancia --> instance id
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
 * Gets a moodle user given his id
 * @see getUserMoodleByid($id)
 * @param $id --> user id
 * @return object representing the user
 */
function getUserMoodleByid($id){
    global $DB;
    $sql_query = "SELECT * FROM {user} WHERE id =".$id.";";
    return $DB->get_record_sql($sql_query);
}


/**
 * Recovers information from groupal trackings table ({talentospilos_seguimiento})
 *
 * @see get_students_assistance($id,$tipo,$instancia)
 * @param $id --> student id
 * @param $type --> Type representing "GRUPAL"
 * @param $instance --> instance id
 * @return array with student information that were part of a groupal tracking (seguimiento grupal)
 */

function get_students_assistance($id,$type,$instance){
    global $DB;
    $estudiantes=array();

    $sql_query = " SELECT * FROM {talentospilos_seguimiento} AS seguimiento INNER JOIN mdl_talentospilos_seg_estudiante AS seguimiento_estudiante ON (seguimiento.id=seguimiento_estudiante.id_seguimiento) where seguimiento.id='$id' and tipo='$type' and id_instancia='$instance'";
    $registros=$DB->get_records_sql($sql_query);
    
    foreach($registros as $registro){
        
        $estudiante->id = get_id_user_moodle($registro->id_estudiante); //Gets student id.
        $nombres_estudiantes = " SELECT id, username,firstname,lastname FROM {user} where id='$estudiante->id'"; //Gets name and lastname given the user id
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
 * Recovers information from groupal trackings table given an id
 *
 * @see get_seguimientos($id,$tipo,$instancia)
 * @param $id --> student id
 * @param $tipo --> Type representing "GRUPAL"
 * @param $instancia --> instance id
 * @return array with groupal tracking information given an id
 */

function get_seguimientos($id,$tipo,$instancia){
    global $DB;
    $estudiantes=array();

    $sql_query = " SELECT * FROM {talentospilos_seguimiento} where id='$id' and tipo='$tipo' and id_instancia='$instancia'";
    $registros=$DB->get_record_sql($sql_query);
    
    return $registros;    
}





/**
 * Gets the profesional id assigned to a student
 * 
 * @see get_id_assigned_professional($id_student)
 * @param $id_student --> student id
 * @return integer Returns professional id or 0 if the student does not have a professional assigned
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
 * Gets the practicant id assigned to a student
 *
 * @see get_id_assigned_pract($id_student)
 * @param $id_student --> student id
 * @return string practicant fullname
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
 * Gets an user given his username
 * 
 * @see get_user_by_username($username)
 * @param  $username --> Moodle username 
 * @return array with an object representing the user
 */
function get_user_by_username($username){
    global $DB;
    
    $sql_query = "SELECT * FROM {user} WHERE username = '".$username."'";
    $user = $DB->get_record_sql($sql_query);
    
    return $user;
}





?>