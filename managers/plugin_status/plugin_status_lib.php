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
 * Ases block
 *
 * @author     Jeison Cardona Gómez
 * @package    block_ases
 * @copyright  2018 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once( dirname(__FILE__). '/../../../../config.php' );
require_once( $CFG->dirroot.'/blocks/ases/managers/lib/lib.php' );


//Eliminar usuarios y desmatricular.
function plugin_status_remove_users_from_instance( $instance ){
	//mdl_enrol
    global $DB;

}

function plugin_status_tmp(){
	$sql = "SELECT * FROM mdl_course AS C INNER JOIN (

			SELECT * FROM mdl_enrol AS E
			INNER JOIN mdl_user_enrolments AS UE 
			ON UE.enrolid = E.id
			WHERE UE.userid = 73380

			) AS T
			ON T.courseid = C.id";
}

function plugin_status_get_ases_instances(){

	global $DB;
	$sql = "SELECT id FROM {block_instances} WHERE blockname = 'ases'";
	return $DB->get_records_sql( $sql );

}

function plugin_status_get_courseid_by_instance( $instanceid ){

	global $DB;
	$sql = "SELECT instanceid 
	FROM {context} 
	WHERE id = (
		SELECT parentcontextid 
		FROM {block_instances} 
		WHERE id = '$instanceid'
	)";

	return $DB->get_record_sql( $sql );
}

function plugin_status_get_manual_enrol_by_courseid( $courseid ){

	global $DB;
	$sql = "SELECT id 
	FROM {enrol} 
	WHERE courseid = '$courseid' AND enrol = 'manual'";

	return $DB->get_record_sql( $sql );
}


?>
