<?php
/**
 * @package		block_ases
 * @subpackage	core.security
 * @author 		Jeison Cardona Gómez
 * @copyright 	(C) 2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const VERSION = 1; //Current version.

require_once( __DIR__ . "/../../../../config.php");
require_once( __DIR__ . "/../module_loader.php");
require_once( __DIR__ . "/v" . VERSION . "/entrypoint.php");

$PREFIX = $GLOBALS[ 'CFG' ]->prefix;

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
 *						{"key":"chat_room", "value":"Family"}	
 *					]	
 *
 * User id: 		9999
 * Rol id:			1 (admin member)
 * sigularization:	[	
 *						{"key":"chat_room", "value":"Sales"}	
 *					]	
 *
 * Powerful example: in this case, the user 9999 have the role 'standard' between in a interval at the day, and 
 * is admin the rest of the time, every day.
 *
 * User id: 		9999
 * Rol id:			1 (standard member)
 * sigularization:	[	
 *						{"key":"start", "value":"00:00"},
 *						{"key":"end", "value":"06:00:00"}	
 *					]
 * User id: 		9999
 * Rol id:			1 (admin member)
 * sigularization:	[	
 *						{"key":"start", "value":"06:00:01"},
 *						{"key":"end", "value":"23:59:59"}		
 *					]
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @param string $function_name
 * @param array $args
 * @param array $context
 * @param integer $user_id
 * @param array $singularizations
 * @param integer $curren_time
 *
 * @return mixed
 *
 */
function core_secure_call( $function_name, $args = null, $context = null, $user_id = null, $singularizations = null, $curren_time = null ){	
	return secure_Call( $function_name, $args, $context, $user_id, $singularizations ); 
};

/*function hello_world( $in ){
	$output = [];
	foreach ($in as $key => $value) {
		echo "hello world\n";
		array_push($output, $value);
	}
	return $output;
}

$context = [
	'hello_world' => [
		'action_alias' => 'say_hello',
		'params_alias' => "any"
	]
];

$singularizations = array(
	'singularizador_1' => "99",
	'singularizador_2' => "55555"
);

core_secure_call( "hello_world", [[1,2,3]], $context, 73380, $singularizations);*/

?>
