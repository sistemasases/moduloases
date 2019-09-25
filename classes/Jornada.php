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
 * Jornada class definition
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2016 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class Jornada {
    const DIURNA = 'DIURNA';
    const NOCTURNA = 'NOCTURNA';
    const VESPERTINA = 'VESPERTINA';
    /**
     * Obtener las Jornadas en un array clave valor (principalmente para uso de select en formularios)
     * @return array Array
     */
    public static function get_options(): array {
        return array(
            Jornada::NOCTURNA=>Jornada::NOCTURNA,
            Jornada::DIURNA=>Jornada::DIURNA,
            Jornada::VESPERTINA=>Jornada::VESPERTINA
        );
    }

    public static function get_possible_values() {
        return array(
            Jornada::VESPERTINA,
            Jornada::DIURNA,
            Jornada::NOCTURNA
        );
    }
}