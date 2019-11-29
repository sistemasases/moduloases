<?php

/**
 * @package	block_ases
 * @subpackage	core.dphpforms
 * @author 	Jeison Cardona Gómez
 * @copyright 	(C) 2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once( __DIR__ . "/../DOMTools.php");

function _dphpforms_generate_CHECKBOX( &$dom, $id_formulario_pregunta, $context, $statement, $prefix_uniqid ){

    $options = $context[ 'options' ];

    $opt_num = count($options);
    
    $div = _core_dphpforms_build_tag(
            $dom, "div", new DOMAttributeList([
                'class' => [
                        "div-$id_formulario_pregunta", 
                        $context['attr_class'], 
                        $context['attr_local_alias']
                    ],
                'data-uid' => uniqid($prefix_uniqid, true)
            ])
    );

    
     if( $statement ){
        $st_label = _core_dphpforms_build_tag(
                $dom, "label", new DOMAttributeList()
            );
        $st_label->nodeValue = $statement;
        $div->appendChild( $st_label );
    }

    $name_checkbox = ( 
        $opt_num > 1 ? 
        $id_formulario_pregunta . '[]' :  
        $id_formulario_pregunta
    );
    
    // Pendiente de pruebas
    $option_pos = array();
    foreach ($options as $key => $_row){
        $option_pos[$key] = $_row->posicion;
    }
    array_multisort($option_pos, SORT_ASC, $options);
    // Fin del pendiente

    foreach( $options as $key => $option ){
        $option = (array) $option;
        
        $option_title = ( array_key_exists('title', $option) ? $opcion['title'] : '' );
        
        $opt_container = _core_dphpforms_build_tag(
                $dom, "div", new DOMAttributeList([
                    'class' => [
                            "checkbox", 
                            $context[ 'attr_checkclass' ] 
                        ],
                    'title' => $option_title
                ])
        );

        $option_attr_checkclass = ( array_key_exists('class', $option) ? $option['class'] : '' );

        if($opt_num == 1){
            $hidden_input = _core_dphpforms_build_tag(
                    $dom, "input", new DOMAttributeList([
                        'type' => "hidden",
                        'name' => $name_checkbox,
                        'value'=> "-1"
                    ])
            );
            $opt_container->appendChild( $hidden_input );
        }
        
        $label = _core_dphpforms_build_tag(
                    $dom, "label", new DOMAttributeList([
                        'class' => [ $option_attr_checkclass ]
                    ])
            );
        
        $label_span = _core_dphpforms_build_tag( $dom, "span", new DOMAttributeList() );
        $label_span->nodeValue = $option['enunciado'];
        
        $checked = ( in_array( $option['valor'], $context['default_value']) ? true : false  );
        
        $opt = _core_dphpforms_build_tag(
                    $dom, "input", new DOMAttributeList([
                        'type' => "checkbox",
                        'class' => [ $context[ 'attr_inputclass' ] ],
                        'name' => $name_checkbox,
                        'value' => $option['valor'],
                        'disabled' => $context[ 'enabled' ],
                        'checked' => $checked
                    ])
            );
        
        $label->appendChild( $opt );
        $label->appendChild( $label_span );
        $opt_container->appendChild( $label );
        
        
        $div->appendChild( $opt_container );
    }   
    
    return $div;
    
}
