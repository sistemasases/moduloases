<?php
require_once (__DIR__ . '/DAO/BaseDAO.php');
require_once (__DIR__ . '/TrackingStatus.php');
require_once (__DIR__ . '/../managers/lib/student_lib.php');
/**
 * Class AsesUserExtended, Relation between moodle user and Ases user, and save the tracking and program status
 * of the student
 * @see database table talentospilos_user_extended
 */
class AsesUserExtended extends BaseDAO {
    const TABLE_NAME = 'talentospilos_user_extended';
    const ID_ASES_USER = 'id_ases_user';
    const ID_MOODLE_USER = 'id_moodle_user';
    const TRACKING_STATUS = 'tracking_status';
    const ID = 'id';
    const ID_ACADEMIC_PROGRAM = 'id_academic_program';
    const PROGRAM_STATUS = 'program_stauts';
    public $id;
    public $id_moodle_user;
    public $id_ases_user;
    public $id_academic_program;
    public $tracking_status;
    public $program_status;
    public function __construct($data = null)
    {
        parent::__construct($data);
        $this->tracking_status = 1;
        $this->program_status = 1;
    }

    public static function get_numeric_fields(): array  {
        return array (
            AsesUserExtended::ID_ACADEMIC_PROGRAM,
            AsesUserExtended::ID,
            AsesUserExtended::ID_ASES_USER,
            AsesUserExtended::ID_MOODLE_USER,
            AsesUserExtended::PROGRAM_STATUS
        );
    }

    public  static function get_table_name(): string {
        return AsesUserExtended::TABLE_NAME;
    }

    /**
     * Get the programs in which the user has records
     * @return array Array of Programas
     * @throws dml_exception
     */
    public function get_programs() {
        return AsesUserExtended::get_programs_by_ases_user_id($this->id_ases_user);
    }
    /**
     * Retorna los programas en los cuales el usuario
     * tiene tracking status 1 en user extended
     * @param $id_ases_user
     * @return array
     * @throws dml_exception
     */
    public static function get_actie_programs_by_ases_user_id($id_ases_user) {

        $programs_std_obj = get_student_active_programs_by_ases_user_id($id_ases_user);
        return Programa::make_objects_from_std_objects_or_arrays($programs_std_obj);
    }

    public static function get_programs_by_ases_user_id($id_ases_user) {

        $programs_std_obj = get_student_programs_by_ases_user_id($id_ases_user);
        return Programa::make_objects_from_std_objects_or_arrays($programs_std_obj);
    }
    /**
     * Return the ASES user id from generic input
     * @param string|int|AsesUser $id_ases_user_or_ases_user Id of ases user or ases user instance
     * @return string|int
     */
    private static function get_ases_user_id_from_generic_input($id_ases_user_or_ases_user)  {
        $type = gettype($id_ases_user_or_ases_user);
        $id_user = -1;
        if ($type == 'number' || $type == 'integer' || $type == 'string') {
            $id_user = $id_ases_user_or_ases_user;
        } elseif (is_a($id_ases_user_or_ases_user, AsesUser::get_class_name())) {
            $id_user = $id_ases_user_or_ases_user->id;
        } else{
            throw new TypeError("Invalid type, given: $type, spected string, int or AsesUser instance");
        }

        return $id_user;
    }

    /**
     * Set all tracking status of ASES user to innactive
     * @param string|int|AsesUser $id_ases_user_or_ases_user Id of ases user or ases user instance
     * @return bool|null True if the tracking status has been set false otherwise
     * @throws dml_exception A DML specific exception is thrown for any errors.

     */
    public static function disable_all_tracking_status($id_ases_user_or_ases_user) {
        global $DB;

        $tracking_status_column = AsesUserExtended::TRACKING_STATUS;
        $tracking_status_inactive = TrackingStatus::INACTIVE;
        $id_ases_user_column = AsesUserExtended::ID_ASES_USER;

        $id_user = AsesUserExtended::get_ases_user_id_from_generic_input($id_ases_user_or_ases_user);
        $sql = <<<SQL
        UPDATE {talentospilos_user_extended} SET $tracking_status_column = $tracking_status_inactive
        WHERE  $id_ases_user_column = '$id_user'
        
SQL;
        return $DB->execute($sql);
    }
}
?>
