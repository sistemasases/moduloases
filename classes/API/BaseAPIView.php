<?php
require_once( __DIR__ . '/../Errors/Factories/FieldValidationErrorFactory.php');
require_once( __DIR__ . '/../common/Validable.php');
/**
 * Class BaseAPIView
 * Is validable because the params can be incorrect, and if this is the case,
 * an error should be returned
 */
abstract class BaseAPIView extends Validable {
    const CONTENT_TYPE_JSON = 'application/json';
    /**
     * @var stdClass $params The params sended to php via post
     */
    protected $params;
    /**
     * @var array $args The args defined at url
     *
     * # Example
     * Given the url fromat  /user/:id and the requested url /user/5
     * ```php
     * $this->assertEqual($args, array('id' => 5);
     * ```
     */
    protected $args;
    /* @var stdClass $url_params The params sended to php via URL (/some?var_a=16&var_b=true)
    protected $url_params;
    /** @var string $response_type Value of header value `Content-Type` */
    public $response_type;
    /**
     * Return an array of string with all required param names for this API endpoint
     */
    function  get_required_params(): array {
        return array();
    }
    function valid(): bool {
        $this->clean_errors();
        $valid = true;
        $required_params = $this->get_required_params();
        foreach($required_params as $required_param) {

            if(!property_exists($this->params, $required_param)) {
                $field_error = FieldValidationErrorFactory::required_field_is_empty( array('field'=>$required_param));
                $this->add_error($field_error, $required_param);
                $valid = false;
            }
        }
        return $valid;
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
     * Return the API response to the client.
     * If some error is detected, all the errors are sended to the client
     */
    function execute($params = null, $args= array()) {

        $this->params = (object) $params;
        $this->args= $args;
        if (!$this->valid()) {

            http_response_code(404);
            echo json_encode($this->send_errors_list_and_object());
            return;
        } else {
            http_response_code(200);
            if ($this->response_type != BaseAPIView::CONTENT_TYPE_JSON ) {
                header("Content-Type: $this->response_type");
                print_r($this->send_response());
            } else {
                header('Content-Type: application/json');
                echo json_encode($this->send_response());
            }

        }
    }
    public function send_errors() {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode($this->get_errors_object());
    }

    /**
     * Get all errors, _errors and _errors_object, each one as a property of a stdClass
     *
     * # Example
     * ```
     * stdClass {
     *  errors => array (0 => The field a is required
     *                   1 => The field b is required),
     *  errors_object => stdClass {
     *           field_a => The field a is required
     *           field_b => The field b is required
     * }
     * ```
     *
     * _errors and _errors_object always have the same errors, but in diferent representations
     *
     *
     */
    public function send_errors_list_and_object() {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode($this->get_errors_list_and_object());
    }
    public function __construct() {
        $this->response_type = 'text/html';
    }

}