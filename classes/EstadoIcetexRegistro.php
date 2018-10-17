<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 11/10/18
 * Time: 01:21 PM
 */
require_once(__DIR__.'/DAO/BaseDAO.php');

class EstadoIcetexRegistro extends BaseDAO {

    public $id;
    const ID_ESTADO_ICETEX = 'id_estado_icetex';
    public $id_estado_icetex;
    public $id_estudiante;
    public $id_motivo_retiro;
    public $fecha;

    public function __construct($data = null) {
        parent::__construct($data);
        $this->fecha = time();
    }

    public static function get_table_name(): string {
        return 'talentospilos_est_est_icetex';
    }
}