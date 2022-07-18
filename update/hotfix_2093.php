<?php

/**
 * Al estudiante JOHANN FELIPE AGREDA CUERO 2043293-3651 no le aparecen los seguimientos de pares registrados por su monitor asignado
 * 
 * La causa es que tiene doble usuario de ases. En el siguiente script se moveran todos los seguimientos a usuario de ases con id 13492 al usuario con id 13554.
 * El usuario 13492 se desactivará asociandolo con un usuario moodle 999999 y quitándole todas las asignaciones.
 * @author Joan Sebastian Betancourt <joan.betancourt@correounivalle.edu.co>
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @copyright (c) Copyleft 2019, Jeison Cardona Gomez.
 */

require_once(dirname(__FILE__) . '/../core/module_loader.php');                  // Please don't remove
require_once(dirname(__FILE__) . '/../../../config.php');                        // Please don't remove

module_loader("cache");                                                         // Load of core cache module
module_loader("core_db");                                                       // Load of core_db module.

const ISSUE_NUMNER = 2093;                                                      
const PASSWORD = 'hotfix_jahir';                                                 // Null if you want a none secure execution.

$script = function () {

    global $DB;

    $ID_ASES = 13554;
    $ID_A_REMOVER = 13492;
    $ID_MOODLE_FANTASMA = 99999;

    // Mover segumientos de pares guardados al id de estudiante 13492 y asociarlos a 13554
    $DB->execute("UPDATE {talentospilos_df_respuestas}
                    SET respuesta = '$ID_ASES' 
                    FROM {talentospilos_df_respuestas} r
                        INNER JOIN {talentospilos_df_preguntas} p ON r.id_pregunta = p.id 
                        INNER JOIN {talentospilos_df_form_preg} f_p ON p.id = f_p.id_pregunta
                        INNER JOIN {talentospilos_df_formularios} f ON f_p.id_formulario = f.id
                    WHERE p.enunciado = 'id_estudiante'
                        AND f.alias = 'seguimiento_pares'
                        AND r.respuesta != ''
                        AND r.respuesta = '$ID_A_REMOVER'");

    // asociar a 13492 con un usuario de moodle fantastma, con id 999999
    $DB->execute("UPDATE {talentospilos_user_extended}
                    SET id_moodle_user = '$ID_MOODLE_FANTASMA' 
                    WHERE id_ases_user = '$ID_A_REMOVER'");

    echo "HOTFIX APLICADO";

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

run_script($script);

function run_script($script): void
{
    echo '<center><br><br><br><br>';
    echo '(GitHub) issue - <a href="https://github.com/sistemasases/moduloases/issues/' . ISSUE_NUMNER . '" target="_blank">#' . ISSUE_NUMNER . '</a><br><br>';

    if (!core_cache_is_supported()) {
        if (check_password()) {
            $script();
        }
    } else {
        if (core_cache_key_exist("HOTFIX_ISSUE_" . ISSUE_NUMNER)) {
            $obj_cache = core_cache_get_obj("HOTFIX_ISSUE_" . ISSUE_NUMNER);
            echo "Este HOTFIX ya fue aplicado en la fecha: " . $obj_cache->fecha_hora_registro . "<br><br>";
            if (check_password()) {
                print_r("<br>Salida de aplicar el HOTFIX => " . $script());
                core_cache_delete("HOTFIX_ISSUE_" . ISSUE_NUMNER);
                core_cache_put_value(
                    $key = "HOTFIX_ISSUE_" . ISSUE_NUMNER,
                    $value = "OK",
                    $expiration = strtotime("3000-12-31")
                );
            }
        } else {
            if (check_password()) {
                print_r("<br>Salida de aplicar el HOTFIX => " . $script());
                core_cache_put_value(
                    $key = "HOTFIX_ISSUE_" . ISSUE_NUMNER,
                    $value = "OK",
                    $expiration = strtotime("3000-12-31")
                );
            }
        }

        // check cache
    }
    echo '</center>';
}

function check_confirmation(): bool
{
    if (!isset($_GET['confirmation'])) {
        echo
        '<form action="#" method="get">
            <input type="text" style="display:none;" name="confirmation" value="YES">
            <input type="submit" value="Confirmar aplicaci&oacute;n" />
        </form>';
        return false;
    }
    $confirmation = $_GET['confirmation'];
    if ($confirmation === "YES") {
        return true;
    } else {
        return false;
    }
}

function check_password(): bool
{
    if (!is_null(PASSWORD)) {

        echo 'Este HOTFIX requiere contraseña para ser aplicado';

        if (!isset($_GET['password']) && !isset($_POST['password'])) {
            echo
            '<form action="#" method="post">
                <input type="password" name="password" value="">
                <input type="submit" value="Confirmar aplicaci&oacute;n" />
            </form>';
            return false;
        } else {
            $password = (isset($_GET['password']) ? $_GET['password'] : $_POST['password']);
            if ($password != PASSWORD) {
                echo
                '<form action="#" method="post">
                    Contraseña incorrecta.<br>
                    <input type="password" name="password" value="">
                    <input type="submit" value="Confirmar aplicaci&oacute;n" />
                </form>';
                return false;
            } else {
                return true;
            }
        }
    } else {
        return check_confirmation();
    }

}