<?php

require_once( __DIR__ . '/classes/DOMAttributeList.php' );

function dom_add_attributte( DOMElement &$tag, DOMAttributeList $attr_obj ):void
{
    foreach( $attr_obj->getAttributes() as $attr => $val ){
        
        $tag->setAttribute("class", "form-control $field_attr_inputclass");
        
    }
}
