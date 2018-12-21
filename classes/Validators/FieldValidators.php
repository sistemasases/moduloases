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
                    return "Field $field_name should be numeric";
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
                    return "Field $field_name is required";
                }
            };
    }
    public static function regex($regex, $custom_message = null) {

        return
            function($value, $field_name = '') use ($regex, $custom_message) {

                if(preg_match($regex, $value)) {
                    return preg_match($regex, $value);
                } else {

                    if($custom_message) {
                        return $custom_message;
                    } else {
                        return "Field $field_name should be match with regex '$regex'";
                    }
                }
            };
    }
}