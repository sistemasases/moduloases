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
 * Pruebas unitarias concernientes a la función get_assigned_monitor dentro de:
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
class block_ases_student_get_monitor_testcase extends advanced_testcase {

    private $student;
    private $monitor;
    private $period;
    private $instance_id = 450299;

    /**
     * Runs before tests.
     * Creates all asignations required to retrieve monitor and practicant
     * info.
     *
     * @author David S. Cortés <david.cortes@correounivalle.edu.co>
     */
    protected function setUp() {
        $this->resetAfterTest(true);

        require_once(__DIR__.'/../core/module_loader.php');
        module_loader('periods');

        global $DB;
        
        // Creación del semestre
        $fecha_inicio = new DateTime('2021-02-01', new DateTimeZone('America/Bogota'));
        $fecha_fin = new DateTime('2021-09-30', new DateTimeZone('America/Bogota'));
        $this->period = core_periods_create_period('2021A', '2021-02-01', '2021-09-30', $this->instance_id);
        
        // Creación de los usuarios.
        $this->monitor = $this->getDataGenerator()->create_user();
        $this->student = $this->getDataGenerator()->create_user();

        // Datos del usuario para insertar en {talentospilos_usuario}
        $mon_student_obj = [
            'id_monitor' => $this->monitor->id,
            'id_estudiante' => $this->student->id,
            'id_instancia' => $this->instance_id,
            'id_semestre' => $this->period
        ];
        $DB->insert_record('talentospilos_monitor_estud', $mon_student_obj);
    }
    
    /**
     * Verifica que con datos correctos se devuelve un objeto del monitor.
     * Para esto se lee el objeto retornado por get_assigned_monitor y
     * se espera que este objeto tenga un campo 'id', el cual corresponde al id del monitor.
     *
     * @author David S. Cortés <david.cortes@correounivalle.edu.co>
     */
    public function test_correct_with_valid_data() {
        require_once(__DIR__.'/../managers/lib/student_lib.php');
        
        $result = get_assigned_monitor($this->student->id, $this->instance_id);
        $this->assertObjectHasAttribute('id', $result);
    }
    
    /**
     * Cuando no existe asignación por parte del estudiante, es decir,
     * el id del estudiante proporcionado no está matriculado en ASES.
     * Se debe devolver un arreglo vacío, pues no hay monitor.
     *
     * @author David S. Cortés <david.cortes@correounivalle.edu.co>
     */
    public function test_empty_array_when_user_does_not_exists() {
        require_once(__DIR__.'/../managers/lib/student_lib.php');
        
        $result = get_assigned_monitor(42, $this->instance_id);
        $this->assertCount(0, $result);
    }
    
    /**
     * Cuando se proporciona un valor no numérico como instancia,
     * se espera que se lance una excepción.
     *
     * @author David S. Cortés <david.cortes@correounivalle.edu.co>
     */
    public function test_error_when_invalid_instance() {
        require_once(__DIR__.'/../managers/lib/student_lib.php');
        
        $this->expectException(Exception::class);
        $result = get_assigned_monitor($this->student->id, null);
    }
    
    /**
     * Cuando se proporciona un valor no numérico como id de estudiante,
     * se espera que se lance una excepción.
     *
     * @author David S. Cortés <david.cortes@correounivalle.edu.co>
     */
    public function test_error_when_invalid_userid() {
        require_once(__DIR__.'/../managers/lib/student_lib.php');
        
        $this->expectException(Exception::class);
        $result = get_assigned_monitor(null, $this->instance_id);

    }

    /**
     * Cuando ambos parametros no son numéricos,
     * se espera que se lance una excepción.
     *
     * @author David S. Cortés <david.cortes@correounivalle.edu.co>
     */
    public function test_error_when_invalid_arguments() {
        require_once(__DIR__.'/../managers/lib/student_lib.php');
        
        $this->expectException(Exception::class);
        $result = get_assigned_monitor(null, null);
    }
}
