<?php

/**
 * @package	block_ases
 * @subpackage	core.dphpforms
 * @author 	Jeison Cardona Gómez
 * @copyright 	(C) 2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once( __DIR__ . "/../DOMTools.php");

function _dphpforms_generate_TEXTFIELD(&$dom, $id_formulario_pregunta, $context, $statement, $prefix_uniqid) {

    $div_attr = new DOMAttributeList([
        'class' => ["div-$id_formulario_pregunta", $context['attr_class'], $context['attr_local_alias']],
        'data-uid' => uniqid($prefix_uniqid, true)
            ]);

    $inner_element_attr = new DOMAttributeList([
        'id' => $id_formulario_pregunta,
        'class' => ["form-control", $context['attr_inputclass']],
        'name' => $id_formulario_pregunta,
        'max' => $context['attr_max'],
        'min' => $context['attr_min'],
        'type' => ( $context['attr_type'] == "" ? "text" : $context['attr_type']),
        'placeholder' => $context['attr_placeholder'],
        'maxlength' => $context['attr_maxlength'],
        'disabled' => $context['enabled'],
        'required' => $context['attr_required'],
        'value' => $context['default_value']
    ]);

    $div = _core_dphpforms_build_tag($dom, "div", $div_attr);
    $input = _core_dphpforms_build_tag($dom, "input", $inner_element_attr);
    $label = _core_dphpforms_build_tag($dom, "label");
    $label->nodeValue = $statement . ":";

    $line_break = _core_dphpforms_build_tag($dom, "br");

    $div->appendChild($label);
    $div->appendChild($line_break);
    $div->appendChild($input);

    return $div;
}
