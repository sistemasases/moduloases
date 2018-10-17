<?php
require_once(__DIR__.'/DAO/BaseDAO.php');
class EstadoAses extends BaseDAO {
    const ID = 'id';
    const NOMBRE = 'nombre';
    const ID_SEGUIMIENTO = '1';
    const ID_SIN_SEGUIMIENTO = '2';

    public $id;
    public $descripcion;
    public $nombre;

    public static function get_table_name(): string {
        return 'talentospilos_estados_ases';
    }

    public static function get_estado_ases_default(): EstadoAses {
        return EstadoAses::get_by(array(EstadoAses::ID=>EstadoAses::ID_SEGUIMIENTO));
    }

    /**
     * Obtener los estados ASES con una descripcion legible en un array clave valor
     * @return array Array donde las llaves son los id de los estados ASES y el valor es el nombre de el estado
     */
    public static function get_options() {
        $fields = EstadoAses::ID.','.EstadoAses::NOMBRE;
        return parent::_get_options($fields);
    }

}

?>