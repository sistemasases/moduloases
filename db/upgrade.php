<?php 

require_once(dirname(__FILE__).'/../../../config.php');

function xmldb_block_ases_upgrade($oldversion = 0) {
    global $DB;
    $dbman = $DB->get_manager();

    $result = true;


    if ($oldversion < 2017101515448) {


        $sql_query = "DELETE FROM {talentospilos_permisos_rol}";
        $success = $DB->execute($sql_query);

        // Define field id_accion to be added to talentospilos_permisos_rol.
        $table = new xmldb_table('talentospilos_permisos_rol');
        $field = new xmldb_field('id_accion', 2017101515448, '20', null, XMLDB_NOTNULL, null, null, 'id_permiso');

         $object = new stdClass();
          $object->id = 1;
          $object->id_rol =4;
          $object->id_accion=1;
              
        $insert_record = $DB->insert_record('talentospilos_permisos_rol', $object, true);
                

        // Conditionally launch add field id_accion.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }


        // Define key permisosr_fk3 (foreign) to be added to talentospilos_permisos_rol.
        $table = new xmldb_table('talentospilos_permisos_rol');
        $key = new xmldb_key('permisosr_fk3', XMLDB_KEY_FOREIGN, array('id_accion'), 'talentospilos_accion', array('id'));

        // Launch add key permisosr_fk3.
        $dbman->add_key($table, $key);



        // Ases savepoint reached.
        upgrade_block_savepoint(true, 2017101515448, 'ases');
    }


    //  if ($oldversion < 2017101313445) {


    //     // Rename field id_accion on table talentospilos_permisos_rol to NEWNAMEGOESHERE.
    //     $table = new xmldb_table('talentospilos_permisos_rol');
    //     $field = new xmldb_field('id_accion', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id_rol');

    //             // Define key permisosr_fk2 (foreign) to be added to talentospilos_permisos_rol.
    //     $table = new xmldb_table('talentospilos_permisos_rol');
    //     $key = new xmldb_key('permisosr_fk2', XMLDB_KEY_FOREIGN, array('id_accion'), 'talentospilos_accion', array('id'));

    //     // Launch add key permisosr_fk2.
    //     $dbman->add_key($table, $key);

    //     // Launch rename field id_accion.
    //     $dbman->rename_field($table, $field, 'NEWNAMEGOESHERE');
    //     upgrade_block_savepoint(true, 2017101313445, 'ases');
    // }



    //     if ($oldversion < 201710131234) {

    //     // Define field id_funcionalidad to be added to talentospilos_accion.
    //     $table = new xmldb_table('talentospilos_accion');
    //     $field = new xmldb_field('id_funcionalidad', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null, 'estado');

    //     // Conditionally launch add field id_funcionalidad.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }

    //     // Ases savepoint reached.

    //     // Define key accion_fk1 (foreign) to be added to talentospilos_accion.
    //     $table = new xmldb_table('talentospilos_accion');
    //     $key = new xmldb_key('accion_fk1', XMLDB_KEY_FOREIGN, array('id_funcionalidad'), 'talentospilos_funcionalidad', array('id'));

    //     // Launch add key accion_fk1.
    //     $dbman->add_key($table, $key);

    //     // Ases savepoint reached.
    //     upgrade_block_savepoint(true, 201710131234, 'ases');
    // }



    // /// Add a new column newcol to the mdl_myqtype_options
    // if ($result && $oldversion < 201709200638) {
        
    //     // Define field id_semestre to be added to talentospilos_monitor_estud.
    //     $table = new xmldb_table('talentospilos_monitor_estud');
    //     $field = new xmldb_field('id_semestre', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'id_instancia');

    //     // Conditionally launch add field id_semestre.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }

    //     // Define key semester_fk (foreign) to be added to talentospilos_monitor_estud.
    //     //$table = new xmldb_table('talentospilos_monitor_estud');
    //     //$key = new xmldb_key('semester_fk', XMLDB_KEY_FOREIGN, array('id_semestre'), 'talentospilos_semestre', array('id'));

    //     // Launch add key mon_est_pk1.
    //     //$dbman->add_key($table, $key);


    //      // Define key mon_est_un (unique) to be added to talentospilos_monitor_estud.
    //     $table = new xmldb_table('talentospilos_monitor_estud');
    //     $key = new xmldb_key('unique_key_1', XMLDB_KEY_UNIQUE, array('id_monitor', 'id_estudiante', 'id_instancia', 'id_semestre'));

    //     // Launch add key mon_est_un.
    //     $dbman->add_key($table, $key);

    //     // Define key mon_est_un (unique) to be dropped form talentospilos_monitor_estud.
    //     $table = new xmldb_table('talentospilos_monitor_estud');
    //     $key = new xmldb_key('mon_est_un', XMLDB_KEY_UNIQUE, array('id_monitor', 'id_estudiante', 'id_instancia'));

    //     // Launch drop key mon_est_un.
    //     $dbman->drop_key($table, $key);


    //     // Ases savepoint reached.
    //     upgrade_block_savepoint(true, 201709200638, 'ases');
    // }
    return $result;
}

?>

