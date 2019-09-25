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
 * Facultad functions, utilities and class definition
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Facultad class
 * @see table talentospilos_facultad
 */
require_once(__DIR__ . '/DAO/BaseDAO.php');
class Facultad extends BaseDAO {
    const ID = 'id';
    const NOMBRE = 'nombre';
    const CODIGO_UNIVALLE = 'cod_univalle';
    public $id;
    public $nombre;
    public $cod_univalle;


    public static function get_table_name(): string
    {
        return 'talentospilos_facultad';
    }
    /**
     * Obtener las Facultades en un array clave valor (principalmente para uso de select en formularios)
     * donde las llaves son el id de la facultad y los valores son los nombres de las facultades
     * @return array Array
     */
    public static function get_options(): array {
        $fields = Facultad::ID.','.Facultad::NOMBRE;
        return parent::_get_options($fields, Facultad::NOMBRE);
    }
}