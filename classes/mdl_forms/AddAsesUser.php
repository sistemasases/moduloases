<?php 
defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir.'/formslib.php');
require_once(__DIR__.'/../Gender.php');
class ases_user_creation extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG;
 /**
      public $tipo_doc_ini = -1;
    public $tipo_doc;
    public $num_doc;
    public $id_ciudad_ini;
    public $id_ciudad_res;
    public $fecha_nac;
    public $id_ciudad_nac;
    public $sexo;
    public $estado;
    public $id_discapacidad;
    public $ayuda_disc;
    public $estado_ases;* 

  */
        $gender_options = Gender::get_options();
        $mform = $this->_form; // Don't forget the underscore! 
        $mform->addElement('text', 'num_doc', 'Número de documento' , null); 
        $mform->addElement('text', 'tipo_doc', 'Tipo de documento' , null); 
        $mform->addElement('text', 'tipo_doc_ini', 'Tipo de documento inicial' , null); 
        $mform->addElement('text', 'id_ciudad_res', 'Ciudad de residencia' , null); 
        $mform->addElement('text', 'fecha_nac', 'Fecha de nacimiento' , null); 
        $mform->addElement('text', 'id_ciudad_nac', 'Ciudad nacimiento' , null); 
        $mform->addElement('select', 'sexo', 'Sexo' , $gender_options); 
        $mform->addElement('text', 'estado', 'Estado' , null); 
        $mform->addElement('text', 'id_discapacidad', 'Discapacidad' , null); 
        $mform->addElement('text', 'ayuda_disc', 'Ayuda a discapacidad' , null); 
        $mform->addElement('text', 'estado_ases', 'Estado ases' , null); 
        $mform->addRule('imagefile', null, 'required');
        //normally you use add_action_buttons instead of this code
        $buttonarray=array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
?>