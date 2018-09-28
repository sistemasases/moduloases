<?php
require_once(__DIR__.'/traits/from_std_object_or_array.php');
require_once(__DIR__.'/DAO/IBaseDAO.php');
require_once(__DIR__.'/DAO/BaseDAO.php');
class TipoDocumento extends BaseDAO implements IBaseDAO {
    public $id;
    public $nombre;
    public $descripcion;
    /**
     * See BaseDAO::get_table_name
     */
    public static function get_table_name(): string {
        return 'talentospilos_tipo_documento';
    }
    /**
     * Obtener los tipos de documento con una descripcion legible
     * @return array Array donde las llaves son los id de los tipos de documento y el valor es el nombre del municipio
     */
    public static function get_options() {
        $tipos_documento = TipoDocumento::get_all();
        $opciones = array();
        foreach($tipos_documento as $tipo_documento) {
            $opciones[$tipo_documento->id] = $tipo_documento->descripcion;
        }
        return $opciones;
    }
    public function format() {
        return $this;
    }
}

?>