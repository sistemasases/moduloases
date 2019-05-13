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
 * Function that processes a secure call request.
 *
 * This function requieres a context to execute, an example of this context:
 *
 * $context = array(
 * 	'fun_name' => array( 
 *		'action_alias' => 'one_alias',
 *		'params_alias' => "one_alias"
 *	)
 * )
 *
 * @see get_action( $in ) in this file.
 * @see user_exist( $user_id ) in this file.
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @param string $function_name, Function name.
 * @param array $args, Arguments.
 * @param array $context, Context for execution.
 * @param integer $user_id, User that execute the function.
 *
 * @return array
 */
function secure_Call( $function_name, $args = null, $context = null, $user_id = null, $singularizations = null, $time_context = null ){

	if( is_null( $time_context ) ){
		$time_context = time();
	}

	//Context validation
	if( is_null( $context ) ){	
		throw new Exception( "Undefined context" ); 
	}else{
		if( !array_key_exists( $function_name , $context) ){
			throw new Exception( "Function '$function_name' does not exist in the context" ); 
		}
	}
	
	//Action validation
	$action = _core_security_get_action( $context[ $function_name ]['action_alias'] );

	if( is_null( $action ) ){
		return array(
			'status' => -1,
			'status_message' => 'unregulated action',
			'data_response' => null
		);
	}else{

		if( is_null( $user_id ) ){
			throw new Exception( "User rol cannot be null" ); 
		}else{

			if( $user_id <= -1 ){
				return array(
					'status' => -1,
					'status_message' => 'invalid user ID',
					'data_response' => null
				);
			}else{

				if( _core_security_user_exist( $user_id ) ){

					$user_rol = _core_security_get_user_rol( $user_id, $time_context, $singularizations );

					if( $user_rol ){

						if( _core_security_can_be_executed( $user_rol['id'], $action['id'] ) ){

							$defined_user_functions = get_defined_functions()['user'];

							if( in_array( $function_name, $defined_user_functions ) ){

								$to_return = call_user_func_array( $function_name, $args );

								if( $action['registra_log'] == 1 ){
									_core_security_register_log( $user_id, $action['id'], $args, $to_return );
								}

								return $to_return;
								
							}else{
								throw new Exception( "Function " . $function_name . " was not declared." );
							}

						}else{
							return array(
								'status' => -1,
								'status_message' => 'forbidden, access is denied',
								'data_response' => null
							);
						}

					}else{
						return array(
							'status' => -1,
							'status_message' => 'forbidden, access is denied',
							'data_response' => null
						);
					}
					
				}else{
					return array(
						'status' => -1,
						'status_message' => 'invalid user ID',
						'data_response' => null
					);
				}

			}

		}

	}

}

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

		}else{
			$valid_singularization = false;
		}

		if( 
			($time_context >= $rol->start) &&
			($time_context >= $rol->end) && 
			$valid_singularization
		){
			return $u_rol;
		}
	}

	return null;
}

/**
 * Function that return an interval given an alternative interval definition.
 * Important!: Prefix is not used here.
 *
 * Example of an alternative interval definition:
 *
 * {
 *		"table_ref": { "name":"table_name", "record_id": 1 },
 *		"col_name_interval_start": "col_start",
 *		"col_name_interval_end": "col_end"
 * }
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @param json $alternative_interval_json
 *
 * @return array|null
*/
function _core_secutiry_solve_alternative_interval( $alternative_interval_json ){

	$params = [];
	$alternative_interval_json = json_decode( $alternative_interval_json );

	if( 
		property_exists($alternative_interval_json, 'table_ref') && 
		property_exists($alternative_interval_json, 'col_name_interval_start') && 
		property_exists($alternative_interval_json, 'col_name_interval_end')
	){
		if( 
			property_exists($alternative_interval_json->table_ref, 'name') && 
			property_exists($alternative_interval_json->table_ref, 'record_id')
		){
			if( 
				is_numeric($alternative_interval_json->table_ref->record_id) && 
				$alternative_interval_json->table_ref->record_id != ""
			){
				if( 
					$alternative_interval_json->table_ref->record_id >= 0
				){
					$tablename = $alternative_interval_json->table_ref->name;
					$col_name_interval_start = $alternative_interval_json->col_name_interval_start;
					$col_name_interval_end = $alternative_interval_json->col_name_interval_end;
					$rid = $alternative_interval_json->table_ref->record_id;

					array_push($params, $rid);

					$manager = get_db_manager();

					$query = "SELECT 
						$col_name_interval_start AS fecha_hora_inicio, 
						$col_name_interval_end AS fecha_hora_fin 
					FROM $tablename 
					WHERE id = $1";

					$data = $manager( $query, $params, $extra = null );

					if( count( $data ) == 1 ){
						return array(
							'fecha_hora_inicio' => $data[0]['fecha_hora_inicio'],
							'fecha_hora_fin' => $data[0]['fecha_hora_fin']
						);
					}
				}
			}
		}
	}

	return null;
}

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
 * Function that insert a new log record.
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @param integer $user_id
 * @param integer $action_id
 * @param integer $action_id
 * @param integer $action_id
 *
 * @return void
*/
function _core_security_register_log( $user_id, $action_id, $params, $output ){

	global $DB_PREFIX;
	
	$params = [];
	$tablename = $DB_PREFIX . "talentospilos_log_acciones";

	if( !is_numeric($user_id) && !is_numeric($action_id) ){
		return false;
	}

	array_push($params, $user_id);
	array_push($params, $action_id);
	array_push($params, json_encode($params));
	array_push($params, json_encode($output));

	$manager = get_db_manager();
	$query = "INSERT INTO $tablename (id_usuario, id_accion, parametros, salida) VALUES($1, $2, $3, $4)";
	$manager( $query, $params, $extra = null );

}

?>