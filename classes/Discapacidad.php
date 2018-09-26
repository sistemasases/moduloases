<?php
require_once(__DIR__.'/traits/from_std_object_or_array.php');

class Discapacidad {
    use from_std_object_or_array;
    public $id;
    public $codigo_men;
    public $nombre;

    /**
     * Retorna las discapacidades existentes 
     * @return array<Discapacidad> Discapacidades existentes en el sistema
     */
    public static function get_discapacidades(){
        global $DB;
        $sql = 
        "
        SELECT * FROM {talentospilos_discap_men}
        ";
        $discapacidades_array = $DB->get_records_sql($sql);
        $discapacidades = Discapacidad::make_objects_from_std_objects_or_arrays($discapacidades_array);
        return $discapacidades;
    }

    
    /**
     * Obtener las discapacidades con una descripcion legible
     * @return array Array donde las llaves son los id de las discapacidades y el valor es el nombre de la discapacidad
     */
    public static function getOptions() {
        $discapacidades = Discapacidad::get_discapacidades();
        $opciones = array();
        foreach($discapacidades as $discapacidad) {
            $opciones[$discapacidad->id] = $discapacidad->nombre;
        }
        return $opciones;
    }

}

?>