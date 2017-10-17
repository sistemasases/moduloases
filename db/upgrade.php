<?php 

require_once(dirname(__FILE__).'/../../../config.php');

function xmldb_block_ases_upgrade($oldversion = 0) {
    global $DB;
    $dbman = $DB->get_manager();

    $result = true;


    if ($oldversion < 2017101515448) {
        
       $table = new xmldb_table('talentospilos_permisos_rol');
       $key = new xmldb_key('mdl_talepermrol_id_id_id__uk', XMLDB_KEY_FOREIGN, array('id_permiso'), 'talentospilos_permisos', array('id'));
       
       $dbman->drop_key($table, $key);

       $sql_query = "DROP INDEX mdl_talepermrol_id_id_id__uk";
       $succes = $DB->execute($sql_query);

        // Define key permisosr_fk2 (foreign) to be dropped form talentospilos_permisos_rol.
       $table = new xmldb_table('talentospilos_permisos_rol');
       $key = new xmldb_key('permisosr_fk2', XMLDB_KEY_FOREIGN, array('id_permiso'), 'talentospilos_permisos', array('id'));

       // Launch drop key permisosr_fk2.
       $dbman->drop_key($table, $key);

        // Define key permisosr_fk3 (foreign) to be dropped form talentospilos_permisos_rol.
       $table = new xmldb_table('talentospilos_permisos_rol');
       $key = new xmldb_key('permisosr_fk3', XMLDB_KEY_FOREIGN, array('id_funcionalidad'), 'talentospilos_funcionalidad', array('id'));

       // Launch drop key permisosr_fk3.
       $dbman->drop_key($table, $key);

        // Define field id_permiso to be dropped from talentospilos_permisos_rol.
       $table = new xmldb_table('talentospilos_permisos_rol');
       $field = new xmldb_field('id_permiso');

       // Conditionally launch drop field id_permiso.
       if ($dbman->field_exists($table, $field)) {
           $dbman->drop_field($table, $field);
       }

         // Define field id_funcionalidad to be dropped from talentospilos_permisos_rol.
       $table = new xmldb_table('talentospilos_permisos_rol');
       $field = new xmldb_field('id_funcionalidad');

       // Conditionally launch drop field id_funcionalidad.
       if ($dbman->field_exists($table, $field)) {
           $dbman->drop_field($table, $field);


            // Define field id_funcionalidad to be added to talentospilos_permisos_rol.
       $table = new xmldb_table('talentospilos_permisos_rol');
       $field = new xmldb_field('id_accion', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, '');

       // Conditionally launch add field id_funcionalidad.
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


   
    return $result;
}

}

?>

