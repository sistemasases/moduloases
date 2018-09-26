<?php
require_once(__DIR__.'/../traits/from_std_object_or_array.php');

class BaseDAO {
    use from_std_object_or_array;

    private static   $nombre_tabla = 'ASDFR';

    public function save() {
        global $DB;

    }
    public static function get_all() {
        global $DB;
        $nombre_tabla = get_called_class()::$nombre_tabla ;
        $sql = 
        "
        SELECT * FROM {$nombre_tabla}
        ";
        $objects_array = $DB->get_records_sql($sql);
        $objects = Discapacidad::make_objects_from_std_objects_or_arrays($objects_array);
        return $objects;
    }
}

?>