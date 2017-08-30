<?php 

require_once(dirname(__FILE__).'/../../../config.php');

function xmldb_block_ases_upgrade($oldversion = 0) {
    global $DB;
    $dbman = $DB->get_manager();

    $result = true;

    /// Add a new column newcol to the mdl_myqtype_options
    if ($result && $oldversion < 201708171158) {
        
        // /**
        //  * Cambios en el modelo asociados a la gestión de acciones y permisos
        //  **/
        
        // // Define table talentospilos_accion to be created.
        // $table = new xmldb_table('talentospilos_accion');

        // // Adding fields to table talentospilos_accion.
        // $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        // $table->add_field('nombre_accion', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
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
        // $table->add_key('nombre_unico', XMLDB_KEY_UNIQUE, array('nombre_perfil'));

        // // Conditionally launch create table for talentospilos_perfil.
        // if (!$dbman->table_exists($table)) {
        //     $dbman->create_table($table);
        // }

        // // Define table talentospilos_perfil_accion to be created.
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
        
        //  /**
        //  * Finaliza cambios en el modelo asociados a la gestión de acciones y permisos
        //  * Inicia adición de campos a la tabla talentospilos_seguimiento
        //  **/
        
        // //Adicion de campos revisado_profesional y revisado_practicante a tabla tp_seguimiento 
           
        //      // Define field revisado_profesional to be added to talentospilos_seguimiento.
        // $table = new xmldb_table('talentospilos_seguimiento');
        // $field = new xmldb_field('revisado_profesional', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'registroid');

        // // Conditionally launch add field revisado_profesional.
        // if (!$dbman->field_exists($table, $field)) {
        //     $dbman->add_field($table, $field);
        // }

        // // Define field revisado_practicante to be added to talentospilos_seguimiento.
        // $table = new xmldb_table('talentospilos_seguimiento');
        // $field = new xmldb_field('revisado_practicante', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'revisado_profesional');

        // // Conditionally launch add field revisado_practicante.
        // if (!$dbman->field_exists($table, $field)) {
        //     $dbman->add_field($table, $field);
        // }
        
        //  /**
        //  * Finaliza adición de campos a la tabla talentospilos_seguimiento
        //  * Inicia cambios en la tabla talentospilos_motivos
        //  **/
           
        // // Rename field decripcion on table talentospilos_motivos to descripcion.
        // $table = new xmldb_table('talentospilos_motivos');
        // $field = new xmldb_field('decripcion', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'id');

        // // Launch rename field descripcion.
        // $dbman->rename_field($table, $field, 'descripcion');

        // /**
        //  * Finaliza cambios en la tabla talentospilos_motivos
        //  * Creción de las tablas para almacenar el historial en los cambios de estado del estudiante
        //  **/
         
        //   // Define table talentospilos_est_estadoases to be created.
        // $table = new xmldb_table('talentospilos_est_estadoases');

        // // Adding fields to table talentospilos_est_estadoases.
        // $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        // $table->add_field('id_estudiante', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('id_estado_ases', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('id_motivo_retiro', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('fecha', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);

        // // Adding keys to table talentospilos_est_estadoases.
        // $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        // $table->add_key('id_estudiante_fk', XMLDB_KEY_FOREIGN, array('id_estudiante'), 'talentospilos_usuario', array('id'));
        // $table->add_key('id_estado_ases', XMLDB_KEY_FOREIGN, array('id_estado_ases'), 'talentospilos_estados_ases', array('id'));
        // $table->add_key('id_motivo_fk', XMLDB_KEY_FOREIGN, array('id_motivo_retiro'), 'talentospilos_motivos', array('id'));

        // // Conditionally launch create table for talentospilos_est_estadoases.
        // if (!$dbman->table_exists($table)) {
        //     $dbman->create_table($table);
        // }
        
        // // Define table talentospilos_est_estadoicet to be created.
        // $table = new xmldb_table('talentospilos_est_estadoicet');

        // // Adding fields to table talentospilos_est_estadoases.
        // $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        // $table->add_field('id_estudiante', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('id_estado_icet', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('id_motivo_retiro', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('fecha', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);

        // // Adding keys to table talentospilos_est_estadoases.
        // $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        // $table->add_key('id_estudiante_fk', XMLDB_KEY_FOREIGN, array('id_estudiante'), 'talentospilos_usuario', array('id'));
        // $table->add_key('id_estado_icet', XMLDB_KEY_FOREIGN, array('id_estado_icet'), 'talentospilos_estados_icetex', array('id'));
        // $table->add_key('id_motivo_fk', XMLDB_KEY_FOREIGN, array('id_motivo_retiro'), 'talentospilos_motivos', array('id'));

        // // Conditionally launch create table for talentospilos_est_estadoases.
        // if (!$dbman->table_exists($table)) {
        //     $dbman->create_table($table);
        // }
        
        // // Rename field estado on table talentospilos_usuario to NEWNAMEGOESHERE.
        // $table = new xmldb_table('talentospilos_usuario');
        // $field = new xmldb_field('estado_ases', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, 'ACTIVO', 'estado_icetex');

        // // Launch rename field estado.
        // $dbman->rename_field($table, $field, 'estado');
        
        /**
         * Finaliza creción de las tablas para almacenar el historial en los cambios de estado del estudiante
         * Inicia migración de información a nuevas tablas
         **/        
         
        // $sql_query = "SELECT id, estado_ases, estado_icetex FROM {talentospilos_usuario}";
        // $ases_students = $DB->get_records_sql($sql_query);
        
        // $sql_query = "SELECT nombre, id, descripcion FROM {talentospilos_estados_ases}";
        // $ases_statuses = $DB->get_records_sql($sql_query);
         
        // for($i = 0; $i < count($ases_students); $i++){
        //     switch($result[$i]->estado_ases){
        //         case "ACTIVO":
                    
        //             $date = now();
                    
        //             $updatable_object->id_estudiante = $ases_students[$i]->id;
        //             $updatable_object->estado_ases =  $ases_statuses['ACTIVO'];
        //             $updatable_object->fecha = "";
        //             break;
        //         case "":
        //             break;
        //         case "":
        //             break;
        //         case "":
        //             break;
        //     }
        // }
        

         // Define table talentospilos_est_estadoases to be created.
        $table = new xmldb_table('talentospilos_est_estadoases');

        // Adding fields to table talentospilos_est_estadoases.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('id_estudiante', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('id_estado_ases', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('id_motivo_retiro', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('fecha', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table talentospilos_est_estadoases.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('id_estudiante_fk', XMLDB_KEY_FOREIGN, array('id_estudiante'), 'talentospilos_usuario', array('id'));
        $table->add_key('id_estado_ases', XMLDB_KEY_FOREIGN, array('id_estado_ases'), 'talentospilos_estados_ases', array('id'));
        $table->add_key('id_motivo_fk', XMLDB_KEY_FOREIGN, array('id_motivo_retiro'), 'talentospilos_motivos', array('id'));

        // Conditionally launch create table for talentospilos_est_estadoases.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define key id_motivo_fk (foreign) to be dropped form talentospilos_est_estadoases.
        $table = new xmldb_table('talentospilos_est_estadoases');
        $key = new xmldb_key('id_motivo_fk', XMLDB_KEY_FOREIGN, array('id_motivo_retiro'), 'talentospilos_motivos', array('id'));

        // Launch drop key id_motivo_fk.
        $dbman->drop_key($table, $key);

        // Changing nullability of field id_motivo_retiro on table talentospilos_est_estadoases to null.
        $table = new xmldb_table('talentospilos_est_estadoases');
        $field = new xmldb_field('id_motivo_retiro', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'id_estado_ases');

        // Launch change of nullability for field id_motivo_retiro.
        $dbman->change_field_notnull($table, $field);

        // Define key id_motivo_fk (foreign) to be added to talentospilos_est_estadoases.
        $table = new xmldb_table('talentospilos_est_estadoases');
        $key = new xmldb_key('id_motivo_fk', XMLDB_KEY_FOREIGN, array('id_motivo_retiro'), 'talentospilos_motivos', array('id'));

        // Launch add key id_motivo_fk.
        $dbman->add_key($table, $key);


        // Ases savepoint reached.
        upgrade_block_savepoint(true, 201708171158, 'ases');
        
    }

    return $result;
}

?>

