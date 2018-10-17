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

const ADD_USER_TO_COHORT = 'add_user_to_cohort';
const CREATE_MOODLE_USER = 'create_moodle_user';
const CREATE_ASES_USER = 'create_ases_user';
const CREATE_UPDATE_USER = 'create_update_user'; // actualiza user

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

