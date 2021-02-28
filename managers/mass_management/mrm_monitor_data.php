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

//require_once(dirname(__FILE__). '/../../../config.php');

require_once(dirname(__FILE__). '/../../../../config.php');
//require_once('massmanagement_lib.php');
require_once('../MyException.php');
require_once('../query.php');
require_once('../monitor_profile/monitor_profile_lib.php');


if ( isset($_FILES['file']) || isset($_POST['idinstancia']) ) {
	
	try {

		$file = $_FILES['file'];
		$extension = pathinfo( $file['name'], PATHINFO_EXTENSION );
		date_default_timezone_set("America/Bogota");
		$filename = $file['name'];

		$rootFolder = "../../view/archivos_subidos/mrm/monitor_data/files/";
		$zipFolder = "../../view/archivos_subidos/mrm/monitor_data/comprimidos/";
		

		if (!file_exists($rootFolder)) {
			mkdir($rootFolder, 0777, true);
		}

		if (!file_exists($zipFolder)) {
			mkdir($zipFolder, 0777, true);
		}

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
		$handle = fopen($rootFolder.'Original_'.$filename, 'r');

		if (! $handle ) {
			Throw New MyException("Error al cargar el archivo ".$file['name'].". Es posible que el archivo se encuentre dañado");
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
		
		validateHeaders($title_pointer);
		//$mappedFields = mapFields($title_pointer);

		// Iterar linea a linea sobre el .csv
        $dataobjects = [];
		while ($data = fgetcsv($handle, 0, ",")) {
			
			$isValidRow = true;
			$seguimientoid = 0;

			// Validación username
            $id_user = validateUsername($data[0]);
            if (!$id_user) {
                $isValidRow = false;
            }
            // Validación programa
            $program = validateProgram($data[1]);
            if (is_null($program)) {
                $isValidRow = false;
            }
            // validación documento de identidad  
            $num_doc = $data[2];
            if (is_null($num_doc)) {
                $isValidRow = false;
            }

	        if (!$isValidRow) {
                $lc_wrong_file++;
                $line_count++;
                array_push($wrong_rows, $data);
                continue;
            } else {
                $new_monitor = mapFields($title_pointer, $data);
                unset($new_monitor['programa']);
                unset($new_monitor['username']);

                $new_monitor['id_programa'] = $program;
                $new_monitor['id_moodle_user'] = $id_user;
                
                // prepare array for in-bulk db update
                array_push($dataobjects, (object)$new_monitor);

                $result = monitor_is_active($new_monitor['id_moodle_user']);
                
                if ($result) {
                    array_push($success_rows, $data);
                } else {
                    array_push($wrong_rows, $data);
                    $lc_wrong_file++;
                } 
                $line_count++;
            }	
		}
        
        if (count($wrong_rows) > 1) {
            
            $filewrongname = $rootFolder.'RegistrosErroneos_'.$filename;
            
            $wrongfile = fopen($filewrongname, 'w');                              
            fprintf($wrongfile, chr(0xEF).chr(0xBB).chr(0xBF)); // feed utf-8 unicode format on
            foreach ($wrong_rows as $row) {
                fputcsv($wrongfile, $row);              
            }
            fclose($wrongfile);
            
            //----
            $detailsFilename =  $rootFolder.'DetallesErrores_'.$filename;
            
            $detailsFileHandler = fopen($detailsFilename, 'w');
            fprintf($detailsFileHandler, chr(0xEF).chr(0xBB).chr(0xBF)); // feed utf-8 unicode format on
            foreach ($detail_errors as $row) {
                fputcsv($detailsFileHandler, $row);              
            }
            fclose($detailsFileHandler);
        }

        
        if(count($success_rows) > 1){ //First row are titles
            // Do bulk update
            create_monitor_records($dataobjects); 

            $arrayIdsFilename =  $rootFolder.'RegistrosExitosos_'.$filename;
            
            $arrayIdsFileHandler = fopen($arrayIdsFilename, 'w');
            fprintf($arrayIdsFileHandler, chr(0xEF).chr(0xBB).chr(0xBF)); // feed utf-8 unicode format on
            foreach ($success_rows as $row) {
                fputcsv($arrayIdsFileHandler, $row);              
            }
            fclose($arrayIdsFileHandler);
            
            $response = new stdClass();
            
            if(count($wrong_rows) > 1){
                $response->warning = 'Archivo cargado con inconsistencias<br> Para mayor informacion descargar la carpeta con los detalles de inconsitencias.'; 
            }else{
                $response->success = 'Archivo cargado satisfactoriamente';
            }
            
            $zipname = $zipFolder."detalle.zip";
            createZip($rootFolder, $zipname);

            $zipname = explode("..", $zipname)[2];            
            
            $response->urlzip = "<a target='_blank' href='..$zipname'>Descargar detalles</a>";
            
            echo json_encode($response);
            
        }else{
            $response = new stdClass();
            $response->error = "No se cargo el archivo. Para mayor informacion descargar la carpeta con los detalles de inconsitencias.";
            
            $zipname = $zipFolder."detalle.zip";
            createZip($rootFolder, $zipname);
            
            $zipname = explode("..", $zipname)[2];

            $response->urlzip = "<a target='_blank': href='..$zipname'>Descargar detalles</a>";
            
            echo json_encode($response);
        }

	
	} catch(MyException $ex) {
		$msj = new stdClass();
		$msj->error = $ex->getMessage().pg_last_error();
		echo json_encode($msj);
		fclose($handle);
	}
}
/**
 * Makes sure username field is filled with valid data.
 */
function validateUsername($username) {
	
	if (isset($username)) {
		$user = get_user_by_username($username);
		if (is_null($user)) {
			array_push($detail_errors, $line_count, $lc_wrong_file, 1, 'username', 'No existe usuario asociado al username ' . $username);
            return false;
        } else {
            return $user->id;
        }
	} else {
		Throw New MyException('El campo username es obligatorio.');
	}
}

function validateProgram($program_code, $id_sede = null) {

    if (is_null($id_sede)) {
        $id_sede = 1;
    }

    // If there's another function that does this, please use it.
    // -- -- --
    global $DB;
    try {
        $sql =
            "SELECT id 
            FROM {talentospilos_programa}
            WHERE id_sede=$id_sede  
            AND cod_univalle=$program_code";
        $result = $DB->get_record_sql($sql);
    } catch (Exception $ex) {
       Throw New MyException($ex->getMessage()); 
    }

    return $result->id;
}

function validateHeaders($title_pointer) {
	$required_headers = [
		"username",
		"programa",
		"num_doc",
		"pdf_cuenta_banco",
		"pdf_doc",
		"pdf_d10",
		"pdf_acuerdo_conf",
		"email_alternativo",
		"telefono1",
		"telefono2"
	];

	if ($title_pointer !== $required_headers) {
		Throw New MyException (
			'Error al cargar el archivo. Los encabezados no son los correctos.'
		);
	}
}

function mapFields($title_pointer, $data) {
	$map = [];
    $i = 0;
	foreach ($title_pointer as $title) {
		$map[$title] = $data[$i];	
        $i++;
	}

	return $map;
}
