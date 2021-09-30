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
 * Pruebas unitarias concernientes a la función get_assigned_pract dentro de:
 * @see managers/lib/student_lib.php
 *
 * @package    block_ases
 * @category   test
 * @copyright  2021 David Santiago Cortés <david.cortes@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @group block_ases
 */

class block_ases_student_get_pract_testcase extends advanced_testcase {
    private $instance_id = 450299;
    private $student;

    protected function setUp() {
        $this->student = $this->getDataGenerator()->create_user();
        $this->resetAfterTest(true);
    }

    public function test_fails_with_invalid_arguments() {
        require_once(__DIR__.'/../managers/lib/student_lib.php');
        
        $this->expectException(Exception::class);
        $result = get_assigned_pract(null, null);
    }

    public function test_error_when_invalid_instance() {
        require_once(__DIR__.'/../managers/lib/student_lib.php');
        
        $this->expectException(Exception::class);
        $result = get_assigned_pract($this->student->id, null);
    }

    public function test_error_when_invalid_user() {
        require_once(__DIR__.'/../managers/lib/student_lib.php');
        
        $this->expectException(Exception::class);
        $result = get_assigned_pract(null, $this->instance_id);
    }

    public function test_returns_empty_array_when_no_assignation() {
        require_once(__DIR__.'/../managers/lib/student_lib.php');
        require_once(__DIR__.'/../core/module_loader.php');
        module_loader('periods');

        global $DB;
        
        // Creación del semestre
        $fecha_inicio = new DateTime('2021-02-01', new DateTimeZone('America/Bogota'));
        $fecha_fin = new DateTime('2021-09-30', new DateTimeZone('America/Bogota'));
        core_periods_create_period('2021A', '2021-02-01', '2021-09-30', $this->instance_id);
        
        $result = get_assigned_pract(42, $this->instance_id);
        $this->assertCount(0, $result);
    }

    public function test_returns_pract() {
        require_once(__DIR__.'/../managers/lib/student_lib.php');
        require_once(__DIR__.'/../core/module_loader.php');
        module_loader('periods');

        global $DB;
        
        // Creación del semestre
        $fecha_inicio = new DateTime('2021-02-01', new DateTimeZone('America/Bogota'));
        $fecha_fin = new DateTime('2021-09-30', new DateTimeZone('America/Bogota'));
        $period = core_periods_create_period('2021A', '2021-02-01', '2021-09-30', $this->instance_id);

        // Creación de los usuarios.
        $monitor = $this->getDataGenerator()->create_user();
        $pract = $this->getDataGenerator()->create_user();

        $mon_student_obj = [
            'id_monitor' => $monitor->id,
            'id_estudiante' => $this->student->id,
            'id_instancia' => $this->instance_id,
            'id_semestre' => $period
        ];

        $DB->insert_record('talentospilos_monitor_estud', $mon_student_obj);

        $mon_role_obj = [
            'id_rol' => 4,
            'id_usuario' => $monitor->id,
            'estado' => 1,
            'id_semestre' => $period,
            'id_jefe' => $pract->id,
            'id_instancia' => $this->instance_id
        ];
        $DB->insert_record('talentospilos_user_rol', $mon_role_obj);

        $result = get_assigned_pract($this->student->id, $this->instance_id);
        $this->assertObjectHasAttribute('id', $result);
    }
}
