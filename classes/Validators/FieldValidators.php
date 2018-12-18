<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 18/12/18
 * Time: 05:08 PM
 */

class FieldValidators
{
    public static function numeric() {
        return
            function($value, $field_name = '') {
                if(is_numeric($value)) {
                    return true;
                } else {
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
    public static function regex($regex) {
        return
            function($value, $field_name = '') use ($regex) {
                if(preg_match($regex, $value)) {
                    return preg_match($regex, $value);
                } else {
                    return "Field $field_name should be match with regex '$regex'";
                }
            };
    }
}