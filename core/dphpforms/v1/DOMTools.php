<?php

require_once( __DIR__ . '/classes/DOMAttributeList.php' );

function _core_dphpforms_dom_add_attributtes( DOMElement &$tag, DOMAttributeList $attr_obj ):void
{   
    foreach( $attr_obj->getAttributes() as $attr => $val ){
        
        $is_array = ( ( gettype( array() ) == gettype( $val ) ) ? true : false );
        if( ( ( $attr == "disabled" ) || ( $attr == "required" ) ) && !$val[0] ){
            continue;
        }else{
            $val = "";
        }
        $tag->setAttribute( $attr, ($is_array ? implode( " " , array_filter($val)) : $val) );
        
        
    }
}
