<?php 

require_once(dirname(__FILE__).'/../../../config.php');

function xmldb_block_ases_upgrade($oldversion = 0) {
    global $DB;
    $dbman = $DB->get_manager();

    $result = true;

    if ($oldversion < 2017110116289) {

    //     // Define field id_funcionalidad to be added to talentospilos_accion.
    //     $table = new xmldb_table('talentospilos_accion');
    //     $field = new xmldb_field('id_funcionalidad', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'estado');

    //     // Conditionally launch add field id_funcionalidad.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }

    //     // Define key funcionalidad_fk1 (foreign) to be added to talentospilos_accion.
    //     $table = new xmldb_table('talentospilos_accion');
    //     $key = new xmldb_key('funcionalidad_fk1', XMLDB_KEY_FOREIGN, array('id_funcionalidad'), 'talentospilos_funcionalidad', array('id'));

    //     // Launch add key funcionalidad_fk1.
    //     $dbman->add_key($table, $key);



    //   // Define table talentospilos_permisos_rol to be dropped.
    //     $table = new xmldb_table('talentospilos_permisos_rol');

    //     // Conditionally launch drop table for talentospilos_permisos_rol.
    //     if ($dbman->table_exists($table)) {
    //         $dbman->drop_table($table);
    //     }

    //   // Define table talentospilos_permisos_rol to be created.
    //     $table = new xmldb_table('talentospilos_permisos_rol');

    //     // Adding fields to table talentospilos_permisos_rol.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('id_rol', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('id_accion', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

    //     // Adding keys to table talentospilos_permisos_rol.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     $table->add_key('permisosr_upk', XMLDB_KEY_UNIQUE, array('id_rol', 'id_accion'));
    //     $table->add_key('permisosr_fk1', XMLDB_KEY_FOREIGN, array('id_rol'), 'talentospilos_rol', array('id'));
    //     $table->add_key('permisosr_fk2', XMLDB_KEY_FOREIGN, array('id_accion'), 'talentospilos_accion', array('id'));

    //     // Conditionally launch create table for talentospilos_permisos_rol.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }

    //     // Ases savepoint reached.
    //     upgrade_block_savepoint(true, 2017101818449, 'ases');
    // }

    // Rename field descripcion on table talentospilos_motivos to NEWNAMEGOESHERE.
    $table = new xmldb_table('talentospilos_motivos');
    $field = new xmldb_field('decripcion', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'id');

    // Launch rename field descripcion.
    $dbman->rename_field($table, $field, 'descripcion');

    $sql_query = "DELETE FROM {talentospilos_motivos}";

    $result = $DB->execute($sql_query);
    
    $array_motivos = array('Bajo rendimiento', 'Retiro voluntario', 'Calamidad doméstica', 'Enfermedad', 'Traslado de universidad', 'Situacion económica', 'Insatisfacción con el programa', 'Falta de adaptación a la vida universitaria');
    $data_object = new stdClass();

    if($result){
        for($i=0; $i<count($array_motivos); $i++){
            $data_object->descripcion = $array_motivos[$i];
            $DB->insert_record('talentospilos_motivos', $data_object, true);
        }
    }   

    // Ases savepoint reached.
    upgrade_block_savepoint(true, 2017110116289, 'ases');
   
    return $result;
    }
}
?>