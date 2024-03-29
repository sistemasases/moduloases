<?php
/**
 * @package	block_ases
 * @subpackage	core.security
 * @author 	Jeison Cardona Gómez
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
 *	"table_ref": { "name":"table_name", "record_id": 1 },
 *	"col_name_interval_start": "col_start",
 *	"col_name_interval_end": "col_end"
 * }
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @param json $alternative_interval_json
 *
 * @return array|null
*/
function _core_security_solve_alternative_interval( $alternative_interval_json ){

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
 * @param integer $user_id Moodle user ide
 * @param integer $action_id Secutiry action id
 * @param integer $call_params Secutiry action call params
 * @param integer $output Secutiry call return
 *
 * @return void
*/
function _core_security_register_log( $user_id, $action_id, $call_params, $output ){

	global $DB_PREFIX;
	
	$params = [];
	$tablename = $DB_PREFIX . "talentospilos_log_acciones";

	if( !is_numeric($user_id) && !is_numeric($action_id) ){
		return false;
	}

	array_push($params, $user_id);
	array_push($params, $action_id);
	array_push($params, json_encode($call_params));
	array_push($params, json_encode($output));

	$manager = get_db_manager();
	$query = "INSERT INTO $tablename (id_usuario, id_accion, parametros, salida) VALUES($1, $2, $3, $4)";
	$manager( $query, $params, $extra = null );

}

/**
 * Function that given a XML configuration file route, return a list with its
 * aliases.
 * 
 * XML example
 * 
 * <?xml version="1.0"?>
 * <configurations>
 *     <config config_for="api" manager="security_core">
 *         <action>
 *             <alias>
 *                 say_hello
 *             </alias>
 *         </action>
 *         <action>
 *             <alias>
 *                 say_goodbye
 *             </alias>
 *         </action>
 *     </config>
 *     <config config_for="file_subffix">
 *         <action>
 *             <alias>
 *                 value
 *             </alias>
 *         </action>
 *    </config>
 * </configurations>
 * 
 * The previous example return [ 'say_hello', 'say_goodbye' ]
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @param string $xml_route Configuration file route
 * @return array List of aliases in the configuration file
 * 
 */
function _core_security_get_config_actions( $xml_route ){
    
    /** @var object $config SimpleXMLElement with the call 
     * existence declarations. */
    $config = simplexml_load_file( $xml_route );
    
    /* Needs be checked if false, because simplexml_load_file( ... ) 
     * has an mixed output.
     */
    if( $config !== false ){
        
        // At the fun documentation exist an example of this file. 
        $actions = $config->xpath(
            "//config[@config_for='api' and @manager='security_core']//action"
        );
        
        return 
            array_values( // Reset array counter
                array_filter( // Remove empty values
                    array_map( // Get aliases
                        function( $action ){ 
                            return trim( (string) $action->alias[0] ); 
                        } , $actions 
                    )
                )
            );
        
    }
    
    return [];
    
}

/**
 * Function that insert a new role into previous system.
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see get_db_manager( ... ) in query_manager.php
 * 
 * @param string $role_name Name of the new role.
 * 
 * @return integer Response of DB Manager.
 */
function _core_security_create_rol_previous_system_role( $role_name ){
    
    global $DB_PREFIX;
            
    $manager = get_db_manager();
    
    $tablename = $DB_PREFIX . "talentospilos_rol";
    $params = [ $role_name, "Enlaced role created by Security Core system" ];
    $query = "INSERT INTO $tablename (nombre_rol, descripcion) VALUES ($1, $2)";
    return $manager( $query, $params, $extra = null );
     
}

/**
 * Throw exception whether a variable is NULL or empty.
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @param array $var_list List of tuples (var name, var value ) to check.
 * 
 * @throws Exception if $var is NULL or empty.
 * @return void 
 */
function is_empty_exception( array $var_list ): void
{
    foreach( $var_list as $var_name => $var_value ){
        if( is_null( $var_value ) || $var_value === "" ){
            throw new Exception( "Sorry, $var_name cannot be empty." ,-1 );
        }
    }
    
}

/**
 * Function that generate a random string.
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @param integer $length Size of the random string.
 * 
 * @return string Random string.
 */
function generate_random_string( int $length = 10 ):string 
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $characters_length = strlen( $characters );
    $random_string = '';
    for( $i = 0; $i < $length; $i++ ){
        $random_string .= $characters[rand(0, $characters_length - 1)];
    }
    return $random_string;
}

/**
 * Function that return the factorial of a number.
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @param integer $number
 * @return mixed Result of Number!
 */
function factorial( int $number )
{
    return ( $number <= 1 ? 1 : $number * factorial($number - 1) );
}

/**
 * Function that return the number of combinations possibles with repetition.
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @param integer $n Number of values.
 * @param integer $r Subset.
 * 
 * @return integer nCr => $nC$r
 */
function combinations_with_repetition( int $n, int $r ): float                  // For PHP, double, float or real are the same datatype.  
{
    return factorial( $n + $r - 1 ) / ( factorial($r) * factorial($n - 1) );
}

/**
 * Function that validate if a given string is a hex value.
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @param string $hexed_value Value to check.
 * @return bool If true is a valid hex value.
 */
function valid_explicit_hex_value( string $hexed_value ){
    
    $valid_rule_characters = [                                                  // Valid characters for a hex value.
        '0','1','2','3','4',
        '5','6','7','8','9',
        'a','b','c','d','f'
    ];
    
    $array_value = str_split( strtolower( $hexed_value ) );                     // Input string to list of chars.
    
    foreach( $array_value as $value ){                                          // Iterarion over every character.
        if( !in_array($value, $valid_rule_characters) ){                        // Check if the current character isn't a valid hex value.
            return false;
        }
    }
    
    return true;
}

?>