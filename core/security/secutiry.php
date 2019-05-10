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

print_r( get_list_of_available_modules() );

function secure_call( $function_name, $args = [], $alias ){	return make_call( $function_name, $args ); };

?>
