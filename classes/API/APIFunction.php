<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 4/11/18
 * Time: 07:33 PM
 */


require_once (__DIR__ . '/../../managers/lib/route.php');
/**
 * Class APIFunction
 * Represent a function in an endpoint of an API
 *
 * ### Example
 * Suppose than you have a manager ases_user_api.php, and you want two functions over user, get a moodle user
 * as json related with the ases_user_id given, also you want get the socioeconomic information of a given user,
 * bot functions under ases_user_api.php but you want access the information separately
 * For make this, you need two API functions, get_mdl_user and get_socioeconomic_info, each function need at least
 * the ases user id as an input, then you can create two API functions:
 *
 * get_mdl_user = APIFunction(
 */
class APIFunction
{
    /**
     * Method for the current function
     */
    public $http_method;
    /**
     * Pattern fomat of the function, is in semi URI format,
     * examples: /users/:id, /users, /groups/:group_id/users/:user_id
     * @var string Pattern of the function
     */
    public $path_format;

    /**
     * Native php pattern after execute build pattern
     * @var
     */
    public $_native_pattern;
    private $param_names;

    /**
     * Represent the function than execute the api function
     * @var callable
     */
    public $callable;
    public function __construct(string $path_format, callable $callable, $http_method = 'GET' ) {
        $this->path_format = $path_format;
        $this->_native_pattern = route_compile_path_format($path_format);
        $this->param_names = route_get_names($path_format);
        $this->http_method = $http_method;
        $this->callable = $callable;
    }
    public function execute($params=null) {
        ($this->callable) ( $params);
    }
}