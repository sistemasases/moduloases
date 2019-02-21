<?php
/**
 * Historial Academico, extern info manager
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2019 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
require_once(__DIR__ . '/../../../../../config.php');
require_once($CFG->libdir . '/datalib.php');
require_once (__DIR__ . '/../../../managers/user_management/user_lib.php');

require_once (__DIR__ . '/../../traits/validate_object_fields.php');
require_once (__DIR__ . '/../../Validators/FieldValidators.php');

require_once (__DIR__ . '/../../../classes/module.php');

require_once(__DIR__ .'/../../../vendor/autoload.php');
use JsonSchema\Validator;


class HistorialAcademicoEI extends Validable {

    use validate_object_fields;
    /**
     * @see talentospilos_usuario.num_doc
     * @var $numero_documento string
     */
    public $numero_documento;
    public $codigo_estudiante;
    const CODIGO_PGORAMA_UNVALLE = 'codigo_programa_univalle';
    /**
     * @var $codigo_programa_univalle string
     */
    public $codigo_programa_univalle;
    public $cancela;
    public $fecha;
    /**
     *
     * @var $nombre_semestre string Ejemplos: ['2019B', '2017A']
     * @see talentospilos_semestre.nombre
     */
    public $nombre_semestre;
    /**
     * @var $promedio string
     */
    public $promedio_semestre;

    public $promedio_acumulado;

    /**
     * @var $json_materias string Materias en formato json
     * @see HistorialAcademico_Materia
     */
    public $json_materias;


    public function extract_historial_academico(): HistorialAcademico {
        $historial_academico = new HistorialAcademico();
        HistorialAcademicoEI::extract_id_estudiante($historial_academico);
        $historial_academico->json_materias = $this->json_materias;
        HistorialAcademicoEI::extract_programa($historial_academico);
        HistorialAcademicoEI::extract_semester_id($historial_academico);
        $historial_academico->promedio_acumulado = $this->promedio_acumulado;
        $historial_academico->promedio_semestre = $this->promedio_semestre;
        return $historial_academico;
    }
    private function extract_semester_id(HistorialAcademico &$historialAcademico) {
        /** @var Semestre $semestre */
        $semestre = Semestre::get_one_by(array(Semestre::NOMBRE=>$this->nombre_semestre));
        $historialAcademico->id_semestre = $semestre->id;
    }
    private function extract_programa(HistorialAcademico &$historial_academico){
        /** @var Programa $user_program_active */
        $user_program_active = Programa::get_by_num_doc_and_student_code(
            $this->numero_documento,
            $this->codigo_estudiante,
            $this->codigo_programa_univalle);

        $historial_academico->id_programa = $user_program_active->id;
    }
    private function get_moodle_user_name(): string {
        return generate_username($this->codigo_estudiante, $this->codigo_programa_univalle);
    }
    private function get_moodle_user() {
        $moodle_user_name = $this->get_moodle_user_name();
        return core_user::get_user_by_username($moodle_user_name);
    }
    private  function extract_id_estudiante(HistorialAcademico &$historial_academico) {
        /** @var  $ases_user AsesUser */
        $ases_users = AsesUser::get_by_num_docs($this->numero_documento);
        $ases_user = $ases_users[0]; // En la validación se chequea que exista uno y solo uno
        $historial_academico->id_estudiante = $ases_user->id;
    }
    private function get_active_user_extended($id_ases_user, $id_moodle_user) {
        return AsesUserExtended::get_one_by(
            array(
                AsesUserExtended::ID_ASES_USER => $id_ases_user,
                AsesUserExtended::ID_MOODLE_USER => $id_moodle_user,
                AsesUserExtended::TRACKING_STATUS => TrackingStatus::ACTIVE
            ));
    }

    /**
     * El programa ingresado en el CSV debe ser el programa en el que el usuario tiene tracking status en 1.
     */
    private function validate_program(): bool {
        /** @var Programa $user_program_active */
        $user_program_active = Programa::get_by_num_doc_and_student_code($this->numero_documento, $this->codigo_estudiante, $this->codigo_programa_univalle);

        if($this->codigo_programa_univalle !== $user_program_active->cod_univalle) {
            $this->add_error("Usted esta subiendo información historia para el programa con codigo $this->codigo_programa_univalle, pero
            el usuario ases con numero documento $this->numero_documento tiene seguimiento activo en el programa con codigo ");
            return false;
        }
        return true;
    }
    private function validate_ases_user() {
        if(!AsesUser::exists_by_num_docs_($this->numero_documento) ) {
            $this->add_error("El usuario ASES con numero documento '$this->numero_documento' no existe.", 'numero_documento');
            return false;
        } else {
            $ases_user = AsesUser::get_one_by(array(AsesUser::NUMERO_DOCUMENTO=>$this->numero_documento));
            if(!$ases_user) {
                $this->add_error("El usuario ASES con número documento '$this->numero_documento' no existe");
                return false;
            } else {
                $moodle_user_name = $this->get_moodle_user_name();
                $moodle_user = $this->get_moodle_user();
                //Previously codigo estudiante and programa univalle are validated, moodle user name here is valid
                if(!$moodle_user ) {
                    $this->add_error("No existe un usuario moodle con un username $moodle_user_name");
                    return false;
                } else {
                    $user_extended = $this->get_active_user_extended($ases_user->id, $moodle_user->id);
                    if(!$user_extended) {
                        $this->add_error("No existe un usuario extendido con tracking status activo para la persona con codigo $this->codigo_estudiante y número documento $this->numero_documento");
                        return false;
                    }  else {
                        return true;
                    }
                }

                return true;
            }
        }
    }
    public static function clean(HistorialAcademicoEI &$historialAcademicoEI) {
        $historialAcademicoEI->promedio_semestre = str_replace(',', '.', $historialAcademicoEI->promedio_semestre);
        $historialAcademicoEI->promedio_acumulado= str_replace(',', '.', $historialAcademicoEI->promedio_acumulado);
    }
    public function valid(): bool
    {
        HistorialAcademicoEI::clean($this);
        $parent_valid = parent::valid(); // TODO: Change the autogenerated stub
        $valid_fields = $this->valid_fields();

        if(!$valid_fields) {
            return false;
        }
        $valid_user = $this->validate_ases_user();
        if(!$valid_user) {
            return false;
        }
        $valid_program = $this->validate_program();
        $valid_semester_name = $this->validate_semestre();
        $valid_json_materias = $this->validate_json_materias();
        return $valid_fields && $valid_json_materias && $parent_valid && $valid_semester_name && $valid_user && $valid_program;
    }
    public function validate_semestre() {
        if(!Semestre::exists(array(Semestre::NOMBRE => $this->nombre_semestre))) {
            $this->add_error("El semestre con nombre $this->nombre_semestre no existe");
            return false;
        } else {
            return true;
        }
    }
    public function validate_json_materias() {

        /** @var $json_schema JsonSchema */
        $json_schema = JsonSchema::get_by(array(JsonSchema::ALIAS => HistorialAcademico::JSON_MATERIAS_SCHEMA_ALIAS));
        $validator = new  Validator;
        $json_materias_as_object = json_decode($this->json_materias);
        $validator->validate($json_materias_as_object, $json_schema->get_json_schema_as_object());

        $json_schema_alias = HistorialAcademico::JSON_MATERIAS_SCHEMA_ALIAS;

        if($validator->isValid()){
            return true;
        } else {
            $errors = $validator->getErrors();
            $error_messages = array_column($errors,'message');
            $error_messages_string = implode(', ', $error_messages);
            $this->add_error("El JSON materias es invalido con respecto a su respectivo schema, consulte
            talentospilos_json_schema con alias $json_schema_alias y corrija los errores. Errores [$error_messages_string]", 'json_materias',
                array(
                    'schema' => $json_schema,
                    'errors'=> $errors));
            return false;
        }
    }
    public function define_field_validators(): stdClass
    {
        /** @var  $field_validators HistorialAcademicoEI */
        $field_validators = new stdClass();
        $field_validators->json_materias = [FieldValidators::required(), FieldValidators::json()];
        $field_validators->numero_documento = [FieldValidators::required(), FieldValidators::numeric()];
        $field_validators->nombre_semestre = [FieldValidators::required(), FieldValidators::regex('/^[0-9]{4}[A|B]{1}$/')];
        $field_validators->promedio_semestre = [FieldValidators::numeric(), FieldValidators::number_between(0,5)];
        $field_validators->promedio_acumulado = [FieldValidators::numeric(), FieldValidators::number_between(0,5)];
        $field_validators->codigo_programa_univalle = [FieldValidators::required(), FieldValidators::numeric()];
        $field_validators->codigo_univalle =  [FieldValidators::required(), FieldValidators::string_size_one_of([7,9]), FieldValidators::numeric()];
        $field_validators->fecha = [
            FieldValidators::required(),
            FieldValidators::date_format(['Y-m-d', 'd-m-Y', 'Y/m/d', 'd/m/Y'])];
        $field_validators->cancela = [FieldValidators::required(), FieldValidators::one_of(['SI', 'NO'])];
        return $field_validators;
    }

}

