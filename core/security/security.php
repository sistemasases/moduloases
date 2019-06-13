<?php
/**
 * @package		block_ases
 * @subpackage	core.security
 * @author 		Jeison Cardona Gómez
 * @copyright 	(C) 2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const VERSION = 1; //Current version.
const CORE_PREFIX = "core_secure_render"; // example: {{#core_secure_render_block_to_protect}}

require_once( __DIR__ . "/../../../../config.php");
require_once( __DIR__ . "/../module_loader.php");

global $DB_PREFIX;
$DB_PREFIX = $GLOBALS[ 'CFG' ]->prefix;
require_once( __DIR__ . "/v" . VERSION . "/entrypoint.php");

/**
 * Function that given a function name, array of arguments, context, user id, singularizations and time, 
 * checks if the user ave permission to execute the function, if can, secure call returns the executed 
 * function, makes a log of this, if this is indicated, else, prevent that function can be executed. 
 *
 * Consider this function to read the rest of the documentation.
 *
 * function hello_world( $times ){
 *		to_return = [];
 * 		for($i = 0; $i < $times; $i++ ) { array_push( $output, "hello world" ); }
 *		return $to_return;
 * }
 *
 * What is a context?
 *
 * A context provide the information necesary for the execution. A set of secure_calls can be defined at 
 * the same context.
 *
 * Context structure:
 *
 * array(
 * 	'fun1_name' => array( 
 *		'action_alias' => 'one_alias',
 *		'params_alias' => "one_alias"
 *	),
 * 'fun2_name' => array( 
 *		'action_alias' => 'one_alias',
 *		'params_alias' => "one_alias"
 *	)
 * );
 *
 * Example:
 * 
 * array(
 *	'hello_world' => array(
 *		'action_alias' => 'say_hello',
 *		'params_alias' => "any"
 *	)
 * )
 *
 * Singularization: An user can be assigned to multiples roles, with a differentiate factor, the singularizations,
 * this allow pick up the current rol, work as a flag, for example, an user can stay in multiple chat rooms, but
 * this user cannot have the same role at all chat rooms, sigularize this user can be possible with the next definition
 * of a singularization.
 *
 * In this example, the user with the identifier 9999, have two roles inside the same system.
 *
 * User id: 		9999
 * Rol id:			5 (standard member)
 * sigularization:	[	
 *						"chat_room" => "Family"
 *					]	
 *
 * User id: 		9999
 * Rol id:			1 (admin member)
 * sigularization:	[	
 *						"chat_room" => "Sales"	
 *					]	
 *
 * Powerful example: in this case, the user 9999 have the role 'standard' between in a interval at the day,  and admin 
 * the rest of the time, every day. The singularizations keys and values need be defined at the user-role relationship.
 *
 * User id: 		9999
 * Rol id:			1 (standard member)
 * sigularization:	[	
 *						"start" => "00:00:00",
 *						"end" => "06:00:00"	
 *					]
 * User id: 		9999
 * Rol id:			1 (admin member)
 * sigularization:	[	
 *						"start" => "06:00:01",
 *						"end" => "23:59:59"			
 *					]
 *
 * Time context: The system need determine the "current" role valid, how can be possible be coordinate with many 
 * time source, for example: database server time, web server time, fixed time, etc. The "current" time need be 
 * provided, if not is provided, the system determine it with time() function.
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @see secure_Call( ... ) in entrypoint.php
 *
 * @param string $function_name
 * @param array $args
 * @param array $context
 * @param integer $user_id
 * @param array $singularizations
 * @param integer $time_context
 *
 * @return mixed
 *
 */
function core_secure_call( $function_name, $args = null, $context = null, $user_id = null, $singularizations = null, $time_context = null ){
	return secure_Call( $function_name, $args, $context, $user_id, $singularizations, $time_context ); 
}

function core_secure_render( &$data, $user_id = null, $singularizations = null, $time_context = null ){
	return secure_render( $data, $user_id, $singularizations, $time_context ); 
}

function core_secure_template_checker( $dir ){
	return secure_template_checker( $dir );
}

/**
 * Interface to secure_call_checker
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see secure_call_checker( ... ) in entrypoint.php
 * 
 * @param $managers_dir Managers location, typical location '/block/ases/managers'
 * 
 * @return array List of call aliases that does not exist at the database
 */
function core_secure_call_checker( $managers_dir ){
    return secure_call_checker( $managers_dir );
}

/**
 * Interface to secure_create_call
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see secure_create_call( ... ) in entrypoint.php
 * 
 * @param string $alias Action alias
 * @param integer|string $action_type Identifier or type alias (back or front)
 * @param string $name Action name
 * @param string $description Action description
 * @param integer $log Allow store every return
 * 
 * @return integer|null If the operation was correct, it will be return 1
 */
function core_secure_create_call($alias, $action_type, $name = NULL, $description = NULL, $log = 0){
    return secure_create_call($alias, $action_type, $name, $description, $log);
}


/**
 * Interface to secure_remove_call
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * 
 * @param integer|string $alias Action alias or identifier
 * @param integer $user_id User Moodle id
 * 
 * @return integer|null If the operation was correct, return 1
 */
function core_secure_remove_call( $alias, $user_id ){
    return secure_remove_call( $alias, $user_id );
}

?>
