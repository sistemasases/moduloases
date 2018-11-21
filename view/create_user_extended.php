<?php
/**
 * User: luis
 * Date: 28/09/18
 * Time: 08:49 AM
 */



require_once(__DIR__ . '/../../../config.php');
require_once('../managers/validate_profile_action.php');

require_once(__DIR__.'/../classes/UserExtended.php');

$output = $PAGE->get_renderer('block_ases');

echo $output->header();
echo UserExtended::check_if_exists_by_ases_user_id(100);
echo $output->footer();


?>