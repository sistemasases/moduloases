<?php
class AsesError {
    /**
     * Standard code, minimum value: 0
     * The code should be unique between all errors of ases block
     */
    public $status_code ;
    /**
     * Simple redeable string than explain the error
     */
    public $error_message;
    /**
     * Standard array than contains all than you want for aditional info
     * about error, ONLY USE on exceptional cases
     */
    public $data_response;
    /**
     * Error constructor
     * @param int $code Error code
     * @param string $message Short and understandable error message
     * @param null or object $data Aditional info than is used in the error
     */
    public function __construct($code = -1, $message = '', $data = null) {
        $this->error_message = $message;
        $this->status_code = $code;
        $this->data_response = $data;

    }

    public function get_dataResponse() {
        return $this->data_response;
    }
    public function set_dataResponse($array) {
        $this->data_response = $array;
    }
}

?>