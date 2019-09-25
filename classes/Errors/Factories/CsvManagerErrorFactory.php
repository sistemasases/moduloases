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

/**
 * Class data_csv_and_class_have_distinct_properties
 * @property $object_properties array array of string with the property names
 * @property $csv_headers array array of string whit csv headers
 */
class data_csv_and_class_have_distinct_properties {
    public $object_properties;
    public $csv_headers;
    public function __construct($object_properties, $csv_headers)
    {
        $this->csv_headers = $csv_headers;
        $this->object_properties = $object_properties;
    }
}
require_once ( __DIR__ . '/../AsesError.php');
class CsvManagerErrorFactory {
    public static $CSV_MANAGER_CLASS_DOES_NOT_EXIST = 41;
    public static $CSV_AND_CLASS_HAVE_DISTINCT_PROPERTIES = 42;
    public static $CSV_EXTENSION_INVALID = 43;
    public static function  csv_manager_class_does_not_exist ($data=null) {
        return new AsesError(CsvManagerErrorFactory::$CSV_MANAGER_CLASS_DOES_NOT_EXIST, 'La clase que se ha instanciado en el csv manager no existe', $data);
    }

    /**
     * @param null $data data_csv_and_class_have_distinct_properties
     * @param null $custom_message
     * @param bool $verbose_message
     * @return AsesError
     */
    public static function  csv_and_class_have_distinct_properties($data=null, $custom_message = null, $verbose_message = false) {
        /** @var $data data_csv_and_class_have_distinct_properties */
        $data = (object) $data;
        $message = $custom_message? $custom_message: 'El CSV ingresado no tiene propiedades validas para con la clase a la que intenta asignarle los valores';
        $object_properties = [];
        $csv_headers = [];
        if($verbose_message){
            if(isset($data->object_properties)) {
                $object_properties =  $data->object_properties;
                $object_properties_names = implode(', ', $object_properties);
                $message.= " Propiedades esperadas: [$object_properties_names]. ";


            }
            if(isset($data->csv_headers)) {
                $csv_headers = $data->csv_headers;
                $csv_headers_names = implode(', ', $csv_headers);
                $message.= "Headers dados: [$csv_headers_names]";
            }
            if(isset($data->object_properties) && isset($data->csv_headers)) {
                $csv_headers_missing = array_diff($object_properties, $csv_headers);
                $csv_headers_leftovers = array_diff($csv_headers, $object_properties);
                $csv_headers_missing_names = implode(', ', $csv_headers_missing);
                $csv_headers_leftovers = implode (', ', $csv_headers_leftovers);
                $message.= " Headers faltantes: [$csv_headers_missing_names]. ";
                $message.= " Headers sobrantes: [$csv_headers_leftovers]. ";
            }

        }

        return new AsesError(CsvManagerErrorFactory::$CSV_AND_CLASS_HAVE_DISTINCT_PROPERTIES, $message, $data);
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