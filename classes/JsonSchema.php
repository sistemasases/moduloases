<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 15/02/19
 * Time: 04:11 PM
 */
require_once(__DIR__.'/module.php');
class JsonSchema extends BaseDAO {
    public $id;
    public $json_schema;
    public $alias;
    const ALIAS = 'alias';
    const JSON_SCHEMA = 'json_schema';

    public static function get_table_name(): string
    {
        return 'talentospilos_json_schema';
    }

    public function get_json_schema_as_object() {
        return json_decode($this->json_schema);
    }
}