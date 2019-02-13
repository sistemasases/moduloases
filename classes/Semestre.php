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
* Semester functions, utilities and class definition
*
* @author     Luis Gerardo Manrique Cardona
* @package    block_ases
* @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
require_once(__DIR__ . '/DAO/BaseDAO.php');

class Semestre extends BaseDAO
{
    public $id;
    public $nombre;
    public $fecha_inicio;
    public $fecha_fin;

    static function get_table_name(): string {
        return 'talentospilos_semestre';
    }

    /**
     * Return a number of semesters later than a given date
     *
     * If $number_of_semesters is larger than the existing semesters
     * only the existing semesters are returned
     *
     * If does not exist semesters later than a given date
     * empty list is returned
     *
     * If an invalid date is given, false is returned
     *
     *
     * @param string $date Initial date
     * @param int $number_of_semesters The number of semesters to return, -1 for return all semesters
     *  and should be more than 1 or -1, if is not the case false is returned and warning is executed
     * @param bool $unix_time Indicate than the given date is in unix time , in this case
     *  date format is no needed and discarded for the operations
     * @param string $date_format If the date is in no unix time, the date is formated and validated based in this format
     * @return bool|array
     * @throws dml_exception
     */
    static function get_semesters_later_than($date, $number_of_semesters=-1, $unix_time = false, $date_format = 'Y-m-d') {

        global $DB;
        $date_ = $date;
        if(!($number_of_semesters == -1 || $number_of_semesters>=1)) {
            $error_message = <<<MESSAGE
El número de semestres es invalido, número dado: $number_of_semesters.  
Recuerde que debe ser -1 (todos los semestres o bien un valor mayor a 1");
MESSAGE;
            trigger_error($error_message);
            return false;
        }
        if ($unix_time) {
            $date_ = date($date_format, $date);
        }
        if(!$unix_time && !Semestre::validateDate($date, $date_format)) {

            return false;
        }
        $table_name = Semestre::get_table_name_for_moodle();
        $sql = <<<SQL
    select * from $table_name
    where fecha_inicio >= '$date_'
   
SQL;
        if($number_of_semesters>=1) {
            $sql .= " limit $number_of_semesters;";
        }
        $semesters_db = $DB->get_records_sql($sql);
        $semesters_db_values = array_values($semesters_db);
        $semesters = Semestre::make_objects_from_std_objects_or_arrays($semesters_db_values);

        return $semesters;
    }

    static function  validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }
}