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
class JsonErrorsFactory {
    const JSON_MALFORMED = 61;
    const JSON_AND_CLASS_HAVE_DISTINCT_PROPERTIES = 62;
    const JSON_SHOULD_BE_AN_ARRAY = 63;
    static function json_malformed($data=null) {
        return new AsesError(JsonManagerErrorFactory::JSON_MALFORMED, 'El json ingresado esta malformado', $data);
    }
    public static function  json_and_class_have_distinct_properties($data=null, $custom_mesasge = null) {
        $message_ = $custom_mesasge? $custom_mesasge : 'El JSON ingresado no tiene propiedades validas para con la clase a la que intenta asignarle los valores';
        return new AsesError(JsonManagerErrorFactory::JSON_AND_CLASS_HAVE_DISTINCT_PROPERTIES, $message_, $data);
    }
    public static function  json_should_be_an_array($data=null) {
        return new AsesError(JsonManagerErrorFactory::JSON_SHOULD_BE_AN_ARRAY, 'El JSON ingresado debe ser un array', $data);
    }
}