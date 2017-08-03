<?php
/*
 * Consultas modulo seguimiento_pilos.
 */



/*
 * Función que retorna el rol de un usuario con el fin de mostrar al correspondiente interfaz en seguimiento_pilos
 *
 * @param $userid
 * @param $instanceid
 * @return Array 
 */


function get_name_rol($userid,$instanceid)
{
    global $DB;
    echo "esto es  : ".$instanceid;
    $sql_query = "SELECT id_rol FROM {talentospilos_user_rol} WHERE id_usuario='$userid' AND id_instancia='$instanceid'";
    $consulta=$DB->get_records_sql($sql_query);

    foreach($consulta as $tomarId)
    {
        $idretornar=$tomarId->id_rol;
    }
     print_r($idretornar);
    return $idretornar;
}

/*
 * Función que trae la información necesaria para los seguimientos considerando el monitor actual, la instancia actual asi como
 * que el monitor este asignado como tal a esta instancia
 *
 * @param $id_monitor
 * @param $id_instance 
 * @return Array 
 */

function get_seguimientos_monitor($id_monitor,$id_instance){
    global $DB;
    
    $sql_query = "SELECT ROW_NUMBER() OVER(ORDER BY seguimiento.id ASC) AS number_unique,seguimiento.id AS id_seguimiento,
                  seguimiento.tipo,usuario_monitor.id AS id_monitor_creo,usuario_monitor.firstname AS nombre_monitor_creo,nombre_usuario_estudiante.firstname 
                  AS nombre_estudiante,nombre_usuario_estudiante.lastname AS apellido_estudiante,seguimiento.created,seguimiento.fecha,seguimiento.hora_ini,
                  seguimiento.hora_fin,seguimiento.lugar,seguimiento.tema,seguimiento.objetivos,seguimiento.actividades,seguimiento.individual,
                  seguimiento.individual_riesgo,seguimiento.familiar_desc,seguimiento.familiar_riesgo,seguimiento.academico,
                  seguimiento.academico_riesgo,seguimiento.economico,seguimiento.economico_riesgo, seguimiento.vida_uni,seguimiento.vida_uni_riesgo,
                  seguimiento.observaciones AS observaciones,seguimiento.id AS status,seguimiento.id AS sede, usuario_estudiante.id_tal AS id_estudiante,monitor_actual.id_monitor,
                  usuario_mon_actual.firstname AS nombre_monitor_actual,usuario_mon_actual.lastname AS apellido_monitor_actual, usuario_monitor.lastname AS apellido_monitor_creo
                  FROM {talentospilos_seg_estudiante} AS s_estudiante INNER JOIN {talentospilos_seguimiento} AS seguimiento ON 
                  (s_estudiante.id_seguimiento=seguimiento.id) INNER JOIN {user} AS usuario_monitor ON (seguimiento.id_monitor = usuario_monitor.id) 
                  INNER JOIN (SELECT DISTINCT data.userid AS userid, data.data as id_tal FROM {talentospilos_usuario} AS usuarios_tal INNER JOIN {user_info_data} AS data 
                  ON (CAST(usuarios_tal.id AS varchar) = data.data) WHERE data.fieldid = 1) AS usuario_estudiante ON 
                  (usuario_estudiante.id_tal=CAST(s_estudiante.id_estudiante AS varchar)) INNER JOIN {user} as nombre_usuario_estudiante ON 
                  (nombre_usuario_estudiante.id=usuario_estudiante.userid) INNER JOIN {talentospilos_monitor_estud} as monitor_actual 
                  ON (CAST(monitor_actual.id_estudiante AS text)=CAST(s_estudiante.id_estudiante AS text)) INNER JOIN {user} AS usuario_mon_actual ON (monitor_actual.id_monitor=usuario_mon_actual.id)
                  WHERE monitor_actual.id_monitor='$id_monitor' AND seguimiento.id_instancia='$id_instance' AND monitor_actual.id_instancia='$id_instance' ORDER BY usuario_monitor.firstname";
    
    $consulta=$DB->get_records_sql($sql_query);
    
    // print_r($consulta);
    return $consulta;
}


/*
 * Función que trae la información de cantidad de seguimientos que posee un monitor
 *
 * @param $id_monitor
 * @param $id_instance 
 * @return Array 
 */

function get_cantidad_seguimientos_monitor($id_monitor,$id_instance){
    global $DB;
    
    $sql_query = "SELECT count(*) as cantidad
                  FROM {talentospilos_seg_estudiante} AS s_estudiante INNER JOIN {talentospilos_seguimiento} AS seguimiento ON 
                  (s_estudiante.id_seguimiento=seguimiento.id) INNER JOIN {user} AS usuario_monitor ON (seguimiento.id_monitor = usuario_monitor.id) 
                  INNER JOIN (SELECT DISTINCT data.userid AS userid, data.data as id_tal FROM {talentospilos_usuario} AS usuarios_tal INNER JOIN {user_info_data} AS data 
                  ON (CAST(usuarios_tal.id AS varchar) = data.data) WHERE data.fieldid = 1 ) AS usuario_estudiante ON 
                  (usuario_estudiante.id_tal=CAST(s_estudiante.id_estudiante AS varchar)) INNER JOIN {user} as nombre_usuario_estudiante ON 
                  (nombre_usuario_estudiante.id=usuario_estudiante.userid) INNER JOIN {talentospilos_monitor_estud} as monitor_actual 
                  ON (CAST(monitor_actual.id_estudiante AS text)=CAST(s_estudiante.id_estudiante AS text)) INNER JOIN {user} AS usuario_mon_actual ON (monitor_actual.id_monitor=usuario_mon_actual.id)
                  WHERE monitor_actual.id_monitor='$id_monitor' AND seguimiento.id_instancia='$id_instance' AND monitor_actual.id_instancia='$id_instance'";
    
    $consulta=$DB->get_records_sql($sql_query);
    foreach($consulta as $tomarId)
    {
        $valorRetorno=$tomarId->cantidad;    
    }
    // print_r($consulta);
    // return $consulta;
    return $valorRetorno;
}

/*
 * Función que consulta información de los monitores asignados a un practicante
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
    
    //por cada registro retornado se toma la información necesaria, se añade a un arreglo auxiliar y este se agrega 
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
    
//  print_r($arreglo_retornar);
    return $arreglo_retornar;
}

/*
 * Función que consulta información de los practicantes asignados a un profesional
 * 
 * @param $id_profesional
 * @return Array 
 */
function get_practicantes_profesional($id_profesional)
{
    global $DB;
    
    $sql_query = "SELECT DISTINCT usuario_rol.id_usuario,usuario.firstname AS nombre,usuario.lastname AS apellido 
                  FROM {talentospilos_user_rol} as usuario_rol INNER JOIN {user} AS usuario ON 
                  (usuario.id=usuario_rol.id_usuario) WHERE id_jefe='$id_profesional' and id_rol<>4";

    $consulta=$DB->get_records_sql($sql_query);
    
    $arreglo_retornar= array();
    //por cada registro retornado se toma la informacion necesaria, se añade a un arreglo auxiliar y este se agrega 
    //al areglo que sera retornado
    foreach($consulta as $practicantes)
    {
        $array_auxiliar=array();
        //posicion 0
        array_push($array_auxiliar,$practicantes->id_usuario);
        $nombre = $practicantes->nombre ;
        $apellido = $practicantes->apellido; 
        $unir = $nombre." ".$apellido;
        //posicion 1
        array_push($array_auxiliar,$unir);
        
        array_push($arreglo_retornar,$array_auxiliar);
    }

    
    // print_r($arreglo_retornar);       
    return $arreglo_retornar;
    
}

/*
 * Función que consulta información de los jefes
 * 
 * @param $id
 * @param $instanceid
 * @return Array 
 */
 
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



/*
 * Función para enviar mensaje al monitor que desea hacer la observación
 * 
 * @param $tipoSeg
 * @param $codigoEnviarN1
 * @param $codigoEnviarN2
 * @param $fecha
 * @param $nombre
 * @param $messageText
 * @return Array 
 */
function send_email_to_user($tipoSeg,$codigoEnviarN1,$codigoEnviarN2,$fecha,$nombre,$messageText){

    global $USER;
    $emailToUser = new stdClass;
    $emailFromUser = new stdClass;

    $sending_user = get_full_user($USER->id);
    $receiving_user = get_full_user($codigoEnviarN1);
    
    $monitor = get_full_user($codigoEnviarN1);
    $name_monitor=$monitor->firstname;
    $name_monitor.=" ";
    $name_monitor.=$monitor->lastname;
    $name_prof = $sending_user->firstname." ".$sending_user->lastname;
    
    $emailToUser->email = $receiving_user->email;
    $emailToUser->firstname = $receiving_user->firstname;
    $emailToUser->lastname = $receiving_user->lastname;
    $emailToUser->maildisplay = true;
    $emailToUser->mailformat = 1;
    $emailToUser->id = $receiving_user->id; 
    $emailToUser->alternatename = '';
    $emailToUser->middlename = '';
    $emailToUser->firstnamephonetic = '';
    $emailToUser->lastnamephonetic = '';

    $emailFromUser->email = $sending_user->email;
    $emailFromUser->firstname = $sending_user->firstname;
    $emailFromUser->lastname = $sending_user->lastname;
    $emailFromUser->maildisplay = false;
    $emailFromUser->mailformat = 1;
    $emailFromUser->id = $sending_user->id; 
    $emailFromUser->alternatename = '';
    $emailFromUser->middlename = '';
    $emailFromUser->firstnamephonetic = '';
    $emailFromUser->lastnamephonetic = '';
    
    if($tipoSeg=="individual")
    {
      $subject = "Observaciones seguimiento del dia $fecha del estudiante $nombre"; 
    }else
    {
      $subject = "Observaciones seguimiento del dia $fecha de los estudiantes $nombre";
    }
    
    
    $messageHtml.="<b>OBSERVACION:<b><br><br>";
    $messageHtml.="Estimado monitor $name_monitor<br><br>";
    
    if($tipoSeg=="individual")
    {
      $messageHtml.="Revisando el seguimiento realizado al estudiante $nombre  el dia $fecha, mis comentarios son los siguientes:<br><br>";
    }else
    {
      $messageHtml.="Revisando el seguimiento realizado a los estudiantes $nombre  el dia $fecha, mis comentarios son los siguientes:<br><br>";
    }
    
    $messageHtml.=$messageText."<br><br>";
    $messageHtml.="Cordialmente<br>";
    $messageHtml.="$name_prof";
    
    $email_result = email_to_user($emailToUser, $emailFromUser, $subject, $messageText, $messageHtml, ", ", true);
    if($email_result!=1)
    {
     return $email_result;
    }else{
     
      $email_result=0;
      //************************************************************************************************************
      //************************************************************************************************************
      //AHORA SE REENVIA EL CORREO AL PROFESIONAL
      //************************************************************************************************************
      //************************************************************************************************************
    
      $receiving_user = get_full_user($USER->id);
      $emailToUser->email = $receiving_user->email;
      $emailToUser->firstname = $receiving_user->firstname;
      $emailToUser->lastname = $receiving_user->lastname;
      $emailToUser->maildisplay = true;
      $emailToUser->mailformat = 1;
      $emailToUser->id = $receiving_user->id; 
      $emailToUser->alternatename = '';
      $emailToUser->middlename = '';
      $emailToUser->firstnamephonetic = '';
      $emailToUser->lastnamephonetic = '';
      
      $email_result = email_to_user($emailToUser, $emailFromUser, $subject, $messageText, $messageHtml, ", ", true);
      if($email_result!=1)
      {
      return $email_result;
      }else{
       
       $email_result=0;
      //************************************************************************************************************
      //************************************************************************************************************
      //SE ENVIA EL CORREO AL SEGUNDO USUARIO CORRESPONDIENTE
      //************************************************************************************************************
      //************************************************************************************************************
    
      $receiving_user = get_full_user($codigoEnviarN2);
    
      $emailToUser->email = $receiving_user->email;
      $emailToUser->firstname = $receiving_user->firstname;
      $emailToUser->lastname = $receiving_user->lastname;
      $emailToUser->maildisplay = true;
      $emailToUser->mailformat = 1;
      $emailToUser->id = $receiving_user->id; 
      $emailToUser->alternatename = '';
      $emailToUser->middlename = '';
      $emailToUser->firstnamephonetic = '';
      $emailToUser->lastnamephonetic = '';
      
      $email_result = email_to_user($emailToUser, $emailFromUser, $subject, $messageText, $messageHtml, ", ", true);
      return $email_result;
      }
    }
}

?>