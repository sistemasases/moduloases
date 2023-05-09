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

use core_user\search\user;

require_once(dirname(__FILE__) . '/../../../../config.php');
require_once(dirname(__FILE__) . '/../periods_management/periods_lib.php');
require_once(dirname(__FILE__) . '/../lib/student_lib.php');
require_once(dirname(__FILE__) . '/../role_management/role_management_lib.php');
require_once(dirname(__FILE__) . '/user_management_lib.php');
require_once(dirname(__FILE__) . '/../monitor_assignments/monitor_assignments_lib.php');
require_once(dirname(__FILE__) . '/../../core/module_loader.php');

module_loader('periods');

/**
 * Function that verifies if an user has a role assigned
 * @see verify_user_assign($username, $idinstancia)
 * @param $username ---> username
 * @param $instancia ---> current instance id
 * @return boolean
 **/

function verify_user_assign($username,$instancia){
    global $DB;

    $sql_query = "SELECT * FROM {user} WHERE username ='$username';";
    $object_user = $DB->get_record_sql($sql_query);

    $object_semester = core_periods_get_current_period($idinstancia); 


    $sql_query_user = "SELECT * from mdl_talentospilos_user_rol where id_instancia='$instancia' and id_usuario='$object_user->id' and id_semestre='$object_semester->id' and estado=0";
    
    $is_assign = $DB->get_record_sql($sql_query_user);


    if($is_assign){
        return true;
    }else{
        return false;
    }
}

/*
 * Function that returns an array of filtered students by active state, cohort and not assigned to other monitor
 * @param $instanceid
 * @return Array 
 */

function get_students($instanceid)
{
    global $DB;
    
    //the program which is associated with the instance is consulted


    $sem = core_periods_get_current_period($instanceid)->nombre;
    
    $año = substr($sem, 0, 4);
    
    if (substr($sem, 4, 1) == 'A') {
        $semestre = $año . '02';
    } else if (substr($sem, 4, 1) == 'B') {
        $semestre = $año . '08';
    }

    $query_courses = "SELECT *
    FROM (SELECT *
    FROM {talentospilos_monitor_estud}  as monitor_estud
     INNER JOIN {talentospilos_user_extended}  extended ON monitor_estud.id_estudiante = extended.id_ases_user
    ) as monitor_estud
    RIGHT JOIN (SELECT user_m.id, user_m.username,user_m.firstname,user_m.lastname
     FROM  {user}  user_m
         INNER JOIN {talentospilos_user_extended}  extended ON user_m.id = extended.id_moodle_user 
         INNER JOIN {talentospilos_usuario}  user_t ON extended.id_ases_user = user_t.id
         INNER JOIN {talentospilos_est_estadoases}  estado_u ON user_t.id = estado_u.id_estudiante
         INNER JOIN {talentospilos_estados_ases}  estados ON estados.id = estado_u.id_estado_ases
         WHERE estados.nombre = 'seguimiento' 
    INTERSECT

    SELECT user_m.id, user_m.username,user_m.firstname,user_m.lastname
    FROM {user}  user_m INNER JOIN {cohort_members}  memb ON user_m.id = memb.userid 
    WHERE memb.cohortid IN (SELECT id_cohorte
                            FROM   {talentospilos_inst_cohorte}
                            WHERE  id_instancia = $instanceid)) as estudiantes

    ON monitor_estud.id_moodle_user = estudiantes.id
    WHERE monitor_estud.id_moodle_user IS NULL  order by firstname";
    
    
    $result = $DB->get_records_sql($query_courses);
    return $result;
}



/**
 * Function that contains all posible user's bosses given a role
 * @see get_boss_users($rol, $idinstancia)
 * @param $id_rol ---> user's role
 * @param $idinstancia ---> current instance id
 * @return array
 **/

function get_boss_users($id_rol, $idinstancia)
{
    global $DB;
    
    $boss_role = get_user_boss($id_rol);
    
    $sql_query = "SELECT username, firstname, lastname, id FROM {user} us  WHERE id IN (SELECT id_usuario FROM {talentospilos_user_rol} ur WHERE id_rol=" . $boss_role . " AND ur.id_instancia =" . $idinstancia . ")";
    return $DB->get_records_sql($sql_query);
}

/**
 * Return the username for a given student code and program code
 *
 *
 * **If code of student or program are invalid, null is returned**
 * @param $student_code string|number Can be with '20' prefix or without this.
 *  Examples: 201327951, 1327951
 *  Should have size 7 or 9
 * @param $program_code number|string Should be numeric and have four characters
 *  Example: 3743
 * @return  null|string If the given inputs are invalid, null is returned,
 *  the moodle user name is returned otherwise
 * @see get_username_moodle_regex() The username returned is compatible with that regex
 */
function generate_username($student_code, $program_code) {
    $student_code_ = $student_code;
    switch(strlen((string) $student_code)) {
        case 7:
            break;
        case 9:
            $student_code_ = substr($student_code, 2, strlen($student_code));
            break;
        default:

            return null;

    }
    if(strlen((string) $program_code) !== 4) {

        return null;
    }
    return "$student_code_-$program_code";
}

/**
 * Return the short student code without the first two digits,
 * for example, if the full student code is 201327952, returns
 * 1327951, if 1327951 is given as input, 1327951 is returned,
 * if the code does not have size 7 or 9, false is returned
 * @return int|string|null The short student code if the given code
 *  is valid, null otherwise
 */

function user_get_short_code($student_code)
{
    switch (strlen((string)$student_code)) {
        case 7:
            return $student_code;
        case 9:
            return substr($student_code, 2, strlen($student_code));
            break;
        default:
            return null;
    }
}

/**
 * Check if the user code is already registred in moodle
 * @param $student_code Can be with year first two digits or in short format without they
 * @return bool
 * @throws dml_exception
 */
function user_moodle_code_repeated($student_code): bool {
    global $DB;
    $short_code = user_get_short_code($student_code);
    $sql = <<<SQL
    SELECT id from mdl_user
    where username like '$short_code-%'
SQL;
    return $DB->record_exists_sql($sql);
}
/**
 * Return an array of moodle users if exist by student code
 * @param $student_code Can be with year first two digits or in short format without they
 * @return array Mdl users
 * @throws dml_exception
 */
function user_moodle_get_by_code($student_code): array {
    global $DB;
    $short_code = user_get_short_code($student_code);
    $sql = <<<SQL
    SELECT * from mdl_user
    where username like '$short_code-%'
SQL;
    $db_result = $DB->get_records_sql($sql);
    return array_values($db_result);
}
/**
 * Checkea si el nombre completo ya esta registrado para otro usuario ases
 * @param $nombre_completo
 * @return bool
 * @throws dml_exception
 * @author Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 */
function user_duplicated_full_name($full_name) {
    global $DB;
    $sql = <<<SQL
    select * from (
      SELECT concat(mdl_user.firstname, ' ', mdl_user.lastname) as nombre_completo 
      from {talentospilos_user_extended} mdl_talentospilos_user_extended
          inner join {user} mdl_user
                 on mdl_user.id = mdl_talentospilos_user_extended.id_moodle_user
     ) as r where r.nombre_completo = '$full_name'
SQL;
    return $DB->record_exists_sql($sql);

}
/**
 * Checkea si el codigo ya esta registrado para otro usuario ases
 * @param $codigo string|int Codigo de estudiante, puede tener 7 o 9 caracteres
 * @author Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @return bool
 * @throws dml_exception
 */
function user_duplicated_student_code($student_code) {
    global $DB;
    $short_student_code = user_get_short_code($student_code);
    $sql = <<<SQL
    select 1 from mdl_user where username like '$short_student_code-%';
SQL;
    return $DB->record_exists_sql($sql);
}
/**
 * Check if the student code is valid
 * @param $student_code string|int
 * @author Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @return bool
 */
function user_valid_student_code($student_code) {
    if(!is_numeric($student_code) && !is_string($student_code)) {
        return false;
    }
    $student_code_length = strlen((string)$student_code);
    return $student_code_length === 7 || $student_code_length === 9 ;
}
/**
 * Return the user password
 * @param $code string|int
 * @param $firsname string
 * @param $lastname string
 * @return string The password in format $passwd = FIRST_LETTER_OF_FIRSTNAME_IN_UPPER.SHORT_CODE.FIRST_LETTER_OF_LASTNAME_IN_UPPER
 *  If the code is invalid, or firstname or lastname is empty, false is returned
 */
function user_get_password($code , $firsname, $lastname): string {
    switch(strlen($code)) {
        case 9: $code = substr($code, 2, 9); break;
        case 7:  break;
        default: return false;
    }
    if(is_null($firsname) || is_numeric($firsname) || $firsname === '') {
        return false;
    }
    if(is_null($lastname) || is_numeric($lastname) || $lastname === '') {
        return false;
    }
    return strtoupper($firsname[0]). $code . strtoupper($lastname[0]);
}

/**
 * Function than gets the regex used by Univalle for the moodle usernames
 *
 * Current format is in the form 1327951-3794
 * @return string Regex
 */

function user_get_username_moodle_regex(): string {
    return '/[0-9]{7}-[0-9]{4}/';
}

/**
 * Check if a string given is a valid user name
 *
 * @see get_username_moodle_regex()
 * @param string $username
 * @return bool True if the moodle username is valid
 */
function valid_moodle_username(string $username): bool {
    if( preg_match(user_get_username_moodle_regex(), $username) == 0) {
        return false;
    }
    return true;
}
/**
 * Function that gets the user's id given his username
 * @see get_userid_by_username($username)
 * @param $username --> username
 * @return array
 **/

function get_userid_by_username($username)
{
    global $DB;
    
    $sql_query = "SELECT * from {user} where username='$username'";
    return $DB->get_record_sql($sql_query);
}



/**
 * Function that returns every professional user given an instance
 * @see get_professionals($id, $idinstancia)
 * @param $id ---> id de usuario
 * @param $idinstancia ---> current instance id
 * @return array
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
 * Function that returns every user's role given an instance
 * @see get_users_role($idinstancia)
 * @param $idinstancia ---> current instance id
 * @return array
 **/

function get_users_role($idinstancia)
{
    global $DB;
    $current_period = core_periods_get_current_period($idinstancia);

    $array       = Array();
    $sql_query   = 
        "SELECT {user}.id, {user}.username, {user}.firstname, {user}.lastname, {talentospilos_rol}.nombre_rol 
        FROM {talentospilos_user_rol} 
        INNER JOIN {user} 
            ON {talentospilos_user_rol}.id_usuario = {user}.id 
        INNER JOIN {talentospilos_rol} 
            ON {talentospilos_user_rol}.id_rol = {talentospilos_rol}.id INNER JOIN {talentospilos_semestre} s ON  s.id = {talentospilos_user_rol}.id_semestre 
        WHERE {talentospilos_user_rol}.estado = 1 AND {talentospilos_user_rol}.id_instancia=" . $idinstancia . " AND s.id = $current_period->id";

    $users_array = $DB->get_records_sql($sql_query);
    
    foreach ($users_array as $user) {
        //$user->button = "<a id = delete_user$user->id ><span  id=\"" . $user->id . "\" class=\"red glyphicon glyphicon-remove\"></span></a>";
        $user->button = "<span class=delete_user id=$user->id > </span>";
        array_push($array, $user);
    }
    return $array;
}

/**
 * Function used to delete the monitor-estudiante relation from database
 * @deprecated since version buuuuu!
 * 
 * @see  drop_student_of_monitor($monitor,$student)
 * @param $monitor [string] monitor's username in moodle
 * @param $student [string] student's username in moodle
 * @return boolean
 **/

function drop_student_of_monitor($monitor, $student)
{
    global $DB;
    
    //monitor id
    $sql_query = "SELECT id FROM {user} WHERE username = '$monitor'";
    $idmonitor = $DB->get_record_sql($sql_query);
    
    //OBSOLETE METHOD
    // $studentid = get_userById(array(
    //     'idtalentos'
    // ), $student);

    //id is gotten from student's {talentospilos_usuario} table    
    $studentid = get_ases_user_by_code($student);
    
    //where clause
    $whereclause = "id_monitor = " . $idmonitor->id . " AND id_estudiante =" . $studentid->id;
    
    return -1; // $DB->delete_records_select('talentospilos_monitor_estud', $whereclause);
    
}

/**
 * Deletes relation monitor-estudiante from database
 * @see dropStudentofMonitor($monitor, $student)
 * @param $monitor [string] username en moodle del ususario del monitor 
 * @param $student [string] username en moodle del usuario studiante
 * @param $instance_id [int] instancia del periodo
 * @return boolean
 **/

function dropStudentofMonitor($monitor, $student, $instance_id)
{
    global $DB;
    
    //monitor id
    $sql_query = "SELECT id FROM {user} WHERE username = '$monitor'";
    $idmonitor = $DB->get_record_sql($sql_query);
    
    //id is gotten from student's {talentospilos_usuario} table
    // $studentid = get_userById(array(
    //     'idtalentos'
    // ), $student);
    $studentid = get_ases_user_by_code($student);
    
    
    $semestre_act = core_periods_get_current_period($instance_id);
    
    if ($studentid) {
        //where clause
        $whereclause = "id_monitor = " . $idmonitor->id . " AND id_estudiante =" . $studentid->id . " AND id_semestre=" . $semestre_act->id;
        $asignation = $DB->get_record_sql( 
            "SELECT id FROM {talentospilos_monitor_estud} WHERE " . $whereclause
        );
        monitor_assignments_assignation_log( 
            $is_monlog = true, $asignation->id, $type = 'remove' 
        );
        return $DB->delete_records_select('talentospilos_monitor_estud', $whereclause);
    }
}

/**
 * Changes a student's monitor to a new one
 * @see changeMonitor($oldMonitor, $newMonitor)
 * @param $oldMonitor  current student's monitor 
 * @param $newMonitor  new student's monitor
 * @param int  $instance_id  instancia del período
 * @return boolean
 **/
function changeMonitor($oldMonitor, $newMonitor, $instance_id)
{
    global $DB;
    
    try {
        $lastsemester = core_periods_get_current_period($instance_id);
        
        $sql_query = "SELECT  id from {talentospilos_monitor_estud} where id_semestre=" . $lastsemester->id . " and id_monitor =" . $oldMonitor;
        $result    = $DB->get_records_sql($sql_query);
        
        foreach ($result as $row) {
            monitor_assignments_assignation_log( 
                $is_monlog = true, $row->id, $type = 'transfer' 
            );
            $newObject             = new stdClass();
            $newObject->id         = $row->id;
            $newObject->id_monitor = $newMonitor;
            if($newObject->id == 0){
                trigger_error('ASES Notificacion: actualizar monitor en la BD con id 0');
            }else{
            $DB->update_record('talentospilos_monitor_estud', $newObject);
            }
        }
        
        return 1;
        
    }
    catch (Exception $e) {
        return $e->getMessage();
    }
    
}

/**
 * Function that updates a 'practicante_ps' user's role
 * @see  actualiza_rol_practicante($id_moodle_user, $id_role, $state, $id_semester, $username_boss)
 * @param $username ---> monitor's username in moodle 
 * @param $role     --->[string] student's username in moodle
 * @return integer
 **/

function actualiza_rol_practicante($username, $role, $idinstancia, $state = 1, $semester = null, $id_boss = null)
{
    
    global $DB;

    $sql_query      = "SELECT id FROM {user} WHERE username='$username'";
    $id_user_moodle = $DB->get_record_sql($sql_query);
    
    $sql_query = "SELECT id FROM {talentospilos_rol} WHERE nombre_rol='$role';";
    $id_role   = $DB->get_record_sql($sql_query);
    
    //$sql_query   = "select max(id) as id from {talentospilos_semestre};";
    $current_semester = core_periods_get_current_period($idinstancia);

    $array = new stdClass;
    $array->id_rol       = $id_role->id;
    $array->id_usuario   = $id_user_moodle->id;
    $array->estado       = $state;
    $array->id_semestre  = $current_semester->id;
    if($id_boss =='ninguno'){
        $id_boss=null;
        $array->id_jefe = null;
    }else{
        $array->id_jefe      = (int) $id_boss;
    }
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
        if($array->id == 0){
            trigger_error('ASES Notificacion: actualizar user_rol en la BD con id 0');
            $update_record = false;
        }else{
        $update_record = $DB->update_record('talentospilos_user_rol', $array);
        }
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
 END RELATED FUNCTIONS WITH 'PSICOEDUCATIVO' ROLE
 *********************************************************************************
 */

/**
 * Updates monitor's role
 * @see  update_role_monitor_ps($username, $role, $array_students, $boss, $idinstancia, $state = 1)
 * @param $username       ---> monitor's username in moodle
 * @param $role           --->[string] student's username in moodle
 * @param $array_students ---> array of assigned students on current monitor
 * @param $boss           ---> boss user
 * @param $idinstancia    ---> current instance id
 * @param $state          ---> user's state
 * @return integer
 **/
function update_role_monitor_ps($username, $role, $array_students, $boss, $idinstancia, $state = 1)
{
    global $DB;
    
    $sql_query = "SELECT id FROM {user} WHERE username ='$username';";
    $id_moodle = $DB->get_record_sql($sql_query);

    //current semester is consulted
    $semestre  = core_periods_get_current_period($idinstancia);
    
    $sql_query     = "SELECT rol.id as id, rol.nombre_rol as nombre_rol, ur.id as id_user_rol, id_usuario FROM {talentospilos_user_rol} ur INNER JOIN {talentospilos_rol} rol ON rol.id = ur.id_rol  WHERE id_usuario = " . $id_moodle->id . " and id_semestre =" . $semestre->id . " AND ur.id_instancia=" . $idinstancia . ";";
    $id_rol_actual = $DB->get_record_sql($sql_query);
    
    
    //role's id is consulted
    $sql_query = "SELECT id FROM {talentospilos_rol} WHERE nombre_rol='monitor_ps';";
    $id_role   = $DB->get_record_sql($sql_query);
    
    $object_role               = new stdClass;
    $object_role->id_rol       = $id_role->id;
    $object_role->id_usuario   = $id_moodle->id;
    $object_role->estado       = $state;
    $object_role->id_semestre  = $semestre->id;
    if($boss =='ninguno'){
     $id_boss=null;
     $object_role->id_jefe = null;
    }else{
    $object_role->id_jefe      =$boss;}
    $object_role->id_instancia = $idinstancia;
    
    if (empty($id_rol_actual)) {
        $insert_user_rol = $DB->insert_record('talentospilos_user_rol', $object_role, true);
        
        if ($insert_user_rol) {
            //processing student's array
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
        if($object_role->id == 0){
            trigger_error('ASES Notificacion: actualizar rol en la BD con id 0');
        }else{
            $DB->update_record('talentospilos_user_rol', $object_role);
        }
        
        $check_assignment = monitor_student_assignment($username, $array_students, $idinstancia);
        
        if ($check_assignment == 1) {
            return 3;
        } else {
            return $check_assignment;
        }
    }
}

/**
 * Manage 'profesional psicoeductivo' role
 * @see  manage_role_profesional_ps($username, $role, $professional, $idinstancia, $state = 1)
 * @param $username       ---> 'profesional' username in moodle 
 * @param $role           ---> user's role
 * @param $professional   ---> user's id
 * @param $idinstancia    ---> current instance id
 * @param $state          ---> user's state
 * @return integer
 **/
function manage_role_profesional_ps($username, $role, $professional, $idinstancia, $state = 1){
    global $DB;
    
    try {
        // Select object user
        $sql_query   = "SELECT * FROM {user} WHERE username ='$username';";
        $object_user = $DB->get_record_sql($sql_query);
        
        $id_current_semester = core_periods_get_current_period($idinstancia);
        
        // Current role
        pg_query("BEGIN") or die("Could not start transaction\n");
        $sql_query = 
            "SELECT id_rol, nombre_rol 
            FROM {talentospilos_user_rol} ur 
            INNER JOIN {talentospilos_rol} r ON r.id = ur.id_rol 
            WHERE id_usuario = " . $object_user->id . 
            " AND ur.id_instancia=" . $idinstancia . 
            " AND  id_semestre = $id_current_semester->id";

        $id_current_role = $DB->get_record_sql($sql_query);
        pg_query("COMMIT") or die("Transaction commit failed\n");
        
        
        if (empty($id_current_role)) {
            
            // Start db transaction
            pg_query("BEGIN") or die("Could not start transaction\n");
            
            assign_role_user($username, $role, 1, $id_current_semester->id, $idinstancia, null);
            
            assign_professional_user($object_user->id, $professional);
            
            // End db transaction
            pg_query("COMMIT") or die("Transaction commit failed\n");
            
        } else {
            //keep in mind current semester in sql script
            $sql_query        = "SELECT * FROM {talentospilos_user_rol} userrol INNER JOIN {talentospilos_usuario_prof} userprof 
                            ON userrol.id_usuario = userprof.id_usuario INNER JOIN {talentospilos_rol} rol ON rol.id = userrol.id_rol  WHERE userprof.id_usuario = " . $object_user->id . " AND userrol.id_semestre=" . $id_current_semester->id . " AND userrol.id_instancia = " . $idinstancia . ";";
            $object_user_role = $DB->get_record_sql($sql_query);
            
            if ($object_user_role) {
                // include state
                
                $sql_query                = "SELECT id FROM {talentospilos_profesional} WHERE nombre_profesional = '$professional'";
                $new_id_professional_type = $DB->get_records_sql($sql_query);
                
                foreach ($new_id_professional_type as $n) {
                    if ($object_user_role->id_profesional != $n->id) {
                        update_professional_user($object_user->id, $professional);
                    }
                }
                if ($state == 0) {
                    //updates state if it was disabled before
                    update_role_user($username, $role, $idinstancia, $state);
                    $whereclause = "id_usuario = " . $object_user->id;
                    $DB->delete_records_select('talentospilos_usuario_prof', $whereclause);
                }
            } else {
                //case : monitor
                pg_query("BEGIN") or die("Could not start transaction\n");
                if ($id_current_role->nombre_rol == 'monitor_ps') {
                    
                    $lastsemester = core_periods_get_last_period((int)$idinstancia);
                    $whereclause  = "id_semestre =" . $lastsemester->id . " and  id_monitor = " . $object_user->id;
                    $DB->delete_records_select('talentospilos_monitor_estud', $whereclause);
                }
                update_role_user($username, $role, $idinstancia, $state, $id_current_semester->id, null);
                assign_professional_user($object_user->id, $professional);
                pg_query("COMMIT") or die("Transaction commit failed\n");
            }
        }
        return 1;
        
    }catch (Exception $e) {
        return "Error al gestionar los permisos profesional " . $e->getMessage();
    }
    
}

/**
 * update_program_director
 * @see  update_program_director($username, $role, $id_instance, $status = 1, $id_academic_program)
 * @param $username             ---> 'profesional' username in moodle 
 * @param $role                 ---> user's role
 * @param $id_instance          ---> current instance id
 * @param $status               ---> user status
 * @param $id_academic_program  ---> user's state
 * @return integer
 **/

function update_program_director($username, $role, $id_instance, $status = 1, $id_academic_program){

    global $DB;
    
    try{

        // Select object user
        $sql_query   = "SELECT * FROM {user} WHERE username ='$username';";
        $object_user = $DB->get_record_sql($sql_query);

        $current_semester = core_periods_get_current_period($id_instance);

        $sql_query = "SELECT id_rol, nombre_rol 
                      FROM {talentospilos_user_rol} AS user_role 
                      INNER JOIN {talentospilos_rol} AS t_role ON t_role.id = user_role.id_rol 
                      WHERE id_usuario = $object_user->id AND user_role.id_instancia= $id_instance 
                                                          AND id_semestre = $current_semester->id";

        $current_role = $DB->get_record_sql($sql_query);



        if (empty($current_role)) {
            
            // Start db transaction
            pg_query("BEGIN") or die("Could not start transaction\n");
            
            $result = assign_role_user($username, $role, 1, $current_semester->id, $id_instance, null, $id_academic_program);

            // End db transaction
            pg_query("COMMIT") or die("Transaction commit failed\n");
        }else{

            // Start db transaction
            pg_query("BEGIN") or die("Could not start transaction\n");

            $result = update_role_user($username, $role, $id_instance, 1, $current_semester, null, $id_academic_program);
            
            // End db transaction
            pg_query("COMMIT") or die("Transaction commit failed\n");
        }

        return $result;       

    }catch (Exception $e) {
        return "Error al gestionar el rol del usuario director de programa " . $e->getMessage();
    }
}

function update_fac_director($username, $role, $id_instance, $status = 1, $id_fac_program){

    global $DB;
    
    try{

        // Select object user
        $sql_query   = "SELECT * FROM {user} WHERE username ='$username';";
        $object_user = $DB->get_record_sql($sql_query);

        $current_semester = core_periods_get_current_period($id_instance);

        $sql_query = "SELECT id_rol, nombre_rol 
                      FROM {talentospilos_user_rol} AS user_role 
                      INNER JOIN {talentospilos_rol} AS t_role ON t_role.id = user_role.id_rol 
                      WHERE id_usuario = $object_user->id AND user_role.id_instancia= $id_instance 
                                                          AND id_semestre = $current_semester->id";

        $current_role = $DB->get_record_sql($sql_query);



        if (empty($current_role)) {
            
            // Start db transaction
            pg_query("BEGIN") or die("Could not start transaction\n");
            
            $result = assign_role_user($username, $role, 1, $current_semester->id, $id_instance, null, $id_fac_program);

            // End db transaction
            pg_query("COMMIT") or die("Transaction commit failed\n");
        }else{

            // Start db transaction
            pg_query("BEGIN") or die("Could not start transaction\n");

            $result = update_role_user($username, $role, $id_instance, 1, $current_semester, null, $id_fac_program);
            
            // End db transaction
            pg_query("COMMIT") or die("Transaction commit failed\n");
        }

        return $result;       

    }catch (Exception $e) {
        return "Error al gestionar el rol del usuario director de programa " . $e->getMessage();
    }
}

/**
 * 
 * Gets an user given his id
 * @see  get_userById($column, $id)
 * @param $column --> column that contains user's information
 * @param $id --> user's id
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
    //code is formatted to delete program information
    if ($result_query) {
        if (property_exists($result_query, 'username'))
            $result_query->username = substr($result_query->username, 0, -5);
    }
    return $result_query;
}

/**
 * Function used to recover associatd users to ASES course given the course name
 * @see  get_course_user($namecourse)
 * @param $namecourse --> course name        
 * @return array with associated users to the course
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
 * Function used to recover associatd users to ASES course given the course id
 * @see  get_course_usersby_id($id)
 * @param $id        
 * @return array with associated users to the course
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
 * Function used to recover fields from user table
 * @see get_moodle_user($id)
 * @param $id --> user table id
 * @return array filled with fields recoverd from {user}
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
 * Checks if an user has a role assigned
 * @see checking_role($username, $idinstancia)
 * @param $username    ---> username in moodle (user)
 * @param $idinstancia ---> current instance id
 * @return array (id_rol, nombre_rol, id, estado, id_usuario)
 */

function checking_role($username, $idinstancia)
{
    
    global $DB;
    
    $sql_query      = "SELECT id FROM {user} WHERE username = '$username'";
    $id_moodle_user = $DB->get_record_sql($sql_query);
    
    $semestre = core_periods_get_current_period($idinstancia);
    
    $sql_query  = "SELECT ur.id_rol as id_rol , r.nombre_rol as nombre_rol, ur.id as id, ur.id_usuario, ur.estado FROM {talentospilos_user_rol} ur INNER JOIN {talentospilos_rol} r ON r.id = ur.id_rol WHERE ur.id_usuario = " . $id_moodle_user->id . " and ur.id_semestre = " . $semestre->id . " and ur.id_instancia=" . $idinstancia . ";";
    $role_check = $DB->get_record_sql($sql_query);
    
    return $role_check;
}

/**
 * Updates the 'profesional' kind to an user with 'profesional psicoeducativo' rol
 * @see update_professional_user($id_user, $professional)
 * @param $id_user    ---> user's id
 * @param $professional --> 'profesional's name
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
        if($record_professional_type->id == 0){
            trigger_error('ASES Notificacion: actualizar profesional en la BD con id 0');
            $update_record = false;
        }else{
        $update_record = $DB->update_record('talentospilos_usuario_prof', $record_professional_type);
        }
        
        return $update_record;
    } else {
        return false;
    }
    
}

/**
 * Function that assigns a type of 'profesional' to an user with a 'profesional psicoeducativo' rol
 * @see assign_professional_user($id_user, $professional)
 * @param $id_user    ---> user's id
 * @param $professional --> 'profesional's name
 * @return integer
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

/**
 * Función que retorna los estudiantes relacionados a un programa académico
 * @see get_students_by_program($id_academic_program)
 * @param $id_academic_program    ---> Identificador del programa académico
 * @return stdClass
 */

function get_students_by_program($id_academic_program){

    global $DB;

    $sql_query = 'SELECT * FROM {talentospilos_user_extended} AS extended_user
                            INNER JOIN {talentospilos_usuario} AS ases_user ON ases_user.id = extended_user.id_ases_user
                  WHERE extended_user.id_academic_program = $id_academic_program';

    $result_query = $DB->get_records_sql($sql_query);

    return $result_query;

}

/**
 * Función que retorna los estudiantes relacionados a un programa académico
 * @see get_students_by_facul($id_fac_program)
 * @param $id_fac_program   ---> Identificador del programa académico
 * @return stdClass
 */

 function get_students_by_facul($id_fac_program){

    global $DB;

    $sql_query = 'SELECT * FROM {talentospilos_user_extended} AS extended_user
                    INNER JOIN {talentospilos_usuario} AS ases_user ON ases_user.id= extended_user.id_ases_user 
                      INNER JOIN {talentospilos_programa} AS prog_acad on prog_acad.id = extended_user.id_academic_program
                        INNER JOIN {talentospilos_facultad} AS fac_acad on fac_acad.id  =  prog_acad.id_facultad
                          WHERE prog_acad.id_facultad = $id_fac_program';

    $result_query = $DB->get_records_sql($sql_query);

    return $result_query;

}

/**
 * Función que retorna los programas académicos 
 * @see get_academic_programs()
 * @return array of stdclass
 **/

function get_academic_programs(){
    
    global $DB;

    $sql_query = "SELECT academic_program.id, academic_program.nombre AS academic_program_name, academic_program.cod_univalle, location_university.nombre AS location_name,
                         academic_program.jornada 
                  FROM {talentospilos_programa} AS academic_program
                           INNER JOIN {talentospilos_sede} AS location_university ON location_university.id = academic_program.id_sede";
    $result_query = $DB->get_records_sql($sql_query);

    return $result_query;
}

/**
 * Función que retorna las facultades
 * @see get_facultad()
 * @return array of stdclass
 **/


function get_facultad(){
    
    global $DB;

    $sql_query = "SELECT fac_acad.id, fac_acad.nombre AS facultad_nombre,  fac_acad.cod_univalle, location_university.nombre AS location_name, academic_program.jornada
       FROM {talentospilos_facultad} AS fac_acad
        INNER JOIN {talentospilos_programa} AS academic_program on    academic_program.id_facultad  = fac_acad.id
         INNER JOIN {talentospilos_sede} AS location_university ON location_university.id = academic_program.id_sede";
    $result_query = $DB->get_records_sql($sql_query);

    return $result_query;
}

