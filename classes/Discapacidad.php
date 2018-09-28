<?php
require_once(__DIR__.'/DAO/IBaseDAO.php');
require_once(__DIR__.'/DAO/BaseDAO.php');

class Discapacidad extends BaseDAO implements IBaseDAO {
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

    public function format() {
        return $this;
    } 
    /**
     * Obtener las discapacidades con una descripcion legible
     * @return array Array donde las llaves son los id de las discapacidades y el valor es el nombre de la discapacidad
     */
    public static function get_options() {
        $discapacidades = Discapacidad::get_all();
        $opciones = array();
        foreach($discapacidades as $discapacidad) {
            $opciones[$discapacidad->id] = $discapacidad->nombre;
        }
        return $opciones;
    }

}

?>