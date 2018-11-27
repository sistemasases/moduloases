<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 4/11/18
 * Time: 07:22 PM
 */
require_once (__DIR__ . '/../Errors/Factories/APIErrorFactory.php');
require_once (__DIR__ . '/../common/Validable.php');
require_once (__DIR__ . '/APIFunction.php');
/**
 * Class BaseAPI
 * Is validable because the function called
 */
class BaseAPI  extends Validable {
    /**
     * Array of strings with the routes
     * @var array APIFunction array
     */

    /**
     * @var array Array of string with the resources, can be functions or routes
     */
    private $resources;

    private $functions;
    /**
     * @var stdClass $params The params send to php
     */
    private $params;

    public function __construct()
    {
        parent::__construct();
        $this->functions = array();
        $this->resources = array();
        return $this;
    }

    function get($path_format_or_func_name, callable $function) {
        $this->add_function(new APIFunction($path_format_or_func_name, $function));

    }

    /**
     * @param $function APIFunction
     */
    private function add_function($function) {
        if($function->path_format[0]!='/') {
            $function->path_format = '/'.$function->path_format;
        }
        array_push($this->functions, $function);
        array_push($this->resources, $function->path_format);
    }
    function post($path_format_or_func_name, callable $function) {
        $this->add_function(new APIFunction($path_format_or_func_name, $function, 'POST'));
    }
    public function send_errors() {
        http_response_code(404);
        header('Content-Type: application/json');

        echo json_encode($this->get_errors_object());
    }
    /**
     * Extract the params from  `php://input` of if its not found at this,
     * search at $_POST, and make the params available at $this->params
     * @return _params
     */
    private function init_params() {
        global $_POST;
        $this->params = json_decode(file_get_contents('php://input'));
        if(!$this->params || !$this->params != '') {
            $this->params = (object)$_POST;
        }
    }
    private function find_function($method, $path) {
        if($path[0] != '/') {
            $path = '/'.$path;
        }
        /** @var $function APIFunction */
        foreach ($this->functions as &$function) {
            if($method == $function->http_method && preg_match($function->_native_pattern, $path)) {
                return $function;
            }
        }
        return false;
    }

    function get_all_resources_printable() {
        $functions_string = array();
        /** @var APIFunction $function */
        foreach ($this->functions as $function) {
            array_push($functions_string, substr($function->path_format,1).':'.$function->http_method);
        }
        return implode(',', $functions_string);
    }
    function run() {
        global $_SERVER;
        $method = $_SERVER['REQUEST_METHOD'];
        $path_info = $_SERVER['PATH_INFO'];

        $this->init_params();
        $target = null;
        $api_view = null;
        $data = null;
        $args = array();
        /* Infering the function from post data */
        if(isset($this->params->function)) {
            $target =  $this->params->function;
            $api_view = $this->find_function($method, $target);
            if(isset($this->params->params)) {
                $data = $this->params->params;
            }
        } else { /* Infering the function from PATH_INFO */
            $target = $path_info;
            $api_view = $this->find_function($method, $target);
            $data = $this->params; /* The request body is assumed as a data */
        }

        if( $api_view ) {
             $args = route_get_params($api_view->path_format,  $path_info);
             $api_view->execute($data, $args);
        } else {

            $this->add_error(
                APIErrorFactory::resource_not_found(
                    array(
                        'resource'=>$target,
                        'method' => $method,
                        'resources_available'=> $this->get_all_resources_printable())));

            $this->send_errors();
        }

    }
}