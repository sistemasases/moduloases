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

/**
 * Class search_ases_user_form_data
 * @property string $num_doc
 * @property string $codigo_est
 */
abstract class search_ases_user_form_data {

}

/**
 * Class search_ases_user_form
 * @method  search_ases_user_form_data get_data()
 */
class search_ases_user_form extends moodleform {
    //Add elements to form
    public function definition() {

        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('header', 'header',   'Buscar usuario ASES');
        $mform->addElement('text', 'num_doc', 'Numero de documento'); // Add elements to your form
        $mform->addElement('text', 'codigo_est', 'Codigo estudiante'); // Add elements to your form
        //normally you use add_action_buttons instead of this code
        $buttonarray=array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('search'));
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
    }
    public function get_errors(): array {
        $files = array();
        $this->_validate_files($files);
        $common_errors = $this->_form->_errors;
        return $common_errors;

    }

}


?>