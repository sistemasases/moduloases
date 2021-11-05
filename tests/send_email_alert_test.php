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
 * Pruebas unitarias para la función send_email_alert dentro del observador.
 * @author David S. Cortés <david.cortes@correounivalle.edu.co>
 * @see classes/observer.php
 */

require_once(__DIR__ . '/../classes/observer.php');

class send_email_alert_testcase extends advanced_testcase 
{

    private $sistemas;
    private $observer;

    protected function setUp() 
    {
        $this->observer = new block_ases_observer();
    } 

    /**
     * Este test verifica que la función send_email_alert
     * devuelve falso cuando alguno de los parametros no es un número
     * ni un string numérico
     */
    public function test_invalid_arguments()
    {
        $result = $this->observer->send_email_alert('a', 'b', 'c', 'd');
        $this->assertFalse($result);
    }

    /**
     * Si el curso del estudiante no tiene un profesor asignado, se debe lanzar
     * una excepción que contiene el mensaje 'No teacher for course:'
     */
    public function test_no_teacher_enrolled() {

        $this->expectExceptionMessage("[classes/observer.php]: No teacher for course:");
        $this->resetAfterTest(true);

        $sistemas = $this->getDataGenerator()->create_user(array('username'=>'sistemas1008'));

        $student = $this->getDataGenerator()->create_user();
        $professor = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();

        // enrol student
        $this->getDataGenerator()->enrol_user($student->id, $course->id);

        $result = $this->observer->send_email_alert(
            $student->id, 3, 2.9, $course->id
        );
    }
    
    /**
     * Si el grade item no existe en {grade_items} se espera
     * false como respuesta.
     */
    public function test_no_grade_item()
    {
        $result = $this->observer->get_gradeitem('0');
        $this->assertFalse($result);
    }

    /**
     * Si el grade item existe en {grade_items} se espera
     * un objeto como respuesta.
     */
    public function test_correct_grade_item()
    {
        $this->resetAfterTest(true);
        
        $course = $this->getDataGenerator()->create_course();
        $gi = $this->getDataGenerator()->create_grade_item(array('courseid' => $course->id)); 

        $result = $this->observer->get_gradeitem($gi->id);
        $this->assertIsObject($result);
    }

    //public function test_email_sent()
    //{
    //
    //}
}
