<?php
require_once(__DIR__.'/DAO/BaseDAO.php');

class Discapacidad extends BaseDAO {
    const ID ='id';
    const CODIGO_MEN = 'codigo_men';
    const NOMBRE = 'nombre';

    const ID_NO_APLICA = 1;

    public $id;
    public $codigo_men;
    public $nombre;

    public static function get_table_name(): string {
        return 'talentospilos_discap_men';
    }


    /**
     * Obtener las discapacidades con una descripcion legible
     * @return array Array donde las llaves son los id de las discapacidades y el valor es el nombre de la discapacidad
     */
    public static function get_options() {
        $format = Discapacidad::ID.','.Discapacidad::NOMBRE;
        return parent::_get_options($format);

    }

}

?>