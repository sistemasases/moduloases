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
 * Form for create programs
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir.'/formslib.php');
require_once(__DIR__.'/../Programa.php');
require_once(__DIR__.'/../Sede.php');
require_once(__DIR__.'/../Facultad.php');
require_once(__DIR__.'/../Jornada.php');
class program_form extends moodleform {

    public function definition()
    {
        $jornada_options = Jornada::get_options();

        $sede_options = Sede::get_options();
        $facultad_options = Facultad::get_options();
        $mform = $this->_form;
        $r = new MoodleQuickForm_Renderer();

        $mform->addElement('header', 'myheader', 'Creación de programas ');

        $mform->addElement('text', 'nombre', 'Nombre de el programa');
        $mform->addRule('nombre', null, 'required');

        $mform->addElement('text', 'codigosnies', 'Codigo SNIES');
        $mform->addRule('codigosnies', null, 'required');

        $mform->addElement('text', 'cod_univalle', 'Codigo Univalle', array('type'=>'number'));
        $mform->addRule('cod_univalle', null, 'required');
        $mform->addRule('cod_univalle', null, 'numeric');

        $mform->addElement('searchableselector', 'jornada', 'Jornada', $jornada_options);
        $mform->addRule('jornada', null, 'required');

        $mform->addElement('searchableselector', 'id_sede', 'Sede', $sede_options);
        $mform->addRule('id_sede', null, 'required');

        $mform->addElement('searchableselector', 'id_facultad', 'Facultad', $facultad_options);
        $mform->addRule('id_facultad', null, 'required');
        $buttonarray=array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
    }

    public function get_errors(): array {
        $common_errors = $this->_form->_errors;
        $custom_erros = $this->validation((array) $this->get_data(), array());
        $errors = array_merge($custom_erros,  $common_errors);
        return $errors;
    }
    public function validation($data, $files): array {
        parent::validation($data, $files);
        $program = new Programa($data);

        $errors = array();

        if (!$program->valid()) {
            if ($program->has_error(DatabaseErrorFactory::UNIQUE_KEY_CONSTRAINT_VIOLATION)) {
                /* @var Programa $repeated_program */
                $repeated_program = Programa::get_by(array(
                    Programa::JORNADA => $program->jornada,
                    Programa::CODIGO_UNIVALLE => $program->cod_univalle,
                    Programa::ID_SEDE => $program->id_sede));
                /* If array is not empty, error     is detected, but here is no specific field error*/
                $errors[BaseDAO::GENERIC_ERRORS_FIELD]="Ya existe un programa con la misma jornada, codigo univalle y sede, este es '$repeated_program->nombre'";
            }
        }
        return $errors;
    }
    /**
     * Return an instance of Programa extracted of the form data
     *
     * @throws ErrorException This will never happen, form and program have same properties
     * @return Programa Extracted program
     */
    public function get_program(): Programa {

        $program = new Programa();
        $form_data = $this->get_data();

        $program->make_from($form_data);
        return $program;
    }
}