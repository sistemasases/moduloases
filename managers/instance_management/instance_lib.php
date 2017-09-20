<?php

require_once(dirname(__FILE__). '/../../../../config.php');

// require_once("../user_management/user_lib.php");
// require_once("../periods_management/periods_lib.php");

function consult_instance($instanceid){
    global $DB;
    $sql_query = "SELECT instancia.id as id_talentosinstancia ,id_instancia id_director, id_programa, prog.nombre, prog.cod_univalle FROM {talentospilos_instancia} instancia INNER JOIN  {talentospilos_programa} prog ON prog.id = instancia.id_programa   WHERE id_instancia = ".$instanceid.";";
    $consult = $DB->get_record_sql($sql_query);
    // print_r($consult);
    return $consult;
}

function consult_program($codPrograma){
    global $DB;
    $sql_query = "SELECT instancia.id as id_talentosinstancia , id_director, id_programa, prog.cod_univalle, prog.nombre FROM {talentospilos_instancia} instancia INNER JOIN {user} usr ON usr.id = instancia.id_director INNER JOIN  {talentospilos_programa} prog ON prog.id = instancia.id_programa  WHERE prog.cod_univalle = ".$codPrograma.";";
    return $consultPrograma = $DB->get_record_sql($sql_query);
}

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

function loadProgramsForSystemsAdmins(){
    global $DB;
    $sql_query = "SELECT cod_univalle, nombre FROM {talentospilos_programa} WHERE id NOT IN (SELECT id_programa from {talentospilos_instancia});";
    return $DB->get_records_sql($sql_query);
}

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
                if(!$programa) throw new Exception("NO se encontró el programa");
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
            
        }else{//insert
            // se opbtiene el id del programa
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

function deleteSystemAdministrator($username){
    global $DB;
    $directorinfo = getInfoSystemDirector($username);
    //print_r($directorinfo);
    update_role_user($directorinfo->username, "sistemas",$directorinfo->id_instancia,0);
    $DB->delete_records_select('talentospilos_instancia', 'id= '.$directorinfo->id_talentosinstancia);
    
    return true;
}
