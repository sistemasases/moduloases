<?php
    require_once(dirname(__FILE__). '/../../../config.php');
    require_once('periods_management/periods_lib.php');
    require_once('user_management/user_lib.php');

    global $DB,$PAGE;

    require_once($CFG->libdir.'/adminlib.php');
    $msg = new stdClass();


    if(isset($_POST['nombre']) && isset($_POST['descripcion'])&&isset($_POST['id_funcionalidad'])){
        $record = new stdClass;
        $record->nombre_accion = $_POST['nombre'];
        $record->descripcion = $_POST['descripcion'];
        $record->id_funcionalidad = $_POST['id_funcionalidad'];
        $record->estado = true;

        $sql_query = "SELECT * FROM {talentospilos_accion} WHERE nombre_accion = '".$record->nombre_accion."'";
        $accion = $DB->get_record_sql($sql_query);
        $repetido = false;
        if($accion->nombre_accion){
            $repetido = true;
        }
        
        
        if(!$repetido){
            $id_nueva_accion = $DB->insert_record('talentospilos_accion', $record, true); 
            $nombre_archivo = $record->nombre_accion.".php";
            $file = fopen(dirname(__FILE__)."/".$nombre_archivo, "w") or die("Unable to open file!");
            $content = 
"
<?php
    /**
    * Accion generada por el generador de codigo de moodle para el 
    * programa de talentos pilos de la universidad del valle
    * @author Edgar Mauricio Ceron Florez
    * @author ESCRIBA AQUI SU NOMBRE */
    require_once(dirname(__FILE__). '/../../../config.php');
    require('validate_profile_action.php');
    $"."accion = '".$id_nueva_accion."';
    global $"."USER;
    $"."id_instancia = $"."_POST['instance'];
    $"."moodle_id = $"."USER->id; 
    $"."user_id = get_talentos_id($"."moodle_id);
    $"."perfil = get_perfil_usuario($"."user_id, $"."id_instancia);
    if(validar_permisos($"."perfil, $"."accion)){
        //Escriba aqui su codigo en caso de que el usuario tenga acceso a la función
    }
    else{
        //Escriba aqui su codigo en caso de que el usuario no tenga acceso a la función
    }
";
            fwrite($file, $content);
            fclose($file);
            echo "Accion creada exitosamente";
        }
        else{
            echo "Ya existe una accion con este nombre, escoja otro nombre";
        }
    }else if(isset($_POST['nombre_perfil']) && isset($_POST['descripcion_perfil'])){

        $record = new stdClass;
        $record->nombre_rol = $_POST['nombre_perfil'];
        $record->descripcion = $_POST['descripcion_perfil'];
        
        $sql_query = "SELECT * FROM {talentospilos_rol} WHERE nombre_rol = '".$record->nombre_rol."'";
        $perfil = $DB->get_record_sql($sql_query);
        $repetido = false;
        if($perfil->nombre_rol){
           $repetido = true;
           echo "Ya existe un rol con este nombre, escoja otro nombre";

          }
        if(!$repetido){
          $DB->insert_record('talentospilos_rol', $record, true); 
          echo "Perfil creado exitosamente";
        }

    }else if (isset($_POST['profile']) && isset($_POST['actions'])){
      $actions =  json_decode(stripslashes($_POST['actions']));
      $continuar=true;
      $whereclause = "id_rol = ".$_POST['profile'];
      $DB->delete_records_select('talentospilos_permisos_rol',$whereclause);

      
      foreach($actions as $action){
       $record = new stdClass;
       $record->id_rol=$_POST['profile'];
       $record->id_accion = $action; 
       $DB->insert_record('talentospilos_permisos_rol', $record, true); 
      }

       if($continuar){
        $msg->title="Éxito";
        $msg->text= "Permisos asignados exitosamente";
        $msg->type = "success";
        echo  $msg->text;
      }

    }else if(isset($_POST['profiles_user'])&&isset($_POST['users']) &&isset($_POST['instance'])){
      $profile = $_POST['profiles_user'];
      $user = $_POST['users'];
      $user_moodle = get_userid_by_username($user);
      $continuar=true;
      
      try{
      $record = new stdClass;
      $record->id_perfil = $profile;
      $record->id_usuario = $user_moodle->id;
      $record->estado =true;
      $record->id_semestre =get_current_semester()->max;
      $record->id_jefe = false;
      $record->id_instancia=$_POST['instance'];
      $DB->insert_record('talentospilos_usuario_perfil', $record, true); 
      }catch(Exception $ex){
        echo "Se presentó un inconveniente : ".$es;
        $continuar=false;

      }

      if($continuar){
        echo "Se asignó el perfil al usuario exitosamente";
      }
        
    }else if(isset($_POST['nombre_funcionalidad'])&&isset($_POST['descripcion_funcionalidad'])){
      $function_name = $_POST['nombre_funcionalidad'];
      $function_description = $_POST['descripcion_funcionalidad'];
      $continuar=true;
      
      try{
      $record = new stdClass;
      $record->nombre_func = $function_name;
      $record->descripcion = $function_description;
      $DB->insert_record('talentospilos_funcionalidad', $record, true); 
      }catch(Exception $ex){
        echo "Se presentó un inconveniente : ".$es;
        $continuar=false;

      }

      if($continuar){
        $msg->title="Éxito";
        $msg->text= "Funcionalidad creada exitosamente";
        $msg->type = "success";
        echo  $msg->text;
      }



    }else if(isset($_POST['id_profile'])&&isset($_POST['actions'])&&isset($_POST['function'])){

      $actions =  json_decode(stripslashes($_POST['actions']));
      $continuar=true;
      $whereclause = "id_rol = ".$_POST['id_profile'];
      $DB->delete_records_select('talentospilos_permisos_rol',$whereclause);

      
      foreach($actions as $action){
       $record = new stdClass;
       $record->id_rol=$_POST['id_profile'];
       $record->id_accion = $action; 
       $DB->insert_record('talentospilos_permisos_rol', $record, true); 
      }

       if($continuar){
        $msg->title="Éxito";
        $msg->text= "Permisos asignados exitosamente";
        $msg->type = "success";
        echo  $msg->text;
      }
    }
    
    
    
    