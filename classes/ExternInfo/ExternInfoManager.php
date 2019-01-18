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
    private $initial_objects;
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

    /**
     * Save data to persistent data system
     */
    public function persist_data() {

    }
    public function get_objects() {
        return $this->objects;
    }
    public function get_object_errors() {
        return $this->object_errors;
    }
    public function get_initial_objects() {
        return $this->initial_objects;
    }
    public function execute() {

        if(!$this->valid()) {
            print_r($this->send_errors());
        } else {

            if($this->load_data() === true) {
                $this->persist_data();

                http_response_code(200);
                print_r($this->send_response());

            } else {

                $this->send_errors();
            }
        }
    }

    /**
     * @return array
     * @throws ErrorException
     */
    public function get_real_expected_headers() {
        $custom_mapping = $this->custom_column_mapping();
        $object_properties = \reflection\get_properties($this->class_or_class_name);
        if(!$custom_mapping) {
            return $object_properties;
        } else {
            $custom_mapping_ = array_flip($custom_mapping);
            $object_properties = array_combine($object_properties, $object_properties);// the header names are now the keys and the values of array
            $headers_ = array_replace($object_properties, $custom_mapping_); //replace the values with the real mappings
            return array_values($headers_);
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
     * @throws ErrorException If $this->class_or_classname does not exist
     * @return bool
     */
    private function load_data_from_file($load_invalid_data = false) {
        global $_FILES;
        $this->_file = file($_FILES[$this->_file_name]['tmp_name']);
        if($this->loaded_data_with_file()) {
            if($load_invalid_data === true) {
                $std_objects = Csv::csv_file_to_std_objects($this->_file);
                $objects = \reflection\make_from_std_object($std_objects, $this->class_or_class_name, true);
                if(!is_array($objects)){
                    $objects = array($objects);
                }
                $this->objects = $objects;
                $this->initial_objects = $objects;
                return false;

            }
            $objects = $this->create_instances_from_csv($this->_file, $this->custom_column_mapping());

            if($objects !== null) {
                $this->objects = $objects;
                $this->initial_objects = $objects;
                return true;
            } else {
                /* At this point the error was added by create_instances_from_csv method */
                return false;
            }
        }
        return false;
    }
    private function load_data_from_ajax($load_invalid_data = false) {
        if($this->loaded_data_with_ajax()) {
            $this->objects = $this->create_instances_from_post();
            $this->initial_objects = $this->objects;
            return true;
        }
         return false;
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
     * @throws ErrorException If $this->class_or_classname is not found
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
        $this->_file = file($_FILES[$this->_file_name]['tmp_name']);
        if( pathinfo($file_name, PATHINFO_EXTENSION) != $this->_file_extension) {
            $this->add_error(CsvManagerErrorFactory::csv_extension_invalid());
            return false;


        }
        $custom_mapping = $this->custom_column_mapping();
        if($custom_mapping && !empty($custom_mapping) ) {

            if(!Csv::csv_compaitble_with_custom_mapping($this->_file, $custom_mapping)) {

                $real_supposed_headers = $this->get_real_expected_headers();
                $given_headers = Csv::csv_get_headers($this->_file);
                $real_supposed_headers_string = implode(', ', $real_supposed_headers);
                $given_headers_string = implode(', ', $given_headers);
                $headers_missing = array_diff($real_supposed_headers, $given_headers);
                $headers_missing_string = implode(', ', $headers_missing);
                $headers_leftovers = array_diff($given_headers, $real_supposed_headers);
                $headers_leftovers_string = implode(', ', $headers_leftovers);
                /*$this->add_error(new AsesError(
                    -1,

                    "El mapeo actual no es compatible con el csv ingresado. 
                    El mapeo actual supone que los campos son 
                    [$real_supposed_headers_string], 
                    y los headers reales de el archivo son 
                    [$given_headers_string].
                    Headers faltantes: $headers_missing_string. 
                    Headers sobratnes: $headers_leftovers_string.",
                    new data_csv_and_class_have_distinct_properties($real_supposed_headers, $given_headers)));*/
                $this->add_error(CsvManagerErrorFactory::csv_and_class_have_distinct_properties(
                    new data_csv_and_class_have_distinct_properties($real_supposed_headers, $given_headers),
                    "El mapeo actual no es compatible con el csv ingresado. 
                    El mapeo actual supone que los campos son 
                    [$real_supposed_headers_string], 
                    y los headers reales de el archivo son 
                    [$given_headers_string].
                    Headers faltantes: $headers_missing_string. 
                    Headers sobratnes: $headers_leftovers_string.",
                    true
                ));
                return false;
            }
        } else {

            if (!Csv::csv_compatible_with_class($this->_file, $this->class_or_class_name, $this->custom_column_mapping())) {
                $csv_headers = CSV::csv_get_headers($this->_file);
                $class_properties = \reflection\get_properties($this->class_or_class_name);

                $this->add_error(CsvManagerErrorFactory::csv_and_class_have_distinct_properties(
                    new data_csv_and_class_have_distinct_properties($class_properties, $csv_headers),
                    'El csv tiene campos incorrectos.',
                    true
                ));
                return false;
            }
        }
        return true;
    }

    /**
     *
     */
    private function load_invalid_data() {
    $this->load_data_from_file(true) && $this->load_data_from_ajax(true);
    }
    public function send_errors() {
        http_response_code(404);
        $response = new stdClass();
        $response->object_errors = $this->get_errors_object();
        $this->load_invalid_data();
        $column_names = \reflection\get_properties($this->class_or_class_name);
        $datatable_columns = \jquery_datatable\Column::get_columns_from_names($this->get_real_expected_headers());
        $json_datatable = new \jquery_datatable\DataTable(array(), $datatable_columns,
            [array(
                "extend"=>'csvHtml5',
                "text"=>'CSV'
            )]);
        $response->datatable_preview = $json_datatable;
        echo json_encode($response);
    }
    private function validate_ajax_data(): bool {
        global $_POST;
        if($this->loaded_data_with_ajax()) {
            if(isset($_POST['data'])) {
                return true;
            } else {
                $this->add_error(new AsesError('-1', 'Los datos deben ser enviados en un atributo "data" via ajax'));
                return false;
            }
        } else {
            return false;
        }
    }
    private function validate_data_sources(): bool {
        if($this->validate_file_data() ) {
            return true;
        }
       if( $this->validate_ajax_data()){
           return true;
       }
       return false;
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
        if(isset($_POST['data'])){
            return true;
        }
        return false;
    }
}
