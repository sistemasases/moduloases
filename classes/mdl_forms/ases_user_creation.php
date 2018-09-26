<?php 
defined('MOODLE_INTERNAL') || die;

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once($CFG->libdir.'/formslib.php');
require_once(__DIR__.'/../Gender.php');
require_once(__DIR__.'/../TipoDocumento.php');
require_once(__DIR__.'/../Municipio.php');
require_once(__DIR__.'/../Discapacidad.php');
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
        $tipo_doc_options = TipoDocumento::getOptions();
        $cidades_options = Municipio::getOptions();
        $discapacidades_options = Discapacidad::getOptions();
        $ciudad_por_defecto = Municipio::get_municipio_por_defecto();
        $mform = $this->_form; // Don't forget the underscore! 
        $mform->addElement('text', 'num_doc', 'Número de documento' , null); 
        $mform->addRule('num_doc', 'El número de documento debe contener solo numeros', 'numeric');  
        $mform->addElement('select', 'tipo_doc', 'Tipo de documento' , $tipo_doc_options); 
        $mform->addElement('select', 'tipo_doc_ini', 'Tipo de documento inicial' , $tipo_doc_options); 
        $mform->addElement('select', 'id_ciudad_res', 'Ciudad de residencia' , $cidades_options); 
        $mform->addElement('date', 'fecha_nac', 'Fecha de nacimiento' , null); 
        $mform->addElement('select', 'id_ciudad_nac', 'Ciudad nacimiento' , $cidades_options); 
        
        $mform->setDefault('id_ciudad_res', $ciudad_por_defecto->id);
        $mform->setDefault('id_ciudad_nac', $ciudad_por_defecto->id);
        
        $mform->addElement('select', 'sexo', 'Sexo' , $gender_options); 
        $mform->addElement('text', 'estado', 'Estado' , null); 
        $mform->addElement('select', 'id_discapacidad', 'Discapacidad' , $discapacidades_options); 
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