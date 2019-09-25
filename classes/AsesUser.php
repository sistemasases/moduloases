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
 * Ases user functions, utilities and class definition
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;
require_once(__DIR__ . '/../vendor/autoload.php');

use Latitude\QueryBuilder\Query\SelectQuery;
use function Latitude\QueryBuilder\{alias, on, field, QueryInterface, criteria, identify, identifyAll, listing};


require_once(__DIR__ . '/../managers/user_management/user_management_lib.php');
require_once(__DIR__ . '/DAO/BaseDAO.php');
require_once(__DIR__ . '/Estado.php');
require_once(__DIR__ . '/EstadoAsesRegistro.php');
require_once(__DIR__ . '/EstadoAses.php');
require_once(__DIR__ . '/AsesUserExtended.php');
require_once(__DIR__ . '/Discapacidad.php');
require_once(__DIR__ . '/EstadoIcetexRegistro.php');
require_once(__DIR__ . '/EstadoIcetex.php');
require_once(__DIR__ . '/Errors/Factories/DatabaseErrorFactory.php');


class AsesUser extends BaseDAO  {
    const TIPO_DOCUMENTO = 'tipo_doc';
    const TIPO_DOCUMENTO_INICIAL = 'tipo_doc_ini';
    const ID_CIUDAD_INICIAL = 'id_ciudad_ini';
    const ID_CIUDAD_RESIDENCIA = 'id_ciudad_res';
    const FECHA_NACIMIENTO = 'fecha_nac';
    const ID_CIUDAD_NACIMIENTO = 'id_ciudad_nac';
    const SEXO = 'sexo';
    const ESTADO = 'estado';
    const ID_DISCAPACIDAD = 'id_discapacidad';
    const AYUDA_DISCAPACIDAD = 'ayuda_disc';
    const ESTADO_ASES = 'estado_ases';
    const NUMERO_DOCUMENTO = 'num_doc';
    const NUMERO_DOCUMENTO_INICIAL = 'num_doc_ini';
    const ID = 'id';
    public $tipo_doc_ini = -1;
    public $tipo_doc;
    public $num_doc;
    public $num_doc_ini;
    public $id_ciudad_ini;
    public $id_ciudad_res; 
    public $fecha_nac;
    public $id_ciudad_nac; // see Municipio
    public $sexo; // see Gender
    /**
     * Deprecated, is replaced with estado_icetex, estado_programa and tracking_status
     *
     * Is no longer used after 01/01/2017
     *
     * The default value
     * @var string
     */
    public $estado; 
    public $id_discapacidad;
    public $ayuda_disc;
    public $estado_ases;// see EstadoAses
    public $dir_ini; //Dirección inicial
    public $direccion_res; //Dirección residencia
    public $celular;
    public $emailpilos;
    public $acudiente;
    public $observacion;
    /**
     * @var $id_cond_excepcion int
     */
    public $id_cond_excepcion;
    public $colegio;
    public $barrio_ini; //Barrio procedencia
    public $barrio_res; //Barrio residencia
    public $id;
    public $tel_acudiente;
    public $tel_ini; // Telefono procedencia
    public $tel_res; // Telefono residencia;
    public $estamento; // Tipo colegio
    public $grupo;

    public function __construct($data = null) {
        $this->id_discapacidad = Discapacidad::ID_NO_APLICA;
        $this->dir_ini = BaseDAO::NO_REGISTRA;
        $this->direccion_res = BaseDAO::NO_REGISTRA;
        $this->celular = 0;
        $this->emailpilos = BaseDAO::NO_REGISTRA;
        $this->acudiente = BaseDAO::NO_REGISTRA;
        $this->observacion = BaseDAO::NO_REGISTRA;
        $this->colegio = BaseDAO::NO_REGISTRA;
        $this->barrio_ini = BaseDAO::NO_REGISTRA;
        $this->barrio_res = BaseDAO::NO_REGISTRA;
        $this->tel_acudiente = '';
        $this->tel_ini = '';
        $this->tel_res = '';
        $this->estado = Estado::ACTIVO;
        $this->estamento = BaseDAO::NO_REGISTRA;
        $this->grupo = 0;
        if($data) {
            parent::__construct($data);
        }

    }

    /**
     * Custom save, the ases user save is not limited to save an user in table ases user, also need create
     * one registry in estado ases registro (EstadoAsesRegistro) and estado icetex registro (EstadoIcetexRegistro)
     * @return bool|int|void
     * @throws dml_exception
     */
    public function save() {

        if (!$this->valid()) {
            return false;
        }
        parent::save();
        /* Insert EstadoAsesRegistro record related to this AsesUser */
        $estado_ases_registro = new EstadoAsesRegistro();
        $estado_ases_registro->id_estudiante = $this->id;
        $estado_ases_default = EstadoAses::get_estado_ases_default();
        $estado_ases_registro->id_estado_ases =  $estado_ases_default->id;
        $estado_ases_registro->save();

        /* Insert EstadoIcetexRegistro record related to this AsesUser */
        $estado_icetex_registro = new EstadoIcetexRegistro();
        $estado_icetex_registro->id_estudiante = $this->id;
        $estado_icetex_default = EstadoIcetex::get_default_estado_icetex();
        $estado_icetex_registro->id_estado_icetex = $estado_icetex_default->id;
        $estado_icetex_registro->save();

        return $this->id;

    }

    public function _custom_validation(): bool
    {
        $valid = true;
        $estado_icetex_default = EstadoIcetex::get_default_estado_icetex();
        if ( !$estado_icetex_default ) {
            $nombre_default_estado_icetex = EstadoIcetex::NOMBRE_DEFAULT_ESTADO_ICETEX;
            $this->add_error(DatabaseErrorFactory::registry_not_found("No se ha encontrado el estado icetex por defecto, el nombre de este es $nombre_default_estado_icetex "));
            $valid = false;
        }
        return $valid;
    }

    /**
     * Obtener los usuarios ASES, sus id y sus nombres en un array
     * @return array Array donde las llaves son los id de los usuarios ASES y el valor es el nombre del usuario
     */
    public static function get_options(): array {
        $fields = AsesUser::ID.','.AsesUser::NUMERO_DOCUMENTO;
        return parent::_get_options($fields);
    }

    /**
     * Check if a ases user exists by document number and initial document number
     *
     * Check all posible combinations, search by num doc and num doc ini
     *
     * where num_doc = $num_doc
     * or num_doc = $num_doc_ini
     * or num_doc_ini = $num_doc_ini
     * or num_doc_ini = $num_doc
     *
     * @param $num_doc
     * @param $num_doc_ini
     * @return bool
     * @throws dml_exception
     */
    public static function exists_by_num_docs_($num_doc, $num_doc_ini = null) {
        global $DB;
        if(!$num_doc_ini) {
            $num_doc_ini = $num_doc;
        }
        $sql = AsesUser::get_select_by_num_docs($num_doc, $num_doc_ini);
        return $DB->record_exists_sql($sql);
    }

    /**
     * Return the student search by num_doc and num_doc_ini
     * Is possible than exist more than one user, array is returned
     * @param $num_doc
     * @param null $num_doc_ini
     * @return array
     * @throws ErrorException
     * @throws dml_exception
     */
    public static function get_by_num_docs($num_doc, $num_doc_ini = null) {
        global $DB;
        if(!$num_doc_ini) {
            $num_doc_ini = $num_doc;
        }
        $sql = AsesUser::get_select_by_num_docs($num_doc, $num_doc_ini);
        $result = $DB->get_records_sql($sql);
        $result_values = array_values($result);
        return AsesUser::make_objects_from_std_objects_or_arrays($result_values);

    }


    private static function get_select_by_num_docs($num_doc, $num_doc_ini = null) {
        return <<<SQL
        SELECT * from mdl_talentospilos_usuario
        where num_doc = '$num_doc'
        or num_doc = '$num_doc_ini'
        or num_doc_ini = '$num_doc_ini'
        or num_doc_ini = '$num_doc'
SQL;

    }
    /**
     * Check if a ases user exists by document number and initial document number
     *
     * Check all posible combinations, search by num doc and num doc ini
     *
     * where num_doc = $num_doc
     * or num_doc = $num_doc_ini
     * or num_doc_ini = $num_doc_ini
     * or num_doc_ini = $num_doc
     *
     * @param $num_doc
     * @param $num_doc_ini
     * @return bool
     * @throws dml_exception
     */
    public function exists_by_num_docs() {
        return AsesUser::exists_by_num_docs_($this->num_doc, $this->num_doc_ini);
    }
    /**
     * Obtener los usuarios ASES, sus id y sus cedulas contatenadas con los nombres en un array
     * @return array Array donde las llaves son los id de los usuarios ASES y el valor es su número de
     * identificación concatenado con su nombre del usuario completo
     */
    public static function get_options_with_num_doc(): array {
        $options = array();
        $ases_users_with_names = AsesUser::get_ases_users_with_names();
        /* @var AsesUser $ases_user  Ases user with aditional fields, first name and lastname */
        foreach($ases_users_with_names as $ases_user) {
            $user_name = $ases_user->firstname.' '.$ases_user->lastname;
            $options[$ases_user->id] = $ases_user->num_doc.'-'.$user_name;
        }
        return $options;
    }
    /**
     * Return ases user with names, have the same properties than AsesUser,
     * with two aditional properties: firstname and lastname
     * @param array $conditions Key value array where the keys are the column names and the values
     * say what value shuld exactly have the database record for was returned, if $conditions is equal to
     * '''array('id'=>1) this implies where id = 1 in the executed query
     * @returns array array of  AsesUserWithNames Ases users with names
     */
    public static function get_ases_users_with_names($conditions = null): array {
        global $DB;
        $query = BaseDAO::get_factory()
            ->select(
                'mdl_user.firstname',
                'mdl_user.lastname',
                listing(identifyAll(AsesUser::get_column_names('ases_user'))))
            ->from(alias(AsesUser::get_table_name_for_moodle(), 'ases_user'))
            ->innerJoin(
                alias(AsesUserExtended::get_table_name_for_moodle(), 'ases_user_ext'),
                on('ases_user.'.AsesUser::ID, 'ases_user_ext.'.AsesUserExtended::ID_ASES_USER))
            ->innerJoin(
                alias('{user}', 'mdl_user'),
                on('ases_user_ext.'.AsesUserExtended::ID_MOODLE_USER, 'mdl_user.id'));
        if( $conditions ) {
            foreach($conditions as $colum_name => $value) {
                $query->andWhere(field($colum_name)->eq($value));
            }
        }
        $query->compile();
        $results = $DB->get_records_sql($query->sql());
        echo $query->sql();die;
        return AsesUserWithNames::make_objects_from_std_objects_or_arrays($results);
    }
    public function get_not_null_fields(): array
    {
            return array(AsesUser::AYUDA_DISCAPACIDAD);
    }
    public static function get_by_code() {

    }
    /**
     * Return Moodle user related to this ases user
     * @return object Instance of moodle user
     * @throws dml_exception A DML specific exception is thrown for any errors.
     * @see https://docs.moodle.org/dev/Database_schema_introduction, mdl_user table
     */
    public function get_moodle_user() {
        $user_extended = $this->get_user_extended();
        return user_management_get_full_moodle_user($user_extended->id_moodle_user);
    }

    /**
     * Return ases user extended related tho this ases user
     * @return AsesUserExtended
     * @throws dml_exception
     */
    public function get_user_extended(): AsesUserExtended {
        global $DB;
        $user_extended = new AsesUserExtended();
        $user_extended->make_from($DB->get_record(AsesUserExtended::TABLE_NAME, array(AsesUserExtended::ID_ASES_USER=>$this->id)));
        return $user_extended;
    }
    public static function validate_tipo_doc() {

    }
    public static function get_not_null_fields_and_default_in_db(): array {
        return array (
            AsesUser::ESTADO,
            AsesUser::AYUDA_DISCAPACIDAD,
            AsesUser::ESTADO_ASES
        );
    }

    /**
     * Check if self document number is taken by another ases user than exist in database
     * @return bool True if already exists, false otherwise
     */
    public function num_doc_already_exist(): bool {
        return AsesUser::exists(array(AsesUser::NUMERO_DOCUMENTO=>$this->num_doc));
    }
    /**
     * Check if some document number is taken by another ases user than exist in database
     * @param string $num_doc Document number 
     * @return bool True if already exists, false otherwise
     */
    public static function _num_doc_already_exist($num_doc) {
        global $DB;
        if ($DB->record_exists(AsesUser::get_table_name(), array(AsesUser::NUMERO_DOCUMENTO=>$num_doc ))) {
            return true;
        }
        return false;
    }
    public static function get_table_name(): string {
        return 'talentospilos_usuario';
    }

    /**
     * Return the user profile image URL, if not user profile image exist return empty string.
     * @param int $ases_student_id  Ases student id
     * @param int $context_block_id  Ases block context id
     * @return string Absolute URL of the profile image
     */
    public static function get_URL_profile_image(int $context_block_id,  int $ases_student_id ): string {
        $fs = get_file_storage();
        $files = $fs->get_area_files( $context_block_id, 'block_ases', 'profile_image', $ases_student_id);
        $image_file = array_pop($files);
        if (sizeof($files) == 0 ) {
            return '';
        } else {
           return       $url = moodle_url::make_pluginfile_url($image_file->get_contextid(), $image_file->get_component(), $image_file->get_filearea(), $image_file->get_itemid(), $image_file->get_filepath(), $image_file->get_filename());
        }
    }

    /**
     * Return the user profile image as a HTML <img> element, if not user profile image exist return default image.
     * @param int $ases_student_id  Ases student id
     * @param int $context_block_id  Ases block context id
     * @return string HTML <img> element
     */
    public static function get_HTML_img_profile_image(int $context_block_id,  int $ases_student_id, string $width = '100%', string $height = '', $class = ''): string {
        global $OUTPUT;
        $image_url = AsesUser::get_URL_profile_image($context_block_id,  $ases_student_id );
        if ($image_url != '') {
            return html_writer::empty_tag('img' , array('src' => $image_url, 'alt'=>'profile_image', 'width'=>$width, 'height'=>$height, 'class' => $class));
        } else {
            $mdl_user_id = get_id_user_moodle($ases_student_id);
          
            $mdl_user = \core_user::get_user($mdl_user_id, '*', MUST_EXIST);
            return $OUTPUT->user_picture($mdl_user, array('size'=>200,  'link'=> false));
        }
    }

    public static function _select_ases_users(): SelectQuery {
        return BaseDAO::get_factory()
            ->select()
            ->from(alias(AsesUser::get_table_name_for_moodle(), 'usuario'))
            ->innerJoin(
                alias(AsesUserExtended::get_table_name_for_moodle(), 'user_extended'),
                on('usuario.'.AsesUser::ID, 'user_extended.'.AsesUserExtended::ID_ASES_USER));
    }

    /**
     * Select all ases users with tracking status in 1 and estadoases in 'seguimiento'
     *
     * # Returned tables:
     * 1. AsesUser
     * 2. AsesUserExtended
     * 3. EstadoAsesRegistro
     * 4. EstadoAses
     *
     * @return SelectQuery
     */
    public static function _select_active_ases_users(): SelectQuery {
        return AsesUser::_select_ases_users()
            ->innerJoin(
                alias('{talentospilos_est_estadoases}', 'est_estadoases'),
                on('est_estadoases.id_estudiante', 'usuario.id'))
            ->innerJoin(
                alias('{talentospilos_estados_ases}', 'estados_ases'),
                on('estados_ases.id', 'est_estadoases.'.EstadoAsesRegistro::ID_ESTADO_ASES))
            ->where(criteria("%s = 'seguimiento'", identify('estados_ases.nombre')));
    }
}
class AsesUserWithNames extends AsesUser {

    public $firstname;
    public $lastname;
    public static  function get_table_name(): string {
        return '';
    }
}
?>