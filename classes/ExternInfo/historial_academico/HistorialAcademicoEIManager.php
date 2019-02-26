<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 15/02/19
 * Time: 10:42 AM
 */
require_once (__DIR__ . '/../ExternInfoManager.php');
require_once (__DIR__ . '/HistorialAcademicoEI.php');


require_once (__DIR__ . '/../../module.php');
class HistorialAcademicoEIManager extends ExternInfoManager
{
    public function __construct()
    {
        parent::__construct(HistorialAcademicoEI::class);
    }

    public function persist_data()
    {
        $items = $this->get_objects();
        /** @var $item HistorialAcademicoEI */
        foreach($items as $key => $item_) {
            if(!$item_->valid()) {
                return false;
            }
            $item = clone $item_;
            HistorialAcademicoEI::clean($item);
            /** @var $historial_academico HistorialAcademico */
            $historial_academico = $item->extract_historial_academico();
            $historial_academico_col_id_programa = HistorialAcademico::ID_PROGRAMA;
            $historial_academico_col_id_estudiante= HistorialAcademico::ID_ESTUDIANTE;
            $historial_academico_col_id_semestre= HistorialAcademico::ID_SEMESTRE;
            if(HistorialAcademico::exists_select(
                "
                $historial_academico_col_id_programa = :id_programa
              AND $historial_academico_col_id_estudiante = :id_estudiante
              AND $historial_academico_col_id_semestre = :id_semestre
                ",
                array(
                    'id_programa'=>$historial_academico->id_programa,
                    'id_estudiante'=>$historial_academico->id_estudiante,
                    'id_semestre' => $historial_academico->id_semestre))) {
               $this->add_generic_object_errors([new AsesError(-1, "El estudiante con número documento $item->numero_documento ya tenia un registro
               de historial academico en este semestre en esta carrea, no se actualiza nada")], $key);
               return false;
               continue;
            } else {
                if($historial_academico->valid()) {
                    $historial_academico->save();
                    $this->add_success_log_event("El historial academico se ha creado", $key);
                } else {
                    $this->add_generic_object_errors($historial_academico->get_errors(), $key);
                    return false;
                }

            }
        }
        return true;
    }
    public function custom_column_mapping()
    {
        return array(
            'cod_programa_univalle' => HistorialAcademicoEI::CODIGO_PGORAMA_UNVALLE
        );
    }
}