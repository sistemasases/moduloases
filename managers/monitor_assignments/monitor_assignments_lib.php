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
require_once $CFG->dirroot.'/blocks/ases/managers/lib/student_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/user_management/user_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/ases_report/asesreport_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/lib/lib.php';

/**
 * Función que renombra para clasificar la función get_professionals_by_instance en otras partes del plugin, el objetivo
 * es dibujar un mapa de seguimiento para saber de donde provienen las funciones
 *
 * @see monitor_assignments_get_professionals_by_instance 
 * @param $instance_id --> Identificador de instancia
 * @return Array 
 */

function monitor_assignments_get_professionals_by_instance( $instance_id ){
    // This function is in asesreport_lib.php
    return get_professionals_by_instance( $instance_id );
}

/**
 * Función que renombra para clasificar la función get_practicing_by_instance en otras partes del plugin, el objetivo
 * es dibujar un mapa de seguimiento para saber de donde provienen las funciones
 *
 * @see monitor_assignments_get_practicing_by_instance 
 * @param $instance_id --> Identificador de instancia
 * @return Array 
 */

function monitor_assignments_get_practicing_by_instance( $instance_id ){
    // This function is in asesreport_lib.php
    return get_practicing_by_instance( $instance_id );
}

/**
 * Función que renombra para clasificar la función get_monitors_by_instance en otras partes del plugin, el objetivo
 * es dibujar un mapa de seguimiento para saber de donde provienen las funciones
 *
 * @see monitor_assignments_get_monitors_by_instance 
 * @param $instance_id --> Identificador de instancia
 * @return Array 
 */

function monitor_assignments_get_monitors_by_instance( $instance_id ){
    // This function is in asesreport_lib.php
    return get_monitors_by_instance( $instance_id );
}

/**
 * Función que retorna todos los usuarios del sistema.
 *
 * @see monitor_assignments_get_students_by_instance 
 * @param $instance_id --> Identificador de instancia
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

    $sql = "SELECT CONCAT(moodle_user_0.firstname, CONCAT(' ', moodle_user_0.lastname)) AS fullname, moodle_ases_user_programa_0.id_ases_user AS id, moodle_ases_user_programa_0.cod_programa, moodle_ases_user_programa_0.nombre_programa
    FROM {user} AS moodle_user_0
    INNER JOIN 
        (
            SELECT moodle_ases_user_0.id_moodle_user, moodle_ases_user_0.id_ases_user, programa_0.cod_univalle AS cod_programa, programa_0.nombre AS nombre_programa
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
        ) AS moodle_ases_user_programa_0
    ON moodle_user_0.id = moodle_ases_user_programa_0.id_moodle_user";

    return $DB->get_records_sql($sql);

}

/**
 * Función retorna todos los programas asociados a los estudiantes.
 *
 * @see monitor_assignments_get_students_programs 
 * @param $instance_id --> Identificador de instancia
 * @return Array (
 *      stdClass(
 *          ->cod_univalle
 *          ->nombre_programa
 *      )
 * )
 */

function monitor_assignments_get_students_programs( $instance_id ){

    global $DB;

    $sql = "SELECT DISTINCT programa_0.cod_univalle, programa_0.nombre AS nombre_programa
    FROM mdl_talentospilos_programa AS programa_0
    INNER JOIN
        (
            SELECT user_ext_0.id_moodle_user, user_ext_0.id_ases_user, user_ext_0.id_academic_program
            FROM mdl_talentospilos_user_extended AS user_ext_0
            INNER JOIN
                (
                    SELECT DISTINCT cohort_members_0.userid as user_id
                    FROM mdl_cohort_members AS cohort_members_0 
                    INNER JOIN
                        (
                            SELECT id_cohorte 
                            FROM mdl_talentospilos_inst_cohorte AS inst_cohorte_0 
                            WHERE id_instancia = 450299
                        ) AS inst_cohorte1
                    ON inst_cohorte1.id_cohorte = cohort_members_0.cohortid
                ) AS users_distinct_0
            ON users_distinct_0.user_id = user_ext_0.id_moodle_user
            WHERE user_ext_0.tracking_status = 1
        ) AS moodle_ases_user_0
    ON programa_0.id = moodle_ases_user_0.id_academic_program";

    return $DB->get_records_sql($sql);

}

?>