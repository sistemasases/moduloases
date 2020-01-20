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

function _dphpforms_generate_TEXTFIELD_LIST(&$dom, $id_formulario_pregunta, $context, $statement, $prefix_uniqid) {

    $block_uuid = uniqid($prefix_uniqid, true);

    $div_attr = new DOMAttributeList([
        'class' => [
            "div-$id_formulario_pregunta",
            "dphpf-list",
            $context['attr_class'],
            $context['attr_local_alias']
        ],
        'data-uid' => $block_uuid
            ]);

    $inner_element_attr = [
        'class' => [$context['attr_inputclass']], // Pendiente
        'disabled' => $context['enabled'],
        'required' => $context['attr_required'],
        'type' => "text"
    ];

    $elements = $context['options']->initial_list;

    if(gettype($context['default_value']) === "array"){
        $elements = $context['default_value'];
    } 

    // Sorting elements

    $elements_pos = array();
    foreach ($elements as $key => $_row) {
        $elements_pos[$key] = $_row->pos;
    }
    array_multisort($elements_pos, SORT_ASC, $elements);



    // General container

    $div = _core_dphpforms_build_tag($dom, "div", $div_attr);

    $statem = _core_dphpforms_build_tag($dom, "label");
    $statem->nodeValue = $statement . ":";

    $div->appendChild($statem);





    //Template

    $template = _core_dphpforms_build_tag($dom, "template", new DOMAttributeList());

    if($context['options']->list_type === FIXED){
        $DOMAttr_elem_container = new DOMAttributeList([
            "class" => ["dphpf-list-element"],
            "style" => "margin-top:10px;"
        ]);
    }elseif($context['options']->list_type === DYNAMIC){
        $DOMAttr_elem_container = new DOMAttributeList([
            "class" => ["dphpf-list-element"],
            "style" => "margin-top:10px;"
        ]);
    }

    $dom_element_attr = new DOMAttributeList($inner_element_attr);
    $elem_container = _core_dphpforms_build_tag($dom, "div", $DOMAttr_elem_container);

    if($context['options']->list_type === "fixed"){
        $label = _core_dphpforms_build_tag($dom, "label", new DOMAttributeList());
        $br = _core_dphpforms_build_tag($dom, "br", new DOMAttributeList());
    }
    $textfield = _core_dphpforms_build_tag($dom, "input", $dom_element_attr);

    if($context['options']->list_type === "fixed"){
        $elem_container->appendChild($label);
        $elem_container->appendChild($br);
    }
    $elem_container->appendChild($textfield);

    $template->appendChild($elem_container);

    $div->appendChild($template);

    
 

    // Elements

    $list_elements = _core_dphpforms_build_tag($dom, "div", new DOMAttributeList([
        "class" => ["dphpf-elements"]
    ]));

    foreach ($elements as $key => $element) {

        $inner_element_attr['name'] = $id_formulario_pregunta . "_TEXTFIELD_LIST_" . $element->id;

        $dom_element_attr = new DOMAttributeList($inner_element_attr);

        $elem_container = _core_dphpforms_build_tag($dom, "div", $DOMAttr_elem_container);

        if($context['options']->list_type === FIXED){
            $label = _core_dphpforms_build_tag($dom, "label", new DOMAttributeList());
            $label->nodeValue = $element->statement . ":";
            $br = _core_dphpforms_build_tag($dom, "br", new DOMAttributeList());
        }

        $textfield = _core_dphpforms_build_tag($dom, "input", $dom_element_attr);

        if($context['options']->list_type === FIXED){
            $elem_container->appendChild($label);
            $elem_container->appendChild($br);
        }
        $elem_container->appendChild($textfield);

        $list_elements->appendChild($elem_container);
    }

    $div->appendChild($list_elements);


    // Dynamic button

    if ($context['options']->list_type === "dynamic") {
        $add_element = _core_dphpforms_build_tag($dom, "input", new DOMAttributeList([
                    'class' => ["dphpf-text-list-add-elem-btn", "dphpf-text-list-btn-$id_formulario_pregunta"],
                    'type' => "button",
                    "value" => "+",
                    'data-uid' => $block_uuid
                        ]));
        $div->appendChild($add_element);
    }

    return $div;
}
