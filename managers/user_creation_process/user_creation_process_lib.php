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
 * User creation process explained, it cover from the moodle user creation, to user state creation, in other words
 * is the complete process from zero for create a ASES user.
 * This lib have all the constants and methods for support the creation process of Ases user creation
 *
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace user_creation_process;

defined('MOODLE_INTERNAL') || die;

const ADD_USER_TO_COHORT = 1;
const CREATE_MOODLE_USER = 2;
const CREATE_ASES_USER = 3;
const CREATE_UPDATE_USER = 4; // actualiza user

const ADD_USER_TO_COHORT_NAME = 'Añadir usuario a cohorte';
const CREATE_MOODLE_USER_NAME = 'Crear usuario moodle';
const CREATE_ASES_USER_NAME = 'Crear usuario ases';
const CREATE_UPDATE_USER_NAME = 'Crear actualiza user'; // actualiza user

const CREATION_STEPS = array (
    ADD_USER_TO_COHORT,
    CREATE_MOODLE_USER,
    CREATE_ASES_USER,
    CREATE_UPDATE_USER
);

const ADD_USER_TO_COHORT_URL = '/blocks/ases/view/add_moodle_user_to_cohort.php';
const CREATE_MOODLE_USER_URL  = '/blocks/ases/view/moodle_user_creation.php';
const CREATE_ASES_USER_URL  = '/blocks/ases/view/create_ases_user.php';
const CREATE_UPDATE_USER_URL = '/blocks/ases/view/create_user_extended.php'; // actualiza user

function generate_add_user_to_cohort_url($blockid, $courseid, $username, $continue=true, $user_created = false): \moodle_url {
    $url = new \moodle_url(ADD_USER_TO_COHORT_URL,
        array(
            'courseid' => $courseid,
            'instanceid' => $blockid,
            'username'=> $username,
            'continue'=>$continue,
            'user_created'=>$user_created));
    return $url;
}

/**
 * Class Step
 *
 * Model a step, for process than have sequential steps
 *
 * @property integer $number Step number in a sequence of steps
 * @property string $name Step name or title
 * @property boolean $is_current_active True if actually is $this step what is being done
 * @package user_creation_process
 *
 */
class Step {
    public $number;
    public $name;
    public $is_current_active;

    /**
     * Step constructor.
     * @param int $number
     * @param string $name
     * @param bool $is_current_active
     */
    public function __construct(int $number, string $name, bool $is_current_active = false)
    {
        $this->number = $number;
        $this->name = $name;
        $this->is_current_active = $is_current_active;
    }
    public static function _disable_all_steps(array $steps) {
        /* @var Step $step*/
        foreach($steps as $step) {
            $step->is_current_active = false;
        }
    }

}

$add_user_to_cohort = new Step(ADD_USER_TO_COHORT,  ADD_USER_TO_COHORT_NAME);
$create_moodle_user = new Step( CREATE_MOODLE_USER, CREATE_MOODLE_USER_NAME);
$create_ases_user = new Step( CREATE_ASES_USER, CREATE_ASES_USER_NAME);
$create_update_user = new Step( CREATE_UPDATE_USER, CREATE_UPDATE_USER_NAME);

$__steps = array(
    $add_user_to_cohort->number=>$add_user_to_cohort,
    $create_moodle_user->number=>$create_moodle_user,
    $create_ases_user->number=>$create_ases_user,
    $create_update_user->number=>$create_update_user
);

function get_steps($current = null): array {
    global $__steps;
    if( $current ) {
        $steps = $__steps;
        Step::_disable_all_steps($steps);
        /* @var Step $current*/
        $current = $steps[$current];
        $current->is_current_active= true;
        return array_values($steps);
    } else {
        return array_values($__steps);
    }
}


function generate_create_moodle_user_url($blockid, $courseid, $username, $continue=true): \moodle_url {
    $url = new \moodle_url(CREATE_MOODLE_USER_URL,
        array(
            'courseid' => $courseid,
            'instanceid' => $blockid,
            'username'=> $username,
            'continue'=>$continue));
    return $url;
}


function generate_create_ases_user_url($blockid, $courseid, $username, $continue=true): \moodle_url {
    $url = new \moodle_url(CREATE_ASES_USER_URL,
        array(
            'courseid' => $courseid,
            'instanceid' => $blockid,
            'username'=> $username,
            'continue'=>$continue));
    return $url;
}



function generate_create_ases_update_user_extended_url($blockid, $courseid, $username, $num_doc=null, $continue=true): \moodle_url {
    $params =  array(
        'courseid' => $courseid,
        'instanceid' => $blockid,
        'username'=> $username,
        'continue'=>$continue);
    if( $num_doc ) {
        $params['num_doc'] = $num_doc;
    }
    $url = new \moodle_url(CREATE_UPDATE_USER_URL, $params);

    return $url;
}

/**
 * Write html necesary for the steper
 *
 * @param $current_step can be one of the CREATION_STEPS elements
 * @see CREATION_STEPS
 * @throws \coding_exception
 */
function write_steps($current_step) {
    global $PAGE, $output;
    $PAGE->requires->js_call_amd('block_ases/progress_bar_component', 'init');
    $template_data = new \stdClass();
    $template_data->items = \user_creation_process\get_steps($current_step);
    $student_profile_page = new \block_ases\output\progress_bar_component($template_data);
    echo $output->render($student_profile_page);
}

