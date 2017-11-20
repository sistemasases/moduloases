<?php 

require_once(dirname(__FILE__).'/../../../config.php');

function xmldb_block_ases_upgrade($oldversion = 0) {
    global $DB;
    $dbman = $DB->get_manager();

    $result = true;

    if ($oldversion < 2017111715539) {

    // Drop constraint

    $sql_sentence = "ALTER TABLE {talentospilos_funcionalidad} DROP CONSTRAINT mdl_talefunc_nom_uk";
    $DB->execute($sql_sentence);
    // Drop index

    $sql_sentence = "DROP INDEX mdl_talefunc_nom_uk";
    $DB->execute($sql_sentence);

    // Changing precision of field nombre_func on table talentospilos_funcionalidad to (200).
    $table = new xmldb_table('talentospilos_funcionalidad');
    $field = new xmldb_field('nombre_func', XMLDB_TYPE_CHAR, '200', null, XMLDB_NOTNULL, null, null, 'id');

    // Launch change of precision for field nombre_func.
    $dbman->change_field_precision($table, $field);
   
    // Ases savepoint reached.
    upgrade_block_savepoint(true, 2017111715539, 'ases');
   
    return $result;
    }
}
?>