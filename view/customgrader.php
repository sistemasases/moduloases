<?php
require_once '../../../config.php';
require_once (__DIR__ . '/../managers/customgrader/customgrader_lib.php');


$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);
$id_course = optional_param('id_course',0,PARAM_INT);

require_login($courseid, false);

$PAGE->requires->jquery();


$contextcourse = context_course::instance($courseid);
$contextblock =  context_block::instance($blockid);

$url = new moodle_url("/blocks/ases/view/customgrader.php",array('courseid' => $courseid, 'instanceid' => $blockid,'id_course' => $id_course));

$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
/// Make sure they can even access this course
if (!$course = $DB->get_record('course', array('id' => $id_course))) {
    print_error('invalidcourseid');
}

$context = context_course::instance($course->id);
$PAGE->requires->jquery_plugin('ui');
$PAGE->requires->css('/blocks/ases/style/vue-js-modal.css');
// Moove have his own font awesome, if is required hear all icons crash
if(!$PAGE->theme->name === 'moove') {
    $PAGE->requires->css('/blocks/ases/style/font-awesome.css');
}

$PAGE->requires->css('/blocks/ases/style/vue-flex.css', true);
$PAGE->requires->css('/blocks/ases/style/vue-toasted.css', true);
$PAGE->requires->css('/blocks/ases/style/styles_customgrader.css', true);
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('block_ases/grader.vue', null);

$PAGE->requires->js_call_amd('block_ases/grader', 'init');
echo $OUTPUT->footer();
