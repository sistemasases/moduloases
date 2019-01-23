<?php
error_reporting(E_ALL | E_STRICT);   // NOT FOR PRODUCTION SERVERS!
ini_set('display_errors', '1');         // NOT FOR PRODUCTION SERVERS!
require_once(__DIR__ . '/../../../../config.php');
require_once(__DIR__.'/../common/Validable.php');
require_once(__DIR__.'/../common/Renderable.php');
require_once (__DIR__ .'/../traits/validate_object_fields.php');
require_once (__DIR__ . '/../Validators/FieldValidators.php');
require_once (__DIR__ . '/../../classes/EstadoAses.php');
require_once (__DIR__ . '/../../classes/Municipio.php');
require_once (__DIR__ . '/../../classes/TipoDocumento.php');
require_once (__DIR__ . '/../../classes/Discapacidad.php');
require_once (__DIR__ . '/../../managers/user_management/user_lib.php');
//

/**
 * EstadoAses object
 * Clase encargada de mapear la informcion dada en la carga masiva de usuarios
 * particularmente en la 'Actualizacion de estados'
 * @see mass_role_management {@link www_root+blocks/ases/view/mass_role_management.php?courseid=<[0-9]+>&instanceid=<[0-9]+>
 */
class EstadoAsesCSV extends Validable {
    use validate_object_fields;
    /**
     * @see mdl_talentospilos_estado_ases.nombre
     * @var string
     */
    public $tipo_documento_ingreso;
    public $documento_ingreso;
    public $tipo_documento;
    public $documento;
    public $direccion_procedencia;
    public $barrio_procedencia;
    public $ciudad_procedencia;
    public $telefono_procedencia;
    public $direccion_residencia;
    public $barrio_residencia;
    public $ciudad_residencia;
    public $telefonos_residencia;
    public $celular;
    public $firstname;
    public $lastname;
    public $email;
    public $acudiente;
    public $telefono_acudiente;
    public $fecha_nacimiento;
    public $lugar_nacimiento;
    public $sexo;
    public $colegio;
    public $estamento;
    public $observaciones;
    public $estado;
    public $grupo;
    public $discapacidad;
    public $ayuda_discapacidad;
    public $codigo;
    public $programa;
    public $jornada;
    public $sede;
    private $first_and_last_name_capital_letter;
    private $_cleaned;

    public function __construct($first_and_last_name_capital_letter = true) {
        parent::__construct();
        $this->define_field_validators();
        $this->telefonos_residencia = 0;
        $this->telefono_acudiente = 0;
        $this->telefono_procedencia = 0;
        $this->first_and_last_name_capital_letter = $first_and_last_name_capital_letter;

    }

    public function valid(): bool {
        EstadoAsesCSV::clean($this);
        return parent::valid();
    }
    public function define_field_validators() {

        /* @var $field_validators EstadoAsesCSV */
        $field_validators = new stdClass();
        $tipos_documento = TipoDocumento::get_all();
        $tipos_documento_names = array_map(function($tipo_documento) {
            /** @var $tipo_documento TipoDocumento */
            return $tipo_documento->nombre;
        },
            $tipos_documento);
        $tipos_documento_names_string = implode (', ', $tipos_documento_names);
        $field_validators->documento = [FieldValidators::required(), FieldValidators::numeric()];
        $field_validators->documento_ingreso = [FieldValidators::required(), FieldValidators::numeric()];
        $field_validators->email = [FieldValidators::email()];
        $field_validators->tipo_documento = [
            FieldValidators::required(),
            FieldValidators::one_of($tipos_documento_names,
                "El campo 'tipo_documento' solo puede tomar uno de los siguientes valores: [$tipos_documento_names_string]. 
                 Tenga en cuenta que puede ingresar en el formato C.C. o C.C, o T...I, los espacios, puntos y comas seran limpiados
                 al momento de recibir los datos para efectos de ejecución de el guardado.")];
        $field_validators->programa = [FieldValidators::required(), FieldValidators::string_size(4)];
        $field_validators->codigo = [FieldValidators::required(), FieldValidators::string_size_one_of([7,9])];

        $this->set_field_validators($field_validators);
    }
    public static function clean(EstadoAsesCSV &$estadoAsesCSV) {
        $estadoAsesCSV->_cleaned = true;
        /* Trim all the fields*/
        foreach($estadoAsesCSV as $prop => $val)
        {
            if(is_number($val) || is_string($val)) {
                $estadoAsesCSV->$prop = trim($val);
            }
        }
        /* Por convención, en la base de datos los $estadoAsesCSV (ciudades) se almacenan en mayuscula */
        $estadoAsesCSV->ciudad_procedencia = strtoupper($estadoAsesCSV->ciudad_procedencia);
        $estadoAsesCSV->ciudad_residencia = strtoupper($estadoAsesCSV->ciudad_residencia);

        $estadoAsesCSV->clean_tipos_doc();
        return $estadoAsesCSV;

    }
    private function clean_tipos_doc() {

        /**
         * Dado que algunas veces en tipo de documento se prefiere ingresar de la forma T.I. o T.I
         * y en la base de datos se almacenan sin puntos, es decir, TI, CC etc. se deben remover los puntos
         * antes de comenzar cualquier guardado.
         * Adicionalmente se quitan las comas y los espacios, ademas de los números.
         */
        $this->tipo_documento = preg_replace('/[, 0-9.]*/', '', $this->tipo_documento);

        $this->tipo_documento_ingreso = preg_replace('/[, 0-9.]*/', '', $this->tipo_documento_ingreso);

    }
    private static  function pre_save_ciudades(EstadoAsesCSV &$estadoAsesCSV) {
        /**
         * !!!!!!!Si no hay ningun error, los nombres de las ciudades se reemplazan por sus respectivos ids
         * Municipios (ciudades) en 'NO REGISTRA' deben tomar el valor de el municipio con nombre 'NO DEFINIDO' de la base de datos
         */
        if($estadoAsesCSV->ciudad_procedencia === BaseDAO::NO_REGISTRA) {
            $estadoAsesCSV->ciudad_procedencia = Municipio::ID_MUNICIPIO_NO_DEFINIDO;
        } else {
            $ciudad_procedencia = Municipio::get_by([Municipio::NOMBRE=>$estadoAsesCSV->ciudad_procedencia]);

            $estadoAsesCSV->ciudad_procedencia = $ciudad_procedencia->id;

        }
        if($estadoAsesCSV->ciudad_residencia === BaseDAO::NO_REGISTRA) {
            $estadoAsesCSV->ciudad_residencia = Municipio::ID_MUNICIPIO_NO_DEFINIDO;
        } else {
            $ciudad_residencia = Municipio::get_by([Municipio::NOMBRE=>$estadoAsesCSV->ciudad_residencia]);
            $estadoAsesCSV->ciudad_residencia = $ciudad_residencia->id;
        }
        if($estadoAsesCSV->lugar_nacimiento === BaseDAO::NO_REGISTRA) {
            $estadoAsesCSV->lugar_nacimiento = Municipio::ID_MUNICIPIO_NO_DEFINIDO;
        } else {
            $lugar_nacimiento = Municipio::get_by([Municipio::NOMBRE=>$estadoAsesCSV->lugar_nacimiento]);
            $estadoAsesCSV->lugar_nacimiento = $lugar_nacimiento->id;
        }
    }

    private static function pre_save_tipos_doc(EstadoAsesCSV &$estadoAsesCSV) {
        $tipos_documento = TipoDocumento::get_all();
        /* !!!!! Se reemplazan los nombres de los tipos de documento por sus codigos en la BD */
        /** @var $tipo_documento_ingreso_object TipoDocumento */
        $tipo_documento_ingreso_object = array_filter($tipos_documento,
            function($tipo_documento) use ($estadoAsesCSV){
                /** @var $tipo_documento TipoDocumento */
                return $tipo_documento->nombre === $estadoAsesCSV->tipo_documento_ingreso;
            });
        /** @var $tipo_documento_object TipoDocumento */

        $tipo_documento_object = array_filter($tipos_documento,
            function($tipo_documento) use ($estadoAsesCSV) {
                /** @var $tipo_documento TipoDocumento */

                return $tipo_documento->nombre === $estadoAsesCSV->tipo_documento;
            });
        /**
         * !!!!!!!!! algunas veces, el tipo de documento en la base de datos se repite O_O
         * Entonces $tipo_documento_object y $tipo_documento_ingreso_object puede ser un array, increiblemente
         */
        if (is_array($tipo_documento_object)) {
            $tipo_documento_object = array_shift($tipo_documento_object);
        }
        if (is_array($tipo_documento_ingreso_object)) {
            $tipo_documento_ingreso_object = array_shift($tipo_documento_ingreso_object);
        }
        $estadoAsesCSV->tipo_documento = $tipo_documento_object->id;
        $estadoAsesCSV->tipo_documento_ingreso = $tipo_documento_ingreso_object->id;
        return $estadoAsesCSV;

    }
    private static function pre_save_first_and_lastname(EstadoAsesCSV &$estadoAsesCSV) {
        /* Frist name and lastname in capital letter*/
        if($estadoAsesCSV->first_and_last_name_capital_letter) {
            $estadoAsesCSV->firstname = strtoupper($estadoAsesCSV->firstname);
            $estadoAsesCSV->lastname= strtoupper($estadoAsesCSV->lastname);
        }

    }
    public static function pre_save(EstadoAsesCSV &$estadoAsesCSV) {
        EstadoAsesCSV::clean($estadoAsesCSV);
        EstadoAsesCSV::pre_save_ciudades($estadoAsesCSV);
        EstadoAsesCSV::pre_save_tipos_doc($estadoAsesCSV);
        EstadoAsesCSV::pre_save_first_and_lastname($estadoAsesCSV);
    }

    public function validar_discapacidad($glue = ', ') {
        if(Discapacidad::exists(array(Discapacidad::ID=>$this->discapacidad))) {
            return true;
        } else {
            $discapacidades = Discapacidad::get_all();
            $discapacidades_id = array_map(function($discapacidad) { return $discapacidad->id;}, $discapacidades);
            $discapacidad_id_string = implode($glue, $discapacidades_id);
            $this->add_error(new AsesError(
                -1,
                "La discapacidad con id $this->discapacidad no existe. Discapacidades disponibles = [$discapacidad_id_string]"
                ),
                'discapacidad');
            return false;
        }
    }
    /**
     * Se debe recordar que este metodo se ejecuta luego de el clean.
     * Por tanto los datos ya deben haber pasado por un trim y se han echo modificaciones a dichos datos.
     * @see $this->clean()
     * @see $this->validar_ciudades()
     * @throws dml_exception
     * @return bool
     */
    public function _custom_validation(): bool
    {
        $valid = false;
        try {
            $valid = $this->valid_fields() &&
                $this->validar_ciudades() &&
                $this->validar_tipos_documento() && /* En esta función se alteran datos de $this*/
                $this->validar_discapacidad();
            ;
        } catch (Exception $e) {
            print_r($e);
            return false;
        }
        /*
        $estado_ases = EstadoAses::get_by(array(EstadoAses::NOMBRE=>$this->estado_ases));
        // @var $estado_ases EstadoAses
        if(!$estado_ases) {
            $estados_ases_names = array_map(
                function($estado_ases) {
                    return $estado_ases->nombre;
                    },
                EstadoAses::get_all(null, 'nombre'));
            $estado_ases_names_string = implode(', ', $estados_ases_names);
            $field = 'estado_ases';
            $this->add_error(new AsesError(-1, "El estado ases debe ser uno de los siguientes: $estado_ases_names_string",
                array('field'=>$field)), $field);
            $valid = false;
        }
        */
        /* Validar tipos de documento */
        return $valid;

    }
    private function validar_tipos_documento() {
        $valid = true;
        $this->clean_tipos_doc();
        $tipos_documento = TipoDocumento::get_all();
        $tipos_documento_names = array_map(
            function($tipo_documento) {
                /** @var $tipo_documento TipoDocumento */
                return $tipo_documento->nombre;
            },
            $tipos_documento
        );
        $tipos_documento_names = array_unique($tipos_documento_names);
        $tipos_documento_names_string = implode (', ', $tipos_documento_names);
        if(!array_search($this->tipo_documento, $tipos_documento_names)) {
            $this->add_error(new AsesError(-1, "El tipo documento $this->tipo_documento no existe en la tabla tipo_documento. Puede tomar uno de los siguientes valores: [$tipos_documento_names_string]. Alternativamente, dichos valores pueden ir acompañados de puntos, espacios o comas, dichos valores serán removidos antes de guardar la información en la base de datos. Ejemplos: 'C.C', 'T.I... , 'T.I.', 'C... C.'",
                array('field' => 'tipo_documento')), 'tipo_documento');
            $valid = false;
        }

        if(!array_search($this->tipo_documento_ingreso, $tipos_documento_names)) {
            $this->add_error(new AsesError(-1, "El tipo documento ingreso $this->tipo_documento_ingreso no existe en la tabla tipo_documento. Puede tomar uno de los siguientes valores: [$tipos_documento_names_string]' . Alternativamente, dichos valores pueden ir acompañados de puntos, espacios o comas, dichos valores serán removidos antes de guardar la información en la base de datos. Ejemplos: 'C.C', 'T.I... , 'T.I.', 'C... C.'",
                array('field' => 'tipo_documento_ingreso')), 'tipo_documento_ingreso');
            $valid = false;
        }

        return $valid;
    }

    /**
     * @return bool
     * @throws dml_exception Se ejecuta la funcion Municipio->exist()
     *
     */
    private function validar_ciudades() {

        /** !!!!!!!!!!!!!
         * Municipios (ciudades) siempre vienen en texto, por ejemplo 'CALI', 'BUGA'... pero para almacenarlos
         * se debe extraer el id de la base de datos. Por tanto, al validar que el municipio exita, el nombre
         * de el municipio ahora en los datos se reemplaza por su codigo en la base de datos.
         */
        $no_registra = BaseDAO::NO_REGISTRA;
        $valid = true;
        /* Valida ciudad residencia: Esta se da por nombre no por codigo. Ej. CALI, BUGA, etc */
        if (!($this->ciudad_residencia != $no_registra || !Municipio::exists(array(Municipio::NOMBRE => $this->ciudad_residencia)))) {
            $this->add_error(new AsesError(-1, "La ciudad de residencia (municipio) $this->ciudad_residencia no existe en la tabla municipio. Puede tomar el valor de '$no_registra'",
                array('field' => 'ciudad_residencia')), 'ciudad_residencia');
            $valid = false;
        }
        if(!($this->ciudad_procedencia != $no_registra ||!Municipio::exists(array(Municipio::NOMBRE=>$this->ciudad_procedencia)))) {
            $this->add_error(new AsesError(-1, "La ciudad de procedencia (municipio) $this->ciudad_procedencia no existe en la tabla municipio. Puede tomar el valor de  '$no_registra''",
                array('field'=>'ciudad_procedencia')), 'ciudad_procedencia');
            $valid = false;
        }

        return $valid;
    }
    public static function get_class_name() {
        return get_called_class();
    }

}



?>


