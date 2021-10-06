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
 * Pruebas unitarias concernientes a la función core_periods_get_current_period.
 *
 * @package    block_ases
 * @category   test
 * @copyright  2021 David Santiago Cortés <david.cortes@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @group block_ases
 */
class block_ases_current_period_testcase extends advanced_testcase {
    private $instance = 450299;
    /**
    * Crea un período antes de que inicien las pruebas
    */
    protected function setUp() {
        $this->resetAfterTest(true);

        require_once(__DIR__.'/../core/module_loader.php');
        module_loader('periods');

        $fecha_inicio = new DateTime('2021-02-01', new DateTimeZone('America/Bogota'));
        $fecha_fin = new DateTime('2021-10-15', new DateTimeZone('America/Bogota'));
        $period = core_periods_create_period('2021A', '2021-02-01', '2021-09-30', 450299);

    }

    /**
     * Cuando un período no existe, con la instancia asociada,
     * se devuelve null.
     *
     * @author David S. Cortés <david.cortes@correounivalle.edu.co>
     */
    public function test_returns_null() {
        $result = core_periods_get_current_period(42);
        $this->assertNull($result);
    }

    /**
     * Cuando la instancia tiene un valor inválido, 
     * debe lanzarse una excepción.
     *
     * @author David S. Cortés <david.cortes@correounivalle.edu.co>
     */
    public function test_throws_excep_invalid_instance() {
        $this->expectException(Exception::class); 
        core_periods_get_current_period('');
    }

    /**
     * Cuando existe un período y la instancia es válida,
     * devuelve un objeto con la información del período.
     *
     * @author David S. Cortés <david.cortes@correounivalle.edu.co>
     */
    public function test_returns_period() {
       $result = core_periods_get_current_period($this->instance); 
       $this->assertObjectHasAttribute('id', $result);
    }
}
