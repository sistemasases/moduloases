<?php

/**
 * Template to HOTFIX-scripts.
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @copyright (c) Copyleft 2019, Jeison Cardona Gomez.
 */

require_once(dirname(__FILE__) . '/../core/module_loader.php');                  // Please don't remove
require_once(dirname(__FILE__) . '/../../../config.php');                        // Please don't remove

module_loader("cache");                                                         // Load of core cache module
module_loader("core_db");                                                       // Load of core_db module.

const ISSUE_NUMNER = 1864;                                                      // Example ID.
const PASSWORD = 'hotfix_jahir';                                                          // Null if you want a none secure execution.

$script = function () {

    global $DB;

    $id_program_3492 = 193;
    $id_moodle_with_two_relations = 121315;
    $id_moodle_without_relations = 159210;
    $id_ases_in_use = 10743;
    $id_ases_deprecated = 10076;

    $sql_query = "SELECT MAX (id) FROM {talentospilos_usuario}";
    $highest_id = $DB->get_record_sql($sql_query);

    //Update of user

    $query = "
        UPDATE {talentospilos_usuario}
        SET num_doc_ini = 939393,  num_doc = 939393
        WHERE id = $id_ases_deprecated";

    $DB->execute($query);

    //Insert of new relation

    $relation_to_create = new stdClass();
    $relation_to_create->id = $highest_id+1;
    $relation_to_create->id_ases_user = $id_ases_in_use;
    $relation_to_create->id_moodle_user = $id_moodle_without_relations;
    $relation_to_create->id_academic_program = $id_program_3492;
    $relation_to_create->tracking_status = 0;
    $relation_to_create->program_status = 4;

    $DB->insert_record("talentospilos_user_extended", $relation_to_create, true);

    //Delete record

    $DB->delete_records('talentospilos_user_extended', ['id_ases_user' => $id_ases_deprecated, 'id_moodle_user' => $id_moodle_with_two_relations]);

    echo "HOTFIX APLICADO";
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