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
 * Pruebas unitarias para la función isASES dentro del observador.
 * @author David S. Cortés <david.cortes@correounivalle.edu.co>
 * @see classes/observer.php
 * @group block_ases
 */

require_once(__DIR__ . '/../classes/observer.php');
class observer_is_ases_testcase extends advanced_testcase
{

    private $observer;

    protected function setUp()
    {
        $this->observer = new block_ases_observer();
    }

    /**
     * Tests wether isASES() returns false when
     * the given user id does not corresponds to
     * an ASES student
     */
    public function test_user_does_not_exists() 
    {
        $result = $this->observer->isASES(42); 
        $this->assertFalse($result);
    }

    /**
     * Tests that isASES() raises an exception
     * when an invalid data type is passed as 
     * user id.
     */
    public function test_invalid_userid()
    {
       $this->expectException(Exception::class); 
       $this->observer->isASES('0xff');
    }

    /**
     * Tests that isASES() returns true when the provided
     * user id corresponds to an active ASES student.
     */
    //public function test_is_ases()
    //{
    //    $this->resetAfterTest(true); 

    //    $student = $this->getDataGenerator()->create_user();

    //    $query = 
    //        "INSERT INTO {talentospilos_user_extended}
    //        VALUES " 
    //}
}
