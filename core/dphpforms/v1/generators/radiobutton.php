<?php

/**
 * @package	block_ases
 * @subpackage	core.dphpforms
 * @author 	Jeison Cardona Gómez
 * @copyright 	(C) 2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once( __DIR__ . "/../DOMTools.php");

function _dphpforms_generate_RADIOBUTTON(&$dom, $id_formulario_pregunta, $context, $statement, $prefix_uniqid) {
    
    $field_attr_required = $context['attr_required'];

    $options = $context['options'];

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

    $inner_hidden_input = _core_dphpforms_build_tag(
            $dom, "input", new DOMAttributeList([
                'name' => $id_formulario_pregunta,
                'value' => "-#$%-",
                'type' => "hidden"
                    ])
    );

    /**
     * Se utiliza para controlar el registro de una sola
     * condición de required para el primer radio.
     * */
    $required_temporal = $field_attr_required;

    $opt_radio_container = _core_dphpforms_build_tag(
            $dom, "div", new DOMAttributeList([
                'class' => ['opcionesRadio', $context['attr_group_radio_class']],
                'style' => "margin-bottom:0.4em"
                    ])
    );

    // Pendiente de pruebas
    $option_pos = array();
    foreach ($options as $key => $_row) {
        $option_pos[$key] = $_row->posicion;
    }
    array_multisort($option_pos, SORT_ASC, $options);
    // Fin del pendiente

    foreach ($options as $key => $option) {
        $option = (array) $option;

        $option_title = '';
        if (array_key_exists('title', $opcion)) {
            $option_title = $opcion['title'];
        }

        $opt = _core_dphpforms_build_tag(
                $dom, "div", new DOMAttributeList([
                    'id' => $id_formulario_pregunta,
                    'name' => $id_formulario_pregunta,
                    'class' => ['radio', $context['attr_radioclass']],
                    'title' => $option_title,
                        ])
        );

        $opt_label = _core_dphpforms_build_tag($dom, "label");

        $opt_label_span = _core_dphpforms_build_tag($dom, "span");
        $opt_label_span->nodeValue = $option['enunciado'];

        $opt_label_input = _core_dphpforms_build_tag(
                $dom, "input", new DOMAttributeList([
                    'type' => 'radio',
                    'class' => [$context['attr_inputclass']],
                    'name' => $id_formulario_pregunta,
                    'value' => $option['valor'],
                    'disabled' => $context['enabled'],
                    'required' => $required_temporal
                        ])
        );

        $opt_label->appendChild($opt_label_input);
        $opt_label->appendChild($opt_label_span);

        $opt->appendChild($opt_label);

        $opt_radio_container->appendChild($opt);

        $required_temporal = false;
    }

    $clean_btn = _core_dphpforms_build_tag(
            $dom, "a", new DOMAttributeList([
                'href' => 'javascript:void(0);',
                'class' => ["limpiar", "btn", "btn-xs", "btn-default"]
                    ])
    );
    $clean_btn->nodeValue = "Limpiar";

    $div->appendChild($inner_hidden_input);

    if ($statement) {
        //$html = $html . '<label>'.$statement.'</label>';
        $label = $dom->createElement('label');
        $label->nodeValue = $statement . ":";

        $line_break = _core_dphpforms_build_tag($dom, "br");

        $div->appendChild($label);
        $div->appendChild($line_break);
        $div->appendChild($inner_hidden_input);
    }

    $div->appendChild($opt_radio_container);
    $div->appendChild($clean_btn);

    return $div;
}
