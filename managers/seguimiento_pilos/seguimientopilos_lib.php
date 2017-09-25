<<<<<<< HEAD
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
                  WHERE monitor_actual.id_monitor='$id_monitor' AND seguimiento.id_instancia='$id_instance' AND seguimiento.status <> 0 AND
                  (seguimiento.fecha between '$fechas_epoch[0]' and '$fechas_epoch[1]') AND monitor_actual.id_semestre='$periodo->max' AND monitor_actual.id_instancia='$id_instance'  ORDER BY usuario_monitor.firstname;
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
=======
requirejs(['jquery', 'bootstrap', 'datatables.net', 'datatables.net-buttons', 'buttons.flash', 'jszip', 'pdfmake', 'vfs_fonts', 'buttons.html5', 'buttons.print', 'sweetalert', 'amd_actions','select2'], function($) {

    var globalArregloPares = [];
    var globalArregloGrupal = [];
    var arregloMonitorYEstudiantes = [];
    var arregloPracticanteYMonitor = [];
    var arregloImprimirPares = [];
    var arregloImprimirGrupos = [];
    var rol = 0;
    var id = 0;
    var name = "";
    var htmltexto = "";
    var instance = "";
    var email = "";


    $(document).ready(function() {

        var usuario="";
      //Obtenemos el ID de la instancia actual.

        var informacionUrl = window.location.search.split("&");
        for (var i = 0; i < informacionUrl.length; i++) {
            var elemento = informacionUrl[i].split("=");
            if (elemento[0] == "?instanceid" || elemento[0] == "instanceid") {
                var instance = elemento[1];
            }
        }

        //Oculta el div de la parte de sistemas.
        //$(".well.col-md-10.col-md-offset-1.reporte-seguimiento.oculto").hide();
        
        //Se obtiene la información correspondiente al nombre,id,email y rol de la persona conectada.
        $.ajax({
            type: "POST",
            data: {
                type: "getInfo",
                instance: instance
            },
            url: "../../../blocks/ases/managers/seguimiento_pilos/seguimientopilos_report.php",
            async: false,
            success: function(msg) {
              $data= $.parseJSON(msg);
              name = $data.username;
              id = $data.id;
              email = $data.email; 
              rol = $data.rol;
              namerol=$data.name_rol;
            },
            dataType: "text",
            cache: "false",
            error: function(msg) {
                swal({
                    title: "error al obtener información del usuario, getInfo.",
                    html: true,
                    type: "error",
                    confirmButtonColor: "#d51b23"
                });
            },
        });


        name = "";
        //Se muestra la interfaz correspondiente al usuario.
        if (namerol == "monitor_ps") {
            usuario = "monitor";

        }
        else if (namerol == "practicante_ps") {
            usuario ="practicante";
        }
        else if (namerol == "profesional_ps") {

            usuario = "profesional";
        }
        else if (namerol == "sistemas") {
            usuario = "sistemas";
        }

        var usuario = [];
        usuario["id"] = id;
        usuario["name"] = name;
        usuario["namerol"]= namerol;


        crear_conteo(usuario);


        /*Cuando el usuario sea practicante = le es permitido */
        if (namerol == "practicante_ps") {

            $("input[name=profesional]").attr('disabled', true);
            $("input[name=practicante]").attr('disabled', true);

            limpiar_riesgos();
            editar_seguimiento(namerol);
            cancelar_edicion(namerol);
            borrar_seguimiento(namerol);
            modificar_seguimiento();
            actualizar_riesgo();
            enviar_correo(instance);
            consultar_seguimientos_persona(instance,usuario);



        /*Cuando el usuario sea profesional = le es permitido */
        }else if (namerol == "profesional_ps") {
            //se inicia la adicion del evento
            $("input[name=practicante]").attr('disabled', true);
            $("input[name=profesional]").attr('disabled', true);
            limpiar_riesgos();
            cancelar_edicion(namerol);
            editar_seguimiento(namerol);
            modificar_seguimiento();
            borrar_seguimiento(namerol);
            actualizar_riesgo();
            enviar_correo(instance);
            consultar_seguimientos_persona(instance,usuario);
            

        /*Cuando el usuario sea monitor = Le es permitido : */
        }else if (namerol == "monitor_ps") {   
            limpiar_riesgos();
            editar_seguimiento(namerol);
            cancelar_edicion(namerol);
            borrar_seguimiento(namerol);
            modificar_seguimiento();
            actualizar_riesgo();
            consultar_seguimientos_persona(instance,usuario);


        
        /*Cuando el usuario sea sistemas = Le es permitido : */
        }else if(namerol == "sistemas"){
            limpiar_riesgos();
            editar_seguimiento(namerol);
            cancelar_edicion(namerol);
            borrar_seguimiento(namerol);
            modificar_seguimiento();
            actualizar_riesgo();
            enviar_correo(instance);
            anadirEvento(instance);




        }

    });



   

//--------LISTENERS DE LOS ELEMENTOS DE LA PÁGINA.
function consultar_seguimientos_persona(instance,usuario){
            $("#periodos").change(function() {
            if (namerol!='sistemas'){
            var semestre =$("#periodos").val();
            var id_persona = id;
            $.ajax({
                 type: "POST",
                 data: {
                    id_persona: id_persona,
                    id_semestre: semestre,
                    instance: instance,
                    otro : true,
                    type: "consulta_sistemas"
                    },
                    url: "../../../blocks/ases/managers/seguimiento_pilos/seguimientopilos_report.php",
                    async: false,


                    success: function(msg) {

                    if(msg==""){
                      $('#reemplazarToogle').html('<label> No se encontraron registros </label>' );
                      crear_conteo(usuario);



                    }else{
                    $('#reemplazarToogle').html(msg);
                    }
                    $(".well.col-md-10.col-md-offset-1.reporte-seguimiento.oculto").slideDown( "slow" );
                    crear_conteo(usuario);




                    },
                    dataType: "text",
                    cache: "false",
                    error: function(msg) { alert("error al consultar seguimientos de personas");},
             });

            }

            
        });
}
/*
 * Funcion para el rol sistemas
 *
 */

 function anadirEvento(instance) {
            $("#personas").val('').change();
            
            //Se activa el select2 cuando el usuario es de sistemas.
            $("#personas").select2({  
                placeholder: "Seleccionar persona",

                language: {
                noResults: function() {
                return "No hay resultado";        
                },
                searching: function() {
                return "Buscando..";
             }
            }
          });
        $("#periodos").select2({    
                language: {
                noResults: function() {
                return "No hay resultado";        
                },
                searching: function() {
                return "Buscando..";
             }
            }
          });

        consultar_periodos(instance,namerol);


        $('#consultar_persona').on('click', function() {

            var id_persona =$("#personas").children(":selected").attr("value");
            var id_semestre =$("#periodos").children(":selected").attr("value");
            var fechas_epoch=[];



            if(id_persona == undefined){
                swal({
                        title: "Debe escoger una persona para realizar la consulta",
                        html: true,
                        type: "warning",
                        confirmButtonColor: "#d51b23"
                    });
            }else{
                $(".well.col-md-10.col-md-offset-1.reporte-seguimiento.oculto").show();

            $.ajax({
                 type: "POST",
                 data: {
                    id_persona: id_persona,
                    id_semestre: id_semestre,
                    instance: instance,
                    type: "consulta_sistemas"
                    },
                    url: "../../../blocks/ases/managers/seguimiento_pilos/seguimientopilos_report.php",
                    async: false,


                    success: function(msg) {

                    if(msg==""){
                      $('#reemplazarToogle').html('<label> No se encontraron registros </label>' );

                    }else{
                    $('#reemplazarToogle').html(msg);
                    }
                    $(".well.col-md-10.col-md-offset-1.reporte-seguimiento.oculto").slideDown( "slow" );

                    },
                    dataType: "text",
                    cache: "false",
                    error: function(msg) { alert("error al consultar seguimientos de personas");},
             });

           }

        });
    }



function crear_conteo(usuario){
    var periodo = $("#periodos option:selected").text();
    var conteo=0;
    var contenedor="";
    
    if(usuario["namerol"] == 'monitor_ps'){
        var conteos_monitor =realizar_conteo(usuario);
        contenedor = '<div class="row"><div class="col-sm-12"><h2>Información monitor - PERIODO :'+periodo+' </h2><div class="panel panel-default"> <div class="panel-body"><h4 class="panel-title"><span class="pull-left"> Revisados  :<label for="revisado_monitor_'+conteo+'">'+conteos_monitor[0]+'</label><b></b> - NO Revisados :<label for="norevisado_monitor_'+conteo+'">'+conteos_monitor[1]+'</label><b></b> - Total  :<label for="total_monitor_'+conteo+'">'+conteos_monitor[2]+'</label> <b></b> </span></h4></div></div></div></div>';
        $("#conteo_principal").empty();
        $("#conteo_principal").html(contenedor);


    }else if (usuario["namerol"] == 'practicante_ps'){
        var conteos_practicante =realizar_conteo(usuario);
        contenedor = '<div class="row"><div class="col-sm-12"><h2>Información practicante - PERIODO :'+periodo+' </h2><div class="panel panel-default"> <div class="panel-body"><h4 class="panel-title"><span class="pull-left"> Revisados  :'+conteos_practicante[0]+' <b></b> - NO Revisados :'+conteos_practicante[1]+' <b></b> - Total  :'+conteos_practicante[2]+' <b></b> </span></h4></div></div></div></div>';
        $("#conteo_principal").empty();
        $("#conteo_principal").html(contenedor);

    }else if (usuario["namerol"] == 'profesional_ps'){
        var conteos_profesional =  realizar_conteo(usuario);
        contenedor = '<div class="row"><div class="col-sm-12"><h2>Información profesional - PERIODO :'+periodo+' </h2><div class="panel panel-default"> <div class="panel-body"><h4 class="panel-title"><span class="pull-left"> Revisados  :'+conteos_profesional[0]+' <b></b> - NO Revisados :'+conteos_profesional[1]+' <b></b> - Total  :'+conteos_profesional[2]+' <b></b> </span></h4></div></div></div></div>';
        $("#conteo_principal").empty();
        $("#conteo_principal").html(contenedor);

    }else if(usuario["namerol" ] == 'sistemas'){


    }
}
function realizar_conteo(usuario,dependiente="ninguno"){
    var conteos= [];

    var total_grupal_revisado = 0;
    var total_grupal_norevisado = 0;
    var total_monitor_revisado = 0;
    var total_monitor_norevisado = 0;

    if(usuario["namerol"] == 'monitor_ps'){
    var numero_pares=0;
    var numero_grupales=0;

    if (dependiente =="ninguno"){
    numero_pares = $('.panel-heading.pares').children().length;
    numero_grupales = $('.panel-heading.grupal').children().length;


    }else{
    numero_pares = $("#collapse"+usuario["id"]+" .panel-heading.pares").children().length;
    numero_grupales = $("#collapse"+usuario["id"]+" .panel-heading.grupal").children().length;
    }
    $("label[for='norevisado_grupal_"+usuario["id"]+"']").html(numero_grupales);
    $("label[for='total_grupal_"+usuario["id"]+"']").html(numero_grupales);


    for(var cantidad =0; cantidad<numero_pares;cantidad++){
       total_monitor_revisado += Number($("label[for='revisado_pares_"+ usuario["id"]+"_"+cantidad+"']").text());
       total_monitor_norevisado += Number($("label[for='norevisado_pares_"+ usuario["id"]+"_"+cantidad+"']").text());
    }

    for(var cantidad =0; cantidad<numero_grupales;cantidad++){
       total_grupal_revisado += 0;
       total_grupal_norevisado = numero_grupales;

    }
    total = (total_monitor_revisado+total_grupal_revisado) + (total_monitor_norevisado+total_grupal_norevisado);
    return new Array((total_monitor_revisado+total_grupal_revisado),(total_monitor_norevisado+total_grupal_norevisado), total);
    
    }else if (usuario["namerol"] == 'practicante_ps'){
      var numero_monitores=0;
      conteos =[0,0,0];  
      var conteos_monitor =[ ];

      if(dependiente =="ninguno"){
       numero_monitores = $('.panel-heading.practicante').children().length;
       for(var monitor = 0;monitor<numero_monitores;monitor++){

      var collapse_name =$( ".panel-heading.practicante:eq("+monitor+")" ).find('a').attr('href');
      var id_monitor = collapse_name.split("#collapse")[1];
      var usuario_monitor = [];

      usuario_monitor["id"] = id_monitor;
      usuario_monitor["namerol"] ="monitor_ps";
      conteos_monitor = realizar_conteo(usuario_monitor,"practicante");
      $("label[for='revisado_monitor_"+id_monitor+"']").html(conteos_monitor[0]);
      $("label[for='norevisado_monitor_"+id_monitor+"']").html(conteos_monitor[1]);
      $("label[for='total_monitor_"+id_monitor+"']").html(conteos_monitor[2]);


      conteos[0]+=conteos_monitor[0];
      conteos[1]+=conteos_monitor[1];
      conteos[2]+=conteos_monitor[2];

    }

    
      }else{
        numero_monitores = $("#collapse"+usuario["id"]+" .panel-heading.practicante").children().length;
      }
              for(var monitor = 0;monitor<numero_monitores;monitor++){

      var collapse_name =$( "#collapse"+usuario["id"]+" .panel-heading.practicante:eq("+monitor+")" ).find('a').attr('href');
      var id_monitor = collapse_name.split("#collapse")[1];
      var usuario_monitor = [];

      usuario_monitor["id"] = id_monitor;
      usuario_monitor["namerol"] ="monitor_ps";
      conteos_monitor = realizar_conteo(usuario_monitor,"practicante");
      $("label[for='revisado_monitor_"+id_monitor+"']").html(conteos_monitor[0]);
      $("label[for='norevisado_monitor_"+id_monitor+"']").html(conteos_monitor[1]);
      $("label[for='total_monitor_"+id_monitor+"']").html(conteos_monitor[2]);


      conteos[0]+=conteos_monitor[0];
      conteos[1]+=conteos_monitor[1];
      conteos[2]+=conteos_monitor[2];

    }



    return conteos;

    }else if(usuario["namerol"] =='profesional_ps'){
     conteos =[0,0,0];
     var numero_practicantes = $('.panel-heading.profesional').children().length;
     var conteos_practicantes = [];

     for(var practicante=0;practicante<numero_practicantes;practicante++){
      var collapse_name =$(".panel-heading.profesional:eq("+practicante+")" ).find('a').attr('href');
      var id_practicante = collapse_name.split("#collapse")[1];
      var usuario_practicante = [];
      usuario_practicante["id"] = id_practicante;
      usuario_practicante["namerol"] ="practicante_ps";
      conteos_practicantes =realizar_conteo(usuario_practicante,"practicante");
      $("label[for='revisado_practicante_"+id_practicante+"']").html(conteos_practicantes[0]);
      $("label[for='norevisado_practicante_"+id_practicante+"']").html(conteos_practicantes[1]);
      $("label[for='total_practicante_"+id_practicante+"']").html(conteos_practicantes[2]);
      conteos[0]+=conteos_practicantes[0];
      conteos[1]+=conteos_practicantes[1];
      conteos[2]+=conteos_practicantes[2];
     }
     return conteos;

    }




}

/*
 * Funcion para enviar correos.
 *
 */

function enviar_correo(instance){

                $('body').on('click', '.btn.btn-info.btn-lg.botonCorreo', function() {

                var id_registro = $(this).attr('value');
                var texto = $("#textarea_" + id_registro);
                if (texto.val() == "") {
                    swal({
                        title: "Para enviar una observación debe llenar el campo correspondiente",
                        html: true,
                        type: "error",
                        confirmButtonColor: "#d51b23"
                    });
                }else {
                    //se recupera el mensaje y el id del monitor al que se va a enviar el mensaje
                    var particionar_informacion = texto.attr('name').split("_");
                    //alert(particionar_informacion[4]);
                    var tipo = particionar_informacion[0];
                    var codigoN1 = particionar_informacion[1];
                    var codigoN2 = particionar_informacion[2];
                    var fecha = particionar_informacion[3];
                    var nombre = particionar_informacion[4];
                    var mensaje_enviar = texto.val();

                    //se limpia el textarea
                    texto.val("");
                    var respuesta = "";

                    //se llama el ajax para enviar el mensaje
                    $.ajax({
                        type: "POST",
                        data: {
                            type: "send_email_to_user",
                            tipoSeg: tipo,
                            codigoEnviarN1: codigoN1,
                            codigoEnviarN2: codigoN2,
                            fecha: fecha,
                            nombre: nombre,
                            message: mensaje_enviar
                        },
                        url: "../../../blocks/ases/managers/seguimiento_pilos/seguimientopilos_report.php",
                        async: false,
                        success: function(msg) {
                            //si el envio del mensaje fue exitoso
                            if (msg == 1) {
                                swal({
                                    title: "Correo enviado",
                                    html: true,
                                    type: "success",
                                    confirmButtonColor: "#d51b23"
                                });
                            }
                            else {
                                swal({
                                    title: "error al enviar el correo al monitor",
                                    html: true,
                                    type: "error",
                                    confirmButtonColor: "#d51b23"
                                });
                            }
                        },
                        dataType: "text",
                        cache: "false",
                        error: function(msg) {
                            swal({
                                title: "error al enviar el correo",
                                html: true,
                                type: "error",
                                confirmButtonColor: "#d51b23"
                            });
                        },
                    });
                }
            });
}



/*
 * Función para modificar un seguimiento determinado.
 *
 */
function modificar_seguimiento(){


        $('body').on('click', 'span.btn.btn-info.btn-lg.botonModificarSeguimiento', function() {
        var id = $(this).attr("value");
        var profesional = "",practicante = "";
        var combo_hora_inicio = document.getElementById("h_ini_" + id);
        var combo_hora_fin = document.getElementById("h_fin_" + id);
        var combo_min_inicio = document.getElementById("m_ini_" + id);
        var combo_min_fin = document.getElementById("m_fin_" + id);
        var hora_inicial = combo_hora_inicio.options[combo_hora_inicio.selectedIndex].text;
        var hora_final = combo_hora_fin.options[combo_hora_fin.selectedIndex].text;
        var min_inicial = combo_min_inicio.options[combo_min_inicio.selectedIndex].text;
        var min_final = combo_min_fin.options[combo_min_fin.selectedIndex].text;
        var validar = validarHoras(hora_inicial, hora_final, min_inicial, min_final);

        if (validar == "") {
            if ($("#profesional_" + id).is(':checked')) {
                  profesional = 1;
                }else{
                  profesional = 0;
                }

            if ($("#practicante_" + id).is(':checked')) {
                   practicante = 1;
                }else{
                   practicante = 0;
                }

        var $tbody = $(this).parent().parent().parent();
        var idSeguimientoActualizar = $(this).attr('value');
        var lugar = $tbody.find("#lugar_" + idSeguimientoActualizar).val();
        var tema = $tbody.find("#tema_" + id).val();
        var objetivos = $tbody.find("#objetivos_" + id).val();
        var fecha = $tbody.find("#fecha_" + id).val();
        var h_inicial = hora_inicial + ":" + min_inicial;
        var h_final = hora_final + ":" + min_final;
        var obindividual = $tbody.find("#obindividual_" + id).val();
        var riesgoIndividual = $("input[name='riesgo_individual_" + id + "']:checked").val();
        if (riesgoIndividual == undefined) {
               riesgoIndividual = "0";
            }

        var obfamiliar = $tbody.find("#obfamiliar_" + id).val();
        var riesgoFamiliar = $("input[name='riesgo_familiar_" + id + "']:checked").val();
        if (riesgoFamiliar == undefined) {
                riesgoFamiliar = "0";
            }

        var obacademico = $tbody.find("#obacademico_" + id).val();
        var riesgoAcademico = $("input[name='riesgo_academico_" + id + "']:checked").val();
        if (riesgoAcademico == undefined) {
                riesgoAcademico = "0";
            }

        var obeconomico = $tbody.find("#obeconomico_" + id).val();
        var riesgoEconomico = $("input[name='riesgo_economico_" + id + "']:checked").val();
        if (riesgoEconomico == undefined) {
                  riesgoEconomico = "0";
            }

        var obuniversitario = $tbody.find("#obuniversitario_" + id).val();
        var riesgoUniversitario = $("input[name='riesgo_universitario_" + id + "']:checked").val();
        if (riesgoUniversitario == undefined) {
                  riesgoUniversitario = "0";
          }

        var observacionesGeneral = $tbody.find("#observacionesGeneral_" + id).val();


        if (lugar == "" || tema == "" || objetivos == "") {
            swal({
                title: "Debe ingresar los datos completamente",
                html: true,
                type: "warning",
                confirmButtonColor: "#d51b23"
            });
             }else{
                  if (!((obindividual != "" && riesgoIndividual == 0) || (obfamiliar != "" && riesgoFamiliar == 0) || (obacademico != "" && riesgoAcademico == 0) || (obeconomico != "" && riesgoEconomico == 0) || (obuniversitario != "" && riesgoUniversitario == 0) || (obindividual == "" && riesgoIndividual > 0) || (obfamiliar == "" && riesgoFamiliar > 0) || (obacademico == "" && riesgoAcademico > 0) || (obeconomico == "" && riesgoEconomico > 0) || (obuniversitario == "" && riesgoUniversitario > 0))) {
                    var seguimiento =new Object();
                    seguimiento.id = idSeguimientoActualizar;
                    seguimiento.lugar = lugar;
                    seguimiento.tema = tema;
                    seguimiento.objetivos = objetivos;
                    seguimiento.individual = obindividual;
                    seguimiento.individual_riesgo= riesgoIndividual;
                    seguimiento.familiar_desc= obfamiliar;
                    seguimiento.familiar_riesgo = riesgoFamiliar;
                    seguimiento.academico = obacademico;
                    seguimiento.academico_riesgo = riesgoAcademico;
                    seguimiento.economico = obeconomico;
                    seguimiento.economico_riesgo = riesgoEconomico;
                    seguimiento.vida_uni = obuniversitario;
                    seguimiento.vida_uni_riesgo = riesgoUniversitario;
                    seguimiento.observaciones = observacionesGeneral;
                    seguimiento.revisado_practicante = practicante;
                    seguimiento.revisado_profesional = profesional;
                    seguimiento.fecha = fecha;
                    seguimiento.hora_ini = h_inicial;
                    seguimiento.hora_fin = h_final;
                    $.ajax({
                            type: "POST",
                             data: {
                                seguimiento:seguimiento,
                                type: "actualizar_registro",
                            },
                             url: "../../../blocks/ases/managers/seguimiento_pilos/seguimientopilos_report.php",
                             async: false,
                                success: function(msg) {
                                    if (msg == "0") {
                                        swal({
                                            title: "error al actualizar registro",
                                            html: true,
                                            type: "error",
                                            confirmButtonColor: "#d51b23"
                                        });
                                    }
                                    else if (msg == "1") {
                                        swal("¡Hecho!", "El registro ha sido actualizado",
                                            "success");
                                    }
                                    else {
                                        swal({
                                            title: "Debe ingresar correctamente los riesgos",
                                            html: true,
                                            type: "warning",
                                            confirmButtonColor: "#d51b23"
                                        });
                                    }
                                },
                                error: function(msg) {},
                            });
                        }
                        else {
                            swal({
                                title: "Debe ingresar correctamente los riesgos",
                                html: true,
                                type: "warning",
                                confirmButtonColor: "#d51b23"
                            });
                        }

                    }
                }
                else {
                    swal({
                        title: validar,
                        html: true,
                        type: "warning",
                        confirmButtonColor: "#d51b23"
                    });

                }
            });
}



 /*
 * Función para editar un seguimiento determinado dado los roles existentes.
 *
 */
function editar_seguimiento(namerol){

    $('body').on('click', 'span.btn-info.btn-lg.botonesSeguimiento.botonEditarSeguimiento', function() {
        var id = $(this).attr("value");
        var $tbody = $(this).parent().parent().parent();

        var visto_profesional = false;

        if(namerol == 'monitor_ps'){
         visto_profesional = $("#profesional_" + id).is(':checked');

        }else if (namerol == 'practicante_ps'){
          $("input[name=practicante]").attr('disabled', false);

        }else if (namerol =='profesional_ps'){
          $("input[name=profesional]").attr('disabled', false);

        }


        if(visto_profesional == false){

        $tbody.find('.editable').removeAttr('readonly');
        $tbody.find('.botonesSeguimiento').toggleClass('ocultar');
        $tbody.find('.quitar-ocultar').toggleClass('ocultar');
        $tbody.find('.radio-ocultar').toggleClass('ocultar');
        auxiliar_editar(id);
        seleccionarButtons(id);

        }else{
            swal("¡Advertencia!",
                "No es posible editar el seguimiento, debido a que ya ha sido revisado por un profesional",
                "warning");}
        });

    }


/*
 * Función para borrar un seguimiento determinado dado los roles existentes.
 *
 */
function borrar_seguimiento(namerol){

    $('body').on('click', 'span.btn.btn-info.btn-lg.botonBorrar', function() {
    var id_registro = $(this).attr('value');
    var visto_profesional = false;

    if(namerol == 'monitor_ps'){
      visto_profesional = $("#profesional_" + id).is(':checked');

    }else if (namerol == 'practicante_ps'){

    
    }else if (namerol =='profesional_ps'){
    
    } 
    if (visto_profesional  == false){
        swal({
             title: "¿Seguro que desea eliminar el registro?",
             text: "No podrás deshacer este paso",
             type: "warning",
             showCancelButton: true,
             cancelButtonText: "No",
             confirmButtonColor: "#d51b23",
             confirmButtonText: "Si",
             closeOnConfirm: false
             },
        function() {
        $.ajax({
            type: "POST",
            data: {
                    id: id_registro,
                    type: "eliminar_registro",
                   },
            url: "../../../blocks/ases/managers/seguimiento_pilos/seguimientopilos_report.php",
            async: false,
            success: function(msg) {
                swal({
                     title: msg.title,
                     html: true,
                     text: msg.msg,
                     type: msg.type,
                     confirmButtonColor: "#d51b23"
                     });
                     setTimeout('document.location.reload()', 500);
                    },
            dataType: 'json',
            cache: "false",
            error: function(msg) {},
            });
        });
      }

    });
}


 /*
 * Función para cancelar la edición de un seguimiento determinado dado cualquiera de los roles existentes.
 *
 */
function cancelar_edicion(namerol){

        $('body').on('click', 'span.btn.btn-info.btn-lg.botonesSeguimiento.botonCancelarSeguimiento', function() {
            var id = $(this).attr("value");

        if(namerol == 'monitor_ps'){

        }else if (namerol == 'practicante_ps'){
          $("input[name=practicante]").attr('disabled', true);

        }else if (namerol =='profesional_ps'){
          $("input[name=profesional]").attr('disabled', true);

        }
             var $tbody = $(this).parent().parent().parent();
             $tbody.find('.editable').attr('readonly', true);
             $tbody.find('.botonesSeguimiento').toggleClass('ocultar');
             $tbody.find('.quitar-ocultar').toggleClass('ocultar');
             $tbody.find('.radio-ocultar').toggleClass('ocultar');
             auxiliar_cancelar(id);
         });
}

/*
 * Función para limpiar la descripción de los riesgos y los radiobuttons seleccionados.
 *
 */
    function limpiar_riesgos(){

    $('body').on('click', '.limpiar', function() {
                var elemento = $(this).closest("div").attr('id').split("_");
                var id = elemento[2].split("div").pop();
                switch (elemento[1]) {
                    case 'individual':
                        $("#obindividual_" + id).val("");
                        auxiliar_limpiar("#riesgo_individual_", id);
                        break;

                    case 'familiar':
                        $("#obfamiliar_" + id).val("");
                        auxiliar_limpiar("#riesgo_familiar_", id);
                        break;

                    case 'academico':
                        $("#obacademico_" + id).val("");
                        auxiliar_limpiar("#riesgo_academico_", id);
                        break;

                    case 'economico':
                        $("#obeconomico_" + id).val("");
                        auxiliar_limpiar("#riesgo_economico_", id);
                        break;

                    case 'universitario':
                        $("#obuniversitario_" + id).val("");
                        auxiliar_limpiar("#riesgo_universitario_", id);
                        break;

                    default:
                        alert("Dato invalido");
                        break;
                }
            });
    }


//--------FUNCIONES AUXILIARES.



function consultar_periodos(instance,namerol){
            $("#periodos").change(function() {
            var periodo_escogido = $( "#periodos" ).val();
              $.ajax({
                 type: "POST",
                 data: {
                    id: periodo_escogido,
                    instance: instance,
                    type: "actualizar_personas"
                    },
                    url: "../../../blocks/ases/managers/seguimiento_pilos/seguimientopilos_report.php",
                    async: false,
                    success: function(msg) {


                    $('#personas').empty();
                    $("#personas").select2({  
                      placeholder: "Seleccionar persona",
                      language: {
                       noResults: function() {
                       return "No hay resultado";        
                     },
                       searching: function() {
                       return "Buscando..";
                   }
                }
            });       
                    if(namerol =='sistemas'){
                    var inicio = '<option value="">Seleccionar persona</option>';

                     $("#personas").attr('selectedIndex', '-1').find("option:selected").removeAttr("selected");
                     $('#personas').append(inicio+msg);
                    
                    }

                    },
                    dataType: "text",
                    cache: "false",
                    error: function(msg) { alert("error al cargar personas");},
                        });
                    });

}


//Verifica si el profesional desea marcar como revisado el seguimiento.

function verificar_profesional(){
  $('input[name="profesional"]').click(function() {
      if ($(this).is(':checked')) {
        swal({
            title: "¿Seguro que desea cambiar estado a revisado?",
            text: "En caso de modificar el seguimiento no podrá volverlo a editar",
            type: "warning",
            showCancelButton: true,
            cancelButtonText: "No",
            confirmButtonColor: "#d51b23",
            confirmButtonText: "Si",
            closeOnConfirm: true
            },
        function(isConfirm) {
            if (isConfirm == false) {
             $('input[name="profesional"]').prop('checked', false);
                 }
              });
           }
    });
  }



/*
*  Función que obtiene los mensajes de validación de la hora.
*/
    function validarHoras(h_ini, h_fin, m_ini, m_fin) {
        var detalle = "";
        if (h_ini > h_fin) {
            detalle += "* La hora final debe ser mayor a la inicial<br>";
        }else if (h_ini == h_fin) {
         if (m_ini > m_fin) {
            isvalid = false;
            detalle += "* La hora final debe ser mayor a la inicial<br>";
        }else {
         if (m_ini == m_fin) {
            detalle += "* Las horas seleccionadas deben ser diferentes<br>";
          }
        }
      }
    return detalle;
    }


/*
* Función usada para inicializar los selects de las horas/minutos finales e iniciales de cada seguimiento.
*/
    function initFormSeg(id) {
        var date = new Date();
        var minutes = date.getMinutes();
        var hour = date.getHours();
        //incializar hora
        var hora = "";
        for (var i = 0; i < 24; i++) {
            if (i == hour) {
                if (hour < 10) hour = "0" + hour;
                hora += "<option value=\"" + hour + "\" selected>" + hour + "</option>";
            }
            else if (i < 10) {
                hora += "<option value=\"0" + i + "\">0" + i + "</option>";
            }
            else {
                hora += "<option value=\"" + i + "\">" + i + "</option>";
            }
        }
        var min = "";
        for (var i = 0; i < 60; i++) {

            if (i == minutes) {
                if (minutes < 10) minutes = "0" + minutes;
                min += "<option value=\"" + minutes + "\" selected>" + minutes + "</option>";
            }
            else if (i < 10) {
                min += "<option value=\"0" + i + "\">0" + i + "</option>";
            }
            else {
                min += "<option value=\"" + i + "\">" + i + "</option>";
            }
        }
        $('#h_ini_' + id).append(hora);
        $('#m_ini_' + id).append(min);
        $('#h_fin_' + id).append(hora);
        $('#m_fin_' + id).append(min);
        $('#seguimiento #m_fin').append(min);
    }


   /*
   * Función usada para cambiar color cuando se cambie el radiobutton de riesgo.
   */

   function actualizar_riesgo(){
$(document).ready(function() {

        $('input:radio').change(function() {
        var id =$(this).parent().parent().parent().attr('id');
        var tipo_riesgo = $(this).attr('value');
        
        if(tipo_riesgo == 1){
           $("#" + id).removeClass();
           $("#"+id).addClass('table-info-pilos col-sm-12 riesgo_'+'bajo');
        }else if(tipo_riesgo == 2){
           $("#" + id).removeClass();
           $("#"+id).addClass('table-info-pilos col-sm-12 riesgo_'+'medio');
        }else if(tipo_riesgo == 3){
           $("#" + id).removeClass();
           $("#"+id).addClass('table-info-pilos col-sm-12 riesgo_'+'alto');
           }
        });});
   }


    //Oculta y muestra botones al presionar cancelar.
    function auxiliar_cancelar(id) {
        $("#titulo_fecha_" + id).hide();
        $("#borrar_" + id).show();
        $("#editar_" + id).show();
        $("#enviar_" + id).show();
        $("#hora_final_" + id).show();
        $("#mod_hora_final_" + id).hide();
        $("#hora_inicial_" + id).show();
        $("#mod_hora_ini_" + id).hide();
    }

    //Oculta y muestra botones al presionar editar, organiza fecha y horas.
    function auxiliar_editar(id) {
        $("#borrar_" + id).hide();
        $("#editar_" + id).hide();
        $("#enviar_" + id).hide();
        $("#hora_final_" + id).hide();
        $("#hora_inicial_" + id).hide();
        $("#titulo_fecha_" + id).show();
        $("#mod_hora_final_" + id).show();
        $("#mod_hora_ini_" + id).show();

        var f1 = $("#h_inicial_texto_" + id).val();
        var f2 = $("#h_final_texto_" + id).val();
        var array_f1 = f1.split(":");
        var array_f2 = f2.split(":");
        initFormSeg(id);
        //Seleccionamos la hora deacuerdo al sistema

        $("#h_ini_" + id + " option[value=" + array_f1[0] + "]").attr("selected", true);
        $("#m_ini_" + id + " option[value=" + array_f1[1] + "]").attr("selected", true);
        $("#h_fin_" + id + " option[value=" + array_f2[0] + "]").attr("selected", true);
        $("#m_fin_" + id + " option[value=" + array_f2[1] + "]").attr("selected", true);
    }


    //Limpia los campos de riesgos y deschequea su prioridad.
    function auxiliar_limpiar(texto, id) {
        $(texto + id).removeClass("riesgo_bajo");
        $(texto + id).removeClass("riesgo_medio");
        $(texto + id).removeClass("riesgo_alto");
        var text = '"' + texto.replace("#", "") + id + '"';
        $('input:radio[name=' + text + ']').each(function(i) {
            this.checked = false;
        });

    }


    //En el caso de que el check esté revisado por un profesional 
    //quita los botones de editar,borrar y observaciones.
    function revisado_profesional(id) {
        if ($("#profesional_" + id).is(':checked')) {
            $("#borrar_" + id).hide();
            $("#editar_" + id).hide();
            $("#enviar_" + id).hide();
        }
    }

    //Selecciona los radiobuttons correspondientes con la prioridad del riesgo.
    function seleccionarButtons(id_seguimiento) {


        //Riesgo individual
        if ($("#riesgo_individual_" + id_seguimiento).is('.riesgo_bajo')) {
        $("input[name=riesgo_individual_" + id_seguimiento + "][value=1]").attr('checked', 'checked');


        }
        else if ($("#riesgo_individual_" + id_seguimiento).is('.riesgo_medio')) {
            $("input[name=riesgo_individual_" + id_seguimiento + "][value=2]").attr('checked', 'checked');
        }
        else if ($("#riesgo_individual_" + id_seguimiento).is('.riesgo_alto')) {
            $("input[name=riesgo_individual_" + id_seguimiento + "][value=3]").attr('checked', 'checked');
        }
        else {
            $("input[name=riesgo_individual_" + id_seguimiento + "][value=0]").attr('checked', 'checked');

        }

        //Riesgo familiar
        if ($("#riesgo_familiar_" + id_seguimiento).is('.riesgo_bajo')) {
            $("input[name=riesgo_familiar_" + id_seguimiento + "][value=1]").attr('checked', 'checked');
        }
        else if ($("#riesgo_familiar_" + id_seguimiento).is('.riesgo_medio')) {
            $("input[name=riesgo_familiar_" + id_seguimiento + "][value=2]").attr('checked', 'checked');
        }
        else if ($("#riesgo_familiar_" + id_seguimiento).is('.riesgo_alto')) {
            $("input[name=riesgo_familiar_" + id_seguimiento + "][value=3]").attr('checked', 'checked');
        }
        else {
            $("input[name=riesgo_familiar_" + id_seguimiento + "][value=0]").attr('checked', 'checked');

        }

        //Riesgo academico
        if ($("#riesgo_academico_" + id_seguimiento).is('.riesgo_bajo')) {
            $("input[name=riesgo_academico_" + id_seguimiento + "][value=1]").attr('checked', 'checked');
        }
        else if ($("#riesgo_academico_" + id_seguimiento).is('.riesgo_medio')) {
            $("input[name=riesgo_academico_" + id_seguimiento + "][value=2]").attr('checked', 'checked');
        }
        else if ($("#riesgo_academico_" + id_seguimiento).is('.riesgo_alto')) {
            $("input[name=riesgo_academico_" + id_seguimiento + "][value=3]").attr('checked', 'checked');
        }
        else {
            $("input[name=riesgo_academico_" + id_seguimiento + "][value=0]").attr('checked', 'checked');

        }

        //Riesgo economico
        if ($("#riesgo_economico_" + id_seguimiento).is('.riesgo_bajo')) {
            $("input[name=riesgo_economico_" + id_seguimiento + "][value=1]").attr('checked', 'checked');
        }
        else if ($("#riesgo_economico_" + id_seguimiento).is('.riesgo_medio')) {
            $("input[name=riesgo_economico_" + id_seguimiento + "][value=2]").attr('checked', 'checked');
        }
        else if ($("#riesgo_economico_" + id_seguimiento).is('.riesgo_alto')) {
            $("input[name=riesgo_economico_" + id_seguimiento + "][value=3]").attr('checked', 'checked');
        }
        else {
            $("input[name=riesgo_economico_" + id_seguimiento + "][value=0]").attr('checked', 'checked');

        }

        //Riesgo universitario
        if ($("#riesgo_universitario_" + id_seguimiento).is('.riesgo_bajo')) {
            $("input[name=riesgo_universitario_" + id_seguimiento + "][value=1]").attr('checked', 'checked');
        }
        else if ($("#riesgo_universitario_" + id_seguimiento).is('.riesgo_medio')) {
            $("input[name=riesgo_universitario_" + id_seguimiento + "][value=2]").attr('checked', 'checked');
        }
        else if ($("#riesgo_universitario_" + id_seguimiento).is('.riesgo_alto')) {
            $("input[name=riesgo_universitario_" + id_seguimiento + "][value=3]").attr('checked', 'checked');
        }
        else {
            $("input[name=riesgo_universitario_" + id_seguimiento + "][value=0]").attr('checked', 'checked');

        }

    }
});
requirejs(['jquery', 'bootstrap', 'datatables.net', 'datatables.net-buttons', 'buttons.flash', 'jszip', 'pdfmake', 'vfs_fonts', 'buttons.html5', 'buttons.print', 'sweetalert', 'amd_actions','select2'], function($) {

    var globalArregloPares = [];
    var globalArregloGrupal = [];
    var arregloMonitorYEstudiantes = [];
    var arregloPracticanteYMonitor = [];
    var arregloImprimirPares = [];
    var arregloImprimirGrupos = [];
    var rol = 0;
    var id = 0;
    var name = "";
    var htmltexto = "";
    var instance = "";
    var email = "";


    $(document).ready(function() {

        var usuario="";
      //Obtenemos el ID de la instancia actual.

        var informacionUrl = window.location.search.split("&");
        for (var i = 0; i < informacionUrl.length; i++) {
            var elemento = informacionUrl[i].split("=");
            if (elemento[0] == "?instanceid" || elemento[0] == "instanceid") {
                var instance = elemento[1];
            }
        }

        //Oculta el div de la parte de sistemas.
        //$(".well.col-md-10.col-md-offset-1.reporte-seguimiento.oculto").hide();
        
        //Se obtiene la información correspondiente al nombre,id,email y rol de la persona conectada.
        $.ajax({
            type: "POST",
            data: {
                type: "getInfo",
                instance: instance
            },
            url: "../../../blocks/ases/managers/seguimiento_pilos/seguimientopilos_report.php",
            async: false,
            success: function(msg) {
              $data= $.parseJSON(msg);
              name = $data.username;
              id = $data.id;
              email = $data.email; 
              rol = $data.rol;
              namerol=$data.name_rol;
            },
            dataType: "text",
            cache: "false",
            error: function(msg) {
                swal({
                    title: "error al obtener información del usuario, getInfo.",
                    html: true,
                    type: "error",
                    confirmButtonColor: "#d51b23"
                });
            },
        });


        name = "";
        //Se muestra la interfaz correspondiente al usuario.
        if (namerol == "monitor_ps") {
            usuario = "monitor";

        }
        else if (namerol == "practicante_ps") {
            usuario ="practicante";
        }
        else if (namerol == "profesional_ps") {

            usuario = "profesional";
        }
        else if (namerol == "sistemas") {
            usuario = "sistemas";
        }

        var usuario = [];
        usuario["id"] = id;
        usuario["name"] = name;
        usuario["namerol"]= namerol;


        crear_conteo(usuario);


        /*Cuando el usuario sea practicante = le es permitido */
        if (namerol == "practicante_ps") {

            $("input[name=profesional]").attr('disabled', true);
            $("input[name=practicante]").attr('disabled', true);

            limpiar_riesgos();
            editar_seguimiento(namerol);
            cancelar_edicion(namerol);
            borrar_seguimiento(namerol);
            modificar_seguimiento();
            actualizar_riesgo();
            enviar_correo(instance);
            consultar_seguimientos_persona(instance,usuario);



        /*Cuando el usuario sea profesional = le es permitido */
        }else if (namerol == "profesional_ps") {
            //se inicia la adicion del evento
            $("input[name=practicante]").attr('disabled', true);
            $("input[name=profesional]").attr('disabled', true);
            limpiar_riesgos();
            cancelar_edicion(namerol);
            editar_seguimiento(namerol);
            modificar_seguimiento();
            borrar_seguimiento(namerol);
            actualizar_riesgo();
            enviar_correo(instance);
            consultar_seguimientos_persona(instance,usuario);
            

        /*Cuando el usuario sea monitor = Le es permitido : */
        }else if (namerol == "monitor_ps") {   
            limpiar_riesgos();
            editar_seguimiento(namerol);
            cancelar_edicion(namerol);
            borrar_seguimiento(namerol);
            modificar_seguimiento();
            actualizar_riesgo();
            consultar_seguimientos_persona(instance,usuario);


        
        /*Cuando el usuario sea sistemas = Le es permitido : */
        }else if(namerol == "sistemas"){
            limpiar_riesgos();
            editar_seguimiento(namerol);
            cancelar_edicion(namerol);
            borrar_seguimiento(namerol);
            modificar_seguimiento();
            actualizar_riesgo();
            enviar_correo(instance);
            anadirEvento(instance);




        }

    });



   

//--------LISTENERS DE LOS ELEMENTOS DE LA PÁGINA.
function consultar_seguimientos_persona(instance,usuario){
            $("#periodos").change(function() {
            if (namerol!='sistemas'){
            var semestre =$("#periodos").val();
            var id_persona = id;
            $.ajax({
                 type: "POST",
                 data: {
                    id_persona: id_persona,
                    id_semestre: semestre,
                    instance: instance,
                    otro : true,
                    type: "consulta_sistemas"
                    },
                    url: "../../../blocks/ases/managers/seguimiento_pilos/seguimientopilos_report.php",
                    async: false,


                    success: function(msg) {

                    if(msg==""){
                      $('#reemplazarToogle').html('<label> No se encontraron registros </label>' );
                      crear_conteo(usuario);



                    }else{
                    $('#reemplazarToogle').html(msg);
                    }
                    $(".well.col-md-10.col-md-offset-1.reporte-seguimiento.oculto").slideDown( "slow" );
                    crear_conteo(usuario);




                    },
                    dataType: "text",
                    cache: "false",
                    error: function(msg) { alert("error al consultar seguimientos de personas");},
             });

            }

            
        });
}
/*
 * Funcion para el rol sistemas
 *
 */

 function anadirEvento(instance) {
            $("#personas").val('').change();
            
            //Se activa el select2 cuando el usuario es de sistemas.
            $("#personas").select2({  
                placeholder: "Seleccionar persona",

                language: {
                noResults: function() {
                return "No hay resultado";        
                },
                searching: function() {
                return "Buscando..";
             }
            }
          });
        $("#periodos").select2({    
                language: {
                noResults: function() {
                return "No hay resultado";        
                },
                searching: function() {
                return "Buscando..";
             }
            }
          });

        consultar_periodos(instance,namerol);


        $('#consultar_persona').on('click', function() {

            var id_persona =$("#personas").children(":selected").attr("value");
            var id_semestre =$("#periodos").children(":selected").attr("value");
            var fechas_epoch=[];



            if(id_persona == undefined){
                swal({
                        title: "Debe escoger una persona para realizar la consulta",
                        html: true,
                        type: "warning",
                        confirmButtonColor: "#d51b23"
                    });
            }else{
                $(".well.col-md-10.col-md-offset-1.reporte-seguimiento.oculto").show();

            $.ajax({
                 type: "POST",
                 data: {
                    id_persona: id_persona,
                    id_semestre: id_semestre,
                    instance: instance,
                    type: "consulta_sistemas"
                    },
                    url: "../../../blocks/ases/managers/seguimiento_pilos/seguimientopilos_report.php",
                    async: false,


                    success: function(msg) {

                    if(msg==""){
                      $('#reemplazarToogle').html('<label> No se encontraron registros </label>' );

                    }else{
                    $('#reemplazarToogle').html(msg);
                    }
                    $(".well.col-md-10.col-md-offset-1.reporte-seguimiento.oculto").slideDown( "slow" );

                    },
                    dataType: "text",
                    cache: "false",
                    error: function(msg) { alert("error al consultar seguimientos de personas");},
             });

           }

        });
    }



function crear_conteo(usuario){
    var periodo = $("#periodos option:selected").text();
    var conteo=0;
    var contenedor="";
    
    if(usuario["namerol"] == 'monitor_ps'){
        var conteos_monitor =realizar_conteo(usuario);
        contenedor = '<div class="row"><div class="col-sm-12"><h2>Información monitor - PERIODO :'+periodo+' </h2><div class="panel panel-default"> <div class="panel-body"><h4 class="panel-title"><span class="pull-left"> Revisados  :<label for="revisado_monitor_'+conteo+'">'+conteos_monitor[0]+'</label><b></b> - NO Revisados :<label for="norevisado_monitor_'+conteo+'">'+conteos_monitor[1]+'</label><b></b> - Total  :<label for="total_monitor_'+conteo+'">'+conteos_monitor[2]+'</label> <b></b> </span></h4></div></div></div></div>';
        $("#conteo_principal").empty();
        $("#conteo_principal").html(contenedor);


    }else if (usuario["namerol"] == 'practicante_ps'){
        var conteos_practicante =realizar_conteo(usuario);
        contenedor = '<div class="row"><div class="col-sm-12"><h2>Información practicante - PERIODO :'+periodo+' </h2><div class="panel panel-default"> <div class="panel-body"><h4 class="panel-title"><span class="pull-left"> Revisados  :'+conteos_practicante[0]+' <b></b> - NO Revisados :'+conteos_practicante[1]+' <b></b> - Total  :'+conteos_practicante[2]+' <b></b> </span></h4></div></div></div></div>';
        $("#conteo_principal").empty();
        $("#conteo_principal").html(contenedor);

    }else if (usuario["namerol"] == 'profesional_ps'){
        var conteos_profesional =  realizar_conteo(usuario);
        contenedor = '<div class="row"><div class="col-sm-12"><h2>Información profesional - PERIODO :'+periodo+' </h2><div class="panel panel-default"> <div class="panel-body"><h4 class="panel-title"><span class="pull-left"> Revisados  :'+conteos_profesional[0]+' <b></b> - NO Revisados :'+conteos_profesional[1]+' <b></b> - Total  :'+conteos_profesional[2]+' <b></b> </span></h4></div></div></div></div>';
        $("#conteo_principal").empty();
        $("#conteo_principal").html(contenedor);

    }else if(usuario["namerol" ] == 'sistemas'){


    }
}
function realizar_conteo(usuario,dependiente="ninguno"){
    var conteos= [];

    var total_grupal_revisado = 0;
    var total_grupal_norevisado = 0;
    var total_monitor_revisado = 0;
    var total_monitor_norevisado = 0;

    if(usuario["namerol"] == 'monitor_ps'){
    var numero_pares=0;
    var numero_grupales=0;

    if (dependiente =="ninguno"){
    numero_pares = $('.panel-heading.pares').children().length;
    numero_grupales = $('.panel-heading.grupal').children().length;


    }else{
    numero_pares = $("#collapse"+usuario["id"]+" .panel-heading.pares").children().length;
    numero_grupales = $("#collapse"+usuario["id"]+" .panel-heading.grupal").children().length;
    }
    $("label[for='norevisado_grupal_"+usuario["id"]+"']").html(numero_grupales);
    $("label[for='total_grupal_"+usuario["id"]+"']").html(numero_grupales);


    for(var cantidad =0; cantidad<numero_pares;cantidad++){
       total_monitor_revisado += Number($("label[for='revisado_pares_"+ usuario["id"]+"_"+cantidad+"']").text());
       total_monitor_norevisado += Number($("label[for='norevisado_pares_"+ usuario["id"]+"_"+cantidad+"']").text());
    }

    for(var cantidad =0; cantidad<numero_grupales;cantidad++){
       total_grupal_revisado += 0;
       total_grupal_norevisado = numero_grupales;

    }
    total = (total_monitor_revisado+total_grupal_revisado) + (total_monitor_norevisado+total_grupal_norevisado);
    return new Array((total_monitor_revisado+total_grupal_revisado),(total_monitor_norevisado+total_grupal_norevisado), total);
    
    }else if (usuario["namerol"] == 'practicante_ps'){
      var numero_monitores=0;
      conteos =[0,0,0];  
      var conteos_monitor =[ ];

      if(dependiente =="ninguno"){
       numero_monitores = $('.panel-heading.practicante').children().length;
       for(var monitor = 0;monitor<numero_monitores;monitor++){

      var collapse_name =$( ".panel-heading.practicante:eq("+monitor+")" ).find('a').attr('href');
      var id_monitor = collapse_name.split("#collapse")[1];
      var usuario_monitor = [];

      usuario_monitor["id"] = id_monitor;
      usuario_monitor["namerol"] ="monitor_ps";
      conteos_monitor = realizar_conteo(usuario_monitor,"practicante");
      $("label[for='revisado_monitor_"+id_monitor+"']").html(conteos_monitor[0]);
      $("label[for='norevisado_monitor_"+id_monitor+"']").html(conteos_monitor[1]);
      $("label[for='total_monitor_"+id_monitor+"']").html(conteos_monitor[2]);


      conteos[0]+=conteos_monitor[0];
      conteos[1]+=conteos_monitor[1];
      conteos[2]+=conteos_monitor[2];

    }

    
      }else{
        numero_monitores = $("#collapse"+usuario["id"]+" .panel-heading.practicante").children().length;
      }
              for(var monitor = 0;monitor<numero_monitores;monitor++){

      var collapse_name =$( "#collapse"+usuario["id"]+" .panel-heading.practicante:eq("+monitor+")" ).find('a').attr('href');
      var id_monitor = collapse_name.split("#collapse")[1];
      var usuario_monitor = [];

      usuario_monitor["id"] = id_monitor;
      usuario_monitor["namerol"] ="monitor_ps";
      conteos_monitor = realizar_conteo(usuario_monitor,"practicante");
      $("label[for='revisado_monitor_"+id_monitor+"']").html(conteos_monitor[0]);
      $("label[for='norevisado_monitor_"+id_monitor+"']").html(conteos_monitor[1]);
      $("label[for='total_monitor_"+id_monitor+"']").html(conteos_monitor[2]);


      conteos[0]+=conteos_monitor[0];
      conteos[1]+=conteos_monitor[1];
      conteos[2]+=conteos_monitor[2];

    }



    return conteos;

    }else if(usuario["namerol"] =='profesional_ps'){
     conteos =[0,0,0];
     var numero_practicantes = $('.panel-heading.profesional').children().length;
     var conteos_practicantes = [];

     for(var practicante=0;practicante<numero_practicantes;practicante++){
      var collapse_name =$(".panel-heading.profesional:eq("+practicante+")" ).find('a').attr('href');
      var id_practicante = collapse_name.split("#collapse")[1];
      var usuario_practicante = [];
      usuario_practicante["id"] = id_practicante;
      usuario_practicante["namerol"] ="practicante_ps";
      conteos_practicantes =realizar_conteo(usuario_practicante,"practicante");
      $("label[for='revisado_practicante_"+id_practicante+"']").html(conteos_practicantes[0]);
      $("label[for='norevisado_practicante_"+id_practicante+"']").html(conteos_practicantes[1]);
      $("label[for='total_practicante_"+id_practicante+"']").html(conteos_practicantes[2]);
      conteos[0]+=conteos_practicantes[0];
      conteos[1]+=conteos_practicantes[1];
      conteos[2]+=conteos_practicantes[2];
     }
     return conteos;

    }




}

/*
 * Funcion para enviar correos.
 *
 */

function enviar_correo(instance){

                $('body').on('click', '.btn.btn-info.btn-lg.botonCorreo', function() {

                var id_registro = $(this).attr('value');
                var texto = $("#textarea_" + id_registro);
                if (texto.val() == "") {
                    swal({
                        title: "Para enviar una observación debe llenar el campo correspondiente",
                        html: true,
                        type: "error",
                        confirmButtonColor: "#d51b23"
                    });
                }else {
                    //se recupera el mensaje y el id del monitor al que se va a enviar el mensaje
                    var particionar_informacion = texto.attr('name').split("_");
                    //alert(particionar_informacion[4]);
                    var tipo = particionar_informacion[0];
                    var codigoN1 = particionar_informacion[1];
                    var codigoN2 = particionar_informacion[2];
                    var fecha = particionar_informacion[3];
                    var nombre = particionar_informacion[4];
                    var mensaje_enviar = texto.val();

                    //se limpia el textarea
                    texto.val("");
                    var respuesta = "";

                    //se llama el ajax para enviar el mensaje
                    $.ajax({
                        type: "POST",
                        data: {
                            type: "send_email_to_user",
                            tipoSeg: tipo,
                            codigoEnviarN1: codigoN1,
                            codigoEnviarN2: codigoN2,
                            fecha: fecha,
                            nombre: nombre,
                            message: mensaje_enviar
                        },
                        url: "../../../blocks/ases/managers/seguimiento_pilos/seguimientopilos_report.php",
                        async: false,
                        success: function(msg) {
                            //si el envio del mensaje fue exitoso
                            if (msg == 1) {
                                swal({
                                    title: "Correo enviado",
                                    html: true,
                                    type: "success",
                                    confirmButtonColor: "#d51b23"
                                });
                            }
                            else {
                                swal({
                                    title: "error al enviar el correo al monitor",
                                    html: true,
                                    type: "error",
                                    confirmButtonColor: "#d51b23"
                                });
                            }
                        },
                        dataType: "text",
                        cache: "false",
                        error: function(msg) {
                            swal({
                                title: "error al enviar el correo",
                                html: true,
                                type: "error",
                                confirmButtonColor: "#d51b23"
                            });
                        },
                    });
                }
            });
}



/*
 * Función para modificar un seguimiento determinado.
 *
 */
function modificar_seguimiento(){


        $('body').on('click', 'span.btn.btn-info.btn-lg.botonModificarSeguimiento', function() {
        var id = $(this).attr("value");
        var profesional = "",practicante = "";
        var combo_hora_inicio = document.getElementById("h_ini_" + id);
        var combo_hora_fin = document.getElementById("h_fin_" + id);
        var combo_min_inicio = document.getElementById("m_ini_" + id);
        var combo_min_fin = document.getElementById("m_fin_" + id);
        var hora_inicial = combo_hora_inicio.options[combo_hora_inicio.selectedIndex].text;
        var hora_final = combo_hora_fin.options[combo_hora_fin.selectedIndex].text;
        var min_inicial = combo_min_inicio.options[combo_min_inicio.selectedIndex].text;
        var min_final = combo_min_fin.options[combo_min_fin.selectedIndex].text;
        var validar = validarHoras(hora_inicial, hora_final, min_inicial, min_final);

        if (validar == "") {
            if ($("#profesional_" + id).is(':checked')) {
                  profesional = 1;
                }else{
                  profesional = 0;
                }

            if ($("#practicante_" + id).is(':checked')) {
                   practicante = 1;
                }else{
                   practicante = 0;
                }

        var $tbody = $(this).parent().parent().parent();
        var idSeguimientoActualizar = $(this).attr('value');
        var lugar = $tbody.find("#lugar_" + idSeguimientoActualizar).val();
        var tema = $tbody.find("#tema_" + id).val();
        var objetivos = $tbody.find("#objetivos_" + id).val();
        var fecha = $tbody.find("#fecha_" + id).val();
        var h_inicial = hora_inicial + ":" + min_inicial;
        var h_final = hora_final + ":" + min_final;
        var obindividual = $tbody.find("#obindividual_" + id).val();
        var riesgoIndividual = $("input[name='riesgo_individual_" + id + "']:checked").val();
        if (riesgoIndividual == undefined) {
               riesgoIndividual = "0";
            }

        var obfamiliar = $tbody.find("#obfamiliar_" + id).val();
        var riesgoFamiliar = $("input[name='riesgo_familiar_" + id + "']:checked").val();
        if (riesgoFamiliar == undefined) {
                riesgoFamiliar = "0";
            }

        var obacademico = $tbody.find("#obacademico_" + id).val();
        var riesgoAcademico = $("input[name='riesgo_academico_" + id + "']:checked").val();
        if (riesgoAcademico == undefined) {
                riesgoAcademico = "0";
            }

        var obeconomico = $tbody.find("#obeconomico_" + id).val();
        var riesgoEconomico = $("input[name='riesgo_economico_" + id + "']:checked").val();
        if (riesgoEconomico == undefined) {
                  riesgoEconomico = "0";
            }

        var obuniversitario = $tbody.find("#obuniversitario_" + id).val();
        var riesgoUniversitario = $("input[name='riesgo_universitario_" + id + "']:checked").val();
        if (riesgoUniversitario == undefined) {
                  riesgoUniversitario = "0";
          }

        var observacionesGeneral = $tbody.find("#observacionesGeneral_" + id).val();


        if (lugar == "" || tema == "" || objetivos == "") {
            swal({
                title: "Debe ingresar los datos completamente",
                html: true,
                type: "warning",
                confirmButtonColor: "#d51b23"
            });
             }else{
                  if (!((obindividual != "" && riesgoIndividual == 0) || (obfamiliar != "" && riesgoFamiliar == 0) || (obacademico != "" && riesgoAcademico == 0) || (obeconomico != "" && riesgoEconomico == 0) || (obuniversitario != "" && riesgoUniversitario == 0) || (obindividual == "" && riesgoIndividual > 0) || (obfamiliar == "" && riesgoFamiliar > 0) || (obacademico == "" && riesgoAcademico > 0) || (obeconomico == "" && riesgoEconomico > 0) || (obuniversitario == "" && riesgoUniversitario > 0))) {
                    var seguimiento =new Object();
                    seguimiento.id = idSeguimientoActualizar;
                    seguimiento.lugar = lugar;
                    seguimiento.tema = tema;
                    seguimiento.objetivos = objetivos;
                    seguimiento.individual = obindividual;
                    seguimiento.individual_riesgo= riesgoIndividual;
                    seguimiento.familiar_desc= obfamiliar;
                    seguimiento.familiar_riesgo = riesgoFamiliar;
                    seguimiento.academico = obacademico;
                    seguimiento.academico_riesgo = riesgoAcademico;
                    seguimiento.economico = obeconomico;
                    seguimiento.economico_riesgo = riesgoEconomico;
                    seguimiento.vida_uni = obuniversitario;
                    seguimiento.vida_uni_riesgo = riesgoUniversitario;
                    seguimiento.observaciones = observacionesGeneral;
                    seguimiento.revisado_practicante = practicante;
                    seguimiento.revisado_profesional = profesional;
                    seguimiento.fecha = fecha;
                    seguimiento.hora_ini = h_inicial;
                    seguimiento.hora_fin = h_final;
                    $.ajax({
                            type: "POST",
                             data: {
                                seguimiento:seguimiento,
                                type: "actualizar_registro",
                            },
                             url: "../../../blocks/ases/managers/seguimiento_pilos/seguimientopilos_report.php",
                             async: false,
                                success: function(msg) {
                                    if (msg == "0") {
                                        swal({
                                            title: "error al actualizar registro",
                                            html: true,
                                            type: "error",
                                            confirmButtonColor: "#d51b23"
                                        });
                                    }
                                    else if (msg == "1") {
                                        swal("¡Hecho!", "El registro ha sido actualizado",
                                            "success");
                                    }
                                    else {
                                        swal({
                                            title: "Debe ingresar correctamente los riesgos",
                                            html: true,
                                            type: "warning",
                                            confirmButtonColor: "#d51b23"
                                        });
                                    }
                                },
                                error: function(msg) {},
                            });
                        }
                        else {
                            swal({
                                title: "Debe ingresar correctamente los riesgos",
                                html: true,
                                type: "warning",
                                confirmButtonColor: "#d51b23"
                            });
                        }

                    }
                }
                else {
                    swal({
                        title: validar,
                        html: true,
                        type: "warning",
                        confirmButtonColor: "#d51b23"
                    });

                }
            });
}



 /*
 * Función para editar un seguimiento determinado dado los roles existentes.
 *
 */
function editar_seguimiento(namerol){

    $('body').on('click', 'span.btn-info.btn-lg.botonesSeguimiento.botonEditarSeguimiento', function() {
        var id = $(this).attr("value");
        var $tbody = $(this).parent().parent().parent();

        var visto_profesional = false;

        if(namerol == 'monitor_ps'){
         visto_profesional = $("#profesional_" + id).is(':checked');

        }else if (namerol == 'practicante_ps'){
          $("input[name=practicante]").attr('disabled', false);

        }else if (namerol =='profesional_ps'){
          $("input[name=profesional]").attr('disabled', false);

        }


        if(visto_profesional == false){

        $tbody.find('.editable').removeAttr('readonly');
        $tbody.find('.botonesSeguimiento').toggleClass('ocultar');
        $tbody.find('.quitar-ocultar').toggleClass('ocultar');
        $tbody.find('.radio-ocultar').toggleClass('ocultar');
        auxiliar_editar(id);
        seleccionarButtons(id);

        }else{
            swal("¡Advertencia!",
                "No es posible editar el seguimiento, debido a que ya ha sido revisado por un profesional",
                "warning");}
        });

    }


/*
 * Función para borrar un seguimiento determinado dado los roles existentes.
 *
 */
function borrar_seguimiento(namerol){

    $('body').on('click', 'span.btn.btn-info.btn-lg.botonBorrar', function() {
    var id_registro = $(this).attr('value');
    var visto_profesional = false;

    if(namerol == 'monitor_ps'){
      visto_profesional = $("#profesional_" + id).is(':checked');

    }else if (namerol == 'practicante_ps'){

    
    }else if (namerol =='profesional_ps'){
    
    } 
    if (visto_profesional  == false){
        swal({
             title: "¿Seguro que desea eliminar el registro?",
             text: "No podrás deshacer este paso",
             type: "warning",
             showCancelButton: true,
             cancelButtonText: "No",
             confirmButtonColor: "#d51b23",
             confirmButtonText: "Si",
             closeOnConfirm: false
             },
        function() {
        $.ajax({
            type: "POST",
            data: {
                    id: id_registro,
                    type: "eliminar_registro",
                   },
            url: "../../../blocks/ases/managers/seguimiento_pilos/seguimientopilos_report.php",
            async: false,
            success: function(msg) {
                swal({
                     title: msg.title,
                     html: true,
                     text: msg.msg,
                     type: msg.type,
                     confirmButtonColor: "#d51b23"
                     });
                     setTimeout('document.location.reload()', 500);
                    },
            dataType: 'json',
            cache: "false",
            error: function(msg) {},
            });
        });
      }

    });
}


 /*
 * Función para cancelar la edición de un seguimiento determinado dado cualquiera de los roles existentes.
 *
 */
function cancelar_edicion(namerol){

        $('body').on('click', 'span.btn.btn-info.btn-lg.botonesSeguimiento.botonCancelarSeguimiento', function() {
            var id = $(this).attr("value");

        if(namerol == 'monitor_ps'){

        }else if (namerol == 'practicante_ps'){
          $("input[name=practicante]").attr('disabled', true);

        }else if (namerol =='profesional_ps'){
          $("input[name=profesional]").attr('disabled', true);

        }
             var $tbody = $(this).parent().parent().parent();
             $tbody.find('.editable').attr('readonly', true);
             $tbody.find('.botonesSeguimiento').toggleClass('ocultar');
             $tbody.find('.quitar-ocultar').toggleClass('ocultar');
             $tbody.find('.radio-ocultar').toggleClass('ocultar');
             auxiliar_cancelar(id);
         });
}

/*
 * Función para limpiar la descripción de los riesgos y los radiobuttons seleccionados.
 *
 */
    function limpiar_riesgos(){

    $('body').on('click', '.limpiar', function() {
                var elemento = $(this).closest("div").attr('id').split("_");
                var id = elemento[2].split("div").pop();
                switch (elemento[1]) {
                    case 'individual':
                        $("#obindividual_" + id).val("");
                        auxiliar_limpiar("#riesgo_individual_", id);
                        break;

                    case 'familiar':
                        $("#obfamiliar_" + id).val("");
                        auxiliar_limpiar("#riesgo_familiar_", id);
                        break;

                    case 'academico':
                        $("#obacademico_" + id).val("");
                        auxiliar_limpiar("#riesgo_academico_", id);
                        break;

                    case 'economico':
                        $("#obeconomico_" + id).val("");
                        auxiliar_limpiar("#riesgo_economico_", id);
                        break;

                    case 'universitario':
                        $("#obuniversitario_" + id).val("");
                        auxiliar_limpiar("#riesgo_universitario_", id);
                        break;

                    default:
                        alert("Dato invalido");
                        break;
                }
            });
    }


//--------FUNCIONES AUXILIARES.



function consultar_periodos(instance,namerol){
            $("#periodos").change(function() {
            var periodo_escogido = $( "#periodos" ).val();
              $.ajax({
                 type: "POST",
                 data: {
                    id: periodo_escogido,
                    instance: instance,
                    type: "actualizar_personas"
                    },
                    url: "../../../blocks/ases/managers/seguimiento_pilos/seguimientopilos_report.php",
                    async: false,
                    success: function(msg) {


                    $('#personas').empty();
                    $("#personas").select2({  
                      placeholder: "Seleccionar persona",
                      language: {
                       noResults: function() {
                       return "No hay resultado";        
                     },
                       searching: function() {
                       return "Buscando..";
                   }
                }
            });       
                    if(namerol =='sistemas'){
                    var inicio = '<option value="">Seleccionar persona</option>';

                     $("#personas").attr('selectedIndex', '-1').find("option:selected").removeAttr("selected");
                     $('#personas').append(inicio+msg);
                    
                    }

                    },
                    dataType: "text",
                    cache: "false",
                    error: function(msg) { alert("error al cargar personas");},
                        });
                    });

}


//Verifica si el profesional desea marcar como revisado el seguimiento.

function verificar_profesional(){
  $('input[name="profesional"]').click(function() {
      if ($(this).is(':checked')) {
        swal({
            title: "¿Seguro que desea cambiar estado a revisado?",
            text: "En caso de modificar el seguimiento no podrá volverlo a editar",
            type: "warning",
            showCancelButton: true,
            cancelButtonText: "No",
            confirmButtonColor: "#d51b23",
            confirmButtonText: "Si",
            closeOnConfirm: true
            },
        function(isConfirm) {
            if (isConfirm == false) {
             $('input[name="profesional"]').prop('checked', false);
                 }
              });
           }
    });
  }



/*
*  Función que obtiene los mensajes de validación de la hora.
*/
    function validarHoras(h_ini, h_fin, m_ini, m_fin) {
        var detalle = "";
        if (h_ini > h_fin) {
            detalle += "* La hora final debe ser mayor a la inicial<br>";
        }else if (h_ini == h_fin) {
         if (m_ini > m_fin) {
            isvalid = false;
            detalle += "* La hora final debe ser mayor a la inicial<br>";
        }else {
         if (m_ini == m_fin) {
            detalle += "* Las horas seleccionadas deben ser diferentes<br>";
          }
        }
      }
    return detalle;
    }


/*
* Función usada para inicializar los selects de las horas/minutos finales e iniciales de cada seguimiento.
*/
    function initFormSeg(id) {
        var date = new Date();
        var minutes = date.getMinutes();
        var hour = date.getHours();
        //incializar hora
        var hora = "";
        for (var i = 0; i < 24; i++) {
            if (i == hour) {
                if (hour < 10) hour = "0" + hour;
                hora += "<option value=\"" + hour + "\" selected>" + hour + "</option>";
            }
            else if (i < 10) {
                hora += "<option value=\"0" + i + "\">0" + i + "</option>";
            }
            else {
                hora += "<option value=\"" + i + "\">" + i + "</option>";
            }
        }
        var min = "";
        for (var i = 0; i < 60; i++) {

            if (i == minutes) {
                if (minutes < 10) minutes = "0" + minutes;
                min += "<option value=\"" + minutes + "\" selected>" + minutes + "</option>";
            }
            else if (i < 10) {
                min += "<option value=\"0" + i + "\">0" + i + "</option>";
            }
            else {
                min += "<option value=\"" + i + "\">" + i + "</option>";
            }
        }
        $('#h_ini_' + id).append(hora);
        $('#m_ini_' + id).append(min);
        $('#h_fin_' + id).append(hora);
        $('#m_fin_' + id).append(min);
        $('#seguimiento #m_fin').append(min);
    }


   /*
   * Función usada para cambiar color cuando se cambie el radiobutton de riesgo.
   */

   function actualizar_riesgo(){
$(document).ready(function() {

        $('input:radio').change(function() {
        var id =$(this).parent().parent().parent().attr('id');
        var tipo_riesgo = $(this).attr('value');
        
        if(tipo_riesgo == 1){
           $("#" + id).removeClass();
           $("#"+id).addClass('table-info-pilos col-sm-12 riesgo_'+'bajo');
        }else if(tipo_riesgo == 2){
           $("#" + id).removeClass();
           $("#"+id).addClass('table-info-pilos col-sm-12 riesgo_'+'medio');
        }else if(tipo_riesgo == 3){
           $("#" + id).removeClass();
           $("#"+id).addClass('table-info-pilos col-sm-12 riesgo_'+'alto');
           }
        });});
   }


    //Oculta y muestra botones al presionar cancelar.
    function auxiliar_cancelar(id) {
        $("#titulo_fecha_" + id).hide();
        $("#borrar_" + id).show();
        $("#editar_" + id).show();
        $("#enviar_" + id).show();
        $("#hora_final_" + id).show();
        $("#mod_hora_final_" + id).hide();
        $("#hora_inicial_" + id).show();
        $("#mod_hora_ini_" + id).hide();
    }

    //Oculta y muestra botones al presionar editar, organiza fecha y horas.
    function auxiliar_editar(id) {
        $("#borrar_" + id).hide();
        $("#editar_" + id).hide();
        $("#enviar_" + id).hide();
        $("#hora_final_" + id).hide();
        $("#hora_inicial_" + id).hide();
        $("#titulo_fecha_" + id).show();
        $("#mod_hora_final_" + id).show();
        $("#mod_hora_ini_" + id).show();

        var f1 = $("#h_inicial_texto_" + id).val();
        var f2 = $("#h_final_texto_" + id).val();
        var array_f1 = f1.split(":");
        var array_f2 = f2.split(":");
        initFormSeg(id);
        //Seleccionamos la hora deacuerdo al sistema

        $("#h_ini_" + id + " option[value=" + array_f1[0] + "]").attr("selected", true);
        $("#m_ini_" + id + " option[value=" + array_f1[1] + "]").attr("selected", true);
        $("#h_fin_" + id + " option[value=" + array_f2[0] + "]").attr("selected", true);
        $("#m_fin_" + id + " option[value=" + array_f2[1] + "]").attr("selected", true);
    }


    //Limpia los campos de riesgos y deschequea su prioridad.
    function auxiliar_limpiar(texto, id) {
        $(texto + id).removeClass("riesgo_bajo");
        $(texto + id).removeClass("riesgo_medio");
        $(texto + id).removeClass("riesgo_alto");
        var text = '"' + texto.replace("#", "") + id + '"';
        $('input:radio[name=' + text + ']').each(function(i) {
            this.checked = false;
        });

    }


    //En el caso de que el check esté revisado por un profesional 
    //quita los botones de editar,borrar y observaciones.
    function revisado_profesional(id) {
        if ($("#profesional_" + id).is(':checked')) {
            $("#borrar_" + id).hide();
            $("#editar_" + id).hide();
            $("#enviar_" + id).hide();
        }
    }

    //Selecciona los radiobuttons correspondientes con la prioridad del riesgo.
    function seleccionarButtons(id_seguimiento) {


        //Riesgo individual
        if ($("#riesgo_individual_" + id_seguimiento).is('.riesgo_bajo')) {
        $("input[name=riesgo_individual_" + id_seguimiento + "][value=1]").attr('checked', 'checked');


        }
        else if ($("#riesgo_individual_" + id_seguimiento).is('.riesgo_medio')) {
            $("input[name=riesgo_individual_" + id_seguimiento + "][value=2]").attr('checked', 'checked');
        }
        else if ($("#riesgo_individual_" + id_seguimiento).is('.riesgo_alto')) {
            $("input[name=riesgo_individual_" + id_seguimiento + "][value=3]").attr('checked', 'checked');
        }
        else {
            $("input[name=riesgo_individual_" + id_seguimiento + "][value=0]").attr('checked', 'checked');

        }

        //Riesgo familiar
        if ($("#riesgo_familiar_" + id_seguimiento).is('.riesgo_bajo')) {
            $("input[name=riesgo_familiar_" + id_seguimiento + "][value=1]").attr('checked', 'checked');
        }
        else if ($("#riesgo_familiar_" + id_seguimiento).is('.riesgo_medio')) {
            $("input[name=riesgo_familiar_" + id_seguimiento + "][value=2]").attr('checked', 'checked');
        }
        else if ($("#riesgo_familiar_" + id_seguimiento).is('.riesgo_alto')) {
            $("input[name=riesgo_familiar_" + id_seguimiento + "][value=3]").attr('checked', 'checked');
        }
        else {
            $("input[name=riesgo_familiar_" + id_seguimiento + "][value=0]").attr('checked', 'checked');

        }

        //Riesgo academico
        if ($("#riesgo_academico_" + id_seguimiento).is('.riesgo_bajo')) {
            $("input[name=riesgo_academico_" + id_seguimiento + "][value=1]").attr('checked', 'checked');
        }
        else if ($("#riesgo_academico_" + id_seguimiento).is('.riesgo_medio')) {
            $("input[name=riesgo_academico_" + id_seguimiento + "][value=2]").attr('checked', 'checked');
        }
        else if ($("#riesgo_academico_" + id_seguimiento).is('.riesgo_alto')) {
            $("input[name=riesgo_academico_" + id_seguimiento + "][value=3]").attr('checked', 'checked');
        }
        else {
            $("input[name=riesgo_academico_" + id_seguimiento + "][value=0]").attr('checked', 'checked');

        }

        //Riesgo economico
        if ($("#riesgo_economico_" + id_seguimiento).is('.riesgo_bajo')) {
            $("input[name=riesgo_economico_" + id_seguimiento + "][value=1]").attr('checked', 'checked');
        }
        else if ($("#riesgo_economico_" + id_seguimiento).is('.riesgo_medio')) {
            $("input[name=riesgo_economico_" + id_seguimiento + "][value=2]").attr('checked', 'checked');
        }
        else if ($("#riesgo_economico_" + id_seguimiento).is('.riesgo_alto')) {
            $("input[name=riesgo_economico_" + id_seguimiento + "][value=3]").attr('checked', 'checked');
        }
        else {
            $("input[name=riesgo_economico_" + id_seguimiento + "][value=0]").attr('checked', 'checked');

        }

        //Riesgo universitario
        if ($("#riesgo_universitario_" + id_seguimiento).is('.riesgo_bajo')) {
            $("input[name=riesgo_universitario_" + id_seguimiento + "][value=1]").attr('checked', 'checked');
        }
        else if ($("#riesgo_universitario_" + id_seguimiento).is('.riesgo_medio')) {
            $("input[name=riesgo_universitario_" + id_seguimiento + "][value=2]").attr('checked', 'checked');
        }
        else if ($("#riesgo_universitario_" + id_seguimiento).is('.riesgo_alto')) {
            $("input[name=riesgo_universitario_" + id_seguimiento + "][value=3]").attr('checked', 'checked');
        }
        else {
            $("input[name=riesgo_universitario_" + id_seguimiento + "][value=0]").attr('checked', 'checked');

        }

    }
});
>>>>>>> db_management
