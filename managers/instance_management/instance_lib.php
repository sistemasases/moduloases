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
 * Talentos Pilos
 *
 * @author     John Lourido 
 * @package    block_ases
 * @copyright  2017 JOhn Lourido <jhonkrave@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__). '/../../../../config.php');

// require_once("../user_management/user_lib.php");
// require_once("../periods_management/periods_lib.php");

/**
 * Returns an instance given its id
 * 
 * @see consult_instance($id_instance)
 * @param $id_instance --> instance id
 * @return object representing the instance from {talentospilos_instancia} and {talentospilos_programa} tables (JOIN)
 */
function consult_instance($id_instance){
    global $DB;
    $sql_query = "SELECT *
                  FROM {talentospilos_instancia} 
                  WHERE id_instancia = ".$id_instance.";";
    $result_query = $DB->get_record_sql($sql_query);
    return $result_query;
}

/**
 * Returns a instance of a program given the program id
 * 
 * @see consult_program($codPrograma)
 * @param $codPrograma --> program id
 * @return object representing the instance
 */

function consult_program($codPrograma){
    global $DB;
    $sql_query = "SELECT instancia.id as id_talentosinstancia , id_director, id_programa, prog.cod_univalle, prog.nombre FROM {talentospilos_instancia} instancia INNER JOIN {user} usr ON usr.id = instancia.id_director INNER JOIN  {talentospilos_programa} prog ON prog.id = instancia.id_programa  WHERE prog.cod_univalle = ".$codPrograma.";";
    return $consultPrograma = $DB->get_record_sql($sql_query);
}

/**
 * Obtains user information given his username on {user} table
 * 
 * @see getInfoSystemDirector($username)
 * @param $username 
 * @return object representing an user
 */

function getInfoSystemDirector($username){
     global $DB;

        $sql_query = "SELECT id, firstname, lastname,username FROM {user} WHERE username = '".$username."';";
        $info_user = $DB->get_record_sql($sql_query);
    
        if($info_user){
            $sql_query = "SELECT instancia.id as id_talentosinstancia, id_director, id_programa, id_instancia, prog.cod_univalle, prog.nombre, seg_academico, seg_asistencias, seg_socioeducativo FROM {talentospilos_instancia} instancia INNER JOIN {user} usr ON usr.id = instancia.id_director INNER JOIN  {talentospilos_programa} prog ON prog.id = instancia.id_programa  WHERE usr.id = ".$info_user->id.";";
            $rol_user = $DB->get_record_sql($sql_query);
            
            if(!$rol_user)
            {
                $info_user->cod_programa = 0;
                $info_user->nombre_programa = "Ninguno";
            }
            else {
                $info_user->cod_programa = $rol_user->cod_univalle;
                $info_user->nombre_programa = $rol_user->nombre;
                $info_user->id_talentosinstancia = $rol_user->id_talentosinstancia;
                $info_user->id_instancia = $rol_user->id_instancia;
            }
            return $info_user;
        }else{
            $object =  new stdClass();
            $object->error = "Error al consultar la base de datos. El usuario con codigo ".$username." no se encuentra en la base de datos.";
            return $object;
        }
}

/**
 * Obtains user info registered in {talentospilos_programa} table but not in {talentospilos_instancia} table
 * 
 * @see loadProgramsForSystemsAdmins()
 * @return object representing the user info
 */

function loadProgramsForSystemsAdmins(){
    global $DB;
    $sql_query = "SELECT cod_univalle, nombre FROM {talentospilos_programa} WHERE id NOT IN (SELECT id_programa from {talentospilos_instancia});";
    return $DB->get_records_sql($sql_query);
}

/**
 * Deletes an instance in case it already has one and update it or return true in case the instance has been deleted
 * 
 * @see updateSystemDirector($username, $codPrograma, $idinstancia, $segAca, $segAsis, $segSoc)
 * @param $username --> username
 * @param $codPrograma --> program code
 * @param $idinstancia --> instance id
 * @param $segAca --> Academic tracking (Seguimiento académico)
 * @param $segAsis --> Asistance tracking (Seguimiento asistencia)
 * @param $segSoc --> socio-educational tracking (Seguimiento socioeducativo)
 * @return boolean in case it's succesfull or sql exception otherwise
 */

function updateSystemDirector($username, $codPrograma, $idinstancia, $segAca, $segAsis, $segSoc){
    global $DB;
    try{
        
        $directorinfo = getInfoSystemDirector($username);
        
        $consultPrograma = consult_program($codPrograma);
        $consultIntancia = consult_instance($idinstancia);
        
        if($directorinfo->cod_programa != 0){ //se elima la instanciade en caso de que ya tenga una
            $DB->delete_records_select('talentospilos_instancia', 'id= '.$directorinfo->id_talentosinstancia);
            update_role_user($directorinfo->username, "sistemas",$idinstancia,0);
        }
        
        if($codPrograma == 0) return true; //0->ningunprograma - previamente se ha borrado una instancia en caso de que tenga una
        
        if($consultPrograma || $consultIntancia){//update 1126259 - 1144066653
            $updateObject= new stdClass();
            
            if($consultPrograma){  // se consulta si ya existe una einstancia en el tabla instancias
                $updateObject->id = $consultPrograma->id_talentosinstancia; //
            }else {
                $updateObject->id = $consultIntancia->id_talentosinstancia;
                $sql_query = "SELECT id FROM {talentospilos_programa} WHERE cod_univalle=".$codPrograma.";";
                $programa = $DB->get_record_sql($sql_query);
                if(!$programa) throw new Exception("No se encontró el programa");
                $updateObject->id_programa = $programa->id;
            }
            
            
            $updateObject->id_instancia = $idinstancia;
            $updateObject->id_director = $directorinfo->id;
            $updateObject->estado = 1;
            $updateObject->seg_academico = $segAca;
            $updateObject->seg_asistencias = $segAsis;
            $updateObject->seg_socioeducativo = $segSoc;
            $DB->update_record('talentospilos_instancia', $updateObject);
            update_role_user($directorinfo->username, "sistemas", $idinstancia); // se actualiza al rol sistemas
            
        }else{//Gets program id
            $sql_query = "SELECT id FROM {talentospilos_programa} WHERE cod_univalle=".$codPrograma.";";
            $programa = $DB->get_record_sql($sql_query);
            if(!$programa) throw new Exception("NO se encontró el programa");
            
            $record = new stdClass; 
            $record->id_instancia = $idinstancia;
            $record->id_director = $directorinfo->id; 
            $record->id_programa = $programa->id;
            $record->seg_academico = $segAca;
            $record->seg_asistencias = $segAsis;
            $record->seg_socioeducativo = $segSoc;
            $record->estado = 1;
            $DB->insert_record('talentospilos_instancia', $record, false);
            update_role_user($directorinfo->username, "sistemas", $idinstancia); // se actualiza al rol sistemas
        }
        return true;
    
    }catch(Exception $e){
        $errorSqlServer = pg_last_error();
        $result = $e->getMessage()." <br>".$errorSqlServer;
        
        return $result;
    }
}

/**
 * Returns information of an instance from {talentospilos_instancia}, {user} and {talentospilos_programa} tables (JOIN)
 * 
 * @see getSystemAdministrators()
 * @return array with instance info
 */

function getSystemAdministrators(){
    global $DB;
    $sql_query ="SELECT instancia.id , username, firstname, lastname, prog.nombre, prog.cod_univalle, instancia.id_instancia FROM {talentospilos_instancia} instancia INNER JOIN {user} usr ON usr.id = instancia.id_director INNER JOIN  {talentospilos_programa} prog ON prog.id = instancia.id_programa  ;";
    $result = $DB->get_records_sql($sql_query);
    
    $array = array();
    
    foreach ($result as $r){
        $r->programa = $r->cod_univalle." - ".$r->nombre;
        $r->button = "<a id = \"delete_user\"  ><span  id=\"".$r->id."\" class=\"red glyphicon glyphicon-remove\"></span></a>";
        array_push($array,$r );
    }
    return $array;
}

/**
 * Deletes an user from {talentospilos_instancia} given his username
 * 
 * @see deleteSystemAdministrator($username)
 * @param $username 
 * @return true
 */

function deleteSystemAdministrator($username){
    global $DB;
    $directorinfo = getInfoSystemDirector($username);
    update_role_user($directorinfo->username, "sistemas",$directorinfo->id_instancia,0);
    $DB->delete_records_select('talentospilos_instancia', 'id= '.$directorinfo->id_talentosinstancia);
    
    return true;
}

/**
 * Función que
 * 
 * @see get_cohorts_without_assignment()
 * @return 
 */

function get_cohorts_without_assignment(){
    global $DB;
    $sql_query = "SELECT id, idnumber, name 
                  FROM {cohort} 
                  WHERE id NOT IN (SELECT id_cohorte FROM {talentospilos_inst_cohorte})";
    $result = $DB->get_records_sql($sql_query);
    return $result;
}

