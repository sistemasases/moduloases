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
 * Cohort lib
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Función retorna las cohortes asignadas a una instancia
 *
 * @see load_cohorts_by_instance()
 * @param string|int $id_instance ID instancia
 * @return array
 * @throws \dml_exception
 */
namespace cohort_lib;
require_once(__DIR__.'/../instance_management/instance_lib.php');
use function substr;

function load_cohorts_by_instance($id_instance){

    global $DB;

    $result_to_return = array();

    $sql_query = "SELECT t_cohort.name, t_cohort.idnumber
                  FROM {talentospilos_inst_cohorte} AS instance_cohort
                  INNER JOIN {cohort} AS t_cohort ON t_cohort.id = instance_cohort.id_cohorte
                  WHERE id_instancia = $id_instance
                  ORDER BY t_cohort.name ASC";

    $result_query = $DB->get_records_sql($sql_query);

    foreach($result_query as $cohort){
        $controls_html = "";
        $controls_html .= "<span class='unassigned_cohort glyphicon glyphicon-remove' id='$cohort->idnumber'";
        $controls_html .= "style='color:red'></span>";
        $cohort->controls_column = $controls_html;
        array_push($result_to_return, $cohort);
    }

    return $result_to_return;
}

/**
 * Return string than represents the start date in the given semester.
 *
 ** The normal format for ASES cohorts have four chars for a specific cohort id, the next four numbers represent the year
 *  of the cohort and the last char is the period than can be A or B, first semester of the year and last semester of
 *  the year respectively.
 *
 ** In other cases are more or less than four numbers in the cohort id, but never can be than the last char was not the
 *  period (first or last semester), and the penultimate four numbers can be always the year.
 *
 ** With the above, for the first semester of the year **Y-01-01** is returned and for the lst semester of the year
 * **Y-06-01** is returned
 *
 * **If for some annunaki reason the last character (period) is not B and not A, 01 is returned in the month place
 *
 * @param string $mdl_cohort_id_number String than represents the moodle cohort id number
 * @param string $format Format for the string date
 * @return string Example: '2018-06-01'
 * @see mdl_cohort
 */
function get_date_string_from_mdl_cohort_id_number($mdl_cohort_id_number) {
    $year_and_period = substr($mdl_cohort_id_number,  -5);
    $period_string /* A or B */ = substr($year_and_period, -1);
    $month = $period_string === 'B'? '06': '01';
    $year = substr_replace($year_and_period ,"", -1);
    return $year.'-'.$month.'-01';
}

/**
 * Función que genera el select de html y lo retorna para las cohortes de una instancia en particular
 * @param string $instance_id
 * @param bool $include_todos Include todos for subcategories or not
 * @return string Html base de el select a mostrar
 */

function get_html_cohorts_select($instance_id,$include_todos=true,  $name='conditions[]', $id='conditions', $class = 'form-control' ) {
    $cohorts = load_cohorts_by_instance($instance_id);
    $info_instance = \get_info_instance($instance_id);
    $cohorts_select = "<select name=\"$name\" id=\"$id\" class=\"$class\">" ;
    if($info_instance->id_number == 'ases'){

        $cohorts_groups = array(['id'=>'SPP', 'name'=>'Ser Pilo Paga'],
            ['id'=>'SPE', 'name'=>'Condición de Excepción'],
            ['id'=>'3740', 'name'=>'Ingeniería Topográfica'],
            ['id'=>'OTROS', 'name'=>'Otros ASES']);

        if($include_todos) {
            $cohorts_select.='<option value="TODOS">Todas las cohortes</option>';
        }

        foreach($cohorts_groups as $cohort_group){
            $cohorts_select.="<optgroup label='".$cohort_group['name']."'>";
            if($include_todos) {
                $cohorts_select .= "<option value='TODOS-".$cohort_group['id']."'>Todos ".$cohort_group['id']."</option>";
            }

            foreach($cohorts as $ch){
                if(substr($ch->idnumber, 0, 3) == substr($cohort_group['id'], 0, 3)){
                    $cohorts_select.= "<option value='$ch->idnumber'>$ch->name</option>";
                }
            }

            $cohorts_select.="</optgroup>";
        }

    }else{
        foreach($cohorts as $ch){
            $cohorts_select.= "<option value='$ch->idnumber'>$ch->name</option>";
        }
    }

    $cohorts_select.='</select>';
    return $cohorts_select;
}