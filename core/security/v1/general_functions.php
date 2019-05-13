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