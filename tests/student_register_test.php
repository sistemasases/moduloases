<?php 

class PruebaTest extends advanced_testcase {
    
    /**
     * Creacion de usuario con datos enviados de manera correcta
     * Se espera que retorne el id del usuario creado
     *
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */
    public function test_create_mdlUser() {
        global $CFG;
        require_once(__DIR__.'/../managers/student_profile/studentprofile_lib.php');
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
     * Creacion de usuario en la tabla mdl_user con datos vacios
     * Se espera que retorne una excepcion
     *
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_create_null_mdlUser(){
        global $CFG;
        require_once(__DIR__.'/../managers/student_profile/studentprofile_lib.php');
        
        $this->resetAfterTest(true);

        $this->expectException(Exception::class);
        $result = save_mdl_user("", "", "", "", "");
    }

    /**
     * Creacion de usuario en la tabla mdl_talentospilos_usuario con datos correctos
     * Se espera que retorne el id del usuario registrado
     *************************EN PROCESO DE CREACION***************************************************************************
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function prueba(){
        global $CFG;
        require_once(__DIR__.'/../managers/student_profile/studentprofile_lib.php');

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
     * Almacenamiento de datos economicos del estudiante, se envian los datos 
     * de manera correcta. 
     * 
     * Se espera que retorne el id del registro creado
     * 
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_insert_economicsData(){
        global $CFG;
        require_once(__DIR__.'/../managers/student_profile/studentprofile_lib.php');

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
        global $CFG;
        require_once(__DIR__.'/../managers/student_profile/studentprofile_lib.php');

        $this->resetAfterTest(true);

        $data = [];
        $estrato = 3;
        $id_ases = 14809;
        
            
        $this->expectException(Exception::class);
        $result = insert_economics_data($data, $estrato, $id_ases);
    }


    /**
     * Almacenamiento de datos academicos del estudiante, se envian los datos 
     * de manera correcta. 
     * 
     * Se espera que retorne el id del registro creado
     * 
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_insert_academicsData(){
        global $CFG;
        require_once(__DIR__.'/../managers/student_profile/studentprofile_lib.php');

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
        global $CFG;
        require_once(__DIR__.'/../managers/student_profile/studentprofile_lib.php');

        $this->resetAfterTest(true);

        $data = [];
        $programa = 3743;
        $titulo = "bachiller";
        $observacion = "ninguna";
        $id_ases = "";
            
        $this->expectException(Exception::class);
        $result = insert_academics_data($data, $programa, $titulo, $observacion, $id_ases);
    }

    /**
     * Almacenamiento de datos de discapacidad del estudiante, se envian los datos 
     * de manera correcta. 
     * 
     * Se espera que retorne el id del registro creado
     * 
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_insert_discapacityData(){
        global $CFG;
        require_once(__DIR__.'/../managers/student_profile/studentprofile_lib.php');

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
        global $CFG;
        require_once(__DIR__.'/../managers/student_profile/studentprofile_lib.php');

        $this->resetAfterTest(true);

        $data = [];
        $id_ases = "";
            
        $this->expectException(Exception::class);
        $result = insert_disapacity_data($data,$id_ases);
    }

    /**
     * Almacenamiento de datos de servicio de salud del estudiante, se envian los datos 
     * de manera correcta. 
     * 
     * Se espera que retorne el id del registro creado
     * 
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_insert_HealtServiceData(){
        global $CFG;
        require_once(__DIR__.'/../managers/student_profile/studentprofile_lib.php');

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
        global $CFG;
        require_once(__DIR__.'/../managers/student_profile/studentprofile_lib.php');

        $this->resetAfterTest(true);

        $data = [];
        $eps = "Asmet salud";
        $id_ases = 14809;

        
        $this->expectException(Exception::class);
        $result = insert_health_service($data,$eps,$id_ases);
    }

    /**
     * Almacenamiento de datos que se encuentran en la seccion 2 del formulario
     * y hacen parte de la tabla talentospilos_usuario
     * 
     * Se espera que retorne true luego de almacenar los datos
     * 
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_data_step2(){
        global $CFG;
        require_once(__DIR__.'/../managers/student_profile/studentprofile_lib.php');

        $this->resetAfterTest(true);

        $result = save_data_user_step2(14662, 3, 0, json_encode(["soraya","mama"]));

        $this->assertTrue($result);

    }

        /**
     *  Almacenamiento de datos que se encuentran en la seccion 2 del formulario
     * y hacen parte de la tabla talentospilos_usuario, enviando el id del usuario vacio
     * 
     * Se espera que retorne una excepcion, puesto que el id es crucial 
     * para poder almacenar los datos
     * 
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_data_error_step2(){
        global $CFG;
        require_once(__DIR__.'/../managers/student_profile/studentprofile_lib.php');

        $this->resetAfterTest(true);

        $this->expectException(Exception::class);
        $result = save_data_user_step2("", 3, 0, json_encode([["key_input"=>"vive","val_input"=>"2"]]));

    }

    /**
     * Almacenamiento de datos que se encuentran en la seccion 3 del formulario
     * y hacen parte de la tabla talentospilos_usuario
     * 
     * Se espera que retorne true luego de almacenar los datos
     * 
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_data_step3(){
        global $CFG;
        require_once(__DIR__.'/../managers/student_profile/studentprofile_lib.php');

        $this->resetAfterTest(true);

        $result = save_data_user_step3(14662, 270, 2015, "colegio prueba", 126);

        $this->assertTrue($result);

    }

        /**
     *  Almacenamiento de datos que se encuentran en la seccion 3 del formulario
     * y hacen parte de la tabla talentospilos_usuario, enviando el id del usuario vacio
     * 
     * Se espera que retorne una excepcion, puesto que el id es crucial 
     * para poder almacenar los datos
     * 
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_data_error_step3(){
        global $CFG;
        require_once(__DIR__.'/../managers/student_profile/studentprofile_lib.php');

        $this->resetAfterTest(true);

        $this->expectException(Exception::class);
        $result = save_data_user_step3("", 270, 2015, "colegio prueba", 126);

    }

    /**
     * Almacenamiento de datos que se encuentran en la seccion 4 del formulario
     * y hacen parte de la tabla talentospilos_usuario
     * 
     * Se espera que retorne true luego de almacenar los datos
     * 
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_data_step4(){
        global $CFG;
        require_once(__DIR__.'/../managers/student_profile/studentprofile_lib.php');

        $this->resetAfterTest(true);

        $result = save_data_user_step4(14662, 9);

        $this->assertTrue($result);

    }

        /**
     *  Almacenamiento de datos que se encuentran en la seccion 4 del formulario
     * y hacen parte de la tabla talentospilos_usuario, enviando el id del usuario vacio
     * 
     * Se espera que retorne una excepcion, puesto que el id es crucial 
     * para poder almacenar los datos
     * 
     * @author Dilan Polanco <dilan.polanco@correounivalle.edu.co>     
     */

    public function test_data_error_step4(){
        global $CFG;
        require_once(__DIR__.'/../managers/student_profile/studentprofile_lib.php');

        $this->resetAfterTest(true);


        $this->expectException(Exception::class);
        $result = save_data_user_step4("", "");

    }

    
}
