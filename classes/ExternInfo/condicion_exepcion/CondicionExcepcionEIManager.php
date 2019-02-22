<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 8/02/19
 * Time: 04:04 PM
 */
require_once (__DIR__ . '/../ExternInfoManager.php');
require_once (__DIR__ . '/CondicionExcepcionEI.php');
require_once (__DIR__ . '/../../AsesUser.php');

class CondicionExcepcionEIManager extends ExternInfoManager
{
    public function __construct()
    {
        parent::__construct(CondicionExcepcionEI::get_class_name());
    }
    public function persist_data()
    {

        $data = $this->get_objects();

        /** @var  $item  CondicionExcepcionEI */
        foreach($data as $key => $item) {
            if(!$item->valid()) {
                return false;
            }
            /** @var  $ases_user AsesUser */
            $ases_user = AsesUser::get_by (array(AsesUser::NUMERO_DOCUMENTO => $item->num_documento));
            /** @var  $condicion_excepcion CondicionExcepcion */
            $condicion_excepcion = CondicionExcepcion::get_by(array(CondicionExcepcion::ALIAS => $item->condicion));
            if(CondicionExcepcion::exists(array('id'=>$ases_user->id_cond_excepcion))) {
                /** @var $condicion_excepcion_previa CondicionExcepcion */
                $condicion_excepcion_previa = CondicionExcepcion::get_by(array(CondicionExcepcion::ID=>$ases_user->id_cond_excepcion));
                if($condicion_excepcion_previa->id === $condicion_excepcion->id) {
                    $this->add_success_log_event("La condición de excepción de el usuario con número documento $ases_user->num_doc ya era $condicion_excepcion->alias Nada por actualizar", $key);
                    continue;
                }
                $this->add_object_warning(
                    "El usuario ya tenia una condición de excepción previa, esta era $condicion_excepcion_previa->alias", $key
                );
            }
            $ases_user->id_cond_excepcion = $condicion_excepcion->id;
            if($ases_user->valid()) {
               // print_r(gettype($ases_user->ayuda_disc));die;
                $updated = $ases_user->update();

                if( $updated ) {
                    $this->add_success_log_event("La condición de excepción de el usuario con numero documento $ases_user->num_doc ha sido actualizado a $condicion_excepcion->alias", $key);
                } else {
                    $this->add_generic_object_errors("El usuario no se pudo actualizar", $key);
                }
            } else {
                $this->add_generic_object_errors($ases_user->get_errors('Error en el objeto AsesUser: '),$key);
                return false;
            }

        }
    }
}