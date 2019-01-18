<?php
require_once(__DIR__ . '/DAO/BaseDAO.php');

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

    public static function get_numeric_fields(): array  {
        return array (
            AsesUserExtended::ID_ACADEMIC_PROGRAM,
            AsesUserExtended::ID,
            AsesUserExtended::ID_ASES_USER,
            AsesUserExtended::ID_MOODLE_USER,
            AsesUserExtended::PROGRAM_STATUS
        );
    }
    public static function exist_by_username($mdl_user_name) {
        global $DB;
        $sql = <<<SQL
        select * from {user} mdl_user inner join {talentospilos_user_extended} mdl_talentospilos_user_extended
        on mdl_talentospilos_user_extended.id_moodle_user = mdl_user.id
        where username = '$mdl_user_name'
SQL;

        $DB->record_exists_sql($sql);
    }
    public  static function get_table_name(): string {
        return AsesUserExtended::TABLE_NAME;
    }

    /**
     * Check if exists some registry at table user extended by ases user id
     * @param string  ases_user_id Id of ases user
     * @return bool True if exist some registry false otherwise
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public static function check_if_exists_by_ases_user_id($ases_user_id): bool {
        global $DB;
        return $DB->record_exists( AsesUserExtended::TABLE_NAME, array(AsesUserExtended::ID_ASES_USER=> $ases_user_id) );
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
     * Check if the user have one or more active tracking status
     * @param string|int|AsesUser $id_ases_user_or_ases_user Id of ases user or ases user instance
     * @return bool
     */
    public static function have_active_tracking_status($id_ases_user_or_ases_user) {
        global $DB;
        $type = gettype($id_ases_user_or_ases_user);
        $id_user = AsesUserExtended::get_ases_user_id_from_generic_input($id_ases_user_or_ases_user);
        return $DB->record_exists(AsesUserExtended::TABLE_NAME, array(
            AsesUserExtended::ID_ASES_USER=> $id_user,
            AsesUserExtended::TRACKING_STATUS=> TrackingStatus::ACTIVE ));

    }
    /**
     * Set all tracking status of ASES user to innactive
     * @param string|int|AsesUser $id_ases_user_or_ases_user Id of ases user or ases user instance
     * @return bool|null True if the tracking status has been set false otherwise
     * @throws dml_exception A DML specific exception is thrown for any errors.

     */
    public static function disable_all_tracking_status($id_ases_user_or_ases_user) {
        global $DB;
        $table_name = AsesUserExtended::get_table_name();

        $tracking_status_column = AsesUserExtended::TRACKING_STATUS;
        $tracking_status_inactive = TrackingStatus::INACTIVE;
        $id_ases_user_column = AsesUserExtended::ID_ASES_USER;

        $id_user = AsesUserExtended::get_ases_user_id_from_generic_input($id_ases_user_or_ases_user);
        $sql = "UPDATE {talentospilos_user_extended} SET $tracking_status_column = $tracking_status_inactive
        WHERE  $id_ases_user_column = '$id_user'
        ";
        if($DB->execute($sql)) {
            return true;
        }
        return false;
    }
}
?>
