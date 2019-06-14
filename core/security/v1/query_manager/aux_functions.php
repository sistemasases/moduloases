<?php

/**
 * @package	block_ases
 * @subpackage	core.security
 * @author 	Jeison Cardona Gómez
 * @copyright 	(C) 2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Function that given a SQL query, ...
 * 
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @param $query
 * 
 * @return bool|null
 */
function _is_select($query) {
    if ($query) {
        $words = explode(' ', trim($query));
        if (strtolower(trim($words[0])) == "select") {
            return true;
        } else {
            return false;
        }
    } else {
        return null;
    }
}

/**
 * Function that return a list of managers available 
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @return array List of managers
 */
function _get_list_of_available_managers() {
    return AVAILABLE_MANAGERS;
}

function _strpos_all($haystack, $needle) {
    $offset = 0;
    $allpos = array();
    while (($pos = strpos($haystack, $needle, $offset)) !== FALSE) {
        $offset = $pos + 1;
        $allpos[] = $pos;
    }
    return $allpos;
}

?>