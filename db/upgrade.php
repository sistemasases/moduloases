<?php 

require_once(dirname(__FILE__).'/../../../config.php');

function xmldb_block_ases_upgrade($oldversion = 0) {
    global $DB;
    $dbman = $DB->get_manager();

    $result = true;

    if ($oldversion < 2018011716029) {

    // ************************************************************************************************************
    // Actualización que crea la tabla para los campos extendidos de usuario (Tabla: {talentospilos_user_extended})
    // Versión: 2018010911179
    // ************************************************************************************************************

    // Define table talentospilos_user_extended to be created.
    $table = new xmldb_table('talentospilos_user_extended');

    // Adding fields to table talentospilos_user_extended.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('id_moodle_user', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    $table->add_field('id_ases_user', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    $table->add_field('id_academic_program', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    $table->add_field('program_status', XMLDB_TYPE_BINARY, null, null, XMLDB_NOTNULL, null, null);

    // Adding keys to table talentospilos_user_extended.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_key('fk1_moodle_user', XMLDB_KEY_FOREIGN, array('id_moodle_user'), 'user', array('id'));
    $table->add_key('fk2_ases_user', XMLDB_KEY_FOREIGN, array('id_ases_user'), 'talentospilos_usuario', array('id'));
    $table->add_key('fk3_academic_program', XMLDB_KEY_FOREIGN, array('id_academic_program'), 'talentospilos_programa', array('id'));

    // Conditionally launch create table for talentospilos_user_extended.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
    // ************************************************************************************************************

    // ************************************************************************************************************
    // Actualización:
    // Añade el campo id_funcionalidad en la tabla {talentospilos_est_estadoases}
    // Versión en la que se incluye: 2018011716029
    // ************************************************************************************************************

    // Define field id_instancia to be added to talentospilos_est_estadoases.
    $table = new xmldb_table('talentospilos_est_estadoases');
    $field = new xmldb_field('id_instancia', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0', 'fecha');

    // Conditionally launch add field id_instancia.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);

        // Define key id_instancia_fk (foreign) to be added to talentospilos_est_estadoases.
        $table = new xmldb_table('talentospilos_est_estadoases');
        $key = new xmldb_key('id_instancia_fk', XMLDB_KEY_FOREIGN, array('id_instancia'), 'talentospilos_instancia', array('id'));

        // Launch add key id_instancia_fk.
        $dbman->add_key($table, $key);
    }

    // ************************************************************************************************************
    // Actualización:
    // Añade el campo estado_seguimiento en la tabla {talentospilos_user_extended}
    // Versión en la que se incluye: 2018011716029
    // ************************************************************************************************************
    // Define field id_instancia to be added to talentospilos_est_estadoases.
    $table = new xmldb_table('talentospilos_user_extended');
    $field = new xmldb_field('estado_seguimiento', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'program_status');

    // Conditionally launch add field id_instancia.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // ************************************************************************************************************
    // Actualización:
    // Añade el campo estado_seguimiento en la tabla {talentospilos_user_extended}
    // Versión en la que se incluye: 2018011716029
    // ************************************************************************************************************

    // Ases savepoint reached.
    upgrade_block_savepoint(true, 2018011716029, 'ases');
   
    return $result;
    }
}
?>