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
        },
            {
                "alias" : "hora_inicio",
                "default_value" : "11:00"
            },
            {
                "alias" : "hora_finalizacion",
                "default_value" : "13:00"
            },
            {
                "alias" : "fecha",
                "default_value" : "2019-11-20"
            },
            {
                "alias" : "acciones",
                "default_value" : [
                    "c_acciones_monitor_apoyo_academico", 
                    "c_acciones_monitor_apoyo_reconocimiento_ciudad_universidad"
                ]
            },
            {
                "alias" : "list_abc_fixed",
                "default_value" : [
                    {
                        "statement" : "9999999-9999 - Firstname Lastname",
                        "value" : "9999999-9999",
                        "id" : "first_element",
                        "pos" : 0
                    }, 
                    {
                        "statement" : "0000000-0000 - Firstname Lastname",
                        "value" : "0000000-0000",
                        "id" : "second_element",
                        "pos" : 1
                    }
                ]
            },
            {
                "alias" : "id_creado_por",
                "default_value" : 999
            },
            {
                "alias" : "id_estudiante",
                "default_value" : 999
            }
        
    ],
    "aditional_buttons" : [
        {
            "alias" : "extra_button",
            "text" : "Extra Button",
            "classes" : ["e-class", "e-class-2"]
        }
    ],
    "action" : "http://192.168.1.100/moodle366/blocks/ases/managers/dphpforms/procesador.php"
}';

$initial_config = json_decode( $initial_config );

//echo _dphpforms_generate_html_recorder( 'seguimiento_pares', "sistemas", $initial_config, false  );

echo _dphpforms_generate_html_updater( 50115, "sistemas", false  );
