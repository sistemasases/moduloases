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

        if ($extension !== 'csv') {
            throw new MyException("El archivo " . $archivo['name'] . " no corresponde al un archivo de tipo CSV. Por favor verifícalo");
        }

        if (!move_uploaded_file($archivo['tmp_name'], $rootFolder . 'Original_' . $nombre)) {
            throw new MyException("Error al cargar el archivo.");
        }

        ini_set('auto_detect_line_endings', true);
        if (!($handle = fopen($rootFolder . 'Original_' . $nombre, 'r'))) {
            throw new MyException("Error al cargar el archivo " . $archivo['name'] . ". Es posible que el archivo se encuentre dañado");
        }

        $wrong_rows = array();
        $success_rows =  array();
        $detail_erros = array();

        array_push($detail_erros,['No. linea - archivo original','No. linea - archivo registros erroneos','No. columna','Nombre Columna' ,'detalle error']);

        $line_count =2;
        $lc_wrongFile =2;
        
        $titlesPos = fgetcsv($handle, 0, ",");
        array_push($wrong_rows,$titlesPos);
        array_push($success_rows,$titlesPos);
         


        $msj = new stdClass();
        $msj->msj = "VAS BIEN";
        echo json_encode($titlesPos);

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
