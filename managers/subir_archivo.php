<<<<<<< HEAD
<?php
require_once(dirname(__FILE__). '/../../../config.php');

require_once('MyException.php');
require_once('dateValidator.php');
require_once('query.php');

if (isset($_FILES['csv_file'])){

   try{
      $archivo = $_FILES['csv_file'];
      $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
      $nombre = $archivo['name'];
      $varSelector = $_POST['typefile_select'];
      if ($extension !== 'csv') throw new MyException("El archivo ".$archivo['name']." no corresponde al un archivo de tipo CSV. Por favor verifícalo"); 
      if (!move_uploaded_file($archivo['tmp_name'], "../view/archivos_subidos/$nombre")) throw new MyException("Error al cargar el archivo.");
      ini_set('auto_detect_line_endings', true);
      if (!($handle = fopen("../view/archivos_subidos/$nombre", 'r'))) throw new MyException("Error al cargar el archivo ".$archivo['name'].". Es posible que el archivo se encuentre dañado");
      
      //se incia la transaccion en bd
       pg_query("BEGIN") or die("Could not start transaction\n");
      //$transaction = $DB->start_delegated_transaction();

      if($varSelector == "Municipio"){
         
         global $DB;
         $record = new stdClass();
         $count = 0;
         $array_id = array();
         $array_data = array();
         $line_count =1;
         
         while($data = fgetcsv($handle, 100, ","))
         {  
            array_push($array_data, $data);
            
            $query = "SELECT id FROM {talentospilos_departamento} WHERE codigodivipola = ".intval($data[1]).";";
            
            $result = $DB->get_record_sql($query);
            
            if(!$result){
               throw new MyException("Por favor Revisa la línea ".$line_count.".<br>El codigo de División Política del departamento ".$data[1]." asociado al  municipio ".$data[2]." no se encuentra en la base de datos");
            }
            array_push($array_id, $result->id);
            $line_count+=1;
         }
         
         foreach ($array_data as $dat)
         {
            $record->codigodivipola = $dat[0];
            $record->cod_depto = $array_id[$count];
            $record->nombre = $dat[2];
            $DB->insert_record('talentospilos_municipio', $record, false);
            $count += 1;
         }
         
         $respuesta = 1;
         echo $respuesta;
      }
      else if($varSelector == "Facultad"){
         global $DB;
         //esta tabla no depende de otra
         $record = new stdClass();
         $count = 0;
         
         while($data = fgetcsv($handle, 50, ",")){
            $record->cod_univalle = $data[0];
            $record->nombre = $data[1];
            $DB->insert_record('talentospilos_facultad', $record, false);
           $count += 1;
         }

         $respuesta = 1;
         echo $respuesta;
         
      }
      else if($varSelector == "Departamento"){
         global $DB;
         // esta tabla no depende de otra
         $record = new stdClass();
         $count = 0;
         
         
         while($data = fgetcsv($handle, 1000, ","))
         {
            $record->codigodivipola = $data[0];
            $record->nombre = $data[1];
            
            $DB->insert_record('talentospilos_departamento', $record, false);
            $count+=1;
         }
         $respuesta = 1;
         echo $respuesta;
      }
      else if($varSelector == "Sede"){
         global $DB;
         $record = new stdClass();
         $count = 0;
         $array_id = array();
         $array_data = array();
         $line_count=1;
         
         while($data = fgetcsv($handle, 100, ","))
         {
            array_push($array_data, $data);
            
            $query = "SELECT id FROM {talentospilos_municipio} WHERE codigodivipola ='".intval($data[0])."';";
            $result = $DB->get_record_sql($query);
            if(!$result){
               throw new MyException("Por favor revisa la linea".$line_count.".<br>El codigo de División Política de la ciudad ".$data[0]." asociado a la sede".$data[2]." no se encuentra en la base de datos");
            }
            array_push($array_id, $result->id);
            $line_count+=1;
         }
      
         foreach ($array_data as $data) 
         { 
            $record->id_ciudad = $array_id[$count];
            $record->cod_univalle = $data[1];
            $record->nombre = $data[2];
            
            $DB->insert_record('talentospilos_sede', $record, false);
           $count+=1;
         }
         $respuesta = 1;
         echo $respuesta;
      }
      else if($varSelector == "Programa"){
         global $DB;
         $record = new stdClass();
         $count = 0;
         $array_id_sede = array();
         $array_id_fac = array();
         $array_data = array();
         $line_count = 1;
         
         
         while($data = fgetcsv($handle, 1000, ","))
         {
            
            array_push($array_data, $data);
            
            $query = "SELECT id FROM {talentospilos_sede} WHERE cod_univalle ='".intval($data[3])."';";
            $result = $DB->get_record_sql($query);
            if(!$result){
               throw new MyException("Por favor revisa la linea ".$line_count.".<br>El codigo Univalle de la sede ".$data[0]." asociado al programa ".$data[2]." no se encuentra en la base de datos");
            }
            array_push($array_id_sede, $result->id);
            
            //se verifica el codigo de l facultad
            $query = "SELECT id FROM {talentospilos_facultad} WHERE cod_univalle ='".$data[4]."';";
            $result = $DB->get_record_sql($query);
            if(!$result){
               throw new MyException("El codigo Univalle de la facultad ".intval($data[4])." asociado al programa ".$data[2]." no se encuentra en la base de datos. linea ".$line_count." ".$data[0]."-".$data[1]."-".$data[2]."-".$data[3]."-".$data[4]."");
            }
            $line_count+=1;
            array_push($array_id_fac, $result->id);
         }
       
         foreach($array_data as $data)
         { 
               $record->codigosnies = $data[0];
               $record->cod_univalle = $data[1];
               $record->nombre = $data[2];
               $record->id_sede = $array_id_sede[$count];
               $record->id_facultad = $array_id_fac[$count];
               $record->jornada = $data[5];
               
               $DB->insert_record('talentospilos_programa', $record, false);
              $count+=1;
         }
         $respuesta = 1;
         echo $respuesta;
      }
      else if($varSelector == "Discapacidad"){
         global $DB;
         //no depende de ninguna tabla
         $record = new stdClass();
         $count = 0;
        
         while($data = fgetcsv($handle, 100, ","))
         { 
              $record->codigo_men = $data[0];
              $record->nombre = $data[1];
               
              $DB->insert_record('talentospilos_discap_men', $record, false);
              $count+=1;
         }
         $respuesta = 1;
         echo $respuesta;
      }
      else if($varSelector == "Usuario"){
         global $DB;
         $record = new stdClass();
         $dateValidator = new dateValidator();
         $count = 0;
         $array_id_ciudadini = array(); //ciudad incial
         $array_id_ciudadres = array();//ciudad recidencia
         $array_id_ciudadnac = array(); //ciudad nacimiento
         $array_id_discap = array();  //discapacidad
         $array_id_talentos =  array();  
         $array_data = array();
         $line_count=0;
         $exists = true; //variable que verifica la existencia de un registro
         
         
         while($data = fgetcsv($handle, 10000, ","))
         {  
            //se verifica la existencia del registro en la base de datos para determinar si es un actualización o un nuevo registro
            $query = "SELECT id FROM {talentospilos_usuario} WHERE num_doc_ini ='".intval($data[1])."';";
            $result = $DB->get_record_sql($query);
            
            
            if(!$result) {
               $exists = false;
            }else{
               $array_id_talentos[$line_count] = $result->id;
            }
            
            
            //se verifica el formato de la fecha de nacimiento
            $dateValidator -> validateDateStyle($data[16]);
            
            
            //se almacena la informacion de toda linea leida
            array_push($array_data, $data);
               
               
            //en caso de que no exista se obtiene la información requerida para la nueva insercción 
            if(!$exists){
               //se verifica la existencia de la ciudad incial
               $query = "SELECT id FROM {talentospilos_municipio} WHERE codigodivipola ='".intval($data[6])."';";
               $result = $DB->get_record_sql($query);
               if(!$result){
                  throw new MyException("Por favor revisa la linea ".($line_count + 1).".<br>El codigo de Divsion politica ".$data[6]." asociado a la ciudad de procedencia del estudiante con número de  identificación: ".$data[3]." no se encuentra en la base de datos.".$data[0]."-".$data[1]."-".$data[2]."-".$data[3]."-".$data[4]."-".$data[5]."-".$data[6]."-");
               }
               $array_id_ciudadini[$line_count] = $result->id;
               
               //se verifica la ciudad de recidencia
               $query = "SELECT id FROM {talentospilos_municipio} WHERE codigodivipola ='".intval($data[10])."';";
               $result = $DB->get_record_sql($query);
               if(!$result){
                  throw new MyException("Por favor revisa la linea ".($line_count + 1).".<br>El codigo de division politica ".$data[10]." asociado a la ciudad de recidencia del estudiante con número de identificación:".$data[3]." no se encuentra en la base de datos");
               }
               $array_id_ciudadres[$line_count] = $result->id;
               
               
               //se verifica la ciudad de nacimiento
               $query = "SELECT id FROM {talentospilos_municipio} WHERE codigodivipola ='".intval($data[17])."';";
               $result = $DB->get_record_sql($query);
               if(!$result){
                  throw new MyException("Por favor revisa la linea ".($line_count + 1).".<br>El codigo de division politica ".$data[17]." asociado a la ciudad de nacimiento del estudiante con número de identificación:".$data[3]." no se encuentra en la base de datos");
               }
               $array_id_ciudadnac[$line_count] = $result->id;
               
               //se verifica el codigo de discapacidad
               $query = "SELECT id FROM {talentospilos_discap_men} WHERE codigo_men ='".intval($data[24])."';";
               $result = $DB->get_record_sql($query);
               if(!$result){
                  throw new MyException("Por favor revisa la linea ".($line_count + 1).".<br>El codigo  ".$data[24]." asociado a la discapacidad del estudiante con número de identificación:".$data[3]." no se encuentra en la base de datos");
               }
               $array_id_discap[$line_count] = $result->id;
            }
            
            $line_count+=1;
         }
         foreach ($array_data as $data)
         {
               $record->tipo_doc_ini = $data[0];
               $record->num_doc_ini = intval($data[1]);
               $record->tipo_doc = $data[2];
               $record->num_doc = intval($data[3]);
               $record->dir_ini = $data[4];
               $record->barrio_ini = $data[5];
               
               $record->tel_ini = $data[7];
               $record->direccion_res = $data[8];
               $record->barrio_res = $data[9];
               
               $record->tel_res = $data[11];
               $record->celular = $data[12];
               $record->emailpilos = $data[13];
               $record->acudiente = $data[14];
               $record->tel_acudiente = $data[15];
               $record->fecha_nac = $data[16];
               
               $record->sexo = $data[18];
               $record->colegio = $data[19];
               $record->estamento = $data[20];
               $record->observacion = $data[21];
               $record->estado = $data[22];
               $record->estado_icetex = 0;
               $record->grupo = $data[23];
               
               $record->ayuda_discap = $data[25];
               
               //se realiza la inserccion o actualizacion pertinente
               
               if($exists){
                  $record->id_ciudad_ini = $data[6];
                  $record->id_ciudad_res = $data[10];
                  $record->id_ciudad_nac = $data[17];
                  $record->id_discapacidad = $data[24];
                  $record->id = $array_id_talentos[$count];
                  $DB->update_record('talentospilos_usuario',$record);
               }else{
                  $record->id_ciudad_ini = $array_id_ciudadini[$count];
                  $record->id_ciudad_res = $array_id_ciudadres[$count];
                  $record->id_ciudad_nac = $array_id_ciudadnac[$count];
                  $record->id_discapacidad = $array_id_discap[$count];
                  $DB->insert_record('talentospilos_usuario', $record, false);
               }
         
               $count+=1;
         }
         $respuesta = 1;
         echo $respuesta;
         
      }else if ($varSelector =="user"){
         
         global $DB;
         
         $count = 0;
         $array_username = array();
         $array_programa = array();
         $array_data = array();
         $line_count = 1;
         $act= "";
         $query="";
         
         while($data = fgetcsv($handle, 500, ","))
         {  
            $temp_array = array();
            //se verifica el número de documento
            $query = "SELECT id FROM {talentospilos_usuario} WHERE num_doc = '".intval($data[0])."';";
            $result = $DB->get_record_sql($query);
            
            if(!$result){
               throw new MyException("Por favor revisa la línea ".$line_count.".<br>El número de documento ".$data[0]." no corresponde a un estudiante de pilos");
            }
            //se adiciona el id  de la tabla talentospilos_usuario correspondiente al nuero de documento
            array_push($temp_array, $result->id);
            
            // se verifica el programa
            $query = "SELECT id FROM {talentospilos_programa} WHERE cod_univalle = ".intval($data[2])." AND  jornada='".$data[3]."' AND id_sede = (SELECT id from {talentospilos_sede} WHERE cod_univalle =".intval($data[4]).");";
            $result = $DB->get_record_sql($query);
            
            if(!$result){
               throw new MyException("Por favor revisa la línea ".$line_count.".<br>El programa con las siguientes características; codigo univalle: ".$data[2].", jornada: ".$data[3]."  y sede: ".$data[4]." no existe en la base de datos. ");
            }
            array_push($temp_array, $result->id);
            
            //se verifica el username
            $username = substr($data[1],-7)."-".$data[2];
            $query = "SELECT id FROM {user} WHERE username = '".$username."' ;";
            $result = $DB->get_record_sql($query);
            
            if(!$result){
               throw new MyException("Por favor revisa la linea ".$line_count.".<br>No existe un ususario en moodle para el estudiante con codigo univalle: ".$data[1]." y programa ".$data[2].". ");
            }
            array_push($temp_array, $result->id);
            
            // SE ADICIONA el restode informacion al arreglo temporal el cual tendria (id_talentos,id_programa,id_user, ACTIVO)
            array_push($temp_array, 'ACTIVO');
            
            //SE ADICIONA EL ARRAY TEMPORAL AL arreglo que contiene la informarion general
            array_push($array_data,$temp_array);
            
            $line_count += 1;
         }
         
         //se obtine los id de los campos idtalentos,idprograma,estado
         $result = $DB->get_record_sql("SELECT id FROM {user_info_field} WHERE shortname = 'idtalentos'");
         $idtalentos_field = $result->id;
         
         $result= $DB->get_record_sql("SELECT id FROM {user_info_field} WHERE shortname = 'idprograma'");
         $idprograma_field = $result->id;
         
         $result= $DB->get_record_sql("SELECT id FROM {user_info_field} WHERE shortname = 'estado'");
         $idestado_field = $result->id;
        
         foreach ($array_data as $dat){  
            $record = new stdClass();
            //se verifica si ya el campo está creado asi saber si insertar o actualizar
            $query="select  d.id, f.shortname  from {user_info_data} d inner join {user_info_field} f on d.fieldid= f.id  where (f.shortname='idtalentos' OR f.shortname='idprograma' OR f.shortname='estado') AND userid =".$dat[2]." order by shortname;";
            $result= $DB->get_records_sql($query);
            if(!$result){
               $act .="(".$dat[2].",".$idestado_field.")-";
               //se inserta la info del id del usuario de la tabla talentos en el campo idtalentos asociado a la tabla user
               $record->userid = $dat[2]; //id del usario en moodle
               $record->fieldid = $idtalentos_field;
               //data[0] es el id del usario de tabla talentos
               $record->data = $dat[0];
               $record->dataformat = 0;
               $DB->insert_record('user_info_data', $record);
               
               //se inserta la info del campo idprograma
               $record->userid = $dat[2];
               $record->fieldid = $idprograma_field;
               //data[1] es el id del programa de la tabla talentospilosprograma
               $record->data = $dat[1];
               $record->dataformat = 0;
               $DB->insert_record('user_info_data', $record);
               
               //se inserta la info del campo estado
               $record->userid = $dat[2];
               $record->fieldid = $idestado_field;
               //data[3] es el estado que por defeto es activo
               $record->data = $dat[3];
               $record->dataformat = 0; //campo necesario para guradar coherencia con la tabla user_info_data
               $DB->insert_record('user_info_data', $record);

            
            }else{

               foreach ($result as $value) {
                  $shortname = $value->shortname;
                  
                  if($shortname == 'idtalentos'){

                     $record->id = $value->id; //se asigna el id que correponde a la informacion del campo a actualizar
                     $record->data = $dat[0];   //se actualiza la informacion con la info de la tabla
                     
                  }else if ($shortname == 'idprograma') {

                     $record->id = $value->id; //
                     $record->data = $dat[1];   
                  }else if ($shortname == 'estado') {
                     
                     $record->id = $value->id; 
                     $record->data = $dat[3]; 
                  }
                  $DB->update_record('user_info_data',$record);
               }          
            }
            $count += 1;
         }
         
         $respuesta = 1;
         echo $respuesta;
      }
      // Carga roles
      else if($varSelector == "Roles"){
         global $DB;
         $record = new stdClass();
      
         while($data = fgetcsv($handle, 100, ","))
         {
            $record->nombre_rol = $data[0];
            $record->descripcion = $data[1];
            
            $DB->insert_record('talentospilos_rol', $record, false);
         }
         $respuesta = 1;
         echo $respuesta;
      }
      else if($varSelector == "Funcionalidad"){
         global $DB;
         $record = new stdClass();
     
         while($data = fgetcsv($handle, 100, ","))
         {
            $record->nombre_func = $data[0];
            $record->descripcion = $data[1];
            
            $DB->insert_record('talentospilos_funcionalidad', $record, false);
         }
         $respuesta = 1;
         echo $respuesta;
      }
      else if($varSelector == "Permisos"){
         global $DB;
         $record = new stdClass();

         while($data = fgetcsv($handle, 100, ","))
         {
            $record->permiso = $data[0];
            $record->descripcion = $data[1];
            
            $DB->insert_record('talentospilos_permisos', $record, false);
         }
         $respuesta = 1;
         echo $respuesta;
      }
      else if($varSelector == "Permisos-Rol"){
         global $DB;
         $record = new stdClass();

         while($data = fgetcsv($handle, 100, ","))
         {
            //se obtine los id de los campos permiso, rol, funcionalidad
            $result = $DB->get_record_sql("SELECT id FROM {talentospilos_rol} WHERE  nombre_rol = '".$data[0]."'");
            $record->id_rol = $result->id;

            $result = $DB->get_record_sql("SELECT id FROM {talentospilos_permisos} WHERE permiso = '".$data[1]."'");
            $record->id_permiso = $result->id;

            $result = $DB->get_record_sql("SELECT id FROM {talentospilos_funcionalidad} WHERE nombre_func = '".$data[2]."'");
            $record->id_funcionalidad = $result->id;

            $DB->insert_record('talentospilos_permisos_rol', $record, false);
         }

         $respuesta = 1;
         echo $respuesta;
         
      }else if($varSelector == "Enfasis"){
         global $DB;
         $record =  new stdClass;
         $count = 0;

         while($data = fgetcsv($handle,100,",")){
            
            $record -> nombre = $data[0];
            $record -> descripcion = $data[1];
            $DB->insert_record('talentospilos_enfasis',$record);
            $count += 1;
         }
         $respuesta = 1;
         echo $respuesta;
      }else if($varSelector == "Vocacional"){
         $info =array();
         $count=0;
         $line_count = 1;
         
         while($data = fgetcsv($handle,1000,",")){
            $data_array = new stdClass;
            //se elimina el 20 incial
            $cod = intval($data[0]) - 200000000;
            //$query = "select idtalentos from (SELECT * FROM (SELECT *, id AS id_user FROM {user}) AS userm INNER JOIN (SELECT * FROM {user_info_data} as d INNER JOIN {user_info_field} as f ON d.fieldid = f.id WHERE f.shortname ='idtalentos' and d.data <> '') AS field ON userm.id_user = field.userid ) AS usermoodle INNER JOIN (SELECT *,id AS idtalentos FROM {talentospilos_usuario}) AS usuario ON CAST( usermoodle.data AS INT) = usuario.id where substr(username,1,7) ='".$cod."';";
            //die($query);q
            //se verifica la existencia del estudiante. Se espera la informacion del codigo  en $data[0]
            //$query = "select idtalentos from (SELECT * FROM (SELECT *, id AS id_user FROM {user}) AS userm INNER JOIN (SELECT * FROM {user_info_data} as d INNER JOIN {user_info_field} as f ON d.fieldid = f.id WHERE f.shortname ='idtalentos' and d.data <> '') AS field ON userm.id_user = field.userid ) AS usermoodle INNER JOIN (SELECT *,id AS idtalentos FROM {talentospilos_usuario}) AS usuario ON CAST( usermoodle.data AS INT) = usuario.id where substr(username,1,7) ='".$cod."';";
            $query = "SELECT idtalentos FROM {user} u INNER JOIN (SELECT userid, CAST(d.data as int) as idtalentos FROM {user_info_data} d WHERE d.data <> '' and fieldid = (SELECT id FROM  {user_info_field} as f WHERE f.shortname ='idtalentos')) campo ON campo.userid = u.id WHERE substr(username,1,7) ='".$cod."';";
            $result = $DB->get_record_sql($query);
            if(!$result) throw new MyException("El estudiante con código univalle ".$data[0]." no se encontró en la base de datos.<br> Por favor revisa la linea ".$line_count);
            $data_array -> idtalentos =  $result->idtalentos;
            
            //se verifica la existencia del enfasis opc 1 el cual no debe ser nulo. 
            //Se espera la informacion del nomre del enfasis //opc1 en $data[1]
            $query = "SELECT id FROM {talentospilos_enfasis} WHERE nombre ='".$data[1]."';";
            $result = $DB->get_record_sql($query); 
            if(!$result) throw new MyException("El enfasis ".$data[1]." no se encuentra en la base de datos.<br>Por favor revisa la linea ".$line_count);
            $data_array -> eop1 = $result -> id;
            
            //se verifica la existencia del enfasis opc 2 en caso de que sea ingresado.
            // Se espera la información del nombre del enfasis opc 2 $data[2]
            if($data[2] == ""){
               $data_array -> eop2 = null;
            }else{
               $query = "SELECT id FROM {talentospilos_enfasis} WHERE nombre ='".$data[2]."';";
               $result = $DB->get_record_sql($query); 
               if(!$result) throw new MyException("El enfasis ".$data[2]." no se encuentra en la base de datos.<br>Por favor revisa la linea ".$line_count);
               $data_array -> eop2 = $result -> id;
            }
            
            //se verifica la existencia del enfasis opc 3 en caso de que sea ingresado.
            // Se espera la información del enfasis opc 3 en $data[3]
            if($data[3] == ""){
               $data_array -> eop3 = null;
            }else{
               $query = "SELECT id FROM {talentospilos_enfasis} WHERE nombre ='".$data[3]."';";
               $result = $DB->get_record_sql($query); 
               if(!$result) throw new MyException("El enfasis ".$data[3]." no se encuentra en la base de datos.<br>Por favor revisa la linea ".$line_count);
               $data_array -> eop3 = $result -> id;
            }
            
             //se verifica  la existencia del programa opc 1el cual no debe ser nulo. Se espera la informacion del codigo en $data[4] la jornada en $data[5]
             // se determió que los programas del enfasis daran en la sede de cali (id_sede = 1)
            $query = "SELECT id FROM {talentospilos_programa} WHERE id_sede= 1 AND  cod_univalle =".intval($data[4])." AND jornada ='".$data[5]."'; ";
            $result = $DB->get_record_sql($query);
            if(!$result) throw new MyException("El programa op1 académico académico con código univalle: ".$data[4]." en la jornada ".$data[5]." No corrreponde a un programa registrado en la ciudad de Cali<br>Por favor revisa la linea: ".$line_count);
            $data_array -> pop1 = $result -> id;
            
            //se verifica la existencia del programa opc 2 en caso de que sea ingresdo. Se espera la informacion del codigo en $data[6] la jornada en $data[7]
            if($data[6] ==""){
               $data_array -> pop2 = null;
            }else {
               $query = "SELECT id FROM {talentospilos_programa} WHERE id_sede= 1 AND  cod_univalle =".intval($data[6])." AND jornada ='".$data[7]."';";
               $result = $DB -> get_record_sql($query);
               if(!$result) throw new MyException("El programa op2 académico académico con código univalle: ".$data[6]." en la jornada ".$data[7]." No corrreponde a un programa registrado en la ciudad de Cali<br>Por favor revisa la linea: ".$line_count);   
               $data_array -> pop2 = $result -> id;
            }
            
            //se verifica la existencia del programa opc 3 en caso de que sea ingresdo. Se espera la informacion del codigo en $data[8] la jornada en $data[9]
            if($data[8] ==""){
               $data_array -> pop3 = null;
            }else {
               $query = "SELECT id FROM {talentospilos_programa} WHERE id_sede= 1 AND  cod_univalle =".intval($data[8])." AND jornada ='".$data[9]."';";
               $result = $DB -> get_record_sql($query);
               if(!$result) throw new MyException("El programa opc3 académico académico con código univalle: ".$data[8]." en la jornada ".$data[9]." No corrreponde a un programa registrado en la ciudad de Cali<br>Por favor revisa la linea: ".$line_count);   
               $data_array -> pop3 = $result -> id;
            }
            
            //se verifica el enfasis final. se espera la informcion del enfasis final en  $data[10]
            $query = "SELECT id FROM {talentospilos_enfasis} WHERE nombre ='".$data[10]."';";
            $result = $DB->get_record_sql($query); 
            if(!$result) throw new MyException("El enfasis ".$data[10]." no se encuentra en la base de datos.<br>Por favor revisa la linea ".$line_count);
            $data_array -> ef = $result -> id;
            
            
            //se verifica el programa final. Se espera la informacion del codigo en $data[11] y la jornada $data[12]
            // if($data[11] == ""){
            //    $data_array -> pf = null;
            // }else {
            //    $result = $DB -> get_record_sql("SELECT id FROM {talentospilos_programa} WHERE id_sede= 1 AND  cod_univalle =".intval($data[11])." AND jornada ='".$data[12]."';");
            //    if(!$result) throw new MyException("El programa opc3 académico académico con código univalle: ".$data[11]." en la jornada ".$data[12]." No corrreponde a un programa registrado en la ciudad de Cali<br>Por favor revisa la linea: ".$line_count);   
            //    $data_array -> pf = $result -> id;
            // }
            $data_array -> pf = null; //comentar cuando se requiera el programa final
            
            array_push($info, $data_array);
                    
            $line_count += 1;
         }
         
         global $DB;
         $record = new stdClass;
         foreach ($info as $data){
            $record->id_estudiante = $data -> idtalentos;
            $record->opc1_enfasis = $data -> eop1;
            $record->opc2_enfasis = $data -> eop2;
            $record->opc3_enfasis = $data -> eop3;
            $record->opc1_programa = $data -> pop1;
            $record->opc2_programa = $data -> pop2;
            $record->opc3_programa = $data -> pop3;
            $record->final_enfasis = $data -> ef;
            $record->final_programa = $data -> pf;
            
            $DB->insert_record('talentospilos_vocacional', $record, false);
         }
         
         $respuesta = 1;
         echo $respuesta;
        
      }
      else if($varSelector == "cursos"){
         
         global $DB;
         $record = new stdClass();
         
         while($data = fgetcsv($handle, 100, ","))
         {
            $query = "SELECT id FROM {course} WHERE shortname='".$data[0]."';";
            $id_curso = $DB->get_record_sql($query);
            $query = "SELECT id FROM {talentospilos_semestre} WHERE nombre ='".$data[1]."';";
            $id_semestre = $DB->get_record_sql($query);

            $record->id_curso = $id_curso->id;
            $record->id_semestre = $id_semestre->id;

            $DB->insert_record('talentospilos_cursos', $record, false);
         }
         
         $respuesta = 1;
         echo $respuesta;

      }
      else if($varSelector == "profesional"){
         
         global $DB;
         $record = new stdClass();
         
         while($data = fgetcsv($handle, 100, ",")){
            $record->nombre_profesional = $data[0];

            $DB->insert_record('talentospilos_profesional', $record, false);
         }
         
         $respuesta = 1;
         echo $respuesta;

      }
      else if($varSelector == "Semestre"){
         
         global $DB;
         $record = new stdClass();
         
         while($data = fgetcsv($handle, 100, ","))
         {
            $record->nombre = $data[0];
            $record->fecha_inicio = $data[1];
            $record->fecha_fin = $data[2];
            $DB->insert_record('talentospilos_semestre', $record);
         }
         $respuesta = 1;
         echo $respuesta;
      }
      else if($varSelector == "Barrios"){
         
         global $DB;
         $record = new stdClass();
         
         while($data = fgetcsv($handle, 100, ",")){
            
            $sql_query="SELECT id FROM {talentospilos_barrios} WHERE cod_barrio = $data[0]";
            $id_barrio = $DB->get_record_sql($sql_query)->id;
            
            if($id_barrio){
               $record->id = $id_barrio;
               $record->cod_barrio = (int)$data[0];
               $record->cod_comuna = $data[1];
               $record->nombre = $data[2];
               $record->estrato = $data[3];
               $DB->update_record('talentospilos_barrios', $record);   
            }else{
               $record->cod_barrio = (int)$data[0];
               $record->cod_comuna = $data[1];
               $record->nombre = $data[2];
               $record->estrato = $data[3];
               $DB->insert_record('talentospilos_barrios', $record);   
            }
            
            
         }
         $respuesta = 1;
         echo $respuesta;
      }
      else if($varSelector == "Geolocalizacion"){
         
         // Campos csv: código barrio, código estudiante, latitud, longitud
         
         global $DB;
         $record = new stdClass();
         $count = 0;
         
         while($data = fgetcsv($handle, 100, ",")){
            $count++;
            $sql_query = "SELECT id FROM {talentospilos_barrios} WHERE cod_barrio = ".$data[0];
            $id_barrio = $DB->get_record_sql($sql_query);
            
            $query = "SELECT id FROM {user} WHERE username LIKE '".substr($data[1], 2)."%'";
            
            $id_user = $DB->get_record_sql($query);

            if(!$id_user){
               throw new MyException("El estudiante con código ".substr($data[1], 2)." no se encuentra registrado en el campus virtual");
            }
            
            $additional_fields = get_adds_fields_mi((int)$id_user->id);

            $id_user_talentos = $additional_fields->idtalentos;

            if(!$id_user_talentos){
               throw new MyException("El estudiante con código ".substr($data[1], 2)." no se encuentra enlazado a la tabla talentospilos_usuario");
            }
            
            $query = "SELECT id FROM {talentospilos_demografia} WHERE id_usuario = $id_user_talentos";
            $id_register = $DB->get_record_sql($query);
            
            if($id_register){
               
               $record->id = $id_register->id;
               $record->id_usuario = $id_user_talentos;
               $record->longitud = (float)$data[2];
               $record->latitud = (float)$data[3];
               $record->barrio = (int)$id_barrio->id;

               $DB->update_record('talentospilos_demografia', $record);
               
            }else{
               $record->id_usuario = (int)$id_user_talentos;
               $record->longitud = (float)$data[2];
               $record->latitud = (float)$data[3];
               $record->barrio = (int)$id_barrio->id;
               
               $DB->insert_record('talentospilos_demografia', $record);
            }
            
            
         }
         
         $respuesta = 1;
         echo $respuesta;
         
      }
      else{
         throw new MyException("Lo sentimos la carga de archivos para la tabla ".$varSelector." esta en desarrollo.");
      }
      //se termina la transaccion
      pg_query("COMMIT") or die("Transaction commit failed\n");
      //$transaction->allow_commit();
      fclose($handle);
   }
   catch(MyException $ex){
      fclose($handle);
      if (file_exists("../view/archivos_subidos/$nombre")) {
      unlink("../view/archivos_subidos/$nombre");
      }
      echo $ex->getMessage();
      
   }
   catch(Exception $e){
      $errorSqlServer = pg_last_error();
      fclose($handle);
      if (file_exists("../view/archivos_subidos/$nombre")) {
         unlink("../view/archivos_subidos/$nombre");
      }
      pg_query("ROLLBACK");
      //se captura el error sql generado por el serversql en alguna insersion cuando está en medio de una transaccion no lo hace. averiguar porque? y como hacerlo siempre
      
      echo $e->getMessage()."<br>".$errorSqlServer."<br>".$query."<b>Consejos:</b><br><b>*</b> Por favor verifica la linea: ".intval($count+1)." en el archivo: ".$archivo['name'].". Asegurate de que no haya duplicidad en la información<br><b>*</b>Asegurate de que el archivo cargado contenga a la información necesaria en el formato determinado para cargar la tabla ".$varSelector.".";
      
   }
}
else{
   echo "El envio no se realiza sactisfactoriamente.";
}
?>
=======
<?php
require_once(dirname(__FILE__). '/../../../config.php');

require_once('MyException.php');
require_once('dateValidator.php');
require_once('query.php');

if (isset($_FILES['csv_file'])){

   try{
      $archivo = $_FILES['csv_file'];
      $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
      $nombre = $archivo['name'];
      $varSelector = $_POST['typefile_select'];
      if ($extension !== 'csv') throw new MyException("El archivo ".$archivo['name']." no corresponde al un archivo de tipo CSV. Por favor verifícalo"); 
      if (!move_uploaded_file($archivo['tmp_name'], "../view/archivos_subidos/$nombre")) throw new MyException("Error al cargar el archivo.");
      ini_set('auto_detect_line_endings', true);
      if (!($handle = fopen("../view/archivos_subidos/$nombre", 'r'))) throw new MyException("Error al cargar el archivo ".$archivo['name'].". Es posible que el archivo se encuentre dañado");
      
      //se incia la transaccion en bd
       pg_query("BEGIN") or die("Could not start transaction\n");
      //$transaction = $DB->start_delegated_transaction();

      if($varSelector == "Municipio"){
         
         global $DB;
         $record = new stdClass();
         $count = 0;
         $array_id = array();
         $array_data = array();
         $line_count =1;
         
         while($data = fgetcsv($handle, 100, ","))
         {  
            array_push($array_data, $data);
            
            $query = "SELECT id FROM {talentospilos_departamento} WHERE codigodivipola = ".intval($data[1]).";";
            
            $result = $DB->get_record_sql($query);
            
            if(!$result){
               throw new MyException("Por favor Revisa la línea ".$line_count.".<br>El codigo de División Política del departamento ".$data[1]." asociado al  municipio ".$data[2]." no se encuentra en la base de datos");
            }
            array_push($array_id, $result->id);
            $line_count+=1;
         }
         
         foreach ($array_data as $dat)
         {
            $record->codigodivipola = $dat[0];
            $record->cod_depto = $array_id[$count];
            $record->nombre = $dat[2];
            $DB->insert_record('talentospilos_municipio', $record, false);
            $count += 1;
         }
         
         $respuesta = 1;
         echo $respuesta;
      }
      else if($varSelector == "Facultad"){
         global $DB;
         //esta tabla no depende de otra
         $record = new stdClass();
         $count = 0;
         
         while($data = fgetcsv($handle, 50, ",")){
            $record->cod_univalle = $data[0];
            $record->nombre = $data[1];
            $DB->insert_record('talentospilos_facultad', $record, false);
           $count += 1;
         }

         $respuesta = 1;
         echo $respuesta;
         
      }
      else if($varSelector == "Departamento"){
         global $DB;
         // esta tabla no depende de otra
         $record = new stdClass();
         $count = 0;
         
         
         while($data = fgetcsv($handle, 1000, ","))
         {
            $record->codigodivipola = $data[0];
            $record->nombre = $data[1];
            
            $DB->insert_record('talentospilos_departamento', $record, false);
            $count+=1;
         }
         $respuesta = 1;
         echo $respuesta;
      }
      else if($varSelector == "Sede"){
         global $DB;
         $record = new stdClass();
         $count = 0;
         $array_id = array();
         $array_data = array();
         $line_count=1;
         
         while($data = fgetcsv($handle, 100, ","))
         {
            array_push($array_data, $data);
            
            $query = "SELECT id FROM {talentospilos_municipio} WHERE codigodivipola ='".intval($data[0])."';";
            $result = $DB->get_record_sql($query);
            if(!$result){
               throw new MyException("Por favor revisa la linea".$line_count.".<br>El codigo de División Política de la ciudad ".$data[0]." asociado a la sede".$data[2]." no se encuentra en la base de datos");
            }
            array_push($array_id, $result->id);
            $line_count+=1;
         }
      
         foreach ($array_data as $data) 
         { 
            $record->id_ciudad = $array_id[$count];
            $record->cod_univalle = $data[1];
            $record->nombre = $data[2];
            
            $DB->insert_record('talentospilos_sede', $record, false);
           $count+=1;
         }
         $respuesta = 1;
         echo $respuesta;
      }
      else if($varSelector == "Programa"){
         global $DB;
         $record = new stdClass();
         $count = 0;
         $array_id_sede = array();
         $array_id_fac = array();
         $array_data = array();
         $line_count = 1;
         
         
         while($data = fgetcsv($handle, 1000, ","))
         {
            
            array_push($array_data, $data);
            
            $query = "SELECT id FROM {talentospilos_sede} WHERE cod_univalle ='".intval($data[3])."';";
            $result = $DB->get_record_sql($query);
            if(!$result){
               throw new MyException("Por favor revisa la linea ".$line_count.".<br>El codigo Univalle de la sede ".$data[0]." asociado al programa ".$data[2]." no se encuentra en la base de datos");
            }
            array_push($array_id_sede, $result->id);
            
            //se verifica el codigo de l facultad
            $query = "SELECT id FROM {talentospilos_facultad} WHERE cod_univalle ='".$data[4]."';";
            $result = $DB->get_record_sql($query);
            if(!$result){
               throw new MyException("El codigo Univalle de la facultad ".intval($data[4])." asociado al programa ".$data[2]." no se encuentra en la base de datos. linea ".$line_count." ".$data[0]."-".$data[1]."-".$data[2]."-".$data[3]."-".$data[4]."");
            }
            $line_count+=1;
            array_push($array_id_fac, $result->id);
         }
       
         foreach($array_data as $data)
         { 
               $record->codigosnies = $data[0];
               $record->cod_univalle = $data[1];
               $record->nombre = $data[2];
               $record->id_sede = $array_id_sede[$count];
               $record->id_facultad = $array_id_fac[$count];
               $record->jornada = $data[5];
               
               $DB->insert_record('talentospilos_programa', $record, false);
              $count+=1;
         }
         $respuesta = 1;
         echo $respuesta;
      }
      else if($varSelector == "Discapacidad"){
         global $DB;
         //no depende de ninguna tabla
         $record = new stdClass();
         $count = 0;
        
         while($data = fgetcsv($handle, 100, ","))
         { 
              $record->codigo_men = $data[0];
              $record->nombre = $data[1];
               
              $DB->insert_record('talentospilos_discap_men', $record, false);
              $count+=1;
         }
         $respuesta = 1;
         echo $respuesta;
      }
      else if($varSelector == "Usuario"){
         global $DB;
         $record = new stdClass();
         $dateValidator = new dateValidator();
         $count = 0;
         $array_id_ciudadini = array(); //ciudad incial
         $array_id_ciudadres = array();//ciudad recidencia
         $array_id_ciudadnac = array(); //ciudad nacimiento
         $array_id_discap = array();  //discapacidad
         $array_id_talentos =  array();  
         $array_data = array();
         $line_count=0;
         $exists = true; //variable que verifica la existencia de un registro
         
         
         while($data = fgetcsv($handle, 10000, ","))
         {  
            //se verifica la existencia del registro en la base de datos para determinar si es un actualización o un nuevo registro
            $query = "SELECT id FROM {talentospilos_usuario} WHERE num_doc_ini ='".intval($data[1])."';";
            $result = $DB->get_record_sql($query);
            
            
            if(!$result) {
               $exists = false;
            }else{
               $array_id_talentos[$line_count] = $result->id;
            }
            
            
            //se verifica el formato de la fecha de nacimiento
            $dateValidator -> validateDateStyle($data[16]);
            
            
            //se almacena la informacion de toda linea leida
            array_push($array_data, $data);
               
               
            //en caso de que no exista se obtiene la información requerida para la nueva insercción 
            if(!$exists){
               //se verifica la existencia de la ciudad incial
               $query = "SELECT id FROM {talentospilos_municipio} WHERE codigodivipola ='".intval($data[6])."';";
               $result = $DB->get_record_sql($query);
               if(!$result){
                  throw new MyException("Por favor revisa la linea ".($line_count + 1).".<br>El codigo de Divsion politica ".$data[6]." asociado a la ciudad de procedencia del estudiante con número de  identificación: ".$data[3]." no se encuentra en la base de datos.".$data[0]."-".$data[1]."-".$data[2]."-".$data[3]."-".$data[4]."-".$data[5]."-".$data[6]."-");
               }
               $array_id_ciudadini[$line_count] = $result->id;
               
               //se verifica la ciudad de recidencia
               $query = "SELECT id FROM {talentospilos_municipio} WHERE codigodivipola ='".intval($data[10])."';";
               $result = $DB->get_record_sql($query);
               if(!$result){
                  throw new MyException("Por favor revisa la linea ".($line_count + 1).".<br>El codigo de division politica ".$data[10]." asociado a la ciudad de recidencia del estudiante con número de identificación:".$data[3]." no se encuentra en la base de datos");
               }
               $array_id_ciudadres[$line_count] = $result->id;
               
               
               //se verifica la ciudad de nacimiento
               $query = "SELECT id FROM {talentospilos_municipio} WHERE codigodivipola ='".intval($data[17])."';";
               $result = $DB->get_record_sql($query);
               if(!$result){
                  throw new MyException("Por favor revisa la linea ".($line_count + 1).".<br>El codigo de division politica ".$data[17]." asociado a la ciudad de nacimiento del estudiante con número de identificación:".$data[3]." no se encuentra en la base de datos");
               }
               $array_id_ciudadnac[$line_count] = $result->id;
               
               //se verifica el codigo de discapacidad
               $query = "SELECT id FROM {talentospilos_discap_men} WHERE codigo_men ='".intval($data[24])."';";
               $result = $DB->get_record_sql($query);
               if(!$result){
                  throw new MyException("Por favor revisa la linea ".($line_count + 1).".<br>El codigo  ".$data[24]." asociado a la discapacidad del estudiante con número de identificación:".$data[3]." no se encuentra en la base de datos");
               }
               $array_id_discap[$line_count] = $result->id;
            }
            
            $line_count+=1;
         }
         foreach ($array_data as $data)
         {
               $record->tipo_doc_ini = $data[0];
               $record->num_doc_ini = intval($data[1]);
               $record->tipo_doc = $data[2];
               $record->num_doc = intval($data[3]);
               $record->dir_ini = $data[4];
               $record->barrio_ini = $data[5];
               
               $record->tel_ini = $data[7];
               $record->direccion_res = $data[8];
               $record->barrio_res = $data[9];
               
               $record->tel_res = $data[11];
               $record->celular = $data[12];
               $record->emailpilos = $data[13];
               $record->acudiente = $data[14];
               $record->tel_acudiente = $data[15];
               $record->fecha_nac = $data[16];
               
               $record->sexo = $data[18];
               $record->colegio = $data[19];
               $record->estamento = $data[20];
               $record->observacion = $data[21];
               $record->estado = $data[22];
               $record->estado_icetex = 0;
               $record->grupo = $data[23];
               
               $record->ayuda_discap = $data[25];
               
               //se realiza la inserccion o actualizacion pertinente
               
               if($exists){
                  $record->id_ciudad_ini = $data[6];
                  $record->id_ciudad_res = $data[10];
                  $record->id_ciudad_nac = $data[17];
                  $record->id_discapacidad = $data[24];
                  $record->id = $array_id_talentos[$count];
                  $DB->update_record('talentospilos_usuario',$record);
               }else{
                  $record->id_ciudad_ini = $array_id_ciudadini[$count];
                  $record->id_ciudad_res = $array_id_ciudadres[$count];
                  $record->id_ciudad_nac = $array_id_ciudadnac[$count];
                  $record->id_discapacidad = $array_id_discap[$count];
                  $DB->insert_record('talentospilos_usuario', $record, false);
               }
         
               $count+=1;
         }
         $respuesta = 1;
         echo $respuesta;
         
      }else if ($varSelector =="user"){
         
         global $DB;
         
         $count = 0;
         $array_username = array();
         $array_programa = array();
         $array_data = array();
         $line_count = 1;
         $act= "";
         $query="";
         
         while($data = fgetcsv($handle, 500, ","))
         {  
            $temp_array = array();
            //se verifica el número de documento
            $query = "SELECT id FROM {talentospilos_usuario} WHERE num_doc = '".intval($data[0])."';";
            $result = $DB->get_record_sql($query);
            
            if(!$result){
               throw new MyException("Por favor revisa la línea ".$line_count.".<br>El número de documento ".$data[0]." no corresponde a un estudiante de pilos");
            }
            //se adiciona el id  de la tabla talentospilos_usuario correspondiente al nuero de documento
            array_push($temp_array, $result->id);
            
            // se verifica el programa
            $query = "SELECT id FROM {talentospilos_programa} WHERE cod_univalle = ".intval($data[2])." AND  jornada='".$data[3]."' AND id_sede = (SELECT id from {talentospilos_sede} WHERE cod_univalle =".intval($data[4]).");";
            $result = $DB->get_record_sql($query);
            
            if(!$result){
               throw new MyException("Por favor revisa la línea ".$line_count.".<br>El programa con las siguientes características; codigo univalle: ".$data[2].", jornada: ".$data[3]."  y sede: ".$data[4]." no existe en la base de datos. ");
            }
            array_push($temp_array, $result->id);
            
            //se verifica el username
            $username = substr($data[1],-7)."-".$data[2];
            $query = "SELECT id FROM {user} WHERE username = '".$username."' ;";
            $result = $DB->get_record_sql($query);
            
            if(!$result){
               throw new MyException("Por favor revisa la linea ".$line_count.".<br>No existe un ususario en moodle para el estudiante con codigo univalle: ".$data[1]." y programa ".$data[2].". ");
            }
            array_push($temp_array, $result->id);
            
            // SE ADICIONA el restode informacion al arreglo temporal el cual tendria (id_talentos,id_programa,id_user, ACTIVO)
            array_push($temp_array, 'ACTIVO');
            
            //SE ADICIONA EL ARRAY TEMPORAL AL arreglo que contiene la informarion general
            array_push($array_data,$temp_array);
            
            $line_count += 1;
         }
         
         //se obtine los id de los campos idtalentos,idprograma,estado
         $result = $DB->get_record_sql("SELECT id FROM {user_info_field} WHERE shortname = 'idtalentos'");
         $idtalentos_field = $result->id;
         
         $result= $DB->get_record_sql("SELECT id FROM {user_info_field} WHERE shortname = 'idprograma'");
         $idprograma_field = $result->id;
         
         $result= $DB->get_record_sql("SELECT id FROM {user_info_field} WHERE shortname = 'estado'");
         $idestado_field = $result->id;
        
         foreach ($array_data as $dat){  
            $record = new stdClass();
            //se verifica si ya el campo está creado asi saber si insertar o actualizar
            $query="select  d.id, f.shortname  from {user_info_data} d inner join {user_info_field} f on d.fieldid= f.id  where (f.shortname='idtalentos' OR f.shortname='idprograma' OR f.shortname='estado') AND userid =".$dat[2]." order by shortname;";
            $result= $DB->get_records_sql($query);
            if(!$result){
               $act .="(".$dat[2].",".$idestado_field.")-";
               //se inserta la info del id del usuario de la tabla talentos en el campo idtalentos asociado a la tabla user
               $record->userid = $dat[2]; //id del usario en moodle
               $record->fieldid = $idtalentos_field;
               //data[0] es el id del usario de tabla talentos
               $record->data = $dat[0];
               $record->dataformat = 0;
               $DB->insert_record('user_info_data', $record);
               
               //se inserta la info del campo idprograma
               $record->userid = $dat[2];
               $record->fieldid = $idprograma_field;
               //data[1] es el id del programa de la tabla talentospilosprograma
               $record->data = $dat[1];
               $record->dataformat = 0;
               $DB->insert_record('user_info_data', $record);
               
               //se inserta la info del campo estado
               $record->userid = $dat[2];
               $record->fieldid = $idestado_field;
               //data[3] es el estado que por defeto es activo
               $record->data = $dat[3];
               $record->dataformat = 0; //campo necesario para guradar coherencia con la tabla user_info_data
               $DB->insert_record('user_info_data', $record);

            
            }else{

               foreach ($result as $value) {
                  $shortname = $value->shortname;
                  
                  if($shortname == 'idtalentos'){

                     $record->id = $value->id; //se asigna el id que correponde a la informacion del campo a actualizar
                     $record->data = $dat[0];   //se actualiza la informacion con la info de la tabla
                     
                  }else if ($shortname == 'idprograma') {

                     $record->id = $value->id; //
                     $record->data = $dat[1];   
                  }else if ($shortname == 'estado') {
                     
                     $record->id = $value->id; 
                     $record->data = $dat[3]; 
                  }
                  $DB->update_record('user_info_data',$record);
               }          
            }
            $count += 1;
         }
         
         $respuesta = 1;
         echo $respuesta;
      }
      // Carga roles
      else if($varSelector == "Roles"){
         global $DB;
         $record = new stdClass();
      
         while($data = fgetcsv($handle, 100, ","))
         {
            $record->nombre_rol = $data[0];
            $record->descripcion = $data[1];
            
            $DB->insert_record('talentospilos_rol', $record, false);
         }
         $respuesta = 1;
         echo $respuesta;
      }
      else if($varSelector == "Funcionalidad"){
         global $DB;
         $record = new stdClass();
     
         while($data = fgetcsv($handle, 100, ","))
         {
            $record->nombre_func = $data[0];
            $record->descripcion = $data[1];
            
            $DB->insert_record('talentospilos_funcionalidad', $record, false);
         }
         $respuesta = 1;
         echo $respuesta;
      }
      else if($varSelector == "Permisos"){
         global $DB;
         $record = new stdClass();

         while($data = fgetcsv($handle, 100, ","))
         {
            $record->permiso = $data[0];
            $record->descripcion = $data[1];
            
            $DB->insert_record('talentospilos_permisos', $record, false);
         }
         $respuesta = 1;
         echo $respuesta;
      }
      else if($varSelector == "Permisos-Rol"){
         global $DB;
         $record = new stdClass();

         while($data = fgetcsv($handle, 100, ","))
         {
            //se obtine los id de los campos permiso, rol, funcionalidad
            $result = $DB->get_record_sql("SELECT id FROM {talentospilos_rol} WHERE  nombre_rol = '".$data[0]."'");
            $record->id_rol = $result->id;

            $result = $DB->get_record_sql("SELECT id FROM {talentospilos_permisos} WHERE permiso = '".$data[1]."'");
            $record->id_permiso = $result->id;

            $result = $DB->get_record_sql("SELECT id FROM {talentospilos_funcionalidad} WHERE nombre_func = '".$data[2]."'");
            $record->id_funcionalidad = $result->id;

            $DB->insert_record('talentospilos_permisos_rol', $record, false);
         }

         $respuesta = 1;
         echo $respuesta;
         
      }else if($varSelector == "Enfasis"){
         global $DB;
         $record =  new stdClass;
         $count = 0;

         while($data = fgetcsv($handle,100,",")){
            
            $record -> nombre = $data[0];
            $record -> descripcion = $data[1];
            $DB->insert_record('talentospilos_enfasis',$record);
            $count += 1;
         }
         $respuesta = 1;
         echo $respuesta;
      }else if($varSelector == "Vocacional"){
         $info =array();
         $count=0;
         $line_count = 1;
         
         while($data = fgetcsv($handle,1000,",")){
            $data_array = new stdClass;
            //se elimina el 20 incial
            $cod = intval($data[0]) - 200000000;
            //$query = "select idtalentos from (SELECT * FROM (SELECT *, id AS id_user FROM {user}) AS userm INNER JOIN (SELECT * FROM {user_info_data} as d INNER JOIN {user_info_field} as f ON d.fieldid = f.id WHERE f.shortname ='idtalentos' and d.data <> '') AS field ON userm.id_user = field.userid ) AS usermoodle INNER JOIN (SELECT *,id AS idtalentos FROM {talentospilos_usuario}) AS usuario ON CAST( usermoodle.data AS INT) = usuario.id where substr(username,1,7) ='".$cod."';";
            //die($query);q
            //se verifica la existencia del estudiante. Se espera la informacion del codigo  en $data[0]
            //$query = "select idtalentos from (SELECT * FROM (SELECT *, id AS id_user FROM {user}) AS userm INNER JOIN (SELECT * FROM {user_info_data} as d INNER JOIN {user_info_field} as f ON d.fieldid = f.id WHERE f.shortname ='idtalentos' and d.data <> '') AS field ON userm.id_user = field.userid ) AS usermoodle INNER JOIN (SELECT *,id AS idtalentos FROM {talentospilos_usuario}) AS usuario ON CAST( usermoodle.data AS INT) = usuario.id where substr(username,1,7) ='".$cod."';";
            $query = "SELECT idtalentos FROM {user} u INNER JOIN (SELECT userid, CAST(d.data as int) as idtalentos FROM {user_info_data} d WHERE d.data <> '' and fieldid = (SELECT id FROM  {user_info_field} as f WHERE f.shortname ='idtalentos')) campo ON campo.userid = u.id WHERE substr(username,1,7) ='".$cod."';";
            $result = $DB->get_record_sql($query);
            if(!$result) throw new MyException("El estudiante con código univalle ".$data[0]." no se encontró en la base de datos.<br> Por favor revisa la linea ".$line_count);
            $data_array -> idtalentos =  $result->idtalentos;
            
            //se verifica la existencia del enfasis opc 1 el cual no debe ser nulo. 
            //Se espera la informacion del nomre del enfasis //opc1 en $data[1]
            $query = "SELECT id FROM {talentospilos_enfasis} WHERE nombre ='".$data[1]."';";
            $result = $DB->get_record_sql($query); 
            if(!$result) throw new MyException("El enfasis ".$data[1]." no se encuentra en la base de datos.<br>Por favor revisa la linea ".$line_count);
            $data_array -> eop1 = $result -> id;
            
            //se verifica la existencia del enfasis opc 2 en caso de que sea ingresado.
            // Se espera la información del nombre del enfasis opc 2 $data[2]
            if($data[2] == ""){
               $data_array -> eop2 = null;
            }else{
               $query = "SELECT id FROM {talentospilos_enfasis} WHERE nombre ='".$data[2]."';";
               $result = $DB->get_record_sql($query); 
               if(!$result) throw new MyException("El enfasis ".$data[2]." no se encuentra en la base de datos.<br>Por favor revisa la linea ".$line_count);
               $data_array -> eop2 = $result -> id;
            }
            
            //se verifica la existencia del enfasis opc 3 en caso de que sea ingresado.
            // Se espera la información del enfasis opc 3 en $data[3]
            if($data[3] == ""){
               $data_array -> eop3 = null;
            }else{
               $query = "SELECT id FROM {talentospilos_enfasis} WHERE nombre ='".$data[3]."';";
               $result = $DB->get_record_sql($query); 
               if(!$result) throw new MyException("El enfasis ".$data[3]." no se encuentra en la base de datos.<br>Por favor revisa la linea ".$line_count);
               $data_array -> eop3 = $result -> id;
            }
            
             //se verifica  la existencia del programa opc 1el cual no debe ser nulo. Se espera la informacion del codigo en $data[4] la jornada en $data[5]
             // se determió que los programas del enfasis daran en la sede de cali (id_sede = 1)
            $query = "SELECT id FROM {talentospilos_programa} WHERE id_sede= 1 AND  cod_univalle =".intval($data[4])." AND jornada ='".$data[5]."'; ";
            $result = $DB->get_record_sql($query);
            if(!$result) throw new MyException("El programa op1 académico académico con código univalle: ".$data[4]." en la jornada ".$data[5]." No corrreponde a un programa registrado en la ciudad de Cali<br>Por favor revisa la linea: ".$line_count);
            $data_array -> pop1 = $result -> id;
            
            //se verifica la existencia del programa opc 2 en caso de que sea ingresdo. Se espera la informacion del codigo en $data[6] la jornada en $data[7]
            if($data[6] ==""){
               $data_array -> pop2 = null;
            }else {
               $query = "SELECT id FROM {talentospilos_programa} WHERE id_sede= 1 AND  cod_univalle =".intval($data[6])." AND jornada ='".$data[7]."';";
               $result = $DB -> get_record_sql($query);
               if(!$result) throw new MyException("El programa op2 académico académico con código univalle: ".$data[6]." en la jornada ".$data[7]." No corrreponde a un programa registrado en la ciudad de Cali<br>Por favor revisa la linea: ".$line_count);   
               $data_array -> pop2 = $result -> id;
            }
            
            //se verifica la existencia del programa opc 3 en caso de que sea ingresdo. Se espera la informacion del codigo en $data[8] la jornada en $data[9]
            if($data[8] ==""){
               $data_array -> pop3 = null;
            }else {
               $query = "SELECT id FROM {talentospilos_programa} WHERE id_sede= 1 AND  cod_univalle =".intval($data[8])." AND jornada ='".$data[9]."';";
               $result = $DB -> get_record_sql($query);
               if(!$result) throw new MyException("El programa opc3 académico académico con código univalle: ".$data[8]." en la jornada ".$data[9]." No corrreponde a un programa registrado en la ciudad de Cali<br>Por favor revisa la linea: ".$line_count);   
               $data_array -> pop3 = $result -> id;
            }
            
            //se verifica el enfasis final. se espera la informcion del enfasis final en  $data[10]
            $query = "SELECT id FROM {talentospilos_enfasis} WHERE nombre ='".$data[10]."';";
            $result = $DB->get_record_sql($query); 
            if(!$result) throw new MyException("El enfasis ".$data[10]." no se encuentra en la base de datos.<br>Por favor revisa la linea ".$line_count);
            $data_array -> ef = $result -> id;
            
            
            //se verifica el programa final. Se espera la informacion del codigo en $data[11] y la jornada $data[12]
            // if($data[11] == ""){
            //    $data_array -> pf = null;
            // }else {
            //    $result = $DB -> get_record_sql("SELECT id FROM {talentospilos_programa} WHERE id_sede= 1 AND  cod_univalle =".intval($data[11])." AND jornada ='".$data[12]."';");
            //    if(!$result) throw new MyException("El programa opc3 académico académico con código univalle: ".$data[11]." en la jornada ".$data[12]." No corrreponde a un programa registrado en la ciudad de Cali<br>Por favor revisa la linea: ".$line_count);   
            //    $data_array -> pf = $result -> id;
            // }
            $data_array -> pf = null; //comentar cuando se requiera el programa final
            
            array_push($info, $data_array);
                    
            $line_count += 1;
         }
         
         global $DB;
         $record = new stdClass;
         foreach ($info as $data){
            $record->id_estudiante = $data -> idtalentos;
            $record->opc1_enfasis = $data -> eop1;
            $record->opc2_enfasis = $data -> eop2;
            $record->opc3_enfasis = $data -> eop3;
            $record->opc1_programa = $data -> pop1;
            $record->opc2_programa = $data -> pop2;
            $record->opc3_programa = $data -> pop3;
            $record->final_enfasis = $data -> ef;
            $record->final_programa = $data -> pf;
            
            $DB->insert_record('talentospilos_vocacional', $record, false);
         }
         
         $respuesta = 1;
         echo $respuesta;
        
      }
      else if($varSelector == "cursos"){
         
         global $DB;
         $record = new stdClass();
         
         while($data = fgetcsv($handle, 100, ","))
         {
            $query = "SELECT id FROM {course} WHERE shortname='".$data[0]."';";
            $id_curso = $DB->get_record_sql($query);
            $query = "SELECT id FROM {talentospilos_semestre} WHERE nombre ='".$data[1]."';";
            $id_semestre = $DB->get_record_sql($query);

            $record->id_curso = $id_curso->id;
            $record->id_semestre = $id_semestre->id;

            $DB->insert_record('talentospilos_cursos', $record, false);
         }
         
         $respuesta = 1;
         echo $respuesta;

      }
      else if($varSelector == "profesional"){
         
         global $DB;
         $record = new stdClass();
         
         while($data = fgetcsv($handle, 100, ",")){
            $record->nombre_profesional = $data[0];

            $DB->insert_record('talentospilos_profesional', $record, false);
         }
         
         $respuesta = 1;
         echo $respuesta;

      }
      else if($varSelector == "Semestre"){
         
         global $DB;
         $record = new stdClass();
         
         while($data = fgetcsv($handle, 100, ","))
         {
            $record->nombre = $data[0];
            $record->fecha_inicio = $data[1];
            $record->fecha_fin = $data[2];
            $DB->insert_record('talentospilos_semestre', $record);
         }
         $respuesta = 1;
         echo $respuesta;
      }
      else if($varSelector == "Barrios"){
         
         global $DB;
         $record = new stdClass();
         
         while($data = fgetcsv($handle, 100, ",")){
            
            $sql_query="SELECT id FROM {talentospilos_barrios} WHERE cod_barrio = $data[0]";
            $id_barrio = $DB->get_record_sql($sql_query)->id;
            
            if($id_barrio){
               $record->id = $id_barrio;
               $record->cod_barrio = (int)$data[0];
               $record->cod_comuna = $data[1];
               $record->nombre = $data[2];
               $record->estrato = $data[3];
               $DB->update_record('talentospilos_barrios', $record);   
            }else{
               $record->cod_barrio = (int)$data[0];
               $record->cod_comuna = $data[1];
               $record->nombre = $data[2];
               $record->estrato = $data[3];
               $DB->insert_record('talentospilos_barrios', $record);   
            }
            
            
         }
         $respuesta = 1;
         echo $respuesta;
      }
      else if($varSelector == "Geolocalizacion"){
         
         // Campos csv: código barrio, código estudiante, latitud, longitud
         
         global $DB;
         $record = new stdClass();
         $count = 0;
         
         while($data = fgetcsv($handle, 100, ",")){
            $count++;
            $sql_query = "SELECT id FROM {talentospilos_barrios} WHERE cod_barrio = ".$data[0];
            $id_barrio = $DB->get_record_sql($sql_query);
            
            $query = "SELECT id FROM {user} WHERE username LIKE '".substr($data[1], 2)."%'";
            
            $id_user = $DB->get_record_sql($query);

            if(!$id_user){
               throw new MyException("El estudiante con código ".substr($data[1], 2)." no se encuentra registrado en el campus virtual");
            }
            
            $additional_fields = get_adds_fields_mi((int)$id_user->id);

            $id_user_talentos = $additional_fields->idtalentos;

            if(!$id_user_talentos){
               throw new MyException("El estudiante con código ".substr($data[1], 2)." no se encuentra enlazado a la tabla talentospilos_usuario");
            }
            
            $query = "SELECT id FROM {talentospilos_demografia} WHERE id_usuario = $id_user_talentos";
            $id_register = $DB->get_record_sql($query);
            
            if($id_register){
               
               $record->id = $id_register->id;
               $record->id_usuario = $id_user_talentos;
               $record->longitud = (float)$data[2];
               $record->latitud = (float)$data[3];
               $record->barrio = (int)$id_barrio->id;

               $DB->update_record('talentospilos_demografia', $record);
               
            }else{
               $record->id_usuario = (int)$id_user_talentos;
               $record->longitud = (float)$data[2];
               $record->latitud = (float)$data[3];
               $record->barrio = (int)$id_barrio->id;
               
               $DB->insert_record('talentospilos_demografia', $record);
            }
            
            
         }
         
         $respuesta = 1;
         echo $respuesta;
         
      }
      else{
         throw new MyException("Lo sentimos la carga de archivos para la tabla ".$varSelector." esta en desarrollo.");
      }
      //se termina la transaccion
      pg_query("COMMIT") or die("Transaction commit failed\n");
      //$transaction->allow_commit();
      fclose($handle);
   }
   catch(MyException $ex){
      fclose($handle);
      if (file_exists("../view/archivos_subidos/$nombre")) {
      unlink("../view/archivos_subidos/$nombre");
      }
      echo $ex->getMessage();
      
   }
   catch(Exception $e){
      $errorSqlServer = pg_last_error();
      fclose($handle);
      if (file_exists("../view/archivos_subidos/$nombre")) {
         unlink("../view/archivos_subidos/$nombre");
      }
      pg_query("ROLLBACK");
      //se captura el error sql generado por el serversql en alguna insersion cuando está en medio de una transaccion no lo hace. averiguar porque? y como hacerlo siempre
      
      echo $e->getMessage()."<br>".$errorSqlServer."<br>".$query."<b>Consejos:</b><br><b>*</b> Por favor verifica la linea: ".intval($count+1)." en el archivo: ".$archivo['name'].". Asegurate de que no haya duplicidad en la información<br><b>*</b>Asegurate de que el archivo cargado contenga a la información necesaria en el formato determinado para cargar la tabla ".$varSelector.".";
      
   }
}
else{
   echo "El envio no se realiza sactisfactoriamente.";
}
?>
>>>>>>> 97c7d23d80c7365c0b40027b0d4abac40b2e33b4
