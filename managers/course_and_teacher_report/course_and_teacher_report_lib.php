<?php


use function jquery_datatable\get_datatable_class_column;
require_once(__DIR__ . '/../../../../config.php');
require_once (__DIR__ . '/../jquery_datatable/jquery_datatable_lib.php');
require_once (__DIR__ . '/../../managers/periods_management/periods_lib.php');
require_once (__DIR__ . '/../../classes/DAO/BaseDAO.php');

require_once (__DIR__ . '/../course/course_lib.php');

require_once(__DIR__.'/../../vendor/autoload.php');

error_reporting(E_ALL | E_STRICT);   // NOT FOR PRODUCTION SERVERS!
ini_set('display_errors', '1');         // NOT FOR PRODUCTION SERVERS!
$CFG->debug = (E_ALL | E_STRICT);   // === DEBUG_DEVELOPER - NOT FOR PRODUCTION SERVERS!
$CFG->debugdisplay = 1;

use Latitude\QueryBuilder\Query\SelectQuery;
use function Latitude\QueryBuilder\{ alias, on, field, QueryInterface, criteria, literal };
error_reporting(E_ALL);

/**
 * Class ItemReporteCursoProfesores
 *
 * @property string $curso Codigo del curso seguid de el Nombre completo de el curso moodle
 *  ejemplo: PSICOLOGÍA EDUCATIVA I  402200M
 * @property int curso_id Id de el curso moodle
 * @property 'SI'|'NO' $critica String indicando si la materia es critica o no
 * @property string $nombre_profesor Nombre completo de el profesor (nombres y apellidos)
 * @property int $estudiantes_sin_ninguna_nota  Estudiantes que no han recibido ninguna nota en el curso, ninguna nota
 *  en una actividad en la cual almenos un estudiante halla recibido nota
 * @property int $estudiantes_perdiendo Estudiantes que van perdido la mitad o más de la cantidad total de items
 *  calificados hasta el momento
 * @property int $estudiantes_ganando Estudiantes que van ganando la mitad o más de la cantidad total de items que se
 *  han calificado hasta el momento
 * @property int $cantidad_estudiantes_ases Cantidad de estudiatnes ASES con seguimiento activo matriculados en el curso
 * @property int $items_con_almenos_una_nota Cantidad de items los cuales se le han calificado para almenos un usuario ASES
 * @property int $cantidad_items Cantidad de items para calificación creados en el curso
 *  Se excluyen los totales
 */
class ItemReporteCursoProfesores {};

/**
 * Return all courses with teacher and aditional info about students course status based in moodle grade items
 * @see https://docs.moodle.org/all/es/%C3%8Dtems_de_calificaci%C3%B3n
 * @see Tables: mdl_grade_items, mdl_user, mdl_course, mdl_grade_grades
 * @return array Array of ItemReporteCursoProfesores
 * @throws dml_exception
 */

function get_reporte_curso_profesores($id_instancia) {
    global $DB;
    $semestre_object = get_current_semester();
    $sem = $semestre_object->nombre;
    $año = substr($sem,0,4);

    if(substr($sem,4,1) == 'A'){
        $semestre = $año.'02';
    }else if(substr($sem,4,1) == 'B'){
        $semestre = $año.'08';
    }

    $sql = <<<SQL
 SELECT DISTINCT ON ( moodle_course.curso_id ) 
                moodle_course.curso_id,
                concat_ws(' ',  moodle_course.fullname, substring(moodle_course.shortname from 4 for 7)) as curso,
                Concat_ws(' ', mdl_user.firstname, mdl_user.lastname) AS nombre_profesor,
                case
      when (select id from {course} mdl_course
            where mdl_course.id = moodle_course.curso_id
            and substring(mdl_course.shortname from 4 for 7)
            in (
            select mdl_talentospilos_materias_criti.codigo_materia
            from mdl_talentospilos_materias_criti

            ) limit 1) is null
      then 'NO'
      else
          'SI'
      END AS  critica,
                (
                    select count(*) filter (where first_note is null)  from (
SELECT distinct  on (id) *
  FROM   (
         SELECT     firstname, _mdl_user.id,
                    (
                    SELECT DISTINCT  ON( mdl_grade_grades.finalgrade) finalgrade
                    FROM            mdl_grade_grades
             INNER JOIN      {user} mdl_user
             ON              mdl_user.id = mdl_grade_grades.userid
             INNER JOIN      {grade_items} mdl_grade_items
             ON              mdl_grade_items.id = mdl_grade_grades.itemid
             INNER JOIN      {course} mdl_course
             ON              mdl_course.id = mdl_grade_items.courseid
             WHERE           mdl_user.id = _mdl_user.id
             AND             mdl_course.id = _mdl_course.id
             ORDER BY        mdl_grade_grades.finalgrade ASC limit 1) AS first_note --if at least one grade is not null the first is this, otherwise the first note is null
      FROM       {user} as _mdl_user
      INNER JOIN  mdl_talentospilos_user_extended
      ON         _mdl_user.id = mdl_talentospilos_user_extended.id_moodle_user
      AND        mdl_talentospilos_user_extended.tracking_status = 1

      INNER JOIN mdl_role_assignments
      ON         _mdl_user.id = mdl_role_assignments.userid
      INNER JOIN  mdl_talentospilos_est_estadoases
      ON mdl_talentospilos_est_estadoases.id_estudiante = mdl_talentospilos_user_extended.id_ases_user
      inner join mdl_talentospilos_estados_ases
      on mdl_talentospilos_est_estadoases.id_estado_ases = mdl_talentospilos_estados_ases.id
      INNER JOIN  mdl_context
      ON         mdl_context.id = mdl_role_assignments.contextid
      INNER JOIN mdl_course as _mdl_course
      ON         _mdl_course.id = mdl_context.instanceid
      WHERE      _mdl_course.id = moodle_course.curso_id
      AND mdl_talentospilos_estados_ases.nombre = 'seguimiento'
      AND        mdl_role_assignments.roleid = 5)as notas_mas_bajas_estudiantes
     where  notas_mas_bajas_estudiantes.first_note IS NULL) as estudiantes_sin_ninguna_nota ) AS estudiantes_sin_ninguna_nota,
                -- Cantidad de estudiantes con mas de el 50% de las notas calificadas perdidas o no entregadas
                (
                       SELECT count(*)
                       FROM   (
                                         --Usuarios  con mas de el 50% de items perdidos en una materia
                                         SELECT     count(finalgrade) filter (WHERE finalgrade < grademax * 0.6 ) AS cantidad_notas_perdidas ,
                                                    _mdl_user.*,
                                                    (
                                                           SELECT count(*)
                                                           FROM   (
                                                                                  SELECT DISTINCT ON (mdl_grade_items.id) mdl_grade_items.id
                                                                                  FROM            {grade_items} mdl_grade_items
                                                                                  INNER JOIN      {grade_grades} mdl_grade_grades
                                                                                  ON              mdl_grade_items.id = mdl_grade_grades.itemid
                                                                                  WHERE           mdl_grade_items.courseid = moodle_course.curso_id
                                                                                  AND             mdl_grade_items.itemtype != 'category'
                                                                                  AND             mdl_grade_items.itemtype != 'course'
                                                                                  AND             finalgrade IS NOT NULL
                                                                                  EXCEPT
                                                                                  SELECT     mdl_grade_items.id
                                                                                  FROM       {grade_items} mdl_grade_items
                                                                                  INNER JOIN {grade_grades} mdl_grade_grades
                                                                                  ON         mdl_grade_items.id = mdl_grade_grades.itemid
                                                                                  INNER JOIN {user} mdl_user
                                                                                  ON         mdl_grade_grades.userid = mdl_user.id
                                                                                  WHERE      mdl_grade_items.courseid = moodle_course.curso_id
                                                                                  AND        mdl_user.id = _mdl_user.id
                                                                                  AND        mdl_grade_grades.finalgrade IS NOT NULL) AS cantidad_notas_calificadas_y_no_entregadas) AS cantidad_notas_calificadas_y_no_entregadas
                                         FROM       {grade_grades} mdl_grade_grades
                                         INNER JOIN {grade_items} mdl_grade_items
                                         ON         mdl_grade_items.id = mdl_grade_grades.itemid
                                         INNER JOIN {user} AS _mdl_user
                                         ON         _mdl_user.id = mdl_grade_grades.userid
                                         INNER JOIN {talentospilos_user_extended} mdl_talentospilos_user_extended
                                         ON         _mdl_user.id = mdl_talentospilos_user_extended.id_moodle_user
                                                     inner join mdl_talentospilos_est_estadoases
            on mdl_talentospilos_est_estadoases.id_estudiante = mdl_talentospilos_user_extended.id_ases_user
            inner join mdl_talentospilos_estados_ases
            on mdl_talentospilos_est_estadoases.id_estado_ases = mdl_talentospilos_estados_ases.id
                                         WHERE      mdl_grade_items.courseid = moodle_course.curso_id
                                         AND        mdl_grade_items.itemtype != 'category'
                                         AND        mdl_talentospilos_user_extended.tracking_status = 1
                                         AND        mdl_grade_items.itemtype != 'course'
                                         and mdl_talentospilos_estados_ases.nombre = 'seguimiento'
                                         AND        mdl_grade_grades.finalgrade IS NOT NULL
                                         GROUP BY   _mdl_user.id) AS usuarios_y_notas_perdidas
                       WHERE  (
                                     usuarios_y_notas_perdidas.cantidad_notas_perdidas + usuarios_y_notas_perdidas.cantidad_notas_calificadas_y_no_entregadas) >= --usuarios perdiendo la mitad o mas de las notas
                              (
                                     --Piso de el 50% de items de un curso
                                     SELECT ceil(count(*) * 0.5)
                                     FROM   (
                                                            SELECT DISTINCT ON (mdl_grade_items.id) *
                                                            FROM            {grade_items} mdl_grade_items
                                                            INNER JOIN      {grade_grades} mdl_grade_grades
                                                            ON              mdl_grade_items.id = mdl_grade_grades.itemid
                                                            WHERE           mdl_grade_items.courseid = moodle_course.curso_id
                                                            AND             mdl_grade_items.itemtype != 'category'
                                                            AND             mdl_grade_items.itemtype != 'course' 
                                                            AND             finalgrade IS NOT NULL) AS a )) AS estudiantes_perdiendo ,
                (
                     SELECT count(*)
                       FROM   (
                                         --Usuarios  con mas de el 50% de items perdidos en una materia
                                         SELECT     count(finalgrade) filter (WHERE finalgrade < grademax * 0.6 ) AS cantidad_notas_perdidas ,
                                                    _mdl_user.*,
                                                    (
                                                           SELECT count(*)
                                                           FROM   (
                                                                                  SELECT DISTINCT ON (mdl_grade_items.id) mdl_grade_items.id
                                                                                  FROM            {grade_items} mdl_grade_items
                                                                                  INNER JOIN      {grade_grades} mdl_grade_grades
                                                                                  ON              mdl_grade_items.id = mdl_grade_grades.itemid
                                                                                  WHERE           mdl_grade_items.courseid = moodle_course.curso_id
                                                                                  AND             mdl_grade_items.itemtype != 'category'
                                                                                  AND             mdl_grade_items.itemtype != 'course'
                                                                                  AND             finalgrade IS NOT NULL
                                                                                  EXCEPT
                                                                                  SELECT     mdl_grade_items.id
                                                                                  FROM       {grade_items} mdl_grade_items
                                                                                  INNER JOIN {grade_grades} mdl_grade_grades
                                                                                  ON         mdl_grade_items.id = mdl_grade_grades.itemid
                                                                                  INNER JOIN {user} mdl_user
                                                                                  ON         mdl_grade_grades.userid = mdl_user.id
                                                                                  WHERE      mdl_grade_items.courseid = moodle_course.curso_id
                                                                                  AND        mdl_user.id = _mdl_user.id
                                                                                  AND        mdl_grade_grades.finalgrade IS NOT NULL) AS cantidad_notas_calificadas_y_no_entregadas) AS cantidad_notas_calificadas_y_no_entregadas
                                         FROM       {grade_grades} mdl_grade_grades
                                         INNER JOIN {grade_items} mdl_grade_items
                                         ON         mdl_grade_items.id = mdl_grade_grades.itemid
                                         INNER JOIN {user} AS _mdl_user
                                         ON         _mdl_user.id = mdl_grade_grades.userid
                                         INNER JOIN {talentospilos_user_extended} mdl_talentospilos_user_extended
                                         ON         _mdl_user.id = mdl_talentospilos_user_extended.id_moodle_user
                                                     inner join mdl_talentospilos_est_estadoases
            on mdl_talentospilos_est_estadoases.id_estudiante = mdl_talentospilos_user_extended.id_ases_user
            inner join mdl_talentospilos_estados_ases
            on mdl_talentospilos_est_estadoases.id_estado_ases = mdl_talentospilos_estados_ases.id
                                         WHERE      mdl_grade_items.courseid = moodle_course.curso_id
                                         AND        mdl_grade_items.itemtype != 'category'
                                         AND        mdl_talentospilos_user_extended.tracking_status = 1
                                         AND        mdl_grade_items.itemtype != 'course'
                                         and mdl_talentospilos_estados_ases.nombre = 'seguimiento'
                                         AND        mdl_grade_grades.finalgrade IS NOT NULL
                                         GROUP BY   _mdl_user.id) AS usuarios_y_notas_perdidas
                       WHERE  (
                                     usuarios_y_notas_perdidas.cantidad_notas_perdidas + usuarios_y_notas_perdidas.cantidad_notas_calificadas_y_no_entregadas)< --usuarios perdiendo la mitad o mas de las notas
                              (
                                     --Piso de el 50% de items de un curso
                                     SELECT ceil(count(*) * 0.5)
                                     FROM   (
                                                            SELECT DISTINCT ON (mdl_grade_items.id) *
                                                            FROM            {grade_items} mdl_grade_items
                                                            INNER JOIN      {grade_grades} mdl_grade_grades
                                                            ON              mdl_grade_items.id = mdl_grade_grades.itemid
                                                            WHERE           mdl_grade_items.courseid = moodle_course.curso_id
                                                            AND             mdl_grade_items.itemtype != 'category'
                                                            AND             mdl_grade_items.itemtype != 'course' 
                                                            AND             finalgrade IS NOT NULL) AS a )) AS estudiantes_ganando,
                (
                select count(*) from (
                          SELECT    distinct  on (mdl_user.id) mdl_user.id
FROM       mdl_user
             INNER JOIN mdl_role_assignments
               ON         mdl_user.id = mdl_role_assignments.userid
             INNER JOIN mdl_context
               ON         mdl_context.id = mdl_role_assignments.contextid
             INNER JOIN mdl_course
               ON         mdl_course.id = mdl_context.instanceid
             INNER JOIN mdl_talentospilos_user_extended
               ON         mdl_user.id = mdl_talentospilos_user_extended.id_moodle_user
            inner join mdl_talentospilos_est_estadoases
            on mdl_talentospilos_est_estadoases.id_estudiante = mdl_talentospilos_user_extended.id_ases_user
            inner join mdl_talentospilos_estados_ases
            on mdl_talentospilos_est_estadoases.id_estado_ases = mdl_talentospilos_estados_ases.id
WHERE      mdl_role_assignments.roleid = 5
  AND        mdl_talentospilos_user_extended.tracking_status = 1
  AND        mdl_course.id = moodle_course.curso_id
and mdl_talentospilos_estados_ases.nombre = 'seguimiento' ) AS id_estudiantes_ases ) AS cantidad_estudiantes_ases,
                (
                       SELECT count(*)
                       FROM   (
                                              SELECT DISTINCT  ON ( mdl_grade_items.id) *
                                              FROM            {grade_items}  AS mdl_grade_items
                                              INNER JOIN      {grade_grades} AS mdl_grade_grades
                                              ON              mdl_grade_items.id = mdl_grade_grades.itemid
                                              WHERE           mdl_grade_items.courseid = moodle_course.curso_id
                                              AND             mdl_grade_items.itemtype != 'category'
                                              AND             mdl_grade_items.itemtype != 'course'
                                              AND             finalgrade IS NOT NULL) a )AS items_con_almenos_una_nota,
                (
                       SELECT count(*)
                       FROM   {grade_items} AS mdl_grade_items
                       WHERE  mdl_grade_items.courseid = moodle_course.curso_id
                       AND    mdl_grade_items.itemtype != 'course'
                       AND    mdl_grade_items.itemtype != 'category' ) AS cantidad_items
FROM            {user}                                                 AS mdl_user
INNER JOIN      {role_assignments}                                     AS mdl_role_assignments
ON              mdl_user.id = mdl_role_assignments.userid
INNER JOIN      {role} AS mdl_role
ON              mdl_role.id = mdl_role_assignments.roleid
INNER JOIN      {context} AS mdl_context
ON              mdl_context.id = mdl_role_assignments.contextid
INNER JOIN
                (
          select distinct  on (mdl_course.id)
                            mdl_course.id AS curso_id,
                            mdl_course.fullname,
                            mdl_course.shortname
        from {user} mdl_user
        inner join {talentospilos_user_extended} mdl_talentospilos_user_extended
            on mdl_user.id = mdl_talentospilos_user_extended.id_moodle_user
        inner join {cohort_members} mdl_cohort_members
            on mdl_cohort_members.userid = mdl_user.id
        inner join {talentospilos_inst_cohorte} mdl_talentospilos_inst_cohorte
            on mdl_talentospilos_inst_cohorte.id_cohorte = mdl_cohort_members.cohortid
        inner join {role_assignments} mdl_role_assignments
            on mdl_role_assignments.userid = mdl_user.id
        inner join {context} mdl_context
            on mdl_role_assignments.contextid = mdl_context.id
        inner join {course} mdl_course
            on mdl_course.id = mdl_context.instanceid
        inner join {talentospilos_est_estadoases} mdl_talentospilos_est_estadoases
            on mdl_talentospilos_user_extended.id_ases_user = mdl_talentospilos_est_estadoases.id_estudiante
        inner join {talentospilos_estados_ases} mdl_talentospilos_estados_ases
            on mdl_talentospilos_est_estadoases.id_estado_ases = mdl_talentospilos_estados_ases.id
        where mdl_talentospilos_estados_ases.nombre='seguimiento'
        and mdl_talentospilos_inst_cohorte.id_instancia = $id_instancia
        AND mdl_talentospilos_user_extended.tracking_status = 1
        and mdl_role_assignments.roleid = 5
        and substring(mdl_course.shortname from 15 for 6) = '$semestre' ) AS moodle_course
        ON              moodle_course.curso_id = mdl_context.instanceid
        WHERE           mdl_role_assignments.roleid = 3
SQL;

    return $DB->get_records_sql($sql);
}

/**
 * Retorna los mismos datos de get_reporte_curso_profesores, pero para los cursos que no tienen profesor
 *
 * La cantidad de items, estudiantes sin nota, etc, se rellenan con ceros
 * @param $instance_id
 * @param $semester
 */
function get_reporte_cursos_sin_profesor($instance_id, $semester): array {
    $cursos_sin_profesor = array_values(get_ases_courses_without_teachers($instance_id, $semester));
    $report_items = array();
    foreach($cursos_sin_profesor as $curso_sin_profesor) {
        /* @var $report_item ItemReporteCursoProfesores */
        $report_item = new stdClass();
        $report_item->cantidad_items = 0;
        $report_item->estudiantes_ganando = 0;
        $report_item->estudiantes_perdiendo= 0;
        $report_item->estudiantes_sin_ninguna_nota = 0;
        $report_item->curso = $curso_sin_profesor->fullname . 'TODO';
        $report_item->curso_id= $curso_sin_profesor->id;
        $report_item->cantidad_estudiantes_ases = 'TODO';
        $report_item->items_con_almenos_una_nota = 0;
        $report_item->nombre_profesor = 'No registra';

        array_push($report_items, $report_item);
    }
    return $report_items;
}

/**
 * Return a datatable formated as array with all information needed for course and teacher report by items
 * @param string $instance_id
 * @param boolean $append_courses_whitout_teachers Append info referetn to courses than have ASES students,
 *  but has no teacher assigned in moodle.
 * @return array Datatable with indexs: {bsort, columns, data, language, order}
 */
function get_datatable_for_course_and_teacher_report($instance_id, $append_courses_whitout_teachers= false) {
    $common_language_config = \jquery_datatable\get_datatable_common_language_config();
    $columns = array();
    /* Index of column  'Est < 50' (starting from 0)*/

    $est_lt_50_colum = array(
        "title"=>"Est < 50%",
        "name"=>"estudiantes_perdiendo",
        "data"=>"estudiantes_perdiendo",
        'description'=>'Estudiantes perdiendo más de la mitad de los items calificados',
        'className'=>'estudiantes_perdiendo');

    $data = array_values(get_reporte_curso_profesores($instance_id));

    array_push($columns, get_datatable_class_column());
    array_push($columns, array("title"=>"Curso", "name"=>'curso', "data"=>"curso"));
    array_push($columns, array(
        "title"=>"Profesor",
        "name"=>"nombre_profesor",
        "data"=>"nombre_profesor",
        "description"=>'Nombre del profesor'));

    array_push($columns, $est_lt_50_colum);

    array_push($columns, array(
        "title"=>"Est >=50%",
        "name"=>"estudiantes_ganando",
        "data"=>"estudiantes_ganando",
        "description"=>"Estudiantes ganando más de la mitad de los items calificados"));

    array_push($columns, array(
        "title"=>"Est. sin notas",
        "name"=>"estudiantes_sin_ninguna_nota",
        "data"=>"estudiantes_sin_ninguna_nota",
        "description"=>"Estudiantes sin ningún item calificado"));

    array_push($columns, array(
        "title"=>"Estudiantes ASES",
        "name"=>"cantidad_estudiantes_ases",
        "data"=>"cantidad_estudiantes_ases",
        'description'=>'Cantidad de estudiantes ASES'));

    array_push($columns, array(
        "title"=>"Items  calificados",
        "name"=>"items_con_almenos_una_nota",
        "data"=>"items_con_almenos_una_nota",
        'description'=>'Cantidad de items en los cuales almenos un estudiante tiene una nota',
        'className'=>'items_con_almenos_una_nota'));

    array_push($columns, array(
        "title"=>"Cantidad de items",
        "name"=>"cantidad_items",
        "data"=>"cantidad_items",
        'description'=>'Cantidad de items calificables de el curso',
        'className'=>'cantidad_items'));

    array_push($columns, array(
        "title"=>"Es critica",
        "name"=>"critica",
        "data"=>"critica",
        "description"=>'Indica si la materia ha sido marcada como critica por ASES'));

    // The previous order of columns may be change, because that we need search the actual index of this column
    // at execution time
    $est_lt_50_colum_index = array_search($est_lt_50_colum, $columns);
    $data_table = array(
        "bsort" => false,
        "columns" => $columns,
        "data" => $data,
        "language" => $common_language_config,
        "order"=> array($est_lt_50_colum_index, "desc")

    );
    return $data_table;
}