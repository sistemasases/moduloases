<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 11/10/18
 * Time: 01:23 PM
 */
require_once(__DIR__ . '/DAO/BaseDAO.php');

class EstadoIcetex extends BaseDAO {


    public $id;
    public $nombre;
    const NOMBRE = 'nombre';
    const NOMBRE_DEFAULT_ESTADO_ICETEX = '5. IES renovó, ICETEX pendiente de giro';
    public $descripcion;
    public static function get_table_name(): string
    {
        return 'talentospilos_estados_icetex';
    }

    /**
     * If for some razon of destinity, the default icetex state does not exist
     * this should be return false, but is in very exceptional cases
     * @return EstadoIcetex|bool
     * @throws ErrorException
     * @throws dml_exception
     */
    public static function get_default_estado_icetex() {
        return EstadoIcetex::get_by(array(EstadoIcetex::NOMBRE=>EstadoIcetex::NOMBRE_DEFAULT_ESTADO_ICETEX));
    }
}