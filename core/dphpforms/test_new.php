<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once( __DIR__ . "/../module_loader.php");
module_loader("dphpforms");

$initial_config = '{
    "allow_register":false,
    "allow_update":true,
    "allow_delete":true,
    "aditional_form_classes" : ["col-xs-12", "col-sm-12", "dphpforms"],
    "initial_values" : [
        {
            "alias" : "lugar",
            "default_value" : "Lugar de prueba"
        },
        {
            "alias" : "objetivos",
            "default_value" : "Objetivos de prueba"
        },{
            "alias" : "id_instancia",
            "default_value" : "450299"
        }
    ],
    "aditional_buttons" : [
        {
            "alias" : "extra_button",
            "text" : "Extra Button",
            "classes" : ["e-class", "e-class-2"]
        }
    ]
}';

$initial_config = json_decode( $initial_config );

echo dphpformsV2_generate_html_recorder( 'seguimiento_pares', "sistemas"  );