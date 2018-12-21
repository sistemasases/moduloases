<?php
namespace jquery_datatable;


use function array_fill_keys;
use function array_search;

/**
 * Return the column for datatables for detail data, in other words, the column with '+' symbol in
 * the datatable
 * @see https://datatables.net/examples/api/row_details.html
 * @return array
 */
function get_datatable_class_column(): array {
    return array(
        "class"=> "details-control",
        "orderable"=>      false,
        "data"=>           null,
        "defaultContent"=> "");
}

/**
 * Return the common language cnofiguration for ASES datatables
 * @return array
 */
function get_datatable_common_language_config(): array {
    return array(
        "search"=> "Buscar:",
        "oPaginate" => array(
            "sFirst"=>    "Primero",
            "sLast"=>     "Último",
            "sNext"=>     "Siguiente",
            "sPrevious"=> "Anterior"
        ),
        "sProcessing"=>     "Procesando...",
        "sLengthMenu"=>     "Mostrar _MENU_ registros",
        "sZeroRecords"=>    "No se encontraron resultados",
        "sEmptyTable"=>     "Ningún dato disponible en esta tabla",
        "sInfo"=>           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        "sInfoEmpty"=>      "Mostrando registros del 0 al 0 de un total de 0 registros",
        "sInfoFiltered"=>   "(filtrado de un total de _MAX_ registros)",
        "sInfoPostFix"=>    "",
        "sSearch"=>         "Buscar:",
        "sUrl"=>            "",
        "sInfoThousands"=>  ",",
        "sLoadingRecords"=> "Cargando...",
    );
}
class DataTable {
    /** Data for DataTable, can be stdClass objects or class instances
     * @link https://datatables.net/reference/option/data
     */
    public $data;
    /** Columns of JSONDataTable, each column must correspond with one property of the objects
     *  than exist in '$this->data'
     * @see JSONColumn
     * @link https://datatables.net/reference/option/columns
     */
    public $columns;
    public $responsive = true;
    public $sPaginationType = "full_numbers";

    public function __construct($data=[], $columns=[]) {
        $this->data = $data;
        $this->columns = $columns;
    }
}

/**
 * Column, jquery datatables column than extract data from json
 * for more information about how it works go to
 * {@link https://datatables.net/reference/option/columns}, or for concrete
 * example {@link https://editor.datatables.net/examples/advanced/deepObjects.html}
 */
class Column {
    /**
     * @link https://datatables.net/reference/option/columns.title
     */
    public $title;
    public $name;
    /**
     * @link https://datatables.net/reference/option/columns.data
     */
    public $data;
    public $description;
    public $className;
    public function __construct($name, $title = null, $data=null, $description=null, $className=null)
    {
        $this->name = $name;
        $this->data = $data? $data: $name;
        $this->description = $description? $description: $name;
        $this->className = $className? $className: $name;
        $this->title = $title? $title: $name;
    }
    /**
     * Return column title based in standard object property name
     * ej. ('userEmail' ->  'User Email'}.
     * @param   string  $property_name  Standard property name, with words separed by underscores or capital letters. examples: userEmail, user_email
     * @return string return standard datatable column title based in property name ej. user_email -> 'User Email'.
     */
    public static function get_column_title_from_property_name($property_name) {
        $column_title = '';
        $separed_words_chain_by_spaces = [];
        if (strpos($property_name, '_') != false) {
            $separed_words_chain_by_spaces = preg_replace("/_+/", " ", $property_name);
            $column_title = ucwords($separed_words_chain_by_spaces);
        }
        else if(preg_match("/[A-Z]+/", $property_name)) {
            $separed_words_chain_by_spaces = preg_replace('/([A-Z])/', ' $1', $property_name);
            $column_title = ucwords($separed_words_chain_by_spaces);
        } else {
            $column_title = ucwords($property_name);
        }
        // All words should be in uppercase

        return $column_title;
    }
    /**
     * Return column based in standard object property name,
     * ej. (userEmail -> {data: 'userEmail', title: 'User Email'}).
     * @param   string  $property_name  Standard property name, with words separed by underscores or capital letters.
     *  examples: userEmail, user_email
     * @return Column return standard datatable column based in property name ej. (user_email -> {data: 'user_email',
     *  title: 'User Email'}).
     */
    public static function get_column_from_property_name($property_name) {
        $data = $property_name;
        $title = Column::get_column_title_from_property_name($property_name);
        return new Column($data, $title);
    }
    /**
     * Return jquery datatable columns than extract the data from formated
     * object, based in a object instance or in a class. Only public properties
     * are converted to table columns.
     * @see Column::get_column_from_property_name
     * @see Column
     * @param string|object instance $class_name_or_object Data structure for extract JSON columns,
     *  that are generated based in object or class properties
     * @param $custom_column_names array Key value array where the keys are the object property name
     * and the value is the custom column name
     * @return array<Column> columns for jquery datatable than should print the information
     *  than contain instances of object or class given as an input
     */
    public static function get_columns($class_name_or_object, $custom_column_titles = array() ) {
        $object = null;
        $object_properties = null;
        $sourceProperties = null;
        $data_table_columns = [];
        if (is_string($class_name_or_object)) {
            if (class_exists ($class_name_or_object)) {
                $object = new $class_name_or_object();
            } else {
                throw new \ValueError("La clase $class_name_or_object no esta definida");
            }
        } else {
            $object = $class_name_or_object;
        }
        $sourceProperties = new \ReflectionObject($object);
        $object_properties = $sourceProperties->getProperties();
        /* @var $object_property \ReflectionProperty */
        foreach($object_properties as $object_property) {
            /* Only public properties should be converted to table columns*/
            if($object_property->isPublic()) {
                $object_property_name = $object_property->name;
                $custom_title = array_search($object_property_name, $custom_column_titles);
                $json_column = Column::get_column_from_property_name($object_property_name);
                if($custom_title) {
                    $json_column->title = $custom_title;

                }
                array_push($data_table_columns, $json_column);
            }
        }

        return $data_table_columns;

    }
    public static function get_columns_from_names(array $names ): array {
        $datatable_columns = [];
        foreach($names as $name) {
            $json_column = new Column($name);
            array_push($datatable_columns, $json_column);
        }
        return $datatable_columns;
    }
}