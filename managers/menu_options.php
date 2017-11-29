<?php
require_once (dirname(__FILE__) . '/../../../config.php');

require_once ('permissions_management/permissions_lib.php');
/**
 * Función que crea las opciones del menú dinámico
 * 
 */

function create_menu_options($userid, $blockid){

    $id_role = get_id_rol($userid, $blockid);
    $functions = get_functions_by_role_id($id_role);
    $menu_options = '';

    foreach($functions as $function){
        if($function == 'academic_reports'){
            $url = new moodle_url("/blocks/ases/view/academic_reports.php", array(
                'courseid' => $courseid,
                'instanceid' => $blockid
            ));

            $menu_options .= '<li><a href= "'. $url .'">Reportes académicos</a><li>';
            print_r($menu_options); 
        }
    }

    print_r($functions);

}