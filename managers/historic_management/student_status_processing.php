<?php
/**
 * Created by PhpStorm.
 * User: alejandro
 * Date: 9/24/19
 * Time: 11:04 AM
 */
/**
 * Ases block
 *
 * @author     Alejandro Palacios Hurtado
 * @package    block_ases
 * @copyright  2019 Alejandro Palacios Hurtado <palacios.alejandro@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//This file is based on academic_processing.php

require_once dirname(__FILE__) . '/../../../../config.php';
require_once '../MyException.php';
require_once '../mass_management/massmanagement_lib.php';
require_once '../historic_management/historic_academic_lib.php';

//Checks if a file was recieved
if (isset($_FILES['file'])) {

    try {

        global $DB;
        $response = new stdClass();

        $archivo = $_FILES['file'];
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        date_default_timezone_set("America/Bogota");
        $nombre = $archivo['name'];

        $rootFolder = "../../view/archivos_subidos/historic/student_status/files/";
        $zipFolder = "../../view/archivos_subidos/historic/student_status/comprimidos/";

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

            //validate programa
            if ($associativeTitles['codigo_programa'] != null) {
                $codigo_programa = $data[$associativeTitles['codigo_programa']];
                if ($codigo_programa != '') {

                    $id_programa = get_id_program($codigo_programa);
                    if (!$id_programa) {
                        $isValidRow = false;
                        array_push($detail_errors, [$line_count, $lc_wrongFile, ($associativeTitles['codigo_programa'] + 1), 'codigo_programa', 'No existe un programa asociado al codigo ' . $codigo_programa]);
                    }

                } else {
                    $isValidRow = false;
                    array_push($detail_errors, [$line_count, $lc_wrongFile, ($associativeTitles['codigo_programa'] + 1), 'codigo_programa', 'El campo codigo_programa es obligatorio y se encuentra vacio']);
                }

            } else {
                throw new MyException('La columna con el campo programa es obligatoria');
            }

            //validate codigo_estudiante
            if (!is_null($associativeTitles['codigo_estudiante'])) {

                $codigo_estudiante = $data[$associativeTitles['codigo_estudiante']].'-'.$codigo_programa;

                if ($codigo_estudiante != '') {

                    $id_estudiante = get_ases_id_by_code($codigo_estudiante);
                    if (!$id_estudiante) {
                        $isValidRow = false;
                        array_push($detail_errors, [$line_count, $lc_wrongFile, ($associativeTitles['codigo_estudiante'] + 1), 'codigo_estudiante', 'No existe un estudiante ases asociado al codigo' . $data[$associativeTitles['codigo_estudiante']]]);
                    }

                } else {
                    $isValidRow = false;
                    array_push($detail_errors, [$line_count, $lc_wrongFile, ($associativeTitles['codigo_estudiante'] + 1), 'codigo_estudiante', 'El campo codigo_estudiante es obligatorio y se encuentra vacio']);
                }

            } else {
                throw new MyException('La columna con el campo codigo_estudiante es obligatoria');
            }

            //validate semestre
            if ($associativeTitles['semestre'] != null) {
                $semestre = $data[$associativeTitles['semestre']];
                if ($semestre != '') {

                    $id_semestre = get_id_semester($semestre);
                    if (!$id_semestre) {
                        $isValidRow = false;
                        array_push($detail_errors, [$line_count, $lc_wrongFile, ($associativeTitles['semestre'] + 1), 'semestre', 'No existe ningun semestre registrado al nombre' . $semestre]);
                    }

                } else {
                    $isValidRow = false;
                    array_push($detail_errors, [$line_count, $lc_wrongFile, ($associativeTitles['semestre'] + 1), 'semestre', 'El campo semestre es obligatorio y se encuentra vacio']);
                }

            } else {
                throw new MyException('La columna con el campo semestre es obligatoria');
            }

            //validate estado_grado
            $graduado = false;
            if ($associativeTitles['estado_grado'] != null){
                $estado_grado = $data[$associativeTitles['estado_grado']];
                if($estado_grado === 'SI'){
                    $graduado = true;
                }
            }

            $hasCancel = false;
            //validate fecha_cancelacion
            if ($associativeTitles['cancelo_semestre'] != null) {
                $fecha_cancelacion = $data[$associativeTitles['cancelo_semestre']];
                if ($fecha_cancelacion != "" and $fecha_cancelacion != 'undefined') {
                    $hasCancel = true;
                }
            }

            //FINALIZACION DE VALIDACIONES. CARGA O ACTUALIZACIÓN
            if (!$isValidRow) {
                $lc_wrongFile++;
                array_push($wrong_rows, $data);
                continue;
            } else {
                //Actualizar o crear un registro
                $result = update_student_status($id_estudiante, $id_programa, $id_semestre, $graduado, $hasCancel);

                if (!$result) {
                    array_push($detail_errors, [$line_count, $lc_wrongFile, 'Error al registrar historico', 'Error Servidor', 'Error del server registrando el historico']);
                    array_push($wrong_rows, $data);
                    $lc_wrongFile++;
                }else {
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

    }catch (MyException $e) {
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
