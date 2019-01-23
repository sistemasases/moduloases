<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 18/12/18
 * Time: 05:08 PM
 */

class FieldValidators
{
    public static function numeric(string $custom_message = null) {
        return
            function($value, $field_name = '') use ($custom_message) {
                if(is_numeric($value)) {
                    return true;
                } else {
                    if($custom_message !== null) {
                        return $custom_message;
                    }
                    return "El campo '$field_name' debe ser numérico";
                }
            };
    }
    public static function one_of(array $options, string $custom_message = null) {
        return
            function ($value, $field_name = '') use ($options, $custom_message) {
            foreach($options as $option) {
                if($value === $option) {
                    return true;
                }
            }
                $options_string = implode(', ', $options);
                return $custom_message? $custom_message: "El campo $field_name debe tomar uno de los siguientes valores: [$options_string]";
            };

    }
    /**
     * Check if the field have one of the given sizes
     * @param array $sizes
     * @param null $custom_message
     * @param string $custom_glue
     * @return Closure
     */
    public static function string_size_one_of(array $sizes, $custom_message=null, $custom_glue = ', ') {
        return
            function($value, $field_name = '') use ($sizes, $custom_message, $custom_glue) {
                $given_size = strlen($value);
                $posible_sizes_str = implode($custom_glue, $sizes);
                foreach($sizes as $size) {
                    if($given_size === $size) {
                        return true;
                    }
                }
                return $custom_message? $custom_message: "El campo '$field_name' debe tener una de estas longitudes: [$posible_sizes_str]. Longitud dada. $given_size";

            };
    }
    public static function string_size(int $size, $custom_message=null)  {
        return
            function($value, $field_name = '') use ($size, $custom_message) {
            $given_size = strlen($value);
                $message = $custom_message? $custom_message: "El campo '$field_name' debe tener exactamente la longitud de $size. Longitud dada. $given_size";
                if ($given_size != $size) {
                return $message;
            } else {
                return true;
            }
            };
    }
    /**
     * @return true|string
     */
    public static function required() {
        return
            function($value, $field_name = '') {
                if(!is_null($value) && !($value === '')) {
                    return true;
                } else {
                    return "El campo  '$field_name' es requerido";
                }
            };
    }
    public static function email($custom_message = null, $custom_regex = null) {
        return
        function ($value, $field_name = '') use ($custom_message, $custom_regex) {
            $regex = $custom_regex? $custom_regex: '/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/' ;

            if(preg_match($regex, $value)) {
                return true;
            } else {
                return $custom_message? $custom_message : 'Dirección de correo electronico invailda';
            }
        };
    }
    public static function regex($regex, $custom_message = null) {

        return
            function($value, $field_name = '') use ($regex, $custom_message) {

                if(preg_match($regex, $value)) {
                    return true;
                } else {

                    if($custom_message) {
                        return $custom_message;
                    } else {
                        return "El campo '$field_name' debe coincidir con la expresión regular '$regex'";
                    }
                }
            };
    }
}