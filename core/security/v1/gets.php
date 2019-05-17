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
 * Function that returns an action given an id or alias.
 *
 * @see get_db_manager() in query_manager.php
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @param mixed $in, this input param can be and id (Integer) or alias (String)
 *
 * @return object|null
*/
function _core_security_get_action( $in ){

	global $DB_PREFIX;

	$params = [];
	$criteria = null;
	$tablename = $DB_PREFIX . "talentospilos_acciones";

	if( $in ){
		if( is_numeric($in) ){
			$criteria = "id";
		}else if( is_string( $in ) ){
			$criteria = "alias";
		}else{
			return null;
		}
	}else{
		return null;
	}

	array_push($params, $in);

	$manager = get_db_manager();
	$action = $manager( $query = "SELECT * FROM $tablename WHERE $criteria = $1 AND eliminado = 0", $params, $extra = null );
	return ( count( $action ) == 1 ? $action[0] : null );

}

/**
 * Function that returns actions given an role_id.
 *
 * @see get_db_manager() in query_manager.php
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @param integer $user_id
 *
 * @return array
*/
function _core_security_get_actions( $role_id, $type = null ){

	global $DB_PREFIX;

	$params = [];
	$type_filter = null;
	$tablename = $DB_PREFIX . "talentospilos_roles_acciones";
	$actions_tablename = $DB_PREFIX . "talentospilos_acciones";

	if( is_numeric($role_id) ){
		array_push($params, $role_id);
		if( !is_null( $type ) ){
			if( is_numeric( $type ) ){
				$type_filter = "AND id_tipo_accion = $2";
				array_push($params, $type);
			}else{
				return null;
			}
		}
	}else{
		return null;
	}

	$manager = get_db_manager();
	$query = "SELECT * 
	FROM $actions_tablename AS A 
	INNER JOIN $tablename AS RA 
	ON RA.id_accion = A.id 
	WHERE RA.id_rol = $1 AND RA.eliminado = 0 AND A.eliminado = 0 $type_filter";

	$role_actions = $manager( $query, $params, $extra = null );
	return ( count( $role_actions ) >= 1 ? $role_actions : null );

}

/**
 * Function that returns actions types.
 *
 * @see get_db_manager() in query_manager.php
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @param integer $user_id
 *
 * @return array
*/
function _core_security_get_actions_types(){

	global $DB_PREFIX;
	$tablename = $DB_PREFIX . "talentospilos_tipos_accion";

	$manager = get_db_manager();
	return $manager( $query = "SELECT * FROM $tablename", $params, $extra = null );

}

/**
 * Function that return a rol given an user id.
 *
 * Singylarizations are extra filters.
 *
 * Example:
 *
 * 	array(
 * 		'filter_1' => "value",
 *  	'filter_2' => "value"
 * 	)
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @param integer $user_id
 * @param integer $time_context
 * @param array $singularizations
 *
 * @return object|null
*/
function _core_security_get_user_rol( $user_id, $time_context = null, $singularizations = null ){

	global $DB_PREFIX;

	$params = [];
	$tablename = $DB_PREFIX . "talentospilos_usuario_rol";

	if( !is_numeric($user_id) ){
		return false;
	}

	if( is_null( $time_context ) ){
		$time_context = time();
	}

	array_push($params, $user_id);

	$manager = get_db_manager();

	$user_roles = $manager( $query = "SELECT * FROM $tablename WHERE id_usuario = $1 AND eliminado = 0", $params, $extra = null );


	foreach ($user_roles as $key => $u_rol) {

		$rol = new stdClass();
		
		if( 
			( $u_rol->usar_intervalo_alternativo == 0 ) && 
			( !is_null( $u_rol['fecha_hora_inicio'] ) ) && 
			( !is_null( $u_rol['fecha_hora_fin'] ) )
		){
			$rol->start = strtotime($u_rol['fecha_hora_inicio']);
			$rol->end = strtotime($u_rol['fecha_hora_fin']);
		}else if( 
			( $u_rol['usar_intervalo_alternativo'] == 1 ) && 
			( !is_null( $u_rol['usar_intervalo_alternativo'] ) )
		){
			$alternative_interval = _core_secutiry_solve_alternative_interval( $u_rol['intervalo_validez_alternativo'] );
			if( $alternative_interval ){
				$rol->start = strtotime($alternative_interval['fecha_hora_inicio']);
				$rol->end = strtotime($alternative_interval['fecha_hora_fin']);
			}
		}

		$valid_singularization = true;

		if( !is_null($u_rol['singularizador']) ){

			foreach (json_decode($u_rol['singularizador']) as $key => $db_singularization) {
				if( array_key_exists($db_singularization->key, $singularizations) ){
					if( !($db_singularization->value == $singularizations[ $db_singularization->key ]) ){
						$valid_singularization = false;
						break;
					}
				}else{
					$valid_singularization = false;
					break;
				}
			}

		}

		if( 
			($time_context >= $rol->start) &&
			($time_context <= $rol->end) && 
			$valid_singularization
		){
			
			return $u_rol;
		}
	}

	return null;
}

/**
 * 
 * @see get_db_manager() in query_manager.php
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @return array
*/
function _core_security_get_role( $role_id ){

	global $DB_PREFIX;

	$params = [];
	$tablename = $DB_PREFIX . "talentospilos_roles";

	if( !is_numeric($role_id) ){
		return null;
	}

	array_push($params, $role_id);

	$manager = get_db_manager();
	$role = $manager( $query = "SELECT * FROM $tablename WHERE id = $1", $params, $extra = null );
	
	return ( count( $role ) == 1 ? $role[0] : null );

}


?>