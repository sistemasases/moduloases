<?php

use jquery_datatable\Column;
use jquery_datatable\DataTable;
require_once(__DIR__.'/../../../../config.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once($CFG->dirroot.'/cohort/lib.php');
require_once(__DIR__.'/ExternInfoManager.php');
require_once(__DIR__.'/EstadoAsesCSV.php');
require_once(__DIR__.'/../AsesUser.php');
require_once(__DIR__.'/../Programa.php');

require_once(__DIR__.'/../../managers/cohort/cohort_lib.php');
require_once(__DIR__ . '/../../managers/user_management/user_lib.php');

class EstadoAsesEIManager extends ExternInfoManager {
    public $cohort_id;
    public $instance_id;
    public function __construct($cohort_id, $instance_id) {
        parent::__construct( EstadoAsesCSV::class);
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
        /* @var $item EstadoAsesCSV */

        foreach ($data as $key => $item) {
            $id_moodle_user = null;
            $id_ases_user = null;
            /* Creación de usuario moodle si no existe*/
            $moodle_user = $item->get_moodle_user();
            $username = $moodle_user['username'];
            if(!core_user::get_user_by_username($username)) {
                $id_moodle_user = user_create_user((object)$moodle_user);
                $this->add_success_log_event("El usuario moodle fue creado con nombre de usuario $username y contraseña por defecto", $key);
            } else {
                $moodle_user = core_user::get_user_by_username($username);

                $this->add_object_warning("El usuario moodle con username $username ya existia", $key);
               // print_r($this->get_object_warnings());
                $id_moodle_user = $moodle_user->id;
            }


            /* Añadir el usuario a la cohorte dada */


            /** @var $cohort \cohort_lib\Cohort */
            $cohorts = \cohort_lib\get_cohorts(array(\cohort_lib\ID_NUMBER=>$this->cohort_id));
            $cohort = $cohorts[0]; /** El id number de una corte es unico, por lo que esto es posible,
                                       además, en la validación se verifico que esta existiera */
            if( cohort_is_member($cohort->id, $id_moodle_user) ) {
                /** No hace falta añadir un mensaje warning ya que más abajo se advierte si el usuario existia en alguna cohorte
                 * incluida la cohorte en la que se desea adicionar actualmente al usuario
                 */
            } else {
                $added_to_cohort = \cohort_lib\cohort_add_user_to_cohort($cohort->id, $id_moodle_user);
                if(!$added_to_cohort) {
                    $this->add_error(new AsesError(
                        -1,
                        "El usuario con codigo $item->codigo no ha podido añadirse a la cohorte con id_number $this->cohort_id por una razon inesperada
                    Revisa que el codigo y el programa esten bien, y que el id_number de la cohorte este registrado en mdl_cohort"));

                    return false;
                }
            }
            if($student_cohorts = get_cohorts_by_student($id_moodle_user)) {
                $cohort_names = array_column($student_cohorts, 'name');
                $cohort_names_string = implode(', ', $cohort_names);
                $this->add_object_warning("El estudiante ya estaba en la(s) cohorte(s) [$cohort_names_string]", $key);
            }
            /* Create ases user if not exist */
            if(!AsesUser::exists_by_num_docs_($item->documento, $item->documento_ingreso)) {

                $ases_user = EstadoAsesCSV::extract_ases_user($item);
                if($ases_user->valid()) {
                    $id_ases_user = $ases_user->save();
                    $this->add_success_log_event("El usuario con número de documento $item->documento se ha creado.", $key);
                }
             } else {
                $this->add_object_warning("El usuario con número de documento $item->documento ya existia en la tabla usuarios ases, sea por documento inicial o por documento actual", $key);
                $ases_user = AsesUser::get_by(array(AsesUser::NUMERO_DOCUMENTO=>$item->documento));
                $id_ases_user= $ases_user->id;
            }
            if(AsesUserExtended::exists(array(AsesUserExtended::ID_ASES_USER=>$id_ases_user))) {
                $possible_moodle_users_related = user_moodle_get_by_code($item->codigo);
                if( !empty ($possible_moodle_users_related)) {
                    if(count($possible_moodle_users_related) === 1) {

                    }
                }
                $programs = AsesUserExtended::get_active_programs_by_ases_user_id($id_ases_user);
                $program_names = array_column($programs, Programa::NOMBRE);
                $program_names_string = implode($program_names);
                $this->add_object_warning(
                    "El usuario tenia tracking status 1 en el (los) programa(s) [$program_names_string]. Estos se pasarán a tracking status 0.",
                    $key);
            }
             if(!AsesUserExtended::exists(array(AsesUserExtended::ID_MOODLE_USER=>$id_moodle_user, AsesUserExtended::ID_ASES_USER=>$id_ases_user))) {
                $ases_user_extended = $item->extract_user_extended($id_ases_user, $id_moodle_user);
                AsesUserExtended::disable_all_tracking_status($id_ases_user);
                $ases_user_extended->save();
             }
        }
        return true;
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