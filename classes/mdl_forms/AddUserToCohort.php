<?php 
require_once($CFG->libdir.'/formslib.php');
require_once(__DIR__.'/../../managers/lib/cohort_lib.php');
/**
 * Form for search moodle user based in its user name in moodle
 * @author Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @example username = '1327951-3743'
 */
class add_user_to_cohort extends moodleform {
    public function definition() {
        global $CFG;
        $ases_cohorts_options = $this->get_cohort_options();
        $mform = $this->_form; // Don't forget the underscore! 
        $mform->addElement('text', 'username', 'Nombre de usuario moodle' , null); // Add elements to your form
        $mform->addRule('username', null, 'required');
        $mform->addElement('select', 'cohort', 'Cohorte ASES' , $ases_cohorts_options); // Add elements to your form
        $mform->addRule('cohort', null, 'required');
        //normally you use add_action_buttons instead of this code
        $buttonarray=array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', 'Adicionar  usuario a la cohorte');
        $buttonarray[] = $mform->createElement('submit', 'cancel', get_string('cancel'));
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
    }
    function  validation($data, $files) {
        $parent_errors = parent::validation($data, $files);
        print_r($parent_errors);
        global $DB;
        $errors = array();
        $user_exists = $DB->record_exists('user', array('username' => $data['username']));
        if (!$user_exists) {
            $errors['username'] = "El usurio moodle no existe";
        }
        if(cohort_lib::is_registred_in_cohort($data['username'],$data['cohort'])){
            $errors['cohort'] = "El usuario ya esta inscrito en la cohorte dada";
        }
        return $errors;
    }

    /**
     * Returns array for html select than contains the id of cohorts as keys and name as values
     * @see https://docs.moodle.org/dev/lib/formslib.php_Form_Definition#select
     * @return array 
     * @example Return type is array with the form ('cohortId'->cohortName...)
     */
    private function get_cohort_options() {
        $ases_cohorts = cohort_lib::get_cohorts();
        $ases_cohorts_options = [];
        foreach($ases_cohorts as $ases_cohort) {
            $ases_cohorts_options[$ases_cohort->id] = $ases_cohort->name;
        }
        return $ases_cohorts_options;
    }


}



?>