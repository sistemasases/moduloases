<?php
/**
 * Created by PhpStorm.
 * User: alejandro
 * Date: 7/31/19
 * Time: 4:39 AM
 */
require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

/* list of incorrect users */
$wrongusers = array("1722601-3841", "1780004-3484", "1780021-3146", "1780091-3857", "1780078-3484", "1780037-3743",
    "1780045-3545", "1780047-3545", "1780068-3651", "1780006-3541", "1780022-3753", "1780057-3740");
$wronglength = count($wrongusers);

/* gets id_moodle_user from {user} of the list of incorrect users */
$wrongid = array();
for($i = 0; $i < $wronglength; $i++) {
    $consulta = "SELECT * FROM {user} WHERE username LIKE '" . $wrongusers[$i] . "'";
    array_push($wrongid, $DB->get_record_sql($consulta)->id);
}
$idlength = count($wrongid);

/*This is the id_ases_user linked to document 9999999909*/
$replacementid = '10054';

/*The id_ases_user in {talentospilos_user_extended} of the incorrect users is replaced by $replacementid*/
for($i = 0; $i < $idlength; $i++) {
    $consulta = "SELECT * FROM {talentospilos_user_extended} WHERE id_moodle_user = '" . $wrongid[$i] . "'";
    $update = new StdClass;
    $update->id = $DB->get_record_sql($consulta)->id;
    $update->id_ases_user = $replacementid;
    $success = $DB->update_record('talentospilos_user_extended', $update);
}

if(success)
    {
        echo "Successful";
    } else
    {
        echo "Could not update";
    }

/* get the wrong registriess */