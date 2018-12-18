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
 * Csv manager error factory
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once ( __DIR__ . '/../AsesError.php');
class CsvManagerErrorFactory {
    public static $CSV_MANAGER_CLASS_DOES_NOT_EXIST = 41;
    public static $CSV_AND_CLASS_HAVE_DISTINCT_PROPERTIES = 42;
    public static $CSV_EXTENSION_INVALID = 43;
    public static function  csv_manager_class_does_not_exist ($data=null) {
        return new AsesError(CsvManagerErrorFactory::$CSV_MANAGER_CLASS_DOES_NOT_EXIST, 'La clase que se ha instanciado en el csv manager no existe', $data);
    }
    public static function  csv_and_class_have_distinct_properties($data=null) {
        return new AsesError(CsvManagerErrorFactory::$CSV_AND_CLASS_HAVE_DISTINCT_PROPERTIES, 'El CSV ingresado no tiene propiedades validas para con la clase a la que intenta asignarle los valores', $data);
    }

    /**
     *
     * @param object|array $data Data for error context, alternatively can have the given_extension and allowed_extensions,
     * and allowed_extensions. If is gieven, allowed_extensions should be an array of strings.
     * @return AsesError
     */
    public static function csv_extension_invalid($data=null) {
        $data_ = (object) $data;
        $given_extension = null;
        $allowed_extensions = null;
        $message = 'El CSV ingresado no tiene una extensión valida.';
        if(isset($data_->given_extension)) {
            $given_extension = $data_->given_extension;
            $message.= " Extensión de el archivo ingresado:$given_extension.";
        }
        if(isset($data_->allowed_extensions)) {
            $allowed_extensions = implode(',', $data_->allowed_extensions);
            $message.=" Extensiones de archivo permitidas: $allowed_extensions";
        }
        return new AsesError(CsvManagerErrorFactory::$CSV_EXTENSION_INVALID,
            $message, $data);
    }
}