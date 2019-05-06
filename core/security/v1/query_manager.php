<?php
/**
 * @package		block_ases
 * @subpackage	core.security
 * @author 		Jeison Cardona Gómez
 * @copyright 	(C) 2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once( __DIR__ . "/query_manager/aux_functions.php" );

const FLAG_DAO = "dao_get";
const FLAG_MOODLE = "DB";
const AVAILABLE_MANAGERS = [ "dao", "moodle", "general" ];

/**
 * ...
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @see _is_select(...)
 * @param string $selector DB-Manager filter, if this param is not null, a specífic manager is selected.
 * @return lambda function
 */
function get_db_manager( $selector = NULL ){

	/*$selector_filter = [
		'dao' = true,
		'moodle' = true,
		'postgres' = true
	];

	if( !is_null( $selector ) ){
		if( array_key_exists( $selector[$selector], $selector_filter ) ){
			$selector[$selector] = true;
		}
	}else{
		$selector_filter = null;
	}*/

	
	
	if(	in_array( FLAG_DAO, get_defined_functions()['user']) ){
		return function( $query, $extra = NULL, $params = NULL ){
			$select_filter = _is_select( $query );
			if( is_null( $select_filter ) ){
				return null;
			}else{
				if( $select_filter ){
					return dao_select_sql( $query, $params );
				}else{
					return dao_execute( $query, $params );
				}
			}
		};
	}else if( array_key_exists( FLAG_MOODLE, $GLOBALS ) ){
		return function( $query, $extra = NULL, $params = NULL ){
			$select_filter = _is_select( $query );
			if( is_null( $select_filter ) ){
				return null;
			}else{
				if( $select_filter ){
					return $GLOBALS[ FLAG_MOODLE ]->get_records_sql($query, $params);
				}else{
					return $GLOBALS[ FLAG_MOODLE ]->execute($query, $params);
				}
			}
		};
	}else{
		return function( $query, $extra = NULL, $params = NULL ){
			if( is_null( $extra ) ){
				return null;
			}else{
				if( array_key_exists($extra, "db_connection" ) ){
					if( 
						array_key_exists($extra['db_connection'], "host" ) &&
						array_key_exists($extra['db_connection'], "port" ) &&
						array_key_exists($extra['db_connection'], "dbname" ) &&
						array_key_exists($extra['db_connection'], "user" ) &&
						array_key_exists($extra['db_connection'], "password" )
					){

						$host = $extra['db_connection']['host'];
						$port = $extra['db_connection']['port'];
						$dbname = $extra['db_connection']['dbname'];
						$user = $extra['db_connection']['user'];
						$password = $extra['db_connection']['password'];

						$conn_string = "host=".$host." port=".$port." dbname=".$dbname." user=".$user." password=".$password."";
						$connection = pg_connect( $conn_string );

						pg_query_params( $connection, $query, $params );
					
					}
				}else{
					return null;
				}
			}
		};
	}

}

$manager = get_db_manager();
$result = $manager("SELECT * FROM mdl_user LIMIT 1", NULL, []);
print_r($result);

/*function exist_extended_validations(){

	$manage

}*/