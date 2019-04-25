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
 * @author     Isabella Serna Ramirez
 * @author     Jeison Cardona Gómez
 * @package    block_ases
 * @copyright  2017 Isabella Serna RamĆ­rez <isabella.serna@correounivalle.edu.co>
 * @copyright  2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once( 'pilos_tracking_lib.php' );
require_once( dirname(__FILE__) . '/../lib/student_lib.php' );
require_once( dirname(__FILE__) . '/../dphpforms/dphpforms_get_record.php' );
require_once( dirname(__FILE__) . '/../student_profile/studentprofile_lib.php' );
require_once( dirname(__FILE__) . '/../seguimiento_grupal/seguimientogrupal_lib.php' );
require_once( dirname(__FILE__) . '/../dphpforms/v2/dphpforms_lib.php' );
require_once( dirname(__FILE__) . '/../monitor_assignments/monitor_assignments_lib.php' );


/**
 * Get the toggle of the monitor with the follow-ups of each student with the implementation of the new form
 *
 * @see render_monitor_new_form($students_by_monitor)
 * @param $student_by_monitor --> students assigned to a monitor
 * @return String
 *
 */

function render_monitor_new_form($students_by_monitor, $period = null)
{
    $panel = "";
    foreach($students_by_monitor as $student) {
        $student_code = get_user_moodle($student->id_estudiante);//Get user moodle by ases id

        $ases_student_code = $student->id_estudiante;
        $current_semester = get_current_semester();
        $fullname = $student_code->firstname . " " .  $student_code->lastname;

        $panel.= "<a data-toggle='collapse' data-container='student$ases_student_code' data-username='$ases_student_code' data-asesid='$ases_student_code' class='student collapsed btn btn-danger btn-univalle btn-card collapsed' data-parent='#accordion_students' style='text-decoration:none' href='#student$ases_student_code'>
                    <div class='panel-heading heading_students_tracking'>
                        <div class='row'>
                            <div class='col-xs-12 col-sm-12 col-md-6 col-lg-6'>
                                <h4 class='panel-title'>
                                    $fullname
                                </h4>
                            </div>
                            <div class='col-xs-12 col-sm-12 col-md-5 col-lg-5' id='counting_$ases_student_code'>
                                <div class='loader'>Cargando conteo...</div>
                            </div>
                            <div class='col-xs-12 col-sm-12 col-md-1 col-lg-1'><span class='open-close-icon glyphicon glyphicon-chevron-left'></span></div>
                        </div>
                    </div>
                 </a>
                 <div id='student$ases_student_code' data-username='$ases_student_code' data-asesid='$ases_student_code'  class='show collapse_v2 collapse border_rt' role='tabpanel' aria-labelledby='headingstudent$ases_student_code' aria-expanded='true'>
                    <div class='panel-body'> </div>
                 </div>";
    }

    return $panel;
}


/**
 * Create group tracking toogle given a monitor_id
 *
 * @see aux_create_groupal_toggle($monitor_id)
 * @param $monitor_id
 * @return String
 *
 */

function aux_create_groupal_toggle($monitor_id)
{
    $panel = "";
    $panel.= "<a data-toggle='collapse' data-container='groupal$monitor_id' class='groupal collapsed btn btn-danger btn-univalle btn-card collapsed' data-parent='#accordion_students' style='text-decoration:none' href='#groupal" . $monitor_id . "'>";
    $panel.= "<div class='panel-heading heading_students_tracking'>";
    $panel.= "<h4 class='panel-title'>";
    $panel.= "SEGUIMIENTOS GRUPALES";
    $panel.= "<span class='open-close-icon glyphicon glyphicon-chevron-left'></span>";
    $panel.= "</h4>"; //End panel-title
    $panel.= "</div>"; //End panel-heading
    $panel.= "</a>";
    $panel.= "<div id='groupal$monitor_id'  class='show collapse_v2 collapse border_rt' role='tabpanel' aria-labelledby='headinggroupal$monitor_id' aria-expanded='true'>";
    $panel.= "<div class='panel-body'>";
    $panel.= "</div>"; // End panel-body
    $panel.= "</div>"; // End collapse
    return $panel;
}

/**
 * Get the toggle of the monitor with the groupal follow-ups of each student with the implementation of the new form
 *
 * @see render_monitor_new_form($students_by_monitor)
 * @param $student_by_monitor --> students assigned to a monitor
 * @return String
 *
 */

function render_groupal_tracks_monitor_new_form($groupal_tracks, $monitor_id, $period = null)
{
    $panel = "";
    foreach($groupal_tracks as $student) {
        $current_semester = get_current_semester();
        if ($period == null) {
            $monitor_trackings = get_tracking_grupal_monitor_current_semester($monitor_id, $current_semester->max);
        }
        else {
            $monitor_trackings = get_tracking_grupal_monitor_current_semester($monitor_id, $period);
        }
    }

    if ( $groupal_tracks ) {
        $panel.= aux_create_groupal_toggle($monitor_id);
    }

    return $panel;
}

/**
 * Get the toggle of the practicant with the trackings of each student that belongs to a certain monitor with the implementation of the new form
 *
 * @see render_practicant_new_form($monitors_of_pract)
 * @param $monitors_of_pract --> monitors of practicants
 * @return String
 *
 */

function render_practicant_new_form($monitors_of_pract, $instance, $period = null)
{
    $panel = "";
    $practicant_counting = [];
    $current_semester = get_current_semester();
    foreach($monitors_of_pract as $monitor) {
        $monitor_id = $monitor->id_usuario;
        $students_by_monitor = get_students_of_monitor($monitor_id, $instance);

        // If the practicant has monitors with students that show

        $panel.= "<a data-toggle='collapse' data-container='monitor$monitor->username' data-username='$monitor->username' class='monitor collapsed btn btn-danger btn-univalle btn-card collapsed' data-parent='#accordion_monitors' style='text-decoration:none' href='#monitor" . $monitor->username . "'>";
        $panel.= "<div class='panel-heading heading_monitors_tracking'>";
        $panel.= "<div class='row'><div class='col-xs-10 col-sm-10 col-md-5 col-lg-5'>";
        $panel.= "<h4 class='panel-title'>";
        $panel.= "$monitor->firstname $monitor->lastname";
        $panel.= "</h4></div>"; //End panel-title
        $panel.= "<div class='col-xs-2 col-sm-2 col-md-1 col-lg-1'>";
        $panel.= "<span class='protected glyphicon glyphicon-user subpanel' style='font-size: 20px;'></span> : " . count(get_students_of_monitor($monitor_id, $instance));
        $panel.= "</div>";
        $panel.= "<div class='col-xs-12 col-sm-12 col-md-5 col-lg-4' id='counting_" . $monitor->username . "'>";
        $panel.= '<div class="loader">Cargando conteo...</div>';
        $panel.= "</div>";
        $panel.= "<div class='col-xs-12 col-sm-12 col-md-1 col-lg-1 col-lg-offset-1'><span class='open-close-icon glyphicon glyphicon-chevron-left'></span></div>";
        $panel.= "</div>";
        $panel.= "</div>"; //End panel-heading
        $panel.= "</a>";
        $panel.= "<div id='monitor$monitor->username' class='show collapse_v2 collapse border_rt' role='tabpanel' aria-labelledby='headingmonitor$monitor->username' aria-expanded='true'>";
        $panel.= "<div class='panel-body'>";
        $panel.= "</div>"; // End panel-body
        $panel.= "</div>"; // End collapse
    }

    return $panel;
}

/**
 * Get the toggle of the practicant with the trackings of each student that belongs to a certain monitor with the implementation of the new form
 *
 * @see render_practicant_new_form($monitors_of_pract)
 * @param $monitors_of_pract --> monitors of practicants
 * @return String
 *
 */

function render_professional_new_form($practicant_of_prof, $instance, $period = null)
{
    $panel = "";
    $practicant_counting = [];
    $current_semester = get_current_semester();
    foreach($practicant_of_prof as $practicant) {
        $panel.= "<div class='panel panel-default'>";
        $practicant_id = $practicant->id_usuario;
        $monitors_of_pract = get_monitors_of_pract($practicant_id, $instance);

        // If the professional has associate practitioners with monitors that show

        $panel.= "<a data-toggle='collapse' data-container='practicant$practicant->username' data-username='$practicant->username' class='practicant collapsed btn btn-danger btn-univalle btn-card collapsed' data-parent='#accordion_practicant' style='text-decoration:none' href='#practicant" . $practicant->username . "'>";
        $panel.= "<div class='panel-heading heading_practicant_tracking'>";
        $panel.= "<div class='row'><div class='col-xs-10 col-sm-10 col-md-5 col-lg-5'>";
        $panel.= "<h4 class='panel-title'>";
        $panel.= "$practicant->firstname $practicant->lastname";
        $panel.= "</h4></div>"; //End panel-title
        $panel.= "<div class='col-xs-2 col-sm-2 col-md-1 col-lg-1'>";
        $panel.= "<span class='protected glyphicon glyphicon-user subpanel' style='font-size: 20px;'></span> : " . count(get_monitors_of_pract($practicant_id, $instance));
        $panel.= "<br /><span class='protected glyphicon glyphicon-education subpanel' style='font-size: 20px;'></span> : " . get_quantity_students_by_pract($practicant_id, $instance);
        $panel.= "</div>";
        $panel.= "<div class='col-xs-12 col-sm-12 col-md-5 col-lg-4' id='counting_" . $practicant->username . "'>";
        $panel.= '<div class="loader">Cargando conteo...</div>';
        $panel.= "</div>";
        $panel.= "<div class='col-xs-12 col-sm-12 col-md-1 col-lg-1 col-lg-offset-1'><span class='open-close-icon glyphicon glyphicon-chevron-left'></span></div>";
        $panel.= "</div>";
        $panel.= "</div>"; //End panel-heading
        $panel.= "</a>";
        $panel.= "<div id='practicant$practicant->username'  class='show collapse_v2 collapse border_rt' role='tabpanel' aria-labelledby='heading_practicant_tracking$practicant->username' aria-expanded='true'>";
        $panel.= "<div class='panel-body'>";
        $panel.= "</div>"; // End panel-body
        $panel.= "</div>"; // End collapse
        $panel.= "</div>"; // End panel-collapse
    }

    return $panel;
}

/**
 * Formatting of array with dates of trackings
 *
 * @see format_dates_trackings($array_peer_trackings_dphpforms)
 * @param array_peer_trackings_dphpforms --> array with trackings of student
 * @return array with formatted dates
 *
 */

function render_student_trackings($peer_tracking_v2){

    $form_rendered = '';
    if ($peer_tracking_v2) {
        foreach($peer_tracking_v2[0] as $key => $period) {
            $year_number = $period;
            foreach($period as $key => $tracking) {
                $is_reviewed = false;
                $rev_pract = false;
                $type = null;
                $icon_rev_pract = '';
                foreach($tracking[record][campos] as $key => $review) {
                    if ($review[local_alias] == 'revisado_profesional') {
                        $type = "ficha";
                        if ($review[respuesta] === "0") {
                            $form_rendered.= '<div id="dphpforms-peer-record-' . $tracking[record][id_registro] . '" class="card-block dphpforms-peer-record peer-tracking-record-review class-'.$tracking[record][alias].'"  data-record-id="' . $tracking[record][id_registro] . '" title="Asistencia"><strong><i class="fas fa-calendar-o"></i> </strong>Registro:   ' . $tracking[record][alias_key][respuesta] . '{{icon_rev_pract}}</div>';
                            $is_reviewed = true;
                        }
                    }elseif ($review[local_alias] == 'in_revisado_profesional') {
                        $type = "inasistencia";
                        if ($review[respuesta] === "0") {
                            $form_rendered.= '<div id="dphpforms-peer-record-' . $tracking[record][id_registro] . '" class="card-block dphpforms-peer-record peer-tracking-record-review class-'.$tracking[record][alias].'"  data-record-id="' . $tracking[record][id_registro] . '" title="Inasistencia"><strong><i class="far fa-calendar-times"></i> </strong>Registro:   ' . $tracking[record][alias_key][respuesta] . '{{icon_rev_pract}}</div>';
                            $is_reviewed = true;
                        }
                    }

                    if ($review[local_alias] == 'revisado_practicante') {
                        if ($review[respuesta] === "0") {
                            $rev_pract = true;
                        }
                    }elseif ($review[local_alias] == 'in_revisado_practicante'){
                        if ($review[respuesta] === "0") {
                            $rev_pract = true;
                        }
                    }
                }

                if ((!$is_reviewed )&& ($type == "ficha")) {
                    $form_rendered.= '<div id="dphpforms-peer-record-' . $tracking[record][id_registro] . '" class="card-block dphpforms-peer-record peer-tracking-record class-'.$tracking[record][alias].'"  data-record-id="' . $tracking[record][id_registro] . '" title="Asistencia"><strong><i class="fas fa-calendar-o"></i> </strong>Registro:   ' . $tracking[record][alias_key][respuesta] . '{{icon_rev_pract}}</div>';
                }elseif((!$is_reviewed) && ($type == "inasistencia")){
                    $form_rendered.= '<div id="dphpforms-peer-record-' . $tracking[record][id_registro] . '" class="card-block dphpforms-peer-record peer-tracking-record class-'.$tracking[record][alias].'"  data-record-id="' . $tracking[record][id_registro] . '" title="Inasistencia"><strong><i class="far fa-calendar-times"></i> </strong>Registro:   ' . $tracking[record][alias_key][respuesta] . '{{icon_rev_pract}}</div>';
                }

                if( $rev_pract ){
                    $form_rendered = str_replace("{{icon_rev_pract}}", '<i title="Revisado practicante" style="float:right" class="fas fa-check"></i>', $form_rendered );
                }else{
                    $form_rendered = str_replace("{{icon_rev_pract}}", '', $form_rendered );
                }

            }
        }
    }

    return $form_rendered;
}

function render_student_trackingsV2($peer_tracking_v2){
    
    $form_rendered = '';
    $special_date_interval = [
        'start' => strtotime( "2019-01-01" ),
        'end' => strtotime( "2019-04-30" )
    ];
    
    if ($peer_tracking_v2) {
        foreach($peer_tracking_v2 as $key => $tracking) {
            
            $is_reviewed = false;
            $rev_pract = false;
            $type = null;
            $icon_rev_pract = '';
            $custom_class = "";

            $_fecha = strtotime( $tracking['fecha'] );

            if( ( $_fecha >= $special_date_interval['start'] ) && ( $_fecha <= $special_date_interval['end'] ) ){
                $custom_class = "special_tracking";
            }

            if ( array_key_exists('revisado_profesional', $tracking) ) {
                $type = "ficha";
                if ($tracking['revisado_profesional'] === "0") {
                    $form_rendered.= '<div id="dphpforms-peer-record-' . $tracking['id_registro'] . '" class="'.$custom_class.' card-block dphpforms-peer-record peer-tracking-record-review class-'.$tracking['alias_form'].'"  data-record-id="' . $tracking['id_registro'] . '" title="Asistencia"><strong><i class="fas fa-calendar-o"></i> </strong>Registro:   ' . $tracking['fecha'] . '{{icon_rev_pract}}</div>';
                    $is_reviewed = true;
                }
            }elseif ( array_key_exists('in_revisado_profesional', $tracking) ) {
                $type = "inasistencia";
                if ($tracking['in_revisado_profesional'] === "0") {
                    $form_rendered.= '<div id="dphpforms-peer-record-' . $tracking['id_registro'] . '" class="'.$custom_class.' card-block dphpforms-peer-record peer-tracking-record-review class-'.$tracking['alias_form'].'"  data-record-id="' . $tracking['id_registro'] . '" title="Inasistencia"><strong><i class="far fa-calendar-times"></i> </strong>Registro:   ' . $tracking['fecha'] . '{{icon_rev_pract}}</div>';
                    $is_reviewed = true;
                }
            }

            if ( array_key_exists('revisado_practicante', $tracking) ) {
                if ( $tracking['revisado_practicante'] === "0") {
                    $rev_pract = true;
                }
            }elseif ( array_key_exists('in_revisado_practicante', $tracking) ){
                if ( $tracking['in_revisado_practicante'] === "0") {
                    $rev_pract = true;
                }
            }

            if ((!$is_reviewed )&& ($type == "ficha")) {
                $form_rendered.= '<div id="dphpforms-peer-record-' . $tracking['id_registro'] . '" class="'.$custom_class.' card-block dphpforms-peer-record peer-tracking-record class-'.$tracking['alias_form'].'"  data-record-id="' . $tracking['id_registro'] . '" title="Asistencia"><strong><i class="fas fa-calendar-o"></i> </strong>Registro:   ' . $tracking['fecha'] . '{{icon_rev_pract}}</div>';
            }elseif((!$is_reviewed) && ($type == "inasistencia")){
                $form_rendered.= '<div id="dphpforms-peer-record-' . $tracking['id_registro'] . '" class="'.$custom_class.' card-block dphpforms-peer-record peer-tracking-record class-'.$tracking['alias_form'].'"  data-record-id="' . $tracking['id_registro'] . '" title="Inasistencia"><strong><i class="far fa-calendar-times"></i> </strong>Registro:   ' . $tracking['fecha'] . '{{icon_rev_pract}}</div>';
            }

            if( $rev_pract ){
                $form_rendered = str_replace("{{icon_rev_pract}}", '<i title="Revisado practicante" style="float:right" class="fas fa-check"></i>', $form_rendered );
            }else{
                $form_rendered = str_replace("{{icon_rev_pract}}", '', $form_rendered );
            }

        }
    }

    return $form_rendered;
}

/**
 * Filter the trackings of a monitor that are reviewed by the professional
 *
 * @see filter_trackings_by_review($peer_tracking_v2)
 * @param peer_tracking_v2 --> Array of trackings
 * @return
 *
 */

function filter_trackings_by_review($peer_tracking_v2)
{

    $rev_pro = 0;
    $not_rev_pro = 0;
    $rev_prac = 0;
    $not_rev_prac = 0;

    foreach( $peer_tracking_v2 as $track ){
        if( $track["revisado_profesional"] == 0 ){
            $rev_pro++;
        }else{
            $not_rev_pro++;
        }
        if( $track["revisado_practicante"] == 0 ){
            $rev_prac++;
        }else{
            $not_rev_prac++;
        }
    }

    $counting = [];
    $counting[0] = $rev_pro;
    $counting[1] = $not_rev_pro;
    $counting[2] = $rev_prac;
    $counting[3] = $not_rev_prac;

    return $counting;
}

/**
 * Calculate the  tracking count of the practitioner and professional roles
 *
 * @see auxiliary_specific_counting($user_kind,$user_id,$semester,$instance)
 * @param $user_kind --> Name of role
 * @param $user_id --> id of user
 * @param $semester
 * @param $instance --> id of instance
 * @return Array
 *
 */

function auxiliary_specific_counting($user_kind, $user_id, $semester, $instance){
    $array_final = array();
    if ($user_kind == 'profesional_ps') {
        $practicant_of_prof = get_pract_of_prof($user_id, $instance);
        foreach($practicant_of_prof as $practicant) {
            $practicant_id = $practicant->id_usuario;
            $monitors_of_pract = get_monitors_of_pract($practicant_id, $instance);
            $profesional_counting = calculate_specific_counting('PROFESIONAL', $monitors_of_pract, $semester->max, $instance);
            $counting_advice = new stdClass();
            $counting_advice->code = $practicant->username;
            $counting_advice->html="<h6><p class='text-right'><strong class='subpanel'>RP :</strong><label class='review_prof'>".$profesional_counting[0]."</label> - <strong class='subpanel'> N RP: </strong><label class='not_review_prof'>".$profesional_counting[1]."</label> - <strong class='subpanel'>TOTAL:</strong><label class='total_prof'>".($profesional_counting[0]+$profesional_counting[1])."</label></p><p class='text-right'><strong class='subpanel'>Rp :</strong><label class='review_pract'>".$profesional_counting[2]."</label> - <strong class='subpanel'> N Rp: </strong><label class='not_review_pract'>".$profesional_counting[3]."</label> - <strong class='subpanel'>TOTAL:</strong><label class='total_pract'>".($profesional_counting[2]+$profesional_counting[3])."</label></p></h6>";
            array_push($array_final, $counting_advice);
        }
    }else if ($user_kind == 'practicante_ps') {
        $monitors_of_pract = get_monitors_of_pract($user_id, $instance);
        foreach($monitors_of_pract as $monitor) {
            $monitor_id = $monitor->id_usuario;
            $practicant_counting = calculate_specific_counting("PRACTICANTE", $monitor, $semester->max, $instance);
            $counting_advice = new stdClass();
            $counting_advice->code = $monitor->username;
            $counting_advice->html = "<h6><p class='text-right'><strong class='subpanel'>RP :</strong><label class='review_prof'>" . $practicant_counting[0] . "</label> - <strong class='subpanel'> N RP: </strong><label class='not_review_prof'>" . $practicant_counting[1] . "</label> - <strong class='subpanel'>TOTAL:</strong><label class='total_prof'>" . ($practicant_counting[0] + $practicant_counting[1]) . "</label></p><p class='text-right'><strong class='subpanel'>Rp :</strong><label class='review_pract'>" . $practicant_counting[2] . "</label> - <strong class='subpanel'> N Rp: </strong><label class='not_review_pract'>" . $practicant_counting[3] . "</label> - <strong class='subpanel'>TOTAL:</strong><label class='total_pract'>" . ($practicant_counting[2] + $practicant_counting[3]) . "</label></p></h6>";
            array_push($array_final, $counting_advice);
        }
    }
    return $array_final;
}

function auxiliary_specific_countingV2($user_kind, $user_id, $semester, $instance){

    $fecha_inicio = null;
    $fecha_fin = null;

    $semester_id = $semester->max;
    $interval = get_semester_interval($semester->max);
    $fecha_inicio = getdate(strtotime($interval->fecha_inicio));
    $fecha_fin = getdate(strtotime($interval->fecha_fin));

    $mon_tmp = $fecha_inicio["mon"];
    $day_tmp = $fecha_inicio["mday"];
    if( $mon_tmp < 10 ){
        $mon_tmp = "0" . $mon_tmp;
    }
    if( $day_tmp < 10 ){
        $day_tmp = "0" . $day_tmp;
    }

    $fecha_inicio_str = $fecha_inicio["year"]."-".$mon_tmp."-".$day_tmp;

    $mon_tmp = $fecha_fin["mon"];
    $day_tmp = $fecha_fin["mday"];
    if( $mon_tmp < 10 ){
        $mon_tmp = "0" . $mon_tmp;
    }
    if( $day_tmp < 10 ){
        $day_tmp = "0" . $day_tmp;
    }

    $fecha_fin_str = $fecha_fin["year"]."-".$mon_tmp."-".$day_tmp;

    $array_final = array();
    if ($user_kind == 'profesional_ps') {


        //Get assignments
        $student_list_ids = [];
        $practicant_from_profesional = monitor_assignments_get_practicants_from_professional( $instance, $user_id , $semester_id );
        foreach( $practicant_from_profesional as $key__ => $pract ){
            $monitors_from_practicant = monitor_assignments_get_monitors_from_practicant( $instance, $pract->id , $semester_id );
            foreach( $monitors_from_practicant as $key_ => $monitor ){
                $students_from_monitor = monitor_assignments_get_students_from_monitor( $instance, $monitor->id , $semester_id );
                foreach( $students_from_monitor as $key => $student ){
                    array_push( $student_list_ids, $student );
                }
            }
        }

        $xquery_seguimiento_pares_filterFields = [
            ["fecha",[[$fecha_inicio_str,">="],[$fecha_fin_str,"<="]], false],
            ["revisado_profesional",[["%%","LIKE"]], false],
            ["revisado_practicante",[["%%","LIKE"]], false]
        ];
        foreach( $student_list_ids as $key => $student ){
            array_push( $xquery_seguimiento_pares_filterFields, ["id_estudiante", [[ $student->id, "=" ]], false ] );
        }
        
        array_push( $xquery_seguimiento_pares_filterFields, ["id_profesional",[["%%","LIKE"]], false] );
       

        $xQuery = new stdClass();
        $xQuery->form = "seguimiento_pares";
        $xQuery->filterFields = $xquery_seguimiento_pares_filterFields;
        $xQuery->orderFields = [["fecha","DESC"]];
        $xQuery->orderByDatabaseRecordDate = true; 
        $xQuery->recordStatus = [ "!deleted" ];
        $xQuery->selectedFields = []; 

        $trackings = dphpformsV2_find_records( $xQuery );

        $rev_pro = 0;
        $not_rev_pro = 0;
        $rev_prac = 0;
        $not_rev_prac = 0;
    
        foreach( $trackings as $track ){
            if( $track["revisado_profesional"] == 0 ){
                $rev_pro++;
            }else{
                $not_rev_pro++;
            }
            if( $track["revisado_practicante"] == 0 ){
                $rev_prac++;
            }else{
                $not_rev_prac++;
            }
        }

        $xquery_inasistencia_filterFields = [
            ["in_fecha",[[$fecha_inicio_str,">="],[$fecha_fin_str,"<="]], false],
            ["in_revisado_profesional",[["%%","LIKE"]], false],
            ["in_revisado_practicante",[["%%","LIKE"]], false]
        ];

        foreach( $student_list_ids as $key => $student ){
            array_push( $xquery_inasistencia_filterFields, ["in_id_estudiante", [[ $student->id, "=" ]], false ] );
        }
        
        array_push( $xquery_inasistencia_filterFields, ["in_id_profesional",[["%%","LIKE"]], false] );


        $xQuery = new stdClass();
        $xQuery->form = "inasistencia";
        $xQuery->filterFields = $xquery_inasistencia_filterFields;
        $xQuery->orderFields = [["in_fecha","DESC"]];
        $xQuery->orderByDatabaseRecordDate = true; 
        $xQuery->recordStatus = [ "!deleted" ];
        $xQuery->selectedFields = []; 

        $in_trackings = dphpformsV2_find_records( $xQuery );

        $in_rev_pro = 0;
        $in_not_rev_pro = 0;
        $in_rev_prac = 0;
        $in_not_rev_prac = 0;
    
        foreach( $in_trackings as $track ){
            if( $track["in_revisado_profesional"] == 0 ){
                $in_rev_pro++;
            }else{
                $in_not_rev_pro++;
            }
            if( $track["in_revisado_practicante"] == 0 ){
                $in_rev_prac++;
            }else{
                $in_not_rev_prac++;
            }
        }

        $count['revisado_profesional'] = $rev_pro;
        $count['not_revisado_profesional'] = $not_rev_pro;
        $count['total_profesional'] = $rev_pro + $not_rev_pro;
        $count['revisado_practicante'] = $rev_prac;
        $count['not_revisado_practicante'] = $not_rev_prac;
        $count['total_practicante'] = $rev_prac + $not_rev_prac;

        $count['in_revisado_profesional'] = $in_rev_pro;
        $count['in_not_revisado_profesional'] = $in_not_rev_pro;
        $count['in_total_profesional'] = $in_rev_pro + $in_not_rev_pro;
        $count['in_revisado_practicante'] = $in_rev_prac;
        $count['in_not_revisado_practicante'] = $in_not_rev_prac;
        $count['in_total_practicante'] = $in_rev_prac + $in_not_rev_prac;

        return $count;

    }else if ($user_kind == 'practicante_ps') {

        //Get assignments
        $student_list_ids = [];
        $monitors_from_practicant = monitor_assignments_get_monitors_from_practicant( $instance, $user_id , $semester_id );
        foreach( $monitors_from_practicant as $key_ => $monitor ){
            $students_from_monitor = monitor_assignments_get_students_from_monitor( $instance, $monitor->id , $semester_id );
            foreach( $students_from_monitor as $key => $student ){
                array_push( $student_list_ids, $student );
            }
        }
        
        $xquery_seguimiento_pares_filterFields = [
            ["fecha",[[$fecha_inicio_str,">="],[$fecha_fin_str,"<="]], false],
            ["revisado_profesional",[["%%","LIKE"]], false],
            ["revisado_practicante",[["%%","LIKE"]], false]
        ];
        foreach( $student_list_ids as $key => $student ){
            array_push( $xquery_seguimiento_pares_filterFields, ["id_estudiante", [[ $student->id, "=" ]], false ] );
        }
        
        array_push( $xquery_seguimiento_pares_filterFields, ["id_practicante",[["%%","LIKE"]], false] );

        $xQuery = new stdClass();
        $xQuery->form = "seguimiento_pares";
        $xQuery->filterFields = $xquery_seguimiento_pares_filterFields;
        $xQuery->orderFields = [["fecha","DESC"]];
        $xQuery->orderByDatabaseRecordDate = false; 
        $xQuery->recordStatus = [ "!deleted" ];
        $xQuery->selectedFields = []; 

        $trackings = dphpformsV2_find_records( $xQuery );

        $rev_pro = 0;
        $not_rev_pro = 0;
        $rev_prac = 0;
        $not_rev_prac = 0;
    
        foreach( $trackings as $track ){
            if( $track["revisado_profesional"] == 0 ){
                $rev_pro++;
            }else{
                $not_rev_pro++;
            }
            if( $track["revisado_practicante"] == 0 ){
                $rev_prac++;
            }else{
                $not_rev_prac++;
            }
        }

        $xquery_inasistencia_filterFields = [
            ["in_fecha",[[$fecha_inicio_str,">="],[$fecha_fin_str,"<="]], false],
            ["in_revisado_profesional",[["%%","LIKE"]], false],
            ["in_revisado_practicante",[["%%","LIKE"]], false]
        ];

        foreach( $student_list_ids as $key => $student ){
            array_push( $xquery_inasistencia_filterFields, ["in_id_estudiante", [[ $student->id, "=" ]], false ] );
        }
        
        array_push( $xquery_inasistencia_filterFields, ["in_id_practicante",[["%%","LIKE"]], false] );

        $xQuery = new stdClass();
        $xQuery->form = "inasistencia";
        $xQuery->filterFields = $xquery_inasistencia_filterFields;
        $xQuery->orderFields = [["in_fecha","DESC"]];
        $xQuery->orderByDatabaseRecordDate = true; 
        $xQuery->recordStatus = [ "!deleted" ];
        $xQuery->selectedFields = []; 

        $in_trackings = dphpformsV2_find_records( $xQuery );

        $in_rev_pro = 0;
        $in_not_rev_pro = 0;
        $in_rev_prac = 0;
        $in_not_rev_prac = 0;
    
        foreach( $in_trackings as $track ){
            if( $track["in_revisado_profesional"] == 0 ){
                $in_rev_pro++;
            }else{
                $in_not_rev_pro++;
            }
            if( $track["in_revisado_practicante"] == 0 ){
                $in_rev_prac++;
            }else{
                $in_not_rev_prac++;
            }
        }

        $count['revisado_profesional'] = $rev_pro;
        $count['not_revisado_profesional'] = $not_rev_pro;
        $count['total_profesional'] = $rev_pro + $not_rev_pro;
        $count['revisado_practicante'] = $rev_prac;
        $count['not_revisado_practicante'] = $not_rev_prac;
        $count['total_practicante'] = $rev_prac + $not_rev_prac;

        $count['in_revisado_profesional'] = $in_rev_pro;
        $count['in_not_revisado_profesional'] = $in_not_rev_pro;
        $count['in_total_profesional'] = $in_rev_pro + $in_not_rev_pro;
        $count['in_revisado_practicante'] = $in_rev_prac;
        $count['in_not_revisado_practicante'] = $in_not_rev_prac;
        $count['in_total_practicante'] = $in_rev_prac + $in_not_rev_prac;

        return $count;
    }else if ($user_kind == 'monitor_ps') {

        //Get assignments
        $students_from_monitor = monitor_assignments_get_students_from_monitor( $instance, $user_id , $semester_id );

        $xquery_seguimiento_pares_filterFields = [
            ["fecha",[[$fecha_inicio_str,">="],[$fecha_fin_str,"<="]], false],
            ["revisado_profesional",[["%%","LIKE"]], false],
            ["revisado_practicante",[["%%","LIKE"]], false]
        ];

        foreach( $students_from_monitor as $key => $student ){
            array_push( $xquery_seguimiento_pares_filterFields, ["id_estudiante", [[ $student->id, "=" ]], false ] );
        }

        array_push( $xquery_seguimiento_pares_filterFields, ["id_monitor",[["%%","LIKE"]], false] );
        
        $xQuery = new stdClass();
        $xQuery->form = "seguimiento_pares";
        $xQuery->filterFields = $xquery_seguimiento_pares_filterFields;
        $xQuery->orderFields = [["fecha","DESC"]];
        $xQuery->orderByDatabaseRecordDate = false; 
        $xQuery->recordStatus = [ "!deleted" ];
        $xQuery->selectedFields = []; 
        
        $trackings = dphpformsV2_find_records( $xQuery );
        
        $rev_pro = 0;
        $not_rev_pro = 0;
        $rev_prac = 0;
        $not_rev_prac = 0;
    
        foreach( $trackings as $track ){
            if( $track["revisado_profesional"] == 0 ){
                $rev_pro++;
            }else{
                $not_rev_pro++;
            }
            if( $track["revisado_practicante"] == 0 ){
                $rev_prac++;
            }else{
                $not_rev_prac++;
            }
        }

        //'Inasistencia' counting
        
        $xquery_inasistencia_filterFields = [
            ["in_fecha",[[$fecha_inicio_str,">="],[$fecha_fin_str,"<="]], false],
            ["in_revisado_profesional",[["%%","LIKE"]], false],
            ["in_revisado_practicante",[["%%","LIKE"]], false]
        ];

        foreach( $students_from_monitor as $key => $student ){
            array_push( $xquery_inasistencia_filterFields, ["in_id_estudiante", [[ $student->id, "=" ]], false ] );
        }

        array_push( $xquery_inasistencia_filterFields, ["in_id_monitor",[["%%","LIKE"]], false] );

        $xQuery = new stdClass();
        $xQuery->form = "inasistencia";
        $xQuery->filterFields = $xquery_inasistencia_filterFields;
        $xQuery->orderFields = [["in_fecha","DESC"]];
        $xQuery->orderByDatabaseRecordDate = true; 
        $xQuery->recordStatus = [ "!deleted" ];
        $xQuery->selectedFields = []; 

        $in_trackings = dphpformsV2_find_records( $xQuery );
        
        $in_rev_pro = 0;
        $in_not_rev_pro = 0;
        $in_rev_prac = 0;
        $in_not_rev_prac = 0;
    
        foreach( $in_trackings as $track ){
            if( $track["in_revisado_profesional"] == 0 ){
                $in_rev_pro++;
            }else{
                $in_not_rev_pro++;
            }
            if( $track["in_revisado_practicante"] == 0 ){
                $in_rev_prac++;
            }else{
                $in_not_rev_prac++;
            }
        }

        $count['revisado_profesional'] = $rev_pro;
        $count['not_revisado_profesional'] = $not_rev_pro;
        $count['total_profesional'] = $rev_pro + $not_rev_pro;
        $count['revisado_practicante'] = $rev_prac;
        $count['not_revisado_practicante'] = $not_rev_prac;
        $count['total_practicante'] = $rev_prac + $not_rev_prac;

        $count['in_revisado_profesional'] = $in_rev_pro;
        $count['in_not_revisado_profesional'] = $in_not_rev_pro;
        $count['in_total_profesional'] = $in_rev_pro + $in_not_rev_pro;
        $count['in_revisado_practicante'] = $in_rev_prac;
        $count['in_not_revisado_practicante'] = $in_not_rev_prac;
        $count['in_total_practicante'] = $in_rev_prac + $in_not_rev_prac;

        return $count;
    }
    return $array_final;
}

/**
 * Calculate the  tracking count of the practitioner and professional roles
 *
 * @see calculate_specific_counting($user_kind,$person,$dates_interval,$instance)
 * @param $user_kind --> Name of role
 * @param $array_people --> class of monitor or practicant
 * @param $dates_interval
 * @param $instance --> id of instance
 * @return Array
 *
 */

function calculate_specific_counting($user_kind, $person, $dates_interval, $instance)
{
    $new_counting = array();
    $new_counting[0] = 0;
    $new_counting[1] = 0;
    $new_counting[2] = 0;
    $new_counting[3] = 0;
    if ($user_kind == 'PRACTICANTE') {
        $tracking_current_semestrer = get_tracking_current_semesterV2('monitor', $person->id_usuario, $dates_interval);
        $counting_trackings = filter_trackings_by_review($tracking_current_semestrer);
        $new_counting[0]+= $counting_trackings[0];
        $new_counting[1]+= $counting_trackings[1];
        $new_counting[2]+= $counting_trackings[2];
        $new_counting[3]+= $counting_trackings[3];
        return $new_counting;
    }
    else
    if ($user_kind == 'PROFESIONAL') {
        foreach($person as $key => $monitor) {
            $xQuery = new stdClass();
            $xQuery->form = "seguimiento_pares";
            $xQuery->filterFields = [["fecha",[[$fecha_inicio_str,">="],[$fecha_fin_str,"<="]], false],
                                    ["revisado_profesional",[["%%","LIKE"]], false],
                                    ["revisado_practicante",[["%%","LIKE"]], false]
                                    ];
            $xQuery->orderFields = [["fecha","DESC"]];
            $xQuery->orderByDatabaseRecordDate = false; 
            $xQuery->recordStatus = [ "!deleted" ];
            $xQuery->selectedFields = [ ]; 

            //$trackings = dphpformsV2_find_records( $xQuery );
            $tracking_current_semestrer = get_tracking_current_semesterV2('monitor', $monitor->id_usuario, $dates_interval);
            $counting_trackings = filter_trackings_by_review($tracking_current_semestrer);
            $new_counting[0]+= $counting_trackings[0];
            $new_counting[1]+= $counting_trackings[1];
            $new_counting[2]+= $counting_trackings[2];
            $new_counting[3]+= $counting_trackings[3];
        }
    }

    return $new_counting;
}

/**
 * Create the notice sign of the counts by professional and practicant
 *
 * @see create_counting_advice($user_kind,$result)
 * @param $user_kind --> String with the role of user
 * @param $result --> Array with number of reviewed trackings by profesional (0,1) and
 * practicant (2,3).
 * @return String
 *
 */

function create_counting_advice($user_kind, $result)
{
    $advice = "";
    $advice.= '<h2> INFORMACIÓN:  ' . $user_kind . '</h2><hr>';
    $advice.= '<div class="row">';
    $advice.= '<div class="col-sm-6">';
    $advice.= '<strong>Profesional</strong><br />';
    $advice.= 'Revisado :' . $result[0] . ' - No revisado : ' . $result[1] . ' -  Total :' . ($result[1] + $result[0]) . '</div>';
    $advice.= '<div class="col-sm-6">';
    $advice.= '<strong>Practicante</strong><br />';
    $advice.= 'Revisado :' . $result[2] . ' - No revisado : ' . $result[3] . ' -  Total :' . ($result[2] + $result[3]) . '</div></div>';
    return $advice;
}

/**
 * Formatting of array with dates of trackings
 *
 * @see format_dates_trackings($array_peer_trackings_dphpforms)
 * @param array_peer_trackings_dphpforms --> array with trackings of student
 * @return array with formatted dates
 *
 */

function format_dates_trackings(&$array_detail_peer_trackings_dphpforms, &$array_tracking_date, &$array_peer_trackings_dphpforms)
{
    foreach($array_peer_trackings_dphpforms->results as & $peer_trackings_dphpforms) {
        array_push($array_detail_peer_trackings_dphpforms, json_decode(dphpforms_get_record($peer_trackings_dphpforms->id_registro, 'fecha')));
    }

    foreach($array_detail_peer_trackings_dphpforms as & $peer_tracking) {
        foreach($peer_tracking->record->campos as & $tracking) {
            if ($tracking->local_alias == 'fecha') {
                array_push($array_tracking_date, strtotime($tracking->respuesta));
            }
        }
    }
}

/**
 * FunciĆ³n que ordena en un array los trackings para imprimir
 *
 * @see format_dates_trackings($array_peer_trackings_dphpforms)
 * @param array_peer_trackings_dphpforms --> array with trackings of student
 * @return array with formatted dates
 *
 */

function trackings_sorting($array_detail_peer_trackings_dphpforms, $array_tracking_date, $array_peer_trackings_dphpforms)
{
    $seguimientos_ordenados = new stdClass();
    $seguimientos_ordenados->index = array();

    // Inicio de ordenamiento

    $periodo_a = [1, 2, 3, 4, 5, 6, 7];

    // periodo_b es el resto de meses;

    for ($x = 0; $x < count($array_tracking_date); $x++) {
        $string_date = $array_tracking_date[$x];
        $array_tracking_date[$x] = getdate($array_tracking_date[$x]);
        $year = $array_tracking_date[$x]['year'];
        if (property_exists($seguimientos_ordenados, $year)) {
            if (in_array($array_tracking_date[$x]['mon'], $periodo_a)) {
                for ($y = 0; $y < count($array_detail_peer_trackings_dphpforms); $y++) {
                    if ($array_detail_peer_trackings_dphpforms[$y]) {
                        foreach($array_detail_peer_trackings_dphpforms[$y]->record->campos as & $tracking) {
                            if ($tracking->local_alias == 'fecha') {
                                if (strtotime($tracking->respuesta) == $string_date) {
                                    array_push($seguimientos_ordenados->$year->per_a, $array_detail_peer_trackings_dphpforms[$y]);
                                    $array_detail_peer_trackings_dphpforms[$y] = null;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            else {
                for ($y = 0; $y < count($array_detail_peer_trackings_dphpforms); $y++) {
                    if ($array_detail_peer_trackings_dphpforms[$y]) {
                        foreach($array_detail_peer_trackings_dphpforms[$y]->record->campos as & $tracking) {
                            if ($tracking->local_alias == 'fecha') {
                                if (strtotime($tracking->respuesta) == $string_date) {
                                    array_push($seguimientos_ordenados->$year->per_b, $array_detail_peer_trackings_dphpforms[$y]);
                                    $array_detail_peer_trackings_dphpforms[$y] = null;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
        else {
            array_push($seguimientos_ordenados->index, $year);
            $seguimientos_ordenados->$year->year = $year;
            $seguimientos_ordenados->$year->per_a = array();
            $seguimientos_ordenados->$year->per_b = array();
            $seguimientos_ordenados->$year->year = $year;
            if (in_array($array_tracking_date[$x]['mon'], $periodo_a)) {
                for ($y = 0; $y < count($array_detail_peer_trackings_dphpforms); $y++) {
                    if ($array_detail_peer_trackings_dphpforms[$y]) {
                        foreach($array_detail_peer_trackings_dphpforms[$y]->record->campos as & $tracking) {
                            if ($tracking->local_alias == 'fecha') {
                                if (strtotime($tracking->respuesta) == $string_date) {
                                    array_push($seguimientos_ordenados->$year->per_a, $array_detail_peer_trackings_dphpforms[$y]);
                                    $array_detail_peer_trackings_dphpforms[$y] = null;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            else {
                for ($y = 0; $y < count($array_detail_peer_trackings_dphpforms); $y++) {
                    if ($array_detail_peer_trackings_dphpforms[$y]) {
                        foreach($array_detail_peer_trackings_dphpforms[$y]->record->campos as & $tracking) {
                            if ($tracking->local_alias == 'fecha') {
                                if (strtotime($tracking->respuesta) == $string_date) {
                                    array_push($seguimientos_ordenados->$year->per_b, $array_detail_peer_trackings_dphpforms[$y]);
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

    // Fin de ordenamiento

    return $seguimientos_ordenados;
}

// ////////////////////////////////////////////////////////////////////////////////////////////////7

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


function replace_content_inside_delimiters($start, $end, $new, $source)
{
    return preg_replace('#(' . preg_quote($start) . ')(.*?)(' . preg_quote($end) . ')#si', '$1' . $new . '$3', $source);
}

/** 
 * Function that erase parts of toogle according to  user permissions
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


/**
 * Gets a select organized by existent periods
 * @see get_period_select($periods)
 * @param $periods ---> existent periods
 * @param $rol
 * @return string html table
 */

function get_period_select($periods, $rol = null){

    $extra = "";
    $table = "";

    if($rol !== "sistemas"){
        $extra .= "col-xs-offset-6 col-sm-offset-6 col-md-offset-7 col-lg-offset-7";
    }

    $table.= '<div id="consulta_periodo" class="form-group col-xs-6 col-sm-6 col-md-5 col-lg-5 '.$extra.'">';
    $table.= '<label for="periodos">Periodo:&nbsp;</label>';
    $table .= '<select style="width:80%" class="form-control" id="periodos">';

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

function get_people_select($people){
    
    $table = '<div id="consulta_personas" class="form-group col-xs-6 col-sm-6 col-md-5 col-lg-5">';
    $table.= '<label for="persona" >Persona:&nbsp;</label>';
    $table.= '<select style="width:80%" class="form-control" id="personas">';
    foreach($people as $person) {
        $table.= '<option data-username="' . $person->username . '" value="' . $person->id_usuario . '">' . $person->username . " - " . $person->firstname . " " . $person->lastname . '</option>';
    }
    $table.= '</select>';
    $table.= '</div>';

    $table.= '<div id="container-consulta-btn" class="col-xs-12 col-sm-12 col-md-2 col-lg-2">';
    $table.= '  <span class="btn btn-info" id="consultar_persona" type="button">Consultar</span>';
    $table.= '</div>';

    return $table;
}

?>