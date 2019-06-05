<?php

require_once(dirname(__FILE__). '/../../../config.php');
global $DB;

       //    // *****************************************************************************************//
        // //  Actualización: Se actualiza JSON SCHEMA ficha de discapacidad                    //
        // //  Versión: 2019052413060                                                                  //
        // // *****************************************************************************************//

        $new_schema = new stdClass();
        $new_schema->json_schema = json_encode('{
      "type": "string",
   		"enum": [
              "NO REGISTRA",
              "Masculino",
              "Femenino",
              "Intersexual"
          ]
        }');
        $new_schema->alias  = 'sex_type_schema';

        if($DB->insert_record('talentospilos_json_schema', $new_schema)){
            echo "Éxito";
        }

        ?>