<?php 
require_once($CFG->libdir.'/formslib.php');
require_once(__DIR__.'/../../managers/lib/cohort_lib.php');
/**
 * Form for search moodle user based in its user name in moodle
 * @author Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @example username = '1327951-3743'
 */
class add_user_to_cohort extends moodleform {
    const NULL_COHORT_ID = -1;
    const NULL_COHORT_NAME = '-------';
    public function definition()
    {
        global $CFG;
        $ases_cohorts_options = cohort_lib::get_options();

        /* add a null cohort and mark it as default */
        $ases_cohorts_options[add_user_to_cohort::NULL_COHORT_ID] = add_user_to_cohort::NULL_COHORT_NAME;

        $mform = $this->_form; // Don't forget the underscore! 
        $mform->addElement('text', 'username', 'Nombre de usuario moodle' , null); // Add elements to your form
        $mform->addRule('username', null, 'required');
        $mform->addElement('select', 'cohort', 'Cohorte ASES' , $ases_cohorts_options); // Add elements to your form
        $mform->setDefault('cohort', add_user_to_cohort::NULL_COHORT_ID);
        $mform->addRule('cohort', null, 'required');
        //normally you use add_action_buttons instead of this code
        $buttonarray=array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', 'Adicionar  usuario a la cohorte');
        $buttonarray[] = $mform->createElement('submit', 'cancel', get_string('cancel'));
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
    }
    function  validation($data, $files) {
        $parent_errors = parent::validation($data, $files);
        global $DB;
        $errors = array();
        $user_exists = $DB->record_exists('user', array('username' => $data['username']));
        if (!$user_exists) {
            $errors['username'] = "El usurio moodle no existe";
        }
        if(cohort_lib::is_registred_in_cohort($data['username'],$data['cohort'])){
            $errors['cohort'] = "El usuario ya esta inscrito en la cohorte dada";
        }
        if ($data['cohort'] == add_user_to_cohort::NULL_COHORT_ID) {
            $errors['cohort'] = "Debe seleccionar una cohorte válida";
        }
        return $errors;
    }




}



?>