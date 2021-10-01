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
 * Pruebas unitarias concernientes al reporte general.
 *
 * @package    block_ases
 * @category   test
 * @copyright  2021 David Santiago Cortés <david.cortes@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @group block_ases
 */
class block_ases_ases_report_testcase extends advanced_testcase {

    private $user; 

    /**
     * Creates a new randomized user in {user}, and sets the current
     * $USER as the same randomized user generated previously.
     *
     * Runs before any test inside this class.
     */
    protected function setUp() {
        require_once(__DIR__.'/../core/module_loader.php');
        module_loader('periods');
        
        global $DB;
        global $USER;
        $dbman = $DB->get_manager();
        
        $this->resetAfterTest(true);
        
        $fecha_inicio = new DateTime('2021-02-01', new DateTimeZone('America/Bogota'));
        $fecha_fin = new DateTime('2021-09-30', new DateTimeZone('America/Bogota'));
        $period = core_periods_create_period('2021A', '2021-02-01', '2021-09-30', 450299);
        
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        
        $dataobject = [
            'id_usuario' => $USER->id,
            'id_rol' => 6,
            'estado' => 1,
            'id_semestre' => $period,
            'id_instancia' => 450299
        ];

        $DB->insert_record('talentospilos_user_rol', $dataobject);

        $ases_report_obj = [
            'nombre_func' => 'ases_report',
            'descripcion' => 'Reporte general de estudiantes'
        ];
        $id_func = $DB->insert_record('talentospilos_funcionalidad', $ases_report_obj);
        
        $table = new xmldb_table('talentospilos_accion');
        $field = new xmldb_field('id_funcionalidad', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $dbman->add_field($table, $field);

        $action = [
            'nombre_accion' => 'view_summary_report',
            'descripcion' => 'lorem ipsum dolor sit amet',
            'estado' => 1,
            'id_funcionalidad' => $id_func
        ];
        $actionid = $DB->insert_record('talentospilos_accion', $action);
        

        $table = new xmldb_table('talentospilos_permisos_rol');
        $dbman->drop_table($table);


        $table->add_field( 'id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field( 'id_accion', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('id_rol', XMLDB_TYPE_INTEGER, '20', null, null, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        
        $dbman->create_table($table);

        $perm = [
            'id_rol' => 6,
            'id_accion' => $actionid,
        ];

        $DB->insert_record('talentospilos_permisos_rol', $perm);
    }
    
     
    public function test_user_can_view() {
        require_once(__DIR__.'/../managers/validate_profile_action.php');
        global $USER;

        $result = authenticate_user_view($USER->id, 450299, 'ases_report');
        $this->assertObjectNotHasAttribute('message', $result);
    }

    public function test_user_can_not_view() {
        require_once(__DIR__.'/../managers/validate_profile_action.php');
        global $USER;

        $result = authenticate_user_view(42, 450299, 'ases_report');
        $this->assertObjectHasAttribute('message', $result);
    }

    public function test_throws_exception_with_null_user() {
        require_once(__DIR__.'/../managers/validate_profile_action.php');
        global $USER;

        $this->expectException(Exception::class);
        $result = authenticate_user_view(null, 450299, 'ases_report');
    }

    public function test_throws_exception_with_null_instance() {
        require_once(__DIR__.'/../managers/validate_profile_action.php');
        global $USER;

        $this->expectException(Exception::class);
        $result = authenticate_user_view($USER->id, null, 'ases_report');
    }
}
