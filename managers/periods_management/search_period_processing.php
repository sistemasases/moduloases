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
 * @author     Juan Pablo Moreno Muñoz
 * @package    block_ases
 * @copyright  2017 Juan Pablo Moreno Muñoz <moreno.juan@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

	require_once(dirname(__FILE__).'/../../../../config.php');
	require_once(dirname(__FILE__).'/../../core/module_loader.php');
	require_once('periods_lib.php');

	module_loader("periods");

	if(isset($_POST['dat'])){

		$info_semester = core_periods_get_period_by_id((int)$_POST['dat']);
		setlocale(LC_TIME, "es_CO.utf8");     
		
		$info_semester->fecha_inicio = strftime("%d %B %Y", strtotime($info_semester->fecha_inicio));
     		$info_semester->fecha_fin = strftime("%d %B %Y", strtotime($info_semester->fecha_fin));
		
		echo json_encode($info_semester);

	}else{

		$object = new stdClass();
		$object->error = "Error al consultar la base de datos. El semestre " .$_POST['dat']. " no se encuentra registrado en la base de datos";
		echo json_encode($object);
	}
