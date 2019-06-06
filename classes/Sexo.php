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


class Sexo extends BaseDao {
    public $id;
    public $sexo;
    public $opcion_general;

    const ID = 'id';
    const SEXO = 'sexo';
    const OPCION_GENERAL = 'opcion_general';
    public static function get_table_name(): string {
        return 'talentospilos_sexo';
    }
}