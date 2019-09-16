<?php 
/**
 * @package	block_ases
 * @subpackage	core.security
 * @author 	Jeison Cardona Gómez
 * @copyright 	(C) 2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once( __DIR__ . "/query_manager.php");
require_once( __DIR__ . "/gets.php");

/**
 * Function that given a rol_id and action_id return if are associated.
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see get_db_manager( ... ) in query_manager.php
 *
 * @param integer $rol_id
 * @param integer $action_id
 *
 * @return bool
*/
function _core_security_can_be_executed( $rol_id, $action_id ){

	global $DB_PREFIX;
	
	$params = [];
	$tablename = $DB_PREFIX . "talentospilos_roles_acciones";

	if( !is_numeric($rol_id) && !is_numeric($action_id) ){
		return false;
	}

	array_push($params, $rol_id);
	array_push($params, $action_id);

	$manager = get_db_manager();
	$query = "SELECT * FROM $tablename WHERE id_rol = $1 AND id_accion = $2 AND eliminado = 0";
	$rol_action = $manager( $query, $params, $extra = null );

	return ( count( $rol_action ) == 1 ? true : false );

}

/**
 * Function that validate if an user exist.
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see get_db_manager( ... ) in query_manager.php
 *
 * @param integer $user_id
 *
 * @return bool
*/
function _core_security_user_exist( $user_id ){

	global $DB_PREFIX;

	$params = [];
	$tablename = $DB_PREFIX . "user";

	if( !is_numeric($user_id) ){
		return false;
	}

	array_push($params, $user_id);

	$manager = get_db_manager();
	$user = $manager( $query = "SELECT * FROM $tablename WHERE id = $1", $params, $extra = null );

	return ( count( $user ) == 1 ? true : false );

}

/**
 * Function that validate if an user has an specific role.
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see _core_security_get_role( ... ) in gets.php
 *
 * @param integer $user_id
 *
 * @return bool
*/
function _core_security_check_role( $user_id, $role_id, $time_context = null, $singularizations = null ){


	$user_role = _core_security_get_user_rol( $user_id, $time_context, $singularizations = null );
	$assigned_role = _core_security_get_role( $user_role['id_rol'] );

	$role_wanted = _core_security_get_role( $role_id );
	
	if( $role_wanted && $assigned_role ){

		$found_role = false;
		do{
			$current_role = $assigned_role;
			if( $role_wanted['id'] == $assigned_role['id'] ){
				$found_role = true;
			}
			if( $current_role['id_rol_padre'] != "-1" ){
				$assigned_role = _core_security_get_role( $assigned_role['id_rol_padre'] );
			}
		}while( $current_role['id_rol_padre'] != "-1" );
		
		return $found_role;

	}else{
		return null;
	}

}

/**
 * Function that, given a role id or alias, return if it's an inherited role. An 
 * inherited role is a role that exist in both systems with the same alias ("nombre" in the previous system).
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see _core_security_get_role( ... ) in gets.php
 * @see _core_security_get_previous_system_role( ... ) in gets.php
 * 
 * @param mixed $role Role alias.
 * @return bool Indicates if the given role is an inherited role.
 * 
 */
function _core_security_check_inherited_role( $role ){ 
    return ( ( _core_security_get_role( $role ) && _core_security_get_previous_system_role( $role ) ) ? true : false );
}

/**
 * Function that check if exist inherited role from a given role.
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @param mixed $role Role id or alias.
 * 
 * @throws Exception If the given role doesn't exist in the system.
 * 
 * @return bool True if exist one or more inherited roles.
 */
function _core_security_check_subroles( $role ): bool
{
    
    $db_role = _core_security_get_role( $role );                                // Get role data.
    if( is_null( $role ) ){                                                     // Throw new exception is the given role doesn't exist.
        throw new Exception( "Role '$role' doesn't exist.", -1 );               // Exception if role doesn't exist
    }
    
    global $DB_PREFIX;                                                          // Moodle DB prefix. Ex. mdl_                           
    $tablename = $DB_PREFIX . "talentospilos_roles";                            // Moodle tablename. Ex. mdl_talentospilos_user
    $params = [ $db_role['id'] ];                                               // Params to query. [0] Role id.
    
    $manager = get_db_manager();                                                // Security core database manager.
    $inheritance = $manager(                                                    // Roles that inherit from role given.
        $query = "SELECT * FROM $tablename WHERE id_rol_padre = $1",            // DB query to get inherited roles.
        $params                                                                 // Query params. $1 is assigned to $params[0]
    );

    return ( count( $inheritance ) > 1 ? true : false );                        // If exist one or more inherited roles then return true.
    
}


?>