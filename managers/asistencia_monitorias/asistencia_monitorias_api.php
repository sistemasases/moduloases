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
 * Ases block
 *
 * @author     Joan Sebastian Betancourt Arias
 * @package    block_ases
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__). '/../../../../config.php');
require_once('../asistencia_monitorias/asistencia_monitorias_lib.php');
$input = json_decode(file_get_contents("php://input"));

if(isset($input->function) && isset($input->params)) {

    $function = $input->function;
    if($function == 'cargar_materias'){
            $result = cargar_materias();
                if($result){
                    echo json_encode(
                        array(
                            "status_code" => 0,
                            "message" => "Success",
                            "data_response" => $result
                        )
                    );
                }else{
                    echo json_encode(
                        array(
                            "status_code" => 0,
                            "message" => "Results may be empty",
                            "data_response" => $result
                        )
                    );
                }
    }else if($function == 'cargar_monitores'){
        $result = cargar_monitores($input->params[0]);
            if($result){
                echo json_encode(
                    array(
                        "status_code" => 0,
                        "message" => "Success",
                        "data_response" => $result
                    )
                );
            }else{
                echo json_encode(
                    array(
                        "status_code" => 0,
                        "message" => "Results may be empty",
                        "data_response" => $result
                    )
                );
            }
            
} else if($function == 'anadir_monitoria') {
        $params = $input->params;
        if(count($params) == 6) {
            // dia, hora, materia, monitor, programar, hasta
            $dia = $params[0];
            $hora = $params[1];
            $materia = $params[2];
            $monitor_id = $params[3];
            $programar = $params[4];
            $programar_hasta = $params[5];

            if($dia >= 0 && $dia <= 6 && is_string($hora)) {

                $result = anadir_monitoria($dia, $hora, $materia, $monitor_id, $programar, $programar_hasta);

                if($result){
                    echo json_encode(
                        array(
                            "status_code" => 0,
                            "message" => "Éxito",
                            "data_response" => $result
                        )
                    );
                } else {
                    return_with_code(-5);
                }
            } else {
                return_with_code(-2);
            }
        } else {
            return_with_code(-6);
        }
    }  else if($function == 'anadir_asistente_a_sesion_de_monitoria') {
        $params = $input->params;
        if(count($params) == 4) {
            // id_sesion, id_asistente, asignatura_a_consultar, tematica_a_consultar
            $sesion = $params[0];
            $asistente = $params[1];
            $asignatura_a_consultar = $params[2];
            $tematica_a_consultar = $params[3];

            $result = anadir_asistente_a_sesion_de_monitoria($sesion, $asistente, $asignatura_a_consultar, $tematica_a_consultar);

                if($result){
                    echo json_encode(
                        array(
                            "status_code" => 0,
                            "message" => "Éxito",
                            "data_response" => $result
                        )
                    );
                } else {
                    return_with_code(-5);
                }
        } else {
            return_with_code(-6);
        }
    } else if($function == 'eliminar_asistencia') {
        $id = intval($input->params);
            if(is_int($id)) {

                $result = eliminar_asistencia($id);

                if($result){
                    echo json_encode(
                        array(
                            "status_code" => 0,
                            "message" => "Éxito",
                        )
                    );
                } else {
                    return_with_code(-5);
                }
            } else {
                return_with_code(-2);
            }
    } else if($function == 'modificar_monitoria') {
        $params = $input->params;
        if(count($params) == 5) {
            // dia, hora, materia, monitor, id monitoria
            $dia = $params[0];
            $hora = $params[1];
            $materia = $params[2];
            $monitor_id = $params[3];
            $id_monitoria = $params[4];

            if($dia >= 0 && $dia <= 6 && is_string($hora)) {

                $result = modificar_monitoria($dia, $hora, $materia, $monitor_id, $id_monitoria);

                if($result){
                    echo json_encode(
                        array(
                            "status_code" => 0,
                            "message" => "Éxito",
                            "data_response" => $result
                        )
                    );
                } else {
                    return_with_code(-5);
                }
            } else {
                return_with_code(-2);
            }
        } else {
            return_with_code(-6);
        }
    } else if($function == 'get_tabla_sesiones') {
        $params = $input->params;
        if(count($params) == 3) {
            $id = $params[0];
            $desde = $params[1];
            $hasta = $params[2];

            $result = get_tabla_sesiones($id, $desde, $hasta);

                if($result){
                    echo json_encode(
                        array(
                            "status_code" => 0,
                            "message" => "Éxito",
                            "data_response" => $result
                        )
                    );
                } else {
                    return_with_code(-5);
                }
        } else {
            return_with_code(-6);
        }
    } else if($function == 'programar_sesiones') {
        $params = $input->params;
        if(count($params) == 4) {
            $id = $params[0];
            $dia = $params[1];
            $desde = $params[2];
            $hasta = $params[3];

            $result = programar_sesiones($id, $dia, date_create_from_format("Ymd",formatear_fecha_legible_a_int($desde)), formatear_fecha_legible_a_int($hasta));

                if($result){
                    echo json_encode(
                        array(
                            "status_code" => 0,
                            "message" => "Éxito",
                            "data_response" => $result
                        )
                    );
                } else {
                    return_with_code(-5);
                }
        } else {
            return_with_code(-6);
        }
    } else if($function == 'cargar_monitorias'){
        $params = $input->params;
        if(count($params) == 1) {
            echo json_encode(get_reporte_by_id($params[0]));
        }
        if($result){
            echo json_encode(
                array(
                    "status_code" => 0,
                    "message" => "Success",
                    "data_response" => $result
                )
            );
        }
    } else if($function == 'eliminar_monitoria') {
        $id = intval($input->params);
            if(is_int($id)) {

                $result = eliminar_monitoria($id);

                if($result){
                    echo json_encode(
                        array(
                            "status_code" => 0,
                            "message" => "Éxito",
                        )
                    );
                } else {
                    return_with_code(-5);
                }
            } else {
                return_with_code(-2);
            }
    } else if($function == 'get_proxima_sesion_de_monitoria') {
        $id = intval($input->params);
            if(is_int($id)) {

                $result = get_proxima_sesion_de_monitoria($id);

                if($result){
                    echo json_encode(
                        array(
                            "status_code" => 0,
                            "message" => $result
                        )
                    );
                } else {
                    return_with_code(-5);
                }
            } else {
                return_with_code(-2);
            }
    } else if($function == 'eliminar_sesion') {
        $id = intval($input->params);
            if(is_int($id)) {

                $result = eliminar_sesion($id);

                if($result){
                    echo json_encode(
                        array(
                            "status_code" => 0,
                            "message" => "Éxito",
                        )
                    );
                } else {
                    return_with_code(-5);
                }
            } else {
                return_with_code(-2);
            }
    }  else if($function == 'modificar_celular_de_usuario') {
        $params = $input->params;
        if(count($params) == 2) {
            // id, numero de celular
            $id = intval($params[0]);
            $celular = $params[1];

            if(is_int($id)) {

                $result = modificar_celular_de_usuario($id, $celular);

                if($result){
                    echo json_encode(
                        array(
                            "status_code" => 0,
                            "message" => "Éxito",
                            "data_response" => $result
                        )
                    );
                } else {
                    return_with_code(-5);
                }
            } else {
                return_with_code(-2);
            }
        } else {
            return_with_code(-6);
        }
    }else if($function == 'anadir_materia') {
        $params = $input->params;
        if(count($params) == 1) {
            // materia
            $materia = $params[0];

            if(is_string($materia)) {

                $result = anadir_materia($materia);

                if($result){
                    echo json_encode(
                        array(
                            "status_code" => 0,
                            "message" => "Éxito",
                            "id" => $result
                        )
                    );
                } else {
                    return_with_code(-5);
                }
            } else {
                return_with_code(-2);
            }
        } else {
            return_with_code(-6);
        }
    } else if($function == 'eliminar_materia') {
        $params = $input->params;
        if(count($params) == 1) {
            // id
            $id = $params[0];

            if(is_int($id)) {

                $result = eliminar_materia($id);

                if($result){
                    echo json_encode(
                        array(
                            "status_code" => 0,
                            "message" => "Éxito",
                        )
                    );
                } else {
                    return_with_code(-5);
                }
            } else {
                return_with_code(-2);
            }
        } else {
            return_with_code(-6);
        }
    } else if($function == 'cargar_grupos'){
        $result = cargar_grupos($input->params[0]);
        $grupo_seleccionado = cargar_grupo_seleccionado();
            if($result){
                echo json_encode(
                    array(
                        "status_code" => 0,
                        "message" => "Success",
                        "data_response" => $result,
                        "seleccionado" => $grupo_seleccionado
                    )
                );
            }else{
                echo json_encode(
                    array(
                        "status_code" => 0,
                        "message" => "Results may be empty",
                        "data_response" => $result,
                        
                    )
                );
            }
            
} else if($function == 'actualizar_config') {
    $params = $input->params;
    if(count($params) == 1) {
        $grupo = $params[0];

        if(is_int($grupo)) {

            $result = actualizar_config($grupo);

            if($result){
                echo json_encode(
                    array(
                        "status_code" => 0,
                        "message" => "Éxito",
                    )
                );
            } else {
                return_with_code(-5);
            }
        } else {
            return_with_code(-2);
        }
    } else {
        return_with_code(-6);
    }
}else {
        return_with_code(-4);
    }
} else {
    return_with_code(-1);
}

/**
 * @method return_with_code
 * Returns a message with the code of the error.
 * reserved codes: -1, -2, -3, -4, -5, -6, -99.
 * @param $code
 */
function return_with_code($code){

    switch( $code ){

        case -1:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "Error en el servidor.",
                    "data_response" => ""
                )
            );
            break;

        case -2:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "Error in the scheme.",
                    "data_response" => ""
                )
            );
            break;

        case -3:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "Invalid values in the parameters.",
                    "data_response" => ""
                )
            );
            break;

        case -4:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "Function not defined.",
                    "data_response" => ""
                )
            );
            break;

        case -5:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "Duplicate.",
                    "data_response" => ""
                )
            );
            break;

        case -6:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "Wrong quantity of parameters in input.",
                    "data_response" => ""
                )
            );
            break;

        case -99:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "critical error.",
                    "data_response" => ""
                )
            );
            break;
    }
    die();
}