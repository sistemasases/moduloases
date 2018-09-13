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
 * Talentos Pilos
 *
 * @author     Luis Gerardo Manrique Cardona 
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona  <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Return all moodle cohorts filtered by id_instance
 * @param string $id_instance
 * @return array $cohorts Cohorts filtered by id_instance, empty array if does not exists 
 * @see {@link https://docs.moodle.org/35/en/Cohorts}
 */
function get_cohorts($id_instance)
{
    global $DB;


    $sql_query = "SELECT * FROM {cohort}
                    WHERE  id IN (SELECT id_cohorte
                                    FROM   {talentospilos_inst_cohorte}
                                    WHERE  id_instancia = $id_instance)";

    $cohorts = $DB->get_records_sql($sql_query);

    return $cohorts;
}
?>