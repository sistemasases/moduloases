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
 * General Reports
 *
 * @author     Jeison Cardona Gomez
 * @copyright  2017 Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__). '/../../../../config.php');

 global $DB;

 function getPeriodoActual(){
     global $DB;
     $sql_query = 
        "SELECT *
        FROM {talentospilos_semestre}
        ORDER BY fecha_inicio DESC
        LIMIT 1
        ";
    $retornoConsulta = $DB->get_record_sql($sql_query);
    $periodo = get_object_vars($retornoConsulta);
    return array(
        'nombre_periodo' => $periodo['nombre'],
        'fecha_inicio' => $periodo['fecha_inicio'],
        'fecha_fin' => $periodo['fecha_fin']
    );
 }

/**
 * getSeguimientosEstudiante($idEstudiante): Función que obtiene los seguimientos realizados a determinado
 * estudiante.
 *
 * Aspectos importantes: se renombra familiar_desc a familiar, vida_uni a vida_universitaria y vida_uni_riesgo
 * a vida_universitaria_riesgo para conservar el patrón de nombramiento.
 *
 * @param int $idEstudiante Identificación del estudiante al interior
 *        del sistema ASES, no es el id que asigna moodle, ni el 
 *        código institucional.
 * @return array Seguimientos.
 */
 function getSeguimientosEstudiante($idEstudiante, $periodo){
    global $DB;
    $sql_query = 
        "SELECT 
            TPS.id AS id_seguimiento,
            TPS.hora_ini AS hora_ini,
            TPS.hora_fin AS hora_fin,
            TPS.familiar_desc AS familiar,
            TPS.familiar_riesgo AS familiar_riesgo,
            TPS.academico AS academico, 
            TPS.academico_riesgo AS academico_riesgo,
            TPS.economico AS economico,
            TPS.economico_riesgo AS economico_riesgo,
            TPS.vida_uni AS vida_universitaria,
            TPS.vida_uni_riesgo AS vida_universitaria_riesgo,
            TPS.individual AS individual,
            TPS.individual_riesgo AS individual_riesgo,
            TPS.tipo AS tipo,
            TPS.fecha AS fecha_seguimiento
        FROM {talentospilos_seg_estudiante} AS TPSE 
        INNER JOIN {talentospilos_seguimiento} AS TPS 
            ON TPSE.id_seguimiento = TPS.id 
        WHERE 
            (fecha BETWEEN " . strtotime($periodo['fecha_inicio']) . " AND " . strtotime($periodo['fecha_fin']) . ")
            AND (id_estudiante = $idEstudiante)
            AND (tipo = 'PARES')
        ORDER BY TPS.fecha ASC
        ";
    
    // Esto es un arreglo de objetos del tipo stdClass
    $seguimientos = $DB->get_records_sql($sql_query);

    $arraySeguimientos = array();
    foreach ($seguimientos as $key => $seguimiento) { 
        array_push($arraySeguimientos, get_object_vars($seguimiento));
    }
    return $arraySeguimientos;
 }

/**
 * obtenerDatosDimensionesSeguimiento($seguimiento): Función que permite obtener la información
 * del riesgo presente en cada seguimiento, respecto a las dimensiones:
 *
 *  - Familiar
 *  - Academico
 *  - Económico
 *  - Vida Universitaria
 *  - Individual
 *
 * @param array $seguimiento Registro de seguimiento.
 * @return array Arreglo de la forma 
 *              ( 
 *                  'id_seguimiento' => '$id_seguimiento'
 *                   'datos' =>  
 *                          array(
 *                                  'dimension' => 'familiar', 
 *                                  'riesgo' => '$riesgo'
 *                              ),
 *                              (
 *                                  'dimension' => 'academico', 
 *                                  'riesgo' => '$riesgo'
 *                              ),
 *                              (
 *                                  'dimension' => 'economico', 
 *                                  'riesgo' => '$riesgo'
 *                              ),
 *                              (
 *                                  'dimension' => 'vida_universitaria', 
 *                                  'riesgo' => '$riesgo'
 *                              ),
 *                              (
 *                                  'dimension' => 'individual', 
 *                                  'riesgo' => '$riesgo'
 *                              ),
 *                     'fecha' => '$fecha'
 *              )
 */
 function obtenerDatosDimensionSeguimiento($seguimiento, $dimension = 'todas'){

    $fechaFormateada = new DateTime();
    $fechaFormateada->setTimestamp($seguimiento['fecha_seguimiento']);

    // Datos formateados de una o multiples dimensiones, según como se indique
    // en el parámetro de dimensión.
    $datos = array(); 
    if(($dimension == 'familiar')||($dimension == 'todas')){
        array_push(
            $datos, 
            array(
                'dimension' => 'familiar',
                'riesgo' => $seguimiento['familiar_riesgo']
            )
        );
    }

    if(($dimension == 'academico')||($dimension == 'todas')){
        array_push(
            $datos, 
            array(
                'dimension' => 'academico',
                'riesgo' => $seguimiento['academico_riesgo']
            )
        );
    }

    if(($dimension == 'economico')||($dimension == 'todas')){
        array_push(
            $datos, 
            array(
                'dimension' => 'economico',
                'riesgo' => $seguimiento['economico_riesgo']
            )
        );
    }

    if(($dimension == 'vida_universitaria')||($dimension == 'todas')){
        array_push(
            $datos, 
            array(
                'dimension' => 'vida_universitaria',
                'riesgo' => $seguimiento['vida_universitaria_riesgo']
            )
        );
    }

    if(($dimension == 'individual')||($dimension == 'todas')){
        array_push(
            $datos, 
            array(
                'dimension' => 'individual',
                'riesgo' => $seguimiento['individual_riesgo']
            )
        );
    }

    return array(
        'id_seguimiento' => $seguimiento['id_seguimiento'],
        'datos' => $datos,
        'fecha' =>  $fechaFormateada->format('Y-M-d')

    );
 }

 /**
 * obtenerDatosSeguimientoFormateados($idEstudiante): Función que me procesa todos los registros de
 * seguimiento de un estudiante, respecto a una dimensión específica. 
 *
 * @param int $idEstudiante Identificación del estudiante.
 * @param string $dimension Filtra la dimensión a consultarse, el valor de 'todas', trae el conjunto de seguimiento 
 *               de todas las dimensiones.
 * @param array $periodo Arreglo con el intervalo de tiempo a consultar, array(fecha_nicio=>'yyyy-mm-dd hh:mm:ss',fecha_fin=>'yyyy-mm-dd hh:mm:ss')
 * @return array Datos formateados listos para graficarse 
 *
 */
 function obtenerDatosSeguimientoFormateados($idEstudiante, $dimension = 'todas', $periodo){
    $seguimientos = getSeguimientosEstudiante($idEstudiante, $periodo);
    $datosSeguimientoFormateados = array();
    foreach($seguimientos as $key => $seguimiento){
        $seguimientoFormateado = obtenerDatosDimensionSeguimiento($seguimiento, $dimension);
        $riesgoSeguimiento = $seguimientoFormateado['datos'][0]['riesgo'];
        if($riesgoSeguimiento > '0'){
            $color = null;
            if($riesgoSeguimiento == '1'){
                $color = 'green';
            }elseif ($riesgoSeguimiento == '2') {
                $color = 'orange';
            }elseif ($riesgoSeguimiento == '3') {
                $color = 'red';
            }
            $seguimientoFormateado['datos'][0] = array_merge($seguimientoFormateado['datos'][0], array('color' => $color));
            array_push($datosSeguimientoFormateados, $seguimientoFormateado);
        }
    }
    return $datosSeguimientoFormateados;
 }


?>