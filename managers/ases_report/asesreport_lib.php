<?php
require_once(dirname(__FILE__).'/../../../../config.php');
require_once(dirname(__FILE__).'/../instance_management/instance_lib.php');
require_once(dirname(__FILE__).'/../lib/lib.php');
require_once(dirname(__FILE__).'/../lib/student_lib.php');
require_once(dirname(__FILE__).'/../user_management/user_lib.php');
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
 * Funcion recupera la informacion necesaria para la grafica de sexo de acuerdo a la cohorte seleccionado
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
    foreach($result as $datoEdad){
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
 * @see get_ases_report()
 * @param $column       --> Campos a seleccionar
 * @param $population   --> Estado y cohorte
 * @param $risk         --> Nivel de riesgo a mostrar
 * @param $academic_fields --> Campos relacionados con el programa académico y facultad
 * @param $idinstancia  --> Instancia del módulo
 * @return Array 
 */
function get_ases_report($general_fields=null, $conditions, $risk_fields=null, $academic_fields=null, $instance_id){

    global $DB, $USER;

    $actions = $USER->actions;

    // ********* Se arman las clausulas de la consulta sql ***********

    // ***** Select clause *****
    $select_clause = "SELECT ";
    $from_clause = "";
    $where_clause = " WHERE ";
    $sub_query_cohort = "";
    $sub_query_status = "";
    $sub_query_academic = "";
    $sub_query_risks = "";

    if($general_fields){
        foreach($general_fields as $field){
            $select_clause .= $field.', ';
        }
    }

    if($risk_fields){

        foreach($risk_fields as $risk_field){
            $name_query = "SELECT * FROM {talentospilos_riesgos_ases} WHERE id =".$risk_field;
            $risk_name = $DB->get_record_sql($name_query)->nombre;
            //array_push($column_risk_nombres, $name_query);
            
            //calificacion_riesgo
            $select_clause = $select_clause." (SELECT 
                                                    CASE WHEN calificacion_riesgo = 1 THEN 'Bajo'
                                                         WHEN calificacion_riesgo = 2 THEN 'Medio'
                                                         WHEN calificacion_riesgo = 3 THEN 'Alto'
                                                         WHEN calificacion_riesgo = 0 THEN 'N.R.'
                                                         ELSE 'N.R.' 
                                                    END
                                                FROM {talentospilos_riesg_usuario} WHERE ";
            $select_clause = $select_clause."id_usuario = user_extended.id_ases_user AND id_riesgo = ".$risk_field.") AS ".$risk_name.", ";
        }
    }

    if($academic_fields){
        foreach($academic_fields as $field){
            $select_clause .= $field.', ';
        }

        $sub_query_academic .= " INNER JOIN {talentospilos_programa} AS acad_program ON user_extended.id_academic_program = acad_program.id
                                INNER JOIN {talentospilos_facultad} AS faculty ON faculty.id = acad_program.id_facultad";
    }

    $select_clause = substr($select_clause, 0, -2);

    // **** From clause ****
    $from_clause = " FROM {user} as user_moodle INNER JOIN {talentospilos_user_extended} AS user_extended ON user_moodle.id = user_extended.id_moodle_user
                                                INNER JOIN {talentospilos_usuario} AS tp_user ON user_extended.id_ases_user = tp_user.id ";

    // **** Where clause ****
    //$where_clause .= " tp_ases_status.id_instancia = $instance_id";

    // Condición cohorte
    if($conditions[0] != 'TODOS'){

        $sub_query_cohort .= "INNER JOIN (SELECT user_moodle.username
                                         FROM {cohort_members} AS members_cohort INNER JOIN {user} AS user_moodle ON user_moodle.id = members_cohort.userid
                                                                                 INNER JOIN {cohort} AS cohorts ON cohorts.id = members_cohort.cohortid
                                         WHERE cohorts.idnumber = '$conditions[0]') AS cohort_query ON cohort_query.username = user_moodle.username ";
    }else if($conditions[0] == 'TODOS'){
        $sub_query_cohort .= " INNER JOIN (SELECT cohort.id, moodle_user.username 
                                            FROM {cohort} AS cohort INNER JOIN {talentospilos_inst_cohorte} AS instance_cohort ON cohort.id = instance_cohort.id_cohorte
                                                                    INNER JOIN {cohort_members} AS cohort_member ON cohort_member.cohortid = cohort.id
                                                                    INNER JOIN {user} AS moodle_user ON moodle_user.id = cohort_member.userid
                                            WHERE id_instancia = $instance_id) AS all_students_cht ON all_students_cht.username = user_moodle.username";
    }
    // Condición estado ASES
    if($conditions[1] != 'TODOS'){

        $sub_query_status .= " INNER JOIN (SELECT current_status.username, status_ases.id_estado_ases 
                                            FROM (SELECT MAX(status_ases.id) AS id, moodle_user.username
                                                FROM {talentospilos_est_estadoases} AS status_ases 
                                                    INNER JOIN {talentospilos_user_extended} AS user_extended ON status_ases.id_estudiante = user_extended.id_ases_user
                                                INNER JOIN {user} AS moodle_user ON moodle_user.id = user_extended.id_moodle_user
                                                GROUP BY moodle_user.username) AS current_status
                                            INNER JOIN {talentospilos_est_estadoases} AS status_ases ON status_ases.id = current_status.id
                                            WHERE id_estado_ases = $conditions[1]
                                            ) AS query_status_ases ON query_status_ases.username = user_moodle.username";
    }

    if(property_exists($actions, 'search_all_students_ar')){
        
        $sql_query = $select_clause.$from_clause.$sub_query_cohort.$sub_query_status.$sub_query_academic;
        $result_query = $DB->get_records_sql($sql_query);

    }else if(property_exists($actions, 'search_assigned_students_ar')){

        $user_id = $USER->id;
        $id_current_semester = get_current_semester()->max;

        $sql_query = "SELECT roles.nombre_rol, user_role.id_programa 
                      FROM {talentospilos_user_rol} AS user_role 
                                                INNER JOIN {talentospilos_rol} AS roles ON user_role.id_rol = roles.id
                      WHERE user_role.id_semestre = $id_current_semester AND user_role.estado = 1 AND user_role.id_usuario = $user_id";

        $user_role = $DB->get_record_sql($sql_query);

        switch($user_role->nombre_rol){
            case 'director_prog':

                $conditions_query_directors = " user_extended.id_academic_program = $user_role->id_programa";

                $where_clause .= $conditions_query_directors;

                $sql_query = $select_clause.$from_clause.$sub_query_cohort.$sub_query_status.$sub_query_academic.$where_clause;
                $result_query = $DB->get_records_sql($sql_query);

                break;

            case 'profesional_ps':

                $sub_query_ps_staff = " INNER JOIN {talentospilos_monitor_estud} AS monitor_student ON monitor_student.id_estudiante = user_extended.id_ases_user
                                        INNER JOIN (SELECT t_monitor_practicante.id_monitor, t_monitor_practicante.id_practicante, t_practicante_profesional.id_profesional
                                                FROM
                                                (SELECT id_usuario AS id_monitor, id_jefe AS id_practicante 
                                                FROM {talentospilos_user_rol} AS user_rol
                                                    INNER JOIN {talentospilos_rol} AS rol ON user_rol.id_rol = rol.id
                                                WHERE rol.nombre_rol = 'monitor_ps' AND user_rol.id_semestre = $id_current_semester AND user_rol.id_instancia = $instance_id) AS t_monitor_practicante
                                                INNER JOIN
                                                (SELECT id_usuario AS id_practicante, id_jefe AS id_profesional
                                                FROM {talentospilos_user_rol} AS user_rol
                                                    INNER JOIN {talentospilos_rol} AS rol ON user_rol.id_rol = rol.id
                                                WHERE rol.nombre_rol = 'practicante_ps' AND user_rol.id_semestre = $id_current_semester AND user_rol.id_instancia = $instance_id) AS t_practicante_profesional
                                                ON t_monitor_practicante.id_practicante = t_practicante_profesional.id_practicante) AS t_monitor_practicante_profesional
                                    ON t_monitor_practicante_profesional.id_monitor = monitor_student.id_monitor";

                $where_clause .= " t_monitor_practicante_profesional.id_profesional = $user_id AND monitor_student.id_semestre = $id_current_semester";

                $sql_query = $select_clause.$from_clause.$sub_query_cohort.$sub_query_status.$sub_query_academic.$sub_query_ps_staff.$where_clause;

                $result_query = $DB->get_records_sql($sql_query);
                
                break;

            case 'practicante_ps':
            
                $sub_query_ps_staff = " INNER JOIN {talentospilos_monitor_estud} AS monitor_student ON monitor_student.id_estudiante = user_extended.id_ases_user
                                        ";


                break;

            case 'monitor_ps':

                $query_monitors = " INNER JOIN {talentospilos_monitor_estud} AS monitor_student ON monitor_student.id_estudiante = user_extended.id_ases_user";
                $where_clause .= " monitor_student.id_monitor = $user_id AND monitor_student.id_semestre = $id_current_semester";

                $sql_query = $select_clause.$from_clause.$sub_query_cohort.$sub_query_status.$sub_query_academic.$query_monitors.$where_clause;
                $result_query = $DB->get_records_sql($sql_query);

                break;

            default:
                break;
        }
            
    }else{
        return 'El usuario no tiene permisos para listar estudiantes en el reporte ASES';
    }

    $result_to_return = array();

    foreach($result_query as $result){

        array_push($result_to_return, $result);
    }

    return $result_to_return;
}

?>
