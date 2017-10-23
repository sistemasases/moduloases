<?php
require_once(dirname(__FILE__). '/../../../../config.php');
require_once(dirname(__FILE__).'/../periods_management/periods_lib.php');
require_once(dirname(__FILE__).'/../lib/student_lib.php');

/*
 * Función que obtiene los semestres almacenados
 *
 * @return array
 */
function get_semesters(){
  
  global $DB;

    $sql_query = "select * from {talentospilos_semestre} order by fecha_fin desc";
    $semesters = $DB->get_records_sql($sql_query);
    return $semesters;
}

/*
 * Función que obtiene el rol dado id_usuario, instancia y id_semestre 
 *
 * @return object
 */


function get_users_rols($user,$semester,$id_instancia){
  global $DB;

    $sql_query = "select * from {talentospilos_user_rol} where id_usuario='$user' and id_semestre='$semester' and id_instancia='$id_instancia'";
    $rol = $DB->get_record_sql($sql_query);
    return $rol;
}
/*
 * Función que obtiene los roles almacenados que contengan el substring _ps
 *
 * @return array
 */
function get_rol_ps(){
  
  global $DB;

    $sql_query = "select * from {talentospilos_rol}";
    $rols = $DB->get_records_sql($sql_query);
    $roles_ps=[];

    foreach ($rols as $rol) {
      $esta = strpos($rol->nombre_rol, "_ps");
      if($esta!==false){
        array_push($roles_ps,$rol);
      }
    }
    return $roles_ps;
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



/*
 * Función que obtiene las personas dado un semestre y el rol a que tienen cargo.
 *
 * @return array
 */
function get_people_onsemester($period,$rols,$id_instancia){
  
  global $DB;


    $sql_query = "SELECT usuario.id AS id_usuario ,id_rol,username,firstname,lastname FROM 
    {user} AS usuario INNER JOIN {talentospilos_user_rol} AS usuario_rol ON usuario.id = usuario_rol.id_usuario where id_semestre='$period' and id_instancia='$id_instancia'";
    
    
    $people_last_period = $DB->get_records_sql($sql_query);
    $people_ps=[];

    foreach ($people_last_period as $person_last_period) {
       foreach($rols as $rol){
        if($person_last_period->id_rol == $rol->id){
          array_push($people_ps,$person_last_period);
        }
      }
    }
    return $people_ps;
}

/**
 * Función para insertar un seguimiento.
 *
 * @see get_record($object, $id_est)
 * @param $object  ---> objeto seguimiento
 * @param $id_est  ---> id del estudiante
 * @return boolean
 */
function insert_record($object, $id_est){
    global $DB;
    $id_seg = $DB->insert_record('talentospilos_seguimiento', $object,true);
    
    //se relaciona el seguimiento con el estudiante
    insert_record_student($id_seg, $id_est);
    
    //se actualiza el riesgo
    if($object->tipo == 'PARES'){
        foreach ($id_est as $idStudent) {
            updateRisks($object, $idStudent);
        }
    }
    
    return true;
}





/**
 * Función para insertar un registro de seguimiento en {talentospilos_seg_estudiante}
 * dado el id del seguimiento insertado y el estudiante.
 *
 * @see insert_record_student($id_seg, $id_est)
 * @param $id_seg ---> id del seguimiento
 * @param $id_est  ---> id del estudiante
 * @return boolean
 */
function insert_record_student($id_seg, $id_est){
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
 * Función para obtener el seguimiento dado un monitor especifico.
 *
 * @see get_record_by_monitor($id_monitor, $id_seg= null, $tipo, $idinstancia)
 * @param $id_monitor  ---> id del monitor
 * @param $id_seg      ---> id del seguimiento
 * @param $tipo        ---> tipo del seguimiento
 * @param $idinstancia ---> id de instancia actual 
 * @return Array ---> obtiene array con los seguimientos del monitor
 */

function get_record_by_monitor($id_monitor, $id_seg= null, $tipo, $idinstancia){
    global $DB;
    $sql_query= "";
    $sql_query="SELECT seg.id as id_seg, to_timestamp(fecha) as fecha_formato,*  from {talentospilos_seguimiento} seg  where seg.id_monitor = ".$id_monitor." AND seg.tipo = '".$tipo."' AND seg.id_instancia=".$idinstancia." ORDER BY fecha_formato DESC;";

    if($id_seg != null){
      $sql_query = trim($sql_query,";");
      $sql_query.= " AND seg.id =".$id_seg.";";
    }
   return $DB->get_records_sql($sql_query);
}

/*
 * Función que actualiza los seguimientos pares.
 *
 * @param $object
 * @return 0 or 1 
 */

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

/*
 * Función que da una calificaciones a los riesgos dados.
 *
 * @param $array_student_risk
 * @param $name_risk
 * @param $calificacion
 * @param $idstudent
 * @return agrega datos a array 
 */
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


/*
 * Función que crea arreglo con la información a actualizar de los riesgos de un seguimiento
 * @param $segObject
 * @param $idStudent
 * @return true 
 */
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

/*
 * Función que retorna el rol de un usuario con el fin de mostrar al correspondiente interfaz en seguimiento_pilos
 *
 * @param $userid
 * @param $instanceid
 * @return Array 
 */


function get_id_rol($userid,$instanceid)
{
    global $DB;
    $sql_query = "SELECT id_rol FROM {talentospilos_user_rol} WHERE id_usuario='$userid' AND id_instancia='$instanceid'";
    $consulta=$DB->get_records_sql($sql_query);

    foreach($consulta as $tomarId)
    {
        $idretornar=$tomarId->id_rol;
    }
    return $idretornar;
}

/*
 * Función que retorna el nombre del rol de un usuario con el fin de mostrar al correspondiente interfaz en seguimiento_pilos
 *
 * @param $userid
 * @param $instanceid
 * @return Array 
 */


function get_name_rol($idrol)
{
    global $DB;
    $sql_query = "SELECT nombre_rol FROM {talentospilos_rol} WHERE id='$idrol'";
    $consulta=$DB->get_record_sql($sql_query);
    return $consulta->nombre_rol;
}

/**
 * Función que obtiene la información de los conteos por monitor de los seguimientos PARES y GRUPALES
 * @see consult_counting_tracking($revisado,$tipo,$instancia,$fechas_epoch,$persona)
 * @param $revisado ---> revisado por profesional (1 ó 0)
 * @param $tipo     ---> tipo de seguimiento (PARES ó GRUPAL) 
 * @param $instancia
 * @param $fechas_epoch --> Intervalo de fechas en la que empieza y termina el semestre actual
 * @return string 
 */
 
 function consult_counting_tracking($revisado,$tipo,$instancia,$fechas_epoch,$persona){
    $sql = "";
    $aux = "";

    if ($tipo == 'PARES'){
      $sql.= "SELECT count(DISTINCT {talentospilos_seguimiento}.id) FROM {talentospilos_seguimiento}  INNER JOIN {talentospilos_seg_estudiante} ON {talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento where ";
      $aux.="and id_estudiante='$persona->id_estudiante'";

    }else if($tipo == 'GRUPAL') {
      $sql .= "SELECT count(*)   FROM {talentospilos_seguimiento} where ";
      $aux .= "and id_monitor='$persona'";

    }
     $sql.="revisado_profesional='$revisado' and tipo='$tipo' and id_instancia='$instancia' and status<>0 and (fecha between '$fechas_epoch[0]' and '$fechas_epoch[1]')";
     $sql.=$aux;

     return $sql;
 }



/*
 * Función que trae la información necesaria para los seguimientos considerando el monitor actual, la instancia actual asi como
 * que el monitor este asignado como tal a esta instancia
 *
 * @param $id_monitor
 * @param $id_instance 
 * @return Array 
 */

function get_seguimientos_monitor($id_monitor,$id_instance,$fechas_epoch,$periodo){
    global $DB;

    $semestre_act = get_current_semester();

    $id_info_field=get_id_info_field("idtalentos");
    $sql_query = "SELECT ROW_NUMBER() OVER(ORDER BY seguimiento.id ASC) AS number_unique,seguimiento.id AS id_seguimiento,
                  seguimiento.tipo,usuario_monitor
                  .id AS id_monitor_creo,usuario_monitor.firstname AS nombre_monitor_creo,nombre_usuario_estudiante.firstname 
                  AS nombre_estudiante,nombre_usuario_estudiante.lastname AS apellido_estudiante,seguimiento.created,seguimiento.fecha,seguimiento.hora_ini,
                  seguimiento.hora_fin,seguimiento.lugar,seguimiento.tema,seguimiento.objetivos,seguimiento.actividades,seguimiento.individual,seguimiento.revisado_profesional AS profesional,
                  seguimiento.revisado_practicante AS practicante,seguimiento.individual_riesgo,seguimiento.familiar_desc,seguimiento.familiar_riesgo,seguimiento.academico,
                  seguimiento.academico_riesgo,seguimiento.economico,seguimiento.economico_riesgo, seguimiento.vida_uni,seguimiento.vida_uni_riesgo,
                  seguimiento.observaciones AS observaciones,seguimiento.id AS status,seguimiento.id AS sede, usuario_estudiante.id_tal AS id_estudiante,monitor_actual.id_monitor AS id_monitor_actual,
                  usuario_mon_actual.firstname AS nombre_monitor_actual,usuario_mon_actual.lastname AS apellido_monitor_actual, usuario_monitor.lastname AS apellido_monitor_creo
                  FROM {talentospilos_seg_estudiante} AS s_estudiante INNER JOIN {talentospilos_seguimiento} AS seguimiento ON 
                  (s_estudiante.id_seguimiento=seguimiento.id) INNER JOIN {user} AS usuario_monitor ON (seguimiento.id_monitor = usuario_monitor.id) 
                  INNER JOIN (SELECT DISTINCT MAX(data.userid) AS userid, data.data as id_tal FROM {talentospilos_usuario} AS usuarios_tal INNER JOIN mdl_user_info_data AS data 
                  ON (CAST(usuarios_tal.id AS varchar) = data.data) WHERE data.fieldid ='$id_info_field->id' GROUP BY id_tal) AS usuario_estudiante  ON 
                  (usuario_estudiante.id_tal=CAST(s_estudiante.id_estudiante AS varchar)) INNER JOIN {user} as nombre_usuario_estudiante ON 
                  (nombre_usuario_estudiante.id=usuario_estudiante.userid) INNER JOIN {talentospilos_monitor_estud} as monitor_actual 
                  ON (CAST(monitor_actual.id_estudiante AS text)=CAST(s_estudiante.id_estudiante AS text)) INNER JOIN {user} AS usuario_mon_actual ON (monitor_actual.id_monitor=usuario_mon_actual.id)
                  WHERE monitor_actual.id_monitor='$id_monitor' AND seguimiento.id_instancia='$id_instance' AND seguimiento.status <> 0  AND monitor_actual.id_semestre='$periodo->max' AND
                  (seguimiento.fecha between '$fechas_epoch[0]' and '$fechas_epoch[1]')  AND monitor_actual.id_instancia='$id_instance'  ORDER BY usuario_monitor.firstname;
    ";
    
    $consulta=$DB->get_records_sql($sql_query);
    $array_cantidades =[];
    $array_estudiantes=[];


    foreach($consulta as $estudiante)
    {
      //Número de registros del estudiante revisados por el profesional,no revisados por el mismo,Número total de registros del estudiante.
      $sql = consult_counting_tracking(1,"PARES",$id_instance,$fechas_epoch,$estudiante);
      $estudiante->registros_estudiantes_revisados=$DB->get_record_sql($sql)->count;

      $sql = consult_counting_tracking(0,"PARES",$id_instance,$fechas_epoch,$estudiante);
      $estudiante->registros_estudiantes_norevisados=$DB->get_record_sql($sql)->count;

      $estudiante->registros_estudiantes_total=($estudiante->registros_estudiantes_revisados + $estudiante->registros_estudiantes_norevisados);


      
      //Número de registros del estudiante revisados por el profesional, no revisados por el mismo,Número total de registros del monitor cuando son GRUPALES. 
       $sql = consult_counting_tracking(1,"GRUPAL",$id_instance,$fechas_epoch,$id_monitor);
       $estudiante->registros_estudiantes_revisados_grupal=0;

       $sql = consult_counting_tracking(0,"GRUPAL",$id_instance,$fechas_epoch,$id_monitor);
       $estudiante->registros_estudiantes_norevisados_grupal=0;

       $estudiante->registros_estudiantes_total_grupal=($estudiante->registros_estudiantes_revisados_grupal + $estudiante->registros_estudiantes_norevisados_grupal );
       array_push($array_estudiantes,$estudiante);
    }

    return $array_estudiantes;
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
    $valorRetorno=[];
    
    $sql_query= "SELECT count(DISTINCT {talentospilos_seguimiento}.id) FROM {talentospilos_seguimiento} INNER JOIN {talentospilos_seg_estudiante} ON ({talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento) where (revisado_profesional='1') and id_monitor='$id_monitor' and id_instancia='$id_instance' and status<>0";
    $valorRetorno[0]=$DB->get_record_sql($sql_query);
    
    $sql_query= "SELECT count(DISTINCT {talentospilos_seguimiento}.id) FROM {talentospilos_seguimiento} INNER JOIN {talentospilos_seg_estudiante} ON ({talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento) where (revisado_profesional='0')and id_monitor='$id_monitor' and id_instancia='$id_instance' and status<>0";
    $valorRetorno[1]=$DB->get_record_sql($sql_query);
    
    $sql_query= "SELECT count(DISTINCT {talentospilos_seguimiento}.id) FROM {talentospilos_seguimiento} INNER JOIN {talentospilos_seg_estudiante} ON ({talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento) where  id_monitor='$id_monitor' and id_instancia='$id_instance' and status<>0";
    $valorRetorno[2]=$DB->get_record_sql($sql_query);

    return $valorRetorno;
}


/*
 * Función que consulta información de los monitores asignados a un practicante
 * 
 * @param $id_practicante
 * @return Array 
 */
function get_monitores_practicante($id_practicante,$id_instancia,$semester)
{
    global $DB;
    
    $sql_query = "SELECT DISTINCT usuario_rol.id_usuario,usuario.firstname,usuario.lastname  
                  FROM {talentospilos_user_rol} as usuario_rol INNER JOIN {user} AS usuario ON 
                  (usuario.id=usuario_rol.id_usuario) WHERE id_jefe='$id_practicante' and id_instancia='$id_instancia' and id_semestre='$semester'";

    $consulta=$DB->get_records_sql($sql_query);
    //print_r($consulta);
    
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
function get_practicantes_profesional($id_profesional,$id_instancia,$semester)
{
    global $DB;

    $sql_query = "SELECT DISTINCT usuario_rol.id_usuario,usuario.firstname AS nombre,usuario.lastname AS apellido, usuario_rol.id_semestre AS semestre 
                  FROM {talentospilos_user_rol} as usuario_rol INNER JOIN {user} AS usuario ON 
                  (usuario.id=usuario_rol.id_usuario) WHERE id_jefe='$id_profesional' and id_rol<>4 and id_semestre ='$semester'";


    $consulta=$DB->get_records_sql($sql_query);

    $arreglo_retornar= array();
    $arreglo_cantidades= array();
    $total_registros_no=[];




    //por cada registro retornado se toma la informacion necesaria, se añade a un arreglo auxiliar y este se agrega 
    //al areglo que sera retornado
    foreach($consulta as $practicantes)
    {
        
    $monitores = get_monitores_practicante($practicantes->id_usuario,$id_instancia,$semester);
    $total_registros[0]=0;
    $total_registros[1]=0;
    $total_registros[2]=0;

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
        //array_push($array_auxiliar,$practicantes->semestre);
        array_push($array_auxiliar,$total_registros[0]);
        array_push($array_auxiliar,$total_registros[1]);
        array_push($array_auxiliar,$total_registros[2]);

        array_push($arreglo_retornar,$array_auxiliar);
    }

    //print_r($arreglo_retornar);
    return ($arreglo_retornar);
    
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
    $messageHtml="";

    $id_user = $USER->id;

    $sending_user = get_full_user($id_user);
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
    return "pass";
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