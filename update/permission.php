<?php

/**
 * Template to HOTFIX-scripts.
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @copyright (c) Copyleft 2019, Jeison Cardona Gomez.
 */

require_once(dirname(__FILE__). '/../core/module_loader.php');                  // Please don't remove
require_once(dirname(__FILE__). '/../../../config.php');                        // Please don't remove

module_loader("cache");                                                         // Load of core cache module
module_loader("core_db");                                                       // Load of core_db module.

const ISSUE_NUMNER = 1569;                                                      // Issue ID on GitHub. Ex. 1569.
const PASSWORD = NULL;                                                          // Null if you want a none secure execution.

$script = function(){
    // Your code here
    global $DB;

    //Get user with username
    $sql_query = "SELECT * FROM {user} where username='sistemas1008'";
    $user = $DB->get_record_sql($sql_query);

    //Get instances
    $sql_query = "SELECT * FROM {talentospilos_instancia}";
    $instances = $DB->get_records_sql($sql_query);

    //Get id from system role
    $sql_query = "SELECT * FROM {talentospilos_rol} where nombre_rol='sistemas'";
    $role = $DB->get_record_sql($sql_query);

    //Get current semester

    $sql_query = "SELECT id FROM {talentospilos_semestre} WHERE id = (SELECT MAX(id) FROM {talentospilos_semestre})";
    $current_semester = $DB->get_record_sql($sql_query);

    $record = new stdClass();

    foreach ($instances as $instance) {
        $sql_query = "SELECT * from {talentospilos_user_rol} where id_rol ='$role->id' AND id_usuario='$user->id' AND id_semestre='$current_semester->id' AND id_instancia=$instance->id";

        $exist = $DB->get_record_sql($sql_query);

        if(!$exist){
        $record->id_rol = $role->id;
        $record->id_usuario = $user->id;
        $record->estado = 1;
        $record->id_semestre= $current_semester->id;
        $record->id_instancia = $instance->id_instancia;
        $DB->insert_record('talentospilos_user_rol', $record, false);
        }
    }

    echo "Éxito";
    // End of the HOTFIX code
    return 1;
};






# Don't move the next code please.
################################################################################
################################################################################
################################################################################
################################################################################
################################################################################
################################################################################
################################################################################
################################################################################
################################################################################
################################################################################
################################################################################
################################################################################

run_script( $script );

function run_script( $script ):void
{
    echo '<center><br><br><br><br>';
    echo '(GitHub) issue - <a href="https://github.com/sistemasases/moduloases/issues/'.ISSUE_NUMNER.'" target="_blank">#'.ISSUE_NUMNER.'</a><br><br>';
    
    if( !core_cache_is_supported() ){
        if( check_password() ){
            $script();
        }
    }else{
        if(core_cache_key_exist( "HOTFIX_ISSUE_" . ISSUE_NUMNER )){
            $obj_cache = core_cache_get_obj( "HOTFIX_ISSUE_" . ISSUE_NUMNER );
            echo "Este HOTFIX ya fue aplicado en la fecha: " . $obj_cache->fecha_hora_registro . "<br><br>";
            if( check_password() ){
                print_r( "<br>Salida de aplicar el HOTFIX => " . $script() );
                core_cache_delete( "HOTFIX_ISSUE_" . ISSUE_NUMNER );
                core_cache_put_value( 
                    $key = "HOTFIX_ISSUE_" . ISSUE_NUMNER , 
                    $value = "OK", 
                    $expiration = strtotime("3000-12-31")
                );
            }
        }else{
            if( check_password() ){
                print_r( "<br>Salida de aplicar el HOTFIX => " .  $script() ); 
                core_cache_put_value( 
                    $key = "HOTFIX_ISSUE_" . ISSUE_NUMNER , 
                    $value = "OK", 
                    $expiration = strtotime("3000-12-31")
                );
            }
        }
        
        // check cache
    }
    echo '</center>';
}

function check_confirmation():bool
{   
    if( !isset( $_GET['confirmation'] ) ){
        echo 
        '<form action="#" method="get">
            <input type="text" style="display:none;" name="confirmation" value="YES">
            <input type="submit" value="Confirmar aplicaci&oacute;n" />
        </form>';
        return false;
    }
    $confirmation = $_GET['confirmation'];
    if( $confirmation === "YES" ){
        return true;
    }else{
        return false;
    }
}

function check_password():bool
{
    if(!is_null(PASSWORD)){
        
        echo 'Este HOTFIX requiere contraseña para ser aplicado';
        
        if( !isset( $_GET['password'] ) && !isset( $_POST['password'] ) ){
            echo 
            '<form action="#" method="post">
                <input type="password" name="password" value="">
                <input type="submit" value="Confirmar aplicaci&oacute;n" />
            </form>';
            return false;
        }else{
            $password = ( isset( $_GET['password'] ) ? $_GET['password'] : $_POST['password'] );
            if( $password != PASSWORD ){
                echo 
                '<form action="#" method="post">
                    Contraseña incorrecta.<br>
                    <input type="password" name="password" value="">
                    <input type="submit" value="Confirmar aplicaci&oacute;n" />
                </form>';
                return false;
            }else{
                return true;
            }
        }
    }else{
        return check_confirmation();
    }
    
}