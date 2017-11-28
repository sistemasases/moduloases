<?php

require_once(dirname(__FILE__).'/../../../../config.php');
require_once(dirname(__FILE__).'/../instance_management/instance_lib.php');
require_once(dirname(__FILE__).'/../lib/lib.php');

/**
 * Función que recupera riesgos 
 *
 * @see getRiesgo()
 * @return Array Riesgos
 */
function get_riesgos(){

    global $DB;

    $sql_query = "SELECT * FROM {talentospilos_riesgos_ases}";
    $result = $DB->get_records_sql($sql_query);
    return $result;
}

/**
 * Función que recupera cohortes
 *
 * @see getCohorte()
 * @return Array Cohortes
 */
function get_cohortes(){

    global $DB;

    $sql_query = "SELECT * FROM {cohort}";
    $result = $DB->get_records_sql($sql_query);
    return $result;
}


/**
 * Funcion recupera la informacion necesaria para la grafica de sexo de acuerdo al cohorte seleccionado
 * 
 * @param $cohorte
 * @return Array 
 */
function getGraficSex($cohorte){
    global $DB;
    $query = "SELECT subconsulta.sexo, COUNT(subconsulta.userid)
        FROM (SELECT data.userid, usuarios_tal.sexo
              FROM {talentospilos_usuario} as usuarios_tal 
                         INNER JOIN {user_info_data} as data ON (cast(usuarios_tal.id AS varchar) = data.data) 
              WHERE data.fieldid = 2) as subconsulta 
              INNER JOIN {cohort_members} as cohorts ON subconsulta.userid = cohorts.userid
              INNER JOIN {cohort} as cohorte ON cohorts.cohortid = cohorte.id
        WHERE cohorte.name = '$cohorte'
        GROUP BY sexo";
    
    $sql_query = "SELECT  sexo, COUNT(id) FROM {talentospilos_usuario} GROUP BY sexo";
    
    //se verifica el cohorte ingresado, de acuerdo al caso se invoca el metodo de moodle con una de las dos
    //consultar armadas anteriormente
    if($cohorte == "TODOS"){
        $result = $DB->get_records_sql($sql_query);
    } else {
        $result = $DB->get_records_sql($query);
    }
    return $result;
}


/**
 * Funcion recupera la informacion necesaria para la grafica de edad de acuerdo al cohorte seleccionado
 * 
 * @param $cohorte
 * @return Array 
 */
function getGraficAge($cohorte){
    global $DB;
    $arrayRetornar=array();
    
    $query = "
        SELECT subconsulta.userid, (now() - subconsulta.fecha_nac)/365 AS age
            FROM (SELECT data.userid, usuarios_tal.fecha_nac
                  FROM {talentospilos_usuario} as usuarios_tal 
                             INNER JOIN {user_info_data} as data ON (cast(usuarios_tal.id AS varchar) = data.data) 
                  WHERE data.fieldid = 2) as subconsulta 
                  INNER JOIN {cohort_members} as cohorts ON subconsulta.userid = cohorts.userid
                  INNER JOIN {cohort} as cohorte ON cohorts.cohortid = cohorte.id
            WHERE cohorte.name = '$cohorte'";
    
    $sql_query = "SELECT id,(now() - fecha_nac)/365 AS age FROM {talentospilos_usuario}";
    
    //se verifica el cohorte ingresado, de acuerdo al caso se invoca el metodo de moodle con una de las dos
    //consultar armadas anteriormente
    if($cohorte == "TODOS"){
        $result = $DB->get_records_sql($sql_query);
    } else {
        $result = $DB->get_records_sql($query);
    }
    
    //ya que el dato recibido es un dato calculado se sabe que la edad son los dos primeros digitos de dicho campo
    //razon por la cual se accede a cada valor, se extraen los primeros valores y se retorna el arreglo
    foreach($result as $datoEdad)
     {
         $años=substr($datoEdad->age,0,2);
         
         array_push($arrayRetornar,$años);
     }
    
    return array_count_values($arrayRetornar);
    

}

/**
 * Funcion recupera la informacion necesaria para la grafica de programas de acuerdo al cohorte seleccionado
 * 
 * @param $cohorte
 * @return Array 
 */
function getGraficPrograma($cohorte){
    global $DB;
    
    $sql_query = "SELECT programa.nombre,COUNT(programa.nombre)
                  FROM (SELECT DISTINCT data.userid AS userid,data.data AS codcarrera FROM {talentospilos_usuario} AS usuarios_talentos 
                        INNER JOIN {user_info_data} AS data ON (CAST (usuarios_talentos.id AS varchar) = data.data) WHERE data.fieldid=3)
                  AS sub INNER JOIN {talentospilos_programa} AS programa ON (cast(programa.id as text) = sub.codcarrera) 
                  GROUP BY programa.nombre";
    
    
    // consulta con la parte de los cohortes
    $query = "SELECT programa.nombre,COUNT(programa.nombre)
                  FROM (SELECT DISTINCT data.userid AS userid,data.data AS codcarrera,miembros.cohortid,cohort.name FROM {talentospilos_usuario} AS usuarios_talentos           
                  INNER JOIN {user_info_data} AS data ON (CAST (usuarios_talentos.id AS varchar) = data.data) INNER JOIN {cohort_members} AS miembros ON
                  (data.userid=miembros.userid) INNER JOIN {cohort} AS cohort ON (cohort.id=miembros.cohortid) WHERE data.fieldid = 3 AND cohort.name='$cohorte')
                  AS sub INNER JOIN {talentospilos_programa} AS programa ON (cast(programa.id as text) = sub.codcarrera) 
                  GROUP BY programa.nombre;";

    //se verifica el cohorte ingresado, de acuerdo al caso se invoca el metodo de moodle con una de las dos
    //consultar armadas anteriormente
    if($cohorte == "TODOS"){
        $result = $DB->get_records_sql($sql_query);
    } else {
        $result = $DB->get_records_sql($query);
    }
     //print_r($result);
    return $result;
}


/**
 * Funcion recupera la informacion necesaria para la grafica de facultad de acuerdo al cohorte seleccionado
 * 
 * @param $cohorte
 * @return Array 
 */
function getGraficFacultad($cohorte){
    global $DB;
    
    $sql_query = "SELECT facultad.nombre,COUNT(facultad.nombre) FROM 
                    (SELECT nombre,programa.id_facultad FROM 
                        (SELECT DISTINCT  data.userid AS userid,data.data AS codcarrera FROM {talentospilos_usuario} AS usuarios_talentos 
                         INNER JOIN {user_info_data} AS data ON (cast (usuarios_talentos.id as varchar) = data.data) where data.fieldid=3) 
                     AS sub INNER JOIN {talentospilos_programa} AS programa ON (CAST(programa.id AS text) = sub.codcarrera)) 
                  AS subconsulta INNER JOIN {talentospilos_facultad} AS facultad ON (subconsulta.id_facultad=facultad.id) 
                  GROUP BY facultad.nombre";  


    // consulta con la parte de los cohortes
    $query = "SELECT facultad.nombre,COUNT(facultad.nombre) FROM 
                    (SELECT nombre,programa.id_facultad FROM 
                        (SELECT DISTINCT data.userid AS userid,data.data AS codcarrera,miembros.cohortid,cohort.name FROM {talentospilos_usuario} AS usuarios_talentos           
                  INNER JOIN {user_info_data} AS data ON (CAST (usuarios_talentos.id AS varchar) = data.data) INNER JOIN {cohort_members} AS miembros ON
                  (data.userid=miembros.userid) INNER JOIN {cohort} AS cohort ON (cohort.id=miembros.cohortid) WHERE data.fieldid = 3 AND cohort.name='$cohorte') 
                     AS sub INNER JOIN {talentospilos_programa} AS programa ON (CAST(programa.id AS text) = sub.codcarrera)) 
                  AS subconsulta INNER JOIN {talentospilos_facultad} AS facultad ON (subconsulta.id_facultad=facultad.id) 
                  GROUP BY facultad.nombre";

    //se verifica el cohorte ingresado, de acuerdo al caso se invoca el metodo de moodle con una de las dos
    //consultar armadas anteriormente
    if($cohorte == "TODOS"){
        $result = $DB->get_records_sql($sql_query);
    } else {
        $result = $DB->get_records_sql($query);
    }
    //print_r($result);   
    return $result;
    
}


/**
 * Funcion recupera la informacion necesaria para la grafica de estado de acuerdo al cohorte seleccionado
 * 
 * @param $cohorte
 * @return Array 
 */
function getGraficEstado($cohorte){
    global $DB;
    
    $sql_query = "SELECT data, COUNT(userid)
        FROM(SELECT subconsulta.userid, dato.data 
             FROM    (SELECT DISTINCT data.userid AS userid
                    FROM {talentospilos_usuario} AS usuarios_tal 
                         INNER JOIN {user_info_data} AS data ON (cast(usuarios_tal.id AS varchar) = data.data) 
                    WHERE data.fieldid = 2) AS subconsulta 
                    INNER JOIN {user_info_data} AS dato ON (subconsulta.userid = dato.userid)
             WHERE dato.fieldid = 4 ) AS cont
        GROUP BY data";
        
    //consulta con la parte de los cohortes
    $query = "
        SELECT data, COUNT(userid)
        FROM(SELECT subconsulta.userid, dato.data 
             FROM    (SELECT DISTINCT data.userid AS userid,data.data AS codcarrera,miembros.cohortid,cohort.name 
                      FROM {talentospilos_usuario} AS usuarios_tal INNER JOIN {user_info_data} AS data ON
                      (cast(usuarios_tal.id AS varchar) = data.data) INNER JOIN {cohort_members} AS miembros ON 
                      (data.userid=miembros.userid) INNER JOIN {cohort} AS cohort ON (cohort.id=miembros.cohortid) 
                        WHERE data.fieldid = 2 AND cohort.name='$cohorte') AS subconsulta 
                    INNER JOIN {user_info_data} AS dato ON (subconsulta.userid = dato.userid)
             WHERE dato.fieldid = 4 ) AS cont
        GROUP BY data";
    
    //se verifica el cohorte ingresado, de acuerdo al caso se invoca el metodo de moodle con una de las dos
    //consultar armadas anteriormente
    if($cohorte == "TODOS"){
        $result = $DB->get_records_sql($sql_query);
    } else {
        $result = $DB->get_records_sql($query);
    }
    return $result;
    
}

/**
 * Función que recupera datos para la tabla de ases_report, dado el estado, la cohorte y un conjunto de campos a extraer.
 *
 * @see getUsersByPopulation()
 * @param $column       --> Campos a seleccionar
 * @param $population   --> Estado y cohorte
 * @param $risk         --> Nivel de riesgo a mostrar
 * @param $academic_fields --> Campos relacionados con el programa académico y facultad
 * @param $idinstancia  --> Instancia del módulo
 * @return Array 
 */

function getUsersByPopulation($column, $population, $risk, $academic_fields=null, $idinstancia){
    global $DB;
    global $USER;
    //consulta
    $sql_query = "";
    //cohorte
    $ch = $population[0];
    //estado
    $state = $population[1];
    
    //informacion de la instancia
    $infoinstancia= consult_instance($idinstancia);
    
    $asescohorts = "";
    if($infoinstancia->cod_univalle == 1008){
        $asescohorts = "OR pc.idnumber LIKE 'SP%'";
    }

    //se formatean las columnas
    $chk = array("Código","Nombre","Apellidos", "Documento", "Dirección", "Nombre acudiente", "Celular acudiente", "Grupo", "Estado", "Email","Celular");
    $name_chk_db = array("username", "firstname", "lastname", "num_doc","direccion_res","acudiente", "tel_acudiente","grupo","estado","email","celular");
    
    //se eliminan las columnas con valores nulos: en caso de que el checkbox de grupo esté deshabilitado
    $column = array_filter($column, function($var){return !is_null($var);} );
    
    $columns_str= "";
    for($i = 0; $i < count($column); $i++){
        if (in_array($column[$i],$chk)){
            $column[$i] = $name_chk_db [array_search($column[$i],$chk)];
        } 
        $columns_str = $columns_str.$column[$i].",";
    }
    //print_r($columns_str);
    $columns_str = trim($columns_str,",");
    //se formatea la consulta
    
    /**
     * Se crearan subconsultas para cada riesgo seleccionado en el array de risk
     * de la siguiente forma:
     * (SELECT calificación_riesgo FROM {riesg_usuario} WHERE id_usuario = id and id_riesgo = $id_riesgo) as $nombre_riesgo
     */ 
    //se eliminan las columnas con valores nulos: en caso de que el checkbox de grupo esté deshabilitado
    //Cada valor de este array representa el id de cada riesgo seleccionado
    
    $column_risk = array_filter($risk, function($var){return !is_null($var);} );
    $column_risk_str ="";
    $column_risk_nombres = array();
    //Se busca el nombre del riesgo en la tabla y despues se crea la consulta
    foreach($column_risk as $id_riesgo){
        $query_nombre = "SELECT * FROM {talentospilos_riesgos_ases} WHERE id =".$id_riesgo;
        $nombre_riesgo = $DB->get_record_sql($query_nombre)->nombre;
        array_push($column_risk_nombres, $nombre_riesgo);
        
        //calificacion_riesgo
        $column_risk_str = $column_risk_str.", (SELECT calificacion_riesgo FROM {talentospilos_riesg_usuario} WHERE ";
        $column_risk_str = $column_risk_str."id_usuario = pcmuser.data AND id_riesgo = ".$id_riesgo.") AS ".$nombre_riesgo;
    }
    $columns_str = $columns_str.$column_risk_str;

    
    if(!isMonOrPract($USER)){


        if($state != "TODOS"){
            $query_status = "SELECT umood.id
                        FROM {user} umood INNER JOIN {user_info_data} udata ON umood.id = udata.userid 
                        INNER JOIN {talentospilos_est_estadoases} estado_ases ON udata.data = CAST(estado_ases.id_estudiante as TEXT)
                        WHERE id_estado_ases = $state AND udata.fieldid = (SELECT id FROM  {user_info_field} as f WHERE f.shortname ='idtalentos') 
                              AND estado_ases.fecha = (SELECT MAX(fecha) FROM {talentospilos_est_estadoases} WHERE id_estudiante = estado_ases.id_estudiante)";
        }else{
            $query_status = "SELECT umood.id
                        FROM {user} umood INNER JOIN {user_info_data} udata ON umood.id = udata.userid 
                        INNER JOIN {talentospilos_est_estadoases} estado_ases ON udata.data = CAST(estado_ases.id_estudiante as TEXT)
                        WHERE udata.fieldid = (SELECT id FROM  {user_info_field} as f WHERE f.shortname ='idtalentos')
                         AND estado_ases.fecha = (SELECT MAX(fecha) FROM {talentospilos_est_estadoases} WHERE id_estudiante = estado_ases.id_estudiante)";
        }
        
        
        if($ch == "TODOS"){
            $sql_query = "SELECT ".$columns_str." FROM {cohort} AS pc 
                INNER JOIN (
                    SELECT * FROM {cohort_members} AS pcm 
                    INNER JOIN (
                        SELECT * FROM (
                            SELECT id AS id_1, * FROM (SELECT *, id AS id_user FROM {user}) AS userm 
                            INNER JOIN (
                                SELECT userid, CAST(d.data as int) as data 
                                FROM {user_info_data} d 
                                WHERE d.data <> '' 
                                AND fieldid = (SELECT id FROM  {user_info_field} as f WHERE f.shortname ='idtalentos')
                            ) AS field 
                            ON userm. id_user = field.userid ) AS usermoodle 
                        INNER JOIN {talentospilos_usuario} as usuario 
                        ON usermoodle.data = usuario.id 
                        WHERE usermoodle.id_user in (
                            $query_status )
                        ) as usertm 
                    ON usertm.id_user = pcm.userid) as pcmuser 
                ON pc.id = pcmuser.cohortid WHERE pc.idnumber like '".$infoinstancia->cod_univalle."%' ".$asescohorts.";";
                
            // $sql_query = "SELECT ".$columns_str." FROM {cohort} AS pc INNER JOIN (SELECT * FROM {cohort_members} AS pcm INNER JOIN (SELECT * FROM (SELECT * FROM (SELECT *, id AS id_user FROM {user}) AS userm INNER JOIN (SELECT * FROM {user_info_data} as d INNER JOIN {user_info_field} as f ON d.fieldid = f.id WHERE f.shortname ='idtalentos' ) AS field ON userm. id_user = field.userid ) AS usermoodle INNER JOIN {talentospilos_usuario} as usuario ON CAST( usermoodle.data AS INT) = usuario.id WHERE usermoodle.id_user in (SELECT userid FROM {user_info_data} as d INNER JOIN {user_info_field} as f ON d.fieldid = f.id WHERE f.shortname ='estado' AND d.data ='ACTIVO')) as usertm ON usertm.id_user = pcm.userid) as pcmuser on pc.id = pcmuser.cohortid WHERE pc.idnumber like '%SP%';";
            
        }
        else {
            $sql_query = "
                SELECT ".$columns_str." 
                FROM {cohort} AS pc INNER JOIN (
                    SELECT * FROM {cohort_members} 
                    AS pcm 
                    INNER JOIN (
                        SELECT * FROM (
                            SELECT id AS id_1, * FROM (SELECT *, id AS id_user FROM {user}) AS userm 
                            INNER JOIN (SELECT userid, CAST(d.data as int) AS data 
                            FROM {user_info_data} d 
                            WHERE d.data <> '' and fieldid = (
                                SELECT id 
                                FROM  {user_info_field} as f 
                                WHERE f.shortname ='idtalentos')) AS field 
                            ON CAST(userm.id_user AS TEXT) = CAST(field.userid AS TEXT)) 
                        AS usermoodle 
                        INNER JOIN (
                            SELECT id as idtalentos, * 
                            FROM {talentospilos_usuario}) 
                        AS usuario ON usermoodle.data = usuario.id 
                        WHERE usermoodle.id_user in (
                            $query_status )
                        ) AS usertm 
                    ON usertm.id_user = pcm.userid) as pcmuser on pc.id = pcmuser.cohortid ";
                    
        	$whereclause = "WHERE pc.idnumber ='".$ch."';";

            $sql_query.=  $whereclause;
            
        }
    }else{

        //SE EVALUAN LOS ESTUDIANTES ASIGNADOS AL USUARIO

        $query_user = getQueryUser($USER);

        if($state != "TODOS"){
            $query_status = "SELECT umood.id
                        FROM {user} umood INNER JOIN {user_info_data} udata ON umood.id = udata.userid 
                        INNER JOIN {talentospilos_est_estadoases} estado_ases ON udata.data = CAST(estado_ases.id_estudiante as TEXT)
                        WHERE id_estado_ases = $state AND udata.fieldid = (SELECT id FROM  {user_info_field} as f WHERE f.shortname ='idtalentos') 
                              AND estado_ases.fecha = (SELECT MAX(fecha) FROM {talentospilos_est_estadoases} WHERE id_estudiante = estado_ases.id_estudiante)";
        }else{
            $query_status = "SELECT umood.id
                        FROM {user} umood INNER JOIN {user_info_data} udata ON umood.id = udata.userid 
                        INNER JOIN {talentospilos_est_estadoases} estado_ases ON udata.data = CAST(estado_ases.id_estudiante as TEXT)
                        WHERE udata.fieldid = (SELECT id FROM  {user_info_field} as f WHERE f.shortname ='idtalentos')
                         AND estado_ases.fecha = (SELECT MAX(fecha) FROM {talentospilos_est_estadoases} WHERE id_estudiante = estado_ases.id_estudiante)";
        }
        
        
        if($ch == "TODOS"){
            $sql_query = "SELECT ".$columns_str." FROM {cohort} AS pc 
                INNER JOIN (
                    SELECT * FROM {cohort_members} AS pcm 
                    INNER JOIN (
                        SELECT * FROM (
                            SELECT id AS id_1, * FROM (SELECT *, id AS id_user FROM {user}) AS userm 
                            INNER JOIN (
                                SELECT userid, CAST(d.data as int) as data 
                                FROM {user_info_data} d 
                                WHERE d.data <> '' 
                                AND fieldid = (SELECT id FROM {user_info_field} as f WHERE f.shortname ='idtalentos') 
                            ) AS field 
                            ON userm. id_user = field.userid ) AS usermoodle 
                        INNER JOIN {talentospilos_usuario} as usuario 
                        ON usermoodle.data = usuario.id 
                        WHERE usermoodle.id_user in (
                            $query_status ) AND usermoodle.id_user in ( $query_user )
                        ) as usertm 
                    ON usertm.id_user = pcm.userid) as pcmuser 
                ON pc.id = pcmuser.cohortid WHERE pc.idnumber like '".$infoinstancia->cod_univalle."%' ".$asescohorts.";";
                
            // $sql_query = "SELECT ".$columns_str." FROM {cohort} AS pc INNER JOIN (SELECT * FROM {cohort_members} AS pcm INNER JOIN (SELECT * FROM (SELECT * FROM (SELECT *, id AS id_user FROM {user}) AS userm INNER JOIN (SELECT * FROM {user_info_data} as d INNER JOIN {user_info_field} as f ON d.fieldid = f.id WHERE f.shortname ='idtalentos' ) AS field ON userm. id_user = field.userid ) AS usermoodle INNER JOIN {talentospilos_usuario} as usuario ON CAST( usermoodle.data AS INT) = usuario.id WHERE usermoodle.id_user in (SELECT userid FROM {user_info_data} as d INNER JOIN {user_info_field} as f ON d.fieldid = f.id WHERE f.shortname ='estado' AND d.data ='ACTIVO')) as usertm ON usertm.id_user = pcm.userid) as pcmuser on pc.id = pcmuser.cohortid WHERE pc.idnumber like '%SP%';";
            
        }
        else {
            $sql_query = "
                SELECT ".$columns_str." 
                FROM {cohort} AS pc INNER JOIN (
                    SELECT * FROM {cohort_members} 
                    AS pcm 
                    INNER JOIN (
                        SELECT * FROM (
                            SELECT id AS id_1, * FROM (SELECT *, id AS id_user FROM {user}) AS userm 
                            INNER JOIN (SELECT userid, CAST(d.data as int) AS data 
                            FROM {user_info_data} d 
                            WHERE d.data <> '' and fieldid = (
                                SELECT id 
                                FROM  {user_info_field} as f 
                                WHERE f.shortname ='idtalentos')) AS field 
                            ON CAST(userm.id_user AS TEXT) = CAST(field.userid AS TEXT)) 
                        AS usermoodle 
                        INNER JOIN (
                            SELECT id as idtalentos, * 
                            FROM {talentospilos_usuario}) 
                        AS usuario ON usermoodle.data = usuario.id 
                        WHERE usermoodle.id_user in (
                            $query_status ) AND usermoodle.id_user in ( $query_user ) 
                        ) AS usertm 
                    ON usertm.id_user = pcm.userid) as pcmuser on pc.id = pcmuser.cohortid ";
                    
            $whereclause = "WHERE pc.idnumber ='".$ch."';";

            $sql_query.=  $whereclause;
            
        }



    }
    // print_r($sql_query);
    // die();
    
    $result_query = $DB->get_records_sql($sql_query,null);
    // print_r($result_query);
    // die();

    if($result_query){
      
      $result  = array();
      foreach ($result_query as $ri){
          $temp = array();
          foreach($column as $c){
              $tempc;
              if (in_array($c,$name_chk_db)){
                  $tempc = $chk [array_search($c,$name_chk_db)];
              }
              else{
                  $tempc = $c;
              }
              
              if ($c == "username"){
                  $temp[$tempc] = substr ($ri->$c, 0 , -5);
              }else{
                  $temp[$tempc] = $ri->$c;
              }
          }
          foreach($column_risk_nombres as $c){
              $tempc = $c;
              if($ri->$c < 1){
                  //$temp[$tempc] = '<span style="background: #BEF781;">bajo</span>';    
                  $temp[$tempc] = '-';
              }
              else if($ri->$c == 1){
                  $temp[$tempc] = '<span style="background: #BEF781;">bajo</span>';    
                 // $temp[$tempc] = 'bajo';
              }
              else if($ri->$c == 2){
                  $temp[$tempc] = '<span style="background: #F7BE81;">medio</span>';  
                  //$temp[$tempc] = 'medio';
              }
              else if($ri->$c == 3){
                  $temp[$tempc] = '<span style="background: #F78181;">alto</span>';  
                  //$temp[$tempc] = 'alto';
              }
              else{
                  $temp[$tempc] = $ri->$c;
              }
              
          }
          array_push($result, $temp);    
      }

    /*********************************************************/
    /**** Consulta relacionada con el programa académico *****/
    /*********************************************************/

    // Se desenmascaran los campos asociados a la consulta académica ("Código programa", "Programa académico", "Facultad")
    $academic_fields_array = [
        "Código programa" => "cod_univalle",
        "Programa académico" => "nombre",
        "Facultad" => "nombre"
    ];
    
    $academic_fields_string = "";

    $count = 0;

    if($academic_fields){
        foreach ($academic_fields as $field){
            switch($field){
                case "Código programa":
                    $academic_fields_string .= "programa.".$academic_fields_array[$field]." AS \"Código programa\", ";
                    break;
                case "Programa académico":
                    $academic_fields_string .= "programa.".$academic_fields_array[$field]." AS \"Programa académico\", ";
                    break;
                case "Facultad":
                    $academic_fields_string .= "facultad.".$academic_fields_array[$field]." AS \"Facultad\", ";
                    break;
            }

            $count++;

            if($count == count($academic_fields)){
                $academic_fields_string = substr($academic_fields_string, 0, -2);
            }
        }

        $academic_query = "SELECT programa.id, ".$academic_fields_string." FROM {talentospilos_programa} AS programa 
                                                                  INNER JOIN {talentospilos_facultad} AS facultad 
                                                                  ON programa.id_facultad = facultad.id";

        $result_academic_query = $DB->get_records_sql($academic_query);

        foreach($result as &$student){

            $sql_query = "SELECT id FROM {user} WHERE username LIKE '$student[Código]%'";
            $id_student = $DB->get_record_sql($sql_query)->id;

            $added_fields = get_adds_fields_mi($id_student);

            $academic_program = $result_academic_query[$added_fields->idprograma];

            foreach($academic_fields_array as $field){

                $student = array_merge((array) $student, (array) $academic_program);

            }
        }
    }
      
      //print_r($result);
      
      $prueba =  new stdClass;
      $prueba->data= $result;
      $prueba->columns = $columns_str." y la poblacion es: ".$population[0]." - ".$population[1];
    }else{
      $prueba =  new stdClass;
      $prueba->error = "No hay resultados en la consulta";
    }     

  return $prueba;
}

/**
 * Funcion que retorna un query especifico segun si el rol del usuario es monitor o practicante, para obtener sus estudiantes asignados
 * 
 * @param $USER
 * @return query String 
 */

function getQueryUser($USER){
    global $DB;
    $id = $USER->id;
    $query_role = "SELECT rol.nombre_rol  FROM {talentospilos_rol} rol INNER JOIN {talentospilos_user_rol} uRol ON rol.id = uRol.id_rol WHERE uRol.id_usuario = $id AND uRol.id_semestre = (SELECT max(id_semestre) FROM {talentospilos_user_rol})";
    $rol = $DB->get_record_sql($query_role)->nombre_rol;

    $query = "";

    if($rol == 'monitor_ps'){
        $query = "SELECT muser.id 
                  FROM {user} muser INNER JOIN {user_info_data} data ON muser.id = data.userid 
                  WHERE data.data IN (SELECT CAST(mon_estud.id_estudiante as text) 
                                      FROM {talentospilos_monitor_estud} mon_estud 
                                      WHERE id_monitor = $id AND id_semestre = (SELECT id FROM {talentospilos_semestre} WHERE fecha_inicio = (SELECT max(fecha_inicio) from {talentospilos_semestre}))) 
                      AND data.fieldid = (SELECT id 
                                          FROM  {user_info_field} 
                                          WHERE shortname ='idtalentos')";
    }else if($rol == 'practicante_ps'){
        $query = "SELECT muser.id 
                  FROM {user} muser INNER JOIN {user_info_data} data ON muser.id = data.userid 
                  WHERE data.data IN (SELECT CAST(mon_estud.id_estudiante as text) 
                                      FROM {talentospilos_monitor_estud} mon_estud  
                                      WHERE id_monitor IN (SELECT urol.id_usuario
                                                          FROM {talentospilos_user_rol} urol 
                                                          WHERE id_jefe = $id)
                                      AND id_semestre = (SELECT id FROM {talentospilos_semestre} WHERE fecha_inicio = (SELECT max(fecha_inicio) from {talentospilos_semestre})))
                      AND data.fieldid = (SELECT id 
                                           FROM  mdl_user_info_field 
                                           WHERE shortname ='idtalentos')";
    }
    return $query;

}

?>