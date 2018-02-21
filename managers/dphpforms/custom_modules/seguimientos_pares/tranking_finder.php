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


    /*
    
        require_once '../managers/dphpforms/dphpforms_forms_core.php';
        require_once '../managers/dphpforms/dphpforms_records_finder.php';
        require_once '../managers/dphpforms/dphpforms_get_record.php';
    
    */

    function get_trackings_peer_student_by_semester($student_id, $semester_id){

        $interval = get_semester_interval($semester_id);
        $fecha_inicio = getdate(strtotime($interval->fecha_inicio));
        $fecha_fin = getdate(strtotime($interval->fecha_fin));
        $ano_semester  = $fecha_inicio['year'];

        $array_peer_trackings_dphpforms = dphpforms_find_records('seguimiento_pares', 'seguimiento_pares_id_estudiante', $student_id, 'DESC');
        $array_peer_trackings_dphpforms = json_decode($array_peer_trackings_dphpforms);
        $array_detail_peer_trackings_dphpforms = array();
        foreach ($array_peer_trackings_dphpforms->results as &$peer_trackings_dphpforms) {
            array_push($array_detail_peer_trackings_dphpforms, json_decode(dphpforms_get_record($peer_trackings_dphpforms->id_registro, 'fecha')));
        }

        $array_tracking_date = array();
        foreach ($array_detail_peer_trackings_dphpforms as &$peer_tracking) {
            foreach ($peer_tracking->record->campos as &$tracking) {
                if ($tracking->local_alias == 'fecha') {
                    array_push($array_tracking_date, strtotime($tracking->respuesta));
                }
            }
        }

        rsort($array_tracking_date);

        $seguimientos_ordenados = new stdClass();
        $seguimientos_ordenados->index = array();
        //Inicio de ordenamiento
        $periodo_actual = [];
        for($l = $fecha_inicio['mon']; $l <= $fecha_fin['mon']; $l++ ){
            array_push($periodo_actual, $l);
        }
        for ($x = 0; $x < count($array_tracking_date); $x++) {
            $string_date = $array_tracking_date[$x];
            $array_tracking_date[$x] = getdate($array_tracking_date[$x]);
            if (property_exists($seguimientos_ordenados, $array_tracking_date[$x]['year'])) {
                if (in_array($array_tracking_date[$x]['mon'], $periodo_actual)) {
                    for ($y = 0; $y < count($array_detail_peer_trackings_dphpforms); $y++) {
                        if ($array_detail_peer_trackings_dphpforms[$y]) {
                            foreach ($array_detail_peer_trackings_dphpforms[$y]->record->campos as &$tracking) {
                                if ($tracking->local_alias == 'fecha') {
                                    if (strtotime($tracking->respuesta) == $string_date) {
                                        array_push($seguimientos_ordenados->$array_tracking_date[$x]['year']->periodo, $array_detail_peer_trackings_dphpforms[$y]);
                                        $array_detail_peer_trackings_dphpforms[$y] = null;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                } 

            } else {
                array_push($seguimientos_ordenados->index, $array_tracking_date[$x]['year']);
                $seguimientos_ordenados->$array_tracking_date[$x]['year']->year = $array_tracking_date[$x]['year'];
                $seguimientos_ordenados->$array_tracking_date[$x]['year']->periodo = array();

                $seguimientos_ordenados->$array_tracking_date[$x]['year']->year = $array_tracking_date[$x]['year'];
                if(in_array($array_tracking_date[$x]['mon'], $periodo_actual)){
                    
                    for($y = 0; $y < count($array_detail_peer_trackings_dphpforms); $y++){
                        if($array_detail_peer_trackings_dphpforms[$y]){
                            foreach ($array_detail_peer_trackings_dphpforms[$y]->record->campos as &$tracking) {
                                if ($tracking->local_alias == 'fecha') {
                                    if (strtotime($tracking->respuesta) == $string_date) {
                                        array_push($seguimientos_ordenados->$array_tracking_date[$x]['year']->periodo, $array_detail_peer_trackings_dphpforms[$y]);
                                        $array_detail_peer_trackings_dphpforms[$y] = null;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $seguimientos_array = json_decode(json_encode($seguimientos_ordenados), true);
        $array_periodos = array();
        for ($x = 0; $x < count($seguimientos_array['index']); $x++) {
            array_push($array_periodos, $seguimientos_array[$seguimientos_array['index'][$x]]);
        }

        return $array_periodos;

    }

    function get_all_trackings( $student_id ){

        $array_peer_trackings_dphpforms = dphpforms_find_records('seguimiento_pares', 'seguimiento_pares_id_estudiante', $student_code, 'DESC');
        $array_peer_trackings_dphpforms = json_decode($array_peer_trackings_dphpforms);
        $array_detail_peer_trackings_dphpforms = array();
        foreach ($array_peer_trackings_dphpforms->results as &$peer_trackings_dphpforms) {
            array_push($array_detail_peer_trackings_dphpforms, json_decode(dphpforms_get_record($peer_trackings_dphpforms->id_registro, 'fecha')));
        }

        $array_tracking_date = array();
        foreach ($array_detail_peer_trackings_dphpforms as &$peer_tracking) {
            foreach ($peer_tracking->record->campos as &$tracking) {
                if ($tracking->local_alias == 'fecha') {
                    array_push($array_tracking_date, strtotime($tracking->respuesta));
                }
            }
        }

        rsort($array_tracking_date);

        $seguimientos_ordenados = new stdClass();
        $seguimientos_ordenados->index = array();
        //Inicio de ordenamiento
        $periodo_a = [1, 2, 3, 4, 5, 6, 7];
        //periodo_b es el resto de meses;
        for ($x = 0; $x < count($array_tracking_date); $x++) {
            $string_date = $array_tracking_date[$x];
            $array_tracking_date[$x] = getdate($array_tracking_date[$x]);
            if (property_exists($seguimientos_ordenados, $array_tracking_date[$x]['year'])) {
                if (in_array($array_tracking_date[$x]['mon'], $periodo_a)) {
                    for ($y = 0; $y < count($array_detail_peer_trackings_dphpforms); $y++) {
                        if ($array_detail_peer_trackings_dphpforms[$y]) {
                            foreach ($array_detail_peer_trackings_dphpforms[$y]->record->campos as &$tracking) {
                                if ($tracking->local_alias == 'fecha') {
                                    if (strtotime($tracking->respuesta) == $string_date) {
                                        array_push($seguimientos_ordenados->$array_tracking_date[$x]['year']->per_a, $array_detail_peer_trackings_dphpforms[$y]);
                                        $array_detail_peer_trackings_dphpforms[$y] = null;
                                        break;
                                    }

                                }
                            }
                        }
                    }
                } else {
                    for ($y = 0; $y < count($array_detail_peer_trackings_dphpforms); $y++) {
                        if ($array_detail_peer_trackings_dphpforms[$y]) {
                            foreach ($array_detail_peer_trackings_dphpforms[$y]->record->campos as &$tracking) {
                                if ($tracking->local_alias == 'fecha') {
                                    if (strtotime($tracking->respuesta) == $string_date) {
                                        array_push($seguimientos_ordenados->$array_tracking_date[$x]['year']->per_b, $array_detail_peer_trackings_dphpforms[$y]);
                                        $array_detail_peer_trackings_dphpforms[$y] = null;
                                        break;
                                    }

                                }
                            }
                        }
                    }
                }
            } else {
                array_push($seguimientos_ordenados->index, $array_tracking_date[$x]['year']);
                $seguimientos_ordenados->$array_tracking_date[$x]['year']->year = $array_tracking_date[$x]['year'];
                $seguimientos_ordenados->$array_tracking_date[$x]['year']->per_a = array();
                $seguimientos_ordenados->$array_tracking_date[$x]['year']->per_b = array();

                $seguimientos_ordenados->$array_tracking_date[$x]['year']->year = $array_tracking_date[$x]['year'];
                if (in_array($array_tracking_date[$x]['mon'], $periodo_a)) {

                    for ($y = 0; $y < count($array_detail_peer_trackings_dphpforms); $y++) {
                        if ($array_detail_peer_trackings_dphpforms[$y]) {
                            foreach ($array_detail_peer_trackings_dphpforms[$y]->record->campos as &$tracking) {
                                if ($tracking->local_alias == 'fecha') {
                                    if (strtotime($tracking->respuesta) == $string_date) {
                                        array_push($seguimientos_ordenados->$array_tracking_date[$x]['year']->per_a, $array_detail_peer_trackings_dphpforms[$y]);
                                        $array_detail_peer_trackings_dphpforms[$y] = null;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                } else {

                    for ($y = 0; $y < count($array_detail_peer_trackings_dphpforms); $y++) {
                        if ($array_detail_peer_trackings_dphpforms[$y]) {
                            foreach ($array_detail_peer_trackings_dphpforms[$y]->record->campos as &$tracking) {
                                if ($tracking->local_alias == 'fecha') {
                                    if (strtotime($tracking->respuesta) == $string_date) {
                                        array_push($seguimientos_ordenados->$array_tracking_date[$x]['year']->per_b, $array_detail_peer_trackings_dphpforms[$y]);
                                        $array_detail_peer_trackings_dphpforms[$y] = null;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        //Fin de ordenamiento

        $seguimientos_array = json_decode(json_encode($seguimientos_ordenados), true);
        $array_periodos = array();

        for ($x = 0; $x < count($seguimientos_array['index']); $x++) {
            array_push($array_periodos, $seguimientos_array[$seguimientos_array['index'][$x]]);
        }

        $peer_tracking_v2 = array(
            'index' => $seguimientos_array['index'],
            'periodos' => $array_periodos,
        );   
        
        return $peer_tracking_v2;

    }

?>