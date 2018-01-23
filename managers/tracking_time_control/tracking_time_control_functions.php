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


/**
 * Calculates the number of dedicated hours of a monitor on a given date.
 * 
 * @see calculate_hours($dates)
 * @param $dates
 * @return void
 */

 function calculate_hours($date){

    $register= new stdClass();

    $first_date =$date->fecha;
    $register->date=$first_date;

    $initial_time=$date->hora_fin;
    $final_time=$date->hora_ini;

    $separar[1]=explode(':',$initial_time); 
    $separar[2]=explode(':',$final_time); 

    $total_minutos_trasncurridos[1] = ($separar[1][0]*60)+$separar[1][1]; 
    $total_minutos_trasncurridos[2] = ($separar[2][0]*60)+$separar[2][1]; 
    $total_minutos_trasncurridos = $total_minutos_trasncurridos[1]-$total_minutos_trasncurridos[2]; 
        
    if($total_minutos_trasncurridos<=59) {
        if($total_minutos_trasncurridos<=0){
            $register->total_minutes=0;
        }else{
            $register->total_minutes=$total_minutos_trasncurridos; 
        }
        }elseif($total_minutos_trasncurridos>59){ 
            $HORA_TRANSCURRIDA = round($total_minutos_trasncurridos/60); 
            if($HORA_TRANSCURRIDA<=9) $HORA_TRANSCURRIDA='0'.$HORA_TRANSCURRIDA; 
                $MINUITOS_TRANSCURRIDOS = $total_minutos_trasncurridos%60; 
            if($MINUITOS_TRANSCURRIDOS<=9) $MINUITOS_TRANSCURRIDOS='0'.$MINUITOS_TRANSCURRIDOS; 
            $register->hours=$HORA_TRANSCURRIDA;
            $register->minutes=$MINUITOS_TRANSCURRIDOS;
 }
    return $register;
}
