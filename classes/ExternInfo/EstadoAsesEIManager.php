<?php

use jquery_datatable\Column;
use jquery_datatable\DataTable;

require_once(__DIR__.'/ExternInfoManager.php');
require_once(__DIR__.'/EstadoAsesCSV.php');
require_once(__DIR__.'/../../managers/jquery_datatable/jquery_datatable_lib.php');
class EstadoAsesEIManager extends ExternInfoManager {
    public function __construct() {
        parent::__construct( EstadoAsesCSV::get_class_name());
    }


    /**
     * In this case, a datatable is returned
     * @return string|void
     */
    public function send_response() {

        $sample_std_object = $this->get_objects()[0];

        $datatable_columns = \jquery_datatable\Column::get_JSON_columns($sample_std_object);
        $json_datatable = new \jquery_datatable\DataTable($this->get_objects(), $datatable_columns);
        $response = new \stdClass();
        $response->jquery_datatable = $json_datatable;
        $response->data = $this->get_objects();
        $response->error = !$this->valid();
        $response->errors = $this->get_errors();
        $response->object_errors = $this->get_object_errors();
        $arrayEncoded = json_encode($response);


        return $arrayEncoded;
    }
}


?>