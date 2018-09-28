<?php

interface IBaseDAO {
    /**
     * Return simple string than represents the table name without prefix
     * @example return 'talentospilos_usuario';
     * @return string
     */
    public static function get_table_name(): string;

    /**
     * Format the current object before save in database
     * Use this method if you need modify format any field before insertion, for example
     * convert string date to unix time because in database the format of date is big int
     */
    public function format();
}

?>