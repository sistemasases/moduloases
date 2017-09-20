<<<<<<< HEAD
<?php
class MyException extends Exception {
    public function __construct($message, $code = 0, Exception $previous = null) {
        //se asigna los parametros a la clase padre (Exception)
        parent::__construct($message, $code, $previous);
    }
}
=======
<?php
class MyException extends Exception {
    public function __construct($message, $code = 0, Exception $previous = null) {
        //se asigna los parametros a la clase padre (Exception)
        parent::__construct($message, $code, $previous);
    }
}
>>>>>>> 97c7d23d80c7365c0b40027b0d4abac40b2e33b4
?>