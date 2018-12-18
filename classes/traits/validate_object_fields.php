<?php

/**
 * Trait validate_object_fields
 * For this trait the class should be have the method **add_error**
 * @see https://gist.github.com/luchoman08/6fc168926efe984c490472c0f1b3a353
 */
trait validate_object_fields {
    private $_field_validators ;
    public  function __construct() {
        /*Check if the caller class have the method add_error*/
        if(!method_exists($this, 'add_error')) {
            throw new ErrorException('For implement validate_object_fields the class should have the method "add_error($message, $field_name)"');
        }
        $this->_field_validators = new stdClass();
        $this->define_field_validators();
    }
    public function define_field_validators() {

        return false;
    }
    public  function set_field_validators($field_validators) {
        $this->_field_validators = $field_validators;
    }
    public function get_field_validators() {
        return $this->_field_validators;
    }
    public function valid_fields(): bool {

        $valid = true;

        if($this->_field_validators){
            foreach($this as  $clave => $valor) {

                if( isset($this->_field_validators->$clave) && is_array($this->_field_validators->$clave) ) {

                    foreach($this->_field_validators->$clave as $validator_func) {
                        $validation_result = $validator_func($valor, $clave);
                        if(!(true === $validation_result)) {
                            $this->add_error(new AsesError(-1, $validation_result), $clave);
                            $valid = false;
                        }
                    }
                }
            }
        }
        return $valid;
    }
}

