<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 8/02/19
 * Time: 04:20 PM
 */
require_once (__DIR__ . '/DAO/BaseDAO.php');
class CondicionExcepcion extends BaseDAO
{

    public $id;
    public $condicion_excepcion;
    public $alias;
    public const ALIAS = 'alias';
    public const ID = 'id';
    public const CONDICION_EXCEPCION = 'condicion_excepcion';
public static  function get_table_name(): string {
    return 'talentospilos_cond_excepcion';
}
}