<?php
/**
 * User: luis
 * Date: 28/09/18
 * Time: 08:49 AM
 */

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

require_once(__DIR__ . '/../../../config.php');
require_once('../managers/validate_profile_action.php');

require_once(__DIR__.'/../classes/UserExtended.php');

$output = $PAGE->get_renderer('block_ases');

echo $output->header();
echo var_dump(UserExtended::check_if_exists_by_ases_user_id(2));
echo $output->footer();


?>