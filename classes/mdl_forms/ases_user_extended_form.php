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
 * Form for create user extended
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2016 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/formslib.php');require_once(__DIR__.'/../AsesUser.php');
require_once(__DIR__.'/../TrackingStatus.php');
require_once(__DIR__.'/../EstadoPrograma.php');
require_once(__DIR__.'/../Programa.php');

class AsesUserExtendedFormData {
    public $moodle_user_name;
    public $id_ases_user;
}

/**
 * Class ases_user_extended_form
 * @property AsesUserExtendedFormData $_customdata
 */
class ases_user_extended_form extends moodleform {

    public function definition()
    {

        $mform = $this->_form;
        $tracking_status_options = TrackingStatus::get_options();
        $ases_user_options = AsesUser::get_options();

        $program_status_options = EstadoPrograma::get_options();
        $program_options = Programa::get_options();

        $mform->addElement('header', 'myheader', 'Usuario extended Ases');

        $mform->addElement('text', 'moodle_user_name', 'Nombre de usuario moodle');
        $mform->addElement('searchableselector', 'id_ases_user', 'Usuario ASES', $ases_user_options);
        $mform->addElement('checkbox', 'inactive_previus_track_stat', 'Desactivar seguimientos previos');

        $mform->addElement('select', 'tracking_status', 'Estado de seguimiento', $tracking_status_options);
        $mform->addElement('select', 'program_status', 'Estado de programa', $program_status_options);
        $mform->addElement('select', 'id_academic_program', 'Programa academico', $program_options);

        $mform->addRule('moodle_user_name', null, 'required');
        $mform->addRule('id_ases_user', null, 'required');
        $mform->addRule('tracking_status', null, 'required');
        $mform->addRule('program_status', null, 'required');
        $mform->addRule('id_academic_program', null, 'required');

        $mform->addHelpButton('id_academic_program',
            'academic_program_with_headquarters_and_shift', 'block_ases' );
        $buttonarray=array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);

        if($this->_customdata) {
            $this->set_data($this->_customdata);
        }
    }

    public function after_definition()
    {

        /* If an id_ases_user is given, set id aser user is not posible by normal methods*/
        if(isset($this->_customdata->id_ases_user)) {
            /* @var MoodleQuickForm_searchableselector $id_ases_user_element */
            $id_ases_user_element = $this->_form->getElement('id_ases_user');
            $id_ases_user_element->updateAttributes(array('disabled'));

        }
        if ($this->_customdata) {
            /* If an moodle user name is given, set username is not possible for normal methods*/
            if(isset($this->_customdata->moodle_user_name)) {
                /* @var MoodleQuickForm_text $moodle_user_name_element */
                $moodle_user_name_element = $this->_form->getElement('moodle_user_name');
                $moodle_user_name_element->updateAttributes(array('disabled'));
            }

        }
    }

    /**
     * Return the ases user extended from the validated data of the form
     * @return AsesUserExtended The ases user extended from the validated data of the form
     * @throws dml_exception This will never happen
     * @throws ErrorException If the form is not validated throws an ErrorException
     */
    public function get_ases_user_extended(): AsesUserExtended {
        global $DB;
        $validated_form_data = $this->get_data();
        if(!$this->is_validated()) {
            throw new ErrorException("Para obtener el usuario extendido de el formulario primero debe haber llamado el metodo validate de el mismo");
        }
        $ases_user_extended = new AsesUserExtended();
        $ases_user_extended->make_from($validated_form_data);

        /* The shared properties between of form data and user extended are all exept one, id_moodle_user */
        $ases_user_extended->id_moodle_user = $DB->get_record('user' , array('username'=> $validated_form_data->moodle_user_name))->id;
        return $ases_user_extended;
    }
    public function validation($data, $files): array {
        global $DB;
        if(count($this->_form->_errors)>0) {
            return $this->_form->_errors;
        }
        $ases_user_extended = new AsesUserExtended();
        $ases_user_extended->make_from($data);

        if($data['inactive_previus_track_stat']) {
            AsesUserExtended::disable_all_tracking_status($ases_user_extended->id_ases_user);
        }
        $errors = array();
        /* Check if the user have other tracking status active, only validate this if moodle user name is not empty */
        if($data['moodle_user_name'] &&
            $data['moodle_user_name'] != '' &&
            AsesUserExtended::have_active_tracking_status($ases_user_extended->id_ases_user)) {
            $errors[AsesUserExtended::ID_ASES_USER] = "El usuario ya tiene un seguimiento activo, puede desactivar todos los seguimientos activos y almacenar el actual marcando 'Desactivar seguimientos previos' ";
        }
        /* Check if the moodle user given exists */
        $mdl_user_exists = $DB->record_exists('user', array('username' => $data['moodle_user_name']));
        if (!$mdl_user_exists) {
            $errors['moodle_user_name'] = "El usurio moodle no existe";
        }

        return $errors;
    }
}