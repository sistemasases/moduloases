<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 18/12/18
 * Time: 05:08 PM
 */

require_once (__DIR__ . '/../../managers/lib/json.php');
class FieldValidators
{
    private static  function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    /**
     * Validate the date format, can be only one format or an array of possible formats
     *
     *
     * @param $format_or_formats string|array Examples: 'Y-m-d', ['Y-m-d', 'Y/m/d', 'd-m-Y']
     * @return Closure
     */
    public static function date_format($format_or_formats, string $custom_message = null) {
        return
            function($value, $field_name= '') use ($format_or_formats, $custom_message) {
            $error_message_prefix = "El campo $field_name debe tener ";
                if(is_string($format_or_formats)) {
                    if(!FieldValidators::validateDate($value, $format_or_formats)) {
                        return $custom_message? $custom_message : $error_message_prefix." el formato $format_or_formats";
                    } else {
                        return true;
                    }
                } elseif(is_array($format_or_formats)) {
                    foreach($format_or_formats as $format) {

                        if(FieldValidators::validateDate($value, $format)) {
                           return true;
                        }
                    }
                    $formats_string = implode(', ', $format_or_formats);
                    return $custom_message?  $custom_message: $error_message_prefix." uno de los formatos $formats_string";
                } else {
                    return "Ha ingresado un valor incorrecto para el validador, ejemplo de formatos esperados: 'Y-m-d', ['Y-m-d', 'Y/m/d', 'd-m-Y']. Formato ingresado: $format_or_formats";
                }
            };
    }
    public static function json(string $custom_message = null ) {
        return
        function ($value='', $field_name = '') use ($custom_message) {
          if(\json\valid_json($value))   {
              return true;
          } else {
              return $custom_message? $custom_message: "El campo '$field_name' contiene un JSON mal formado";
          }
        };
    }
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
    public static function number_between($start, $end, $custom_message = null) {
        $start = (float) $start;
        $end = (float) $end;
        return
        function ($value, $field_name = '') use ($start, $end, $custom_message) {
          if(!($value >= $start && $value <= $end )) {
              return $custom_message? $custom_message:  "El valor de el campo '$field_name' debe estar entre $start y $end";
          } else {
              return true;
          }
        };
    }
    public static function regex($regex, $custom_message = null) {

        return
            function($value, $field_name = '') use ($regex, $custom_message) {

                if(preg_match($regex, $value)) {
                    return true;
                } else {
                    return $custom_message? $custom_message: "El campo '$field_name' debe coincidir con la expresión regular '$regex'";

                }
            };
    }
}