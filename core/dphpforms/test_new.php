<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once( __DIR__ . "/../module_loader.php");
module_loader("dphpforms");

/*global $DB;

$query = "CREATE TABLE abd(
            ID serial NOT NULL PRIMARY KEY,
            info json NOT NULL
        )";

$DB->execute($query);


die();*/

$initial_config = '{
    "allow_register":true,
    "allow_update":true,
    "allow_delete":true,
    "allow_reset":true,
    "aditional_form_classes" : ["ases-col-xs-12", "ases-col-sm-12", "dphpforms"],
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

echo _dphpforms_generate_html_recorder( 'seguimiento_pares', "sistemas", $initial_config, false  );