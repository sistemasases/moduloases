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
require_once( $CFG->dirroot.'/blocks/ases/managers/user_management/user_management_lib.php' );


//Eliminar usuarios y desmatricular.
function plugin_status_get_users_data_by_instance( $instanceid ){
	
    global $DB;

    $courseid = plugin_status_get_courseid_by_block_instance( $instanceid ); 
    $enrol = plugin_status_get_manual_enrol_by_courseid($courseid);
    $users = plugin_status_get_user_enrolments($enrol->id);

    $users = array_filter(
    	array_map(
        	function($in){ 

        		$user = user_management_get_full_moodle_user($in->userid);

        		$simple_user = new stdClass();
        		$simple_user->id = $user->id;
        		$simple_user->firstname = $user->firstname;
        		$simple_user->lastname = $user->lastname;
        		$simple_user->username = $user->username;

        		return ( _plugin_status_is_sistemas1008( $user ) ? null : $simple_user ); 
        	}, 
        	$users 
        )
    );


    $users_with_groups = [];
    foreach ($users as $key => $user) {

    	$groups = array_values(plugin_status_get_groups_from_user_by_course( $user->id, $courseid ));
    	$groups = array_map(
    		function($in){
    			$simple_group = new stdClass();
    			$simple_group->name = $in->name;
    			return $simple_group;
    		},
    		$groups
    	);

    	array_push( 
    		$users_with_groups, 
    		array(
    			'user' => $user,
    			'groups' => $groups
    		)
    	);
    }

	return $users_with_groups;

}

function plugin_status_remove_users_enrol( $instanceid, $userids ){

	global $DB;

	if( plugin_status_check_users_enrol( $instanceid, $userids  ) ){
		$curseid = plugin_status_get_courseid_by_block_instance( $instanceid );
		$enrolid = plugin_status_get_manual_enrol_by_courseid( $curseid );
		foreach ($userids as $key => $uid) {

			$user = user_management_get_full_moodle_user($uid);

			if( !_plugin_status_is_sistemas1008( $user ) ){
				$sql_query = "DELETE FROM {user_enrolments} WHERE enrolid = '$enrolid->id' AND userid = '$uid'";
    			$DB->execute($sql_query);
			}

		}

		return true;

	}else{
		return null;
	}

}

function plugin_status_check_users_enrol( $instanceid, $userids ){

	global $DB;

	if( !is_array( $userids ) ){
		return null;
	}

	$curseid = plugin_status_get_courseid_by_block_instance( $instanceid );
	if( $curseid ){

		$enrolid = plugin_status_get_manual_enrol_by_courseid( $curseid );
		if( $enrolid ){

			foreach ($userids as $key => $uid) {

				if( !is_numeric($uid) ){
					return null;
				}

				$sql = "SELECT * 
				FROM {user_enrolments}
				WHERE enrolid = '$enrolid->id' AND userid = '$uid'
				ORDER BY timecreated ASC";

				$uenrol = $DB->get_records_sql( $sql );
				if( !$uenrol ){
					return false;
				}
			}
			return true;

		}else{
			return null;
		}

	}else{
		return null;
	}
}

function plugin_status_get_ases_instances(){

	global $DB;

	$sql = "SELECT id 
	FROM {block_instances} 
	WHERE blockname = 'ases'";

	return $DB->get_records_sql( $sql );

}

function plugin_status_get_courseid_by_block_instance( $instanceid ){

	global $DB;

	$sql = "SELECT instanceid AS courseid
	FROM {context} 
	WHERE id = (
		SELECT parentcontextid 
		FROM {block_instances} 
		WHERE id = '$instanceid'
	)";

	$data = $DB->get_record_sql( $sql );

	return ( $data ? $data->courseid : null);
}

function plugin_status_get_manual_enrol_by_courseid( $courseid ){

	global $DB;

	$sql = "SELECT * 
	FROM {enrol} 
	WHERE courseid = '$courseid' AND enrol = 'manual'";

	return $DB->get_record_sql( $sql );
}

function plugin_status_get_user_enrolments( $enrolid ){

	global $DB;

	$sql = "SELECT * 
	FROM {user_enrolments}
	WHERE enrolid = '$enrolid'
	ORDER BY timecreated ASC";


	return $DB->get_records_sql( $sql );

}

function _plugin_status_is_sistemas1008( $moodle_user ){
	if( $moodle_user->username === "sistemas1008" ){
		return true;
	}else{
		return false;
	}
}

function plugin_status_get_course_groups( $courseid ){

	global $DB;
	$sql = "SELECT * FROM {groups} WHERE courseid = '$courseid'";
	return $DB->get_records_sql( $sql );

}

/**
 * Function that returns given an user and courseid, a list of groups to which belongs
 */
function plugin_status_get_groups_from_user_by_course( $userid, $courseid ){

	global $DB;

	$sql = "SELECT * 
	FROM {groups} AS G0
	INNER JOIN {groups_members} GM0
	ON G0.id = GM0.groupid
	WHERE 
		G0.courseid = '$courseid' AND GM0.userid = '$userid'";

	return $DB->get_records_sql( $sql );

}


?>
