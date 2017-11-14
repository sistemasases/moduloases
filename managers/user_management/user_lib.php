<?php

require_once(dirname(__FILE__) . '/../../../../config.php');
require_once(dirname(__FILE__) . '/../periods_management/periods_lib.php');
require_once(dirname(__FILE__) . '/../role_management/role_management_lib.php');



/**
 * Función que verifica si un usuario ya tiene un rol asignado
 * @see verify_user_assign($username, $idinstancia)
 * @param $username ---> username del usuario
 * @param $instancia ---> id de la instancia actual
 * @return boolean
 **/

function verify_user_assign($username,$instancia){
    global $DB;

    $sql_query = "SELECT * FROM {user} WHERE username ='$username';";
    $object_user = $DB->get_record_sql($sql_query);

    $query_semestre = "SELECT * FROM {talentospilos_semestre} WHERE id = (SELECT MAX(id) FROM {talentospilos_semestre})";
    $object_semester = $DB->get_record_sql($query_semestre);


    $sql_query_user = "SELECT * from mdl_talentospilos_user_rol where id_instancia='$instancia' and id_usuario='$object_user->id' and id_semestre='$object_semester->id' and estado=0";
    
    $is_assign = $DB->get_record_sql($sql_query_user);


    if($is_assign){
        return true;
    }else{
        return false;
    }
}

/*
 * Función que retorna un arreglo con todos los estudiantes filtrados por estado de ases activo, cohorte y que no se encuentran asignados a otro monitor.
 * @param $instanceid
 * @return Array 
 */

function get_students($instanceid)
{
    global $DB;
    
    //Se consulta el programa al cual esta asociada la instancia
    $query_prog = "
        SELECT pgr.cod_univalle as cod
        FROM {talentospilos_instancia} inst
        INNER JOIN {talentospilos_programa} pgr ON inst.id_programa = pgr.id
        WHERE inst.id_instancia= $instanceid";
    
    $prog = $DB->get_record_sql($query_prog)->cod;
    
    //Si el código del programa es 1008 la cohorte comenzará por SP y si no, empezará por el código del programa
    if ($prog === '1008') {
        $cohort = 'SP';
    } else {
        $cohort = $prog;
    }
    $query_semestre = "SELECT nombre FROM {talentospilos_semestre} WHERE id = (SELECT MAX(id) FROM {talentospilos_semestre})";
    $sem            = $DB->get_record_sql($query_semestre)->nombre;
    
    $año = substr($sem, 0, 4);
    
    if (substr($sem, 4, 1) == 'A') {
        $semestre = $año . '02';
    } else if (substr($sem, 4, 1) == 'B') {
        $semestre = $año . '08';
    }
   
    
    $query_courses = "SELECT *
    FROM (SELECT *
    FROM {talentospilos_monitor_estud} as monitor_estud
    INNER JOIN (select * from {user_info_data} where fieldid=(select id from {user_info_field} where shortname='idtalentos')) as info_data ON CAST(monitor_estud.id_estudiante as TEXT) = info_data.data
    ) as monitor_estud
    RIGHT JOIN (SELECT user_m.id, user_m.username,user_m.firstname,user_m.lastname
     FROM  {user} user_m
     INNER JOIN {user_info_data} data ON data.userid = user_m.id
     INNER JOIN {user_info_field} field ON data.fieldid = field.id
     INNER JOIN {talentospilos_usuario} user_t ON data.data = CAST(user_t.id AS VARCHAR)
     INNER JOIN {talentospilos_est_estadoases} estado_u ON user_t.id = estado_u.id_estudiante
     INNER JOIN {talentospilos_estados_ases} estados ON estados.id = estado_u.id_estado_ases
     WHERE estados.nombre = 'ACTIVO/SEGUIMIENTO' AND field.shortname = 'idtalentos'

    INTERSECT

    SELECT user_m.id, user_m.username,user_m.firstname,user_m.lastname
    FROM {user} user_m INNER JOIN {cohort_members} memb ON user_m.id = memb.userid INNER JOIN {cohort} cohorte ON memb.cohortid = cohorte.id
        WHERE cohorte.idnumber LIKE '$cohort%') as estudiantes

    ON monitor_estud.userid = estudiantes.id
    WHERE monitor_estud.userid IS NULL order by firstname";
    
    
    $result = $DB->get_records_sql($query_courses);
    return $result;
}



/**
 * Función que ontiene los posibles jefes de un usuario dado el rol
 * @see get_boss_users($rol, $idinstancia)
 * @param $id_rol ---> rol del usuario
 * @param $idinstancia ---> id de la instancia actual
 * @return Array
 **/

function get_boss_users($id_rol, $idinstancia)
{
    global $DB;
    
    $boss_role = get_user_boss($id_rol);
    
    $sql_query = "SELECT username, firstname, lastname, id FROM {user} us  WHERE id IN (SELECT id_usuario FROM {talentospilos_user_rol} ur WHERE id_rol=" . $boss_role . " AND ur.id_instancia =" . $idinstancia . ")";
    return $DB->get_records_sql($sql_query);
}


/**
 * Función que obtiene el id de usuario dado su username
 * @see get_userid_by_username($username)
 * @param $username -->
 * @return Array
 **/

function get_userid_by_username($username)
{
    global $DB;
    
    $sql_query = "SELECT * from {user} where username='$username'";
    return $DB->get_record_sql($sql_query);
}



/**
 * Función que retorna los usuarios en el sistema dado la instancia
 * @see get_professionals($id, $idinstancia)
 * @param $id ---> id de usuario
 * @param $idinstancia ---> id de la instancia actual
 * @return Array
 **/

function get_professionals($id = null, $idinstancia)
{
    global $DB;
    
    $sql_query = "SELECT username, firstname, lastname, id FROM {user} us  WHERE id IN (SELECT id_usuario FROM {talentospilos_user_rol} ur WHERE id_rol IN (3,7) AND ur.id_instancia =" . $idinstancia . ")";
    
    if ($id != null)
        $sql_query .= " AND us.id =" . $id . ";";
    return $DB->get_records_sql($sql_query);
}

/**
 * Función que retorna los usuarios en el sistema dado la instancia
 * @see get_users_role($idinstancia)
 * @param $idinstancia ---> id de la instancia actual
 * @return Array
 **/

function get_users_role($idinstancia)
{
    global $DB;
    $array       = Array();
    $sql_query   = "SELECT {user}.id, {user}.username, {user}.firstname, {user}.lastname, {talentospilos_rol}.nombre_rol FROM {talentospilos_user_rol} INNER JOIN {user} ON {talentospilos_user_rol}.id_usuario = {user}.id 
                                INNER JOIN {talentospilos_rol} ON {talentospilos_user_rol}.id_rol = {talentospilos_rol}.id INNER JOIN {talentospilos_semestre} s ON  s.id = {talentospilos_user_rol}.id_semestre 
                                WHERE {talentospilos_user_rol}.estado = 1 AND {talentospilos_user_rol}.id_instancia=" . $idinstancia . " AND s.id = (SELECT MAX(id) FROM {talentospilos_semestre});";
    $users_array = $DB->get_records_sql($sql_query);
    
    foreach ($users_array as $user) {
        $user->button = "<a id = \"delete_user\"  ><span  id=\"" . $user->id . "\" class=\"red glyphicon glyphicon-remove\"></span></a>";
        array_push($array, $user);
    }
    return $array;
}

/**
 * Función que elimina de base de datos la relacion monitor-estudiante
 * @see  drop_student_of_monitor($monitor,$student)
 * @param $monitor [string] username en moodle del ususario del monitor 
 * @param $student [string] username en moodle del usuario estudiante
 * @return boolean
 **/

function drop_student_of_monitor($monitor, $student)
{
    global $DB;
    
    //idmonitor
    $sql_query = "SELECT id FROM {user} WHERE username = '$monitor'";
    $idmonitor = $DB->get_record_sql($sql_query);
    
    //se obtiene el id en la tabla de {talentospilos_usuario} del estudiante
    $studentid = get_userById(array(
        'idtalentos'
    ), $student);
    
    //where clause
    $whereclause = "id_monitor = " . $idmonitor->id . " AND id_estudiante =" . $studentid->idtalentos;
    return $DB->delete_records_select('talentospilos_monitor_estud', $whereclause);
    
}


/**
 * dropStudentofMonitor
 * 
 * Elimina de base de datos la relacion monitor - estudiante
 * @param $monitor [string] username en moodle del ususario del monitor 
 * @param $student [string] username en moodle del usuario studiante
 * @return void
 **/

function dropStudentofMonitor($monitor, $student)
{
    global $DB;
    
    //idmonitor
    $sql_query = "SELECT id FROM {user} WHERE username = '$monitor'";
    $idmonitor = $DB->get_record_sql($sql_query);
    
    //se obtiene el id en la tabla de {talentospilos_usuario} del estudiante
    $studentid = get_userById(array(
        'idtalentos'
    ), $student);
    
    $semestre_act = get_current_semester();
    
    if ($studentid) {
        //where clause
        $whereclause = "id_monitor = " . $idmonitor->id . " AND id_estudiante =" . $studentid->idtalentos . " AND id_semestre=" . $semestre_act->max;
        return $DB->delete_records_select('talentospilos_monitor_estud', $whereclause);
    }
}


function changeMonitor($oldMonitor, $newMonitor)
{
    global $DB;
    
    
    try {
        $lastsemester = get_current_semester();
        
        $sql_query = "SELECT  id from {talentospilos_monitor_estud} where id_semestre=" . $lastsemester->max . " and id_monitor =" . $oldMonitor;
        $result    = $DB->get_records_sql($sql_query);
        
        foreach ($result as $row) {
            $newObject             = new stdClass();
            $newObject->id         = $row->id;
            $newObject->id_monitor = $newMonitor;
            $DB->update_record('talentospilos_monitor_estud', $newObject);
        }
        
        return 1;
        
    }
    catch (Exception $e) {
        return $e->getMessage();
    }
    
}

/**
 * Función que actualiza el rol de un usuario practicante_ps
 * @see  actualiza_rol_practicante($id_moodle_user, $id_role, $state, $id_semester, $username_boss)
 * @param $username ---> username en moodle del usuario del monitor 
 * @param $role     --->[string] username en moodle del usuario estudiante
 * @return Integer
 **/

function actualiza_rol_practicante($username, $role, $idinstancia, $state = 1, $semester = null, $id_boss = null)
{
    
    global $DB;
    
    $sql_query      = "SELECT id FROM {user} WHERE username='$username'";
    $id_user_moodle = $DB->get_record_sql($sql_query);
    
    $sql_query = "SELECT id FROM {talentospilos_rol} WHERE nombre_rol='$role';";
    $id_role   = $DB->get_record_sql($sql_query);
    
    $sql_query   = "select max(id) as id from {talentospilos_semestre};";
    $id_semester = $DB->get_record_sql($sql_query);
    
    $array = new stdClass;
    
    $array->id_rol       = $id_role->id;
    $array->id_usuario   = $id_user_moodle->id;
    $array->estado       = $state;
    $array->id_semestre  = $id_semester->id;
    $array->id_jefe      = (int) $id_boss;
    $array->id_instancia = $idinstancia;
    
    $result = 0;
    
    if ($array->id_usuario == $array->id_jefe) {
        $result = 5;
        return $result;
    }
    
    if ($checkrole = checking_role($username, $idinstancia)) {
        
        if ($checkrole->nombre_rol == 'monitor_ps') {
            $whereclause = "id_monitor = " . $id_user_moodle->id;
            $DB->delete_records_select('talentospilos_monitor_estud', $whereclause);
            
        } else if ($checkrole->nombre_rol == 'profesional_ps') {
            
            $whereclause = "id_usuario = " . $id_user_moodle->id;
            $DB->delete_records_select('talentospilos_usuario_prof', $whereclause);
        }
        
        $array->id     = $checkrole->id;
        $update_record = $DB->update_record('talentospilos_user_rol', $array);
        //echo $update_record;
        if ($update_record) {
            $result = 3;
        } else {
            $result = 4;
        }
    } else {
        $insert_record = $DB->insert_record('talentospilos_user_rol', $array);
        if ($insert_record) {
            $result = 1;
        } else {
            $result = 2;
        }
    }
    return $result;
}


/*
 *********************************************************************************
 FIN FUNCIONES RELACIONADAS CON EL ROL PROFESIONAL PSICOEDUCATIVO
 *********************************************************************************
 */

/**
 * Función encargada de actualizar el rol monitor
 * @see  actualiza_rol_practicante($id_moodle_user, $id_role, $state, $id_semester, $username_boss)
 * @param $username       ---> username en moodle del usuario del monitor 
 * @param $role           --->[string] username en moodle del usuario estudiante
 * @param $array_students ---> array estudiantes asignados al monitor actual
 * @param $boss           ---> usuario jefe
 * @param $idinstancia    ---> id de la instancia actual
 * @param $state          ---> estado del usuario
 * @return Integer
 **/
function update_role_monitor_ps($username, $role, $array_students, $boss, $idinstancia, $state = 1)
{
    global $DB;
    
    $sql_query = "SELECT id FROM {user} WHERE username ='$username';";
    $id_moodle = $DB->get_record_sql($sql_query);
    
    //se consulta el id del semestre actual
    $sql_query = "select max(id) as id_semestre from {talentospilos_semestre};";
    $semestre  = $DB->get_record_sql($sql_query);
    
    $sql_query     = "SELECT rol.id as id, rol.nombre_rol as nombre_rol, ur.id as id_user_rol, id_usuario FROM {talentospilos_user_rol} ur INNER JOIN {talentospilos_rol} rol ON rol.id = ur.id_rol  WHERE id_usuario = " . $id_moodle->id . " and id_semestre =" . $semestre->id_semestre . " AND ur.id_instancia=" . $idinstancia . ";";
    $id_rol_actual = $DB->get_record_sql($sql_query);
    
    
    //se consulta el id del rol
    $sql_query = "SELECT id FROM {talentospilos_rol} WHERE nombre_rol='monitor_ps';";
    $id_role   = $DB->get_record_sql($sql_query);
    
    //se consulta el jefe
    $bossid = null;
    if (intval($boss)) {
        if (get_professionals($boss, $idinstancia))
            $bossid = $boss;
    }
    
    $object_role               = new stdClass;
    $object_role->id_rol       = $id_role->id;
    $object_role->id_usuario   = $id_moodle->id;
    $object_role->estado       = $state;
    $object_role->id_semestre  = $semestre->id_semestre;
    $object_role->id_jefe      = $bossid;
    $object_role->id_instancia = $idinstancia;
    
    if (empty($id_rol_actual)) {
        $insert_user_rol = $DB->insert_record('talentospilos_user_rol', $object_role, true);
        
        if ($insert_user_rol) {
            //procesar el array de estudiantes
            $check_assignment = monitor_student_assignment($username, $array_students, $idinstancia);
            if ($check_assignment == 1) {
                return 1;
            } else {
                return $check_assignment;
            }
        } else {
            return 2;
        }
    } else {
        if ($id_rol_actual->nombre_rol == 'profesional_ps') {
            
            $whereclause = "id_usuario = " . $id_rol_actual->id_usuario;
            $DB->delete_records_select('talentospilos_usuario_prof', $whereclause);
        }
        $object_role->id = $id_rol_actual->id_user_rol;
        $DB->update_record('talentospilos_user_rol', $object_role);
        
        $check_assignment = monitor_student_assignment($username, $array_students, $idinstancia);
        
        if ($check_assignment == 1) {
            return 3;
        } else {
            return $check_assignment;
        }
    }
}

/**
 * Función que administra el rol profesional psicoeducativo
 * @see  manage_role_profesional_ps($username, $role, $professional, $idinstancia, $state = 1)
 * @param $username       ---> username en moodle del usuario del profesional  
 * @param $role           ---> rol del usuario
 * @param $professional   ---> id del usuario
 * @param $idinstancia    ---> id de la instancia actual
 * @param $state          ---> estado del usuario
 * @return Integer
 **/
function manage_role_profesional_ps($username, $role, $professional, $idinstancia, $state = 1)
{
    global $DB;
    
    try {
        // Select object user
        $sql_query   = "SELECT * FROM {user} WHERE username ='$username';";
        $object_user = $DB->get_record_sql($sql_query);
        
        // Current role
        pg_query("BEGIN") or die("Could not start transaction\n");
        $sql_query       = "SELECT id_rol, nombre_rol FROM {talentospilos_user_rol} ur INNER JOIN {talentospilos_rol} r ON r.id = ur.id_rol WHERE id_usuario = " . $object_user->id . " AND ur.id_instancia=" . $idinstancia . " AND  id_semestre = (SELECT max(id) FROM {talentospilos_semestre});";
        $id_current_role = $DB->get_record_sql($sql_query);
        pg_query("COMMIT") or die("Transaction commit failed\n");
        
        $id_current_semester = get_current_semester();
        
        if (empty($id_current_role)) {
            
            // Start db transaction
            pg_query("BEGIN") or die("Could not start transaction\n");
            
            assign_role_user($username, $role, 1, $id_current_semester->max, $idinstancia, null);
            
            assign_professional_user($object_user->id, $professional);
            
            // End db transaction
            pg_query("COMMIT") or die("Transaction commit failed\n");
            
        } else {
            //en la consulta se hace tiene en cuenta el semestre concurrente
            $sql_query        = "SELECT * FROM {talentospilos_user_rol} userrol INNER JOIN {talentospilos_usuario_prof} userprof 
                            ON userrol.id_usuario = userprof.id_usuario INNER JOIN {talentospilos_rol} rol ON rol.id = userrol.id_rol  WHERE userprof.id_usuario = " . $object_user->id . " AND userrol.id_semestre=" . $id_current_semester->max . " AND userrol.id_instancia = " . $idinstancia . ";";
            $object_user_role = $DB->get_record_sql($sql_query);
            
            if ($object_user_role) {
                // Incluir el estado
                
                $sql_query                = "SELECT id FROM {talentospilos_profesional} WHERE nombre_profesional = '$professional'";
                $new_id_professional_type = $DB->get_records_sql($sql_query);
                
                foreach ($new_id_professional_type as $n) {
                    if ($object_user_role->id_profesional != $n->id) {
                        update_professional_user($object_user->id, $professional);
                    }
                }
                if ($state == 0) {
                    //se actualiza el estado en caso de que se hjaya desactivado anteriormente
                    update_role_user($username, $role, $idinstancia, $state);
                    $whereclause = "id_usuario = " . $object_user->id;
                    $DB->delete_records_select('talentospilos_usuario_prof', $whereclause);
                }
            } else {
                //case : monitor
                pg_query("BEGIN") or die("Could not start transaction\n");
                if ($id_current_role->nombre_rol == 'monitor_ps') {
                    
                    $lastsemester = get_current_semester();
                    $whereclause  = "id_semestre =" . $lastsemester->max . " and  id_monitor = " . $object_user->id;
                    $DB->delete_records_select('talentospilos_monitor_estud', $whereclause);
                }
                update_role_user($username, $role, $idinstancia, $state, $id_current_semester->max, null);
                assign_professional_user($object_user->id, $professional);
                pg_query("COMMIT") or die("Transaction commit failed\n");
            }
        }
        return 1;
        
    }
    catch (Exception $e) {
        return "Error al gestionar los permisos profesional " . $e->getMessage();
    }
    
}

/**
 * Función que obtiene el usuario deacuerdo al id
 * @see  get_userById($column, $id)
 * @param $column        
 * @param $id           
 * @return array usuario
 **/
function get_userById($column, $id)
{
    global $DB;
    
    $columns_str = "";
    for ($i = 0; $i < count($column); $i++) {
        
        $columns_str = $columns_str . $column[$i] . ",";
    }
    
    if (strlen($id) > 7) {
        $id = substr($id, 0, -5);
    }
    
    $columns_str = trim($columns_str, ",");
    $sql_query   = "SELECT " . $columns_str . ", (now() - fecha_nac)/365 AS age  FROM (SELECT *, idnumber as idn, name as namech FROM {cohort}) AS ch INNER JOIN (SELECT * FROM {cohort_members} AS chm INNER JOIN ((SELECT * FROM (SELECT *, id AS id_user FROM {user}) AS userm INNER JOIN (SELECT userid, CAST(d.data as int) as data FROM {user_info_data} d WHERE d.data <> '' and fieldid = (SELECT id FROM  {user_info_field} as f WHERE f.shortname ='idtalentos')) AS field ON userm. id_user = field.userid ) AS usermoodle INNER JOIN (SELECT *,id AS idtalentos FROM {talentospilos_usuario}) AS usuario ON usermoodle.data = usuario.id) AS infouser ON infouser.id_user = chm.userid) AS userchm ON ch.id = userchm.cohortid WHERE userchm.id_user in (SELECT userid FROM {user_info_data} as d INNER JOIN {user_info_field} as f ON d.fieldid = f.id WHERE f.shortname ='estado' AND d.data ='ACTIVO') AND substr(userchm.username,1,7) = '" . $id . "';";
    
    $result_query = $DB->get_record_sql($sql_query);
    //se formatea el codigo  para eliminar la info del programa
    if ($result_query) {
        if (property_exists($result_query, 'username'))
            $result_query->username = substr($result_query->username, 0, -5);
    }
    return $result_query;
}

/**
 * Función que recupera los usuarios asociados al curso de ASES dado el nombre del curso.
 * @see  get_course_user($namecourse)
 * @param $namecourse        
 * @return Array con usuarios asociados al curso
 **/
function get_course_user($namecourse)
{
    
    global $DB;
    
    $sql_query = "SELECT usuario.username as codigo, usuario.firstname as nombre, usuario.lastname as apellido FROM {course} course INNER JOIN  {enrol} enrol ON 
    (enrol.courseid= course.id) INNER JOIN  {user_enrolments} userenrolments ON (userenrolments.enrolid= enrol.id)INNER JOIN  {user} usuario ON (usuario.id= userenrolments.userid) where fullname='$namecourse';";
    
    $courseusers = $DB->get_records_sql($sql_query);
    
    return $courseusers;
}

/**
 * Función que recupera los usuarios asociados al curso de ASES dado el id del curso.
 * @see  get_course_user($idcourse)
 * @param $idcourse        
 * @return Array con usuarios asociados al curso
 **/
function get_course_usersby_id($id)
{
    
    global $DB;
    
    $sql_query = "SELECT usuario.username as codigo, usuario.firstname as nombre, usuario.lastname as apellido FROM {course} course INNER JOIN  {enrol} enrol ON 
    (enrol.courseid= course.id) INNER JOIN  {user_enrolments} userenrolments ON (userenrolments.enrolid= enrol.id)INNER JOIN  {user} usuario ON (usuario.id= userenrolments.userid) where course.id='$id';";
    
    $courseusers = $DB->get_records_sql($sql_query);
    
    return $courseusers;
    
}


/**
 * Función que recupera los campos de usuario de la tabla {user}
 *
 * @see get_moodle_user($id)
 * @param id_student --> id correspondiente a la tabla {user}
 * @return Array 
 */
function get_moodle_user($id)
{
    
    global $DB;
    
    $sql_query = "SELECT SUBSTRING(username FROM 1 FOR 7) AS code, email AS email_moodle, firstname, lastname
                  FROM {user} WHERE id = $id";
    $user      = $DB->get_record_sql($sql_query);
    
    return $user;
}

/**
 * Función que revisa si un usuario tiene un rol asignado
 *
 * @see checking_role($username)
 * @param $username    ---> username en moodle del usuario
 * @param $idinstancia ---> id de la instacia actual
 * @return Array (id_rol, nombre_rol, id, estado, id_usuario)
 */

function checking_role($username, $idinstancia)
{
    
    global $DB;
    
    $sql_query      = "SELECT id FROM {user} WHERE username = '$username'";
    $id_moodle_user = $DB->get_record_sql($sql_query);
    
    $semestre = get_current_semester();
    
    $sql_query  = "SELECT ur.id_rol as id_rol , r.nombre_rol as nombre_rol, ur.id as id, ur.id_usuario, ur.estado FROM {talentospilos_user_rol} ur INNER JOIN {talentospilos_rol} r ON r.id = ur.id_rol WHERE ur.id_usuario = " . $id_moodle_user->id . " and ur.id_semestre = " . $semestre->max . " and ur.id_instancia=" . $idinstancia . ";";
    $role_check = $DB->get_record_sql($sql_query);
    
    return $role_check;
}

/**
 * Función que actualiza el tipo de profesional a un usuario con rol profesional psicoeducativo
 *
 * @see update_professional_user($id_user, $professional)
 * @param $id_user    ---> id del usuario
 * @param $professional
 * @return boolean
 */

function update_professional_user($id_user, $professional)
{
    
    global $DB;
    
    $sql_query       = "SELECT id FROM {talentospilos_profesional} WHERE nombre_profesional = '$professional'";
    $id_professional = $DB->get_record_sql($sql_query);
    
    if ($id_professional) {
        $sql_query    = "SELECT id FROM {talentospilos_usuario_prof} WHERE id_usuario = '$id_user'";
        $id_to_update = $DB->get_record_sql($sql_query);
        
        $record_professional_type                 = new stdClass;
        $record_professional_type->id             = $id_to_update->id;
        $record_professional_type->id_profesional = $id_professional->id;
        
        $update_record = $DB->update_record('talentospilos_usuario_prof', $record_professional_type);
        
        return $update_record;
    } else {
        return false;
    }
    
}

/**
 * Función que asigna un tipo de profesional a un usuario con rol profesional psicoeducativo
 *
 * @see assign_professional_user($id_user, $professional)
 * @param $id_user    ---> id del usuario
 * @param $professional
 * @return Integer
 */

function assign_professional_user($id_user, $professional)
{
    
    global $DB;
    
    $sql_query       = "SELECT id FROM {talentospilos_profesional} WHERE nombre_profesional = '$professional'";
    $id_professional = $DB->get_record_sql($sql_query);
    
    $record_professional_type                 = new stdClass;
    $record_professional_type->id_usuario     = $id_user;
    $record_professional_type->id_profesional = $id_professional->id;
    
    $insert_record = $DB->insert_record('talentospilos_usuario_prof', $record_professional_type, true);
    
    return $insert_record;
}