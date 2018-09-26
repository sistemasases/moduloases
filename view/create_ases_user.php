<?php

require_once(__DIR__ . '/../../../config.php');
require_once('../managers/validate_profile_action.php');

require_once(__DIR__.'/../classes/mdl_forms/ases_user_creation.php');

$output = $PAGE->get_renderer('block_ases');

$add_ases_user_form = new ases_user_creation();

echo $output->header();

if ($data = $add_ases_user_form->get_data()) {
} else {
}
$add_ases_user_form->display();
echo $output->footer();


?>