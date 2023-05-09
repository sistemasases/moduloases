<?php

require_once(dirname(__FILE__). '/../../../../config.php');
require_once(dirname(__FILE__).'/../periods_management/periods_lib.php');
require_once(dirname(__FILE__).'/../user_management/user_lib.php');
require_once(dirname(__FILE__).'/../MyException.php');
require_once(dirname(__FILE__).'/../../core/module_loader.php');

module_loader('periods');


/**
 * Función que obtiene el codigo del jefe de un monitor (practicante) en un periodo 
 * dado
 *
 * @see get_boss_of_monitor_by_semester()
 * @return object rol
 */

function get_boss_of_monitor_by_semester($id_monitor,$id_semester,$id_instance){
  global $DB;

    $sql_query="
        SELECT * FROM {talentospilos_user_rol} where id_usuario in (
            SELECT id_jefe FROM {talentospilos_user_rol} 
            where id_usuario=$id_monitor 
            and id_semestre=$id_semester 
            and id_instancia=$id_instance
            and id_rol=4
        ) LIMIT 1";

    $practicant = $DB->get_record_sql($sql_query);
    return $practicant;
}



/**
 * Función que obtiene los practicantes de un profesional en el semestre actual.
 *
 * @see get_pract_of_prof()
 * @return object rol
 */
function get_pract_of_prof($id_prof,$id_instance){
   global $DB;

    $current_semester = core_periods_get_current_period($id_instance);
    $id_practicant = get_role_id('practicante_ps');
    $sql_query="SELECT id_usuario,users.firstname,users.lastname,id_semestre,users.username, CONCAT(users.firstname, ' ',users.lastname) AS fullname
    FROM {talentospilos_user_rol} user_rol
    INNER JOIN {user} users ON user_rol.id_usuario = users.id 
    WHERE user_rol.id_jefe = $id_prof 
    AND user_rol.id_rol = $id_practicant->id 
    AND user_rol.estado = 1 
    AND user_rol.id_semestre = $current_semester->id 
    AND user_rol.id_instancia = $id_instance
    ORDER BY fullname ASC";

    $practicants = $DB->get_records_sql($sql_query);
    return $practicants;
}

/**
 * Función que obtiene los monitores de un practicante en el semestre actual.
 *
 * @see get_monitors_of_pract($id_pract,$id_instance)
 * @return Array
 */
function get_monitors_of_pract($id_pract,$id_instance, $period_id = null){
   global $DB;

    if (empty($period_id)) {
        $period_id= (core_periods_get_current_period($id_instance))->id;
    }

    $id_monitor = get_role_id('monitor_ps');
    $sql_query = "SELECT id_usuario, users.firstname,users.lastname,id_semestre,users.username, CONCAT(users.firstname, ' ', users.lastname) AS fullname
                  FROM {talentospilos_user_rol} AS user_rol
                  INNER JOIN {user} users ON user_rol.id_usuario = users.id 
                  WHERE user_rol.id_jefe = '$id_pract' 
                        AND user_rol.id_rol = '$id_monitor->id' 
                        AND user_rol.estado = 1 
                        AND user_rol.id_semestre = '$period_id' 
                        AND user_rol.id_instancia = '$id_instance'
                  ORDER BY fullname ASC";

    $monitors = $DB->get_records_sql($sql_query);
    return $monitors;
}


function get_quantity_students_by_pract($id_practicant,$instance,$semester=null){

   global $DB;


    $total_quantity=0;

    $monitors = get_monitors_of_pract($id_practicant,$instance);


    if($semester == null){
       $semester = core_periods_get_current_period($instance)->id;
    }


    foreach ($monitors as $key => $monitor) {

        
        $sql_query = "SELECT COUNT(*) FROM {talentospilos_monitor_estud} WHERE id_monitor=$monitor->id_usuario and id_instancia=$instance and id_semestre=$semester"; 
        $quantity = $DB->get_record_sql($sql_query);
        $total_quantity+= $quantity->count;

    }

    return $total_quantity; 
}

/**
 * Función que obtiene estudiantes de un monitor en el periodo actual
 *
 * @see get_monitors_of_pract($id_pract,$id_instance)
 * @return Array
 */
function get_students_of_monitor($id_monitor,$id_instance, $period_id = null){
   global $DB;

    if (empty($period_id)) {
        $period_id = (core_periods_get_current_period($id_instance))->id;
    }

    $sql_query = 
    "SELECT ME.id, 
     ME.id_monitor,
     ME.id_estudiante, 
     ME.id_semestre,
     ME.id_instancia,
    U.username,
     CONCAT(U.firstname, ' ',U.lastname) AS fullname 
    FROM {talentospilos_monitor_estud} AS ME
    INNER JOIN {user} AS U ON U.id = (SELECT id_moodle_user
                                                  FROM {talentospilos_user_extended} extended
                                                  WHERE id_ases_user = ME.id_estudiante and tracking_status=1)
    WHERE ME.id_semestre = $period_id 
        AND ME.id_monitor = $id_monitor 
        AND ME.id_instancia = $id_instance
    ORDER BY fullname ASC";

    $students = $DB->get_records_sql($sql_query);
    
    return $students;
}







/**
 * Función que obtiene el nombre del rol de un usuario dado su username completo
 *
 * @see get_user_rol()
 * @return object rol
 */
function get_user_rol($username){
   global $DB;
    $sql_query = "SELECT nombre_rol FROM mdl_user as usuarios INNER JOIN mdl_talentospilos_user_rol as roles ON usuarios.id= roles.id_usuario INNER JOIN mdl_talentospilos_rol as nombres_roles ON nombres_roles.id= roles.id_rol where username='$username'";
    $role_name = $DB->get_record_sql($sql_query);
    return $role_name;
 
}

/**
 * Función que obtiene el nombre de un rol dado el id 
 *
 * @see get_role_name()
 * @return string
 */
function get_role_name($rol){
   global $DB;
    $sql_query = "select nombre_rol from {talentospilos_rol} where id='$rol'";
    $rolename = $DB->get_record_sql($sql_query);
    return $rolename;
 
}

/**
 * Función que obtiene el id de un role dado un nombre
 *
 * @see get_role_id()
 * @return integer
 */
function get_role_id($rol){
   global $DB;

    $sql_query = "SELECT id FROM {talentospilos_rol} WHERE nombre_rol = '$rol'";
    $roleid = $DB->get_record_sql($sql_query);
    return $roleid;
 
}


/**
 * Función que obtiene el id jefe de un rol ingresado
 *
 * @see get_user_boss()
 * @return Integer
 */
function get_user_boss($rol){

    $boss = null;

    if(get_role_name($rol)->nombre_rol=='monitor_ps'){
      $boss = get_role_id('practicante_ps')->id;

    }else if (get_role_name($rol)->nombre_rol == 'practicante_ps'){
      $boss = get_role_id('profesional_ps')->id;
    }

    return $boss;
}

/**
 * Función que relaciona a un conjunto de estudiantes con un monitor
 *
 * @see monitor_student_assignment()
 * @return booleano confirmando el éxito de la operación
 */
function monitor_student_assignment($username_monitor, $array_students, $idinstancia)
{
    global $DB;

    try{
        $sql_query = "SELECT id FROM {user} WHERE username = '$username_monitor'";
        $idmonitor = $DB->get_record_sql($sql_query);

        $first_insertion_sql = "SELECT MAX(id) FROM {talentospilos_monitor_estud};";
        $first_insertion_id = $DB->get_record_sql($first_insertion_sql);
        
        $insert_record = "";
        $array_errors = array();
        $hadErrors = false; 
       
        foreach($array_students as $student)
        {
                // $studentid = get_userById(array('*'),$student);
                $studentid =get_ases_user_by_code($student);
                $semestre_act = core_periods_get_current_period($idinstancia);



                if($studentid){
                    //se valida si el estudiante ya tiene asignado un monitor
                    $sql_query = "SELECT u.id as id, username,firstname, lastname FROM {talentospilos_monitor_estud} me INNER JOIN {user} u  ON  u.id = me.id_monitor WHERE me.id_semestre =".$semestre_act->id."  AND me.id_estudiante =".$studentid->id."";
                    $hasmonitor = $DB->get_record_sql($sql_query);
                
                    if(!$hasmonitor){
                        $object = new stdClass();
                        $object->id_monitor = $idmonitor->id;
                        $object->id_estudiante = $studentid->id;
                        $object->id_instancia = $idinstancia;
                        $object->id_semestre = $semestre_act->id;
              
                        $insert_record = $DB->insert_record('talentospilos_monitor_estud', $object, true);
                
                        if(!$insert_record){
                            $hadErrors = true; 
                            array_push($array_errors, "Error al asignar el estudiante ".$student." al monitor (monitor_student_assignment). Operaciòn de asignaciòn del estudiante anulada.");
                            
                        }
                
                    }elseif($hasmonitor->id != $idmonitor->id){
                        $hadErrors = true; 
                        array_push($array_errors,"El estudiante con codigo ".$student." ya tiene asigando el monitor: ".$hasmonitor->username."-".$hasmonitor->firstname."-".$hasmonitor->lastname.". Operaciòn de asignaciòn del estudiante anulada.");
                    }
                }else{
                    $hadErrors = true; 
                    if($student=='-1'){
                        array_push($array_errors,"Es necesario seleccionar el estudiante para asignarlo al monitor.");

                    }else{
                    array_push($array_errors,"El estudiante con codigo '".$student."' no se encontro en la base de datos. Operaciòn de asignaciòn del estudiante anulada.");
                    }
                } 
        }
        if(!$hadErrors){
            return 1;
        }else{
            $message = "";
            foreach ($array_errors as $error){
                $message .= "*".$error."<br>";
            }
            throw new MyException("Rol Actualizado con los siguientes inconvenientes:<br><hr>".$message);
        }
        
    
    }
    catch(MyException $ex){
        return $ex->getMessage();
    }
    catch(Exception $e){
        $error = "Error en la base de datos(monitor_student_assignment).".$e->getMessage();
        echo $error;
    }
}



/**
 * Función que asigna un rol a un usuario
 *
 * @see assign_role_user($username, $role, $status, $semester, $idinstancia, $username_boss, $id_academic_program){
 * @return Integer
 */
 
 function assign_role_user($username, $role, $state, $semester, $idinstancia, $username_boss = null, $id_academic_program = null){
     
    global $DB;

    $array_record = new stdClass;

    $sql_query = "SELECT id FROM {user} WHERE username='$username'";
    $id_user_moodle = $DB->get_record_sql($sql_query);
     
    $sql_query = "SELECT id FROM {talentospilos_rol} WHERE nombre_rol='$role';";
    $id_role = $DB->get_record_sql($sql_query);
    
    $id_semester = core_periods_get_current_period($idinstancia);
    
    if($role == "monitor_ps"){
        $sql_query = "SELECT * FROM {user} WHERE username='$username_boss'";
        $id_boss = $DB->get_record_sql($sql_query);    
    }else if($role == "director_prog"){
        $array_record->id_programa = $id_academic_program;
        $id_boss = null;
    }else if($role == "vcd_academico"){
        $array_record->id_programa = $id_academic_program;
        $id_boss = null;
    }else{
        $id_boss = null;
    }
        
    $array_record->id_rol = $id_role->id;
    $array_record->id_usuario = $id_user_moodle->id;
    $array_record->estado = $state;
    $array_record->id_semestre = $id_semester->id;
    $array_record->id_jefe = $id_boss;
    $array_record->id_instancia= $idinstancia;

    $insert_user_rol = $DB->insert_record('talentospilos_user_rol', $array_record, false);

    if($insert_user_rol){
        return 1;
    }
    else{
        return 2;
    }
}

/**
 * Función que actualiza el rol de un usuario en particular
 *
 * @see update_role_user($id_moodle_user, $id_role, $state, $id_semester, $username_boss){
 * @return Entero
 */
 function update_role_user($username, $role, $idinstancia, $state = 1, $semester = null, $username_boss = null, $id_academic_program = null){
    
    global $DB;
    
    $sql_query = "SELECT id FROM {user} WHERE username='$username'";
    $id_user_moodle = $DB->get_record_sql($sql_query);
     
    $sql_query = "SELECT id FROM {talentospilos_rol} WHERE nombre_rol='$role';";
    $id_role = $DB->get_record_sql($sql_query);
    
    $id_semester = core_periods_get_current_period($idinstancia)->id;
    
    $array = new stdClass;
    $id_boss = null;
    if($username_boss != null){
        $sql_query = "SELECT * FROM {user} WHERE username='$username_boss'";
        $result = $DB->get_record_sql($sql_query);
        $id_boss =  $result->id;
    }
    if($id_role){
    $array->id_rol = $id_role->id;
    }
    $array->id_usuario = $id_user_moodle->id;
    $array->estado = $state;
    $array->id_semestre = $id_semester;
    $array->id_jefe = $id_boss;
    $array->id_instancia = $idinstancia;
    $array->id_programa = $id_academic_program;
    
    $result = 0;
    
    if ($checkrole = checking_role($username, $idinstancia)){
        
        if ($checkrole->nombre_rol == 'monitor_ps'){
            $whereclause = "id_monitor = ".$id_user_moodle->id;
            $DB->delete_records_select('talentospilos_monitor_estud',$whereclause);
            
        }else if($checkrole->nombre_rol == 'profesional_ps'){ 
            $whereclause = "id_usuario = ".$id_user_moodle->id;
            $DB->delete_records_select('talentospilos_usuario_prof',$whereclause);
        } 
        
        $array->id = $checkrole->id;
        if ($array->id == 0) {
            trigger_error('ASES Notificacion: actualizar user_rol en la BD con id 0');
            $update_record = false;
        } else {
            $update_record = $DB->update_record('talentospilos_user_rol', $array);
        }

        if ($update_record) {
            $result = 3;
        } else {
            $result = 4;
        }
    }else{
        $insert_record = $DB->insert_record('talentospilos_user_rol', $array);
        if($insert_record){
            $result =1;
        }else{
            $result = 2;
        }
    }

    return $result;
}
