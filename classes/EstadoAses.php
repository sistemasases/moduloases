<?php
require_once(__DIR__.'/DAO/IBaseDAO.php');
require_once(__DIR__.'/DAO/BaseDAO.php');
class EstadoAses extends BaseDAO {
    public $id;
    public $descripcion;
    public $nombre;

    public static function get_table_name(): string {
        return 'talentospilos_estados_ases';
    }
    public function format() {

    }
    /**
     * Obtener los estados ASES con una descripcion legible
     * @return array Array donde las llaves son los id de los estados ASES y el valor es el nombre de el estado
     */
    public static function get_options() {
        $estados_ases = EstadoAses::get_all();
        $opciones = array();
        foreach($estados_ases as $estado_ases) {
            $opciones[$estado_ases->id] = $estado_ases->descripcion;
        }
        return $opciones;
    }

}

?>