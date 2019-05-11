<?php 
/**
 * @package		block_ases
 * @subpackage	core.security
 * @author 		Jeison Cardona Gómez
 * @copyright 	(C) 2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once( __DIR__ . "/../../../../../config.php");
require_once( __DIR__ . "/query_manager.php");

/**
 * @see get_action(...)
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @param string $function_name, Function name.
 * @param array $args, Arguments.
 * @param array $context, Context for execution.
 * @param integer $user_id, User that execute the function.
 */
function secure_Call( $function_name, $args = null, $context = null, $user_id = null ){

	/**

	Context example:

		array(
			'fun_name' => array(
				'action_alias' => 'one_alias',
				'params_alias' => "one_alias"
			)
		)

	*/


	if( is_null($context) ){	
		throw new Exception( "Undefined context" ); 
	}else{
		if( !array_key_exists( $function_name , $context) ){
			throw new Exception( "Function '$function_name' does not exist in the context" ); 
		}
	}
	
	//Get action
	$action = get_action( $context[ $function_name ]['action_alias'] );

	if( is_null( $action ) ){
		// Control de no existencia de acción
		/**
		 * En caso de que no exista, se debe registrar en la base de datos
		*/
	}else{

		$defined_user_functions = get_defined_functions()['user'];
		if( in_array( $function_name, $defined_user_functions ) ){
			return call_user_func_array( $function_name, $args );
		}else{
			throw new Exception( "Function " . $function_name . " was not declared." );
		}

	}

}


/**
 * Function that returns an action given an id or alias.
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @param mixed $in, this input param can be and id (Integer)  or alias (String)
 * @return object|null
*/
function get_action( $in ){

	$params = [];
	$criteria = null;
	$actions_tablename = $GLOBALS['PREFIX'] . "talentospilos_acciones";

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

	$manager = get_db_manager();
	array_push($params, $in);
	
	return $manager( $query = "SELECT * FROM $user_table WHERE $criteria = $1", $params, $extra = null );

}

?>