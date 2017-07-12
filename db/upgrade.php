<?php 

require_once(dirname(__FILE__).'/../../../config.php');

function xmldb_block_ases_upgrade($oldversion = 0) {
    global $DB;
    $dbman = $DB->get_manager();

    $result = true;

    /// Add a new column newcol to the mdl_myqtype_options
    if ($result && $oldversion < 2017071114) {
        
        //  // Define table talentospilos_accion to be created.
        // $table = new xmldb_table('talentospilos_accion');

        // // Adding fields to table talentospilos_accion.
        // $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        // $table->add_field('nombre_accion', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        // $table->add_field('descripcion', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        // $table->add_field('estado', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);

        // // Adding keys to table talentospilos_accion.
        // $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        // $table->add_key('unico_nombre', XMLDB_KEY_UNIQUE, array('nombre_accion'));

        // // Conditionally launch create table for talentospilos_accion.
        // if (!$dbman->table_exists($table)) {
        //     $dbman->create_table($table);
        // }
        


        // // Define table talentospilos_perfil to be created.
        // $table = new xmldb_table('talentospilos_perfil');

        // // Adding fields to table talentospilos_perfil.
        // $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        // $table->add_field('nombre_perfil', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('descripcion', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        // // Adding keys to table talentospilos_perfil.
        // $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // // Conditionally launch create table for talentospilos_perfil.
        // if (!$dbman->table_exists($table)) {
        //     $dbman->create_table($table);
        // }
        
        //   // Define table talentospilos_perfil_accion to be created.
        // $table = new xmldb_table('talentospilos_perfil_accion');

        // // Adding fields to table talentospilos_perfil_accion.
        // $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        // $table->add_field('id_perfil', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('id_accion', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('habilitado', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);

        // // Adding keys to table talentospilos_perfil_accion.
        // $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        // $table->add_key('perfil_fk', XMLDB_KEY_FOREIGN, array('id_perfil'), 'talentospilos_perfil', array('id'));
        // $table->add_key('accion_fk', XMLDB_KEY_FOREIGN, array('id_accion'), 'talentospilos_accion', array('id'));

        // // Conditionally launch create table for talentospilos_perfil_accion.
        // if (!$dbman->table_exists($table)) {
        //     $dbman->create_table($table);
        // }
        
        //   // Define table talentospilos_usuario_perfil to be created.
        // $table = new xmldb_table('talentospilos_usuario_perfil');

        // // Adding fields to table talentospilos_usuario_perfil.
        // $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        // $table->add_field('id_perfil', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('id_usuario', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('estado', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('id_semestre', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('id_jefe', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        // $table->add_field('id_instancia', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // // Adding keys to table talentospilos_usuario_perfil.
        // $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        // $table->add_key('perfilu_fk', XMLDB_KEY_FOREIGN, array('id_perfil'), 'talentospilos_perfil', array('id'));
        // $table->add_key('perfil_usuario_fk', XMLDB_KEY_FOREIGN, array('id_usuario'), 'talentospilos_usuario', array('id'));
        // $table->add_key('semestrepu_fk', XMLDB_KEY_FOREIGN, array('id_semestre'), 'talentospilos_semestre', array('id'));
        // $table->add_key('intanciaup_pk', XMLDB_KEY_FOREIGN, array('id_instancia'), 'block_instances', array('id'));
        // $table->add_key('jefeup_fk', XMLDB_KEY_FOREIGN, array('id_jefe'), 'user', array('id'));

        // // Conditionally launch create table for talentospilos_usuario_perfil.
        // if (!$dbman->table_exists($table)) {
        //     $dbman->create_table($table);
        // }
        
        //Adicion de campos revisado_profesional y revisado_practicante a tabla tp_seguimiento 
           
             // Define field revisado_profesional to be added to talentospilos_seguimiento.
        $table = new xmldb_table('talentospilos_seguimiento');
        $field = new xmldb_field('revisado_profesional', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'registroid');

        // Conditionally launch add field revisado_profesional.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field revisado_practicante to be added to talentospilos_seguimiento.
        $table = new xmldb_table('talentospilos_seguimiento');
        $field = new xmldb_field('revisado_practicante', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'revisado_profesional');

        // Conditionally launch add field revisado_practicante.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        
        // Ases savepoint reached.
        upgrade_block_savepoint(true, 2017071114, 'ases');
        
    }

    return $result;
}

?>

