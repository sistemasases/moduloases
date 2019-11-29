<?php

/**
 * @package	block_ases
 * @subpackage	core.dphpforms
 * @author 	Jeison Cardona Gómez
 * @copyright 	(C) 2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once( __DIR__ . "/../DOMTools.php");


/**
 * Function that generates the html of the buttons.
 * @author Jeison Cardona Gómez, <jeison.cardona@correounivalle.edu.co>
 * @param String $alias, this alias will be used as class-identifier, for instance, btn-dphpforms-alias
 * @param String $text, it is the buttom value.
 * @param String $classes, aditional css classes.
 * @return String HTML with the buttons tags.
 */

function _dphpforms_generate_html_button( &$dom, $alias, $text, $classes, $allow_reserved_alias = false ){
    
    $reserved_aliases = [
        "update",
        "delete",
        "reset"
    ];

    if( is_null( $alias ) ){
        return null;
    } 
    
    if( in_array( $alias, $reserved_aliases ) && !$allow_reserved_alias ){
        return null;
    }

    $aditional_classes = "";

    if( $classes ){
        $classes = array_map(
            function($class) use ($alias){
                $default_classes = [
                    "button",
                    "btn-dphpforms",
                    "btn-dphpforms-" . $alias,
                ];
                if( in_array( $class, $default_classes ) ){
                    return null;
                }else{
                    return $class;
                }
            },
            $classes
        );
        $aditional_classes = join( $classes, " " );
    }
    
    $btn_input = _core_dphpforms_build_tag(
            $dom, "input", new DOMAttributeList([
                'class' => array_merge(
                        ["button", "btn-dphpforms", "btn-dphpforms-" . $alias,],
                        $classes
                ),
                'type' => "button",
                'value' => $text
             ])
    );

    return $btn_input;
    
}