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
 * @author     Camilo José Cruz Rivera
 * @package    block_ases
 * @copyright  2017 Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once dirname(__FILE__) . '/../../../../config.php';
require_once '../MyException.php';
require_once '../mass_management/massmanagement_lib.php';
require_once '../historic_management/historic_academic_lib.php';

if (isset($_FILES['file'])) {

    try {
        global $DB;
        $record = new stdClass();

        $archivo = $_FILES['file'];
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        date_default_timezone_set("America/Bogota");
        $nombre = $archivo['name'];

        $rootFolder = "../../view/archivos_subidos/historic/academic/files/";
        $zipFolfer = "../../view/archivos_subidos/historic/academic/comprimidos/";

        //deletes everything from folders
        deleteFilesFromFolder($rootFolder);
        deleteFilesFromFolder($zipFolfer);

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
            //VALIDATIONS OF FIELDS

            //validate codigo_estudiante
            if ($associativeTitles['codigo_estudiante'] != null) {

                $codigo_estudiante = $data[$associativeTitles['codigo_estudiante']];

                if ($codigo_estudiante != '') {

                    $id_estudiante = get_ases_id_by_code($codigo_estudiante);
                    if (!$id_estudiante) {
                        $isValidRow = false;
                        array_push($detail_errors, [$line_count, $lc_wrongFile, ($associativeTitles['codigo_estudiante'] + 1), 'codigo_estudiante', 'No existe un estudiante ases asociado al codigo' . $data[$associativeTitles['codigo_estudiante']]]);
                    } else {

                    }

                } else {
                    $isValidRow = false;
                    array_push($detail_erros, [$line_count, $lc_wrongFile, ($associativeTitles['username'] + 1), 'username', 'No existe un usuario asociado al username' . $data[$associativeTitles['username']]]);
                }

            } else {
                throw new MyException('La columna con el campo codigo_estudiante es obligatoria');
            }

            //validate programa
            if ($index_array['programa'] != null) {

                if ($data[$index_array['programa']] != '') {

                } else {
                    throw new MyException('El campo programa es obligatorio');
                }

            } else {
                throw new MyException('La columna con el campo programa es obligatoria');
            }

            //validate semestre
            if ($index_array['semestre'] != null) {

                if ($data[$index_array['semestre']] != '') {

                } else {
                    throw new MyException('El campo semestre es obligatorio');
                }

            } else {
                throw new MyException('La columna con el campo semestre es obligatoria');
            }

            //validate promedio
            if ($index_array['promedio'] != null) {

            } else {
                throw new MyException('La columna con el campo promedio es obligatoria');
            }

            //validate promedio_acumulado
            if ($index_array['promedio_acumulado'] != null) {

            } else {
                throw new MyException('La columna con el campo promedio_acumulado es obligatoria');
            }
            //validate fecha_cancelacion
            //validate estimulo
            //validate bajo

            $line_count++;
        }

        echo json_encode($index_array);

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
