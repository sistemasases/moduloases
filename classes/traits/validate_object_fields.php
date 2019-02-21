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

    /**
     * Retorna un std object con la descripción de lo que hace valido a las propiedades
     * del objeto que usted decida quiere validar.
     *
     * Si su clase tiene los campos $id_usuario y $nota por ejemlo, puede pensar en retornar
     * los validadores siguientes:
     *```php
     * $validators = new stdObject();
     * $validators->id_usuario = [FieldValidators::required(), FieldValidators::numeric()];
     * $validators->nota = [FieldValidators::required(), FieldValidators::numeric(), FieldValidators::lessThan(5)];
     * return $validators;
     * ```
     * @see FieldValidators
     * @return stdClass
     */
    public function define_field_validators(): stdClass {

        return new stdClass();
    }
    public  function set_field_validators($field_validators) {
        $this->_field_validators = $field_validators;
    }
    public function get_field_validators() {
        return $this->_field_validators;
    }
    public function valid_fields(): bool {
        $this->set_field_validators($this->define_field_validators());
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

