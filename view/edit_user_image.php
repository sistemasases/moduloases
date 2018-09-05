<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * General Reports
 *
 * @author     Iader E. García Gómez
 * @package    block_ases
 * @copyright  2016 Iader E. García <iadergg@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

require_once("$CFG->libdir/formslib.php");

require_once('../managers/instance_management/instance_lib.php');
require_once ('../managers/validate_profile_action.php');
require_once ('../managers/menu_options.php');
require_once('../classes/output/edit_user_image_page.php');


$courseid  = required_param('courseid', PARAM_INT);
require_login($courseid, false);
$blockid   = required_param('instanceid', PARAM_INT);
$actions = authenticate_user_view($USER->id, $blockid);
if (!isset($actions->update_user_profile_image_)) {
 redirect(new moodle_url('/'));
}
$show_html_elements_update_user_profile_image = true;
global $PAGE;
$filemanageroptions = array('maxbytes'       => $CFG->maxbytes,
                             'subdirs'        => 0,
                             'maxfiles'       => 1,
                             'accepted_types' => 'web_image');
class user_image_edit_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG;
 
        $mform = $this->_form; // Don't forget the underscore! 

        $mform->addElement('filepicker', 'imagefile', 'Nueva imágen de perfil'); // Add elements to your form
        $mform->addRule('imagefile', null, 'required');

        //normally you use add_action_buttons instead of this code
        $buttonarray=array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $buttonarray[] = $mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}



//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");
 
include("../classes/output/upload_files_page.php");
include("../classes/output/renderer.php");
require_once('../managers/query.php');

// Set up the page.
$title     = "Edición de imágene de usuario";
$pagetitle = $title;


$mdl_user_id =  required_param('userid', PARAM_INT);
echo $mdl_user_id;
$url_return =  required_param('url_return', PARAM_TEXT);
function save_image($image_form_data, $mdl_user_id) {
    $usernew = new class{};
    $usernew = $image_form_data;
    $usernew->id = $mdl_user_id;
    update_picture($usernew, $filemanageroptions);
}



$contextcourse = context_course::instance($courseid);
$contextblock  = context_block::instance($blockid);
$url           = new moodle_url("/blocks/ases/view/edit_user_image.php", array(
    'courseid' => $courseid,
    'instanceid' => $blockid,
    'userid' => $mdl_user_id,
    'url_return' => $url_return
));

// Instance is consulted for its registration
if (!consult_instance($blockid)) {
    header("Location: instance_configuration.php?courseid=$courseid&instanceid=$blockid");
}

// Menu items are created

// Creates a class with information that'll be send to template
$data = 'data';
$data = new stdClass;


//Nav configuration
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$node       = $coursenode->add('Gestion de archivos', $url);
$node->make_active();

//Page set up

$PAGE->set_url($url);
$PAGE->set_title($title);

$PAGE->set_heading($title);

$PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);
$PAGE->requires->js_call_amd('block_ases/uploaddata_main', 'init');

$output = $PAGE->get_renderer('block_ases');

echo $output->header();  
/** Creando el formulario  */



function update_picture(stdClass $usernew, $filemanageroptions = array()) {
    global $CFG, $DB, $contextblock;
    require_once("$CFG->libdir/gdlib.php");


    $newpicture = $user->picture;
    // Get file_storage to process files.
    
    $fs = get_file_storage();
    if (!empty($usernew->deletepicture)) {
        // The user has chosen to delete the selected users picture.
        
        $fs->delete_area_files($context, 'ases', 'profile_image'); // Drop all images in area.
        $newpicture = 0;

    } else {
        // Save newly uploaded file, this will avoid context mismatch for newly created users.
        file_save_draft_area_files($usernew->imagefile, $context, 'ases', 'profile_image', 0, $filemanageroptions);
        
        $context = context_user::instance($usernew->id, MUST_EXIST);
        if (($iconfiles = $fs->get_area_files($context, 'ases', 'profile_image')) && count($iconfiles) == 2) {
            // Get file which was uploaded in draft area.
            foreach ($iconfiles as $file) {
                if (!$file->is_directory()) {
                    break;
                }
            }
            // Copy file to temporary location and the send it for processing icon.
            if ($iconfile = $file->copy_content_to_temp()) {
                // There is a new image that has been uploaded.
                // Process the new image and set the user to make use of it.
                // NOTE: Uploaded images always take over Gravatar.
                $newpicture = (int)process_new_icon($context, 'ases', 'profile_image', 0, $iconfile);
                // Delete temporary file.
                @unlink($iconfile);
                // Remove uploaded file.
                $fs->delete_area_files($context, 'ases', 'profile_image');
            } else {
                // Something went wrong while creating temp file.
                // Remove uploaded file.
                $fs->delete_area_files($context, 'ases', 'profile_image');
                return false;
            }
        }
    }


}

$user_image_edit_form = new user_image_edit_form($url);
$user_image_edit_form->set_data($_POST);
//Form processing and displaying is done here
if ($user_image_edit_form->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
    redirect($url_return);
} else if ($image_data = $user_image_edit_form->get_data()) {
    save_image($image_data, $mdl_user_id);
    print_r ($contextblock->id);
    redirect($url_return);
  //In this case you process validated data. $mform->get_data() returns data posted in form.
} else {
  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
  // or on the first display of the form.
  //Set default data (if any)
  $user_image_edit_form->set_data($toform);
  //displays the form
  $user_image_edit_form->display(null);
}
$PAGE->requires->js_call_amd('block_ases/editar_imagen_perfil', 'init');
echo $output->footer();
