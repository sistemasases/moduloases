<?php

require_once( __DIR__ . '/classes/DOMAttributeList.php' );

function _core_dphpforms_dom_add_attributtes( DOMElement &$tag, DOMAttributeList $attr_obj ):void
{   
    foreach( $attr_obj->getAttributes() as $attr => $val ){
        
        $is_array = ( ( gettype( array() ) == gettype( $val ) ) ? true : false );
        
        if( ( ( $attr == "disabled" ) || ( $attr == "required" ) ) && !$val[0] ){
            
            continue;
            
        }else if( ( ( $attr == "disabled" ) || ( $attr == "required" ) ) && $val[0] ){
            
            $val = "";
            
        }
        
        $tag->setAttribute( $attr, ($is_array ? implode( " " , array_filter($val)) : $val) );
        
    }
}

function _core_dphpforms_build_tag( DOMDocument &$dom_master, string $tag, DOMAttributeList $attr_obj = NULL ): DOMElement
{
    
    $_attr_obj = ( is_null( $attr_obj ) ? new DOMAttributeList([]) : $attr_obj );
    
    $_tag = &$dom_master->createElement( $tag );
    
    _core_dphpforms_dom_add_attributtes( $_tag, $_attr_obj );
    
    return $_tag;
    
}
