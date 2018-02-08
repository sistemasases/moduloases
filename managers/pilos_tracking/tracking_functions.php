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
 * Estrategia ASES
 *
 * @author     Isabella Serna Ramírez
 * @package    block_ases
 * @copyright  2017 Isabella Serna Ramírez <isabella.serna@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once ('pilos_tracking_lib.php');



/**
 * Does all management to get a final organized by monitor students array
 * 
 * @see monitorUser($pares, $grupal, $codigoMonitor, $noMonitor, $instanceid, $role, $fechas, $sistemas = false, $codigoPracticante = null)
 * @param &$pares --> 'seguimiento de pares' information
 * @param &$grupal --> 'seguimiento grupal' (groupal tracks) information
 * @param $codigoMonitor --> monitor id
 * @param $noMonitor --> monitor number
 * @param $instanceid --> instance id
 * @param $role --> monitor role
 * @param $fechas --> dates interval
 * @param $sistemas = false --> role is not a 'sistemas' one
 * @param $codigoPracticante = null --> practicant id is null
 * @return array with students grouped by monitor
 *
 */
function get_peer_trackings_by_monitor($pares, $grupal, $codigoMonitor, $noMonitor, $instanceid, $role, $fechas, $sistemas = false, $codigoPracticante = null)
{
    $fecha_epoch = [];
    $fecha_epoch[0] = strtotime($fechas[0]);
    $fecha_epoch[1] = strtotime($fechas[1]);
    $semestre_periodo = get_current_semester_byinterval($fechas[0], $fechas[1]);
    $monitorstudents = get_seguimientos_monitor($codigoMonitor, $instanceid, $fecha_epoch, $semestre_periodo);


    return $monitorstudents;

}










////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////

function replace_content_inside_delimiters($start, $end, $new, $source)
{
    return preg_replace('#(' . preg_quote($start) . ')(.*?)(' . preg_quote($end) . ')#si', '$1' . $new . '$3', $source);
}

/** Función que recorta el Toogle a mostrar deacuerdo a los permisos del usuario
* Cuts the shown toogle according to an user licence
* @see show_according_permissions(&$table,$actions)
* @param $table --> Toogle
* @param $actions --> user permission (licence)
* @return array --> toogle
*/

function show_according_permissions(&$table, $actions)
{
    $end = '</div>';
    $replace_with = "";
    $tabla_format = "";
    if (isset($actions->update_assigned_tracking_rt) == 0) {
        $start = '<div class="col-sm-8" id="editar_registro">';
        $table = replace_content_inside_delimiters($start, $end, $replace_with, $table);
    }

    if (isset($actions->delete_assigned_tracking_rt) == 0) {
        $start = '<div class="col-sm-2" id="borrar_registro">';
        $table = replace_content_inside_delimiters($start, $end, $replace_with, $table);
    }

    if (isset($actions->send_observations_rt) == 0) {
        $start = '<div class="col-sm-12" id="enviar_correo">';
        $table = replace_content_inside_delimiters($start, $end, $replace_with, $table);
    }

    if (isset($actions->check_tracking_professional_rt) == 0) {
        $start = '<div class="col-sm-6" id="check_profesional">';
        $table = replace_content_inside_delimiters($start, $end, $replace_with, $table);
    }

    if (isset($actions->check_tracking_intern_rt) == 0) {
        $start = '<div class="col-sm-6" id="check_practicante">';
        $table = replace_content_inside_delimiters($start, $end, $replace_with, $table);
    }



    return $table;
}

/*
* 'Seguimiento pilos' functions which update on view
*/

// ******************************************************************************************************
// ******************************************************************************************************
// ******************************************************************************************************
// SEPARATE BY SEMESTERS METHODS
// ******************************************************************************************************
// ******************************************************************************************************
// ******************************************************************************************************

/**
 * Evaluates tracking existence
 * @see has_tracking($seguimientos)
 * @param $seguimientos ---> html string
 * @return string html table
 *
 */

function has_tracking($seguimientos)
{
    $table = "";
    if ($seguimientos == "") {
        $table.= "<p class='text-center'><strong>No existen seguimientos en el periodo seleccionado</strong></p>";
    }
    else {
        $table.= $seguimientos;
    }

    return $table;
}

/**
 * Gets a select organized by existent periods
 * @see get_period_select($periods)
 * @param $periods ---> existent periods
 * @return string html table
 *
 */

function get_period_select($periods)
{
    $table = "";
    $table.= '<div class="container"><form class="form-inline">';
    $table.= '<div class="form-group"><label for="persona">Periodo</label><select class="form-control" id="periodos">';
    foreach($periods as $period) {
        $table.= '<option value="' . $period->id . '">' . $period->nombre . '</option>';
    }

    $table.= '</select></div>';
    return $table;
}

/**
 * Gets a select organized by users role '_ps'
 * @see get_people_select($people)
 * @param $people ---> existent users
 * @return string html table
 *
 */

function get_people_select($people)
{
    $table = "";
    $table.= '<div class="form-group"><label for="persona">Persona</label><select class="form-control" id="personas">';
    foreach($people as $person) {
        $table.= '<option value="' . $person->id_usuario . '">' . $person->username . " - " . $person->firstname . " " . $person->lastname . '</option>';
    }

    $table.= '</select></div>';
    $table.= '<span class="btn btn-info" id="consultar_persona" type="button">Consultar</span></form></div>';
    return $table;
}


/**
 * Transforms the returned array into a new one who will be used to create a Toogle
 * 
 * @see transformarConsultaSemestreArray($pares, $grupal, $arregloSemestres, $instanceid, $role)
 * @param $pares --> 'Seguimientos de pares'
 * @param $grupal --> Groupal tracks ('seguimiento grupales')
 * @param $arregloSemestres --> Array containing information of current semester
 * @param $instanceid --> instance id
 * @param $role --> profesional role
 * @return array of arrays which every array contains information of every 'practicante' and current semester
 *
 */

function transformarConsultaSemestreArray($pares, $grupal, $arregloSemestres, $instanceid, $role)
{
    $arregloSemestreYPersonas = [];
    foreach($arregloSemestres as $semestre) {
        $arregloAuxiliar = [];
        array_push($arregloAuxiliar, $semestre->id);
        array_push($arregloAuxiliar, $semestre->nombre);
        array_push($arregloAuxiliar, $semestre->fecha_inicio);
        array_push($arregloAuxiliar, $semestre->fecha_fin);

        // An HTML text containing information of the 'profesional' user is added on this position

        array_push($arregloAuxiliar, profesionalUser($pares, $grupal, $arregloPracticantes[$practicante][0], $instanceid, $role));
        array_push($arregloSemestreYPersonas, $arregloAuxiliar);
    }

    return $arregloSemestreYPersonas;
}

// ******************************************************************************************************
// ******************************************************************************************************
// ******************************************************************************************************
// 'PROFESIONAL' METHODS
// ******************************************************************************************************
// ******************************************************************************************************
// ******************************************************************************************************


/**
 * Counts how many tracks a practincant has cheked
 * @see get_conteo_profesional($professionalpracticants)
 * @param $professionalpracticants --> Array information of each practicant for each profesioal 
 * @return string with the count of tracks checked
 *
 */
function get_conteo_profesional($professionalpracticants)
{
    $revisado_profesional = 0;
    $no_revisado_profesional = 0;
    $total = 0;
    $enunciado = '';
    for ($profesional = 0; $profesional < count($professionalpracticants); $profesional++) {
        $revisado_profesional+= $professionalpracticants[$profesional][2];
        $no_revisado_profesional+= $professionalpracticants[$profesional][3];
        $total+= $professionalpracticants[$profesional][4];
    }


    return $enunciado;
}

/**
 * Auxiliar function to create a Toogle and table for a profesional
 * @see profesionalUser(&$pares, &$grupal, $id_prof, $instanceid, $rol, $semester, $sistemas = false)
 * @param &$pares --> 'seguimiento de pares' information
 * @param &$grupal --> 'seguimiento grupal' (groupal tracks) information
 * @param $id_prof --> profesional id
 * @param $instanceid --> instance id
 * @param $rol --> profesional role
 * @param $semester --> semester id
 * @param $sistemas = false --> role is not a 'sistemas' one
 * @return array with the information to create a Toogle and table for a profesional
 *
 */
function profesionalUser(&$pares, &$grupal, $id_prof, $instanceid, $rol, $semester, $sistemas = false)
{
    $arregloPracticanteYMonitor = [];
    $fechas = [];
    $fechas[0] = $semester[0];
    $fechas[1] = $semester[1];
    $fechas[2] = $semester[2];
    $professionalpracticants = get_practicantes_profesional($id_prof, $instanceid, $semester[2]);
    $conteo_profesional = get_conteo_profesional($professionalpracticants);
    $arregloPracticanteYMonitor = transformarConsultaProfesionalArray($pares, $grupal, $professionalpracticants, $instanceid, $rol, $fechas, $sistemas);
    return crearTablaYToggleProfesional($arregloPracticanteYMonitor, $conteo_profesional);
}

/**
 * Transforms the returned array into a new one who will be used to create a Toogle
 * @see transformarConsultaProfesionalArray($pares, $grupal, $arregloPracticantes, $instanceid, $role, $fechas_epoch, $sistemas)
 * @param $pares --> 'Seguimiento pares' information
 * @param $grupal --> 'seguimiento grupal' (groupal tracks) information
 * @param $arregloPracticantes --> Array containing every practicant information
 * @param $instanceid --> instance id
 * @param $role --> practicant role
 * @param $fechas_epoch --> date intervals
 * @param $sistemas --> boolean of 'sistemas' role
 * @return array with the information to create a Toogle
 *
 */

function transformarConsultaProfesionalArray($pares, $grupal, $arregloPracticantes, $instanceid, $role, $fechas_epoch, $sistemas)
{
    $arregloPracticanteYMonitor = [];
    for ($practicante = 0; $practicante < count($arregloPracticantes); $practicante++) {
        $arregloAuxiliar = [];
        array_push($arregloAuxiliar, $arregloPracticantes[$practicante][0]);
        array_push($arregloAuxiliar, $arregloPracticantes[$practicante][1]);

        // se asigna a esta posicion un texto html correspondiente a la informacion del practicante

        array_push($arregloAuxiliar, practicanteUser($pares, $grupal, $arregloPracticantes[$practicante][0], $instanceid, $role, $fechas_epoch, $sistemas));
        array_push($arregloAuxiliar, $arregloPracticantes[$practicante][2]);
        array_push($arregloAuxiliar, $arregloPracticantes[$practicante][3]);
        array_push($arregloAuxiliar, $arregloPracticantes[$practicante][4]);
        array_push($arregloPracticanteYMonitor, $arregloAuxiliar);
    }

    return $arregloPracticanteYMonitor;
}

/**
 * Creates a 'profesional' toogle which contains each assigned practicant
 * @see crearTablaYToggleProfesional($arregloPracticanteYMonitor, $conteo_profesional)
 * @param $arregloPracticanteYMonitor --> Array containing information about each practicant for each profesional
 * @param $conteo_profesional --> amount of checked tracks (seguimientos)
 * @return array with all practicants assgined. Requeired to create a Toogle
 *
 */

function crearTablaYToggleProfesional($arregloPracticanteYMonitor, $conteo_profesional)
{
    $stringRetornar = "";
    $stringRetornar.= $conteo_profesional;
    for ($practicante = 0; $practicante < count($arregloPracticanteYMonitor); $practicante++) {
        $stringRetornar.= '<div class="panel-group"><div class="panel panel-default" ><div class="panel-heading profesional" style="background-color: #938B8B;"><h4 class="panel-title"><a data-toggle="collapse"  href="#collapse' . $arregloPracticanteYMonitor[$practicante][0] . '">' . $arregloPracticanteYMonitor[$practicante][1] . '</a><span>R.P  : <b><label for="revisado_practicante_' . $arregloPracticanteYMonitor[$practicante][0] . '">0</label></b> - NO R.P : <b><label for="norevisado_practicante_' . $arregloPracticanteYMonitor[$practicante][0] . '">0</label></b> - Total  : <b><label for="total_practicante_' . $arregloPracticanteYMonitor[$practicante][0] . '">0</label></b> </span></h4></div>';
        $stringRetornar.= '<div id="collapse' . $arregloPracticanteYMonitor[$practicante][0] . '" class="panel-collapse collapse"><div class="panel-body">';

        // en la tercer posicion del arreglo se encuentra un texto html con un formato especifico

        $stringRetornar.= $arregloPracticanteYMonitor[$practicante][2];
        $stringRetornar.= '</div></div></div></div>';
    }

    return $stringRetornar;
}

// ******************************************************************************************************
// ******************************************************************************************************
// ******************************************************************************************************
// PRACTICANT METHODS
// ******************************************************************************************************
// ******************************************************************************************************
// ******************************************************************************************************

/**
 * Auxiliar function to create a Toogle and table for a practicant
 * @see practicanteUser(&$pares, &$grupal, $id_pract, $instanceid, $rol, $semester, $sistemas = false)
 * @param &$pares --> 'seguimiento de pares' information
 * @param &$grupal --> 'seguimiento grupal' (groupal tracks) information
 * @param $id_pract --> practicant id
 * @param $instanceid --> instance id
 * @param $rol --> practicant role
 * @param $semester --> semester id
 * @param $sistemas = false --> role is not a 'sistemas' one
 * @return array  with the information to create a Toogle and table for a practicant
 *
 */
function practicanteUser(&$pares, &$grupal, $id_pract, $instanceid, $rol, $semester, $sistemas = false)
{
    $arregloMonitorYEstudiantes = [];
    $fechas = [];
    $fechas[0] = $semester[0];
    $fechas[1] = $semester[1];
    $fechas[2] = $semester[2];
    $practicantmonitors = get_monitores_practicante($id_pract, $instanceid, $semester[2]);
    $arregloMonitorYEstudiantes = transformarConsultaPracticanteArray($pares, $grupal, $practicantmonitors, $instanceid, $rol, $id_pract, $fechas, $sistemas);
    return crearTablaYTogglePracticante($arregloMonitorYEstudiantes);
}


/**
 * Transforms the returned array into a new one who will be used to create a Toogle
 * @see transformarConsultaPracticanteArray($pares, $grupal, $arregloMonitores, $instanceid, $role, $id_pract, $fechas_epoch, $sistemas = false)
 * @param $pares --> 'Seguimiento pares' information
 * @param $grupal --> 'seguimiento grupal' (groupal tracks) information
 * @param $arregloMonitores --> Array containing every monitor information
 * @param $instanceid --> instance id
 * @param $role --> practicant role
 * @param $id_pract --> practicant id
 * @param $fechas_epoch --> date intervals
 * @param $sistemas --> boolean of 'sistemas' role
 * @return array with the information to create a Toogle
 *
 */
function transformarConsultaPracticanteArray($pares, $grupal, $arregloMonitores, $instanceid, $role, $id_pract, $fechas_epoch, $sistemas = false)
{
    $arregloMonitorYEstudiantes = [];
    $fechas = [];
    for ($monitor = 0; $monitor < count($arregloMonitores); $monitor++) {
        $arregloAuxiliar = [];
        $cantidad = 0;
        array_push($arregloAuxiliar, $arregloMonitores[$monitor][0]);
        array_push($arregloAuxiliar, $arregloMonitores[$monitor][1]);
        array_push($arregloAuxiliar, monitorUser($pares, $grupal, $arregloMonitores[$monitor][0], $monitor, $instanceid, $role, $fechas_epoch, $sistemas, $id_pract));
        $cantidades = get_cantidad_seguimientos_monitor($arregloMonitores[$monitor][0], $instanceid);
        $revisado_profesional = $cantidades[0]->count;
        $no_revisado_profesional = $cantidades[1]->count;
        $total_registros = $cantidades[2]->count;
        array_push($arregloAuxiliar, $revisado_profesional);
        array_push($arregloAuxiliar, $no_revisado_profesional);
        array_push($arregloAuxiliar, $total_registros);
        array_push($arregloMonitorYEstudiantes, $arregloAuxiliar);
    }

    return $arregloMonitorYEstudiantes;
}


/**
 * Creates a 'practicante' toogle which contains each assigned monitor
 * @see crearTablaYTogglePracticante($arregloMonitorYEstudiantes)
 * @param $arregloMonitorYEstudiantes --> Array containing information about each monitor  for each practicant
 * @return array with all monitors assgined. Requeired to create a Toogle
 *
 */
function crearTablaYTogglePracticante($arregloMonitorYEstudiantes)
{
    $stringRetornar = "";
    $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $nuevo_link=str_replace("report_trackings", "tracking_time_control", $actual_link);

    for ($monitor = 0; $monitor < count($arregloMonitorYEstudiantes); $monitor++) {
        $stringRetornar.= '<div class="panel-group"><div class="panel panel-default" ><div class="panel-heading practicante" style="background-color: #AEA3A3;"><h4 class="panel-title"><a data-toggle="collapse"  href="#collapse' . $arregloMonitorYEstudiantes[$monitor][0] . '">' . $arregloMonitorYEstudiantes[$monitor][1] . '</a><span> R.P  : <b><label for="revisado_monitor_' . $arregloMonitorYEstudiantes[$monitor][0] . '">0</label></b> - NO R.P : <b><label for="norevisado_monitor_' . $arregloMonitorYEstudiantes[$monitor][0] . '">0</label></b> - Total  : <b><label for="total_monitor_' . $arregloMonitorYEstudiantes[$monitor][0] . '">0</label></b>

            <a href="'.$nuevo_link.'&monitorid='.$arregloMonitorYEstudiantes[$monitor][0].'"
        target="_blank"><span class="btn btn-primary btn-xs glyphicon glyphicon-time"></span></a>

             </span></h4></div>';
        $stringRetornar.= '<div id="collapse' . $arregloMonitorYEstudiantes[$monitor][0] . '" class="panel-collapse collapse"><div class="panel-body">';

        // On third position there's a specific HTML format

        $stringRetornar.= $arregloMonitorYEstudiantes[$monitor][2];
        $stringRetornar.= '</div></div></div></div>';
    }

    return $stringRetornar;
}

// ******************************************************************************************************
// ******************************************************************************************************
// ******************************************************************************************************
// MONITOR METHODS
// ******************************************************************************************************
// ******************************************************************************************************
// ******************************************************************************************************

/**
 * Does all management to get a final organized by monitor students array
 * 
 * @see monitorUser($pares, $grupal, $codigoMonitor, $noMonitor, $instanceid, $role, $fechas, $sistemas = false, $codigoPracticante = null)
 * @param &$pares --> 'seguimiento de pares' information
 * @param &$grupal --> 'seguimiento grupal' (groupal tracks) information
 * @param $codigoMonitor --> monitor id
 * @param $noMonitor --> monitor number
 * @param $instanceid --> instance id
 * @param $role --> monitor role
 * @param $fechas --> dates interval
 * @param $sistemas = false --> role is not a 'sistemas' one
 * @param $codigoPracticante = null --> practicant id is null
 * @return array with students grouped by monitor
 *
 */
function monitorUser($pares, $grupal, $codigoMonitor, $noMonitor, $instanceid, $role, $fechas, $sistemas = false, $codigoPracticante = null)
{
    $fecha_epoch = [];
    $fecha_epoch[0] = strtotime($fechas[0]);
    $fecha_epoch[1] = strtotime($fechas[1]);
    $semestre_periodo = get_current_semester_byinterval($fechas[0], $fechas[1]);
    $monitorstudents = get_seguimientos_monitor($codigoMonitor, $instanceid, $fecha_epoch, $semestre_periodo);
    transformarConsultaMonitorArray($monitorstudents, $pares, $grupal, $codigoMonitor, $noMonitor, $instanceid, $role);

    // Group 'seguimiento de pares' information by id 

    $arregloImprimirPares = agrupar_informacion($pares, 20);

    // Group 'seguimientos grupales' information by id 

    $arregloImprimirGrupos = agrupar_informacion($grupal, 12);

    // transforms all 'seguimientos grupales' into just one with same id and link together students names and ids

    $arregloImprimirGrupos = agrupar_Seguimientos_grupales($arregloImprimirGrupos);

    // sort each student track by id

    for ($grupo = 0; $grupo < count($arregloImprimirPares); $grupo++) {
        ordenaPorColumna($arregloImprimirPares[$grupo], 19);
    }

    // Returns toogle information given a monitor 

    return crearTablaYToggle($arregloImprimirPares, $noMonitor, $arregloImprimirGrupos, $codigoMonitor, $codigoPracticante, $role, $sistemas);
}

/**
 * Transforms the returned array into a new one who will be used to create a Toogle
 * @see transformarConsultaMonitorArray($array, &$pares, &$grupal, $codigoMonitor, $noMonitor, $instanceid, $role, $codigoPracticante = null)
 * @param $array --> all tracks a monitor has done
 * @param $pares --> 'Seguimiento pares' information
 * @param $grupal --> 'seguimiento grupal' (groupal tracks) information
 * @param $codigoMonitor --> monitor id
 * @param $noMonitor --> monitor number
 * @param $instanceid --> instance id
 * @param $role --> monitor role
 * @param $codigoPracticante = null --> boolean of 'sistemas' role
 * @return array with the information to create a Toogle
 *
 */
function transformarConsultaMonitorArray($array, &$pares, &$grupal, $codigoMonitor, $noMonitor, $instanceid, $role, $codigoPracticante = null)
{
    foreach($array as $seguimiento) {
        if ($seguimiento->tipo == "PARES") {
            $array_auxiliar = [];
            $fecha = gmdate('M/d/Y', ($seguimiento->fecha));
            $fecha_calendario = new DateTime("@$seguimiento->fecha"); // convert UNIX timestamp to PHP DateTime
            $nombre = $seguimiento->nombre_estudiante;
            $apellido = $seguimiento->apellido_estudiante;
            $profesion = $seguimiento->profesional;
            $practicante = $seguimiento->practicantee;
            $nombre_enviar = "";
            if ($apellido == "" || strlen($apellido) == 0) {
                $nombre_enviar = $nombre;
            }
            else {
                $nombre_enviar = $nombre . " " . $apellido;
            }

            $nombrem = $seguimiento->nombre_monitor_creo;
            $apellidom = $seguimiento->apellido_monitor_creo;
            $nombremon_enviar = "";
            if ($apellidom == "" || strlen($apellidom) == 0) {
                $nombremon_enviar = $nombrem;
            }
            else {
                $nombremon_enviar = $nombrem . " " . $apellidom;
            }

            array_push($array_auxiliar, $nombre_enviar); //0
            array_push($array_auxiliar, $fecha); //1
            array_push($array_auxiliar, $seguimiento->hora_ini); //2
            array_push($array_auxiliar, $seguimiento->hora_fin); //3
            array_push($array_auxiliar, $seguimiento->lugar); //4
            array_push($array_auxiliar, $seguimiento->tema); //5
            array_push($array_auxiliar, $seguimiento->actividades); //6
            array_push($array_auxiliar, $seguimiento->individual); //7
            array_push($array_auxiliar, $seguimiento->individual_riesgo); //8
            array_push($array_auxiliar, $seguimiento->familiar_desc); //9
            array_push($array_auxiliar, $seguimiento->familiar_riesgo); //10
            array_push($array_auxiliar, $seguimiento->academico); //11
            array_push($array_auxiliar, $seguimiento->academico_riesgo); //12
            array_push($array_auxiliar, $seguimiento->economico); //13
            array_push($array_auxiliar, $seguimiento->economico_riesgo); //14
            array_push($array_auxiliar, $seguimiento->vida_uni); //15
            array_push($array_auxiliar, $seguimiento->vida_uni_riesgo); //16
            array_push($array_auxiliar, $seguimiento->observaciones); //17
            array_push($array_auxiliar, "saltar"); //18 borra
            array_push($array_auxiliar, $seguimiento->fecha); // 19
            array_push($array_auxiliar, $seguimiento->id_estudiante); // 20 idtalentos
            array_push($array_auxiliar, $nombremon_enviar); // 21
            array_push($array_auxiliar, $seguimiento->objetivos); // 22
            array_push($array_auxiliar, $seguimiento->id_seguimiento); // 23
            array_push($array_auxiliar, $seguimiento->registros_estudiantes_revisados); // 24
            array_push($array_auxiliar, $seguimiento->registros_estudiantes_norevisados); // 25
            array_push($array_auxiliar, $seguimiento->registros_estudiantes_total); // 26
            array_push($array_auxiliar, $seguimiento->profesional); // 27
            array_push($array_auxiliar, $seguimiento->practicante); // 28
            array_push($array_auxiliar, $fecha_calendario->format('Y-m-d')); //29 calendar date format
            array_push($array_auxiliar, $seguimiento->individual_riesgo); //30 individual risk (Riesgo individual)
            array_push($pares, $array_auxiliar);
        }
        elseif ($seguimiento->tipo == "GRUPAL") {
            $array_auxiliar = [];

            // $fecha = transformarFecha(consulta[registro]["fecha"]);

            $nombre = $seguimiento->nombre_estudiante;
            $apellido = $seguimiento->apellido_estudiante;
            $nombre_enviar = "";
            if ($apellido == "" || strlen($apellido) == 0) {
                $nombre_enviar = $nombre;
            }
            else {
                $nombre_enviar = $nombre . " " . $apellido;
            }

            $nombrem = $seguimiento->nombre_monitor_creo;
            $apellidom = $seguimiento->apellido_monitor_creo;
            $nombremon_enviar = "";
            if ($apellidom == "" || strlen($apellidom) == 0) {
                $nombremon_enviar = $nombrem;
            }
            else {
                $nombremon_enviar = $nombrem . " " . $apellidom;
            }

            array_push($array_auxiliar, $nombre_enviar);
            array_push($array_auxiliar, $fecha);
            array_push($array_auxiliar, $seguimiento->hora_ini);
            array_push($array_auxiliar, $seguimiento->hora_fin);
            array_push($array_auxiliar, $seguimiento->lugar);
            array_push($array_auxiliar, $seguimiento->tema);
            array_push($array_auxiliar, $seguimiento->actividades);
            array_push($array_auxiliar, $seguimiento->objetivos);
            array_push($array_auxiliar, $seguimiento->observaciones);
            array_push($array_auxiliar, "saltar"); //9 delete
            array_push($array_auxiliar, $seguimiento->fecha); // 10
            array_push($array_auxiliar, $seguimiento->id_estudiante); // 11
            array_push($array_auxiliar, $seguimiento->id_seguimiento); // 12
            array_push($array_auxiliar, $nombre_enviar); // 13
            array_push($array_auxiliar, $seguimiento->registros_estudiantes_revisados_grupal); // 14
            array_push($array_auxiliar, $seguimiento->registros_estudiantes_norevisados_grupal); // 15
            array_push($array_auxiliar, $seguimiento->registros_estudiantes_total_grupal); // 16
            array_push($grupal, $array_auxiliar);
        }
    }
}

/**
 * Sorts an array given a column from lower to higher value
 * @see ordenaPorColumna(&$arreglo, $col)
 * @param &$arreglo ---> array to sort
 * @param $col --> column number
 * @return array sorted
 *
 */
function ordenaPorColumna(&$arreglo, $col)
{
    $aux;

    // search through the column

    for ($i = 0; $i < count($arreglo); $i++) {
        for ($j = ($i + 1); $j < count($arreglo); $j++) {

            // Verify if the [i][col] element is greater than [j][col]

            if (intval($arreglo[$i][$col]) < intval($arreglo[$j][$col])) {

                // search through (i, j) selected rows and exchange elements 
                // variable k to control column position through each row

                for ($k = 0; $k < count($arreglo[$i]); $k++) {

                    // exchange rows elements selected column by column

                    $aux = $arreglo[$i][$k];
                    $arreglo[$i][$k] = $arreglo[$j][$k];
                    $arreglo[$j][$k] = $aux;
                }
            }
        }
    }
}


/**
 * Groups'seguimientos grupales' (groupal tracks) by id
 * @see agrupar_Seguimientos_grupales($arreglo)
 * @param $arreglo ---> array containing all tracks
 * @return array with grouped information by id
 *
 */
function agrupar_Seguimientos_grupales($arreglo)
{
    $NuevoArregloGrupal = [];
    for ($elementoRevisar = 0; $elementoRevisar < count($arreglo); $elementoRevisar++) {
        $arregloAuxiliar = $arreglo[$elementoRevisar][0];
        $nombres = "";
        $nombresImpirmir = "";
        $codigos = "";
        $contador = 1;

        // Grabs names and is to create a text to display on table

        for ($tuplaGrupo = 0; $tuplaGrupo < count($arreglo[$elementoRevisar]); $tuplaGrupo++) {
            $cuenta = count($arreglo[$elementoRevisar]) - 1;
            if ($tuplaGrupo == $cuenta) {
                $nombres.= $arreglo[$elementoRevisar][$tuplaGrupo][0];
                $nombresImpirmir.= $arreglo[$elementoRevisar][$tuplaGrupo][0];
                $codigos.= $arreglo[$elementoRevisar][$tuplaGrupo][11];
            }
            else {
                $nombres.= $arreglo[$elementoRevisar][$tuplaGrupo][0];
                $nombresImpirmir.= $arreglo[$elementoRevisar][$tuplaGrupo][0] . ",";
                $codigos.= $arreglo[$elementoRevisar][$tuplaGrupo][11];
            }
        }

        // Names and ids are added into the array to return

        $arregloAuxiliar[0] = $nombres;
        $arregloAuxiliar[11] = $codigos;
        array_push($arregloAuxiliar, $nombresImpirmir);
        array_push($NuevoArregloGrupal, $arregloAuxiliar);
    }

    return $NuevoArregloGrupal;
}


/**
 * Groups all array information given specific parameters in $campoComparar
 * @see agrupar_informacion($infoMonitor, $campoComparar)
 * @param $infoMonitor ---> monitor information
 * @param $campoComparar --> field to compare
 * @return array with grouped information by $campoComparar
 *
 */
function agrupar_informacion($infoMonitor, $campoComparar)
{
    $nuevoArreglo = [];
    for ($i = 0; $i < count($infoMonitor); $i++) {

        // initialize variables

        $confirmarAnanir = "si";
        $posicion = 0;

        // First array element will be added

        if (count($nuevoArreglo) != 0) {

            // Array containing elements

            for ($j = 0; $j < count($nuevoArreglo); $j++) {

                // Verifies other user to has a different name

                if ($infoMonitor[$i][$campoComparar] == $nuevoArreglo[$j][0][$campoComparar]) {

                    // If there has users with same name, it'll be added into a new position

                    $confirmarAnanir = "no";
                    $posicion = $j;
                }
            }
        }

        //Return "si" if there's no student records

        if ($confirmarAnanir == "si") {
            $arregloEstudiante = array();

            // Added to array

            $tamano = count($nuevoArreglo);
            array_push($arregloEstudiante, $infoMonitor[$i]);
            $nuevoArreglo[$tamano] = $arregloEstudiante;
        }
        else {
            $arregloEstudiante = array();
            $arregloEstudiante = $nuevoArreglo[$posicion];
            array_push($arregloEstudiante, $infoMonitor[$i]);

            // Otherwise the record is added into the student

            $nuevoArreglo[$posicion] = [];
            $nuevoArreglo[$posicion] = $arregloEstudiante;
        }
    }

    return $nuevoArreglo;
}

/**
 * Creates a students table who belong to a specified monitor
 * @see crearTablaYToggle($arregloImprimirPares, $monitorNo, $arregloImprimirGrupos, $codigoEnviarN1, $codigoEnviarN2, $rol, $sistemas = false)
 * @param $arregloImprimirPares ---> 'seguimiento de pares' information
 * @param $monitorNo --> monitor number
 * @param $arregloImprimirGrupos --> 'seguimientos grupales' (groupal tracks) information
 * @param $codigoEnviarN1 -->
 * @param $codigoEnviarN2 --> 
 * @param $rol --> student role
 * @param $sistemas = false --> role is not a 'sistemas' one
 * @return string HTML tablaytoogle information
 *
 */
function crearTablaYToggle($arregloImprimirPares, $monitorNo, $arregloImprimirGrupos, $codigoEnviarN1, $codigoEnviarN2, $rol, $sistemas = false)
{
    $stringRetornar = "";

    // search through each student

    for ($student = 0; $student < count($arregloImprimirPares); $student++) {
        $stringRetornar.= '<div class="panel-group"><div class="panel panel-default"><div class="panel-heading pares" style="background-color: #D0C4C4;"><h4 class="panel-title"><a data-toggle="collapse" href="#collapse' . $monitorNo . $arregloImprimirPares[$student][0][20] . '">' . $arregloImprimirPares[$student][0][0] . '<span> R.P  : <b><label for="revisado_pares_' . $codigoEnviarN1 . '_' . $student . '">' . $arregloImprimirPares[$student][0][24] . '</label></b> - NO R.P : <b><label for="norevisado_pares_' . $codigoEnviarN1 . '_' . $student . '">' . $arregloImprimirPares[$student][0][25] . '</label></b> - Total  : <b>' . $arregloImprimirPares[$student][0][26] . '</b> </span></a></h4></div>';
        $stringRetornar.= '<div id="collapse' . $monitorNo . $arregloImprimirPares[$student][0][20] . '" class="panel-collapse collapse"><div class="panel-body">';

        // Creates a Toogle for each track 

        for ($tupla = 0; $tupla < count($arregloImprimirPares[$student]); $tupla++) {
            
            if($arregloImprimirPares[$student][$tupla][27]==0){
                 $stringRetornar.= '<div class="panel-group"><div class="panel panel-default"><div class="panel-heading" style="background-color: #a39999"><h4 class="panel-title"><a data-toggle="collapse" href="#collapse_' . $monitorNo . $arregloImprimirPares[$student][0][20] . $tupla . '"> <label  for="fechatext_' . $arregloImprimirPares[$student][$tupla][23] . '"/ id="fecha_texto_' . $arregloImprimirPares[$student][$tupla][23] . '"> Registro : ' . $arregloImprimirPares[$student][$tupla][1] . '</label></a></h4></div>';
            }else{
                $stringRetornar.= '<div class="panel-group"><div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" href="#collapse_' . $monitorNo . $arregloImprimirPares[$student][0][20] . $tupla . '"> <label  for="fechatext_' . $arregloImprimirPares[$student][$tupla][23] . '"/ id="fecha_texto_' . $arregloImprimirPares[$student][$tupla][23] . '"> Registro : ' . $arregloImprimirPares[$student][$tupla][1] . '</label></a></h4></div>'; 
            }
            

            $stringRetornar.= '<div id="collapse_' . $monitorNo . $arregloImprimirPares[$student][0][20] . $tupla . '" class="panel-collapse collapse"><div class="panel-body hacer-scroll" style="overflow-y"><table class="table table-hover $students_table" id="$students_table' . $arregloImprimirPares[$student][0][20] . $arregloImprimirPares[$student][0][19] . '">';
            $stringRetornar.= '<thead><tr><th></th><th></th><th></th></tr></thead>';
            $stringRetornar.= '<tbody id=' . $tupla . '_' . $arregloImprimirPares[$student][$tupla][23] . '>';
            $stringRetornar.= '<div class="table-info-pilos col-sm-12"><div class="col-sm-4" style="display: none" id="titulo_fecha_' . $arregloImprimirPares[$student][$tupla][23] . '"><b>FECHA :</b><input id="fecha_' . $arregloImprimirPares[$student][$tupla][23] . '" type="date" class="no-borde-fondo fecha"  value="' . $arregloImprimirPares[$student][$tupla][29] . '"/></div></div>';
            $stringRetornar.= '<div class"table-info-pilos col-sm-12"><div class="col-sm-4 "><b>LUGAR:</b> <input id="lugar_' . $arregloImprimirPares[$student][$tupla][23] . '"class="no-borde-fondo editable lugar" readonly value="' . $arregloImprimirPares[$student][$tupla][4] . '"></div><div class="col-md-4" id="hora_inicial_' . $arregloImprimirPares[$student][$tupla][23] . '" style="display: "><label for="h_ini" class="select-hour">HORA INICIO</label><input class="no-borde-fondo fecha" readonly id="h_inicial_texto_' . $arregloImprimirPares[$student][$tupla][23] . '" value="' . $arregloImprimirPares[$student][$tupla][2] . ' "></div><div class="col-md-4" id="mod_hora_ini_' . $arregloImprimirPares[$student][$tupla][23] . '" style="display: none"><label for="h_ini" class="form-control-label col-md-4 col-xs-4">HORA INICIO</label><select  class="select-hour" id="h_ini_' . $arregloImprimirPares[$student][$tupla][23] . '" name="h_ini" ></select><label class="col-md-1 col-xs-1" for="m_ini">:</label><select class="select-hour" id="m_ini_' . $arregloImprimirPares[$student][$tupla][23] . '"  name="m_ini"></select></div><div class="col-md-4" id="hora_final_' . $arregloImprimirPares[$student][$tupla][23] . '" style="display: "><label for="h_ini" class="form-control-label col-md-4 col-xs-4">HORA FIN </label><input class="no-borde-fondo fecha" readonly id="h_final_texto_' . $arregloImprimirPares[$student][$tupla][23] . '" value="' . $arregloImprimirPares[$student][$tupla][3] . '"></div><div class="col-md-4" id="mod_hora_final_' . $arregloImprimirPares[$student][$tupla][23] . '" style="display: none"><label for="h_fin" class="form-control-label col-md-4 col-xs-4">HORA FIN</label><select  class="select-hour" id="h_fin_' . $arregloImprimirPares[$student][$tupla][23] . '" name="h_fin" ></select><label class="col-md-1 col-xs-1" for="m_fin">:</label><select class="select-hour" id="m_fin_' . $arregloImprimirPares[$student][$tupla][23] . '"  name="m_fin"></select></div></div>';
            $stringRetornar.= '<div class="table-info-pilos col-sm-12"><b>TEMA:</b><br /><input id="tema_' . $arregloImprimirPares[$student][$tupla][23] . '" class="no-borde-fondo editable tema" readonly  value="' . $arregloImprimirPares[$student][$tupla][5] . '"></div>';
            $stringRetornar.= '<div class="table-info-pilos col-sm-12"><b>OBJETIVOS:</b><br /><textarea id="objetivos_' . $arregloImprimirPares[$student][$tupla][23] . '" class ="no-borde-fondo editable" readonly>' . $arregloImprimirPares[$student][$tupla][22] . '</textarea></div></div>';
            $riesgo = "";
            $valor = - 1;

            // Depending on risk it will be added to class to identify

            if ($arregloImprimirPares[$student][$tupla][8] == 1) {
                $riesgo = "bajo";
                $valor = 1;
            }
            else
            if ($arregloImprimirPares[$student][$tupla][8] == 2) {
                $riesgo = "medio";
                $valor = 2;
            }
            else
            if ($arregloImprimirPares[$student][$tupla][8] == 3) {
                $riesgo = "alto";
                $valor = 3;
            }
            else {
                $riesgo = "no";
            }

            if ($riesgo != "no") {
                $stringRetornar.= '<div class="table-info-pilos col-sm-12 riesgo_' . $riesgo . '" id="riesgo_individual_' . $arregloImprimirPares[$student][$tupla][23] . '"><b>INDIVIDUAL:</b><br /><textarea id="obindividual_' . $arregloImprimirPares[$student][$tupla][23] . '" class ="no-borde-fondo editable" readonly>' . $arregloImprimirPares[$student][$tupla][7] . '</textarea><br />RIESGO: ' . $riesgo;
                $stringRetornar.= '<div class="col-md-12 radio-ocultar ocultar" id="radio_individual_div' . $arregloImprimirPares[$student][$tupla][23] . '">';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_individual_' . $arregloImprimirPares[$student][$tupla][23] . '"  value="1">Bajo';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_individual_' . $arregloImprimirPares[$student][$tupla][23] . '" value="2">Medio';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_individual_' . $arregloImprimirPares[$student][$tupla][23] . '" value="3">Alto';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
                $stringRetornar.= '</div></div>';
                $stringRetornar.= '</td></tr>';
                $stringRetornar.= '<div class="col-md-12 top-buffer"></div>';
            }
            else
            if ($riesgo == "no") {
                $stringRetornar.= '<div class="table-info-pilos col-sm-12 riesgo_' . $riesgo . ' quitar-ocultar ocultar individual"><b>INDIVIDUAL:</b><br /><textarea id="obindividual_' . $arregloImprimirPares[$student][$tupla][23] . '" class ="no-borde-fondo editable" readonly></textarea><br />RIESGO:No registra';
                $stringRetornar.= '<div class="col-md-12 radio-ocultar ocultar" id="radio_individual_div' . $arregloImprimirPares[$student][$tupla][23] . '">';
                $stringRetornar.= '<label class="radio-inline hidden" >';
                $stringRetornar.= '<input type="radio" name="riesgo_individual_' . $arregloImprimirPares[$student][$tupla][23] . '"  value="0">No registra';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_individual_' . $arregloImprimirPares[$student][$tupla][23] . '"  value="1">Bajo';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_individual_' . $arregloImprimirPares[$student][$tupla][23] . '" value="2">Medio';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_individual_' . $arregloImprimirPares[$student][$tupla][23] . '" value="3">Alto';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
                $stringRetornar.= '</div></div>';
                $stringRetornar.= '</td></tr>';
                $stringRetornar.= '<div class="col-md-12 top-buffer"></div>';
            }

            // Depending on risk it will be added to class to identify

            if ($arregloImprimirPares[$student][$tupla][10] == 1) {
                $riesgo = "bajo";
                $valor = 1;
            }
            else
            if ($arregloImprimirPares[$student][$tupla][10] == 2) {
                $riesgo = "medio";
                $valor = 2;
            }
            else
            if ($arregloImprimirPares[$student][$tupla][10] == 3) {
                $riesgo = "alto";
                $valor = 3;
            }
            else {
                $riesgo = "no";
            }

            if ($riesgo != "no") {
                $stringRetornar.= '<div class="table-info-pilos col-sm-12 riesgo_' . $riesgo . '" id="riesgo_familiar_' . $arregloImprimirPares[$student][$tupla][23] . '"><b>FAMILIAR:</b><br /><textarea id="obfamiliar_' . $arregloImprimirPares[$student][$tupla][23] . '" class ="no-borde-fondo editable" readonly>' . $arregloImprimirPares[$student][$tupla][9] . '</textarea><br />RIESGO: ' . $riesgo;
                $stringRetornar.= '<div class="col-md-12 radio-ocultar ocultar" id="radio_familiar_div' . $arregloImprimirPares[$student][$tupla][23] . '">';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_familiar_' . $arregloImprimirPares[$student][$tupla][23] . '" value="1">Bajo';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_familiar_' . $arregloImprimirPares[$student][$tupla][23] . '" value="2">Medio';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_familiar_' . $arregloImprimirPares[$student][$tupla][23] . '" value="3">Alto';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
                $stringRetornar.= '</div></div>';
                $stringRetornar.= '</td></tr>';
                $stringRetornar.= '<div class=" col-md-12 top-buffer"></div>';
            }
            else
            if ($riesgo == "no") {
                $stringRetornar.= '<div class="table-info-pilos col-sm-12 riesgo_' . $riesgo . ' quitar-ocultar ocultar"><b>FAMILIAR:</b><br /><textarea id="obfamiliar_' . $arregloImprimirPares[$student][$tupla][23] . '" class ="no-borde-fondo editable" readonly></textarea><br />RIESGO:No registra';
                $stringRetornar.= '<div class="col-md-12 radio-ocultar ocultar" id="radio_familiar_div' . $arregloImprimirPares[$student][$tupla][23] . '">';
                $stringRetornar.= '<label class="radio-inline hidden" >';
                $stringRetornar.= '<input type="radio" name="riesgo_familiar_' . $arregloImprimirPares[$student][$tupla][23] . '"  value="0">No registra';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_familiar_' . $arregloImprimirPares[$student][$tupla][23] . '" value="1">Bajo';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_familiar_' . $arregloImprimirPares[$student][$tupla][23] . '" value="2">Medio';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_familiar_' . $arregloImprimirPares[$student][$tupla][23] . '" value="3">Alto';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
                $stringRetornar.= '</div></div>';
                $stringRetornar.= '</td></tr>';
                $stringRetornar.= '<div class="col-md-12 top-buffer"></div>';
            }

            // Depending on risk it will be added to class to identify

            if ($arregloImprimirPares[$student][$tupla][12] == 1) {
                $riesgo = "bajo";
                $valor = 1;
            }
            else
            if ($arregloImprimirPares[$student][$tupla][12] == 2) {
                $riesgo = "medio";
                $valor = 2;
            }
            else
            if ($arregloImprimirPares[$student][$tupla][12] == 3) {
                $riesgo = "alto";
                $valor = 3;
            }
            else {
                $riesgo = "no";
            }

            if ($riesgo != "no") {
                $stringRetornar.= '<div class="table-info-pilos col-sm-12 riesgo_' . $riesgo . '" id="riesgo_academico_' . $arregloImprimirPares[$student][$tupla][23] . '"><b>ACADEMICO:</b><br /><textarea id="obacademico_' . $arregloImprimirPares[$student][$tupla][23] . '" class ="no-borde-fondo editable" readonly>' . $arregloImprimirPares[$student][$tupla][11] . '</textarea><br />RIESGO: ' . $riesgo;
                $stringRetornar.= '<div class="col-md-12 radio-ocultar ocultar" id="radio_academico_div' . $arregloImprimirPares[$student][$tupla][23] . '">';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_academico_' . $arregloImprimirPares[$student][$tupla][23] . '" value="1">Bajo';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_academico_' . $arregloImprimirPares[$student][$tupla][23] . '" value="2">Medio';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_academico_' . $arregloImprimirPares[$student][$tupla][23] . '" value="3">Alto';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
 
                $stringRetornar.= '</div></div>';
                $stringRetornar.= '</td></tr>';
                $stringRetornar.= '<div class="col-md-12 top-buffer"></div>';
            }
            else
            if ($riesgo == "no") {

                 $stringRetornar.= '<div class="table-info-pilos col-sm-12 riesgo_' . $riesgo . ' quitar-ocultar ocultar"><b>ACADEMICO:</b><br /><textarea id="obacademico_' . $arregloImprimirPares[$student][$tupla][23] . '" class ="no-borde-fondo editable" readonly></textarea><br />RIESGO:No registra';
                $stringRetornar.= '<div class="col-md-12 radio-ocultar ocultar" id="radio_academico_div' . $arregloImprimirPares[$student][$tupla][23] . '">';
                $stringRetornar.= '<label class="radio-inline hidden" >';
                $stringRetornar.= '<input type="radio" name="riesgo_academico_' . $arregloImprimirPares[$student][$tupla][23] . '"  value="0">No registra';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_academico_' . $arregloImprimirPares[$student][$tupla][23] . '" value="1">Bajo';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_academico_' . $arregloImprimirPares[$student][$tupla][23] . '" value="2">Medio';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_academico_' . $arregloImprimirPares[$student][$tupla][23] . '" value="3">Alto';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
                $stringRetornar.= '</div>';
                $stringRetornar.= '</td></tr>';
                $stringRetornar.= '<div class=" col-md-12 top-buffer"></div></div>';



            }

            // Depending on risk it will be added to class to identify

            if ($arregloImprimirPares[$student][$tupla][14] == 1) {
                $riesgo = "bajo";
                $valor = 1;
            }
            else
            if ($arregloImprimirPares[$student][$tupla][14] == 2) {
                $riesgo = "medio";
                $valor = 2;
            }
            else
            if ($arregloImprimirPares[$student][$tupla][14] == 3) {
                $riesgo = "alto";
                $valor = 3;
            }
            else {
                $riesgo = "no";
            }

            if ($riesgo != "no") {
                $stringRetornar.= '<div class="table-info-pilos col-sm-12 riesgo_' . $riesgo . '" id="riesgo_economico_' . $arregloImprimirPares[$student][$tupla][23] . '"><b>ECONOMICO:</b><br /><textarea id="obeconomico_' . $arregloImprimirPares[$student][$tupla][23] . '" class ="no-borde-fondo editable" readonly>' . $arregloImprimirPares[$student][$tupla][13] . '</textarea><br />RIESGO: ' . $riesgo;
                $stringRetornar.= '<div class="col-md-12 radio-ocultar ocultar" id="radio_economico_div' . $arregloImprimirPares[$student][$tupla][23] . '">';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_economico_' . $arregloImprimirPares[$student][$tupla][23] . '" value="1">Bajo';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_economico_' . $arregloImprimirPares[$student][$tupla][23] . '" value="2">Medio';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_economico_' . $arregloImprimirPares[$student][$tupla][23] . '" value="3">Alto';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
                $stringRetornar.= '</div></div>';
                $stringRetornar.= '</td></tr>';
                $stringRetornar.= '<div class="col-md-12  top-buffer"></div>';
            }
            else
            if ($riesgo == "no") {
                $stringRetornar.= '<div class="table-info-pilos col-sm-12 riesgo_' . riesgo . ' quitar-ocultar ocultar"><b>ECONOMICO:</b><br /><textarea id="obeconomico_' . $arregloImprimirPares[$student][$tupla][23] . '" class ="no-borde-fondo editable" readonly></textarea><br />RIESGO:No registra';
                $stringRetornar.= '<div class="col-md-12 radio-ocultar ocultar" id="radio_economico_div' . $arregloImprimirPares[$student][$tupla][23] . '">';
                $stringRetornar.= '<label class="radio-inline hidden" >';
                $stringRetornar.= '<input type="radio" name="riesgo_economico_' . $arregloImprimirPares[$student][$tupla][23] . '"  value="0">No registra';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_economico_' . $arregloImprimirPares[$student][$tupla][23] . '" value="1">Bajo';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_economico_' . $arregloImprimirPares[$student][$tupla][23] . '" value="2">Medio';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_economico_' . $arregloImprimirPares[$student][$tupla][23] . '" value="3">Alto';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
                $stringRetornar.= '</div></div>';
                $stringRetornar.= '</td></tr>';
                $stringRetornar.= '<div class="col-md-12 top-buffer"></div>';
            }

            // Depending on risk it will be added to class to identify

            if ($arregloImprimirPares[$student][$tupla][16] == 1) {
                $riesgo = "bajo";
                $valor = 1;
            }
            else
            if ($arregloImprimirPares[$student][$tupla][16] == 2) {
                $riesgo = "medio";
                $valor = 2;
            }
            else
            if ($arregloImprimirPares[$student][$tupla][16] == 3) {
                $riesgo = "alto";
                $valor = 3;
            }
            else {
                $riesgo = "no";
            }

            if ($riesgo != "no") {
                $stringRetornar.= '<div class="table-info-pilos col-sm-12 riesgo_' . $riesgo . '" id="riesgo_universitario_' . $arregloImprimirPares[$student][$tupla][23] . '"><b>UNIVERSITARIO:</b><br /><textarea id="obuniversitario_' . $arregloImprimirPares[$student][$tupla][23] . '" class ="no-borde-fondo editable" readonly>' . $arregloImprimirPares[$student][$tupla][15] . '</textarea><br />RIESGO: ' . $riesgo;
                $stringRetornar.= '<div class="col-md-12 radio-ocultar ocultar" id="radio_universitario_div' . $arregloImprimirPares[$student][$tupla][23] . '">';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_universitario_' . $arregloImprimirPares[$student][$tupla][23] . '" value="1">Bajo';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_universitario_' . $arregloImprimirPares[$student][$tupla][23] . '" value="2">Medio';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_universitario_' . $arregloImprimirPares[$student][$tupla][23] . '" value="3">Alto';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
                $stringRetornar.= '</div></div>';
                $stringRetornar.= '</td></tr>';
                $stringRetornar.= '<div class="col-md-12 top-buffer"></div>';
            }
            else
            if ($riesgo == "no") {
                $stringRetornar.= '<div class="table-info-pilos col-sm-12 riesgo_' . riesgo . ' quitar-ocultar ocultar"><b>UNIVERSITARIO:</b><br /><textarea id="obuniversitario_' . $arregloImprimirPares[$student][$tupla][23] . '" class ="no-borde-fondo editable" readonly></textarea><br />RIESGO:No registra';
                $stringRetornar.= '<div class="col-md-12 radio-ocultar ocultar" id="radio_universitario_div' . $arregloImprimirPares[$student][$tupla][23] . '">';
                $stringRetornar.= '<label class="radio-inline hidden" >';
                $stringRetornar.= '<input type="radio" name="riesgo_universitario_' . $arregloImprimirPares[$student][$tupla][23] . '"  value="0">No registra';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_universitario_' . $arregloImprimirPares[$student][$tupla][23] . '" value="1">Bajo';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_universitario_' . $arregloImprimirPares[$student][$tupla][23] . '" value="2">Medio';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" >';
                $stringRetornar.= '<input type="radio" name="riesgo_universitario_' . $arregloImprimirPares[$student][$tupla][23] . '" value="3">Alto';
                $stringRetornar.= '</label>';
                $stringRetornar.= '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
                $stringRetornar.= '</div></div>';
                $stringRetornar.= '</td></tr>';
                $stringRetornar.= '<div class="col-md-12 top-buffer"></div>';
            }

            $stringRetornar.= '<div class="table-info-pilos col-sm-12"><b>OBSERVACIONES:</b><br /><textarea id="observacionesGeneral_' . $arregloImprimirPares[$student][$tupla][23] . '" class ="no-borde-fondo editable" readonly>' . $arregloImprimirPares[$student][$tupla][17] . '</textarea></div>';
            $stringRetornar.= '<div class="table-info-pilos col-sm-12"><b>CREADO POR:</b><br />' . $arregloImprimirPares[$student][$tupla][21] . '</div>';
            $stringRetornar.= '<div class="col-sm-12" id="enviar_correo"><div class="table-info-pilos col-sm-12"><b>REPORTAR OBSERVACIÓN</b><br /><textarea  id="textarea_' . $arregloImprimirPares[$student][$tupla][23] . '" class="textarea-seguimiento-pilos" name="individual_' . $codigoEnviarN1 . '_' . $codigoEnviarN2 . '_' . $arregloImprimirPares[$student][$tupla][1] . '_' . $arregloImprimirPares[$student][$tupla][0] . '" rows="4" cols="150"></textarea><br /></div>';
            if ($arregloImprimirPares[$student][$tupla][27] == 1) {
                $stringRetornar.= '
                <div class="col-sm-6" id="profesional">
                <div class="col-sm-12"><label class="checkbox-inline"><input type="checkbox" name="profesional"  id="profesional_' . $arregloImprimirPares[$student][$tupla][23] . '" value="1" checked disabled>R. profesional</label><label class="checkbox-inline"></div></div>';
            }
            else {
                $stringRetornar.= '
                <div class="col-sm-6" id="check_profesional">
                <div class="col-sm-12"><label class="checkbox-inline"><input type="checkbox" name="profesional" id="profesional_' . $arregloImprimirPares[$student][$tupla][23] . '" value="1" disabled>R. profesional</label><label class="checkbox-inline"></div></div>';
            }

            if ($arregloImprimirPares[$student][$tupla][28] == 1) {
                $stringRetornar.= '
                <div class="col-sm-6" id="check_practicante">
                <input type="checkbox" name="practicante" id="practicante_' . $arregloImprimirPares[$student][$tupla][23] . '" value="1" checked disabled>R. practicante</label></div></div>';
            }
            else {
                $stringRetornar.= '
                <div class="col-sm-6" id="check_practicante">
                <input type="checkbox" name="practicante" id="practicante_' . $arregloImprimirPares[$student][$tupla][23] . '" value="1" disabled>R. practicante</label></div></div>';
            }

            if ($arregloImprimirPares[$student][$tupla][27] != 1 or $sistemas == 1) {
                $stringRetornar.= '
                        <div class="col-sm-12" id="enviar_correo"><div class="col-sm-4 col" id="enviar_' . $arregloImprimirPares[$student][$tupla][23] . '" style="display: "><span class="btn btn-info btn-lg  botonCorreo" value="' . $arregloImprimirPares[$student][$tupla][23] . '" id="correo_' . $arregloImprimirPares[$student][$tupla][23] . '" type="button">Enviar observaciones</span></div></div>

                        <div class="col-sm-8" id="editar_registro">
                        <div class="col-sm-4" id="editar_' . $arregloImprimirPares[$student][$tupla][23] . '" style="display:"><span class="btn btn-info btn-lg botonesSeguimiento botonEditarSeguimiento" value="' . $arregloImprimirPares[$student][$tupla][23] . '" type="button">Editar</span></div></div>

                        <div class="col-sm-2" id="borrar_registro">
                        <div class="col-sm-4" id="borrar_' . $arregloImprimirPares[$student][$tupla][23] . '" style="display:"><span class="btn btn-info btn-lg botonBorrar"  value="' . $arregloImprimirPares[$student][$tupla][23] . '" type="button">Borrar</span></div></div>';
                $stringRetornar.= '<div class="col-sm-12"><div class="col-sm-5"><span class="btn btn-info btn-lg botonesSeguimiento botonModificarSeguimiento ocultar" value="' . $arregloImprimirPares[$student][$tupla][23] . '" type="button">Guardar</span></div><div class="col-sm-5"><span class="btn btn-info btn-lg botonesSeguimiento botonCancelarSeguimiento ocultar" value="' . $arregloImprimirPares[$student][$tupla][23] . '" type="button">Cancelar</span></div></div><td></tr>';
            }

            // close all collapsables

            $stringRetornar.= '</tbody></table></div></div></div></div>';
        }

        $stringRetornar.= '</div></div></div></div>';
    }

    // In case of groupal tracks 'Seguimientos grupales'

    if (count($arregloImprimirGrupos) != 0) {
        $stringRetornar.= '<div class="panel-group"><div class="panel panel-default"><div class="panel-heading grupal" style="background-color: #D0C4C4;"><h4 class="panel-title"><a data-toggle="collapse" href="#collapsegroup' . $monitorNo . $arregloImprimirGrupos[0][11] . '">SEGUIMIENTOS GRUPALES <span> R.P  : <b><label for="revisado_grupal_' . $codigoEnviarN1 . '">' . $arregloImprimirGrupos[0][14] . '</label></b> - NO R.P : <b><label for="norevisado_grupal_' . $codigoEnviarN1 . '">' . $arregloImprimirGrupos[0][15] . '</label></b> - Total  : <b><label for="total_grupal_' . $codigoEnviarN1 . '">' . $arregloImprimirGrupos[0][16] . '</b> </span></a></h4></div>';
        $stringRetornar.= '<div id="collapsegroup' . $monitorNo . $arregloImprimirGrupos[0][11] . '" class="panel-collapse collapse"><div class="panel-body">';
        for ($grupo = 0; $grupo < count($arregloImprimirGrupos); $grupo++) {
            $stringRetornar.= '<div class="panel-group"><div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" href="#collapsegroup' . $monitorNo . $grupo . $$arregloImprimirGrupos[$grupo][11] . '">' . $arregloImprimirGrupos[$grupo][1] . '</a></h4></div>';
            $stringRetornar.= '<div id="collapsegroup' . $monitorNo . $grupo . $$arregloImprimirGrupos[$grupo][11] . '" class="panel-collapse collapse"><div class="panel-body"><table class="table table-hover" id="grouptable">';
            $stringRetornar.= '<thead><tr><th></th><th></th><th></th></tr></thead>';
            $stringRetornar.= '<tbody id=' . $grupo . '_' . $arregloImprimirGrupos[$grupo][12] . '>';
            $stringRetornar.= '<tr><td>' . $arregloImprimirGrupos[$grupo][1] . '</td>';
            $stringRetornar.= '<td>LUGAR: ' . $arregloImprimirGrupos[$grupo][4] . '</td>';
            $stringRetornar.= '<td>HORA: ' . $arregloImprimirGrupos[$grupo][2] . '-' . $arregloImprimirGrupos[$grupo][3] . '</td></tr>';
            $stringRetornar.= '<tr><td colspan="3"><b>ESTUDIANTES:</b><br /> ' . $arregloImprimirGrupos[$grupo][17] . '</td></tr>';
            $stringRetornar.= '<tr><td colspan="3"><b>TEMA:</b><br /> ' . $arregloImprimirGrupos[$grupo][5] . '</td></tr>';
            $stringRetornar.= '<tr><td colspan="3"><b>ACTIVIDADES GRUPALES:</b><br /> ' . $arregloImprimirGrupos[$grupo][6] . '</td></tr>';
            $stringRetornar.= '<tr><td colspan="3"><b>OBSERVACIONES:</b><br />' . $arregloImprimirGrupos[$grupo][7] . '</td></tr>';
            $stringRetornar.= '<tr><td colspan="3"><b>CREADO POR:</b><br />' . $arregloImprimirGrupos[$grupo][13] . '</td></tr>';
            $stringRetornar.= '
                    <div class="col-sm-12" id="enviar_correo">
                    <tr>
                    <td colspan="3"><b>REPORTAR OBSERVACIÓN</b><br /><textarea id="grupal_' . $codigoEnviarN1 . '_' . $codigoEnviarN2 . '_' . $arregloImprimirGrupos[$grupo][1] . '_' . $arregloImprimirGrupos[$grupo][14] . '" rows="4" cols="150"></textarea><br /><br /><span class="btn btn-info btn-lg botonCorreo" value="' . $arregloImprimirPares[$student][$tupla][23] . '" type="button">Enviar observaciones</span><td></tr></div>';


            // If role is OK a field and a button will be added to send messages to both monitor and profesional

            $stringRetornar.= '</tbody></table></div></div></div></div>';
        }

        $stringRetornar.= '</div></div></div></div>';
    }

    $globalArregloPares = [];
    $globalArregloGrupal = [];
    return $stringRetornar;
}

?>