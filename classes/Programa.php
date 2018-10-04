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
 * Ases user functions, utilities and class definition
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

class Programa extends BaseDAO {
    const ID = 'id';
    const NOMBRE = 'nombre';
    public $id;
    public $codigosnies;
    public $cod_univalle;
    public $nombre;
    public $id_sede;
    public $jornada;
    public $id_facultad;

    /**
     * Return array
     * @return array
     * @throws dml_exception
     */
    public static function get_options(): array {
        global $DB;
        $programas = Programa::get_all();
        $options = array();
        foreach($programas as $programa){
            $options[$programa->id] = $programa->nombre;
        }
        return $options;

    }

    public static function get_table_name(): string {
        return 'talentospilos_programa';
    }
    public function format() {
        return $this;
    }
}