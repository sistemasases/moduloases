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

class EstadoPrograma {
    const INACTIVE = 0;
    const ACTIVE = 1;
    const INACTIVE_NAME = 'Inactivo';
    const ACTIVE_NAME = 'Activo';
    /**
     * Return program status options in legible format where the keys are
     * the real values in database for tracking status and values
     * are readable value in string
     * @return array Tracking status options
     */
    public static function get_options() {
        return  array(
            EstadoPrograma::INACTIVE => EstadoPrograma::INACTIVE_NAME,
            EstadoPrograma::ACTIVE => EstadoPrograma::ACTIVE_NAME
        );
    }
}

?>