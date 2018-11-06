<?php


abstract class Validable {


    /** @var array Errors array  */
    protected $_errors = array();

    /**
     * This variable contains the same errors than $_erros but are agrouped by the class properties, if exist
     * some error than does not related with a unique class attribute this is stored in $_errors_object['generic_errors']
     *
     * Example
     * The class Foo have two properties, $a and $b, $a is required and $b should be numeric, but
     * as shown below:
     *
     * '''class Foo extends BaseDAO {
     *  public $a;
     *  public $b;
     *
     *  public function __construct($a, $b) {
     *      $this->a = $a;
     *      $this->b = $b;
     *  }
     * }'''
     * If you have an instance '''$fo = new Foo(null, 'some_no_numeric');''' and execute $this->valid()
     * the $_errors_array should be equal to
     *'''
     * object(
     *  'a' => AsesError(10, 'El campo es requerido'),
     *  'b' => AsesError(11, 'El campo debe ser numerico')
     * );
     *
     *'''
     *And you can access this error array via $this->get_errors_object();
     * @var
     */
    protected $_errors_object;
    /**
     * Check if the current object is valid, and if is not valid add all the errors and make
     * these available by calling get_errors
     * @see get_errors
     * @return bool
     */
    public function valid(): bool {
        $this->clean_errors();
    }
    public function __construct()
    {
        $this->_errors_object = new stdClass();
    }

    /**
     * Clean errors
     */
    protected function clean_errors() {
        $this->_errors = array();
        $this->_errors_object = new \stdClass();
    }

    /**
     * Return errors, instances of AsesError if at least one error exists, emtpy array otherwise
     * @see AsesError
     * @return array
     */
    public function get_errors(): array {
        return $this->_errors;
    }
    /**
     * Add an error to the current object,
     * @param AsesError $error
     * @param string $fieldname Field (or object property) where the error is found, default is generic
     * errors field, this means than the error is not related with any object field or means than the error
     * is related to more than one field at the same time
     *
     */
    public function add_error(AsesError $error, $fieldname = BaseDAO::GENERIC_ERRORS_FIELD ) {
        array_push($this->_errors, $error);

        if(!isset($this->_errors_object->$fieldname)) {
            $this->_errors_object->$fieldname = array ($error);
        } else {
            array_push($this->_errors_object->$fieldname , $error);
        }
    }


    /**
     * Return errors array, AsesError agrouped under the object fields or generic errors,
     * the errors object have at most the same properties than the object where are the
     * errors, for example, if you get the errors of the class A and this have the
     * properties $b and $c, $errors can have $errors->b and $errros->c, if exist
     * errors on field b or c, and both $errros->c and $errors->b are an array of
     * AsesError instances
     * @see $this->_errors_object
     * @see AsesError
     * @return object
     */
    public function get_errors_object() {
        return $this->_errors_object;
    }

    /**
     * Get all errors, `_errors` and `_errors_object`, each one as member of a std class
     * `_errors` and `_errors_object` always have the same errors, but in diferent representations
     *
     * @return stdClass
     */
    public function get_errors_list_and_object(): stdClass {
        $all_erros = new stdClass();
        $all_erros->errors = $this->_errors;
        $all_erros->errors_object = $this->_errors_object;
        return $all_erros;
    }

}