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
 * Create program view
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once __DIR__ . '/../../../config.php';

require_once(__DIR__ . '/../classes/mdl_forms/search_ases_user_form.php');
require_once(__DIR__ . '/../classes/AsesUser.php');
include "../classes/output/renderer.php";
include "../classes/output/progress_bar_component.php";
include '../managers/students_finalgrade_report/students_finalgrade_report_lib.php';

$pagetitle = 'Busqueda de usuarios ASES';
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);
$next_url= optional_param('next_url', null, PARAM_PATH);

require_login($courseid, false);

$url = new moodle_url("/blocks/ases/view/search_ases_user.php",
    array(
        'courseid' => $courseid,
        'instanceid' => $blockid,
        'next_url'=>$next_url)
);
$PAGE->set_url($url);
$PAGE->set_title($pagetitle);

$output = $PAGE->get_renderer('block_ases');

$search_ases_user_form = new search_ases_user_form($url);

echo $output->header();

if ($search_ases_user_form->is_submitted() && $search_ases_user_form->is_validated()) {
    $search_form_data = $search_ases_user_form->get_data();
    $user_exists = AsesUser::exists(array(
        AsesUser::NUMERO_DOCUMENTO=>$search_form_data->num_doc
    ));
    if( $user_exists ) {
        $ases_users = AsesUser::get_by(array(
            AsesUser::NUMERO_DOCUMENTO=>$search_form_data->num_doc
        ));
        //print_r($ases_users);
    } else {
        \core\notification::info('No existe ningun usuario que cumpla con las condiciones dadas');
    }

} else {

}
/*
$c_query = _select_cursos_ases_with_teacher()->compile();
//echo $c_query->sql();
print_r($c_query->params());
print_r($c_query->sql());
echo '<pre>';
$ases_courses_with_teacher= $DB->get_records_sql($c_query->sql(), $c_query->params());
print_r($ases_courses_with_teacher);*/
$semestre_object = get_current_semester();
$sem = $semestre_object->nombre;
$id_semestre = $semestre_object->max;
$año = substr($sem,0,4);

if(substr($sem,4,1) == 'A'){
    $semestre = $año.'02';
}else if(substr($sem,4,1) == 'B'){
    $semestre = $año.'08';
}
echo $semestre;
$sql = <<<SQL
SELECT DISTINCT ON (mdl_user.id) moodle_courses.fullname, mdl_user.firstname, mdl_user.lastname, moodle_courses.curso_id FROM 
  
    {user} as mdl_user
    inner join {role_assignments} as mdl_role_assignments
    on mdl_user.id = mdl_role_assignments.userid
    inner join {role} as mdl_role
    on mdl_role.id = mdl_role_assignments.roleid
   
    
    inner join {context} as mdl_context
    on mdl_context.id = mdl_role_assignments.contextid
    inner join (
        SELECT DISTINCT curso.id as curso_id, curso.fullname, curso.shortname    
                   FROM {course} curso
            INNER JOIN {enrol} ROLE ON curso.id = role.courseid
            INNER JOIN {user_enrolments} enrols ON enrols.enrolid = role.id
            WHERE SUBSTRING(curso.shortname FROM 15 FOR 6) = '$semestre' AND enrols.userid IN
                (SELECT user_m.id
                    FROM  {user} user_m
                    INNER JOIN {talentospilos_user_extended} extended ON user_m.id = extended.id_moodle_user
                    INNER JOIN {talentospilos_usuario} user_t ON extended.id_ases_user = user_t.id
                    INNER JOIN {talentospilos_est_estadoases} estado_u ON user_t.id = estado_u.id_estudiante
                    INNER JOIN {talentospilos_estados_ases} estados ON estados.id = estado_u.id_estado_ases
                    WHERE estados.nombre = 'seguimiento' )
                    ) AS moodle_courses
      on moodle_courses.curso_id = mdl_context.instanceid
      where mdl_role_assignments.roleid = 3

SQL;

$cursos = $DB->get_records_sql($sql);



echo '<pre>';
foreach($cursos as $ases_course) {
    $ases_course->items_with_grades = get_items_con_notas($ases_course->curso_id);
}
print_r($cursos);
echo '</pre>';

/*
$semestre_object = get_current_semester();
$sem = $semestre_object->nombre;
$id_semestre = $semestre_object->max;
$año = substr($sem,0,4);

if(substr($sem,4,1) == 'A'){
    $semestre = $año.'02';
}else if(substr($sem,4,1) == 'B'){
    $semestre = $año.'08';
}
$sql = <<<SQL
SELECT DISTINCT ON (mdl_user.id) * FROM 
  
    {user} as mdl_user
    inner join {role_assignments} as mdl_role_assignments
    on mdl_user.id = mdl_role_assignments.userid
    inner join {role} as mdl_role
    on mdl_role.id = mdl_role_assignments.roleid
   
    
    inner join {context} as mdl_context
    on mdl_context.id = mdl_role_assignments.contextid
    inner join (
        SELECT DISTINCT curso.id as curso_id, curso.fullname, curso.shortname    
                   FROM {course} curso
            INNER JOIN {enrol} ROLE ON curso.id = role.courseid
            INNER JOIN {user_enrolments} enrols ON enrols.enrolid = role.id
            WHERE SUBSTRING(curso.shortname FROM 15 FOR 6) = '$semestre' AND enrols.userid IN
                (SELECT user_m.id
                    FROM  {user} user_m
                    INNER JOIN {talentospilos_user_extended} extended ON user_m.id = extended.id_moodle_user
                    INNER JOIN {talentospilos_usuario} user_t ON extended.id_ases_user = user_t.id
                    INNER JOIN {talentospilos_est_estadoases} estado_u ON user_t.id = estado_u.id_estudiante
                    INNER JOIN {talentospilos_estados_ases} estados ON estados.id = estado_u.id_estado_ases
                    WHERE estados.nombre = 'seguimiento' )
                    ) AS moodle_courses
      on moodle_courses.curso_id = mdl_context.instanceid

SQL;
echo '<pre>';

print_r($DB->get_records_sql($sql));
echo '</pre>';

*/

$search_ases_user_form->display();

echo $output->footer();

?>