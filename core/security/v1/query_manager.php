<?php
/**
 * @package		block_ases
 * @subpackage	core.security
 * @author 		Jeison Cardona Gómez
 * @copyright 	(C) 2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once( __DIR__ . "/query_manager/aux_functions.php" );


const CORE_DB_LOCATION =  __DIR__ . "/../../core_db/core_db.php";
const FLAG_CORE_DB = "core_db_select_sql";
const FLAG_MOODLE = "DB";
const MANAGER_ALIAS_CORE_DB = "core_db";
const MANAGER_ALIAS_MOODLE = "moodle";
const MANAGER_ALIAS_POSTGRES = "postgres";
const AVAILABLE_MANAGERS = [ 
	MANAGER_ALIAS_CORE_DB, 
	MANAGER_ALIAS_MOODLE, 
	MANAGER_ALIAS_POSTGRES 
];

if( file_exists ( CORE_DB_LOCATION ) ){
	require_once( CORE_DB_LOCATION );
}

/**
 * ...
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @see _is_select(...)
 * @param string $selector DB-Manager filter, if this param is not null, a specífic manager is selected.
 * @return lambda function
 */
function get_db_manager( $selector = NULL ){

	$selector_filter = [];

	foreach ( AVAILABLE_MANAGERS as $key => $manager) {
		$selector_filter[$manager] = true;
	}

	if( !is_null( $selector ) ){
		$selector_filter = [];
		foreach ( AVAILABLE_MANAGERS as $key => $manager) {
			$selector_filter[$manager] = false;
		}
		if( array_key_exists( $selector, $selector_filter ) ){
			$selector_filter[$selector] = true;
		}
	}

	/**
	 * Automatic and specific manager selection.
	*/
	
	if(	in_array( FLAG_CORE_DB, get_defined_functions()['user']) && $selector_filter[ MANAGER_ALIAS_CORE_DB ] ){

		return function( $query, $params = NULL, $extra = NULL ){
			$select_filter = _is_select( $query );
			if( is_null( $select_filter ) ){
				throw new Exception( "Query cannot be empty." );
			}else{
				if( $select_filter ){
					return core_db_select_sql( $query, $params );
				}else{
					return core_db_execute( $query, $params );
				}
			}
		};

	}else if( array_key_exists( FLAG_MOODLE, $GLOBALS ) && $selector_filter[ MANAGER_ALIAS_MOODLE ] ){

		return function( $query, $params = NULL, $extra = NULL ){
			$select_filter = _is_select( $query );
			if( is_null( $select_filter ) ){
				throw new Exception( "Query cannot be empty." );
			}else{
				if( $select_filter ){
					return array_values($GLOBALS[ FLAG_MOODLE ]->get_records_sql($query, $params));
				}else{
					return $GLOBALS[ FLAG_MOODLE ]->execute($query, $params);
				}
			}
		};

	}else{

		return function( $query, $params = NULL, $extra = NULL ){
			if( is_null( $extra ) ){
				throw new Exception( "extra['db_connection'] does not exist" );
			}else{
				if( array_key_exists( "db_connection", $extra ) ){
					if( 
						array_key_exists( "host", $extra['db_connection'] ) &&
						array_key_exists( "port", $extra['db_connection'] ) &&
						array_key_exists( "dbname", $extra['db_connection'] ) &&
						array_key_exists( "user", $extra['db_connection'] ) &&
						array_key_exists( "password", $extra['db_connection'] )
					){
						$host = $extra['db_connection']['host'];
						$port = $extra['db_connection']['port'];
						$dbname = $extra['db_connection']['dbname'];
						$user = $extra['db_connection']['user'];
						$password = $extra['db_connection']['password'];

						$conn_string = "host=".$host." port=".$port." dbname=".$dbname." user=".$user." password=".$password."";
						$connection = pg_connect( $conn_string );

						if( $params ){
							return pg_fetch_all( pg_query_params( $connection, $query, $params ) );
						}else{
							return pg_fetch_all( pg_query( $connection, $query ) );
						}
					}
				}else{
					return null;
				}
			}
		};

	}

}