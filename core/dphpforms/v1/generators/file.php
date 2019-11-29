<?php

/**
 * @package	block_ases
 * @subpackage	core.dphpforms
 * @author 	Jeison Cardona Gómez
 * @copyright 	(C) 2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once( __DIR__ . "/../DOMTools.php");

function _dphpforms_generate_FILE( &$dom, $id_formulario_pregunta, $context, $statement, $prefix_uniqid ){
            
    $div = _core_dphpforms_build_tag( $dom, "div", new DOMAttributeList() );
    
    return $div;

}