<?php

require_once( __DIR__ . '/classes/DOMAttributeList.php' );

function _dom_add_attributtes( DOMElement &$tag, DOMAttributeList $attr_obj ):void
{   
    foreach( $attr_obj->getAttributes() as $attr => $val ){
        
        $is_array = ( ( gettype( array() ) == gettype( $val ) ) ? true : false );
        $tag->setAttribute( $attr, ($is_array ? implode( " " , $val) : $val ) );
        
    }
}
