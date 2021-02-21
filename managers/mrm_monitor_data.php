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
 * lib" para actualizar los datos de monitores existentes (activos o no)
 * en la tabla monitores.
 *
 * @author     David S. Cortés
 * @package    block_ases
 * @copyright  2021 David S. Cortés david.cortes@correounivalle.edu.co
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Estos datos pueden ser:
// - Link al d10.
// - Link al acuerdo de confidencialidad firmado.
// - Link a una copia de la cc.
// - ...
// Para todos los campos posibles revisar las columnas de la tabla monitores. 

require_once(dirname(__FILE__). '/../../../config.php');
require_once('MyException.php');
require_once('mass_management/massmanagement_lib.php');


if ( isset($_FILES['file']) || isset($_POST['idinstancia']) ) {
	
	try {

		$file = $_FILES['file'];
		$extension = pathinfo( $file['name'], PATHINFO_EXTENSION );
		date_default_timezone("America/Bogota");
		$filename = $file['name'];

		$rootFolder = "../view/archivos_subidos/mrm/monitor_data/files/";
		$zipFolder = "../view/archivos_subidos/mrm/monitor_data/comprimidos/";

		// Limpiar carpetas antes de escribir.
		deleteFilesFromFolder($rootFolder);
		deleteFilesFromFolder($zipFolder);
		
		if ($extension !== 'csv') {
			Throw New MyException("El archivo " . $filename . " no corresponde a un archivo de tipo CSV. Por favor verficar.");	
		}

		if (!move_uploaded_file($file['tmp_name'], $rootFolder.'Original_'.$filename)) {
			Throw New MyException("Error al cargar el archivo");	
		}

        ini_set('auto_detect_line_endings', true);

		if (! ($handle = fopen($rootFolder.'Original_'.$nombre, 'r')) ) {
			Throw New MyException("Error al cargar el archivo ".$archivo['name'].". Es posible que el archivo se encuentre dañado");
		} 


		$record = new stdClass();
		$count = 0;
		$wrong_rows = array();
		$success_rows = array();

		$detail_errors = array();
		array_push($detail_errors, ['No. linea - archivo original','No. linea - archivo registros erroneos','No. columna','Nombre Columna' ,'detalle error']);

		$line_count = 2;
		$lc_wrong_file = 2;

		$title_pointer = fgetcsv($handle, 0, ",");
		array_push($detail_errors, $title_pointer);
		array_push($success_rows, $title_pointer);


	
	} catch(MyException $ex) {
		$msj = new stdClass();
		$msj->error = $ex->getMessage().pg_last_error();
		echo json_encode($msj);
	}
}

function validateHeaders($headerPos) {
	$requiredHeaders = [
		"username",
		"programa",
		"num_doc",
		"pdf_cuenta_banco",
		"pdf_doc",
		"pdf_d10",
		"email_alternativo"
	]
}
