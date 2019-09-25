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
 * @author     Juan Pablo Moreno Muñoz
 * @package    block_ases
 * @copyright  2018 Juan Pablo Moreno Muñoz <moreno.juan@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once dirname(__FILE__) . '/../../../../config.php';
require_once '../MyException.php';
require_once '../mass_management/massmanagement_lib.php';
require_once '../historic_management/historic_academic_lib.php';
require_once '../historic_management/historic_icetex_lib.php';

if (isset($_FILES['file'])) {

    try {
        global $DB;
        $response = new stdClass();

        $archivo = $_FILES['file'];
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        date_default_timezone_set("America/Bogota");
        $nombre = $archivo['name'];

        $rootFolder = "../../view/archivos_subidos/historic/icetex_status/files/";
        $zipFolder = "../../view/archivos_subidos/historic/icetex_status/comprimidos/";

        //validate and create folders
        if (!file_exists($rootFolder)) {
            mkdir($rootFolder, 0777, true);
        }
        if (!file_exists($zipFolder)) {
            mkdir($zipFolder, 0777, true);
        }

        //deletes everything from folders
        deleteFilesFromFolder($rootFolder);
        deleteFilesFromFolder($zipFolder);

        //validate extension
        if ($extension !== 'csv') {
            throw new MyException("El archivo " . $archivo['name'] . " no corresponde al un archivo de tipo CSV. Por favor verifícalo");
        }

        //validate and move file
        if (!move_uploaded_file($archivo['tmp_name'], $rootFolder . 'Original_' . $nombre)) {
            throw new MyException("Error al cargar el archivo.");
        }

        //validate and open file
        ini_set('auto_detect_line_endings', true);
        if (!($handle = fopen($rootFolder . 'Original_' . $nombre, 'r'))) {
            throw new MyException("Error al cargar el archivo " . $archivo['name'] . ". Es posible que el archivo se encuentre dañado");
        }

        //Control variables
        $wrong_rows = array();
        $success_rows = array();
        $detail_errors = array();

        //headers of error file
        array_push($detail_errors, ['No. linea - archivo original', 'No. linea - archivo registros erroneos', 'No. columna', 'Nombre Columna', 'detalle error']);

        $line_count = 2;
        $lc_wrongFile = 2;

        //headers of succes files
        $titlesPos = fgetcsv($handle, 0, ",");
        array_push($wrong_rows, $titlesPos);
        array_push($success_rows, $titlesPos);

        $associativeTitles = getAssociativeArray($titlesPos);

        while ($data = fgetcsv($handle, 0, ",")) {
            $isValidRow = true;
            /* VALIDATIONS OF FIELDS */

            
            //validate cedula_estudiante
            if (!is_null($associativeTitles['cedula_estudiante'])) {

                $cedula_estudiante = $data[$associativeTitles['cedula_estudiante']];

                if ($cedula_estudiante != '') {

                    $id_estudiante = get_student_id_by_identification($cedula_estudiante);
                    if (!$id_estudiante) {
                        $isValidRow = false;
                        array_push($detail_errors, [$line_count, $lc_wrongFile, ($associativeTitles['cedula_estudiante'] + 1), 'cedula_estudiante', 'No existe un estudiante ases asociado a la cédula' . $data[$associativeTitles['cedula_estudiante']]]);
                    }

                } else {
                    $isValidRow = false;
                    array_push($detail_errors, [$line_count, $lc_wrongFile, ($associativeTitles['cedula_estudiante'] + 1), 'cedula_estudiante', 'El campo cedula_estudiante es obligatorio y se encuentra vacio']);
                }

            } else {
                throw new MyException('La columna con el campo cedula_estudiante es obligatoria');
            }

            //validate semestre
            if ($associativeTitles['semestre'] != null) {
                $semestre = $data[$associativeTitles['semestre']];
                if ($semestre != '') {

                    $id_semestre = get_id_semester($semestre);
                    if (!$id_semestre) {
                        $isValidRow = false;
                        array_push($detail_errors, [$line_count, $lc_wrongFile, ($associativeTitles['semestre'] + 1), 'semestre', 'No existe ningun semestre registrado el nombre' . $semestre]);
                    }

                } else {
                    $isValidRow = false;
                    array_push($detail_errors, [$line_count, $lc_wrongFile, ($associativeTitles['semestre'] + 1), 'semestre', 'El campo semestre es obligatorio y se encuentra vacio']);
                }

            } else {
                throw new MyException('La columna con el campo semestre es obligatoria');
            }
            
            //validate estado icetex
            if ($associativeTitles['estado_icetex'] != null) {
                $estado_icetex = $data[$associativeTitles['estado_icetex']];
                if ($semestre != '') {

                    $id_estado_icetex = get_id_icetex_status($estado_icetex);
                    if (!$id_estado_icetex) {
                        $isValidRow = false;
                        array_push($detail_errors, [$line_count, $lc_wrongFile, ($associativeTitles['estado_icetex'] + 1), 'estado_icetex', 'No existe ningun estado icetex registrado con el nombre' . $estado_icetex]);
                    }

                } else {
                    $isValidRow = false;
                    array_push($detail_errors, [$line_count, $lc_wrongFile, ($associativeTitles['estado_icetex'] + 1), 'estado_icetex', 'El campo estado_icetex es obligatorio y se encuentra vacio']);
                }

            } else {
                throw new MyException('La columna con el campo estado_icetex es obligatoria');
            }

            
            //FINALIZACION DE VALIDACIONES. CARGA O ACTUALIZACIÓN
            if (!$isValidRow) {
                $lc_wrongFile++;
                array_push($wrong_rows, $data);
                continue;
            } else {

                //Actualizar o crear un registro
                $result = update_historic_icetex_status($id_estudiante, $id_semestre, $id_estado_icetex);

                if (!$result) {
                    array_push($detail_errors, [$line_count, $lc_wrongFile, 'Error al registrar historico', 'Error Servidor', 'Error del server registrando el historico']);
                    array_push($wrong_rows, $data);
                    $lc_wrongFile++;
                } else {
                    array_push($success_rows, $data);                    
                }
            }

            $line_count++;
        }

        //RECORRER LOS REGISTROS ERRONEOS Y CREAR ARCHIVO DE registros_erroneos
        if (count($wrong_rows) > 1) {

            $filewrongname = $rootFolder . 'RegistrosErroneos_' . $nombre;

            $wrongfile = fopen($filewrongname, 'w');
            fprintf($wrongfile, chr(0xEF) . chr(0xBB) . chr(0xBF)); // darle formato unicode utf-8
            foreach ($wrong_rows as $row) {
                fputcsv($wrongfile, $row);
            }
            fclose($wrongfile);

            //----
            $detailsFilename = $rootFolder . 'DetallesErrores_' . $nombre;

            $detailsFileHandler = fopen($detailsFilename, 'w');
            fprintf($detailsFileHandler, chr(0xEF) . chr(0xBB) . chr(0xBF)); // darle formato unicode utf-8
            foreach ($detail_errors as $row) {
                fputcsv($detailsFileHandler, $row);
            }
            fclose($detailsFileHandler);

        }
        //RECORRER LOS REGISTROS EXITOSOS Y CREAR ARCHIVO DE registros_exitosos
        if (count($success_rows) > 1) { //porque la primera fila corresponde a los titulos no datos
            $arrayIdsFilename = $rootFolder . 'RegistrosExitosos_' . $nombre;

            $arrayIdsFileHandler = fopen($arrayIdsFilename, 'w');
            fprintf($arrayIdsFileHandler, chr(0xEF) . chr(0xBB) . chr(0xBF)); // darle formato unicode utf-8
            foreach ($success_rows as $row) {
                fputcsv($arrayIdsFileHandler, $row);
            }
            fclose($arrayIdsFileHandler);

            $response = new stdClass();

            if (count($wrong_rows) > 1) {
                $response->warning = 'Archivo cargado con inconsistencias<br> Para mayor informacion descargar la carpeta con los detalles de inconsitencias.';
            } else {
                $response->success = 'Archivo cargado satisfactoriamente';
            }

            $zipname = $zipFolder . "detalle.zip";
            createZip($rootFolder, $zipname);

            $response->urlzip = "<a href='ases/$zipname'>Descargar detalles</a>";

            echo json_encode($response);

        } else {
            $response = new stdClass();
            $response->error = "No se cargo el archivo. Para mayor informacion descargar la carpeta con los detalles de inconsitencias.";

            $zipname = $zipFolder . "detalle.zip";
            createZip($rootFolder, $zipname);

            $response->urlzip = "<a href='ases/$zipname'>Descargar detalles</a>";

            echo json_encode($response);
        }

    } catch (MyException $e) {
        $msj = new stdClass();
        $msj->error = $e->getMessage() . pg_last_error();
        echo json_encode($msj);
    } catch (Exception $e) {
        $msj = new stdClass();
        $msj->error = $e->getMessage() . pg_last_error();
        echo json_encode($msj);
    }

} else {
    $msj = new stdClass();
    $msj->error = "No se recibio ningun archivo";
    echo json_encode($msj);
}
