<?php

/**
 * @package	block_ases
 * @subpackage	core.dphpforms
 * @author 	Jeison Cardona Gómez
 * @copyright 	(C) 2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once( __DIR__ . "/../DOMTools.php");

const DYNAMIC = "dynamic";
const FIXED = "fixed";

function _dphpforms_generate_TABLE(&$dom, $id_formulario_pregunta, $context, $statement, $prefix_uniqid) {

    $block_uuid = uniqid($prefix_uniqid, true);

    $div_attr = new DOMAttributeList([
        'class' => [
            "div-$id_formulario_pregunta",
            "dphpf-table",
            $context['attr_class'],
            $context['attr_local_alias']
        ],
        'data-uid' => $block_uuid
    ]);

    $elements = $context['options']->initial_list;
    $header = $context['options']->header;
    
    // General container

    $div = _core_dphpforms_build_tag($dom, "div", $div_attr);

    $statem = _core_dphpforms_build_tag($dom, "label");
    $statem->nodeValue = $statement . ":";

    $div->appendChild($statem);
    
    // End general container
    
    // Elements

    $list_elements = _core_dphpforms_build_tag($dom, "div", new DOMAttributeList([
        "class" => ["dphpf-elements"]
    ]));
    
    // -- Headers
    
    $hrow = _core_dphpforms_build_tag($dom, "div", new DOMAttributeList([
                "class" => ["dphpf-table-row"]
    ]));
    
    $width = 100.0 / count( $header );

    foreach ($header as $h_key => $hd) {

        $hcol = _core_dphpforms_build_tag($dom, "div", new DOMAttributeList([
                    "class" => ["dphpf-table-col"],
                    "style" => "width: $width%; float: left;"
        ]));

        $c_span = _core_dphpforms_build_tag($dom, "span", new DOMAttributeList([
                    "class" => ["dphpf-table-header"],
                    "style" => "width: 100%;",
        ]));
        
        $c_span->nodeValue = $hd;
        $hcol->appendChild($c_span);
        $hrow->appendChild($hcol);
        
    }
    
    $list_elements->appendChild($hrow);

    // -- End headers

    foreach ($elements as $r_key => $row) {
        
        $drow = _core_dphpforms_build_tag($dom, "div", new DOMAttributeList([
            "class" => ["dphpf-table-row"]
        ]));
        
        $width = 100.0 / count( $row );
        
        foreach ($row as $c_key => $col) {

            $dcol = _core_dphpforms_build_tag($dom, "div", new DOMAttributeList([
                "class" => ["dphpf-table-col"],
                "style" => "width: $width%; float: left;"
            ]));
            
            $c_input = _core_dphpforms_build_tag($dom, "input", new DOMAttributeList([
                "class" => ["dphpf-table-input"],
                "style" => "width: 100%;",
                "value" => $col
            ]));
            
            $dcol->appendChild($c_input);
            
            $drow->appendChild($dcol);
            
        }
        
        $list_elements->appendChild($drow);
        
    }

    $div->appendChild($list_elements);
    
    // End elements
    
    // Dynamic button

    if ($context['options']->table_type === "dynamic") {
        $add_element = _core_dphpforms_build_tag(
                $dom,
                "input",
                new DOMAttributeList([
                    'class' => ["dphpf-text-table-add-row-btn", "dphpf-table-btn-$id_formulario_pregunta"],
                    'type' => "button",
                    "value" => "+",
                    'data-uid' => $block_uuid
                        ]
                )
        );
        $div->appendChild($add_element);
    }

    // End dynamic button

   

    return $div;
}
