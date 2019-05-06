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

$manager = get_db_manager();
$result = $manager("SELECT * FROM mdl_user WHERE id = $1", [ "128" ]);
print_r($result);

/*
 *
*/
function make_call( $function_name, $args = [] ){

	$manager = get_db_manager('moodle');
	//$result = $manager("SELECT * FROM mdl_user WHERE id = 73400", NULL, []);

	$defined_user_functions = get_defined_functions()['user'];
	if( in_array( $function_name, $defined_user_functions ) ){
		return call_user_func_array( $function_name, $args );
	}else{
		throw new Exception( "Function " . $function_name . " was not declared." );
	}
}

?>