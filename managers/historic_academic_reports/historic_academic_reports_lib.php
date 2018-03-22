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
 * Estrategia ASES
 *
 * @author     Camilo José Cruz Rivera
 * @package    block_ases
 * @copyright  2018 Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once __DIR__ . '/../../../../config.php';
require_once '../managers/student_profile/academic_lib.php';

/**
 * Función que recupera datos para la tabla de reporte historico academico por estudiantes,
 * de los estudiantes asignados a una cohorte de las relacionadas con la instancia
 *
 * @see get_datatable_array_Students($instance_id)
 * @param $instance_id  --> Instancia del módulo
 * @return Array
 */
function get_datatable_array_Students($instance_id)
{
    $default_students = $columns = array();
    array_push($columns, array("title" => "Número de documento", "name" => "num_doc", "data" => "num_doc"));
    array_push($columns, array("title" => "Código estudiante", "name" => "username", "data" => "username"));
    array_push($columns, array("title" => "Nombre(s)", "name" => "firstname", "data" => "firstname"));
    array_push($columns, array("title" => "Apellido(s)", "name" => "lastname", "data" => "lastname"));
    array_push($columns, array("title" => "Semestre", "name" => "semestre", "data" => "semestre"));
    array_push($columns, array("title" => "Cancela", "name" => "cancel", "data" => "cancel"));
    array_push($columns, array("title" => "Promedio Semestre", "name" => "promSem", "data" => "promSem"));
    array_push($columns, array("title" => "Gano Estimulo", "name" => "estim", "data" => "estim"));
    array_push($columns, array("title" => "Cae en Bajo", "name" => "bajo", "data" => "bajo"));
    array_push($columns, array("title" => "Promedio Acumulado", "name" => "promAcum", "data" => "promAcum"));
    array_push($columns, array("title" => "Estimulos", "name" => "Numestim", "data" => "estim"));
    array_push($columns, array("title" => "Bajos", "name" => "bajos", "data" => "bajos"));
    array_push($columns, array("title" => "Materias Perdidas", "name" => "perdidas", "data" => "perdidas"));
    array_push($columns, array("title" => "Programa", "name" => "programa", "data" => "programa"));
    array_push($columns, array("title" => "Cohorte", "name" => "cohorte", "data" => "cohorte"));

    $default_students = get_historic_report($instance_id);

    $data_to_table = array(
        "bsort" => false,
        "data" => $default_students,
        "columns" => $columns,
        "select" => "false",
        "fixedHeader" => array(
            "header" => true,
            "footer" => true,
        ),
        "language" => array(
            "search" => "Buscar:",
            "oPaginate" => array(
                "sFirst" => "Primero",
                "sLast" => "Último",
                "sNext" => "Siguiente",
                "sPrevious" => "Anterior",
            ),
            "sProcessing" => "Procesando...",
            "sLengthMenu" => "Mostrar _MENU_ registros",
            "sZeroRecords" => "No se encontraron resultados",
            "sEmptyTable" => "Ningún dato disponible en esta tabla",
            "sInfo" => "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty" => "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered" => "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix" => "",
            "sSearch" => "Buscar:",
            "sUrl" => "",
            "sInfoThousands" => ",",
            "sLoadingRecords" => "Cargando...",
            "oAria" => array(
                "sSortAscending" => ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending" => ": Activar para ordenar la columna de manera descendente",
            ),
        ),
        "autoFill" => "true",
        "dom" => "lfrtBip",
    );

    return $data_to_table;

}

/**
 * Retorna un arreglo con la informacion de la tabla de historico academico
 * @see get_historic_report($id_instance)
 * @param $id_instance --> id del modulo
 * @return Array --> info historic_academic_report
 */

function get_historic_report($id_instance)
{

    global $DB;
    $array_historic = array();

    $query = "  SELECT     historic.id as id,
                           programa.id as program_id,
                           usuario.id as student_id,
                           num_doc,
                           substring(username from 1 FOR 7) AS username,
                           firstname,
                           lastname,
                           semestre.nombre    AS semestre,
                           promedio_semestre  AS promsem,
                           promedio_acumulado AS promacum,
                           programa.nombre    AS programa,
                           cohorte.NAME       AS cohorte,
                           json_materias
                FROM       {talentospilos_history_academ} historic
                INNER JOIN {talentospilos_usuario} usuario
                ON         historic.id_estudiante = usuario.id
                INNER JOIN {talentospilos_semestre} semestre
                ON         historic.id_semestre = semestre.id
                INNER JOIN {talentospilos_programa} programa
                ON         historic.id_programa = programa.id
                INNER JOIN {talentospilos_user_extended} user_ext
                ON         historic.id_estudiante = user_ext.id_ases_user
                INNER JOIN {user} user_moodle
                ON         user_ext.id_moodle_user = user_moodle.id
                INNER JOIN {cohort_members} memb
                ON         memb.userid = user_moodle.id
                INNER JOIN {cohort} cohorte
                ON         memb.cohortid = cohorte.id";

    $historics = $DB->get_records_sql($query);

    foreach ($historics as $historic) {

        //validate cancel
        $query_cancel = "SELECT * FROM {talentospilos_history_cancel} WHERE id_history = $historic->id ";

        $cancel = $DB->get_record_sql($query_cancel);

        if ($cancel) {
            $historic->cancel = $cancel->fecha_cancelacion;
        } else {
            $historic->cancel = "NO";
        }

        //validate estimulo
        $query_estimulo = "SELECT * FROM {talentospilos_history_estim} WHERE id_history = $historic->id ";

        $estimulo = $DB->get_records_sql($query_estimulo);

        if ($estimulo) {
            $historic->estim = $estimulo->puesto_ocupado;
        } else {
            $historic->estim = "NO";
        }

        //validate bajo
        $query_bajo = "SELECT * FROM {talentospilos_history_bajos} WHERE id_history = $historic->id ";

        $bajo = $DB->get_records_sql($query_bajo);

        if ($bajo) {
            $historic->bajo = "Bajo num: $bajo->numero_bajo";
        } else {
            $historic->bajo = "NO";
        }

        //validate estimulos
        $estimulos = get_estimulos($historic->student_id, $historic->program_id);
        $historic->Numestim = $estimulos;

        //validate bajos
        $bajos = get_bajos_rendimientos($historic->student_id, $historic->program_id);
        $historic->bajos = $bajos;

        //validate materias perdidas
        $materias = json_decode($historic->json_materias);
        $perdidas = 0;

        foreach ($materias as $materia) {
            if ($materia->nota < 3) {
                $perdidas++;
            }
        }

        $historic->perdidas = $perdidas;
        array_push($array_historic, $historic);
    }

    return $array_historic;
}

/**
 * Función que recupera datos para la tabla de reporte historico academico por totales,
 * de las cohortes relacionadas con la instancia
 *
 * @see get_datatable_array_totals($instance_id)
 * @param $instance_id  --> Instancia del módulo
 * @return Array
 */
function get_datatable_array_totals($instance_id)
{
    $default_students = $columns = array();
    array_push($columns, array("title" => "Cohorte", "name" => "cohorte", "data" => "cohorte"));
    array_push($columns, array("title" => "Semestre", "name" => "semestre", "data" => "semestre"));
    array_push($columns, array("title" => "Total Activos", "name" => "act", "data" => "act"));
    array_push($columns, array("title" => "Total Inactivos", "name" => "inact", "data" => "inact"));
    array_push($columns, array("title" => "Total Cohorte", "n" => "total", "data" => "total"));

    $default_students = get_Totals_report($instance_id);

    $data_to_table = array(
        "bsort" => false,
        "data" => $default_students,
        "columns" => $columns,
        "select" => "false",
        "fixedHeader" => array(
            "header" => true,
            "footer" => true,
        ),
        "language" => array(
            "search" => "Buscar:",
            "oPaginate" => array(
                "sFirst" => "Primero",
                "sLast" => "Último",
                "sNext" => "Siguiente",
                "sPrevious" => "Anterior",
            ),
            "sProcessing" => "Procesando...",
            "sLengthMenu" => "Mostrar _MENU_ registros",
            "sZeroRecords" => "No se encontraron resultados",
            "sEmptyTable" => "Ningún dato disponible en esta tabla",
            "sInfo" => "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty" => "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered" => "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix" => "",
            "sSearch" => "Buscar:",
            "sUrl" => "",
            "sInfoThousands" => ",",
            "sLoadingRecords" => "Cargando...",
            "oAria" => array(
                "sSortAscending" => ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending" => ": Activar para ordenar la columna de manera descendente",
            ),
        ),
        "autoFill" => "true",
        "dom" => "lfrtBip",
    );

    return $data_to_table;

}

/**
 * Retorna un arreglo con la informacion de la tabla de historico academico
 * @see get_Totals_report($instance_id)
 * @param $id_instance --> id del modulo
 * @return Array --> info totals_historic_academic_report
 */

function get_Totals_report($instance_id)
{

    global $DB;
    $array_historic = array();

    $query = "SELECT semestre.nombre as semestre,
                     cohorte.name as cohorte,
                     COUNT(academ.id) as total
              FROM {talentospilos_history_academ} academ
              INNER JOIN {talentospilos_semestre} semestre
              ON         academ.id_semestre = semestre.id
              INNER JOIN {talentospilos_user_extended} extend
              ON         academ.id_estudiante = extend.id_ases_user
              INNER JOIN {cohort_members} memb
              ON         memb.userid = extend.id_moodle_user
              INNER JOIN {cohort} cohorte
              ON         memb.cohortid = cohorte.id
              GROUP BY semestre, cohorte";

    $historics = $DB->get_records_sql($query);

    foreach ($historics as $historic) {

        $query_cancel = "SELECT COUNT(cancel.id) as inact,
                                semestre.nombre as semestre,
                                cohorte.name as cohorte
                                FROM {talentospilos_history_academ} academ
                        INNER JOIN {talentospilos_semestre} semestre
                        ON         academ.id_semestre = semestre.id
                        INNER JOIN {talentospilos_history_cancel} cancel
                        ON         academ.id = cancel.id_history 
                        INNER JOIN {talentospilos_user_extended} extend
                        ON         academ.id_estudiante = extend.id_ases_user
                        INNER JOIN {cohort_members} memb
                        ON         memb.userid = extend.id_moodle_user
                        INNER JOIN {cohort} cohorte
                        ON         memb.cohortid = cohorte.id
                        WHERE semestre.nombre = '$historic->semestre' AND cohorte.name = '$historic->cohorte'
                        GROUP BY semestre, cohorte";

        $inact = $DB->get_record_sql($query_cancel);

        if(!$inact){
            $historic->inact = 0;
            $historic->act = $historic->total;
        }else{
            $historic->inact = $inact->inact;   
            $historic->act = $historic->total - $inact->inact;
        }

        array_push($array_historic, $historic);

        //

    }

    return $array_historic;

}
