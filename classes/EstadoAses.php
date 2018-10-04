<?php
require_once(__DIR__.'/DAO/IBaseDAO.php');
require_once(__DIR__.'/DAO/BaseDAO.php');
class EstadoAses extends BaseDAO {
    const ID = 'id';
    const NOMBRE = 'nombre';
    public $id;
    public $descripcion;
    public $nombre;

    public static function get_table_name(): string {
        return 'talentospilos_estados_ases';
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