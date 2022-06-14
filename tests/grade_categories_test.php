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
 * Pruebas "unitarias" para grade_categories_lib
 * @author David S. Cortés <david.cortes@correounivalle.edu.co>
 */

global $CFG;
require_once(__DIR__ . '/../managers/grade_categories/grade_categories_lib.php');

class block_ases_grade_categories_testcase extends advanced_testcase {

    /**
     * Cuando la instancia que se le pasa no es numérica, se 
     * espera una excepción.
     */     
    public function test_non_nummeric_instance() {
	$this->expectException(Exception::class);
	$this->expectExceptionMessage('instancia no numerica');
	get_courses_pilos('bad_instance0x99');
    }

    /**
     * Cuando se le pasa una instancia que sí es numerica pero no
     * existe, debe devolver siempre un arreglo vacío.
     */
    public function test_non_existent_instance() {
       $result = get_courses_pilos(42);	
       $this->assertEmpty($result);
    }

    /**
     * Cuandocla instancia es numerica y existe, 
     * debe devolver un arreglo, puede que esté vacío como puede que no.
     */
    public function test_correct_instance() {
       $result = get_courses_pilos(450299);	
       $this->assertIsArray($result);
    }
}
