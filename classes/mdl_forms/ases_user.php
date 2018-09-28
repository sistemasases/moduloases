<?php 
defined('MOODLE_INTERNAL') || die;

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once($CFG->libdir.'/formslib.php');
require_once(__DIR__.'/../Gender.php');
require_once(__DIR__.'/../TipoDocumento.php');
require_once(__DIR__.'/../AsesUser.php');
require_once(__DIR__.'/../Municipio.php');
require_once(__DIR__.'/../Discapacidad.php');
require_once(__DIR__.'/../Estamento.php');
class ases_user_ extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG;

        $gender_options = Gender::get_options();
        $tipo_doc_options = TipoDocumento::get_options();
        $cidades_options = Municipio::get_options();
        $estamento_options = Estamento::get_options();
        //$discapacidades_options = Discapacidad::get_options();
        $ciudad_por_defecto = Municipio::get_municipio_por_defecto();
        $mform = $this->_form; // Don't forget the underscore! 
        $date_options_fecha_nac = array(
            'startyear' => 1920, 
            'stopyear'  => date("Y")
        );
        $mform->addElement('text', 'num_doc', 'Número de documento' , null); 
        $mform->addRule('num_doc', null, 'required');
        $mform->addRule('num_doc', 'El número de documento debe contener solo numeros', 'numeric');  

        $mform->addElement('select', 'tipo_doc', 'Tipo de documento' , $tipo_doc_options); 
        $mform->addRule('tipo_doc', null, 'required');

        $mform->addElement('select', 'id_ciudad_res', 'Ciudad de residencia' , $cidades_options); 
        $mform->setDefault('id_ciudad_res', $ciudad_por_defecto->id);
        $mform->addRule('id_ciudad_res', null, 'required');

        $mform->addElement('date_selector', 'fecha_nac', 'Fecha de nacimiento', $date_options_fecha_nac);
        $mform->addRule('fecha_nac', null, 'required');

        $mform->addElement('select', 'id_ciudad_nac', 'Ciudad nacimiento' , $cidades_options); 
        $mform->setDefault('id_ciudad_nac', $ciudad_por_defecto->id);
        $mform->addRule('id_ciudad_nac', null, 'required');

        $mform->addElement('select', 'id_ciudad_ini', 'Ciudad inicial' , $cidades_options);
        $mform->setDefault('id_ciudad_ini', $ciudad_por_defecto->id);
        $mform->addRule('id_ciudad_ini', null, 'required');
       
        $mform->addElement('select', 'sexo', 'Sexo' , $gender_options);
        $mform->addRule('sexo', null, 'required');

        $mform->addElement('text', 'barrio_ini', 'Barrio de procedencia');
        $mform->addElement('text', 'dir_ini', 'Dirección procedencia');


        $mform->addElement('text', 'barrio_res', 'Barrio residencia');
        $mform->addElement('text', 'direccion_res', 'Dirección residencia');

        $mform->addElement('text', 'tel_acudiente', 'Teléfono acudiente');
        $mform->addRule('tel_acudiente', 'El número de telefónico de el acudiente debe contener solo dígitos', 'numeric'); 

        $mform->addElement('text', 'tel_ini', 'Teléfono procedencia');
        $mform->addRule('tel_ini', 'El número de telefónico de procedencia debe contener solo dígitos', 'numeric'); 

        $mform->addElement('text', 'tel_res', 'Teléfono residencia');
        $mform->addRule('tel_res', 'El número de telefónico de residencia debe contener solo dígitos', 'numeric'); 

        $mform->addElement('text', 'colegio', 'Colegio');
        $mform->addElement('select', 'estamento', 'Estamento' , $estamento_options); 

        $mform->addElement('text', 'celular', 'Celular');
        $mform->addRule('celular', 'El número de celular debe contener solo dígitos', 'numeric'); 

        $mform->addElement('text', 'emailpilos', 'Correo electronico');
        $mform->addRule('emailpilos', null, 'email'); 

        $mform->addElement('text', 'acudiente', 'Acudiente');
        $mform->addElement('text', 'observacion', 'Observación');


        /*$mform->addElement('select', 'id_discapacidad', 'Discapacidad' , $discapacidades_options); 
        $mform->addRule('id_discapacidad', null, 'required');*/

        $buttonarray=array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
    }
    function get_ases_user() {
        $data = $this->get_data(); 
        // El formato de fecha de moodle forms (timestamp para feha_nac) a una cadena de string valida 
        $date = gmdate("Y-m-d",$data->fecha_nac);
        $data->fecha_nac = $date;
        $data->num_doc_ini = $data->num_doc;
        $data->tipo_doc_ini = $data->tipo_doc;
        $ases_user = new AsesUser();
        $ases_user->make_from($data);
        return $ases_user;
    }
    //Custom validation should be added here
    function validation($data, $files) {
        $ases_user = new AsesUser();
        $ases_user->make_from($data);
        $errors =  array();
        if ($ases_user->num_doc_already_exist()) {
            $errors['num_doc'] = "El usuario con el documento $ases_user->num_doc ya existe";
        }
        return $errors;
    }
}
?>