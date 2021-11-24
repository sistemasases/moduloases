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
    public function test_is_ases()
    {
        $this->resetAfterTest(true); 

        // Arrange
        $student = $this->getDataGenerator()->create_user();
        $this->create_test_student($student->id); 

        $result = $this->observer->isASES($student->id);

        $this->assertTrue($result);
    }

    /**
     * This function aids test_is_ases() by creating
     * a student, i.e: making the respective inserts into
     * talentospilos_usuario and talentospilos_user_extended
     *
     * @param nummeric: $user_id, the moodle user id of the 
     * student. 
     */
    private function create_test_student($user_id)
    {
       /**
        * Campos obligatorios talentospilos_usuario:
        */
        $mandatory = [
            "num_doc_ini" => '',
            "tipo_doc_ini" => '',
            "tipo_doc" => '',
            "num_doc" => '',
            "id_ciudad_ini" => '',
            "id_ciudad_res" => '',
            "fecha_nac" => '',
            "id_ciudad_nac" => '',
            "sexo" => '',
            "estado" => '',
            "id_discapacidad" => '',
            "ayuda_disc" => '',
            "estado_ases" => ''
        ];

        $csv = array_map('str_getcsv', file(__DIR__ . '/fixtures/dummy-user.csv')); 
        $dataobject = array_combine(
            array_values($csv[0]), // Keys
            array_values($csv[1]) // Values
        );

        // only use mandatory fields
        $result = array_intersect_key($dataobject, $mandatory);

        try {
            global $DB;
            $id_ases_user = $DB->insert_record('talentospilos_usuario', $result);

            // insert to {talentospilos_user_extended}
            $csv2 = array_map('str_getcsv', file(__DIR__ . '/fixtures/dummy-extended-user.csv')); 
            $dataobject2 = array_combine(
                array_values($csv2[0]), // Keys
                array_values($csv2[1]) // Values
            );
            $dataobject2['id_ases_user'] = $id_ases_user;
            $dataobject2['id_moodle_user'] = $user_id;
            $DB->insert_record('talentospilos_user_extended', $dataobject2);

            // insert to {talentospilos_est_estadoases}
            $csv3 = array_map('str_getcsv', file(__DIR__ . '/fixtures/dummy-estadoases.csv')); 
            $dataobject3 = array_combine(
                array_values($csv3[0]), // Keys
                array_values($csv3[1]) // Values
            );
            $dataobject3['id_estudiante'] = $id_ases_user;
            $dataobject3['id_estado_ases'] = $this->create_estados_ases();
            $DB->insert_record('talentospilos_est_estadoases', $dataobject3);

        } catch(Exception $ex) {
            throw new exception($ex->getMessage());
        }
    }

    /**
     * Aids create_student_ases, its job is to insert into {talentospilos_estados_ases}
     * the tracking type "seguimiento" which isASES() checks for.
     *
     * @returns the id of the inserted record, null if an error ocurred.
     */
    private function create_estados_ases()
    {
        try {
            global $DB;
            $dataobject = [
                "nombre" => "seguimiento",
                "descripcion" => "Se le realiza seguimiento en la estrategia ASES"
            ];
            $estado_id = $DB->insert_record('talentospilos_estados_ases', $dataobject);
            return $estado_id;
        } catch(Exception $ex) {
            throw new exception($ex->getMessage());
        }
    }
}
