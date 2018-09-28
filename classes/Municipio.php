<?php
require_once(__DIR__.'/DAO/BaseDAO.php');
class Municipio extends BaseDAO  {
    public $id;
    public $codigodivipola;
    public $cod_depto;
    public $nombre;

    const NO_REGISTRA = 'NO REGISTRA';
    /**
     * Retorna el municipio por defecto
     * @return Municipio Municipio por defecto
     * @throws
     */
    public static function get_municipio_por_defecto( ) {
        global $DB;
        $nombre_municipio = 'CALI';
        $sql = 
        "
        SELECT * FROM {talentospilos_municipio}
        WHERE nombre = '$nombre_municipio'
        ";
        $municipio_db = $DB->get_record_sql($sql);

        $municipio = new Municipio();
        $municipio->from_std_object_or_array($municipio_db);
        return $municipio;
    }
    public function format() {
        return $this;
    }
    public static function get_table_name(): string {
        return 'talentospilos_municipio';
    }
    /**
     * Obtener los municipios con una descripcion legible
     * @return array Array donde las llaves son los id de los municipios y el valor es el nombre del municipio
     */
    public static function get_options() {
        $municipios = Municipio::get_all();
        $opciones = array();
        foreach($municipios as $municipio) {
            $opciones[$municipio->id] = $municipio->nombre;
        }
        return $opciones;
    }
}

?>