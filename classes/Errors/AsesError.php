<?php
class AsesError {
    /**
     * Standard code, minimum value: 0
     * The code should be unique between all errors of ases block
     */
    public $code ;
    /**
     * Simple redeable string than explain the error
     */
    public $message;
    /**
     * Standard array than contains all than you want for aditional info
     * about error, ONLY USE on exceptional cases
     */
    private $__data;
    /**
     * Error constructor
     * @param int $code Error code
     * @param string $message Short and understandable error message
     * @param null or object $data Aditional info than is used in the error
     */
    public function __construct($code = -1, $message = '', $data = null) {
        $this->message = $message;
        $this->code = $code;
        $this->__data = $data;

    }

    public function get_data() {
        return $this->data;
    }
    public function set_data($array) {
        $this->__data = $array;
    }
}

?>