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
 * Reflection error factory definition
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once ( __DIR__ . '/../AsesError.php');

class ReflectionErrorFactory {
    public static $CLASS_DOES_NOT_EXIST = 50;
    public static $CLASS_AND_STD_OBJECT_OR_ARRAY_DISTINCT_PROPERTIES = 51;

    public static function  class_does_not_exist ($data=null) {
        return new AsesError(ReflectionErrorFactory::$CLASS_DOES_NOT_EXIST, 'La clase solicitada no exite', $data);
    }

    public static function  class_and_std_object_or_array_distinct_properties($data=null) {
        return new AsesError(ReflectionErrorFactory::$CLASS_AND_STD_OBJECT_OR_ARRAY_DISTINCT_PROPERTIES, 'Esta tratando de asignar un std object o array con propiedades distintas a la clase solicitada', $data);
    }
}