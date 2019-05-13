<?php

/**
 * @package		block_ases
 * @subpackage	core.module_loader
 * @author 		Jeison Cardona Gómez
 * @copyright 	(C) 2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @param $route Module name.
 */
function module_loader( $module_name ){
	$interface = __DIR__ . "/".$module_name."/".$module_name.".php" ;
	if( file_exists( $interface ) ){
		require_once( $interface );
	}
}

/**
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @param $route Module name.
 */
function get_list_of_available_modules(){

	$modules = [];
	foreach (glob(__DIR__ . '/*' , GLOB_ONLYDIR) as $key => $value) {
		$arr = explode('/',trim( $value ));
		array_push( $modules, $arr[ count( $arr ) - 1 ] );
	}

	return $modules;
}

?>