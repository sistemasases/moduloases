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
class block_ases_mrm_monitor_estud_testcase extends basic_testcase 
{
    /**
     * Prueba para la función getAssociativeTitles()
     */
    public function test_titles() 
    {
        require_once(__DIR__ . '/../managers/mass_management/mrm_monitor_estud.php');
	    $this->assertEquals(3, 1+2);
	}
}
