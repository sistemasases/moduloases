<?php

require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

$highest_id = 8857;

$id_program_3492 = 193;

$id_moodle_with_two_relations = 121315;
$id_moodle_without_relations = 159210;

$id_ases_in_use = 10743;
$id_ases_deprecated = 10076;

$user_to_deprecate = new stdClass();
$user_to_deprecate->id = $id_ases_deprecated;
$user_to_deprecate->num_doc_ini = 939393;
$user_to_deprecate->num_doc = 939393;
$DB->update_record("talentospilos_usuario", $user_to_deprecate);

$DB->delete_records('talentospilos_user_extended', ['id_ases_user' => $id_ases_deprecated, 'id_moodle_user' => $id_moodle_with_two_relations]);

$relation_to_create = new stdClass();
$relation_to_create->id = $highest_id+1;
$relation_to_create->id_ases_user = $id_ases_in_use;
$relation_to_create->id_moodle_user = $id_moodle_without_relations;
$relation_to_create->id_academic_program = $id_program_3492;
$relation_to_create->tracking_status = 0;
$relation_to_create->program_status = 4;
$DB->insert_record("talentospilos_user_extended", $relation_to_create, true);