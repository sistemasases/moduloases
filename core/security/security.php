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

function core_secure_call( $function_name, $args = null, $context = null, $user_id = null, $singularizations = null, $curren_time = null ){	
	return secure_Call( $function_name, $args, $context, $user_id, $singularizations ); 
};

function hello_world( $in ){
	foreach ($in as $key => $value) {
		echo "hello world\n";
	}
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

print_r( core_secure_call( "hello_world", [1], $context, 73380, $singularizations) );

?>
