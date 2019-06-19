<?php
/**
 * @package		block_ases
 * @subpackage	core.security
 * @author 		Jeison Cardona Gómez
 * @copyright 	(C) 2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

module_loader( "core_db" );

require_once( __DIR__ . "/query_manager/aux_functions.php" );

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

/**
 * ...
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @see _is_select(...)
 * @param string $selector DB-Manager filter, if this param is not null, a specífic manager is selected.
 * @return callable function
 *
 * @example 
 *
 	$manager = get_db_manager("general");
	$result = $manager(
		"SELECT * FROM mdl_user WHERE id = $1",
		[ "128" ],
		[
			"db_connection" => [
				"host" => "localhost",
				"port" => "5432",
				"user" => "user",
				"dbname" => "moodle35",
				"password" => "password"
			]
		] 
	);
 *
 */
function get_db_manager( $selector = null ) {

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
	
	if( in_array( FLAG_CORE_DB, get_defined_functions()['user']) && $selector_filter[ MANAGER_ALIAS_CORE_DB ] ){
		return function( $query, $params = null, $extra = null ){
			$select_filter = _is_select( $query );
			if( is_null( $select_filter ) ){
				throw new Exception( "Query cannot be empty." );
			}else{
				if( $select_filter ){
					return json_decode(json_encode( core_db_select_sql( $query, $params ) ), true);
				}else{
					return core_db_execute( $query, $params );
				}
			}
		};

	}else if( array_key_exists( FLAG_MOODLE, $GLOBALS ) && $selector_filter[ MANAGER_ALIAS_MOODLE ] ){
		return function( $query, $params = null, $extra = null ){
			$select_filter = _is_select( $query );
			if( is_null( $select_filter ) ){
				throw new Exception( "Query cannot be empty." );
			}else{
				if( $select_filter ){
					return json_decode(json_encode( array_values( $GLOBALS[ FLAG_MOODLE ]->get_records_sql($query, $params)) ), true);
				}else{
					return $GLOBALS[ FLAG_MOODLE ]->execute($query, $params);
				}
			}
		};

	}else{

		return function( $query, $params = null, $extra = null ){
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

/**
 * Function that given a table name without prefix, list of criteria and params, 
 * return a simple selector with a list or records
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see get_db_manager( ... ) in this file
 * @param string $tablename
 * @param array $criteria Filters
 * @param array $params 
 * 
 * @return array|null 
 */
function get_db_records( $tablename, $criteria = null, $params = null ){
    
    global $DB_PREFIX;

    $table = $DB_PREFIX . $tablename;
    $manager = get_db_manager();
    
    $where = "";
    if( count($criteria) > 0 ){
        $where .= "WHERE";
        foreach ($criteria as $key => $cond){
            $where .= " $cond = $" . ($key + 1);
            ( next($criteria) ? $where .= " AND" : null );
        }
    }
    
    $result = $manager( $query = "SELECT * FROM $table $where", $params, $extra = null );
    
    return ( count( $result ) > 0 ? $result : null );
    
}

/**
 * Function that given a table name, return a description.
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see get_db_manager( ... ) in this file
 * 
 * @param string $tablename Table name
 * 
 * @return array|null Return an array if table exist
 */
function get_table_structure( $tablename ){
    
    $manager = get_db_manager();
    
    $query = "
        SELECT
            ROW_NUMBER() OVER (), * 
        FROM
            information_schema.COLUMNS
        WHERE
            TABLE_NAME = '$tablename'";
    
    $result = $manager( $query, $params = null, $extra = null );

    return ( count( $result ) > 0 ? $result : null );
    
}

/**
 * Function that given a table name and schema name, return a list of constrains.
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see get_db_manager( ... ) in this file
 * 
 * @param string $tablename Table name
 * @param string $schema Schema name
 * 
 * @return array|null Return an array if exist at least one constraint
 */
function get_table_constrains( $tablename, $schema = 'public' ){
    
    $manager = get_db_manager();
    
    $query = "
        SELECT 
            ROW_NUMBER() OVER (), con.*
        FROM 
            pg_catalog.pg_constraint con
        INNER JOIN 
            pg_catalog.pg_class rel 
        ON 
            rel.oid = con.conrelid
        INNER JOIN 
            pg_catalog.pg_namespace nsp 
        ON 
            nsp.oid = connamespace
        WHERE 
            nsp.nspname = '$schema'
                AND rel.relname = '$tablename'";
    
    $result = $manager( $query, $params = null, $extra = null );
    
    return ( count( $result ) > 0 ? $result : null );
}

/**
 * ...
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @param array $query_variable 
 * @param array $query_params
 * @param array|null $aditional_filter
 * 
 * @return ...
 */

function solve_query_variable( $query_variable, $query_params, $aditional_filter ){
    
    $manager = get_db_manager();
    $to_return = [];
    
    $ref_table_name = $query_variable[ 'core_special_var_table_name' ];
    $ref_table_filters = $query_variable[ 'core_special_var_filters' ];
    $criteria = "";
    
    foreach( $ref_table_filters as &$filter ){
        $criteria .= 
            $filter . " = " . $query_params[ $filter ] . 
            ( next( $ref_table_filters ) ? " AND " : null );
    }
    
    ( count( $aditional_filter ) > 0 ? $criteria .= " AND " : null );
    
    foreach( $aditional_filter as $key => $filter ){
        $criteria .= 
            $key . " = " . $filter . 
            ( next( $aditional_filter ) ? " AND " : null );
    }
    
    $query = "SELECT * FROM $ref_table_name WHERE $criteria";
    $records = $manager( $query, $param = null, $extra = null );

    $to_exclude = [
        "core_special_var_table_name",
        "core_special_var_filters",
    ];
    
    foreach( $records as $key => $record ){
        $solved_object = [];
        foreach( $query_variable as $key => $data ){
            if(!in_array($key, $to_exclude) ){
                if( gettype($data) == "array" ){

                    $solved_object[$key] = solve_array_value_query_variable( 
                        $record[ $data[ 'core_special_var_col_name' ] ],
                        $data[ 'core_special_var_ref_table_name' ],
                        $data[ 'core_special_var_ref_col_value' ]
                    )[
                        $data[ 'core_special_var_ref_col_value' ]
                    ];

                }else{
                    $solved_object[$key] = $record[$data];
                }
            }
        }
        array_push($to_return, $solved_object);
    }
    
    return $to_return;
    
}

function solve_array_value_query_variable( $id, $tablename, $field_name ){
    $manager = get_db_manager();
    $param = [ $id ];
    $query = "SELECT $field_name FROM $tablename WHERE id = $1";
    $to_return =  $manager( $query, $param, $extra = null );
    return ( count( $to_return ) == 1 ? $to_return[0] : null );
}
