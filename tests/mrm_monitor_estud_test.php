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
 * Archivo de pruebas unitarias para:
 * managers/mass_role_management/mrm_monitor_estud.php
 * @author David S. Cortés <david.cortes@correounivalle.edu.co>
 */

require_once(__DIR__ . '/../managers/mass_management/mrm_monitor_estud.php');

class block_ases_mrm_monitor_estud_testcase extends basic_testcase 
{
    /**
     * Prueba que la función getAssociativeTitles() lance una excepción
     * cuando se llama con un arreglo de titulos incorrecto.
    */
    public function test_bad_titles() 
    {
	    $bad_titles = ["foo", "bar"];

        $this->expectException(MyException::class);
        $this->expectExceptionMessage('Error al cargar el archivo. El titulo "foo" no corresponde a alguna columna valida');

        getAssociativeTitles($bad_titles);
	}

    /**
     * Prueba que la función getAssociativeTitles() 
     * devuelve un arreglo asociativo
     * cuando se llama con un arreglo de titulos correcto.
    */
    public function test_good_titles() 
    {
	    $good_titles = ["username_monitor", "username_estudiante"];
        $result = getAssociativeTitles($good_titles);
        $this->assertArrayHasKey('username_monitor', $result);
        $this->assertArrayHasKey('username_estudiante', $result);
	}
    /**
     * Array
    (
        [name] => mon_estud.csv
        [type] => text/csv
        [tmp_name] => /tmp/phpWXCWpL
        [error] => 0
        [size] => 53
    )
     */
}
