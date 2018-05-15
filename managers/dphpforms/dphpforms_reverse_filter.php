<?php 
    
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Dynamic PHP Forms
 *
 * @author     Jeison Cardona Gómez
 * @package    block_ases
 * @copyright  2018 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs

    require_once(dirname(__FILE__). '/../../../../config.php');
    
    // El filtro con respuesta casteada no es completo.
    //Criteria
    /*{
        "criteria":[
            {
                "operator":"AND",
                "value":"XYZ"
            },
            {
                "operator":"OR",
                "value":"XXY"
            }
        ]
    }*/

    $test_criteria = json_decode( 
        '{
            "criteria":[
                {
                    "operator":"AND",
                    "value":"XYZ"
                },
                {
                    "operator":"OR",
                    "value":"XXY"
                }
            ]
        }' 
    );

    dphpforms_reverse_filter( "25", "none", $test_criteria );

    function dphpforms_reverse_filter($id_pregunta, $cast_to, $criteria){
        global $DB;
        if( $cast_to == "none" ){

        };
        $sql_criteria = "";
        foreach( $criteria->criteria as $key => $criteria_element ){
            if( $key == 0 ){
                $sql_criteria .= "respuesta = '" . $criteria_element->value . "'";
            }else{
                $sql_criteria .= " " . $criteria_element->operator ." respuesta = '" . $criteria_element->value . "'";
            };
        };
        $sql="SELECT *, NULLIF('respuesta','')::string FROM {talentospilos_df_respuesta} WHERE id_pregunta = '". $id_pregunta ."' AND " . $sql_criteria;
        print_r( $sql );
    };

?>