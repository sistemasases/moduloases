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
 * @deprecated
 * @deprecated No utilizado
 * Función que recupera cohortes
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
 * @see get_not_assign_students()
 * @param $column       --> Campos a seleccionar
 * @param $population   --> Estado y cohorte
 * @param $academic_fields --> Campos relacionados con el programa académico y facultad
 * @param $idinstancia  --> Instancia del módulo
 * @return Array 
 */
function get_not_assign_students($general_fields=null, $conditions, $academic_fields=null, $instance_id){

    global $DB, $USER;

    $actions = $USER->actions;

    //$conditions[1] = 'TODOS';

    // ********* Se arman las clausulas de la consulta sql ***********

    // ***** Select clause *****
    $select_clause = "SELECT DISTINCT ";
    $from_clause = "";
    $where_clause = " WHERE ";
    $sub_query_cohort = "";
    $sub_query_status = "";
    $sub_query_academic = "";

    if($general_fields){
        foreach($general_fields as $field){
            $select_clause .= $field.', ';
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

        $sub_query_cohort .= "INNER JOIN (SELECT DISTINCT user_moodle.username, STRING_AGG(cohorts.idnumber, ', ') AS cohorts_student
                                         FROM {cohort_members} AS members_cohort INNER JOIN {user} AS user_moodle ON user_moodle.id = members_cohort.userid
                                                                                 INNER JOIN {cohort} AS cohorts ON cohorts.id = members_cohort.cohortid
                                         WHERE cohorts.idnumber = '$conditions[0]'
                                         GROUP BY user_moodle.username) AS cohort_query ON cohort_query.username = user_moodle.username ";
    }else if($conditions[0] == 'TODOS'){
        $sub_query_cohort .= " INNER JOIN (SELECT moodle_user.username, STRING_AGG(cohorts.idnumber, ', ') AS cohorts_student 
                                            FROM {cohort} AS cohort INNER JOIN {talentospilos_inst_cohorte} AS instance_cohort ON cohort.id = instance_cohort.id_cohorte
                                                                    INNER JOIN {cohort_members} AS cohort_member ON cohort_member.cohortid = cohort.id
                                                                    INNER JOIN {cohort} AS cohorts ON cohort_member.cohortid = cohorts.id
                                                                    INNER JOIN {user} AS moodle_user ON moodle_user.id = cohort_member.userid
                                            WHERE id_instancia = $instance_id
                                            GROUP BY moodle_user.username) AS all_students_cht ON all_students_cht.username = user_moodle.username";
    }

    if(property_exists($actions, 'search_all_students_ar')){
        
        $sql_query = $select_clause.$from_clause.$sub_query_cohort.$sub_query_status.$sub_query_academic;
        $result_query = $DB->get_records_sql($sql_query);

    }

        $user_id = $USER->id;

        $instance= $instance_id;
        $id_current_semester = get_current_semester()->max;
        $sql_query = "SELECT roles.nombre_rol, user_role.id_programa 
                      FROM {talentospilos_user_rol} AS user_role 
                                                INNER JOIN {talentospilos_rol} AS roles ON user_role.id_rol = roles.id
                      WHERE user_role.id_semestre = $id_current_semester AND user_role.estado = 1 and user_role.id_usuario='$user_id' and id_instancia='$instance'";

        $user_role = $DB->get_record_sql($sql_query);

        switch($user_role->nombre_rol){
            case 'director_prog':

                $conditions_query_directors = " user_extended.id_academic_program = $user_role->id_programa";

                $where_clause .= $conditions_query_directors;

                $sql_query = $select_clause.$from_clause.$sub_query_cohort.$sub_query_status.$sub_query_academic.$where_clause;
                $result_query = $DB->get_records_sql($sql_query);

                break;

            case 'profesional_ps':

                $where_clause .= " user_moodle.username NOT IN (SELECT user_moodle.username
                                                                FROM {talentospilos_usuario}  AS ases_user
                                                                INNER JOIN {talentospilos_user_extended} AS user_extended ON user_extended.id_ases_user = ases_user.id
                                                                INNER JOIN {user} AS user_moodle ON user_moodle.id = user_extended.id_moodle_user
                                                                INNER JOIN {cohort_members}  AS cohorts_students ON cohorts_students.userid = user_extended.id_moodle_user
                                                                INNER JOIN {talentospilos_monitor_estud} AS students_monitor ON students_monitor.id_estudiante = ases_user.id
                                                                INNER JOIN {talentospilos_inst_cohorte}  AS cohorts_instance ON cohorts_instance.id_cohorte = cohorts_students.cohortid
                                                                WHERE cohorts_instance.id_instancia = $instance)";
                $sql_query = $select_clause.$from_clause.$sub_query_cohort.$sub_query_status.$sub_query_academic.$where_clause;
                $result_query = $DB->get_records_sql($sql_query);

                break;

            case 'practicante_ps':
            
                $query_monitors = " INNER JOIN {talentospilos_monitor_estud} AS monitor_student ON monitor_student.id_estudiante = user_extended.id_ases_user";
                $query_pract = " INNER JOIN (SELECT id_jefe, id_usuario, id_semestre FROM {talentospilos_monitor_estud}) AS pract_monitor ON monitor_student.id_usuario = pract_monitor.id_usuario";
                $where_clause .= " pract_monitor.id_jefe = $user_id AND pract_monitor.id_semestre = $id_current_semester";

                $sql_query = $select_clause.$from_clause.$sub_query_cohort.$sub_query_status.$sub_query_academic.$query_monitors;
                $result_query = $DB->get_records_sql($sql_query);

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
            

    $result_to_return = array();

    foreach($result_query as $result){

        array_push($result_to_return, $result);
    }

    return $result_to_return;
}

/**
 * Función que recupera datos para la tabla de ases_report, dado el estado, la cohorte y un conjunto de campos a extraer.
 *
 * @see get_ases_report()
 * @param $general_fields       --> Campos a seleccionar
 * @param $conditions   --> Estado y cohorte
 * @param $risk         --> Nivel de riesgo a mostrar
 * @param $academic_fields --> Campos relacionados con el programa académico y facultad
 * @param $idinstancia  --> Instancia del módulo
 * @return Array 
 */
function get_ases_report($general_fields=null, $conditions, $risk_fields=null, $academic_fields=null, $statuses_fields=null, $assignment_fields=null, $instance_id){

    global $DB, $USER;

    $actions = $USER->actions;
    $id_current_semester = get_current_semester()->max;

    $conditions[1] = 'TODOS';

    // ********* Se arman las clausulas de la consulta sql ***********

    // ***** Select clause *****
    $select_clause = "SELECT DISTINCT ";
    $from_clause = "";
    $where_clause = " WHERE ";
    $sub_query_cohort = "";
    $sub_query_status = "";
    $sub_query_icetex_status = "";
    $sub_query_academic = "";
    $sub_query_risks = "";
    $sub_query_assignment_fields = "";

    if($general_fields){
        foreach($general_fields as $field){
            $select_clause .= $field.', ';
        }
    }

    if($risk_fields){

        foreach($risk_fields as $risk_field){
            $name_query = "SELECT * FROM {talentospilos_riesgos_ases} WHERE id =".$risk_field;
            $risk_name = $DB->get_record_sql($name_query)->nombre;
            
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
                                 INNER JOIN {talentospilos_facultad} AS faculty ON faculty.id = acad_program.id_facultad
                                 LEFT JOIN {talentospilos_history_academ} AS academic_history ON academic_history.id_estudiante = user_extended.id_ases_user
                                 LEFT JOIN (SELECT COUNT(estim.id) AS estimulos, academ.id_estudiante
                                            FROM {talentospilos_history_academ} AS academ INNER JOIN {talentospilos_history_estim} AS estim ON academ.id = estim.id_history
                                            GROUP BY (academ.id_estudiante))
                                            AS estim_query
                                            ON estim_query.id_estudiante = user_extended.id_ases_user
                                 LEFT JOIN (SELECT COUNT(bajo.id) AS bajos, academ.id_estudiante
                                            FROM {talentospilos_history_academ} AS academ INNER JOIN {talentospilos_history_bajos} AS bajo 
                                                 ON academ.id = bajo.id_history
                                            GROUP BY (academ.id_estudiante))
                                            AS bajos_query
                                            ON bajos_query.id_estudiante = user_extended.id_ases_user";

                                   
        $where_clause .= " academic_history.id_semestre = $id_current_semester AND ";
    }

    $select_clause = substr($select_clause, 0, -2);

    // **** From clause ****
    $from_clause = " FROM {user} as user_moodle INNER JOIN {talentospilos_user_extended} AS user_extended ON user_moodle.id = user_extended.id_moodle_user
                                                INNER JOIN {talentospilos_usuario} AS tp_user ON user_extended.id_ases_user = tp_user.id ";

    // **** Where clause ****
    //$where_clause .= " tp_ases_status.id_instancia = $instance_id";

    // Condición cohorte
    if($conditions[0] != 'TODOS'){

        $sub_query_cohort .= "INNER JOIN (SELECT user_moodle.username, STRING_AGG(cohorts.idnumber, ', ') AS cohorts_student
                                         FROM {cohort_members} AS members_cohort INNER JOIN {user} AS user_moodle ON user_moodle.id = members_cohort.userid
                                                                                 INNER JOIN {cohort} AS cohorts ON cohorts.id = members_cohort.cohortid
                                         WHERE cohorts.idnumber = '$conditions[0]'
                                         GROUP BY user_moodle.username) AS all_students_cht ON all_students_cht.username = user_moodle.username ";
    }else if($conditions[0] == 'TODOS'){
        $sub_query_cohort .= " INNER JOIN (SELECT moodle_user.username, STRING_AGG(cohorts.idnumber, ', ') AS cohorts_student
                                            FROM {cohort} AS cohort INNER JOIN {talentospilos_inst_cohorte} AS instance_cohort ON cohort.id = instance_cohort.id_cohorte
                                                                    INNER JOIN {cohort_members} AS cohort_member ON cohort_member.cohortid = cohort.id
                                                                    INNER JOIN {cohort} AS cohorts ON cohort_member.cohortid = cohorts.id
                                                                    INNER JOIN {user} AS moodle_user ON moodle_user.id = cohort_member.userid
                                            WHERE id_instancia = $instance_id
                                            GROUP BY moodle_user.username) AS all_students_cht ON all_students_cht.username = user_moodle.username";
    }

    // Campos Estados del estudiante
    if($statuses_fields){

        foreach($statuses_fields as $status_field){

            switch(explode(".", $status_field)[1]){
                case 'estado_ases':
                    $select_clause = $select_clause.", (CASE WHEN $statuses_fields[0] = 'seguimiento' THEN 'SEGUIMIENTO'
                                                        WHEN $statuses_fields[0] = 'noseguimiento' THEN 'NO SEGUIMIENTO'
                                                        ELSE 'N.R.' 
                                                        END) AS estado_ases";

                    $sub_query_status .= " LEFT JOIN (SELECT current_status.username, statuses_ases.nombre AS estado_ases
                                            FROM (SELECT MAX(status_ases.id) AS id, moodle_user.username
                                                FROM {talentospilos_est_estadoases} AS status_ases 
                                                    INNER JOIN {talentospilos_user_extended} AS user_extended ON status_ases.id_estudiante = user_extended.id_ases_user
                                                    INNER JOIN {user} AS moodle_user ON moodle_user.id = user_extended.id_moodle_user
                                                    GROUP BY moodle_user.username) AS current_status
                                            INNER JOIN {talentospilos_est_estadoases} AS status_ases ON status_ases.id = current_status.id
                                            INNER JOIN {talentospilos_estados_ases} AS statuses_ases ON statuses_ases.id = status_ases.id_estado_ases
                                            ) AS query_status_ases ON query_status_ases.username = user_moodle.username";
                    break;
                
                case 'program_status':
                    $select_clause = $select_clause.", ".$status_field;
                    break;
                case 'estado_icetex':
                    $select_clause = $select_clause.", ".$status_field." AS estado_icetex";
                    $sub_query_icetex_status .= " LEFT JOIN (SELECT current_status.username, icetex_statuses.nombre AS estado_icetex
                                                FROM (SELECT MAX(icetex_status.id) AS id, user_moodle.username
                                                      FROM {talentospilos_est_est_icetex} AS icetex_status
                                                      INNER JOIN {talentospilos_user_extended} AS user_extended ON user_extended.id_ases_user = icetex_status.id_estudiante
                                                      INNER JOIN {user} AS user_moodle ON user_moodle.id = user_extended.id_moodle_user
                                                      GROUP BY user_moodle.username
                                                      ) AS current_status
                                                INNER JOIN {talentospilos_est_est_icetex} AS icetex_status ON icetex_status.id = current_status.id
                                                INNER JOIN {talentospilos_estados_icetex} AS icetex_statuses ON icetex_status.id_estado_icetex = icetex_statuses.id
                                                ) AS query_icetex_status ON query_icetex_status.username = user_moodle.username";
                    break;
            }

        }
    }

    //Campos Asignaciones personal socioeducativo
    if($assignment_fields){

        foreach($assignment_fields as $assignment_field){
            $select_clause = $select_clause.", ".$assignment_field;
        }

        $sub_query_assignment_fields = " LEFT JOIN 
                                         (SELECT monitor_student.username, psico_staff.monitor AS monitor, psico_staff.trainer AS trainer, psico_staff.professional AS professional
                                          FROM
        
                                            (SELECT monitor_student.id_monitor, moodle_user.username
                                             FROM {talentospilos_monitor_estud} AS monitor_student
                                             LEFT JOIN {talentospilos_user_extended} AS user_extended ON user_extended.id_ases_user = monitor_student.id_estudiante
                                             INNER JOIN {user} AS moodle_user ON moodle_user.id = user_extended.id_moodle_user
                                             WHERE monitor_student.id_semestre = $id_current_semester AND monitor_student.id_instancia = $instance_id) AS monitor_student
                                            
                                            LEFT JOIN
                                            
                                            (SELECT query_monitor.id_monitor, query_monitor.monitor_name AS monitor, query_trainer.trainer_name AS trainer, query_professional.professional_name AS professional
                                             FROM
                                              (SELECT user_role.id_usuario AS id_monitor, CONCAT(moodle_user.firstname, CONCAT(' ', moodle_user.lastname)) AS monitor_name, user_role.id_jefe AS id_boss_monitor
                                               FROM {talentospilos_user_rol} AS user_role 
                                               INNER JOIN {user} AS moodle_user ON user_role.id_usuario = moodle_user.id
                                               WHERE id_rol = (SELECT id FROM {talentospilos_rol} AS role WHERE nombre_rol = 'monitor_ps') AND id_semestre = $id_current_semester AND id_instancia = $instance_id) AS query_monitor

                                             INNER JOIN
                                            
                                             (SELECT user_role.id_usuario AS id_trainer, CONCAT(moodle_user.firstname, CONCAT(' ', moodle_user.lastname)) AS trainer_name, user_role.id_jefe AS id_boss_trainer
                                              FROM {talentospilos_user_rol} AS user_role 
                                              INNER JOIN {user} AS moodle_user ON user_role.id_usuario = moodle_user.id
                                              WHERE id_rol = (SELECT id FROM {talentospilos_rol} AS role WHERE nombre_rol = 'practicante_ps') AND id_semestre = $id_current_semester AND id_instancia = $instance_id) AS query_trainer
                                            
                                             ON query_monitor.id_boss_monitor = query_trainer.id_trainer
                                            
                                            INNER JOIN
                                            
                                            (SELECT user_role.id_usuario AS id_professional, CONCAT(moodle_user.firstname, CONCAT(' ', moodle_user.lastname)) AS professional_name
                                            FROM {talentospilos_user_rol} AS user_role 
                                            INNER JOIN {user} AS moodle_user ON user_role.id_usuario = moodle_user.id
                                            WHERE id_rol = (SELECT id FROM {talentospilos_rol} AS role WHERE nombre_rol = 'profesional_ps') AND id_semestre = $id_current_semester AND id_instancia = $instance_id) AS query_professional
                                            
                                            ON query_professional.id_professional = query_trainer.id_boss_trainer) AS psico_staff
                                            
                                            ON monitor_student.id_monitor = psico_staff.id_monitor) AS assignments_query
                                        ON assignments_query.username = user_moodle.username
                                         ";
    }

    if(property_exists($actions, 'search_all_students_ar')){
        
        $sql_query = $select_clause.$from_clause.$sub_query_cohort.$sub_query_status.$sub_query_icetex_status.$sub_query_academic.$sub_query_assignment_fields;
        $result_query = $DB->get_records_sql($sql_query);

    }else if(property_exists($actions, 'search_assigned_students_ar')){

        $user_id = $USER->id;
        
        $sql_query = "SELECT roles.nombre_rol, user_role.id_programa 
                      FROM {talentospilos_user_rol} AS user_role 
                                                INNER JOIN {talentospilos_rol} AS roles ON user_role.id_rol = roles.id
                      WHERE user_role.id_semestre = $id_current_semester AND user_role.estado = 1 AND user_role.id_usuario = $user_id";

        $user_role = $DB->get_record_sql($sql_query);

        switch($user_role->nombre_rol){
            case 'director_prog':

                $conditions_query_directors = " user_extended.id_academic_program = $user_role->id_programa";

                $where_clause .= $conditions_query_directors;

                $sql_query = $select_clause.$from_clause.$sub_query_cohort.$sub_query_status.$sub_query_icetex_status.$sub_query_academic.$sub_query_assignment_fields.$where_clause;
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

                $sql_query = $select_clause.$from_clause.$sub_query_cohort.$sub_query_status.$sub_query_academic.$sub_query_icetex_status.$sub_query_assignment_fields.$sub_query_ps_staff.$where_clause;

                $result_query = $DB->get_records_sql($sql_query);
                
                break;

            case 'practicante_ps':
            
                $sub_query_ps_staff = " INNER JOIN {talentospilos_monitor_estud} AS monitor_student ON monitor_student.id_estudiante = user_extended.id_ases_user
                                        INNER JOIN (SELECT id_usuario AS id_monitor, id_jefe AS id_practicante 
                                                    FROM {talentospilos_user_rol} AS user_rol
                                                        INNER JOIN {talentospilos_rol} AS rol ON user_rol.id_rol = rol.id
                                                    WHERE rol.nombre_rol = 'monitor_ps' AND user_rol.id_semestre = $id_current_semester AND user_rol.id_instancia = $instance_id) AS t_monitor_practicante
                                        ON t_monitor_practicante.id_monitor = monitor_student.id_monitor";
                
                $where_clause .= " t_monitor_practicante.id_practicante = $user_id AND monitor_student.id_semestre = $id_current_semester";

                $sql_query = $select_clause.$from_clause.$sub_query_cohort.$sub_query_status.$sub_query_academic.$sub_query_icetex_status.$sub_query_assignment_fields.$sub_query_ps_staff.$where_clause;

                $result_query = $DB->get_records_sql($sql_query);

                break;

            case 'monitor_ps':

                $query_monitors = " INNER JOIN {talentospilos_monitor_estud} AS monitor_student ON monitor_student.id_estudiante = user_extended.id_ases_user";
                $where_clause .= " monitor_student.id_monitor = $user_id AND monitor_student.id_semestre = $id_current_semester";

                $sql_query = $select_clause.$from_clause.$sub_query_cohort.$sub_query_status.$sub_query_academic.$sub_query_icetex_status.$sub_query_assignment_fields.$query_monitors.$where_clause;
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

/**
 * Función que recupera datos para la tabla de ases_report, de los estudiantes asignados a una persona del rol socioeducativo o
 * asociados a un director de programa académico
 *
 * @see get_default_ases_report()
 * @param $column       --> Campos a seleccionar
 * @param $population   --> Estado y cohorte
 * @param $risk_fields         --> Nivel de riesgo a mostrar
 * @param $academic_fields --> Campos relacionados con el programa académico y facultad
 * @param $instance_id  --> Instancia del módulo
 * @return Array 
 */
function get_default_ases_report($id_instance){

    $query_fields = array();
    array_push($query_fields, "user_moodle.username");
    array_push($query_fields, "user_moodle.firstname");
    array_push($query_fields, "user_moodle.lastname");
    array_push($query_fields, "tp_user.num_doc");
    array_push($query_fields, "all_students_cht.cohorts_student");

    $conditions = array();
    array_push($conditions, "TODOS");
    //array_push($conditions, "TODOS");

    $columns = array();
    array_push($columns, array("title"=>"Código estudiante", "name"=>"username", "data"=>"username"));
    array_push($columns, array("title"=>"Nombre(s)", "name"=>"firstname", "data"=>"firstname"));
    array_push($columns, array("title"=>"Apellido(s)", "name"=>"lastname", "data"=>"lastname"));
    array_push($columns, array("title"=>"Número de documento", "name"=>"num_doc", "data"=>"num_doc"));
    array_push($columns, array("title"=>"Cohorte", "name"=>"cohorts_students", "data"=>"cohorts_student"));

    $default_students = get_ases_report($query_fields, $conditions, null, null, null, null, $id_instance);

    $data_to_table = array(
        "bsort" => false,
        "data"=> $default_students,
        "columns" => $columns,
        "select" => "false",
        "fixedHeader"=> array(
            "header"=> true,
            "footer"=> true
        ),
        "language" => 
            array(
                "search"=> "Buscar:",
                "oPaginate" => array (
                    "sFirst"=>    "Primero",
                    "sLast"=>     "Último",
                    "sNext"=>     "Siguiente",
                    "sPrevious"=> "Anterior"
                ),
                "sProcessing"=>     "Procesando...",
                "sLengthMenu"=>     "Mostrar _MENU_ registros",
                "sZeroRecords"=>    "No se encontraron resultados",
                "sEmptyTable"=>     "Ningún dato disponible en esta tabla",
                "sInfo"=>           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty"=>      "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered"=>   "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix"=>    "",
                "sSearch"=>         "Buscar:",
                "sUrl"=>            "",
                "sInfoThousands"=>  ",",
                "sLoadingRecords"=> "Cargando...",
                "oAria"=> array(
                    "sSortAscending"=>  ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending"=> ": Activar para ordenar la columna de manera descendente"
                )
            ),
        "dom"=>'lifrtpB',
        "buttons"=>array(
            array(
                "extend"=>'print',
                "text"=>'Imprimir'
            ),
            array(
                "extend"=>'csvHtml5',
                "text"=>'CSV'
            ),
            array(
                "extend" => "excel",
                                "text" => 'Excel',
                                "className" => 'buttons-excel',
                                "filename" => 'Export excel',
                                "extension" => '.xls'
            )
        )
    );

    return $data_to_table;

}
?>
