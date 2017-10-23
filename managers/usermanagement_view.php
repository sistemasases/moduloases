
<?php
    /**
    * Accion generada por el generador de codigo de moodle para el 
    * programa de talentos pilos de la universidad del valle
    * @author Edgar Mauricio Ceron Florez
    * @author ESCRIBA AQUI SU NOMBRE */
    require_once(dirname(__FILE__). '/../../../config.php');
    require('validate_profile_action.php');
    $accion = '5';
    global $USER;
    $id_instancia =required_param('instanceid', PARAM_INT);
    $moodle_id = $USER->id; 
    $user_id = get_talentos_id($moodle_id);
    //$perfil = get_perfil_usuario($user_id, $id_instancia);

    if(!$user_id){
      $perfil = get_perfil_usuario($moodle_id, $id_instancia);

    }

    if(validar_permisos($perfil, $accion)){
        echo "si";
    }
    else{
        echo "no";
    }
