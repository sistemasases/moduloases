<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 11/10/18
 * Time: 12:59 PM
 */

require_once(__DIR__ . '/DAO/BaseDAO.php');


/**
 * Class EstadoAsesRegistro
 *
 * Hace referencia a un registro asignado a un estudiante ASES de EstadoAses
 * @see Table talentosplilos_est_estadoases
 */
class EstadoAsesRegistro extends BaseDAO {

    const ID_ESTUDIANTE = 'id_estudiante';
    const ID_ESTADO_ASES = 'id_estado_ases';
    const ID_MOTIVO_RETIRO = 'id_motivo_retiro';
    const ID_INSTANCIA = 'id_instancia';
    const FECHA = 'fecha';

    public $id;
    public $id_estudiante;
    public $id_estado_ases;
    public $id_motivo_retiro;
    public $fecha;
    public $id_instancia;
    public function __construct($data = null)
    {
        parent::__construct($data);
        $this->fecha = time();

    }

    public static function get_table_name(): string
    {
        return 'talentospilos_est_estadoases';
    }
    public static function get_not_null_fields_and_default_in_db(): array
    {
        return array(EstadoAsesRegistro::ID_INSTANCIA);
    }

    public static function get_numeric_fields(): array
    {
        return array(
            EstadoAsesRegistro::ID_ESTADO_ASES,
            EstadoAsesRegistro::ID_ESTUDIANTE,
            EstadoAsesRegistro::ID_INSTANCIA,
            EstadoAsesRegistro::ID_MOTIVO_RETIRO,
            EstadoAsesRegistro::FECHA
        );
    }

}