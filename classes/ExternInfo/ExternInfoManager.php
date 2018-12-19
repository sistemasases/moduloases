<?php
require_once(__DIR__.'/../JSON/JsonManager.php');
require_once(__DIR__.'/../CSV/CsvManager.php');
require_once (__DIR__.'/../common/Validable.php');
require_once (__DIR__ . '/../Errors/Factories/CsvManagerErrorFactory.php');
abstract class ExternInfoManager extends Validable {
    use CsvManager, JsonManager;
    /**
     * PHP class than model the csv, the csv headers and the class properties
     * should be the same, otherwise it is taken as error
     */
    public $class_or_class_name;
    private $objects;
    /**
     * Key value array where the keys are the object position in $objects array and the values
     * are the errors of this object
     * @var array
     */
    private $object_errors = array();
    private $_file;
    private $_file_name = 'fileToUpload';
    private $_file_extension = 'csv';
    public function __construct($class_or_class_name) {
        $this->class_or_class_name = $class_or_class_name;
    }
    public function get_objects() {
        return $this->objects;
    }
    public function get_object_errors() {
        return $this->object_errors;
    }
    public function execute() {
        if(!$this->valid()) {


            http_response_code(404);
            print_r($this->send_errors());
        } else {
            $this->load_data();
            http_response_code(200);
            print_r($this->send_response());
        }
    }
    /**
     * Overwrite this method for return the response, only if the method `valid()` return true
     * this method is executed, otherwise `get_errors_object()` is returned to the client,
     * the response is returned with content type specified in `$this->response_type`
     *
     * ## Send response method should return the response, not echo nor print_r the response
     */
    abstract function send_response();
    /**
     * If the load data fails return false, return true and init $this->>objects otherwise
     * @return bool
     */
    private function load_data_from_file() {
        global $_FILES;
        $this->_file = file($_FILES[$this->_file_name]['tmp_name']);
        if($this->loaded_data_with_file()) {
            $this->objects = $this->create_instances_from_csv($this->_file);
            return true;
        }
        return false;
    }
    private function load_data_from_ajax() {
        if($this->loaded_data_with_ajax()) {
            $this->objects = $this->create_instances_from_post();
        }
         return true;
    }
    public function _custom_validation(): bool
    {
        $valid = true;
        /*Validate each element*/
        /** @var  $object Validable*/
        if($this->objects) {
            foreach($this->objects as $key=>$object) {
                if(!$object->valid()){
                    $this->object_errors[$key] = $object->get_errors_object();
                    $valid = false;
                }
            }
        }
        return $valid;
    }
    /**
     * If you need process the data given, overwrite this method and make all the logic here
     *
     * Tip: if you need make additional methods for help in processing, be free of create this methods
     * in the respective child class (not here).
     * @return mixed
     */
    public function process_data(): bool {
        return true;
    }
    /**
     * Init objects from ajax source or file source.
     * If some error exist return false and make available the errors
     * @see Validable
     * @return bool
     */
    private function load_data() {
        return $this->load_data_from_file() || $this->load_data_from_ajax();
    }
    private function validate_file_data(): bool {
        global $_FILES;
        /* Validate file extension */
        if(!isset($_FILES[$this->_file_name])) {
            return false;
        }
        $file_name = $_FILES[$this->_file_name]['name'];

        if( pathinfo($file_name, PATHINFO_EXTENSION) != $this->_file_extension) {
            $this->add_error(CsvManagerErrorFactory::csv_extension_invalid());
            return false;
        }
        return true;
    }
    public function send_errors() {
        http_response_code(404);

        echo json_encode($this->get_errors_object());
    }
    private function validate_ajax_data(): bool {
        if(!isset($POST['data'])) {
            $this->add_error(new AsesError('-1', 'Los datos deben ser enviados en un atributo "data" via ajax'));
            return false;
        }
        return true;
    }
    private function validate_data_sources(): bool {
        return $this->validate_file_data() || $this->validate_ajax_data();
    }
    public function valid(): bool
    {

        $valid = parent::valid(); // TODO: Change the autogenerated stub

        $valid = $valid && $this->validate_data_sources();
        return $valid;
    }

    /**
     * This function should be used for return the data in printable format, for example, return the objects
     * converted in json or return the objects in a jquery datatable.
     */
    public function return_printable_data() {
        return 'Custom implementation not found';
    }
    private function loaded_data_with_file(): bool {
    if(file_exists($_FILES['fileToUpload']['tmp_name']) || is_uploaded_file($_FILES['fileToUpload']['tmp_name'])) {
        return true;
    }
    return false;
    }
    private function loaded_data_with_ajax(): bool {
        if($_POST){
            return true;
        }
        return false;
    }
}
