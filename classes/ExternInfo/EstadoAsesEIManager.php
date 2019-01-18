<?php

use jquery_datatable\Column;
use jquery_datatable\DataTable;
require_once(__DIR__.'/../../../../config.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once(__DIR__.'/ExternInfoManager.php');
require_once(__DIR__.'/EstadoAsesCSV.php');
require_once(__DIR__.'/../AsesUser.php');
require_once(__DIR__.'/../../managers/jquery_datatable/jquery_datatable_lib.php');
require_once(__DIR__.'/../../managers/lib/reflection.php');
require_once(__DIR__.'/../../managers/user_management/user_lib.php');
class EstadoAsesEIManager extends ExternInfoManager {
    public function __construct() {
        parent::__construct( EstadoAsesCSV::get_class_name());
    }

    public function persist_data() {
        $data = $this->get_objects();
        /* @var $item EstadoAsesCSV */
        foreach ($data as $item) {
            $username = generate_username($item->codigo, $item->programa);
            /* Creación de usuario moodle si no existe*/
            if(!core_user::get_user_by_username($username)) {
               /*user_create_user(
                   array(
                       'username'=>$username,
                       'confirmed'=>'1',
                       'password'=>'suputamadre',
                       'lang'=>'es',
                       'mnethostid'=>3,
                       'email'=> $item->email,
                       'firsname'=> $item->firsname,
                       )
               );*/
            }
            /* Create ases user if not exist */
            if(!AsesUser::exists(array(AsesUser::NUMERO_DOCUMENTO=>$item->documento))) {
               /* $ases_user = new AsesUser();
                $ases_user->num_doc = $item->documento;
                $ases_user->estado_ases = 1;
                $ases_user->estado = $item->estado;
                $ases_user->emailpilos = $item->email;
                $ases_user->celular = $item->celular;
                $ases_user->tel_ini = $item->telefono_procedencia;
                $ases_user->tel_acudiente = $item->telefono_acudiente;
                $ases_user->tel_res = $item->telefonos_residencia;
                $ases_user->tipo_doc_ini = $item->tipo_documento_ingreso;
                $ases_user->tipo_doc = $item->tipo_documento;
                $ases_user->sexo = $item->sexo;
                $ases_user->acudiente = $item->acudiente;
                $ases_user->observacion = $item->observaciones;
                $ases_user->num_doc_ini = $item->documento_ingreso;
                $ases_user->ayuda_disc = $item->ayuda_discapacidad;
                $ases_user->id_discapacidad = $item->discapacidad;
                $ases_user->id_ciudad_res = $item->ciudad_residencia;
                $ases_user->id_ciudad_ini = $item->ciudad_procedencia;
                $ases_user->id_ciudad_nac = $item->lugar_nacimiento;
                $ases_user->fecha_nac = $item->fecha_nacimiento;
                $ases_user->grupo = $item->grupo;
                $ases_user->barrio_ini = $item->barrio_procedencia;
                $ases_user->barrio_res = $item->barrio_residencia;
                $ases_user->estamento = $item->estamento;
                $ases_user->direccion_res = $item->direccion_residencia;
                $ases_user->dir_ini = $item->direccion_procedencia;
                $ases_user->colegio = $item->colegio;
                try {
                    $ases_user->save();
                } catch (Exception $e) {
                    print_r($e);
                }
*/
             }
             if(!AsesUserExtended::exist_by_username($username)) {
                // echo "nel mijo";

             }

        }
    }
    /**
     * In this case, a datatable is returned
     * @throws ErrorException
     * @return string|void
     */
    public function send_response() {

        $sample_std_object = $this->get_objects()[0];

        $datatable_columns = \jquery_datatable\Column::get_columns($sample_std_object, $this->custom_column_mapping());
        $json_datatable = new \jquery_datatable\DataTable($this->get_objects(), $datatable_columns);
        $response = new \stdClass();
        $response->jquery_datatable = $json_datatable;
        $response->data = $this->get_initial_objects();
        $response->error = !$this->valid();
        $response->errors = $this->get_errors();
        $response->initial_object_properties = count($response->data)>=1?  \reflection\get_properties($response->data[0]): [];
        $response->object_errors = $this->get_object_errors();
        $arrayEncoded = json_encode($response);


        return $arrayEncoded;
    }
    public function custom_column_mapping() {

        return array(
            'DirecciOn procedencia' => 'direccion_procedencia',
            'Barrio procedencia' => 'barrio_procedencia',
            'Ciudad procedencia' => 'ciudad_procedencia',
            'Telefono procedencia' => 'telefono_procedencia',
            'Dirección residencia' => 'direccion_residencia',
            'Barrio residencia' => 'barrio_residencia',
            'Estamento (Tipo Colegio)' => 'estamento',
            'Documento' => 'documento'
        );
    }

}


?>