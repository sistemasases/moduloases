<?php
/**
 * @package		block_ases
 * @subpackage	core.security
 * @author 		Jeison Cardona Gómez
 * @copyright 	(C) 2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @param $query
*/
function _is_select( $query ){

	if( $query ){
		$words = explode(' ',trim($query));
		if( strtolower($words[0]) == "select" ){
			return true;
		}else{
			return false;
		}
	}else{
		return null;
	}

}

/**
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
*/
function _get_list_of_available_managers(){	return AVAILABLE_MANAGERS; };

?>