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
 * Pruebas unitarias concernientes al formulario de discapacidad.
 *
 * @package    block_ases
 * @category   test
 * @copyright  2021 Dilam Stive Polanco <dilan.polanco@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @group block_ases
 */

global $CFG;
require_once(__DIR__.'/../managers/student_profile/studentprofile_lib.php');

class PruebaTest extends advanced_testcase {
    
    /**
     * Creacion de usuario con datos enviados de manera correcta en la tabla {user}
     * Se espera que retorne el id del usuario creado
     *
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */
    public function test_create_mdlUser() {
        
        //global $USER;

        /**
        * Campos obligatorios talentospilos_usuario:
        */
        $this->resetAfterTest(true);
        $username = "pruebauser";
        $nombre = "Dilam";
        $apellido = "Polanco";
        $emailI="prueba@gmial.com";
        $pass = "Passs239D";

        $result = save_mdl_user($username, $nombre, $apellido, $emailI, $pass);

        $this->assertIsInt($result);

    }

    /**
     * Creacion de usuario en la tabla {user} con datos vacios
     * Se espera que retorne una excepcion
     *
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_create_null_mdlUser(){
        
        
        $this->resetAfterTest(true);

        $this->expectExceptionMessage('Error al momento de crear user de moodle');
        $result = save_mdl_user("", "", "", "", "");
    }

    /**
     * Creacion de usuario con datos enviados de manera correcta en la tabla {talentospilos_usuario}
     * Se espera que retorne el id del usuario creado
     *
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_create_ases_user(){
        

        $this->resetAfterTest(true);

        $data = file_get_contents(__DIR__ . '/fixtures/user_data.json');
        $data = json_decode($data);

        $deportes = [];
        $familia = [];
        $programa = 91;
        $id_moodle = 13172;
        $json_detalle = [];
        $result = save_data($data,$deportes,$familia,$programa,$id_moodle,$json_detalle);

        $this->assertIsInt($result);

    }

    /**
     * Creacion de usuario en la tabla {talentospilos_usuario} con datos vacios
     * Se espera que retorne una excepcion
     *
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_create_error_ases_user(){
        

        $this->resetAfterTest(true);

        $data = [];

        $deportes = [];
        $familia = [];
        $programa = 91;
        $id_moodle = 13172;
        $json_detalle = [];

        $this->expectExceptionMessage('Error al momento de crear ases user');
        $result = save_data($data,$deportes,$familia,$programa,$id_moodle,$json_detalle);

    }

     /**
     * Almacenamiento de datos economicos del estudiante, se envian los datos 
     * de manera correcta para insertar en la tabla {talentospilos_economics_data}. 
     * 
     * Se espera que retorne el id del registro creado
     * 
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_insert_economicsData(){
        

        $this->resetAfterTest(true);

        $data = file_get_contents(__DIR__ . '/fixtures/economics_data.json');
        $data = json_decode($data);
        $estrato = 3;
        $id_ases = 14662;
            

        $result = insert_economics_data($data, $estrato, $id_ases);

        $this->assertIsInt($result);
    }

     /**
     * Almacenamiento de datos economicos del estudiante, se envian los datos 
     * de manera incorrecta debido a que el array $data y el $id_ases se envian vacios. 
     * 
     * Se espera que retorne una excepcion debido a que estos datos son indispensables 
     * para la creacion del registro.
     * 
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_insert_error_economicsData(){
        

        $this->resetAfterTest(true);

        $data = [];
        $estrato = 3;
        $id_ases = 14809;
        
            
        $this->expectExceptionMessage('Error al momento de insertar datos economicos');
        $result = insert_economics_data($data, $estrato, $id_ases);
    }


    /**
     * Almacenamiento de datos academicos del estudiante, se envian los datos 
     * de manera correcta para insertar en la tabla {talentospilos_academics_data}. 
     * 
     * Se espera que retorne el id del registro creado
     * 
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_insert_academicsData(){
        

        $this->resetAfterTest(true);

        $data = file_get_contents(__DIR__ . '/fixtures/academics_data.json');
        $data = json_decode($data);
        $programa = 3743;
        $titulo = "bachiller";
        $observacion = "ninguna";
        $id_ases = 14809;

        $result = insert_academics_data($data, $programa, $titulo, $observacion, $id_ases);

        $this->assertIsInt($result);
    }

    /**
     * Almacenamiento de datos academicos del estudiante, se envian los datos 
     * de manera incorrecta debido a que el array $data y el $id_ases se envian vacios. 
     * 
     * Se espera que retorne una excepcion debido a que estos datos son indispensables 
     * para la creacion del registro.
     * 
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_insert_error_academicsData(){
        

        $this->resetAfterTest(true);

        $data = [];
        $programa = 3743;
        $titulo = "bachiller";
        $observacion = "ninguna";
        $id_ases = "";
            
        $this->expectExceptionMessage('Error al momento de insertar datos academicos');
        $result = insert_academics_data($data, $programa, $titulo, $observacion, $id_ases);
    }

    /**
     * Almacenamiento de datos de discapacidad del estudiante, se envian los datos 
     * de manera correcta para insertar en la tabla {talentospilos_discapacity_dt}. 
     * 
     * Se espera que retorne el id del registro creado
     * 
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_insert_discapacityData(){
        

        $this->resetAfterTest(true);

        $data = file_get_contents(__DIR__ . '/fixtures/discapacity_data.json');
        $data = json_decode($data);
        $id_ases = 14809;

        $result = insert_disapacity_data($data,$id_ases);

        $this->assertIsInt($result);
    }

    /**
     * Almacenamiento de datos de discapacidad del estudiante, se envian los datos 
     * de manera incorrecta debido a que el array $data y el $id_ases se envian vacios. 
     * 
     * Se espera que retorne una excepcion debido a que estos datos son indispensables 
     * para la creacion del registro.
     * 
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_insert_error_discapacityData(){
        

        $this->resetAfterTest(true);

        $data = [];
        $id_ases = "";
            
        $this->expectExceptionMessage('Error al momento de insertar datos de discapacidad');
        $result = insert_disapacity_data($data,$id_ases);
    }

    /**
     * Almacenamiento de datos de servicio de salud del estudiante, se envian los datos 
     * de manera correcta para insertar en la tabla {talentospilos_healt_data}. 
     * 
     * Se espera que retorne el id del registro creado
     * 
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_insert_HealtServiceData(){
        

        $this->resetAfterTest(true);

        $data = file_get_contents(__DIR__ . '/fixtures/healt_service_data.json');
        $data = json_decode($data);
        $eps = "Asmet salud";
        $id_ases = 14809;

        $result = insert_health_service($data,$eps,$id_ases);

        $this->assertIsInt($result);
    }

     /**
     * Almacenamiento de datos de servicio de salud del estudiante, se envian los datos 
     * de manera incorrecta debido a que el array $data y el $id_ases se envian vacios. 
     * 
     * Se espera que retorne una excepcion debido a que estos datos son indispensables 
     * para la creacion del registro.
     * 
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_insert_error_HealtServiceData(){
        

        $this->resetAfterTest(true);

        $data = [];
        $eps = "Asmet salud";
        $id_ases = 14809;

        
        $this->expectExceptionMessage('Error al momento de insertar datos de servicio de salud');
        $result = insert_health_service($data,$eps,$id_ases);
    }

    /**
     * Almacenamiento de datos que se encuentran en la seccion 2 del formulario
     * y hacen parte de la tabla {talentospilos_usuario}
     * 
     * Se espera que retorne true luego de almacenar los datos
     * 
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_data_step2(){
        

        $this->resetAfterTest(true);

        $result = save_data_user_step2(14662, 3, 0, json_encode(["soraya","mama"]));

        $this->assertTrue($result);

    }

        /**
     *  Almacenamiento de datos que se encuentran en la seccion 2 del formulario
     * y hacen parte de la tabla {talentospilos_usuario}, enviando el id del usuario vacio
     * 
     * Se espera que retorne una excepcion, puesto que el id es crucial 
     * para poder almacenar los datos
     * 
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_data_error_step2(){
        

        $this->resetAfterTest(true);

        $this->expectExceptionMessage('Error al momento de insertar datos del step2');
        $result = save_data_user_step2("", 3, 0, json_encode([["key_input"=>"vive","val_input"=>"2"]]));

    }

    /**
     * Almacenamiento de datos que se encuentran en la seccion 3 del formulario
     * y hacen parte de la tabla {talentospilos_usuario}
     * 
     * Se espera que retorne true luego de almacenar los datos
     * 
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_data_step3(){
        

        $this->resetAfterTest(true);

        $result = save_data_user_step3(14662, 270, 2015, "colegio prueba", 126);

        $this->assertTrue($result);

    }

        /**
     *  Almacenamiento de datos que se encuentran en la seccion 3 del formulario
     * y hacen parte de la tabla {talentospilos_usuario}, enviando el id del usuario vacio
     * 
     * Se espera que retorne una excepcion, puesto que el id es crucial 
     * para poder almacenar los datos
     * 
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_data_error_step3(){
        

        $this->resetAfterTest(true);

        $this->expectExceptionMessage('Error al momento de insertar datos del step3');
        $result = save_data_user_step3("", 270, 2015, "colegio prueba", 126);

    }

    /**
     * Almacenamiento de datos que se encuentran en la seccion 4 del formulario
     * y hacen parte de la tabla {talentospilos_usuario}
     * 
     * Se espera que retorne true luego de almacenar los datos
     * 
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_data_step4(){
        

        $this->resetAfterTest(true);

        $result = save_data_user_step4(14662, 9);

        $this->assertTrue($result);

    }

        /**
     *  Almacenamiento de datos que se encuentran en la seccion 4 del formulario
     * y hacen parte de la tabla {talentospilos_usuario}, enviando el id del usuario vacio
     * 
     * Se espera que retorne una excepcion, puesto que el id es crucial 
     * para poder almacenar los datos
     * 
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_data_error_step4(){
        

        $this->resetAfterTest(true);


        $this->expectExceptionMessage('Error al momento de insertar datos del step4');
        $result = save_data_user_step4("", "");

    }

    
}
