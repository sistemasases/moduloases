<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Student item grades report lib
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2016 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once (__DIR__ . '/../jquery_datatable/jquery_datatable_lib.php');
require_once (__DIR__ . '/../../managers/periods_management/periods_lib.php');
use jquery_datatable;
/**
 * Class LosedAndAprovedItemGradesItemReport, model for objects returned at method get_losed_and_aproved_item_grades
 * @see get_losed_and_aproved_item_grades
 */
abstract class LosedAndAprovedItemGradesItemReport {
    public $cantidad_items_perdidos;
    public $cantidad_items_ganados;
    public $username;
    public $firstname;
    public $lastname;
    public $num_doc;
}

/**
 * Return counters of aproved or losed item grades agrouped  by student
 * @param $semestre string Current semester representation (example: 201808, 201804)
 *  if none is given, return the current semester data
 * @param $id_instancia string|int Instance id for filter the results
 */
function get_losed_and_aproved_item_grades($id_instancia, $semestre = null) {

    global $DB;

    if(!$semestre) {
        $semestre_object = get_current_semester();
        $sem = $semestre_object->nombre;
        $anio = substr($sem,0,4);

        if(substr($sem,4,1) == 'A'){
            $semestre = $anio.'02';
        }else if(substr($sem,4,1) == 'B'){
            $semestre = $anio.'08';
        }

    }

    $sql = <<<SQL
select  num_doc, count(*) filter(where not item_ganado) as cantidad_items_perdidos, count(*) filter (where item_ganado) as cantidad_items_ganados, username, mdl_talentospilos_usuario_id, firstname, lastname   from (
select
   distinct mdl_user.*, mdl_talentospilos_usuario.num_doc,
            case when (finalgrade < grademax * 0.6 or finalgrade is  null) then false else true end as item_ganado,
            mdl_talentospilos_usuario.id as mdl_talentospilos_usuario_id, finalgrade, grademax, mdl_grade_items.itemname, mdl_grade_items.id as item_id  ,
            (select count(*) from mdl_grade_grades as mdl_grade_grades_inner
                                    inner join mdl_grade_items as mdl_grade_items_inner
                                      on mdl_grade_grades_inner.itemid = mdl_grade_items_inner.id
             where mdl_grade_items_inner.courseid = mdl_course.id
               and mdl_grade_items_inner.id = mdl_grade_items.id
               and mdl_grade_grades.userid = mdl_user.id
               and mdl_grade_grades_inner.finalgrade is not null ) as calificaciones_item_todos_estudiantes
from mdl_talentospilos_usuario
      inner join mdl_talentospilos_user_extended
        on mdl_talentospilos_user_extended.id_ases_user = mdl_talentospilos_usuario.id
      inner join mdl_user
        on mdl_user.id = mdl_talentospilos_user_extended.id_moodle_user
    inner join mdl_cohort_members
    on mdl_cohort_members.userid = mdl_user.id
      inner join mdl_talentospilos_est_estadoases
        on mdl_talentospilos_user_extended.id_ases_user = mdl_talentospilos_est_estadoases.id_estudiante
      inner join mdl_talentospilos_estados_ases
        on mdl_talentospilos_est_estadoases.id_estado_ases = mdl_talentospilos_estados_ases.id
      inner join mdl_grade_grades
        on mdl_grade_grades.userid = mdl_user.id
      inner join mdl_grade_items
        on mdl_grade_items.id = mdl_grade_grades.itemid
      inner join mdl_course
        on mdl_grade_items.courseid= mdl_course.id

where substring(mdl_course.shortname from 15 for 6) =  '$semestre'
  and mdl_cohort_members.cohortid in (
    select id from mdl_talentospilos_inst_cohorte where mdl_talentospilos_inst_cohorte.id_instancia = $id_instancia
    )
 and mdl_grade_items.itemtype != 'category'
 AND  mdl_grade_items.itemtype != 'course'
 and mdl_talentospilos_user_extended.tracking_status = 1
 and mdl_talentospilos_estados_ases.nombre = 'seguimiento'
) as notas_estudiante
where notas_estudiante.calificaciones_item_todos_estudiantes > 0
group by (username, mdl_talentospilos_usuario_id,  firstname, lastname, num_doc)
;
SQL;
    return $DB->get_records_sql($sql);

}



/**
 * Return a datatable formated as array with all information needed for item grades agrouped by students
 * @param string $instance_id
 * @return array Datatable with indexs: {bsort, columns, data, language, order}
 */
function get_datatable_for_student_grades_report($instance_id) {

    $common_language_config = \jquery_datatable\get_datatable_common_language_config();
    $columns = array();
    $data = array_values(get_losed_and_aproved_item_grades($instance_id));
    $columna_items_perdidos =  array(
        "title"=>"# Items perdidos",
        "name"=>"cantidad_items_perdidos",
        "data"=>"cantidad_items_perdidos",
        "description"=>"Sumatoria de los items perdidos entre todos los cursos de el estudiante");
    array_push($columns, \jquery_datatable\get_datatable_class_column());
    array_push($columns, array(
        "title"=>"Número de documento",
        "name"=>'num_doc',
        "data"=>"num_doc",
        "description"=>"Número de docuento de el estudiante"));
    array_push($columns, array(
        "title"=>"Apellidos",
        "name"=>"lastname",
        "data"=>"lastname"));


    array_push($columns, array(
        "title"=>"Nombres",
        "name"=>"firstname",
        "data"=>"firstname"));

    array_push($columns, $columna_items_perdidos);

    array_push($columns, array(
        "title"=>"# Items ganados",
        "name"=>"cantidad_items_ganados",
        "data"=>"cantidad_items_ganados",
        'description'=>'Sumantoria de los items ganados entre todos los cursos de el estudiante'));

    $columna_items_perdidos_index = array_search($columna_items_perdidos, $columns);
    $data_table = array(
        "bsort" => false,
        "columns" => $columns,
        "data" => $data,
        "language" => $common_language_config,
        "order"=> array($columna_items_perdidos_index, "desc")
    );
    return $data_table;
}

