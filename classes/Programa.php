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
 * Ases Program functions, utilities and class definition
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

require_once(__DIR__.'/DAO/BaseDAO.php');

class Programa extends BaseDAO {
    const ID = 'id';
    const NOMBRE = 'nombre';
    const CODIGO_UNIVALLE = 'cod_univalle';
    const ID_SEDE = 'id_sede';
    const JORNADA = 'jornada';
    public $id;
    public $codigosnies;
    public $cod_univalle;
    public $nombre;
    public $id_sede;
    public $jornada;
    public $id_facultad;

    /**
     * Retorna un array clave valor el cual tiene como llaves los id de programa y los valores son los nombres
     * de programas existentes en la base de datos
     * @return array
     * @see BaseDAO::_get_options()
     * @throws dml_exception
     */
    public static function get_options(): array {
        $fields = Programa::ID.','.Programa::NOMBRE;
        return parent::_get_options($fields, Programa::NOMBRE);

    }

    /**
     * Validate the current object
     * @return true|array True if the Object is valid, an array of errors otherwise
     * @throws dml_exception
     */
    public function validate() {
        return $this->validate_unique_key();
    }
    /**
     * Check if the unique constrain (cod_univalle, id_sede, jornada) is not not violated
     * @return bool
     * @throws dml_exception
     */
    public function valid_unique_key(): bool {
        $conditions = array(
            Programa::JORNADA=>$this->jornada,
            Programa::CODIGO_UNIVALLE=>$this->cod_univalle,
            Programa::ID_SEDE=>$this->id_sede
        );
        if (Programa::exists($conditions)) {
           return false;
        } else {
            return true;
        }
    }

    public static function get_table_name(): string {
        return 'talentospilos_programa';
    }

}