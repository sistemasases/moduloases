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
 * Pruebas para la función student_profile_carga_historico()
 * del lib de la ficha de estudiantes.
 * @see managers/student_profile/studentprofile_lib.php
 * @author David S. Cortés <david.cortes@correounivalle.edu.co>
 * @group block_ases
 */

global $CFG;
require_once(__DIR__ . '/../managers/student_profile/studentprofile_lib.php');

class block_ases_student_profile_carga_historico_testcase extends advanced_testcase
{
    /**
     * Verifica que retorna falso si se llama a la función
     * con argumentos que no son numericos.
     */
    public function test_invalid_arguments()
    {
        $result = student_profile_carga_historico('0x00', null);
        $this->assertFalse($result);
    }

    //public function test_block_does_not_exists()
    //{
    //    $this->resetAfterTest(true);
    //    $_SERVER['REQUEST_URI'] = '/moodle/blocks/ases/view/student_profile.php?courseid=25643&instanceid=450299';

    //    $user = $this->getDataGenerator()->create_user();
    //    $this->setUser($user);

    //    $result = student_profile_carga_historico($user->id, '450299', 'view_button_historic');
    //}
}
