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
 * Ases Program functions, utilities and class definition
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use function Latitude\QueryBuilder\{alias, on, fn, param};

defined('MOODLE_INTERNAL') || die;
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/datalib.php');

require_once(__DIR__ . '/Errors/Factories/DatabaseErrorFactory.php');
require_once (__DIR__ . '/../managers/user_management/user_lib.php');

require_once(__DIR__ . '/DAO/BaseDAO.php');
require_once(__DIR__ . '/Sede.php');
require_once(__DIR__ . '/AsesUser.php');
require_once(__DIR__ . '/TrackingStatus.php');
class Programa extends BaseDAO {
    const ID = 'id';
    const NOMBRE = 'nombre';
    const CODIGO_UNIVALLE = 'cod_univalle';
    const ID_SEDE = 'id_sede';
    const JORNADA = 'jornada';
    const CODIGO_SNIES = 'codigosnies';
    const ID_FACULTAD= 'id_facultad';

    public $id;
    public $codigosnies;
    public $cod_univalle;
    public $nombre;
    public $id_sede;
    public $jornada;
    public $id_facultad;

    /**
     * Retorna un array clave valor el cual tiene como llaves los id de programa y los valores son los nombres
     * de programas existentes en la base de datos concatenados con la jornada
     * @return array
     * @throws dml_exception
     */
    public static function get_options(): array {

        global $DB;
        $query = Programa::get_factory()
            ->select(
                'programa.'.Programa::ID,
                alias(
                    fn(
                        'concat_ws',
                        param(' - '),
                        'programa.'.Programa::NOMBRE,
                        'programa.'.Programa::JORNADA,
                        'sede.'.Sede::NOMBRE
                    ),
                    'option_name'
                )
            )
            ->from(alias(Programa::get_table_name_for_moodle(), 'programa'))
            ->innerJoin(
                alias(Sede::get_table_name_for_moodle(), 'sede'),
                on('programa.'.Programa::ID_SEDE, 'sede.'.Sede::ID)
            )
            ->compile();
        /* @var Programa $programa */
        $options = $DB->get_records_sql_menu($query->sql(), $query->params());
        return $options;
    }

    public static function get_numeric_fields(): array {
        return array(
            Programa::ID,
            Programa::CODIGO_SNIES,
            Programa::CODIGO_UNIVALLE,
            Programa::ID_FACULTAD,
            Programa::ID_SEDE
        );
    }


    /**
     * Validate the current object, if at least one error is detected return false and
     * add make disponible the error calling get_errors()
     * WARNING you should never call this method, call $this->valid(), this will be execute this method
     * @see get_errors
     * @return bool
     * @throws
     */
    public function _custom_validation(): bool {
        if(!$this->valid_unique_key()) {

            $this->add_error(DatabaseErrorFactory::unique_key_constraint_violation($this));
            return false;
        }
        return true;

    }

    /**
     * Retorna el programas en el que el estudiante tiene seguimiento, el estudiante es deducido
     * por numero documento, codigo estudiante y codigo programa del estudiante
     * @param string $num_doc See talentospilos_usuario.num_doc
     * @param string $student_code Codigo de estudiante univalle, con o sin el prefijo de año ejd. 1327951, 201327951
     * @param string $student_program_code Codigo programa univalle. ej. 3743
     * @return Programa|null
     * @throws
     */
    public static function get_by_num_doc_and_student_code(string $num_doc, string $student_code, string $student_program_code) {
        $moodle_user_name = generate_username($student_code, $student_program_code);
        $ases_user = AsesUser::get_one_by(array(AsesUser::NUMERO_DOCUMENTO=>$num_doc));
        if(!$ases_user) {
            return null;
        }
        $moodle_user = core_user::get_user_by_username($moodle_user_name);
        if(!$moodle_user){
            return null;
        }
        /** @var  AsesUserExtended $ases_user_extended */
        $ases_user_extended  = AsesUserExtended::get_one_by(
            array(
                AsesUserExtended::ID_ASES_USER => $ases_user->id,
                AsesUserExtended::ID_MOODLE_USER => $moodle_user->id,
                AsesUserExtended::TRACKING_STATUS => TrackingStatus::ACTIVE
            ));

        if(!$ases_user_extended) {
            return null;
        }
        return Programa::get_one_by(array(Programa::ID=>$ases_user_extended->id_academic_program));
    }
    /**
     * Check if the unique constrain (cod_univalle, id_sede, jornada) is not not violated
     * @return bool
     * @throws dml_exception
     */

    public function valid_unique_key(): bool {
        $conditions = array(
            Programa::JORNADA=>$this->jornada,
            Programa::CODIGO_UNIVALLE=>$this->cod_univalle,
            Programa::ID_SEDE=>$this->id_sede
        );
        if (Programa::exists($conditions)) {
           return false;
        } else {
            return true;
        }
    }

    public static function get_table_name(): string {
        return 'talentospilos_programa';
    }

}