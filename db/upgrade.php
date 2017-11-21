<?php 

require_once(dirname(__FILE__).'/../../../config.php');

function xmldb_block_ases_upgrade($oldversion = 0) {
    global $DB;
    $dbman = $DB->get_manager();

    $result = true;

    if ($oldversion < 2017111715539) {

    // Drop constraint

        // Define table talentospilos_funcionalidad to be dropped.
        $table = new xmldb_table('talentospilos_funcionalidad');
        
        // Conditionally launch drop table for talentospilos_funcionalidad.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

    // Define table talentospilos_funcionalidad to be created.
    $table = new xmldb_table('talentospilos_funcionalidad');
    
    // Adding fields to table talentospilos_funcionalidad.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('nombre_func', XMLDB_TYPE_CHAR, '200', null, XMLDB_NOTNULL, null, null);
    $table->add_field('descripcion', XMLDB_TYPE_CHAR, '150', null, null, null, null);

    // Adding keys to table talentospilos_funcionalidad.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_key('unique', XMLDB_KEY_UNIQUE, array('nombre_func'));

    // Conditionally launch create table for talentospilos_funcionalidad.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }


    // Define table talentospilos_director_prog to be created.
    $table = new xmldb_table('talentospilos_director_prog');
    
    // Adding fields to table talentospilos_director_prog.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('id_director', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    $table->add_field('id_programa', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);

    // Adding keys to table talentospilos_director_prog.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_key('id_director_fk', XMLDB_KEY_FOREIGN, array('id_director'), 'user', array('id'));
    $table->add_key('id_programa_fk', XMLDB_KEY_FOREIGN, array('id_programa'), 'talentospilos_programa', array('id'));

    // Conditionally launch create table for talentospilos_director_prog.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
   
    // Ases savepoint reached.
    upgrade_block_savepoint(true, 2017111715539, 'ases');
   
    return $result;
    }
}
?>