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
/**
 * Prefix for group of cohorts, for example:
 * TODOS_PREFIX.'-SPP' are all cohorts in ser pilo paga
 */
const TODOS_PREFIX = 'TODOS';
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
function valid_cohort_id_number($cohort_id_number) {
    return preg_match(get_cohort_id_number_regex(), $cohort_id_number) === 1;
}
function get_cohort_id_number_regex() {
    return '/.{4}[0-9]{4}[AB]/';
}
/**
 * Return the cohort prefix from a cohort name
 *
 * For example, if 'SPP32017A' is given, the return value is 'SPP', if 'TODOS_PREFIX-SPP' is given
 * 'SPP' is returned, if 'TODOS' is given, 'TODOS' is returned
 * @param $cohort_name string  Can be a cohort name or TODOS_PREFIX-COHORT_PREFIX
 * @return string Cohort name prefix if cohort
 */
function get_cohort_name_prefix($cohort_name) {
    if(is_todos_cohort($cohort_name)){
        if(TODOS_PREFIX === $cohort_name) {
            return $cohort_name;
        }
        $values = explode('-', $cohort_name);
        return $values[1];
    } else {
        return substr($cohort_name, 0, 3);
    }
}

/**
 *
 * Return the first cohort of a cohort group, first with respect to creation order
 *
 * ## Fields returned
 * - mdl_cohort.*
 *
 * ## Tables joined
 * - mdl_cohort
 * - mdl_talentospilos_inst_cohorte
 * @param string $cohort_group_name Should be a name for cohort group with TODOS prefix
 * @return bool|mixed If the name is not for cohort group return false, return mdl_cohort instance otherwise
 * @see is_todos_cohort()
 * @throws \dml_exception
 */
function get_first_cohort_for_cohort_group(string $cohort_group_name) {
    global $DB;
    if(!is_todos_cohort($cohort_group_name)) {
        return false;
    }
    $sql_where_for_cohorts = '';
    $cohort_group_prefix = get_cohort_name_prefix($cohort_group_name);
    if($cohort_group_prefix === TODOS_PREFIX) {
        $sql_where_for_cohorts = '';
    } else {
        $sql_where_for_cohorts = "where mdl_cohort.idnumber like '$cohort_group_prefix%'";
    }
    $sql = <<<SQL
    select mdl_cohort.* from mdl_cohort
      inner join mdl_talentospilos_inst_cohorte
on mdl_talentospilos_inst_cohorte.id_cohorte = mdl_cohort.id
    $sql_where_for_cohorts
    order by mdl_cohort.id
SQL;
    $cohorts_ =  $DB->get_records_sql($sql);
    $cohorts = array_values($cohorts_);
    $valid_cohorts = array_filter($cohorts, function($cohort) {
        return valid_cohort_id_number($cohort->idnumber);
    });
    $cohort_sort_by_date = function ($cohort_a, $cohort_b) {
        $cohort_a_time_str = get_date_string_from_mdl_cohort_id_number($cohort_a->idnumber);
        $cohort_b_time_str = get_date_string_from_mdl_cohort_id_number($cohort_b->idnumber);

        return strtotime($cohort_a_time_str) - strtotime($cohort_b_time_str);
    };
    usort($valid_cohorts, $cohort_sort_by_date);
    $first_cohort_id = $valid_cohorts[0]->id;
   return $cohorts_[$first_cohort_id];
}
/**
 * Check if the value of cohort is a cohort id or a todos cohort
 * Example: if $cohort_value is TODOS-SPP return true, basicaly
 * return true if the $cohort_value start in TODOS
 * @param $cohort_value string Can be a cohort name or TODOS-* value
 * @return bool
 */
function is_todos_cohort($cohort_value): bool {
    $todos_cohort_prefix = \cohort_lib\TODOS_PREFIX;
    return substr($cohort_value, 0, strlen($todos_cohort_prefix)) === $todos_cohort_prefix;
}
/**
 * Return the cohort groups knowed
 * @return array Example: [['id'=>'SPP', 'name'=>'Ser Pilo Paga']...]
 */
function get_cohort_groups() {
    return array(['id'=>'SPP', 'name'=>'Ser Pilo Paga'],
        ['id'=>'SPE', 'name'=>'Condición de Excepción'],
        ['id'=>'3740', 'name'=>'Ingeniería Topográfica'],
        ['id'=>'OTROS', 'name'=>'Otros ASES']);
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

        $cohorts_groups = get_cohort_groups();

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