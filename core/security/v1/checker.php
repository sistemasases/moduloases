<?php 
/**
 * @package		block_ases
 * @subpackage	core.security
 * @author 		Jeison Cardona Gómez
 * @copyright 	(C) 2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once( __DIR__ . "/query_manager.php");

/**
 * Function that given a rol_id and action_id return if are associated.
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
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

?>