<?php

require_once(__DIR__ . '/DAO/BaseDAO.php');
require_once(__DIR__ . '/HistorialAcademico_Materia.php');
require_once(__DIR__ . '/../managers/lib/json.php');
class HistorialAcademico extends BaseDAO  {
    const ID = 'id';
    const ID_ESTUDIANTE = 'id_estudiante';
    const JSON_MATERIAS_SCHEMA_ALIAS = 'historial_academico_materias';
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
    /**
     * Representa el registro de notas (es un array) en formato JSON,
     *  este esta mapeado en la clase HistorialAcademico_Materia
     * @see HistorialAcademico_Materia
     * @var $json_materias string
     */
    public $json_materias;


    public static function get_table_name(): string {
        return 'talentospilos_history_academ';
    }

    /**
     * Returns the subjects converted in objects, probably but not sure
     *  than have the same properties than HistorialAcademico_Materia
     * @return HistorialAcademico_Materia|null
     */
    public function get_materias_object() {
        if($this->valid_json_materias()) {
            return (object) json_decode($this->json_materias);
        } else {
            return null;
        }
    }

    /**
     * Check if json materias is a valid json
     * @return bool
     */
    public function valid_json_materias() {
        if (\json\valid_json($this->json_materias)) {
            return true;
        } else {
            $this->add_error(JsonErrorsFactory::json_malformed($this->json_materias));
            return false;
        }
    }
    public function valid(): bool {
        return parent::valid() && $this->valid_json_materias();
    }
}
