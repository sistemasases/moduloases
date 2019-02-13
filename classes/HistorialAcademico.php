<?php

require_once(__DIR__ . '/DAO/BaseDAO.php');
class HistorialAcademico extends BaseDAO  {
    const ID = 'id';
    const ID_ESTUDIANTE = 'id_estudiante';
    const ID_SEMESTRE = 'id_semestre';
    const ID_PROGRAMA = 'id_programa';
    const PROMEDIO_SEMESTRE = 'promedio_semestre';
    const PROMEDIO_ACUMULADO = 'promedio_acumulado';
    public $id;
    public $id_estudiante;
    public $id_semestre;
    public $id_programa;
    public $promedio_semestre;
    public $promedio_acumulado;

    public static function get_table_name(): string {
        return 'talentospilos_history_academ';
    }
}

?>