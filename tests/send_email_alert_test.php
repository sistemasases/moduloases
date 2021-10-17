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
     * @author David S. Cortés <david.cortes@correounivalle.edu.co>
     */
    public function test_invalid_arguments()
    {

        $result = $this->observer->send_email_alert('a', 'b', 'c', 'd');
        $this->assertFalse($result);
    }


    public function test_no_teacher_enrolled() {

        //$this->expectException(Exception::class);
        $this->resetAfterTest(true);

        $sistemas = $this->getDataGenerator()->create_user(array('username'=>'sistemas1008'));

        $student = $this->getDataGenerator()->create_user();
        $professor = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();

        // enrol student
        $this->getDataGenerator()->enrol_user($student->id, $course->id);
        $teacherroleid = 3;
        $this->getDataGenerator()->enrol_user($professor->id, $course->id, $teacherroleid);

        $result = $this->observer->send_email_alert(
            $student->id, 3, 2.9, $course->id
        );
        $this->assertFalse($result);
    
    }

}
