<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 8/02/19
 * Time: 04:04 PM
 */
require_once (__DIR__.'/../../common/Validable.php');
require_once (__DIR__.'/../../Validators/FieldValidators.php');
require_once (__DIR__.'/../../CondicionExcepcion.php');
require_once (__DIR__.'/../../traits/validate_object_fields.php');
class CondicionExcepcionEI extends Validable
{
 use validate_object_fields;
    /**
     * @var $condicion string Student excepcion status
     * @see CondicionExcepcion::ALIAS
     */
 public $condicion;
    /**
     * Student document number
     * @var $num_documento int
     * @see AsesUser::NUMERO_DOCUMENTO
     */
 public $num_documento;
 public function define_field_validators(): stdClass
 {
     /** @var $field_validators CondicionExcepcionEI */
     $field_validators = new stdClass();
     $field_validators->condicion = [FieldValidators::required()];
     $field_validators->num_documento = [FieldValidators::required(), FieldValidators::numeric()];
     return $field_validators;
 }
 private  function clean_condicion($separator = '.') {
     $this->condicion = trim($this->condicion);
     $this->condicion = preg_replace('/[, 0-9.]*/', '', $this->condicion);
     $this->condicion = chunk_split( $this->condicion, 1 , $separator);
 }
 public function clean() {
     $this->clean_condicion();
 }
 public function validar_condicion(): bool {

     if(!CondicionExcepcion::exists(array(CondicionExcepcion::ALIAS => $this->condicion))) {
         $condiciones_excepcion = CondicionExcepcion::get_all();
         $condiciones_excepcion_aliases = array_column($condiciones_excepcion, CondicionExcepcion::ALIAS);
         $condiciones_excepcion_aliases_string = implode(', ', $condiciones_excepcion_aliases);
         $this->add_error("La condición de exepcion $this->condicion no existe, condiciones disponibles: [$condiciones_excepcion_aliases_string]");
         return false;
     }
     return true;
 }

 public function validar_num_documento() {
     if(!AsesUser::exists(array(AsesUser::NUMERO_DOCUMENTO => $this->num_documento))) {
         $this->add_error("El usuario ases con número documento $this->num_documento no existe", 'num_documeneto');
         return false;
     }
     return true;
 }
    public static function get_class_name() {
        return get_called_class();
    }
 public function valid(): bool
 {
     $valid_parent = parent::valid();
     $valid_fields = $this->valid_fields();
     if(!$valid_fields) {

         return false;
     }
     $this->clean();
     $valid_condicion = $this->validar_condicion();
     $valid_documento = $this->validar_num_documento();
     return  $valid_parent && $valid_documento && $valid_condicion;
 }
}