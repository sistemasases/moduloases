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
 * Ases block
 *
 * @author     Jeison Cardona Gómez
 * @package    block_ases
 * @copyright  2018 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__). '/../../../../config.php');
require_once(dirname(__FILE__).'/../jquery_datatable/jquery_datatable_lib.php');
require_once $CFG->dirroot.'/blocks/ases/managers/lib/student_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/user_management/user_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/ases_report/asesreport_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/lib/lib.php';

use jquery_datatable\Datatable;

const ENDPOINT_GET_MONITOR_PRACTICING_AND_STUDENTS_REPORT = 'get_monitor_practicing_and_students_report';

/**
 * Función que renombra para clasificar la función get_professionals_by_instance en otras partes del plugin, el objetivo
 * es dibujar un mapa de seguimiento para saber de donde provienen las funciones
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @return Array 
 */

function monitor_assignments_get_professionals_by_instance( $instance_id ){

    global $DB;

    $sql = "SELECT CONCAT(moodle_user.firstname, CONCAT(' ', moodle_user.lastname)) AS fullname, moodle_user.id, moodle_user.username
    FROM {talentospilos_user_rol} AS user_rol
    INNER JOIN {user} AS moodle_user ON moodle_user.id = user_rol.id_usuario
    WHERE id_rol = (SELECT id FROM {talentospilos_rol} WHERE nombre_rol = 'profesional_ps')
    AND id_instancia = $instance_id
    AND estado = 1
    AND id_semestre =". get_current_semester()->max ." ORDER BY fullname";
    return $DB->get_records_sql( $sql );
}

/**
 * Función que renombra para clasificar la función get_practicing_by_instance en otras partes del plugin, el objetivo
 * es dibujar un mapa de seguimiento para saber de donde provienen las funciones
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @return Array 
 */

function monitor_assignments_get_practicing_by_instance( $instance_id ){

    global $DB;

    $sql = "SELECT CONCAT(moodle_user.firstname, CONCAT(' ', moodle_user.lastname)) AS fullname, moodle_user.id, user_rol.id_jefe, moodle_user.username
    FROM {talentospilos_user_rol} AS user_rol
    INNER JOIN {user} AS moodle_user ON moodle_user.id = user_rol.id_usuario
    WHERE id_rol = (SELECT id FROM {talentospilos_rol} WHERE nombre_rol = 'practicante_ps')
    AND id_instancia = $instance_id
    AND estado = 1
    AND id_semestre = ". get_current_semester()->max." ORDER BY fullname";

    return $DB->get_records_sql( $sql );
}

/**
 * Función que renombra para clasificar la función get_monitors_by_instance en otras partes del plugin, el objetivo
 * es dibujar un mapa de seguimiento para saber de donde provienen las funciones
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @return Array 
 */

function monitor_assignments_get_monitors_by_instance( $instance_id ){

    global $DB;

    $sql = "SELECT DISTINCT user_programa_0.id, user_programa_0.fullname, user_programa_0.cod_programa, user_programa_0.nombre_programa, facultad_0.id AS id_facultad, facultad_0.nombre AS nombre_facultad, user_programa_0.username
    FROM {talentospilos_facultad} AS facultad_0
    INNER JOIN (
            SELECT user_0.id, user_0.fullname, user_0.cod_programa, programa_0.nombre AS nombre_programa, programa_0.id_facultad, user_0.username
            FROM {talentospilos_programa} AS programa_0
            INNER JOIN (
                    SELECT CONCAT(moodle_user.firstname, CONCAT(' ', moodle_user.lastname)) AS fullname, moodle_user.id, moodle_user.username, cast(nullif(split_part(moodle_user.username, '-', 2), '') AS INTEGER) AS cod_programa
                    FROM {talentospilos_user_rol} AS user_rol
                    INNER JOIN {user} AS moodle_user ON moodle_user.id = user_rol.id_usuario
                    WHERE id_rol = (
                                SELECT id
                                FROM {talentospilos_rol} 
                                WHERE nombre_rol = 'monitor_ps'
                            )
                    AND id_instancia = $instance_id
                    AND id_semestre = ". get_current_semester()->max ." 
                    AND estado = 1 
                    ORDER BY fullname
                   ) AS user_0
            ON user_0.cod_programa = programa_0.cod_univalle
           ) AS user_programa_0
    ON user_programa_0.id_facultad = facultad_0.id
   ORDER BY fullname ASC";

    return $DB->get_records_sql( $sql );
}

/**
 * Función que retorna todos los usuarios del sistema.
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @return Array (
 *      stdClass(
 *          ->fullname
 *          ->id_ases_user
 *          ->cod_programa
 *          ->nombre_programa
 *      )
 * )
 */

function monitor_assignments_get_students_by_instance( $instance_id ){

    global $DB;

    $sql = "SELECT moodle_ases_user_programa_facultad_0.id_ases_user AS id, CONCAT(moodle_user_0.firstname, CONCAT(' ', moodle_user_0.lastname)) AS fullname, moodle_ases_user_programa_facultad_0.cod_programa, moodle_ases_user_programa_facultad_0.nombre_programa, moodle_ases_user_programa_facultad_0.id_facultad, moodle_ases_user_programa_facultad_0.nombre_facultad, moodle_user_0.username
    FROM {user} AS moodle_user_0
    INNER JOIN
        (
            SELECT moodle_ases_user_programa_0.id_moodle_user, moodle_ases_user_programa_0.id_ases_user, moodle_ases_user_programa_0.cod_programa, moodle_ases_user_programa_0.nombre_programa, facultad_0.id AS id_facultad, facultad_0.nombre AS nombre_facultad
                FROM {talentospilos_facultad} AS facultad_0
                INNER JOIN 
                (
                    SELECT moodle_ases_user_0.id_moodle_user, moodle_ases_user_0.id_ases_user, programa_0.cod_univalle AS cod_programa, programa_0.nombre AS nombre_programa, id_facultad
                    FROM {talentospilos_programa} AS programa_0
                    INNER JOIN
                    (
                        SELECT *
                        FROM {talentospilos_user_extended} AS user_ext_0
                        INNER JOIN
                        (
                            SELECT DISTINCT cohort_members_0.userid as user_id
                            FROM {cohort_members} AS cohort_members_0 
                            INNER JOIN
                            (
                                SELECT id_cohorte 
                                FROM {talentospilos_inst_cohorte} AS inst_cohorte_0 
                                WHERE id_instancia = $instance_id
                            ) AS inst_cohorte1
                            ON inst_cohorte1.id_cohorte = cohort_members_0.cohortid
                        ) AS users_distinct_0
                        ON users_distinct_0.user_id = user_ext_0.id_moodle_user
                        WHERE user_ext_0.tracking_status = 1
                    ) AS moodle_ases_user_0
                    ON programa_0.id = moodle_ases_user_0.id_academic_program
                ) AS moodle_ases_user_programa_0
                ON facultad_0.id = moodle_ases_user_programa_0.id_facultad
        ) AS moodle_ases_user_programa_facultad_0
    ON moodle_ases_user_programa_facultad_0.id_moodle_user = moodle_user_0.id
    ORDER BY fullname ASC";

    return $DB->get_records_sql( $sql );

}

/**
 * Función retorna todos los programas asociados a los estudiantes de determinada instancia.
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @return Array (
 *      stdClass(
 *          ->cod_programa
 *          ->nombre_programa
 *      )
 * )
 */

function monitor_assignments_get_students_programs( $instance_id ){

    global $DB;

    $sql = "SELECT DISTINCT programa_0.cod_univalle AS cod_programa, programa_0.nombre AS nombre_programa
    FROM {talentospilos_programa} AS programa_0
    INNER JOIN
        (
            SELECT user_ext_0.id_moodle_user, user_ext_0.id_ases_user, user_ext_0.id_academic_program
            FROM {talentospilos_user_extended} AS user_ext_0
            INNER JOIN
                (
                    SELECT DISTINCT cohort_members_0.userid as user_id
                    FROM {cohort_members} AS cohort_members_0 
                    INNER JOIN
                        (
                            SELECT id_cohorte 
                            FROM {talentospilos_inst_cohorte} AS inst_cohorte_0 
                            WHERE id_instancia = $instance_id
                        ) AS inst_cohorte1
                    ON inst_cohorte1.id_cohorte = cohort_members_0.cohortid
                ) AS users_distinct_0
            ON users_distinct_0.user_id = user_ext_0.id_moodle_user
            WHERE user_ext_0.tracking_status = 1
        ) AS moodle_ases_user_0
    ON programa_0.id = moodle_ases_user_0.id_academic_program
    ORDER BY programa_0.nombre ASC";

    return $DB->get_records_sql( $sql );

}

/**
 * Función retorna todas las facultades asociadas a los programas académicos de los estudiantes en una determinada instancia.
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @return Array (
 *      stdClass(
 *          ->id_facultad
 *          ->nombre_facultad
 *      )
 * )
 */

function monitor_assignments_get_students_faculty( $instance_id ){

    global $DB;

    $sql = "SELECT DISTINCT facultad_0.id AS id_facultad, facultad_0.nombre AS nombre_facultad
    FROM {talentospilos_facultad} AS facultad_0
    INNER JOIN 
    (
        SELECT moodle_ases_user_0.id_moodle_user, moodle_ases_user_0.id_ases_user, programa_0.cod_univalle AS cod_programa, programa_0.nombre AS nombre_programa, id_facultad
        FROM {talentospilos_programa} AS programa_0
        INNER JOIN
        (
            SELECT *
            FROM {talentospilos_user_extended} AS user_ext_0
            INNER JOIN
            (
                SELECT DISTINCT cohort_members_0.userid as user_id
                FROM {cohort_members} AS cohort_members_0 
                INNER JOIN
                (
                    SELECT id_cohorte 
                    FROM {talentospilos_inst_cohorte} AS inst_cohorte_0 
                    WHERE id_instancia = $instance_id
                ) AS inst_cohorte1
                ON inst_cohorte1.id_cohorte = cohort_members_0.cohortid
            ) AS users_distinct_0
            ON users_distinct_0.user_id = user_ext_0.id_moodle_user
            WHERE user_ext_0.tracking_status = 1
        ) AS moodle_ases_user_0
        ON programa_0.id = moodle_ases_user_0.id_academic_program
    ) AS moodle_ases_user_programa_0
    ON facultad_0.id = moodle_ases_user_programa_0.id_facultad
    ORDER BY facultad_0.nombre ASC";

    return $DB->get_records_sql( $sql );

}

/**
 * Función retorna todas las facultades asociadas a los programas académicos de los monitores en una determinada instancia.
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @return Array (
 *      stdClass(
 *          ->id_facultad
 *          ->nombre_facultad
 *      )
 * )
 */

function monitor_assignments_get_monitors_faculty( $instance_id ){

    global $DB;

    $sql = "SELECT DISTINCT user_programa_0.id_facultad, facultad_0.nombre AS nombre_facultad
    FROM {talentospilos_facultad} AS facultad_0
    INNER JOIN (
            SELECT user_0.id, user_0.fullname, user_0.cod_programa, programa_0.nombre AS nombre_programa, programa_0.id_facultad
            FROM {talentospilos_programa} AS programa_0
            INNER JOIN (
                    SELECT CONCAT(moodle_user.firstname, CONCAT(' ', moodle_user.lastname)) AS fullname, moodle_user.id, cast(nullif(split_part(moodle_user.username, '-', 2), '') AS INTEGER) AS cod_programa
                    FROM {talentospilos_user_rol} AS user_rol
                    INNER JOIN {user} AS moodle_user ON moodle_user.id = user_rol.id_usuario
                    WHERE id_rol = (
                                SELECT id
                                FROM {talentospilos_rol} 
                                WHERE nombre_rol = 'monitor_ps'
                            )
                    AND id_instancia = $instance_id
                    AND id_semestre = ". get_current_semester()->max ." 
                    AND estado = 1 
                    ORDER BY fullname
                   ) AS user_0
            ON user_0.cod_programa = programa_0.cod_univalle
           ) AS user_programa_0
    ON user_programa_0.id_facultad = facultad_0.id
    ORDER BY nombre_facultad ASC";

    return $DB->get_records_sql( $sql );

}

/**
 * Función retorna todas los programas de los monitores en una determinada instancia.
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @return Array (
 *      stdClass(
 *          ->id_facultad
 *          ->nombre_facultad
 *      )
 * )
 */

function monitor_assignments_get_monitors_programs( $instance_id ){

    global $DB;

    $sql = "SELECT DISTINCT user_0.cod_programa, programa_0.nombre AS nombre_programa
    FROM {talentospilos_programa} AS programa_0
    INNER JOIN (
            SELECT CONCAT(moodle_user.firstname, CONCAT(' ', moodle_user.lastname)) AS fullname, moodle_user.id, cast(nullif(split_part(moodle_user.username, '-', 2), '') AS INTEGER) AS cod_programa
            FROM {talentospilos_user_rol} AS user_rol
            INNER JOIN {user} AS moodle_user ON moodle_user.id = user_rol.id_usuario
            WHERE id_rol = (
                        SELECT id
                        FROM {talentospilos_rol} 
                        WHERE nombre_rol = 'monitor_ps'
                    )
            AND id_instancia = $instance_id
            AND id_semestre = ". get_current_semester()->max ." 
            AND estado = 1 
            ORDER BY fullname
           ) AS user_0
    ON user_0.cod_programa = programa_0.cod_univalle
    ORDER BY nombre_programa ASC";

    return $DB->get_records_sql( $sql );

}

/**
 * Función retorna todas las relaciones monitor-estudiante del semestre actual en una instancia
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @return Array (
 *      stdClass(
 *          ->id_monitor
 *          ->id_estudiante
 *      )
 * )
 */

function monitor_assignments_get_monitors_students_relationship_by_instance( $instance_id ){

    global $DB;

    $sql = "SELECT id, id_monitor, id_estudiante 
    FROM {talentospilos_monitor_estud} 
    WHERE id_semestre = ". get_current_semester()->max ." AND id_instancia = $instance_id";
  
    return $DB->get_records_sql( $sql );;

}

/**
 * Función retorna todas las relaciones monitor-estudiante de un semestre indicado por instancia
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @return Array (
 *      stdClass()
 *          ->id_monitor
 *          ->id_estudiante
 * )
 */

function monitor_assignments_get_monitors_students_relationship_by_instance_n_semester( $instance_id, $semester_id ){

    global $DB;

    $sql = "SELECT id, id_monitor, id_estudiante 
    FROM {talentospilos_monitor_estud} 
    WHERE id_semestre = ". $semester_id ." AND id_instancia = $instance_id";
  
    return $DB->get_records_sql( $sql );

}

/**
 * Función que retorna todas las relaciones profesional-practicante del semestre actual en una instancia
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @return Array(
 * 	stdClass(
 *	    ->id_profesional
 * 	    ->id_practicante
 *	)
 * )
 */

function monitor_assignments_get_profesional_practicant_relationship_by_instance( $instance_id ){

    global $DB;

    $sql="SELECT user_rol_1.id, user_rol_1.id_jefe AS id_profesional, user_rol_1.id_usuario AS id_practicante
	  FROM {talentospilos_user_rol} AS user_rol_1
	  INNER JOIN (
		SELECT id_usuario
        	FROM {talentospilos_user_rol} AS user_rol_0
	 	WHERE id_rol = ( 
			SELECT id 
			FROM {talentospilos_rol} 
			WHERE nombre_rol = 'profesional_ps'
		)
	  AND id_instancia = $instance_id
      AND id_semestre = ". get_current_semester()->max . "
      AND estado = 1
	) AS profesionales_0
	ON profesionales_0.id_usuario = id_jefe
	WHERE user_rol_1.id_semestre = ". get_current_semester()->max;

    return $DB->get_records_sql( $sql );

}

/**
 * @param $instance_id  string|number Instance id @see talentospilos_instancia.id_instancia
 * @param $semester_name string Semester name, examples: [2018B, 2019A]
 * @throws dml_exception
 * @return DataTable
 */
function monitor_assignments_get_practicants_monitors_and_students_datatable($instance_id, $semester_name): DataTable {
    $objects = monitor_assignments_get_practicants_monitors_and_students($instance_id, $semester_name);
    $datatable = new DataTable($objects);
    return $datatable;
}

/**
 * Función que retorna todas las relaciones practicante-monitor del semestre actual en una instancia
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @return Array(
 * 	stdClass(
 *	    ->id_practicante
 * 	    ->id_monitor
 *	)
 * )
 */

function monitor_assignments_get_practicant_monitor_relationship_by_instance( $instance_id ){

    global $DB;

    $sql="SELECT user_rol_1.id, user_rol_1.id_jefe AS id_practicante, user_rol_1.id_usuario AS id_monitor
	  FROM {talentospilos_user_rol} AS user_rol_1
	  INNER JOIN (
		SELECT id_usuario
        	FROM {talentospilos_user_rol} AS user_rol_0
	 	WHERE id_rol = ( 
			SELECT id 
			FROM {talentospilos_rol} 
			WHERE nombre_rol = 'practicante_ps'
		)
	  AND id_instancia = $instance_id 
      AND id_semestre = ". get_current_semester()->max . "
      AND estado = 1
	) AS practicantes_0
	ON practicantes_0.id_usuario = id_jefe
	WHERE user_rol_1.id_semestre = ". get_current_semester()->max;

    return $DB->get_records_sql( $sql );

}

/**
 * Función que asigna un monitor a un estudiante en determinada instancia, en el semestre actual.
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @param int $monitor_id Monitor id.
 * @param int $student_id Student Ases id.
 * @return int id
 */

function monitor_assignments_create_monitor_student_relationship( $instance_id, $monitor_id, $student_id ){

    global $DB;

    $current_id_semester = get_current_semester()->max;

    $sql = "SELECT * 
            FROM {talentospilos_monitor_estud} 
            WHERE id_monitor = $monitor_id 
                AND id_estudiante = $student_id
                AND id_instancia = $instance_id
                AND id_semestre = $current_id_semester";
    
    $record = $DB->get_record_sql( $sql );

    if( !$record ){

        $new_relation = new stdClass();
        $new_relation->id_monitor = $monitor_id;
        $new_relation->id_estudiante = $student_id;
        $new_relation->id_instancia = $instance_id;
        $new_relation->id_semestre = $current_id_semester;

        return $DB->insert_record('talentospilos_monitor_estud', $new_relation, $returnid=true, $bulk=false);

    }else{

        return null;

    }

 }

 /**
 * Función que elimina la asignación de un monitor a un estudiante en determinada instancia, en el semestre actual.
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @param int $monitor_id Monitor id.
 * @param int $student_id Student Ases id.
 * @return int
 */

function monitor_assignments_delete_monitor_student_relationship( $instance_id, $monitor_id, $student_id ){

    global $DB;

    $current_id_semester = get_current_semester()->max;

    $sql = "SELECT * 
            FROM {talentospilos_monitor_estud} 
            WHERE id_monitor = $monitor_id 
                AND id_estudiante = $student_id
                AND id_instancia = $instance_id
                AND id_semestre = $current_id_semester";
    
    $record = $DB->get_record_sql( $sql );

    if( $record ){

        $conditions = array(
            'id_monitor' => $monitor_id,
            'id_estudiante' => $student_id,
            'id_instancia' => $instance_id,
            'id_semestre' => $current_id_semester
        );

        return $DB->delete_records('talentospilos_monitor_estud', $conditions);

    }else{
        return null;
    }

 }

 /**
 * Función que asigna un monitor a un practicante en determinada instancia, en el semestre actual.
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @param int $practicant_id Practicant id.
 * @param int $monitor_id Monitor id.
 * @return int id
 */

function monitor_assignments_create_practicant_monitor_relationship( $instance_id, $practicant_id, $monitor_id ){

    global $DB;

    $current_id_semester = get_current_semester()->max;

    $sql = "SELECT id FROM {talentospilos_rol} WHERE nombre_rol = 'monitor_ps'";
    $id_rol = $DB->get_record_sql( $sql )->id;

    // Indice triple sin validación de estado
    $sql = "SELECT * 
            FROM {talentospilos_user_rol} 
            WHERE id_rol = $id_rol
                AND id_usuario = $monitor_id
                AND estado = 1
                AND id_semestre = $current_id_semester
                AND id_jefe IS NULL
                AND id_instancia = $instance_id";
    
    $record = $DB->get_record_sql( $sql );

    if( $record ){

        $record->id_jefe = $practicant_id;

        return $DB->update_record('talentospilos_user_rol', $record, $bulk=false);

    }else{

        return null;

    }
    
 }


 /**
 * Función que elimina la asignación de un practicante a un monitor en determinada instancia, en el semestre actual.
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @param int $practicant_id Practicant id.
 * @param int $monitor_id Monitor id.
 * @return int
 */

function monitor_assignments_delete_practicant_monitor_relationship( $instance_id, $practicant_id, $monitor_id ){

    global $DB;

    $current_id_semester = get_current_semester()->max;

    $sql = "SELECT * 
            FROM {talentospilos_user_rol} 
            WHERE id_usuario = $monitor_id 
                AND estado = 1
                AND id_semestre = $current_id_semester
                AND id_jefe = $practicant_id
                AND id_instancia = $instance_id";
    
    $record = $DB->get_record_sql( $sql );

    if( $record ){

        $record->id_jefe = null;

        return $DB->update_record('talentospilos_user_rol', $record, $bulk=false);

    }else{
        return null;
    }

 }

/**
  * Función que permite transferir las asignaciones de un monitor a otro monitor, en determinada instancia, en el 
  * semestre actual.
  *
  * @param int $instance_id
  * @param int $old_monitor_id 
  * @param int $new_monitor_id
  */

function monitor_assignments_transfer( $instance_id, $old_monitor_id, $new_monitor_id ){

    global $DB;

    $current_id_semester = get_current_semester()->max;

    // Get old monitor asignations
    $sql = "SELECT * 
            FROM {talentospilos_monitor_estud} 
            WHERE id_semestre = ". get_current_semester()->max ." AND id_instancia = $instance_id AND id_monitor = $old_monitor_id";
  
    $asignations = $DB->get_records_sql( $sql );
    if( $asignations ){

        foreach($asignations as &$asignation){

            $asignation->id_monitor = $new_monitor_id;
            $DB->update_record('talentospilos_monitor_estud', $asignation, $bulk=false);

        }
        return 1;
    }else{
        return null;
    }
}

/**
 * Function that returns a list of students assigned to a specific monitor.
 * @param int instance_id
 * @param int monitor_id
 * @param int semester_id
 * @return array
 */

function monitor_assignments_get_students_from_monitor( $instance_id, $monitor_id, $semester_id ){
    global $DB;

    $sql = "SELECT id_estudiante AS id
    FROM {talentospilos_monitor_estud} 
    WHERE id_semestre = '$semester_id' AND id_instancia = '$instance_id' AND id_monitor = '$monitor_id'";

    $students = $DB->get_records_sql( $sql );
    
    return array_values( $students );
}

/**
 * Function that returns a list of monitors assigned to a specific practicant.
 * @param int instance_id
 * @param int practicant_id
 * @param int semester_id
 * @return array
 */

function monitor_assignments_get_monitors_from_practicant( $instance_id, $practicant_id, $semester_id ){
    global $DB;

    $sql="SELECT user_rol_1.id_usuario AS id
	  FROM {talentospilos_user_rol} AS user_rol_1
	  INNER JOIN (
		SELECT id_usuario
        	FROM {talentospilos_user_rol} AS user_rol_0
	 	WHERE id_rol = ( 
			SELECT id 
			FROM {talentospilos_rol} 
			WHERE nombre_rol = 'practicante_ps'
		)
	  AND id_instancia = '$instance_id' 
      AND id_usuario = '$practicant_id'
      AND id_semestre = '$semester_id'
      AND estado = 1
	) AS practicantes_0
	ON practicantes_0.id_usuario = id_jefe
    WHERE user_rol_1.id_semestre = '$semester_id'";
    
    $monitors = $DB->get_records_sql( $sql );

    return array_values( $monitors );
}

/**
 * Class MonitorAndStudentAndPracticant
 *
 * Dummy class for describe monitor_assignments_get_boss_and_students instances returned
 * @see monitor_assignments_get_boss_and_students
 * @property string $codigo_monitor
 * @property string $nombre_monitor
 * @property string $codigo_practicante
 * @property string $nombre_practicante
 * @property string $codigo_estudiante
 * @property string $nombre_estudiante
 */
abstract class MonitorAndStudentAndPracticant {

}
/**
 * Return monitor join practicante join estudiante_monitor
 *
 * ## Returned columns
 * - row_number
 * - nombre_usuario_moodle__profesional
 * - nombre_profesional
 * - codigo_monitor
 * - nombre_monitor
 * - codigo_practicante
 * - nombre_practicante
 * - codigo_estudiante
 * - nombre_estudiante
 * @param $instance_id  string|number Instance id @see talentospilos_instancia.id_instancia
 * @author Luis Gerardo Manrqiue Cardona <luis.manrique@corereounivalle.edu.co>
 * @param $semester_name string Semester name, examples: [2018B, 2019A]
 * @return array Items are described by MonitorAndStudentAndPracticant
 * @throws dml_exception
 */
function monitor_assignments_get_practicants_monitors_and_students($instance_id, $semester_name ) {
    global $DB;
    $sql = <<<SQL
select distinct
                row_number() over() as index  ,
                mdl_user_profesional.username as nombre_usuario_moodle__profesional,
                concat_ws(' ', mdl_user_profesional.firstname , mdl_user_profesional.lastname)  as nombre_profesional,
                mdl_user_practicante.username as codigo_practicante,
                concat_ws(' ', mdl_user_practicante.firstname , mdl_user_practicante.lastname)  as nombre_practicante,
                mdl_user_monitor.username as codigo_monitor,
                concat_ws(' ', mdl_user_monitor.firstname , mdl_user_monitor.lastname) as nombre_monitor ,
                mdl_user_estudiante.username as codigo_estudiante,
                concat_ws(' ', mdl_user_estudiante.firstname , mdl_user_estudiante.lastname)  as nombre_estudiante

from mdl_user as mdl_user_monitor
       inner join mdl_talentospilos_user_rol as mdl_talentospilos_user_rol_monitor
         on mdl_talentospilos_user_rol_monitor.id_usuario  = mdl_user_monitor.id
       inner join mdl_talentospilos_rol as mdl_talentospilos_rol_monitor
         on mdl_talentospilos_rol_monitor.id = mdl_talentospilos_user_rol_monitor.id_rol
       inner join mdl_talentospilos_semestre
         on mdl_talentospilos_semestre.id  = mdl_talentospilos_user_rol_monitor.id_semestre
       inner join mdl_talentospilos_instancia
         on mdl_talentospilos_instancia.id_instancia = mdl_talentospilos_user_rol_monitor.id_instancia
       inner join mdl_user as mdl_user_practicante
         on mdl_talentospilos_user_rol_monitor.id_jefe = mdl_user_practicante.id
       inner join mdl_talentospilos_user_rol as mdl_talentospilos_user_rol_practicante
              on mdl_talentospilos_user_rol_practicante.id_usuario = mdl_user_practicante.id
                     and mdl_talentospilos_user_rol_practicante.id_semestre = mdl_talentospilos_semestre.id
                     and mdl_talentospilos_user_rol_practicante.id_instancia = mdl_talentospilos_instancia.id_instancia
                     and mdl_talentospilos_user_rol_practicante.id_rol = (select id
                                                                          from mdl_talentospilos_rol as mdl_talentos_pilos_rol_practicante
                                                                          where mdl_talentos_pilos_rol_practicante.nombre_rol = 'practicante_ps')

       inner join mdl_user as mdl_user_profesional
              on mdl_talentospilos_user_rol_practicante.id_jefe = mdl_user_profesional.id
       inner join mdl_talentospilos_user_rol as mdl_talentospilos_user_rol_profesional
         on mdl_talentospilos_user_rol_profesional.id_usuario = mdl_user_profesional.id
                   and mdl_talentospilos_user_rol_profesional.id_semestre = mdl_talentospilos_semestre.id
                     and mdl_talentospilos_user_rol_profesional.id_instancia = mdl_talentospilos_instancia.id_instancia
                     and mdl_talentospilos_user_rol_profesional.id_rol = (select id
                                                                          from mdl_talentospilos_rol as mdl_talentos_pilos_rol_profesional
                                                                             where mdl_talentos_pilos_rol_profesional.nombre_rol = 'profesional_ps')
       inner join mdl_talentospilos_monitor_estud
              on mdl_user_monitor.id = mdl_talentospilos_monitor_estud.id_monitor
                     and mdl_talentospilos_monitor_estud.id_semestre = mdl_talentospilos_semestre.id
                     and mdl_talentospilos_monitor_estud.id_instancia = mdl_talentospilos_instancia.id_instancia
       inner join mdl_talentospilos_usuario as mdl_talentospilos_usuario_estudiante
         on mdl_talentospilos_usuario_estudiante.id = mdl_talentospilos_monitor_estud.id_estudiante
       inner join mdl_talentospilos_user_extended as mdl_talentospilos_user_extended_estudiante
         on mdl_talentospilos_user_extended_estudiante.id_ases_user = mdl_talentospilos_usuario_estudiante.id
              and mdl_talentospilos_user_extended_estudiante.tracking_status = 1
       inner join mdl_talentospilos_programa as mdl_talentospilos_programa_estudiante
         on mdl_talentospilos_programa_estudiante.id = mdl_talentospilos_user_extended_estudiante.id_academic_program
       inner join mdl_user as mdl_user_estudiante
         on mdl_talentospilos_user_extended_estudiante.id_moodle_user = mdl_user_estudiante.id
where
    mdl_talentospilos_rol_monitor.nombre_rol = 'monitor_ps'
 and mdl_talentospilos_instancia.id_instancia = :instance_id
and mdl_talentospilos_semestre.nombre = :semester_name;
SQL;
    $monitores_estudiates_y_practicantes = $DB->get_records_sql($sql, array(
        'instance_id' =>  $instance_id,
        'semester_name' => $semester_name));
    return array_values(
        $monitores_estudiates_y_practicantes
    );

}

/**
 * Return monitor join practicante join estudiante_monitor
 *
 * @param $instance_id  string|number Instance id @see talentospilos_instancia.id_instancia
 * @author Luis Gerardo Manrqiue Cardona <luis.manrique@corereounivalle.edu.co>
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @param $semester_name string Semester name, examples: [2018B, 2019A]
 * @return array Items are described by MonitorAndStudentAndPracticant
 * @throws dml_exception
 */
function monitor_assignments_get_practicants_monitors_and_studentsV2($instance_id, $semester_name ) {
    global $DB;
    $sql = <<<SQL
select distinct
                row_number() over() as index  ,
                mdl_user_profesional.id as moodle_id_profesional,
                concat_ws(' ', mdl_user_profesional.firstname , mdl_user_profesional.lastname)  as nombre_profesional,
                mdl_user_practicante.id as moodle_id_practicante,
                concat_ws(' ', mdl_user_practicante.firstname , mdl_user_practicante.lastname)  as nombre_practicante,
                mdl_user_monitor.id as moodle_id_monitor,
                concat_ws(' ', mdl_user_monitor.firstname , mdl_user_monitor.lastname) as nombre_monitor ,
                mdl_user_estudiante.username as codigo_estudiante,
                concat_ws(' ', mdl_user_estudiante.firstname , mdl_user_estudiante.lastname)  as nombre_estudiante,
                mdl_talentospilos_user_extended_estudiante.id_ases_user as codigo_ases

from mdl_user as mdl_user_monitor
       inner join mdl_talentospilos_user_rol as mdl_talentospilos_user_rol_monitor
         on mdl_talentospilos_user_rol_monitor.id_usuario  = mdl_user_monitor.id
       inner join mdl_talentospilos_rol as mdl_talentospilos_rol_monitor
         on mdl_talentospilos_rol_monitor.id = mdl_talentospilos_user_rol_monitor.id_rol
       inner join mdl_talentospilos_semestre
         on mdl_talentospilos_semestre.id  = mdl_talentospilos_user_rol_monitor.id_semestre
       inner join mdl_talentospilos_instancia
         on mdl_talentospilos_instancia.id_instancia = mdl_talentospilos_user_rol_monitor.id_instancia
       inner join mdl_user as mdl_user_practicante
         on mdl_talentospilos_user_rol_monitor.id_jefe = mdl_user_practicante.id
       inner join mdl_talentospilos_user_rol as mdl_talentospilos_user_rol_practicante
              on mdl_talentospilos_user_rol_practicante.id_usuario = mdl_user_practicante.id
                     and mdl_talentospilos_user_rol_practicante.id_semestre = mdl_talentospilos_semestre.id
                     and mdl_talentospilos_user_rol_practicante.id_instancia = mdl_talentospilos_instancia.id_instancia
                     and mdl_talentospilos_user_rol_practicante.id_rol = (select id
                                                                          from mdl_talentospilos_rol as mdl_talentos_pilos_rol_practicante
                                                                          where mdl_talentos_pilos_rol_practicante.nombre_rol = 'practicante_ps')

       inner join mdl_user as mdl_user_profesional
              on mdl_talentospilos_user_rol_practicante.id_jefe = mdl_user_profesional.id
       inner join mdl_talentospilos_user_rol as mdl_talentospilos_user_rol_profesional
         on mdl_talentospilos_user_rol_profesional.id_usuario = mdl_user_profesional.id
                   and mdl_talentospilos_user_rol_profesional.id_semestre = mdl_talentospilos_semestre.id
                     and mdl_talentospilos_user_rol_profesional.id_instancia = mdl_talentospilos_instancia.id_instancia
                     and mdl_talentospilos_user_rol_profesional.id_rol = (select id
                                                                          from mdl_talentospilos_rol as mdl_talentos_pilos_rol_profesional
                                                                             where mdl_talentos_pilos_rol_profesional.nombre_rol = 'profesional_ps')
       inner join mdl_talentospilos_monitor_estud
              on mdl_user_monitor.id = mdl_talentospilos_monitor_estud.id_monitor
                     and mdl_talentospilos_monitor_estud.id_semestre = mdl_talentospilos_semestre.id
                     and mdl_talentospilos_monitor_estud.id_instancia = mdl_talentospilos_instancia.id_instancia
       inner join mdl_talentospilos_usuario as mdl_talentospilos_usuario_estudiante
         on mdl_talentospilos_usuario_estudiante.id = mdl_talentospilos_monitor_estud.id_estudiante
       inner join mdl_talentospilos_user_extended as mdl_talentospilos_user_extended_estudiante
         on mdl_talentospilos_user_extended_estudiante.id_ases_user = mdl_talentospilos_usuario_estudiante.id
              and mdl_talentospilos_user_extended_estudiante.tracking_status = 1
       inner join mdl_talentospilos_programa as mdl_talentospilos_programa_estudiante
         on mdl_talentospilos_programa_estudiante.id = mdl_talentospilos_user_extended_estudiante.id_academic_program
       inner join mdl_user as mdl_user_estudiante
         on mdl_talentospilos_user_extended_estudiante.id_moodle_user = mdl_user_estudiante.id
        
where
        mdl_talentospilos_rol_monitor.nombre_rol = 'monitor_ps'
    and mdl_talentospilos_instancia.id_instancia = :instance_id
    and mdl_talentospilos_semestre.nombre = :semester_name;
SQL;
    $monitores_estudiates_y_practicantes = $DB->get_records_sql($sql, array(
        'instance_id' =>  $instance_id,
        'semester_name' => $semester_name));
    return array_values(
        $monitores_estudiates_y_practicantes
    );

}

/**
 * Function that returns a list of practicants assigned to a specific professional.
 * @param int instance_id
 * @param int professional_id
 * @param int semester_id
 * @return array
 */

function monitor_assignments_get_practicants_from_professional( $instance_id, $professional_id, $semester_id ){
    global $DB;

    $sql="SELECT user_rol_1.id_usuario AS id
	  FROM {talentospilos_user_rol} AS user_rol_1
	  INNER JOIN (
		SELECT id_usuario
        	FROM {talentospilos_user_rol} AS user_rol_0
	 	WHERE id_rol = ( 
			SELECT id 
			FROM {talentospilos_rol} 
			WHERE nombre_rol = 'profesional_ps'
		)
	  AND id_instancia = '$instance_id'
      AND id_usuario = '$professional_id'
      AND id_semestre = '$semester_id'
      AND estado = 1
	) AS profesionales_0
	ON profesionales_0.id_usuario = id_jefe
    WHERE user_rol_1.id_semestre = '$semester_id'";
    
    $practicants = $DB->get_records_sql( $sql );

    return array_values( $practicants );
}

/**
 * 
 */
function monitor_assignments_get_last_student_assignment( $id_ases, $instance_id ){

    global $DB;
    $to_return = [
        "monitor_obj" => null,
        "pract_obj" => null,
        "prof_obj" => null
    ];

    $sql_relationship = "SELECT id, id_monitor, id_estudiante, id_instancia, id_semestre 
    FROM {talentospilos_monitor_estud} 
    WHERE id_estudiante = '$id_ases'
    AND id_instancia = '$instance_id'
    ORDER BY id_semestre DESC
    LIMIT 1";

    $mon_est_relationship = $DB->get_record_sql( $sql_relationship );

    if( $mon_est_relationship ){

        $monitor_id = $mon_est_relationship->id_monitor;
        $semester_id = $mon_est_relationship->id_semestre;

        $sql_monitor = "SELECT id, username, firstname, lastname 
        FROM {user} 
        WHERE id  = '$monitor_id'";

        $monitor_obj = $DB->get_record_sql( $sql_monitor );
        $to_return['monitor_obj'] =  $monitor_obj;
        
        $sql_relationship = "SELECT * 
        FROM {talentospilos_user_rol}
        WHERE id_usuario = '$monitor_id'
        AND id_semestre = '$semester_id'
        AND id_instancia = '$instance_id'";

        $mon_pract_relationship = $DB->get_record_sql( $sql_relationship );
        
        if( $mon_pract_relationship ){

            $pract_id = $mon_pract_relationship->id_jefe;

            $sql_practicant = "SELECT id, username, firstname, lastname 
            FROM {user} 
            WHERE id  = '$pract_id'";

            $pract_obj = $DB->get_record_sql( $sql_practicant );
            $to_return['pract_obj'] =  $pract_obj;

            $sql_relationship = "SELECT * 
            FROM {talentospilos_user_rol}
            WHERE id_usuario = '$pract_id'
            AND id_semestre = '$semester_id'
            AND id_instancia = '$instance_id'";

            $pract_prof_relationship = $DB->get_record_sql( $sql_relationship );
            
            if( $pract_prof_relationship ){

                $prof_id = $pract_prof_relationship->id_jefe;
                
                $sql_professional = "SELECT id, username, firstname, lastname 
                FROM {user} 
                WHERE id  = '$prof_id'";

                $prof_obj = $DB->get_record_sql( $sql_professional );
                $to_return['prof_obj'] =  $prof_obj;

            }

        }

        
    }

    return $to_return;

}

/**
* Function that given an ASES student id, instance id and semester id, return the monitor assigned.
* @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
* @param integer $instance_id 
* @param integer $student_id ASES id.
* @param integer $semester_id
* @param stdClass | NULL
*/
function monitor_assignments_get_monitor_by_student( $instance_id, $student_id, $semester_id ){
    global $DB;

    $sql = "SELECT id_monitor AS id
    FROM {talentospilos_monitor_estud} 
    WHERE id_semestre = '$semester_id' AND id_instancia = '$instance_id' AND id_estudiante = '$student_id'";

    $monitor = $DB->get_record_sql( $sql );
    
    return ( property_exists($monitor, "id") ? $monitor : null );
}

/**
* Function that given an ASES student id and instance id, return the current monitor assigned.
* @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
* @see monitor_assignments_get_monitor_by_student(...) in this file.
* @see periods_get_current_semester(...) in periods_lib.php
* @param integer $instance_id 
* @param integer $student_id ASES id.
* @param stdClass | NULL
*/
function monitor_assignments_get_current_monitor_by_student( $instance_id, $student_id ){
    
    $current_semester = periods_get_current_semester();
    return monitor_assignments_get_monitor_by_student( $instance_id, $student_id, $current_semester->id )

}

?>
