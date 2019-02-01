<?php

use jquery_datatable\Column;
use jquery_datatable\DataTable;
require_once(__DIR__.'/../../../../config.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once(__DIR__.'/ExternInfoManager.php');
require_once(__DIR__.'/EstadoAsesCSV.php');
require_once(__DIR__.'/../AsesUser.php');
require_once(__DIR__.'/../Programa.php');

require_once(__DIR__.'/../../managers/jquery_datatable/jquery_datatable_lib.php');
require_once(__DIR__.'/../../managers/lib/reflection.php');
require_once(__DIR__.'/../../managers/cohort/cohort_lib.php');
class EstadoAsesEIManager extends ExternInfoManager {

    public $cohort_id;
    public $instance_id;
    public function __construct($cohort_id, $instance_id) {
        parent::__construct( EstadoAsesCSV::get_class_name());
        $this->cohort_id = $cohort_id;
        $this->instance_id = $instance_id;
    }

    /**
     * @return bool
     * @throws ErrorException
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function persist_data() {
        $data = $this->get_objects();
        /* @var $item_ EstadoAsesCSV */
        foreach ($data as $key => $item_) {
            $item = clone $item_;

            if(!$item->valid()) {

                return false;
            }
            /** @var $item EstadoAsesCSV */

            EstadoAsesCSV::pre_save($item);

            $username = generate_username($item->codigo, $item->programa);
            $id_moodle_user = null;
            $id_ases_user = null;
            /* Creación de usuario moodle si no existe*/

            if(!core_user::get_user_by_username($username)) {

             $id_moodle_user = user_create_user(
                 (object) array(
                      'username'=>$username,
                      'confirmed'=>'1',
                      'password'=> get_user_password($item->codigo, $item->firstname, $item->lastname),
                      'lang'=>'es',
                      'mnethostid'=>3,
                      'email'=> $item->email,
                      'firstname'=> $item->firstname,
                      'lastname'=> $item->lastname,
                      )
              );
                $this->add_success_log_event("El usuario moodle fue creado con nombre de usuario $username y contraseña por defecto", $key);
            } else {
                $moodle_user = core_user::get_user_by_username($username);
                $this->add_warning("El usuario moodle con username $username ya existia", $key);
                $id_moodle_user = $moodle_user->id;
            }
            /* Añadir el usuario a la cohorte dada */
            /** @var $cohort \cohort_lib\Cohort */
            $cohorts = \cohort_lib\get_cohorts(array(\cohort_lib\ID_NUMBER=>$this->cohort_id));
            $cohort = $cohorts[0]; // El id number de una corte es unico, por lo que esto es posible,
            // además, en la validación se varifico que esta existiera
            $added_to_cohort = \cohort_lib\cohort_add_user_to_cohort($cohort->id, $id_moodle_user);
            if(!$added_to_cohort) {
                $this->add_error(new AsesError(
                    -1,
                    "El usuario con codigo $item->codigo no ha podido añadirse a la cohorte con id_number $this->cohort_id por una razon inesperada
                    Revisa que el codigo y el programa esten bien, y que el id_number de la cohorte este registrado en mdl_cohort"));

                return false;
            }
            /* Create ases user if not exist */
            if(!AsesUser::exists(array(AsesUser::NUMERO_DOCUMENTO=>$item->documento))) {
                $ases_user = EstadoAsesCSV::extract_ases_user($item);
                if($ases_user->valid()) {
                    $id_ases_user = $ases_user->save();
                    $this->add_success_log_event("El usuario con número de documento $item->documento se ha creado.", $key);
                }
             }else {
                $this->add_warning("El usuario con número de documento $item->documento ya existia en la tabla usuarios ases", $key);

                $ases_user = AsesUser::get_by(array(AsesUser::NUMERO_DOCUMENTO=>$item->documento));
                $id_ases_user= $ases_user->id;
            }
             if(!AsesUserExtended::exists(array(AsesUserExtended::ID_MOODLE_USER=>$id_moodle_user, AsesUserExtended::ID_ASES_USER=>$id_ases_user))) {
                $academic_program = Programa::get_by(array(Programa::CODIGO_UNIVALLE=>$item->programa, Programa::ID_SEDE=>$item->sede));
                $id_academic_program = $academic_program->id;
                $ases_user_extended = new AsesUserExtended();
                $ases_user_extended->id_ases_user = $id_ases_user;
                $ases_user_extended->id_moodle_user = $id_moodle_user;
                $ases_user_extended->id_academic_program = $id_academic_program;

                AsesUserExtended::disable_all_tracking_status($id_ases_user);
                $ases_user_extended->save();
             }

        }
        return true;
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
    public function valid(): bool
    {
        $valid = parent::valid(); // TODO: Change the autogenerated stub

        if($this->cohort_id && \cohort_lib\exists(array('idnumber'=>$this->cohort_id))) {

        } else {
            $this->add_error(new AsesError(-1 , 'La cohorte ingresada, o la instancia son incorrectas'));
            $valid = false;
        }
        return $valid;
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