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
require_once (__DIR__ . '/../student/student_lib.php');
require_once (__DIR__ . '/../jquery_datatable/jquery_datatable_lib.php');
require_once (__DIR__ . '/../../managers/cohort/cohort_lib.php');
use jquery_datatable\Column;
use student_lib\ActiveSemestersReportField;
use function student_lib\get_active_semesters;

/**
 * Report active semesters functions
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2016 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
function _get_semesters_names_after_cohort($id_instance, $ases_cohort_id) {
    $date_format = 'Y-m-d';
    $mdl_and_ases_cohorts = _get_ases_cohorts_inner_mdl_cohorts($id_instance, $ases_cohort_id);
    $mdl_and_ases_cohort = array_values($mdl_and_ases_cohorts)[0];
    $cohort_start_date_string = \cohort_lib\get_date_string_from_mdl_cohort_id_number($mdl_and_ases_cohort->idnumber);
    $semesters = Semestre::get_semesters_later_than($cohort_start_date_string, -1, false, $date_format);
    $semester_names = array_map(
        function(Semestre $semester ) {
            return $semester->nombre;
        }, $semesters);
    return $semester_names;
}
require_once (__DIR__ . '/../../classes/Semestre.php');
/**
 * @param $semester_names
 * @param $student_and_active_semesters ActiveSemestersReportField
 */
function _student_and_active_semesters_to_row($semester_names, $student_and_active_semesters) {
    $row = array();
    foreach ($semester_names as $semester_name) {
        $row[$semester_name] = $student_and_active_semesters->have_active_semester($semester_name)? 'SI': 'NO';
    }
    $row['num_doc'] = $student_and_active_semesters->num_doc;
    $row['nombre'] = $student_and_active_semesters->nombre;
    $row['codigo'] = $student_and_active_semesters->codigo;
    return $row;
}
function _students_and_active_semesters_to_rows($semester_names, $students_and_active_semesters) {
    $rows = array();
    foreach($students_and_active_semesters as $student_and_active_semesters) {
        array_push($rows, _student_and_active_semesters_to_row($semester_names, $student_and_active_semesters));
    }
    return $rows;
}

/**
 * @param $id_instance
 * @param $ases_cohort_id
 * @return array Array with items dataTable and semesters, where semesters are array of string with semester names
 */
function get_report_active_semesters_report($id_instance, $ases_cohort_id) {

    $semester_names = _get_semesters_names_after_cohort($id_instance, $ases_cohort_id);
    $students_and_active_semesters = get_active_semesters($id_instance, $ases_cohort_id);

    $common_language_config = \jquery_datatable\get_datatable_common_language_config();
    $columns = array();
    $nombre_column =  new Column('nombre', 'Nombre', null, 'Nombre de el estudiante');
    $data = _students_and_active_semesters_to_rows($semester_names, $students_and_active_semesters);

    array_push($columns,
        new Column('codigo','Código' , null, 'Código de el estudiante'));
    array_push($columns, $nombre_column  );
    array_push($columns,
        new Column('num_doc', 'Número de documento',  null, 'Número de documento'));
    foreach($semester_names as $semester_name) {
        array_push($columns, new \jquery_datatable\Column($semester_name));
    }
    $nombre_index_column = array_search($nombre_column,  $columns);
    $data_table = array(
        "bsort" => false,
        "columns" => $columns,
        "data" => $data,
        "language" => $common_language_config,
        "semesters" => $semester_names,
        "order"=> array($nombre_index_column, "desc")

    );
    return array('dataTable'=>$data_table, 'semesters'=>$semester_names);
}

function _get_ases_cohorts_inner_mdl_cohorts($id_instance, $ases_cohort_id) {
    global $DB;
    $sql = <<<SQL
    select * from mdl_cohort
inner join mdl_talentospilos_inst_cohorte
on mdl_talentospilos_inst_cohorte.id_cohorte = mdl_cohort.id
 WHERE mdl_talentospilos_inst_cohorte.id_instancia = $id_instance
 and mdl_cohort.idnumber= '$ases_cohort_id'
SQL;
    return $DB->get_records_sql($sql);
}
