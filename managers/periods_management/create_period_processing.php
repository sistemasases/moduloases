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

	module_loader("periods");

	if(isset($_POST['op']) && isset($_POST['instance']) && isset($_POST['name']) && isset($_POST['beginning']) && isset($_POST['ending']) && $_POST['op'] == 'createSemester'){

		$create = core_periods_create_period($_POST['name'], $_POST['beginning'], $_POST['ending'], $_POST['instance']);

		echo "El registro se realizó con éxito";	

	}
