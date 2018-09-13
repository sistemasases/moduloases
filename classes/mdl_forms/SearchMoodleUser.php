<?php 
require_once($CFG->libdir.'/formslib.php');
/**
 * Form for search moodle user based in its user name in moodle
 * @author Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @example username = '1327951-3743'
 */
class search_moodle_user extends moodleform {
    public function definition() {
        global $CFG;
 
        $mform = $this->_form; // Don't forget the underscore! 
        $mform->addElement('text', 'username', 'Nombre de usuario moodle' , null); // Add elements to your form
        $mform->addRule('username', null, 'required');
        //normally you use add_action_buttons instead of this code
        $buttonarray=array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('search'));
        $buttonarray[] = $mform->createElement('submit', 'cancel', get_string('cancel'));
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
    }


}



?>