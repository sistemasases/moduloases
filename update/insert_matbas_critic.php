<?php
/**
 * Created by PhpStorm.
 * User: alejandro
 * Date: 8/21/19
 * Time: 2:53 PM
 */
require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

$insert = new StdClass;
$insert->codigo_materia = "111095M";
$success = $DB->insert_record('talentospilos_materias_criti', $insert);


if(success)
{
    echo "Successful";
} else
{
    echo "Could not update";
}