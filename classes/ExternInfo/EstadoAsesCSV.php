<?php
require_once(__DIR__.'/../common/Validable.php');
require_once(__DIR__.'/../common/Renderable.php');
require_once(__DIR__.'/../../managers/query.php');
require_once (__DIR__ .'/../traits/validate_object_fields.php');
require_once (__DIR__ . '/../Validators/FieldValidators.php');
require_once (__DIR__ . '/../../classes/EstadoAses.php');
/**
 * EstadoAses object
 * Clase encargada de mapear la informcion dada en la carga masiva de usuarios
 * particularmente en la 'Actualizacion de estados'
 * @see mass_role_management {@link www_root+blocks/ases/view/mass_role_management.php?courseid=<[0-9]+>&instanceid=<[0-9]+>
 */
class EstadoAsesCSV extends Validable {
    use validate_object_fields;
    public $username;
    /**
     * @see mdl_talentospilos_estado_ases.nombre
     * @var string
     */
    public $estado_ases;
    public $estado_icetex;
    public $estado_programa;
    public $tracking_status ;
    public $motivo_ases;
    public $motivo_icetex;

    public function __construct() {
        parent::__construct();
        $this->define_field_validators();
        $this->username = '';
        $this->estado_ases = -1;
        $this->estado_icetex = -1;
        $this->estado_programa = -1;
        $this->tracking_status = -1;
        $this->motivo_ases	 = null;
        $this->motivo_icetex = null;

    }


    public function define_field_validators() {

        /* @var $field_validators EstadoAsesCSV */
        $field_validators = new stdClass();
        $field_validators->estado_ases = [FieldValidators::required()];
        $field_validators->estado_icetex = [FieldValidators::required(), FieldValidators::numeric()];
        $field_validators->tracking_status = [FieldValidators::required(), FieldValidators::numeric()];
        $field_validators->motivo_ases = [FieldValidators::required()];
        $field_validators->motivo_icetex = [FieldValidators::required()];
        $field_validators->estado_programa= [FieldValidators::required()];
        $field_validators->username= [FieldValidators::required()];

        $this->set_field_validators($field_validators);
    }
    /**
     * @return bool
     * @throws ErrorException
     * @throws dml_exception
     */
    public function _custom_validation(): bool
    {
        $valid = $this->valid_fields();

        $estado_ases = EstadoAses::get_by(array(EstadoAses::NOMBRE=>$this->estado_ases));
        if(!$estado_ases) {
            $field = 'estado_ases';
            $this->add_error(new AsesError(-1, 'El estado ases debe ser *SEGUIMIENTO* o *SIN SEGUMIENTO*',
                array('field'=>$field)), $field);
            $valid = false;
        }
        return $valid;

    }

    public static function get_class_name() {
        return get_called_class();
    }

}


?>