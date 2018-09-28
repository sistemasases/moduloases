<?php
require_once(__DIR__.'/DAO/IBaseDAO.php');
require_once(__DIR__.'/DAO/BaseDAO.php');

class UserExtended extends BaseDAO implements IBaseDAO{
    const TABLE_NAME = 'talentospilos_user_extended';
    const ID_ASES_USER = 'id_ases_user';
    public $id;
    public $id_moodle_user;
    public $id_ases_user;
    public $id_academic_program;
    public $tracking_status;
    public $program_status;

    /**
     * @return $this
     */
    public function format() {
        return $this;
    }
    public  static function get_table_name(): string {
        return UserExtended::TABLE_NAME;
        $this->check
    }
    /**
     * Check if exists some registry at table user extended by ases user id
     * @param string  ases_user_id Id of ases user
     * @return bool True if exist some registry false otherwise
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public static function check_if_exists_by_ases_user_id($ases_user_id): bool {
        global $DB;
        return $DB->record_exists( UserExtended::TABLE_NAME, array(UserExtended::ID_ASES_USER=> $ases_user_id) );
    }
}
?>
