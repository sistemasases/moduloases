<?php 
require_once(dirname(__FILE__).'/../../../config.php');
function xmldb_block_ases_upgrade($oldversion = 0) {
    global $DB;
    $dbman = $DB->get_manager();
    $result = true;
    if ($oldversion < 2019040212300 ) {
      
    //     // ************************************************************************************************************
    //     // Actualización que crea la tabla para los campos extendidos de usuario (Tabla: {talentospilos_user_extended})
    //     // Versión: 2018010911179
    //     // ************************************************************************************************************
    //     // Define table talentospilos_user_extended to be created.
    //     $table = new xmldb_table('talentospilos_user_extended');
    //     // Adding fields to table talentospilos_user_extended.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('id_moodle_user', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('id_ases_user', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('id_academic_program', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('program_status', XMLDB_TYPE_BINARY, null, null, XMLDB_NOTNULL, null, null);
    //     // Adding keys to table talentospilos_user_extended.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     $table->add_key('fk1_moodle_user', XMLDB_KEY_FOREIGN, array('id_moodle_user'), 'user', array('id'));
    //     $table->add_key('fk2_ases_user', XMLDB_KEY_FOREIGN, array('id_ases_user'), 'talentospilos_usuario', array('id'));
    //     $table->add_key('fk3_academic_program', XMLDB_KEY_FOREIGN, array('id_academic_program'), 'talentospilos_programa', array('id'));
    //     // Conditionally launch create table for talentospilos_user_extended.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     // ************************************************************************************************************
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Añade el campo id_funcionalidad en la tabla {talentospilos_est_estadoases}
    //     // Versión en la que se incluye: 2018011716029
    //     // ************************************************************************************************************
    //     // Define field id_instancia to be added to talentospilos_est_estadoases.
    //     $table = new xmldb_table('talentospilos_est_estadoases');
    //     $field = new xmldb_field('id_instancia', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0', 'fecha');
    //     // Conditionally launch add field id_instancia.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //         // Define key id_instancia_fk (foreign) to be added to talentospilos_est_estadoases.
    //         $table = new xmldb_table('talentospilos_est_estadoases');
    //         $key = new xmldb_key('id_instancia_fk', XMLDB_KEY_FOREIGN, array('id_instancia'), 'talentospilos_instancia', array('id'));
    //         // Launch add key id_instancia_fk.
    //         $dbman->add_key($table, $key);
    //     }
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Añade el campo estado_seguimiento en la tabla {talentospilos_user_extended}
    //     // Versión en la que se incluye: 2018011716029
    //     // ************************************************************************************************************
    //     // Define field id_instancia to be added to talentospilos_est_estadoases.
    //     $table = new xmldb_table('talentospilos_user_extended');
    //     $field = new xmldb_field('tracking_status', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'program_status');
    //     // Conditionally launch add field id_instancia.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se modifica el nombre del campo estado_seguimiento en la tabla {talentospilos_user_extended}. Se pasa de estado_seguimiento a tracking_status
    //     // Versión en la que se incluye: 2018011911069
    //     // ************************************************************************************************************
    //     // Rename field id_estado_icetex on table talentospilos_est_est_icetex to tracking_status.
    //     //$table = new xmldb_table('talentospilos_user_extended');
    //     //$field = new xmldb_field('estado_seguimiento', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'program_status');
    //     // Launch rename field id_estado_icetex.
    //     //$dbman->rename_field($table, $field, 'tracking_status');
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se crea la tabla {talentospilos_instancia_cohorte}. Con los campos
    //     //          id --> Autoincremental
    //     //          id_cohorte --> Identificador de la cohorte 
    //     //          id_instancia --> Identificador de la instancia relacionada a la cohorte
    //     // Versión en la que se incluye: 2018011911069
    //     // ************************************************************************************************************
        
    //     // Define table talentospilos_inst_cohorte to be created.
    //     $table = new xmldb_table('talentospilos_inst_cohorte');
    //     // Adding fields to table talentospilos_inst_cohorte.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('id_cohorte', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('id_instancia', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     // Adding keys to table talentospilos_inst_cohorte.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     // Conditionally launch create table for talentospilos_inst_cohorte.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se crea la tabla {talentospilos_history_academ}. Con los campos
    //     //          id --> Autoincremental
    //     //          id_estudiante --> Identificador del estudiante ASES
    //     //          id_semestre --> Identificador del semestre o periodo académico. Apunta a {talentospilos_semestre}
    //     //          id_programa --> Identificador del programa académico. Apunta a {talentospilos_programa}
    //     //          promedio_semestre --> Promedio semestral
    //     //          promedio_acumulado --> Promedio acumulado 
    //     //          json_materias --> Materias relacionadas al período académico
    //     // Versión en la que se incluye: 2018011911069
    //     // ************************************************************************************************************
        
    //     // Define table talentospilos_history_academ to be created.
    //     $table = new xmldb_table('talentospilos_history_academ');
        
    //     // Adding fields to table talentospilos_history_academ.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     // Adding keys to table talentospilos_history_academ.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     // Conditionally launch create table for talentospilos_history_academ.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     // Define field id_estudiante to be added to talentospilos_history_academ.
    //     $table = new xmldb_table('talentospilos_history_academ');
    //     $field = new xmldb_field('id_estudiante', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');
    //     // Conditionally launch add field id_estudiante.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // Define field id_semestre to be added to talentospilos_history_academ.
    //     $table = new xmldb_table('talentospilos_history_academ');
    //     $field = new xmldb_field('id_semestre', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id_estudiante');
    //     // Conditionally launch add field id_semestre.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // Define field id_programa to be added to talentospilos_history_academ.
    //     $table = new xmldb_table('talentospilos_history_academ');
    //     $field = new xmldb_field('id_programa', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id_semestre');
    //     // Conditionally launch add field id_programa.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    // // Define field promedio_semestre to be added to talentospilos_history_academ.
    // $table = new xmldb_table('talentospilos_history_academ');
    // $field = new xmldb_field('promedio_semestre', XMLDB_TYPE_FLOAT, '20', null, null, null, null, 'id_programa');
    // // Conditionally launch add field promedio_semestre.
    // if (!$dbman->field_exists($table, $field)) {
    //     $dbman->add_field($table, $field);
    // }
    // // Define field promedio_acumulado to be added to talentospilos_history_academ.
    // $table = new xmldb_table('talentospilos_history_academ');
    // $field = new xmldb_field('promedio_acumulado', XMLDB_TYPE_FLOAT, '20', null, null, null, null, 'promedio_semestre');
    // // Conditionally launch add field promedio_acumulado.
    // if (!$dbman->field_exists($table, $field)) {
    //     $dbman->add_field($table, $field);
    // }
        
    //     // Define field json_materias to be added to talentospilos_history_academ.
    //     $table = new xmldb_table('talentospilos_history_academ');
    //     $field = new xmldb_field('json_materias', XMLDB_TYPE_TEXT, null, null, null, null, null, 'promedio_acumulado');
    //     // Conditionally launch add field json_materias.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // Define key fk_estudiante (foreign) to be added to talentospilos_history_academ.
    //     $table = new xmldb_table('talentospilos_history_academ');
    //     $key = new xmldb_key('fk_estudiante', XMLDB_KEY_FOREIGN, array('id_estudiante'), 'talentospilos_usuario', array('id'));
    //     // Launch add key fk_estudiante.
    //     $dbman->add_key($table, $key);
    //     // Define key fk_semestre (foreign) to be added to talentospilos_history_academ.
    //     $table = new xmldb_table('talentospilos_history_academ');
    //     $key = new xmldb_key('fk_semestre', XMLDB_KEY_FOREIGN, array('id_semestre'), 'talentospilos_semestre', array('id'));
    //     // Launch add key fk_semestre.
    //     $dbman->add_key($table, $key);
    //     // Define key fk_programa (foreign) to be added to talentospilos_history_academ.
    //     $table = new xmldb_table('talentospilos_history_academ');
    //     $key = new xmldb_key('fk_programa', XMLDB_KEY_FOREIGN, array('id_programa'), 'talentospilos_programa', array('id'));
    //     // Launch add key fk_programa.
    //     $dbman->add_key($table, $key);
    //     // Define key unique_key (unique) to be added to talentospilos_history_academ.
    //     $table = new xmldb_table('talentospilos_history_academ');
    //     $key = new xmldb_key('unique_key', XMLDB_KEY_UNIQUE, array('id_estudiante', 'id_semestre', 'id_programa'));
    //     // Launch add key unique_key.
    //     $dbman->add_key($table, $key);
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se crea la tabla {talentospilos_history_cancel}. Con los campos
    //     //          id --> Autoincremental
    //     //          id_history --> Identificador del histórico académico
    //     //          fecha_cancelacion --> Fecha en la que se realiza la cancelación del semestre
    //     // Versión en la que se incluye: 2018011911069
    //     // ************************************************************************************************************
    //     // Define table talentospilos_history_cancel to be created.
    //     $table = new xmldb_table('talentospilos_history_cancel');
    //     // Adding fields to table talentospilos_history_cancel.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('id_history', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('fecha_cancelacion', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     // Adding keys to table talentospilos_history_cancel.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     $table->add_key('fk_history', XMLDB_KEY_FOREIGN, array('id_history'), 'talentospilos_history_academ', array('id'));
    //     // Conditionally launch create table for talentospilos_history_cancel.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     // Define field id_history to be added to talentospilos_history_cancel.
    //     $table = new xmldb_table('talentospilos_history_cancel');
    //     $field = new xmldb_field('id_history', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null, 'id');
    //     // Conditionally launch add field id_history.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // Define field fecha_cancelacion to be added to talentospilos_history_cancel.
    //     $table = new xmldb_table('talentospilos_history_cancel');
    //     $field = new xmldb_field('fecha_cancelacion', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null, 'id_history');
    //     // Conditionally launch add field fecha_cancelacion.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // Define key fk_history (foreign) to be added to talentospilos_history_cancel.
    //     $table = new xmldb_table('talentospilos_history_cancel');
    //     $key = new xmldb_key('fk_history', XMLDB_KEY_FOREIGN, array('id_history'), 'talentospilos_history_academ', array('id'));
    //     // Launch add key fk_history.
    //     $dbman->add_key($table, $key);
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se crea la tabla {talentospilos_history_bajos}. Con los campos
    //     //          id --> Autoincremental
    //     //          id_history --> Identificador del histórico académico
    //     //          numero_bajo --> Cantidad de bajos registrados
    //     // Versión en la que se incluye: 2018011911069
    //     // ************************************************************************************************************
    //     // Define table talentospilos_history_bajos to be created.
    //     $table = new xmldb_table('talentospilos_history_bajos');
    //     // Adding fields to table talentospilos_history_bajos.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     // Adding keys to table talentospilos_history_bajos.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     // Conditionally launch create table for talentospilos_history_bajos.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     // Define field id_history to be added to talentospilos_history_bajos.
    //     $table = new xmldb_table('talentospilos_history_bajos');
    //     $field = new xmldb_field('id_history', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null, 'id');
    //     // Conditionally launch add field id_history.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // Define field numero_bajo to be added to talentospilos_history_bajos.
    //     $table = new xmldb_table('talentospilos_history_bajos');
    //     $field = new xmldb_field('numero_bajo', XMLDB_TYPE_INTEGER, '3', null, null, null, null, 'id_history');
    //     // Conditionally launch add field numero_bajo.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // Define key fk_history (foreign) to be added to talentospilos_history_bajos.
    //     $table = new xmldb_table('talentospilos_history_bajos');
    //     $key = new xmldb_key('fk_history', XMLDB_KEY_FOREIGN, array('id_history'), 'talentospilos_history_academ', array('id'));
    //     // Launch add key fk_history.
    //     $dbman->add_key($table, $key);
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se crea la tabla {talentospilos_history_estim}. Con los campos
    //     //          id --> Autoincremental
    //     //          id_history --> Identificador del histórico académico
    //     //          puesto_ocupado --> Puesto ocupado por el estudiante en el semestre  
    //     // Versión en la que se incluye: 2018011911069
    //     // ************************************************************************************************************
    //     // Define table talentospilos_history_estim to be created.
    //     $table = new xmldb_table('talentospilos_history_estim');
    //     // Adding fields to table talentospilos_history_estim.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     // Adding keys to table talentospilos_history_estim.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     // Conditionally launch create table for talentospilos_history_estim.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     // Define field id_history to be added to talentospilos_history_estim.
    //     $table = new xmldb_table('talentospilos_history_estim');
    //     $field = new xmldb_field('id_history', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null, 'id');
    //     // Conditionally launch add field id_history.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // Define field puesto_ocupado to be added to talentospilos_history_estim.
    //     $table = new xmldb_table('talentospilos_history_estim');
    //     $field = new xmldb_field('puesto_ocupado', XMLDB_TYPE_INTEGER, '3', null, null, null, null, 'id_history');
    //     // Conditionally launch add field puesto_ocupado.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // Define key fk_history (foreign) to be added to talentospilos_history_estim.
    //     $table = new xmldb_table('talentospilos_history_estim');
    //     $key = new xmldb_key('fk_history', XMLDB_KEY_FOREIGN, array('id_history'), 'talentospilos_history_academ', array('id'));
    //     // Launch add key fk_history.
    //     $dbman->add_key($table, $key);
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se crea la tabla {talentospilos_res_estudiante}. Con los campos
    //     //          id --> Autoincremental
    //     //          monto_estudiante --> Identificador del histórico académico
    //     //          id_semestre --> Identificador del semestre académico
    //     //          id_estudiante --> Identificador asociado al estudiante ASES
    //     //          id_resolucion --> Identificador de la resolución asociada al estudiante
    //     // Versión en la que se incluye: 2018013010459
    //     // ************************************************************************************************************
    //     // Define table talentospilos_res_estudiante to be dropped.
    //     $table = new xmldb_table('talentospilos_res_estudiante');
    //     // Conditionally launch drop table for talentospilos_res_estudiante.
        
    //     // Define table talentospilos_res_estudiante to be created.
    //     $table = new xmldb_table('talentospilos_res_estudiante');
    //     // Adding fields to table talentospilos_res_estudiante.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     // Adding keys to table talentospilos_res_estudiante.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     // Conditionally launch create table for talentospilos_res_estudiante.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     // Define field monto_estudiante to be added to talentospilos_res_estudiante.
    //     $table = new xmldb_table('talentospilos_res_estudiante');
    //     $field = new xmldb_field('monto_estudiante', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'id');
    //     // Conditionally launch add field monto_estudiante.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
        
    //     // Define field id_estudiante to be added to talentospilos_res_estudiante.
    //     $table = new xmldb_table('talentospilos_res_estudiante');
    //     $field = new xmldb_field('id_estudiante', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id_semestre');
    //     // Conditionally launch add field id_estudiante.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // Define field id_resolucion to be added to talentospilos_res_estudiante.
    //     $table = new xmldb_table('talentospilos_res_estudiante');
    //     $field = new xmldb_field('id_resolucion', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id_estudiante');
    //     // Conditionally launch add field id_resolucion.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }    
    //     // Define key foreign_key_estudiante (foreign) to be added to talentospilos_res_estudiante.
    //     $table = new xmldb_table('talentospilos_res_estudiante');
    //     $key = new xmldb_key('foreign_key_estudiante', XMLDB_KEY_FOREIGN, array('id_estudiante'), 'talentospilos_usuario', array('id'));
    //     // Launch add key foreign_key_estudiante.
    //     $dbman->add_key($table, $key);
    //     // Define key foreign_key_res_icetex (foreign) to be added to talentospilos_res_estudiante.
    //     $table = new xmldb_table('talentospilos_res_estudiante');
    //     $key = new xmldb_key('foreign_key_res_icetex', XMLDB_KEY_FOREIGN, array('id_resolucion'), 'talentospilos_res_icetex', array('id'));
    //     // Launch add key foreign_key_res_icetex.
    //     $dbman->add_key($table, $key);
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se crea la tabla {talentospilos_res_icetex}. Con los campos
    //     //          id --> Autoincremental
    //     //          codigo_resolucion --> Identificador del histórico académico
    //     //          monto_total --> Identificador del semestre académico
    //     //          fecha_resolucion --> Identificador asociado al estudiante ASES
    //     // Versión en la que se incluye: 2018013010459
    //     // ************************************************************************************************************
    //     // Define table talentospilos_res_icetex to be dropped.
    //     $table = new xmldb_table('talentospilos_res_icetex');
    //     // Conditionally launch drop table for talentospilos_res_icetex.
        
    //     // Define table talentospilos_res_icetex to be created.
    //     $table = new xmldb_table('talentospilos_res_icetex');
    //     // Adding fields to table talentospilos_res_icetex.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     // Adding keys to table talentospilos_res_icetex.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     // Conditionally launch create table for talentospilos_res_icetex.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     // Define field codigo_resolucion to be added to talentospilos_res_icetex.
    //     $table = new xmldb_table('talentospilos_res_icetex');
    //     $field = new xmldb_field('codigo_resolucion', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'id');
    //     // Conditionally launch add field codigo_resolucion.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }

    //     // Define field id_semestre to be added to talentospilos_res_icetex.
    //     $table = new xmldb_table('talentospilos_res_icetex');
    //     $field = new xmldb_field('id_semestre', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'codigo_resolucion');
    //     // Conditionally launch add field id_semestre.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }

    //     // Define field monto_total to be added to talentospilos_res_icetex.
    //     $table = new xmldb_table('talentospilos_res_icetex');
    //     $field = new xmldb_field('monto_total', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null, 'codigo_resolucion');
    //     // Conditionally launch add field monto_total.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // Define field fecha_resolucion to be added to talentospilos_res_icetex.
    //     $table = new xmldb_table('talentospilos_res_icetex');
    //     $field = new xmldb_field('fecha_resolucion', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null, 'monto_total');
    //     // Conditionally launch add field fecha_resolucion.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // Define key unique_cod_res (unique) to be added to talentospilos_res_icetex.
    //     $table = new xmldb_table('talentospilos_res_icetex');
    //     $key = new xmldb_key('unique_cod_res', XMLDB_KEY_UNIQUE, array('codigo_resolucion'));
    //     // Launch add key unique_cod_res.
    //     $dbman->add_key($table, $key);
    //     // Define key foreign_key_semestre (foreign) to be added to talentospilos_res_icetex.
    //     $table = new xmldb_table('talentospilos_res_icetex');
    //     $key = new xmldb_key('foreign_key_semestre', XMLDB_KEY_FOREIGN, array('id_semestre'), 'talentospilos_semestre', array('id'));
    //     // Launch add key foreign_key_semestre.
    //     $dbman->add_key($table, $key);

    //     //*************************************************************************************************************
    //     // ************************************************************************************************************
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade la tabla talentospilos_df_formularios
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
        
    //     // Define table talentospilos_df_formularios to be created.
    //     $table = new xmldb_table('talentospilos_df_formularios');
    //     // Adding fields to table talentospilos_df_formularios.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('nombre', XMLDB_TYPE_CHAR, '140', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('descripcion', XMLDB_TYPE_TEXT, null, null, null, null, null);
    //     $table->add_field('method', XMLDB_TYPE_CHAR, '140', null, null, null, null);
    //     $table->add_field('action', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('enctype', XMLDB_TYPE_TEXT, null, null, null, null, null);
    //     $table->add_field('fecha_hora_registro', XMLDB_TYPE_DATETIME, null, null, null, null, "now()");
    //     // Adding keys to table talentospilos_df_formularios.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     // Conditionally launch create table for talentospilos_df_formularios.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     //end tp_formularios
        
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade la tabla talentospilos_tipo_campo
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
    //     // Define table talentospilos_df_tipo_campo to be created.
    //     $table = new xmldb_table('talentospilos_df_tipo_campo');
    //     // Adding fields to table talentospilos_df_tipo_campo.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('campo', XMLDB_TYPE_CHAR, '140', null, null, null, null);
    //     $table->add_field('fecha_hora_registro', XMLDB_TYPE_DATETIME, null, null, null, null, "now()");
    //     // Adding keys to table talentospilos_df_tipo_campo.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     // Conditionally launch create table for talentospilos_df_tipo_campo.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     //end tp_tipo_campo
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade la tabla talentospilos_preguntas
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
        
    //     // Define table talentospilos_df_preguntas to be created.
    //     $table = new xmldb_table('talentospilos_df_preguntas');
    //     // Adding fields to table talentospilos_df_preguntas.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('tipo_campo', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('opciones_campo', XMLDB_TYPE_TEXT, null, null, null, null, null);
    //     $table->add_field('atributos_campo', XMLDB_TYPE_TEXT, null, null, null, null, null);
    //     $table->add_field('enunciado', XMLDB_TYPE_CHAR, '200', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('fecha_hora_registro', XMLDB_TYPE_DATETIME, null, null, XMLDB_NOTNULL, null, "now()");
    //     // Adding keys to table talentospilos_df_preguntas.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     $table->add_key('fk_preguntas_id_tipo_pregunta', XMLDB_KEY_FOREIGN, array('tipo_campo'), 'talentospilos_df_tipo_campo', array('id'));
    //     // Conditionally launch create table for talentospilos_df_preguntas.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     //end tp_preguntas
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade la tabla talentospilos_df_form_preg
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
    //     // Define table talentospilos_df_form_preg to be created.
    //     $table = new xmldb_table('talentospilos_df_form_preg');
    //     // Adding fields to table talentospilos_df_form_preg.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('id_formulario', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('id_pregunta', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('posicion', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('fecha_hora_registro', XMLDB_TYPE_DATETIME, null, null, XMLDB_NOTNULL, null, "now()");
    //     // Adding keys to table talentospilos_df_form_preg.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     // Conditionally launch create table for talentospilos_df_form_preg.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     //end tp_formulario_preguntas
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade la tabla talentospilos_df_reglas
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
        
    //     // Define table talentospilos_df_reglas to be created.
    //     $table = new xmldb_table('talentospilos_df_reglas');
    //     // Adding fields to table talentospilos_df_reglas.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('regla', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
    //     // Adding keys to table talentospilos_df_reglas.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     // Conditionally launch create table for talentospilos_df_reglas.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     //end tp_reglas
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade la tabla talentospilos_df_respuestas
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
    //     // Define table talentospilos_df_respuestas to be created.
    //     $table = new xmldb_table('talentospilos_df_respuestas');
    //     // Adding fields to table talentospilos_df_respuestas.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('id_pregunta', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('respuesta', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('fecha_hora_registro', XMLDB_TYPE_DATETIME, null, null, null, null, "now()");
    //     // Adding keys to table talentospilos_df_respuestas.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     $table->add_key('fk_respuestas_id_pregunta', XMLDB_KEY_FOREIGN, array('id_pregunta'), 'talentospilos_df_preguntas', array('id'));
    //     // Conditionally launch create table for talentospilos_df_respuestas.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     //end tp_respuestas
        
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade la tabla talentospilos_df_form_resp
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
        
    //     // Define table talentospilos_df_form_resp to be created.
    //     $table = new xmldb_table('talentospilos_df_form_resp');
    //     // Adding fields to table talentospilos_df_form_resp.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('id_formulario', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('id_monitor', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('id_estudiante', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('fecha_hora_registro', XMLDB_TYPE_DATETIME, null, null, XMLDB_NOTNULL, null, "now()");
    //     // Adding keys to table talentospilos_df_form_resp.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     $table->add_key('fk_form_resp_id_formulario', XMLDB_KEY_FOREIGN, array('id_formulario'), 'talentospilos_df_formularios', array('id'));
    //     // Conditionally launch create table for talentospilos_df_form_resp.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     //end tp_formulario_respuestas
        
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade la tabla talentospilos_df_form_solu
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
    
    //     // Define table talentospilos_df_form_solu to be created.
    //     $table = new xmldb_table('talentospilos_df_form_solu');
    //     // Adding fields to table talentospilos_df_form_solu.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('id_formulario_respuestas', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('id_respuesta', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('fecha_hora_registro', XMLDB_TYPE_DATETIME, null, null, XMLDB_NOTNULL, null, "now()");
    //     // Adding keys to table talentospilos_df_form_solu.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     $table->add_key('fk_form_solu_id_form_resp', XMLDB_KEY_FOREIGN, array('id_formulario_respuestas'), 'talentospilos_df_form_resp', array('id'));
    //     $table->add_key('fk_form_solu_id_resp', XMLDB_KEY_FOREIGN, array('id_respuesta'), 'talentospilos_df_respuestas', array('id'));
    //     // Conditionally launch create table for talentospilos_df_form_solu.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     //end tp_soluciones
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade la tabla talentospilos_df_reg_form_pr
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
    //     // Define table talentospilos_df_reg_form_pr to be created.
    //     $table = new xmldb_table('talentospilos_df_reg_form_pr');
    //     // Adding fields to table talentospilos_df_reg_form_pr.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('id_formulario', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('id_regla', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('id_form_pregunta_a', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('id_form_pregunta_b', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     // Adding keys to table talentospilos_df_reg_form_pr.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     $table->add_key('fk_reg_form_pr_formularios', XMLDB_KEY_FOREIGN, array('id_formulario'), 'talentospilos_df_formularios', array('id'));
    //     $table->add_key('fk_reg_form_pr_id_reglas', XMLDB_KEY_FOREIGN, array('id_regla'), 'talentospilos_df_reglas', array('id'));
    //     $table->add_key('fk_reg_form_pr_id_form_pregunta_a', XMLDB_KEY_FOREIGN, array('id_form_pregunta_a'), 'talentospilos_df_form_preg', array('id'));
    //     $table->add_key('fk_reg_form_pr_id_form_pregunta_b', XMLDB_KEY_FOREIGN, array('id_form_pregunta_b'), 'talentospilos_df_form_preg', array('id'));
    //     // Conditionally launch create table for talentospilos_df_reg_form_pr.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     //end tp_reglas_form_preguntas
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade la tabla talentospilos_df_per_form_pr
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
    //     // Define table talentospilos_df_per_form_pr to be created.
    //     $table = new xmldb_table('talentospilos_df_per_form_pr');
    //     // Adding fields to table talentospilos_df_per_form_pr.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('id_formulario_pregunta', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('permisos', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    //     // Adding keys to table talentospilos_df_per_form_pr.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     $table->add_key('fk_per_form_pr_id_form_preg', XMLDB_KEY_FOREIGN, array('id_formulario_pregunta'), 'talentospilos_df_form_preg', array('id'));
    //     // Conditionally launch create table for talentospilos_df_per_form_pr.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     //end tp_permisos_formulario_preguntas
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade la tabla talentospilos_df_disp_fordil
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
    //     // Define table talentospilos_df_disp_fordil to be created.
    //     $table = new xmldb_table('talentospilos_df_disp_fordil');
    //     // Adding fields to table talentospilos_df_disp_fordil.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('id_formulario', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('disparadores', XMLDB_TYPE_TEXT, null, null, null, null, null);
    //     // Adding keys to table talentospilos_df_disp_fordil.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     $table->add_key('fk_disparadores_id_formulario', XMLDB_KEY_FOREIGN_UNIQUE, array('id_formulario'), 'talentospilos_df_formularios', array('id'));
    //     // Conditionally launch create table for talentospilos_df_disp_fordil.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     //end tp_disparadores_formulario_diligenciado
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se inserta campo id_programa en la tabla talentospilos_res_estudiante
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
    //     // Define field id_programa to be added to talentospilos_res_estudiante.
    //     $table = new xmldb_table('talentospilos_res_estudiante');
    //     $field = new xmldb_field('id_programa', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id_resolucion');
    //     // Conditionally launch add field id_programa.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se crea la llave foránea desde campo id_programa en la tabla talentospilos_res_estudiante hacia
    //     // la tabla talentospilos_programa
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
    //     // Define key fk_programa (foreign) to be added to talentospilos_res_estudiante.
    //     $table = new xmldb_table('talentospilos_res_estudiante');
    //     $key = new xmldb_key('fk_programa', XMLDB_KEY_FOREIGN, array('id_programa'), 'talentospilos_programa', array('id'));
    //     // Launch add key fk_programa.
    //     $dbman->add_key($table, $key);
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se insertan registros para los tipos de campo 
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
        
    //     //Registro de los tipos de campos  
    //     $verificador = $DB->get_record_sql("SELECT * FROM {talentospilos_df_tipo_campo} WHERE campo = 'TEXTFIELD'");
    //     if(!$verificador){
    //         $campo_textfield = new stdClass();
    //         $campo_textfield->campo                = 'TEXTFIELD';
    //         $campo_textfield->fecha_hora_registro  = 'now()';
    //         $campo_textarea = new stdClass();
    //         $campo_textarea->campo                 = 'TEXTAREA';
    //         $campo_textarea->fecha_hora_registro   = 'now()';
    //         $campo_date = new stdClass();
    //         $campo_date->campo                     = 'DATE';
    //         $campo_date->fecha_hora_registro       = 'now()';
    //         $campo_time = new stdClass();
    //         $campo_time->campo                     = 'TIME';
    //         $campo_time->fecha_hora_registro       = 'now()';
    //         $campo_radio = new stdClass();
    //         $campo_radio->campo                    = 'RADIOBUTTON';
    //         $campo_radio->fecha_hora_registro      = 'now()';
    //         $campo_check = new stdClass();
    //         $campo_check->campo                    = 'CHECKBOX';
    //         $campo_check->fecha_hora_registro      = 'now()';
    //         $records = array();
    //         array_push($records, $campo_textfield);
    //         array_push($records, $campo_textarea);
    //         array_push($records, $campo_date);
    //         array_push($records, $campo_time);
    //         array_push($records, $campo_radio);
    //         array_push($records, $campo_check);
    //         $DB->insert_records('talentospilos_df_tipo_campo', $records);
    //     }

    //     $sql_intel = "DELETE FROM {talentospilos_df_tipo_campo} WHERE id <> 1 and id <> 2 and id <> 3 and id <> 4 and id <> 5 and id <> 6";
    //     $DB->execute($sql_intel);
        
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se insertan registros para las reglas de los formularios
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
    //     $verificador_reglas = $DB->get_record_sql("SELECT * FROM {talentospilos_df_reglas} WHERE regla = 'EQUAL'");
    //     if(!$verificador_reglas){
    //         $regla_mayor_que = new stdClass();
    //         $regla_mayor_que->regla    = '>';
    //         $regla_menor_que = new stdClass();
    //         $regla_menor_que->regla    = '<';
    //         $regla_igual = new stdClass();
    //         $regla_igual->regla        = 'EQUAL';
    //         $regla_diferente = new stdClass();
    //         $regla_diferente->regla    = 'DIFFERENT';
    //         $regla_depende = new stdClass();
    //         $regla_depende->regla      = 'DEPENDS';
    //         $regla_enlazado = new stdClass();
    //         $regla_enlazado->regla     = 'BOUND';
    //         $records = array();
    //         array_push($records, $regla_mayor_que);
    //         array_push($records, $regla_menor_que);
    //         array_push($records, $regla_igual);
    //         array_push($records, $regla_diferente);
    //         array_push($records, $regla_depende);
    //         array_push($records, $regla_enlazado);
    //         $DB->insert_records('talentospilos_df_reglas', $records);
    //     }
    //     $sql_intel = "DELETE FROM {talentospilos_df_reglas} WHERE id <> 1 and id <> 2 and id <> 3 and id <> 4 and id <> 5 and id <> 6";
    //     $DB->execute($sql_intel);
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se inserta campo id_programa en la tabla talentospilos_res_estudiante
    //     // Versión en la que se incluye: 2018012413229
    //     // ************************************************************************************************************
    //     // Define field id_programa to be added to talentospilos_res_estudiante.
    //     $table = new xmldb_table('talentospilos_res_estudiante');
    //     $field = new xmldb_field('id_programa', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'id_resolucion');
    //     // Conditionally launch add field id_programa.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se crea la llave foránea desde campo id_programa en la tabla talentospilos_res_estudiante hacia
    //     // la tabla talentospilos_programa
    //     // Versión en la que se incluye: 2018012413229
    //     // ************************************************************************************************************
    //     // Define key fk_programa (foreign) to be added to talentospilos_res_estudiante.
    //     $table = new xmldb_table('talentospilos_res_estudiante');
    //     $key = new xmldb_key('fk_programa', XMLDB_KEY_FOREIGN, array('id_programa'), 'talentospilos_programa', array('id'));
    //     // Launch add key fk_programa.
    //     $dbman->add_key($table, $key);

    //         // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade el campo id_programa a la tabla {talentospilos_user_rol}
    //     // Versión en la que se incluye: 2018012911099
    //     // ************************************************************************************************************

    //     // Define field id_programa to be added to talentospilos_user_rol.
    //     $table = new xmldb_table('talentospilos_user_rol');
    //     $field = new xmldb_field('id_programa', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'id_instancia');

    //     // Conditionally launch add field id_programa.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }

    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade la llave foránea fk_program a la tabla {talentospilos_user_rol}
    //     // Versión en la que se incluye: 2018012911099
    //     // ************************************************************************************************************

    //     // Define key fk_program (foreign) to be added to talentospilos_user_rol.
    //     $table = new xmldb_table('talentospilos_user_rol');
    //     $key = new xmldb_key('fk_program', XMLDB_KEY_FOREIGN, array('id_programa'), 'talentospilos_programa', array('id'));

    //     // Launch add key fk_program.
    //     $dbman->add_key($table, $key);


    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se crea la tabla {talentospilos_materias_criti}
    //     // Versión en la que se incluye: 2018012918099
    //     // ************************************************************************************************************

    //     // Define table talentospilos_materias_criti to be created.
    //         $table = new xmldb_table('talentospilos_materias_criti');

    //         // Adding fields to table talentospilos_materias_criti.
    //         $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //         $table->add_field('codigo_materia', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null);

    //         // Adding keys to table talentospilos_materias_criti.
    //         $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //         $table->add_key('unique_key', XMLDB_KEY_UNIQUE, array('codigo_materia'));

    //         // Conditionally launch create table for talentospilos_materias_criti.
    //         if (!$dbman->table_exists($table)) {
    //             $dbman->create_table($table);
    //         }

    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se crea la tabla {talentospilos_df_alias}
    //     // Versión en la que se incluye: 2018013114389
    //     // ************************************************************************************************************

    //         // Define table talentospilos_df_alias to be created.
    //         $table = new xmldb_table('talentospilos_df_alias');

    //         // Adding fields to table talentospilos_df_alias.
    //         $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //         $table->add_field('id_pregunta', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //         $table->add_field('alias', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);

    //         // Adding keys to table talentospilos_df_alias.
    //         $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    //         // Adding indexes to table talentospilos_df_alias.
    //         $table->add_index('unique_id_pregunta', XMLDB_INDEX_UNIQUE, array('id_pregunta'));
    //         $table->add_index('unique_alias', XMLDB_INDEX_UNIQUE, array('alias'));

    //         // Conditionally launch create table for talentospilos_df_alias.
    //         if (!$dbman->table_exists($table)) {
    //             $dbman->create_table($table);
    //         }

    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade el campo nota_credito en la tabla {talentospilos_res_icetex}
    //     // Versión en la que se incluye: 2018020209479 
    //     // ************************************************************************************************************

    //     // Define field nota_credito to be added to talentospilos_res_icetex.
    //     $table = new xmldb_table('talentospilos_res_icetex');
    //     $field = new xmldb_field('nota_credito', XMLDB_TYPE_TEXT, null, null, null, null, null, 'fecha_resolucion');

    //     // Conditionally launch add field nota_credito.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }

    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade el campo nota_credito en la tabla {talentospilos_res_icetex}
    //     // Versión en la que se incluye: 2018020214529
    //     // ************************************************************************************************************

    //     // Define field alias to be added to talentospilos_df_formularios.
    //     $table = new xmldb_table('talentospilos_df_formularios');
    //     $field = new xmldb_field('alias', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'fecha_hora_registro');

    //     // Conditionally launch add field alias.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }

    //     // Define field estado to be added to talentospilos_df_formularios.
    //     $table = new xmldb_table('talentospilos_df_formularios');
    //     $field = new xmldb_field('estado', XMLDB_TYPE_INTEGER, '5', null, XMLDB_NOTNULL, null, '1', 'alias');

    //     // Conditionally launch add field estado.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }

    //     // Define field estado to be added to talentospilos_df_form_resp.
    //     $table = new xmldb_table('talentospilos_df_form_resp');
    //     $field = new xmldb_field('estado', XMLDB_TYPE_INTEGER, '5', null, XMLDB_NOTNULL, null, '1', 'fecha_hora_registro');

    //     // Conditionally launch add field estado.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }

    //     // Define field estado to be added to talentospilos_df_form_preg.
    //     $table = new xmldb_table('talentospilos_df_form_preg');
    //     $field = new xmldb_field('estado', XMLDB_TYPE_INTEGER, '5', null, XMLDB_NOTNULL, null, '1', 'fecha_hora_registro');

    //     // Conditionally launch add field talentospilos_df_form_resp.
    //     if (!$dbman->field_exists($table, $field)){
    //         $dbman->add_field($table, $field);
    //     }

    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se elimina la tabla talentospilos_instancia
    //     // Versión en la que se incluye: 2018021417179
    //     // ************************************************************************************************************
    //     // Define table talentospilos_instancia to be dropped.
    //     // $table = new xmldb_table('talentospilos_instancia');

    //     // // Conditionally launch drop table for talentospilos_instancia.
    //     // if ($dbman->table_exists($table)) {
    //     //     $dbman->drop_table($table);
    //     // }

    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade la nueva tabla para la configuración de la instancia
    //     // Versión en la que se incluye: 2018021417179
    //     // ************************************************************************************************************
    //     // Define table talentospilos_instancia to be created.
    //     $table = new xmldb_table('talentospilos_instancia');

    //     // Adding fields to table talentospilos_instancia.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('id_instancia', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('descripcion', XMLDB_TYPE_CHAR, '200', null, null, null, null);

    //     // Adding keys to table talentospilos_instancia.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    //     // Conditionally launch create table for talentospilos_instancia.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }

    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se elimina la tabla talentospilos_monitor_estud
    //     // Versión en la que se incluye: 2018021417179
    //     // ************************************************************************************************************
    //     // Define table talentospilos_monitor_estud to be dropped.
    //     // $table = new xmldb_table('talentospilos_monitor_estud');

    //     // // Conditionally launch drop table for talentospilos_monitor_estud.
    //     // if ($dbman->table_exists($table)) {
    //     //     $dbman->drop_table($table);
    //     // }

    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se crea la tabla talentospilos_monitor_estud con llave única que incluye id_monitor, id_estudiante, id_instancia, id_semestre
    //     // Versión en la que se incluye: 2018021417179
    //     // ************************************************************************************************************
    //     // Define table talentospilos_monitor_estud to be created.
    //     $table = new xmldb_table('talentospilos_monitor_estud');

    //     // Adding fields to table talentospilos_monitor_estud.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('id_monitor', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('id_estudiante', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('id_instancia', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('id_semestre', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

    //     // Adding keys to table talentospilos_monitor_estud.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     $table->add_key('mon_est_pk1', XMLDB_KEY_FOREIGN, array('id_monitor'), 'user', array('id'));
    //     $table->add_key('mon_est_pk2', XMLDB_KEY_FOREIGN, array('id_estudiante'), 'talentospilos_usuario', array('id'));
    //     $table->add_key('mon_est_un', XMLDB_KEY_UNIQUE, array('id_monitor', 'id_estudiante', 'id_instancia', 'id_semestre'));

    //     // Conditionally launch create table for talentospilos_monitor_estud.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }

        // ************************************************************************************************************
        // Actualización:
        // Se configuran los estados de la tabla talentospilos_estado_ases
        // Versión en la que se incluye: 2018021909439
        // ************************************************************************************************************

        // $register = $DB->get_record('talentospilos_estados_ases', array('nombre'=>'ACTIVO/SEGUIMIENTO'));
        // if($register){
        //     $data_object = new stdClass();
        //     $data_object->id = $register->id;
        //     $data_object->nombre = 'seguimiento';
        //     $data_object->descripcion = 'SEGUIMIENTO';

        //     $DB->update_record('talentospilos_estados_ases', $data_object);
        // }

        // $register = $DB->get_record('talentospilos_estados_ases', array('nombre'=>'ACTIVO/SINSEGUIMIENTO'));
        // if($register){
        //     $data_object = new stdClass();
        //     $data_object = new stdClass();
        //     $data_object->id = $register->id;
        //     $data_object->nombre = 'sinseguimiento';
        //     $data_object->descripcion = 'SIN SEGUIMIENTO';

        //     $DB->update_record('talentospilos_estados_ases', $data_object);
        // }

        // $register = $DB->get_record('talentospilos_estados_ases', array('nombre'=>'RETIRADO'));

        // if($register){
        //     $object_to_delete = array();
        //     $object_to_delete['id'] = $register->id;
        //     $DB->delete_records('talentospilos_estados_ases', $object_to_delete);
        // }

        // $register = $DB->get_record('talentospilos_estados_ases', array('nombre'=>'APLAZADO'));
        
        // if($register){
        //     $object_to_delete = array();
        //     $object_to_delete['id'] = $register->id;
        //     $DB->delete_records('talentospilos_estados_ases', $object_to_delete);
        // }

        // $register = $DB->get_record('talentospilos_estados_ases', array('nombre'=>'EGRESADO'));

        // if($register){
        //     $object_to_delete = array();
        //     $object_to_delete['id'] = $register->id;
        //     $DB->delete_records('talentospilos_estados_ases', $object_to_delete);
        // }





        // ************************************************************************************************************
        // Actualización:
        // Se crea tabla para almacenar los posibles estados del estudiante en un programa académico
        // Versión en la que se incluye: PENDIENTE
        // ************************************************************************************************************
        
        // // Define table talentospilos_estad_programa to be created.
        // $table = new xmldb_table('talentospilos_estad_programa');

        // // Adding fields to table talentospilos_estad_programa.
        // $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        // $table->add_field('nombre', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('descripcion', XMLDB_TYPE_CHAR, '500', null, null, null, null);

        // // Adding keys to table talentospilos_estad_programa.
        // $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // // Conditionally launch create table for talentospilos_estad_programa.
        // if (!$dbman->table_exists($table)) {
        //     $dbman->create_table($table);
        // }

        // $array_status_program = array('ACTIVO', 'APLAZADO', 'EGRESADO', 'INACTIVO', 'NO REGISTRA', 'RETIRADO');
        // $array_description_status_program = array('El estudiante está activo en el programa académico',
        //                                     'El estudiante es egresado del programa académico',
        //                                     'El estudiante se encuentra aplazado en el programa académico',
        //                                     'El estudiante se encuentra inactivo en el programa académico',
        //                                     'No registra estado en el programa académico',
        //                                     'El estudiante se encuentra retirado del programa académico'
        //                                     );
        
        // $record = new stdClass();

        // for($i = 0; $i < count($array_status_program); $i++){
        //     $record->nombre = $array_status_program[$i];
        //     $record->descripcion = $array_description_status_program[$i];
        //     $result = $DB->insert_record('talentospilos_estad_programa', $record, true);
        // }

        // // Define field cantidad_estudiantes to be added to talentospilos_res_icetex.
        // $table = new xmldb_table('talentospilos_res_icetex');
        // $field = new xmldb_field('cantidad_estudiantes', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'fecha_resolucion');

        // // Conditionally launch add field cantidad_estudiantes.
        // if (!$dbman->field_exists($table, $field)) {
        //     $dbman->add_field($table, $field);
        // }

        // // Define table talentospilos_res_estudiante to be dropped.
        // $table = new xmldb_table('talentospilos_res_estudiante');

        // // Conditionally launch drop table for talentospilos_res_estudiante.
        // if ($dbman->table_exists($table)) {
        //     $dbman->drop_table($table);
        // }

        // // Define table talentospilos_res_estudiante to be created.
        // $table = new xmldb_table('talentospilos_res_estudiante');

        // // Adding fields to table talentospilos_res_estudiante.
        // $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        // $table->add_field('id_estudiante', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('id_resolucion', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('id_semestre', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('monto_estudiante', XMLDB_TYPE_NUMBER, '20, 2', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('id_estado_icetex', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // // Adding keys to table talentospilos_res_estudiante.
        // $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        // $table->add_key('uk_res_est', XMLDB_KEY_UNIQUE, array('id_estudiante', 'id_resolucion'));

        // // Conditionally launch create table for talentospilos_res_estudiante.
        // if (!$dbman->table_exists($table)) {
        //     $dbman->create_table($table);
        // }
        
        // /*Eliminación de los campos id_semestre y id_estado_icetex en la tabla
        // talentospilos_res_estudiante */

        // // Define field id_semestre to be dropped from talentospilos_res_estudiante.
        // $table = new xmldb_table('talentospilos_res_estudiante');
        // $field = new xmldb_field('id_semestre');
 
        //  // Conditionally launch drop field id_semestre.
        //  if ($dbman->field_exists($table, $field)) {
        //      $dbman->drop_field($table, $field);
        //  }

        //  // Define field id_estado_icetex to be dropped from talentospilos_res_estudiante.
        // $table = new xmldb_table('talentospilos_res_estudiante');
        // $field = new xmldb_field('id_estado_icetex');

        // // Conditionally launch drop field id_estado_icetex.
        // if ($dbman->field_exists($table, $field)) {
        //     $dbman->drop_field($table, $field);
        // }

        // // Define field id_programa to be added to talentospilos_res_estudiante.
        // $table = new xmldb_table('talentospilos_res_estudiante');
        // $field = new xmldb_field('id_programa', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null);

        // // Conditionally launch add field id_programa.
        // if (!$dbman->field_exists($table, $field)) {
        //     $dbman->add_field($table, $field);
        // }

        // // ************************************************************************************************************
        // // Actualización:
        // // Se crea tabla para almacenar temporalmente los cambios de un formulario diligenciado en el tiempo
        // // Versión en la que se incluye: GIT 4.2, Moodle: 2018053015359
        // // ************************************************************************************************************
        // // Define table talentospilos_df_dwarehouse to be created.
        // $table = new xmldb_table('talentospilos_df_dwarehouse');

        // // Adding fields to table talentospilos_df_dwarehouse.
        // $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        // $table->add_field('id_usuario_moodle', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('accion', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        // $table->add_field('id_registro_respuesta_form', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('datos_previos', XMLDB_TYPE_TEXT, null, null, null, null, null);
        // $table->add_field('datos_enviados', XMLDB_TYPE_TEXT, null, null, null, null, null);
        // $table->add_field('datos_almacenados', XMLDB_TYPE_TEXT, null, null, null, null, null);
        // $table->add_field('observaciones', XMLDB_TYPE_TEXT, null, null, null, null, null);
        // $table->add_field('cod_retorno', XMLDB_TYPE_INTEGER, '5', null, null, null, null);
        // $table->add_field('msg_retorno', XMLDB_TYPE_TEXT, null, null, null, null, null);
        // $table->add_field('dts_retorno', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // // Adding keys to table talentospilos_df_dwarehouse.
        // $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // // Adding indexes to table talentospilos_df_dwarehouse.
        // $table->add_index('indice_df_dw_id_usuario_moodle', XMLDB_INDEX_NOTUNIQUE, array('id_usuario_moodle'));
        // $table->add_index('df_dw_id_registro_respuesta_form', XMLDB_INDEX_NOTUNIQUE, array('id_registro_respuesta_form'));

        // // Conditionally launch create table for talentospilos_df_dwarehouse.
        // if (!$dbman->table_exists($table)) {
        //     $dbman->create_table($table);
        // }

        // ************************************************************************************************************
        // Actualización:
        // Se crea tabla para almacenar temporalmente los cambios de un formulario diligenciado en el tiempo
        // Versión en la que se incluye: GIT 4.5, Moodle: 2018060109129
        // ************************************************************************************************************
        // Define field fecha_hora_registro to be added to talentospilos_df_dwarehouse.
        // $table = new xmldb_table('talentospilos_df_dwarehouse');
        // $field = new xmldb_field('fecha_hora_registro', XMLDB_TYPE_DATETIME, null, null, XMLDB_NOTNULL, null, "now()", 'dts_retorno');

        // // Conditionally launch add field fecha_hora_registro.
        // if (!$dbman->field_exists($table, $field)) {
        //     $dbman->add_field($table, $field);
        // }

        // ************************************************************************************************************
        // Actualización:
        // Se cambia el tipo de campo de program_status en la tabla talentospilos_user_extended
        // Versión en la que se incluye: GIT XXX, Moodle: 2018061810589
        // ************************************************************************************************************

        // Define field program_status to be dropped from talentospilos_user_extended.
        // $table = new xmldb_table('talentospilos_user_extended');
        // $field = new xmldb_field('program_status');

        // Conditionally launch drop field program_status.
        // if ($dbman->field_exists($table, $field)) {
        //     $dbman->drop_field($table, $field);
        // }

        // Define field program_status to be added to talentospilos_user_extended.
        // $table = new xmldb_table('talentospilos_user_extended');
        // $field = new xmldb_field('program_status', XMLDB_TYPE_INTEGER, '10', null, null, null, '1', 'estado_seguimiento');

        // Conditionally launch add field program_status.
        // if (!$dbman->field_exists($table, $field)) {
        //     $dbman->add_field($table, $field);
        // }

        // Define key fk_program_status (foreign) to be added to talentospilos_user_extended.
        // $table = new xmldb_table('talentospilos_user_extended');
        // $key = new xmldb_key('fk_program_status', XMLDB_KEY_FOREIGN, array('program_status'), 'talentospilos_estad_programa', array('id'));

        // Launch add key fk_program_status.
        // $dbman->add_key($table, $key);


        // ************************************************************************************************************
        // Actualización:
        // Se añade campo idnumber a la tabla talentospilos_instancia
        // Versión en la que se incluye: GIT XXX, Moodle: 2018062515379
        // ************************************************************************************************************

        // Define field id_number to be added to talentospilos_instancia.
        // $table = new xmldb_table('talentospilos_instancia');
        // $field = new xmldb_field('id_number', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'changeme', 'descripcion');

        // Conditionally launch add field id_number.
        // if (!$dbman->field_exists($table, $field)) {
        //     $dbman->add_field($table, $field);
        // }

        // $sql_query = "SELECT id FROM {talentospilos_instancia}";
        // $result_instances = $DB->get_records_sql($sql_query);
        // $counter = 0;

        // foreach($result_instances as $instance){
        //     $counter++;
        //     $record = new stdClass();
        //     $record->id = $instance->id;
        //     $record->id_number = 'changeme'+$counter;
        //     $update_query = $DB->update_record('talentospilos_instancia', $record);
        // }

        // Define key instance_uk_1 (unique) to be added to talentospilos_instancia.
        // $table = new xmldb_table('talentospilos_instancia');
        // $key = new xmldb_key('instance_uk_1', XMLDB_KEY_UNIQUE, array('id_number'));

        // Launch add key instance_uk_1.
        // $dbman->add_key($table, $key);


        // ************************************************************************************************************
        // Actualización:
        // Se añade campo navegador y usuario a la tabla talentospilos_df_dwarehouse
        // Versión en la que se incluye: GIT XXX, Moodle: 2018062515379
        // ************************************************************************************************************

        // Define field navegador to be added to talentospilos_df_dwarehouse.
        // $table = new xmldb_table('talentospilos_df_dwarehouse');
        // $field = new xmldb_field('navegador', XMLDB_TYPE_TEXT, null, null, null, null, null, 'dts_retorno');

        // // Conditionally launch add field navegador.
        // if (!$dbman->field_exists($table, $field)) {
        //     $dbman->add_field($table, $field);
        // }

        // // Define field url_request to be added to talentospilos_df_dwarehouse.
        // $table = new xmldb_table('talentospilos_df_dwarehouse');
        // $field = new xmldb_field('url_request', XMLDB_TYPE_TEXT, null, null, null, null, null, 'navegador');

        // // Conditionally launch add field url_request.
        // if (!$dbman->field_exists($table, $field)) {
        //     $dbman->add_field($table, $field);
        // }
        // ************************************************************************************************************
        


        // ************************************************************************************************************
        // Actualización:
        // Se crea tabla que almacena los tipos de documentos posibles 
        // Versión en la que se incluye: GIT XXX, Moodle: 2018080616479
        // ************************************************************************************************************
        // Define table talentospilos_tipo_documento to be created.
        // $table = new xmldb_table('talentospilos_tipo_documento');

        // // Adding fields to table talentospilos_tipo_documento.
        // $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        // $table->add_field('nombre', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('descripcion', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);

        // // Adding keys to table talentospilos_tipo_documento.
        // $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // // Conditionally launch create table for talentospilos_tipo_documento.
        // if (!$dbman->table_exists($table)) {
        //     $dbman->create_table($table);
        // }

        // // ************************************************************************************************************
        // // Actualización:
        // // Se cambia el tipo de dato para el campo tipo_doc_ini en la tabla talentospilos_usuario
        // // Versión en la que se incluye: GIT XXX, Moodle: 2018080815349
        // // ************************************************************************************************************        
        // // Changing type of field tipo_doc_ini on table talentospilos_usuario to int.
        // $table = new xmldb_table('talentospilos_usuario');
        // $field = new xmldb_field('tipo_doc_ini', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // // Launch change of type for field tipo_doc_ini.
        // $dbman->change_field_type($table, $field);

        // // ************************************************************************************************************
        // // Actualización:
        // // Se cambia el tipo de dato para el campo tipo_doc en la tabla talentospilos_usuario
        // // Versión en la que se incluye: GIT XXX, Moodle: 2018080815349
        // // ************************************************************************************************************        
        // // Changing type of field tipo_doc_ini on table talentospilos_usuario to int.
        // $table = new xmldb_table('talentospilos_usuario');
        // $field = new xmldb_field('tipo_doc', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // // Launch change of type for field tipo_doc_ini.
        // $dbman->change_field_type($table, $field);

        // // ************************************************************************************************************
        // // Actualización:
        // // Se crean las llaves foráneas de los campos tipo_doc y tipo_doc_ini en la tabla talentospilos_usuario,
        // // Las cuales apuntan a la tabla talentospilos_tipo_documento
        // // Versión en la que se incluye: GIT XXX, Moodle: 2018080815349
        // // ************************************************************************************************************    
        // // Define key doc_ini_type_fk (foreign) to be added to talentospilos_usuario.
        // $table = new xmldb_table('talentospilos_usuario');
        // $key = new xmldb_key('doc_ini_type_fk', XMLDB_KEY_FOREIGN, array('tipo_doc_ini'), 'talentospilos_tipo_documento', array('id'));

        // // Launch add key doc_ini_type_fk.
        // $dbman->add_key($table, $key);

        // // Define key tipo_doc_fk (foreign) to be added to talentospilos_usuario.
        // $table = new xmldb_table('talentospilos_usuario');
        // $key = new xmldb_key('tipo_doc_fk', XMLDB_KEY_FOREIGN, array('tipo_doc'), 'talentospilos_tipo_documento', array('id'));
 
        // // Launch add key tipo_doc_fk.
        // $dbman->add_key($table, $key);

        //$table = new xmldb_table('talentospilos_alertas_academ');
        // Conditionally launch drop table for talentospilos_alertas_academ.
    // if ($dbman->table_exists($table)) {
    //     $dbman->drop_table($table);
    // }
    // Adding fields to table talentospilos_alertas_academ.
    // $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    // $table->add_field('id_estudiante', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
    // $table->add_field('id_item', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
    // $table->add_field('nota', XMLDB_TYPE_FLOAT, '20', null, null, null, null);
    // $table->add_field('id_user_registra', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
    // $table->add_field('fecha', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
     // Adding keys to table talentospilos_alertas_academ.
    // $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    // $table->add_key('unique_key', XMLDB_KEY_UNIQUE, array('id_item', 'id_estudiante'));
     // Conditionally launch create table for talentospilos_alertas_academ.
    // if (!$dbman->table_exists($table)) {
    //     $dbman->create_table($table);
    // }



     // ************************************************************************************************************
    // Actualización:
    // Se crea tabla de el super super usuario de el bloque 
    // Versión en la que se incluye: GIT XXX, Moodle: 2018083109510


        // Define table talentospilos_superadmin to be created.
        //$table = new xmldb_table('talentospilos_superadmin');

        // Adding fields to table talentospilos_superadmin.
        //$table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        //$table->add_field('id_usuario', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        //$table->add_field('fecha_registro', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        //$table->add_field('fecha_ultima_modificacion', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        //$table->add_field('estado', XMLDB_TYPE_INTEGER, '2', null, null, null, '1');

        // Adding keys to table talentospilos_superadmin.
        //$table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        //$table->add_key('fk_id_usuario', XMLDB_KEY_FOREIGN, array('id_usuario'), 'user', array('id'));

        // Conditionally launch create table for talentospilos_superadmin.
        //if (!$dbman->table_exists($table)) {
        //    $dbman->create_table($table);
        //}


     // ************************************************************************************************************
    // Actualización:
    // Se crea tabla de logs de alertas academicas
    // Versión en la que se incluye: GIT XXX, Moodle: 2018080609050


    // ************************************************************************************************************
    // Actualización:
    // Se crea tabla de histórico estados ICETEX
    // Versión en la que se incluye: GIT XXX, Moodle: 

        // Define table talentospilos_hist_est_ice to be created.
        // $table = new xmldb_table('talentospilos_hist_est_ice');

        // Adding fields to table talentospilos_hist_est_ice.
        // $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        // $table->add_field('id_estudiante', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('id_estado_icetex', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('id_semestre', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table talentospilos_hist_est_ice.
        //$table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for talentospilos_hist_est_ice.
        //if (!$dbman->table_exists($table)) {
        //    $dbman->create_table($table);
        //}

    //Actualización: 
    //Se borra tabla talentospilos_hist_est_ice

        // Define table talentospilos_hist_est_ice to be created.
        // $table = new xmldb_table('talentospilos_hist_est_ice');

        // if ($dbman->table_exists($table)) {
        //         $dbman->drop_table($table);
        //     }       

        // // Adding fields to table talentospilos_hist_est_ice.
        // $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        // $table->add_field('id_estudiante', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('id_semestre', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('id_estado_icetex', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);

        // // Adding keys to table talentospilos_hist_est_ice.
        // $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        // $table->add_key('uk_hist_est_ice', XMLDB_KEY_UNIQUE, array('id_estudiante', 'id_semestre'));

        // // Conditionally launch create table for talentospilos_hist_est_ice.
        // if (!$dbman->table_exists($table)) {
        //     $dbman->create_table($table);
        // }

        // ************************************************************************************************************
        // Actualización:
        // Se añade campo pais, vive_con, hijos  a la tabla talentospilos_usuario
        // Versión en la que se incluye: GIT XXX, Moodle: 2018062515379
        // ************************************************************************************************************    

          // Define field pais to be added to talentospilos_usuario.
//           $table = new xmldb_table('talentospilos_usuario');
//           $field = new xmldb_field('id_pais', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'ayuda_disc');
  
//           // Conditionally launch add field pais.
//           if (!$dbman->field_exists($table, $field)) {
//               $dbman->add_field($table, $field);
//           }


//             // Define field vive_con to be added to talentospilos_usuario.
//             $table = new xmldb_table('talentospilos_usuario');
//             $field = new xmldb_field('vive_con', XMLDB_TYPE_TEXT, null, null, null, null, null, 'id_pais');
    
//             // Conditionally launch add field vive_con.
//             if (!$dbman->field_exists($table, $field)) {
//                 $dbman->add_field($table, $field);
//             }
//       // Define field hijos to be added to talentospilos_usuario.
//       $table = new xmldb_table('talentospilos_usuario');
//       $field = new xmldb_field('hijos', XMLDB_TYPE_TEXT, null, null, null, null, null, 'vive_con');

//       // Conditionally launch add field hijos.
//       if (!$dbman->field_exists($table, $field)) {
//           $dbman->add_field($table, $field);
//       }
//              // Define field id_cond_excepcion to be added to talentospilos_usuario.
//         $table = new xmldb_table('talentospilos_usuario');
//         $field = new xmldb_field('id_cond_excepcion', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'hijos');

//         // Conditionally launch add field id_cond_excepcion.
//         if (!$dbman->field_exists($table, $field)) {
//             $dbman->add_field($table, $field);
//         }

//               // Define field id_estado_civil to be added to talentospilos_usuario.
//         $table = new xmldb_table('talentospilos_usuario');
//         $field = new xmldb_field('id_estado_civil', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'id_cond_excepcion');

//         // Conditionally launch add field id_estado_civil.
//         if (!$dbman->field_exists($table, $field)) {
//             $dbman->add_field($table, $field);
//         }

//             // Define field id_identidad_gen to be added to talentospilos_usuario.
//         $table = new xmldb_table('talentospilos_usuario');
//         $field = new xmldb_field('id_identidad_gen', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'id_estado_civil');

//         // Conditionally launch add field id_identidad_gen.
//         if (!$dbman->field_exists($table, $field)) {
//             $dbman->add_field($table, $field);
//         }

//          // Define field id_act_simultanea to be added to talentospilos_usuario.
//          $table = new xmldb_table('talentospilos_usuario');
//          $field = new xmldb_field('id_act_simultanea', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'id_identidad_gen');
 
//          // Conditionally launch add field id_act_simultanea.
//          if (!$dbman->field_exists($table, $field)) {
//              $dbman->add_field($table, $field);
//          }

//           // Define field id_economics_data to be added to talentospilos_usuario.
//         $table = new xmldb_table('talentospilos_usuario');
//         $field = new xmldb_field('id_economics_data', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'id_act_simultanea');

//         // Conditionally launch add field id_economics_data.
//         if (!$dbman->field_exists($table, $field)) {
//             $dbman->add_field($table, $field);
//         }


//            // Define field anio_ingreso to be added to talentospilos_usuario.
//            $table = new xmldb_table('talentospilos_usuario');
//            $field = new xmldb_field('anio_ingreso', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'id_economics_data');
   
//            // Conditionally launch add field anio_ingreso.
//            if (!$dbman->field_exists($table, $field)) {
//                $dbman->add_field($table, $field);
//            }


//            // Define field actividades_ocio_deporte to be added to talentospilos_usuario.
//         $table = new xmldb_table('talentospilos_usuario');
//         $field = new xmldb_field('actividades_ocio_deporte', XMLDB_TYPE_TEXT, null, null, null, null, null, 'anio_ingreso');

//         // Conditionally launch add field actividades_ocio_deporte.
//         if (!$dbman->field_exists($table, $field)) {
//             $dbman->add_field($table, $field);
//         }

//            // Define field id_schema_json to be added to talentospilos_usuario.
//            $table = new xmldb_table('talentospilos_usuario');
//            $field = new xmldb_field('id_schema_json', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'actividades_ocio_deporte');
   
//            // Conditionally launch add field id_schema_json.
//            if (!$dbman->field_exists($table, $field)) {
//                $dbman->add_field($table, $field);
//            }

//              // Define field json_detalle to be added to talentospilos_usuario.
//         $table = new xmldb_table('talentospilos_usuario');
//         $field = new xmldb_field('json_detalle', XMLDB_TYPE_TEXT, null, null, null, null, null, 'id_schema_json');

//         // Conditionally launch add field json_detalle.
//         if (!$dbman->field_exists($table, $field)) {
//             $dbman->add_field($table, $field);
//         }


//         // ************************************************************************************************************
//          // Actualización:
//         // Se crea tabla de esquemas JSON
//          // Versión en la que se incluye: GIT XXX, Moodle: 

//                // Define table talentospilos_json_schema to be created.
//                $table = new xmldb_table('talentospilos_json_schema');

//                // Adding fields to table talentospilos_json_schema.
//                $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
//                $table->add_field('json_schema', XMLDB_TYPE_TEXT, null, null, null, null, null);
//                $table->add_field('alias', XMLDB_TYPE_CHAR, '200', null, null, null, null);
       
//                // Adding keys to table talentospilos_json_schema.
//                $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
       
//                // Conditionally launch create table for talentospilos_json_schema.
//                if (!$dbman->table_exists($table)) {
//                    $dbman->create_table($table);
//                }
       
//         // ************************************************************************************************************
//         // Actualización:
//         // Se crea tabla de datos económicos de un usuario
//         // Versión en la que se incluye: GIT XXX, Moodle: 

//             // Define table talentospilos_economics_data to be created.
//         $table = new xmldb_table('talentospilos_economics_data');

//         // Adding fields to table talentospilos_economics_data.
//         $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
//         $table->add_field('estrato', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
//         $table->add_field('prestacion_economica', XMLDB_TYPE_CHAR, '200', null, null, null, null);
//         $table->add_field('beca', XMLDB_TYPE_CHAR, '200', null, null, null, null);
//         $table->add_field('ayuda_transporte', XMLDB_TYPE_BINARY, null, null, XMLDB_NOTNULL, null, null);
//         $table->add_field('ayuda_materiales', XMLDB_TYPE_BINARY, null, null, XMLDB_NOTNULL, null, null);
//         $table->add_field('solvencia_econo', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
//         $table->add_field('ocupacion_padres', XMLDB_TYPE_TEXT, null, null, null, null, null);
//         $table->add_field('nivel_educ_padres', XMLDB_TYPE_TEXT, null, null, null, null, null);
//         $table->add_field('situa_laboral_padres', XMLDB_TYPE_TEXT, null, null, null, null, null);
//         $table->add_field('expectativas_laborales', XMLDB_TYPE_TEXT, null, null, null, null, null);

//         // Adding keys to table talentospilos_economics_data.
//         $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

//         // Conditionally launch create table for talentospilos_economics_data.
//         if (!$dbman->table_exists($table)) {
//             $dbman->create_table($table);
//         }
//         // ************************************************************************************************************
//         // Actualización:
//         // Se crea tabla que relaciona a un usuario con un detalle de una institución educativa y sus particularidades
//         // Versión en la que se incluye: GIT XXX, Moodle: 

//             // Define table talentospilos_user_institu to be created.
//             $table = new xmldb_table('talentospilos_user_institu');

//             // Adding fields to table talentospilos_user_institu.
//             $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
//             $table->add_field('id_usuario', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
//             $table->add_field('institucion', XMLDB_TYPE_CHAR, '200', null, XMLDB_NOTNULL, null, null);
//             $table->add_field('nivel_formacion', XMLDB_TYPE_CHAR, '200', null, XMLDB_NOTNULL, null, null);
//             $table->add_field('apoyos_recibidos', XMLDB_TYPE_TEXT, null, null, null, null, null);
    
//             // Adding keys to table talentospilos_user_institu.
//             $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    
//             // Conditionally launch create table for talentospilos_user_institu.
//             if (!$dbman->table_exists($table)) {
//                 $dbman->create_table($table);
//             }

//         // ************************************************************************************************************
//         // Actualización:
//         // Se crea tabla de etnias
//         // Versión en la que se incluye: GIT XXX, Moodle:             

//          // Define table talentospilos_etnia to be created.
//         $table = new xmldb_table('talentospilos_etnia');

//         // Adding fields to table talentospilos_etnia.
//         $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
//         $table->add_field('etnia', XMLDB_TYPE_CHAR, '200', null, null, null, null);

//         // Adding keys to table talentospilos_etnia.
//         $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

//         // Conditionally launch create table for talentospilos_etnia.
//         if (!$dbman->table_exists($table)) {
//             $dbman->create_table($table);
//         }

//               // Define field opcion_general to be added to talentospilos_etnia.
//         $table = new xmldb_table('talentospilos_etnia');
//         $field = new xmldb_field('opcion_general', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'etnia');

//         // Conditionally launch add field opcion_general.
//         if (!$dbman->field_exists($table, $field)) {
//             $dbman->add_field($table, $field);
//         }



//         // ************************************************************************************************************
//         // Actualización:
//         // Se crea tabla que relaciona a un usuario con una etnia
//         // Versión en la que se incluye: GIT XXX, Moodle: 

//                   // Define table talentospilos_etnia_usuario to be created.
//         $table = new xmldb_table('talentospilos_etnia_usuario');

//         // Adding fields to table talentospilos_etnia_usuario.
//         $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
//         $table->add_field('id_usuario', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
//         $table->add_field('id_etnia', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

//         // Adding keys to table talentospilos_etnia_usuario.
//         $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

//         // Conditionally launch create table for talentospilos_etnia_usuario.
//         if (!$dbman->table_exists($table)) {
//             $dbman->create_table($table);
//         }

//         // ************************************************************************************************************
//         // Actualización:
//         // Se crea tabla de actividades simultáneas, actividades que pueden ser realizadas por un usuario
//         // Versión en la que se incluye: GIT XXX, Moodle: 

//                 // Define table talentospilos_act_simultanea to be created.
//                 $table = new xmldb_table('talentospilos_act_simultanea');

//                 // Adding fields to table talentospilos_act_simultanea.
//                 $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
//                 $table->add_field('actividad', XMLDB_TYPE_CHAR, '200', null, null, null, null);
        
//                 // Adding keys to table talentospilos_act_simultanea.
//                 $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        
//                 // Conditionally launch create table for talentospilos_act_simultanea.
//                 if (!$dbman->table_exists($table)) {
//                     $dbman->create_table($table);
//                 }

//     // Define field opcion_general to be added to talentospilos_act_simultanea.
//     $table = new xmldb_table('talentospilos_act_simultanea');
//     $field = new xmldb_field('opcion_general', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'actividad');

//     // Conditionally launch add field opcion_general.
//     if (!$dbman->field_exists($table, $field)) {
//         $dbman->add_field($table, $field);
//     }


//         // ************************************************************************************************************
//         // Actualización:
//         // Se crea tabla de identidad de género
//         // Versión en la que se incluye: GIT XXX, Moodle: 


//                   // Define table talentospilos_identidad_gen to be created.
//         $table = new xmldb_table('talentospilos_identidad_gen');

//         // Adding fields to table talentospilos_identidad_gen.
//         $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
//         $table->add_field('genero', XMLDB_TYPE_CHAR, '200', null, null, null, null);

//         // Adding keys to table talentospilos_identidad_gen.
//         $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

//         // Conditionally launch create table for talentospilos_identidad_gen.
//         if (!$dbman->table_exists($table)) {
//             $dbman->create_table($table);
//         }
//    // Define field opcion_general to be added to talentospilos_identidad_gen.
//    $table = new xmldb_table('talentospilos_identidad_gen');
//    $field = new xmldb_field('opcion_general', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'genero');

//    // Conditionally launch add field opcion_general.
//    if (!$dbman->field_exists($table, $field)) {
//        $dbman->add_field($table, $field);
//    }

//         // ************************************************************************************************************
//         // Actualización:
//         // Se crea tabla de estado civil
//         // Versión en la que se incluye: GIT XXX, Moodle: 

//          // Define table talentospilos_estado_civil to be created.
//          $table = new xmldb_table('talentospilos_estado_civil');

//          // Adding fields to table talentospilos_estado_civil.
//          $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
//          $table->add_field('estado_civil', XMLDB_TYPE_CHAR, '200', null, null, null, null);
 
//          // Adding keys to table talentospilos_estado_civil.
//          $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
 
//          // Conditionally launch create table for talentospilos_estado_civil.
//          if (!$dbman->table_exists($table)) {
//              $dbman->create_table($table);
//          }
//          // ************************************************************************************************************
//         // Actualización:
//         // Se crea tabla de sexos válidos en el sistema
//         // Versión en la que se incluye: GIT XXX, Moodle: 

//         // Define table talentospilos_sexo to be created.
//         $table = new xmldb_table('talentospilos_sexo');

//         // Adding fields to table talentospilos_sexo.
//         $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
//         $table->add_field('sexo', XMLDB_TYPE_CHAR, '200', null, null, null, null);
//         // Adding keys to table talentospilos_sexo.
//         $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

//         // Conditionally launch create table for talentospilos_sexo.
//         if (!$dbman->table_exists($table)) {
//             $dbman->create_table($table);
//         }

//               // Define field opcion_general to be added to talentospilos_sexo.
//               $table = new xmldb_table('talentospilos_sexo');
//               $field = new xmldb_field('opcion_general', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'sexo');
      
//               // Conditionally launch add field opcion_general.
//               if (!$dbman->field_exists($table, $field)) {
//                   $dbman->add_field($table, $field);
//               }

//         // ************************************************************************************************************
//         // Actualización:
//         // Se crea tabla de paises
//         // Versión en la que se incluye: GIT XXX, Moodle: 

//              // Define table talentospilos_pais to be created.
//         $table = new xmldb_table('talentospilos_pais');

//         // Adding fields to table talentospilos_pais.
//         $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
//         $table->add_field('pais', XMLDB_TYPE_CHAR, '200', null, null, null, null);

//         // Adding keys to table talentospilos_pais.
//         $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

//         // Conditionally launch create table for talentospilos_pais.
//         if (!$dbman->table_exists($table)) {
//             $dbman->create_table($table);
//         }

//         // ************************************************************************************************************
//         // Actualización:
//         // Se crea tabla de condiciones de excepción de la Universidad del Valle
//         // Versión en la que se incluye: GIT XXX, Moodle: 

         
//             // Define table talentospilos_cond_excepcion to be created.
//             $table = new xmldb_table('talentospilos_cond_excepcion');

//             // Adding fields to table talentospilos_cond_excepcion.
//             $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
//             $table->add_field('condicion_excepcion', XMLDB_TYPE_TEXT, null, null, null, null, null);
    
//             // Adding keys to table talentospilos_cond_excepcion.
//             $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    
//             // Conditionally launch create table for talentospilos_cond_excepcion.
//             if (!$dbman->table_exists($table)) {
//                 $dbman->create_table($table);
//             }

//                     // Define field alias to be added to talentospilos_cond_excepcion.
//         $table = new xmldb_table('talentospilos_cond_excepcion');
//         $field = new xmldb_field('alias', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'condicion_excepcion');

//         // Conditionally launch add field alias.
//         if (!$dbman->field_exists($table, $field)) {
//             $dbman->add_field($table, $field);
//         }


//                      // Define field alias to be added to talentospilos_df_tipo_campo.
//                      $table = new xmldb_table('talentospilos_df_tipo_campo');
//                      $field = new xmldb_field('expresion_regular', XMLDB_TYPE_TEXT, null, null, null, null, null, 'fecha_hora_registro');
             
//                      // Conditionally launch add field alias.
//                      if (!$dbman->field_exists($table, $field)) {
//                          $dbman->add_field($table, $field);
//                      }



     
// //Insert register into database
// $array_tables = array();
// array_push($array_tables,"talentospilos_cond_excepcion","talentospilos_act_simultanea", 
//             "talentospilos_sexo","talentospilos_identidad_gen","talentospilos_etnia", "talentospilos_estado_civil", "talentospilos_pais");



// //Insert for each table
// for($t=0; $t<count($array_tables);$t++){
// $array_elements = array();
// $array_aditional = array();
// $new_register = new stdClass();

// $table  = $array_tables[$t];
// //Data switch table
// if($table=="talentospilos_cond_excepcion"){
// //Data to cond_excepcion
// array_push($array_elements,"Indígena (I.N.)", "Los más altos puntajes en el Examen de Estado (M.A.P.)", "Comunidades Afrocolombianas (C.A.)", "Cupo (C.U.)"
//                                 , "Programa de Reinserción (P.R.)","Los más altos puntajes en el Examen de Estado, de los colegios oficiales en los municipios del Departamento del Valle del Cauca (M.P.M.)"
//                                 , "Aspirantes que estén realizando actualmente su último año de bachillerato, provenientes de Departamentos donde no existen sedes ni seccionales de Instituciones de Educación Superior (D.N.I)"
//                                 , "Aspirantes que estén realizando actualmente su último año de bachillerato, que provengan de Municipios de difícil acceso o con problemas de orden público (M.D.P.)"
//                                 ,  "Población Desplazada. (P.D.)", "Ninguna de las anteriores") ;
// array_push($array_aditional,"I.N.", "M.A.P.", "C.A.", "C.U."
//                                 , "P.R.","M.P.M."
//                                 , "D.N.I"
//                                 , "M.D.P."
//                                 ,  "P.D.", "N.A") ;
// }
// if($table=="talentospilos_act_simultanea"){
// //Data to act_simultanea
// array_push($array_elements, "Monitor","Docente","Empleado","Ninguna") ;
// }
// if($table == "talentospilos_sexo"){
// //Data to sexo
// array_push($array_elements, "Masculino","Femenino")  ;
// }
// if($table == "talentospilos_identidad_gen"){
//     //Data to genero
//     array_push($array_elements, "Hombre", "Mujer","Transgénero femenino", "Transgénero masculino", "Persona sin género");
// }
// if($table == "talentospilos_etnia"){
//         //Data to etnia
//         array_push($array_elements, "Indígena","Rom", "Raizal del archipiélago de San Andres y Providencia", "Palenquero de San Basilo", "Negro(a), Mulato(a), Afrocolombiano(a), Afrodescendiente",
//                                     "Blanco(a), Mestizo(a)", "Ninguno de los anteriores");    
// }
// if($table == "talentospilos_estado_civil"){
//     //Data to estado_civil
//     array_push($array_elements, "Casado(a)", "Soltero(a)", "Divorciado(a)","Separado(a)","Unión libre","Viudo(a)");
// }
// if($table == "talentospilos_pais"){
//     //Data to pais
//     array_push($array_elements, "Afghanistan",
//     "Albania",
//     "Algeria",
//     "Andorra",
//     "Angola",
//     "Antigua and Barbuda",
//     "Argentina",
//     "Armenia",
//     "Australia",
//     "Austria",
//     "Azerbaijan",
//     "Bahamas",
//     "Bahrain",
//     "Bangladesh",
//     "Barbados",
//     "Belarus",
//     "Belgium",
//     "Belize",
//     "Benin",
//     "Bhutan",
//     "Bolivia",
//     "Bosnia and Herzegovina",
//     "Botswana",
//     "Brazil",
//     "Brunei",
//     "Bulgaria",
//     "Burkina Faso",
//     "Burundi",
//     "Cambodia",
//     "Cameroon",
//     "Canada",
//     "Cape Verde",
//     "Central African Republic",
//     "Chad",
//     "Chile",
//     "China",
//     "Colombia",
//     "Comoros",
//     "Congo (Brazzaville)",
//     "Congo",
//     "Costa Rica",
//     "Cote d'Ivoire",
//     "Croatia",
//     "Cuba",
//     "Cyprus",
//     "Czech Republic",
//     "Denmark",
//     "Djibouti",
//     "Dominica",
//     "Dominican Republic",
//     "East Timor (Timor Timur)",
//     "Ecuador",
//     "Egypt",
//     "El Salvador",
//     "Equatorial Guinea",
//     "Eritrea",
//     "Estonia",
//     "Ethiopia",
//     "Fiji",
//     "Finland",
//     "France",
//     "Gabon",
//     "Gambia, The",
//     "Georgia",
//     "Germany",
//     "Ghana",
//     "Greece",
//     "Grenada",
//     "Guatemala",
//     "Guinea",
//     "Guinea-Bissau",
//     "Guyana",
//     "Haiti",
//     "Honduras",
//     "Hungary",
//     "Iceland",
//     "India",
//     "Indonesia",
//     "Iran",
//     "Iraq",
//     "Ireland",
//     "Israel",
//     "Italy",
//     "Jamaica",
//     "Japan",
//     "Jordan",
//     "Kazakhstan",
//     "Kenya",
//     "Kiribati",
//     "Korea, North",
//     "Korea, South",
//     "Kuwait",
//     "Kyrgyzstan",
//     "Laos",
//     "Latvia",
//     "Lebanon",
//     "Lesotho",
//     "Liberia",
//     "Libya",
//     "Liechtenstein",
//     "Lithuania",
//     "Luxembourg",
//     "Macedonia",
//     "Madagascar",
//     "Malawi",
//     "Malaysia",
//     "Maldives",
//     "Mali",
//     "Malta",
//     "Marshall Islands",
//     "Mauritania",
//     "Mauritius",
//     "Mexico",
//     "Micronesia",
//     "Moldova",
//     "Monaco",
//     "Mongolia",
//     "Morocco",
//     "Mozambique",
//     "Myanmar",
//     "Namibia",
//     "Nauru",
//     "Nepa",
//     "Netherlands",
//     "New Zealand",
//     "Nicaragua",
//     "Niger",
//     "Nigeria",
//     "Norway",
//     "Oman",
//     "Pakistan",
//     "Palau",
//     "Palestina",
//     "Panama",
//     "Papua New Guinea",
//     "Paraguay",
//     "Peru",
//     "Philippines",
//     "Poland",
//     "Portugal",
//     "Qatar",
//     "Romania",
//     "Russia",
//     "Rwanda",
//     "Saint Kitts and Nevis",
//     "Saint Lucia",
//     "Saint Vincent",
//     "Samoa",
//     "San Marino",
//     "Sao Tome and Principe",
//     "Saudi Arabia",
//     "Senegal",
//     "Serbia and Montenegro",
//     "Seychelles",
//     "Sierra Leone",
//     "Singapore",
//     "Slovakia",
//     "Slovenia",
//     "Solomon Islands",
//     "Somalia",
//     "South Africa",
//     "Spain",
//     "Sri Lanka",
//     "Sudan",
//     "Suriname",
//     "Swaziland",
//     "Sweden",
//     "Switzerland",
//     "Syria",
//     "Taiwan",
//     "Tajikistan",
//     "Tanzania",
//     "Thailand",
//     "Togo",
//     "Tonga",
//     "Trinidad and Tobago",
//     "Tunisia",
//     "Turkey",
//     "Turkmenistan",
//     "Tuvalu",
//     "Uganda",
//     "Ukraine",
//     "United Arab Emirates",
//     "United Kingdom",
//     "United States",
//     "Uruguay",
//     "Uzbekistan",
//     "Vanuatu",
//     "Vatican City",
//     "Venezuela",
//     "Vietnam",
//     "Yemen",
//     "Zambia",
//     "Zimbabwe");
// }

// //INSERT INTO DB
// for  ($i = 0; $i < count($array_elements); $i++){

//     //CREATE OBJECT FOR EACH ELEMENT
//     if($table== "talentospilos_cond_excepcion"){
//     $new_register->condicion_excepcion = $array_elements[$i];  
//     $new_register->alias = $array_aditional[$i];
//     $condition = 'condicion_excepcion';
//     }
//     if($table== "talentospilos_act_simultanea"){
//         $new_register->actividad = $array_elements[$i];  
//         $new_register->opcion_general = 1;
//         $condition = 'actividad';
//         }
//     if($table=="talentospilos_sexo"){
//         $new_register->sexo = $array_elements[$i];  
//         $new_register->opcion_general = 1;
//         $condition = 'sexo';
//     }
//     if($table=="talentospilos_identidad_gen"){
//         $new_register->genero = $array_elements[$i];  
//         $new_register->opcion_general = 1;
//         $condition = 'genero';
//     }
//     if($table=="talentospilos_etnia"){
//         $new_register->etnia = $array_elements[$i];  
//         $new_register->opcion_general = 1;
//         $condition = 'etnia';
//     }
//     if($table=="talentospilos_estado_civil"){
//         $new_register->estado_civil = $array_elements[$i];  
//         $condition = 'estado_civil';
//     }
//     if($table=="talentospilos_pais"){
//         $new_register->pais = $array_elements[$i];  
//         $condition = 'pais';
//     }

//     if( !$DB->record_exists($table,array($condition=> $array_elements[$i]))){
//         $DB->insert_record($table, $new_register, true)    ;
//     }
//     }

// unset($array_elements,$array_aditional,$new_register, $table);
// }
// ************************************************************************************************************
// //         // Actualización:
// //         //Se modifica campo de la tabla usuario
//          // Changing type of field hijos on table talentospilos_usuario to int.
//          $table = new xmldb_table('talentospilos_usuario');
//          $field = new xmldb_field('hijos', XMLDB_TYPE_INTEGER, '3', null, null, null, null, 'vive_con');
 
//          // Launch change of type for field hijos.
//          $dbman->change_field_type($table, $field);


        // ACTUALIZACION BD CAMPOS PESTAÑA GENERAL
        // AGREGAR CAMPOS A TABLA USUARIO

        //     // Define field puntaje_icfes to be added to talentospilos_usuario.
        //     $table = new xmldb_table('talentospilos_usuario');
        //     $field = new xmldb_field('puntaje_icfes', XMLDB_TYPE_INTEGER, '3', null, null, null, null, 'json_detalle');

        //     // Conditionally launch add field puntaje_icfes.
        //     if (!$dbman->field_exists($table, $field)) {
        //         $dbman->add_field($table, $field);
        //     }

        //      // Define field estrato to be added to talentospilos_usuario.
        //      $table = new xmldb_table('talentospilos_usuario');
        //      $field = new xmldb_field('estrato', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'puntaje_icfes');
     
        //      // Conditionally launch add field estrato.
        //      if (!$dbman->field_exists($table, $field)) {
        //          $dbman->add_field($table, $field);
        //      }

        //     // Define field id_etnia to be added to talentospilos_usuario.
        //     $table = new xmldb_table('talentospilos_usuario');
        //     $field = new xmldb_field('id_etnia', XMLDB_TYPE_INTEGER, '2', null, null, null, null, 'estrato');
    
        //     // Conditionally launch add field id_etnia.
        //     if (!$dbman->field_exists($table, $field)) {
        //         $dbman->add_field($table, $field);
        //     }
            
        // $new_etnia = new StdClass;
        // $new_etnia->etnia = "NO DEFINIDO";
        // $new_etnia->opcion_general = 1;

        // $DB->insert_record("talentospilos_etnia", $new_etnia, true);

        // ****************************************************************************************************************//
        // Actualización: Se registran nuevas columnas para almacenar la expresión regular que valida los tipos de campos. //
        //  Versión: 2018112215570                                                                                         //
        // ****************************************************************************************************************//

        // $table = new xmldb_table('talentospilos_df_tipo_campo');
        // $field = new xmldb_field('regex_legible_humanos', XMLDB_TYPE_TEXT, null, null, null, null, null, 'expresion_regular');
             
        // // Conditionally launch add field regex_legible_humanos.
        // if (!$dbman->field_exists($table, $field)) {
        //     $dbman->add_field($table, $field);
        // }

        // $field = new xmldb_field('ejemplo', XMLDB_TYPE_TEXT, null, null, null, null, null, 'regex_legible_humanos');
             
        // // Conditionally launch add field ejemplo.
        // if (!$dbman->field_exists($table, $field)) {
        //     $dbman->add_field($table, $field);
        // }

        // $field_date = $DB->get_record('talentospilos_df_tipo_campo', array('campo'=>'DATE'));
        // $field_date->expresion_regular = "/^-?[0-9]+?-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/";
        // $field_date->regex_legible_humanos = "YYYY-MM-DD";
        // $field_date->ejemplo = "2005-05-05";
        // $DB->update_record('talentospilos_df_tipo_campo', $field_date);

        // $field_time = $DB->get_record('talentospilos_df_tipo_campo', array('campo'=>'TIME'));
        // $field_time->expresion_regular = "/^([01][0-9]|2[0-3]):([0-5][0-9])$/";
        // $field_time->regex_legible_humanos = "HH:MM";
        // $field_time->ejemplo = "05:05";
        // $DB->update_record('talentospilos_df_tipo_campo', $field_time);


         // ****************************************************************************************************************//
        // Actualización: Se modifican tipos de campos de la tabla talentospilos_economics_data. //
        //  Versión: 2018112315000                                                                                         //
        // ****************************************************************************************************************//

        //Prestación económica

        // // Changing type of field prestacion_economica on table talentospilos_economics_data to text.
        // $table = new xmldb_table('talentospilos_economics_data');
        // $field = new xmldb_field('prestacion_economica', XMLDB_TYPE_TEXT, null, null, null, null, null, 'estrato');

        // // Launch change of type for field prestacion_economica.
        // $dbman->change_field_type($table, $field);

        // //Beca

        // // Changing type of field beca on table talentospilos_economics_data to text.
        // $table = new xmldb_table('talentospilos_economics_data');
        // $field = new xmldb_field('beca', XMLDB_TYPE_TEXT, null, null, null, null, null, 'prestacion_economica');
      
        // // Launch change of type for field beca.
        // $dbman->change_field_type($table, $field);
      
        // //Ayuda transporte

        // // Changing type of field ayuda_transporte on table talentospilos_economics_data to text.
        // $table = new xmldb_table('talentospilos_economics_data');
        // $field = new xmldb_field('ayuda_transporte', XMLDB_TYPE_TEXT, null, null, null, null, null, 'beca');
     
        // // Launch change of type for field ayuda_transporte.
        // $dbman->change_field_type($table, $field);

        // //Ayuda materiales

        // // Changing type of field ayuda_materiales on table talentospilos_economics_data to text.
        // $table = new xmldb_table('talentospilos_economics_data');
        // $field = new xmldb_field('ayuda_materiales', XMLDB_TYPE_TEXT, null, null, null, null, null, 'ayuda_transporte');
   
        // // Launch change of type for field ayuda_materiales.
        // $dbman->change_field_type($table, $field);

        // //Estrato

        // // Changing type of field estrato on table talentospilos_economics_data to int.
        // $table = new xmldb_table('talentospilos_economics_data');
        // $field = new xmldb_field('estrato', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'id');

        // // Launch change of type for field estrato.
        // $dbman->change_field_type($table, $field);

        // //Solvencia económica

        // // Changing type of field solvencia_econo on table talentospilos_economics_data to int.
        // $table = new xmldb_table('talentospilos_economics_data');
        // $field = new xmldb_field('solvencia_econo', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'ayuda_materiales');

        // // Launch change of type for field solvencia_econo.
        // $dbman->change_field_type($table, $field);

        // //Agrega campo id_ases_user a tabla talentospilos_economics_data
        //         // Define field id_ases_user to be added to talentospilos_economics_data.
        // $table = new xmldb_table('talentospilos_economics_data');
        // $field = new xmldb_field('id_ases_user', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'expectativas_laborales');

        // // Conditionally launch add field id_ases_user.
        // if (!$dbman->field_exists($table, $field)) {
        //     $dbman->add_field($table, $field);
        // }

        //  // ****************************************************************************************************************//
        // // Actualización: Se agrega tabla de ocupaciones  //
        // //  Versión: 2018112610360                                                                                         //
        // // ****************************************************************************************************************//

        //   // Define table talentospilos_ocupaciones to be created.
        //   $table = new xmldb_table('talentospilos_ocupaciones');

        //   // Adding fields to table talentospilos_ocupaciones.
        //   $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        //   $table->add_field('ocupacion', XMLDB_TYPE_TEXT, null, null, null, null, null);
        //   $table->add_field('value', XMLDB_TYPE_CHAR, '200', null, null, null, null);
        //   $table->add_field('alias', XMLDB_TYPE_CHAR, '200', null, null, null, null);
        //   $table->add_field('categoria_padre', XMLDB_TYPE_TEXT, null, null, null, null, null);
  
        //   // Adding keys to table talentospilos_ocupaciones.
        //   $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
  
        //   // Conditionally launch create table for talentospilos_ocupaciones.
        //   if (!$dbman->table_exists($table)) {
        //       $dbman->create_table($table);
        //   }

        // //Se agregan registros a tabla talentospilos_ocupaciones
        //   $array_ocupaciones = array();
        //   $array_padres = array();
        //   array_push($array_padres,
        //   array("categoria" =>"Fuerza Pública", "cantidad"=> 2 ), 
        //   array("categoria" =>"Miembros del Poder Ejecutivo, de los cuerpos legislativos y personal directivo de la administración pública y de empresas", "cantidad"=> 4 ),
        //   array("categoria" =>"Profesionales universitarios, científicos e intelectuales", "cantidad"=> 4 ), 
        //   array("categoria" =>"Técnicos, postsecundarios no universitarios y asistentes", "cantidad"=> 4 ), 
        //   array("categoria" =>"Empleados de oficina", "cantidad"=> 2 ), 
        //   array("categoria" =>"Trabajadores de los servicios y vendedores", "cantidad"=> 3 ), 
        //   array("categoria" =>"Agricultores, trabajadores y obreros agropecuarios, forestales y pesqueros", "cantidad"=> 2 ), 
        //   array("categoria" =>"Oficiales, operarios, artesanos y trabajadores de la industria manufacturera, de la construcción y de la minería", "cantidad"=> 7 ), 
        //   array("categoria" =>"Operadores de instalaciones, de máquinas y ensambladores", "cantidad"=> 3 ), 
        //   array("categoria" =>"Trabajadores no calificados", "cantidad"=> 3 ),
        //   array("categoria" =>"Otra ocupación", "cantidad"=> 1 ));

        //   array_push($array_ocupaciones,
        //     array("ocupacion"=>"FUERZAS MILITARES (EJÉRCITO, ARMADA Y FUERZA AÉREA)", "alias"=>"option_fuerzas_militares"),
        //     array("ocupacion"=>"POLICÍA NACIONAL", "alias"=>"option_policia"),
        //     array("ocupacion"=>"MIEMBROS DEL PODER EJECUTIVO, DE LOS CUERPOS LEGISLATIVOS Y PERSONAL DIRECTIVO DE LA ADMINISTRACIÓN PÚBLICA", "alias"=>"option_poder_ejecutivo"),
        //     array("ocupacion"=>"DIRECTORES Y GERENTES GENERALES DE EMPRESAS PRIVADAS", "alias"=>"option_director_gerente"),
        //     array("ocupacion"=>"DIRECTORES DE DEPARTAMENTOS PÚBLICOS Y PRIVADOS", "alias"=>"option_director_dpto"),
        //     array("ocupacion"=>"COORDINADORES Y SUPERVISORES EN MANDOS MEDIOS DE EMPRESAS PÚBLICAS Y PRIVADAS", "alias"=>"option_coord_sup"),
        //     array("ocupacion"=>"PROFESIONALES DE LAS CIENCIAS FÍSICAS, QUÍMICAS, MATEMÁTICAS Y DE LA INGENIERÍA", "alias"=>"option_prof_fis"),
        //     array("ocupacion"=>"PROFESIONALES DE LAS CIENCIAS BIOLÓGICAS, LA MEDICINA Y LA SALUD", "alias"=>"option_prof_bio"),
        //     array("ocupacion"=>"PROFESIONALES DE LA EDUCACIÓN", "alias"=>"option_prof_edu"),
        //     array("ocupacion"=>"OTROS PROFESIONALES CIENTÍFICOS E INTELECTUALES", "alias"=>"option_prof_otros"),
        //     array("ocupacion"=>"TÉCNICOS Y POSTSECUNDARIOS NO UNIVERSITARIOS DE LAS CIENCIAS FÍSICAS, QUÍMICAS, LA INGENIERÍA Y AFINES", "alias"=>"option_tec_fis"),
        //     array("ocupacion"=>"TÉCNICOS Y POSTSECUNDARIOS NO UNIVERSITARIOS DE LAS CIENCIAS BIOLÓGICAS, LA MEDICINA Y LA SALUD", "alias"=>"option_tec_bio"),
        //     array("ocupacion"=>"ASISTENTES DE ENSEÑANZA E INSTRUCTORES DE EDUCACIÓN FORMAL, ESPECIAL Y VOCACIONAL", "alias"=>"option_tec_edu"),
        //     array("ocupacion"=>"OTROS TÉCNICOS, POSTSECUNDARIOS NO UNIVERSITARIOS Y ASISTENTES", "alias"=>"option_tec_otros"),
        //     array("ocupacion"=>"OFICINISTAS", "alias"=>"option_oficinista"),
        //     array("ocupacion"=>"EMPLEADOS DE TRATO DIRECTO CON EL PÚBLICO", "alias"=>"option_trato_publico"),
        //     array("ocupacion"=>"TRABAJADORES DE LOS SERVICIOS PERSONALES", "alias"=>"option_serv_personales"),
        //     array("ocupacion"=>"PERSONAL DE LOS SERVICIOS DE PROTECCIÓN Y SEGURIDAD", "alias"=>"option_serv_proteccion"),
        //     array("ocupacion"=>"MODELOS, VENDEDORES Y DEMOSTRADORES", "alias"=>"option_modelos"),
        //     array("ocupacion"=>"AGRICULTORES Y TRABAJADORES FORESTALES, PECUARIOS Y PESQUEROS" , "alias"=>"option_agricultura"),
        //     array("ocupacion"=>"OBREROS Y PEONES AGROPECUARIOS, FORESTALES, PESQUEROS Y AFINES", "alias"=>"option_agropecuario"),
        //     array("ocupacion"=>"OFICIALES Y OPERARIOS DE LA INDUSTRIA EXTRACTIVA", "alias"=>"option_industria_extractiva"),
        //     array("ocupacion"=>"OFICIALES Y OPERARIOS DE LA CONSTRUCCIÓN" , "alias"=>"option_construccion"),
        //     array("ocupacion"=>"OPERARIOS DE LA METALURGIA Y AFINES", "alias"=>"option_metalurgia"),
        //     array("ocupacion"=>"MECÁNICOS Y AJUSTADORES DE MÁQUINAS Y EQUIPOS", "alias"=>"option_mec_equipos"),
        //     array("ocupacion"=>"MECÁNICOS DE PRECISIÓN, OPERARIOS DE LAS ARTES GRÁFICAS Y AFINES", "alias"=>"option_mec_precision"),
        //     array("ocupacion"=>"ARTESANOS", "alias"=>"option_artesania"),
        //     array("ocupacion"=>"OPERARIOS DE ALIMENTOS, TEXTILES, PIELES, MADERAS Y AFINES", "alias"=>"option_textiles"),
        //     array("ocupacion"=>"OPERADORES DE INSTALACIONES FIJAS Y AFINES", "alias"=>"option_instalaciones"),
        //     array("ocupacion"=>"OPERADORES DE MÁQUINAS Y ENSAMBLADORES", "alias"=>"option_maquinas"),
        //     array("ocupacion"=>"CONDUCTORES DE VEHÍCULOS Y OPERADORES DE EQUIPOS PESADOS MÓVILES", "alias"=>"option_conduccion"),
        //     array("ocupacion"=>"TRABAJADORES NO CALIFICADOS DE SERVICIOS (EXCEPTO EL PERSONAL DOMÉSTICO Y AFINES)", "alias"=>"option_no_calificados"),
        //     array("ocupacion"=>"PERSONAL DOMÉSTICO, ASEADORES, LAVANDEROS, PLANCHADORES Y AFINES" , "alias"=>"option_domestico"),
        //     array("ocupacion"=>"OBREROS DE LA MINERIA, LA CONSTRUCCIÓN, LA INDUSTRIA MANUFACTURERA Y EL TRANSPORTE", "alias"=>"option_obreros_mineria" ),
        //     array("ocupacion"=>"OTRA OCUPACIÓN" , "alias"=>"option_otro"));

        //     $table='talentospilos_ocupaciones';
        //     $condition = 'value';
        //     $new_register = new stdClass();

        //     foreach($array_padres as $padre){

        //         for($i = 0; $i < $padre["cantidad"]; $i++){
                    
                   
        //             if( !$DB->record_exists($table,array($condition=> $array_ocupaciones[0]["alias"]))){

        //                 $new_register->ocupacion     = $array_ocupaciones[0]["ocupacion"];

        //                 if(strlen($array_ocupaciones[0]["ocupacion"])>20){
    
        //                     $new_register->alias     = substr($array_ocupaciones[0]["ocupacion"],0,20);
    
        //                 }else{
        //                     $new_register->alias     = substr($array_ocupaciones[0]["ocupacion"],0,9); 
        //                 }
                         
        //                 $new_register->value         = $array_ocupaciones[0]["alias"];
        //                 $new_register->categoria_padre     = $padre["categoria"];
                        
        //                 if($DB->insert_record($table, $new_register, true)){
                            
        //                     array_splice($array_ocupaciones,0,1);
        //                 }
                        
        //             }
        //         }
                

        //     }



        // // ****************************************************************************************************************//
        // // Actualización: Se agrega tabla de datos de salud health_data  //
        // //  Versión: 2018112911000                                                                                         //
        // // ****************************************************************************************************************//

        //    // Define table talentospilos_health_data to be created.
        //    $table = new xmldb_table('talentospilos_health_data');

        //    // Adding fields to table talentospilos_health_data.
        //    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        //    $table->add_field('regimen_salud', XMLDB_TYPE_TEXT, null, null, null, null, null);
        //    $table->add_field('servicio_salud_vinculado', XMLDB_TYPE_TEXT, null, null, null, null, null);
        //    $table->add_field('servicios_usados', XMLDB_TYPE_TEXT, null, null, null, null, null);
        //    $table->add_field('id_ases_user', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
   
        //    // Adding keys to table talentospilos_health_data.
        //    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
   
        //    // Conditionally launch create table for talentospilos_health_data.
        //    if (!$dbman->table_exists($table)) {
        //        $dbman->create_table($table);
        //    }
   
        // // ****************************************************************************************************************//
        // // Actualización: Se agrega tabla de logs generales  y eventos para logs //
        // //  Versión: 2018120716300                                                                                         //
        // // ****************************************************************************************************************//
                
        // *****************************************************************************************//
        //  Actualización: Se agrega la columna recorder para diferenciar quién registra el riesgo  //
        //  y la tabla talentospilos_incidencias para la gestión de incidencias.                    //
        //  Versión: 2018121014080                                                                  //
        // *****************************************************************************************//

          
        // Define table talentospilos_general_logs to be created.
        // $table = new xmldb_table('talentospilos_general_logs');

        // // Adding fields to table talentospilos_general_logs.
        // $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        // $table->add_field('id_moodle_user', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('datos_previos', XMLDB_TYPE_TEXT, null, null, null, null, null);
        // $table->add_field('datos_enviados', XMLDB_TYPE_TEXT, null, null, null, null, null);
        // $table->add_field('datos_almacenados', XMLDB_TYPE_TEXT, null, null, null, null, null);
        // $table->add_field('fecha_registro', XMLDB_TYPE_DATETIME, null, null, XMLDB_NOTNULL, null, "now()");
        // $table->add_field('id_evento', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('instanceid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('id_ases_user', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        // $table->add_field('navegador', XMLDB_TYPE_TEXT, null, null, null, null, null);
        // $table->add_field('title_retorno', XMLDB_TYPE_CHAR, '200', null, null, null, null);
        // $table->add_field('msg_retorno', XMLDB_TYPE_CHAR, '200', null, null, null, null);
        // $table->add_field('status_retorno', XMLDB_TYPE_CHAR, '200', null, null, null, null);
        // $table->add_field('url', XMLDB_TYPE_TEXT, null, null, null, null, null);
        // $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // // Adding keys to table talentospilos_general_logs.
        // $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // // Conditionally launch create table for talentospilos_general_logs.
        // if (!$dbman->table_exists($table)) {
        //     $dbman->create_table($table);
        // }



        //           // Define table talentospilos_events_to_logs to be created.
        // $table = new xmldb_table('talentospilos_events_to_logs');

        // // Adding fields to table talentospilos_events_to_logs.
        // $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        // $table->add_field('name_event', XMLDB_TYPE_CHAR, '200', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('description', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        // $table->add_field('funcionalidad', XMLDB_TYPE_CHAR, '200', null, XMLDB_NOTNULL, null, null);

        // // Adding keys to table talentospilos_events_to_logs.
        // $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // // Conditionally launch create table for talentospilos_events_to_logs.
        // if (!$dbman->table_exists($table)) {
        //     $dbman->create_table($table);
        // }

        // //Cargar eventos aplicables a acciones de student_profile asociados al desarrollo de Discapacidad

        // $event = new stdClass();
        // $event->name_event          = 'edit_discapacity_initial_form_sp';
        // $event->description   = "Edición de las características de discapacidad de un estudiante en la ficha de percepción de discapacidad.";
        // $event->funcionalidad = 'student_profile';

        // if( !$DB->record_exists('talentospilos_events_to_logs',array('name_event'=> $event->name_event))){
        //     $DB->insert_record('talentospilos_events_to_logs', $event, true);
        // }

        // $event = new stdClass();
        // $event->name_event          = 'edit_economics_tab_sp';
        // $event->description   = "Edición de los datos económicos de un estudiante en la ficha de percepción de discapacidad.";
        // $event->funcionalidad = 'student_profile';

        // if( !$DB->record_exists('talentospilos_events_to_logs',array('name_event'=> $event->name_event))){
        //     $DB->insert_record('talentospilos_events_to_logs', $event, true);
        // }

        // $event = new stdClass();
        // $event->name_event          = 'edit_salud_tab_sp';
        // $event->description   = "Edición de los datos de servicio de salud de un estudiante en la ficha de percepción de discapacidad.";
        // $event->funcionalidad = 'student_profile';

        // if( !$DB->record_exists('talentospilos_events_to_logs',array('name_event'=> $event->name_event))){
        //     $DB->insert_record('talentospilos_events_to_logs', $event, true);
        // }

        // $event = new stdClass();
        // $event->name_event          = 'save_economics_tab_sp';
        // $event->description   = "Registro inicial de los datos económicos de un estudiante en la ficha de percepción de discapacidad.";
        // $event->funcionalidad = 'student_profile';

        // if( !$DB->record_exists('talentospilos_events_to_logs',array('name_event'=> $event->name_event))){
        //     $DB->insert_record('talentospilos_events_to_logs', $event, true);
        // }

        // $event = new stdClass();
        // $event->name_event          = 'save_salud_tab_sp';
        // $event->description   = "Registro inicial de los datos de servicio de salud de un estudiante en la ficha de percepción de discapacidad.";
        // $event->funcionalidad = 'student_profile';

        // if( !$DB->record_exists('talentospilos_events_to_logs',array('name_event'=> $event->name_event))){
        //     $DB->insert_record('talentospilos_events_to_logs', $event, true);
        // }


        // // Define field recorder to be added to talentospilos_riesg_usuario.
        // $table = new xmldb_table('talentospilos_riesg_usuario');
        // $field = new xmldb_field('recorder', XMLDB_TYPE_TEXT, null, null, null, null, null, 'calificacion_riesgo');

        // // Conditionally launch add field recorder.
        // if (!$dbman->field_exists($table, $field)) {
        //     $dbman->add_field($table, $field);
        // }

        // // Define table talentospilos_incidencias to be created.
        // $table = new xmldb_table('talentospilos_incidencias');

        // // Adding fields to table talentospilos_incidencias.
        // $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        // $table->add_field('id_usuario_registra', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('id_usuario_cierra', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        // $table->add_field('estados', XMLDB_TYPE_TEXT, null, null, null, null, null);
        // $table->add_field('info_sistema', XMLDB_TYPE_TEXT, null, null, null, null, null);
        // $table->add_field('comentarios', XMLDB_TYPE_TEXT, null, null, null, null, null);
        // $table->add_field('cerrada', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0');
        // $table->add_field('fecha_hora_registro', XMLDB_TYPE_DATETIME, null, null, XMLDB_NOTNULL, null, "now()");

        // // Adding keys to table talentospilos_incidencias.
        // $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // // Conditionally launch create table for talentospilos_incidencias.
        // if (!$dbman->table_exists($table)) {
        //     $dbman->create_table($table);
        // }


        // //Add duration field to demographic table

        // // Define field duracion to be added to talentospilos_demografia.
        // $table = new xmldb_table('talentospilos_demografia');
        // $field = new xmldb_field('duracion', XMLDB_TYPE_FLOAT, '10', null, null, null, null, 'barrio');

        // // Conditionally launch add field duracion.
        // if (!$dbman->field_exists($table, $field)) {
        //     $dbman->add_field($table, $field);
        // }

        // // Define field distancia to be added to talentospilos_demografia.
        // $table = new xmldb_table('talentospilos_demografia');
        // $field = new xmldb_field('distancia', XMLDB_TYPE_FLOAT, '10', null, null, null, null, 'duracion');

        // // Conditionally launch add field distancia.
        // if (!$dbman->field_exists($table, $field)) {
        //     $dbman->add_field($table, $field);
        // }

        
        // // Changing nullability of field id_usuario_cierra on table talentospilos_incidencias to null.
        // $table = new xmldb_table('talentospilos_incidencias');
        // $field = new xmldb_field('id_usuario_cierra', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'id_usuario_registra');

        // // Launch change of nullability for field id_usuario_cierra.
        // $dbman->change_field_notnull($table, $field);

        // *****************************************************************************************//
        //  Actualización: Se agrega tabla para datos académicos adicionales                        //
        //  Versión: 2019032914300                                                                  //
        // *****************************************************************************************//
    

           // Define table talentospilos_academics_data to be created.
        // $table = new xmldb_table('talentospilos_academics_data');

        // // Adding fields to table talentospilos_academics_data.
        // $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        // $table->add_field('id_ases_user', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('resolucion_programa', XMLDB_TYPE_TEXT, null, null, null, null, null);
        // $table->add_field('creditos_totales', XMLDB_TYPE_INTEGER, '3', null, null, null, null);
        // $table->add_field('otras_instituciones', XMLDB_TYPE_TEXT, null, null, null, null, null);
        // $table->add_field('dificultades', XMLDB_TYPE_TEXT, null, null, null, null, null);
        // $table->add_field('observaciones', XMLDB_TYPE_TEXT, null, null, null, null, null);
        // $table->add_field('titulo_academico_colegio', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // // Adding keys to table talentospilos_academics_data.
        // $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // // Conditionally launch create table for talentospilos_academics_data.
        // if (!$dbman->table_exists($table)) {
        //     $dbman->create_table($table);
        // }

        //    // *****************************************************************************************//
        // //  Actualización: Se actualiza JSON SCHEMA ficha de discapacidad                    //
        // //  Versión: 2019040211300                                                                  //
        // // *****************************************************************************************//

        // $new_schema = new stdClass();
        // $new_schema->id = 1;
        // $new_schema->json_schema = json_encode( '{ "__comment": {"general": "El schema json de discapacidad describe las posibles características que debe tener un registro que utilice este modelo. Es necesario hacer la validación con este schema antes de guardar un registro en la base de datos.",
        //     "ubicacion": "Este schema se encuentra almacenado en la base de datos tabla talentospilos_json_schema.",
        //     "ubicacion_uso": "El schema se implementa en el archivo discapacity_tab_api.php con la ayuda de una librería externa de php.",
        //     "uso": "La librería permite validar si un JSON cumple con las características del Schema. En este caso, si un JSON (detalle discapacidad) cumple con el esquema a continuación",
        //     "warning1": "Evite cambiar el schema. Si es necesario, debe modificarlo de acuerdo al funcionamiento de la herramienta. Tenga en cuenta la validación del front para cambiar el Schema",
        //     "warning2": "Para modificar el Schema, tenga en cuenta la interfaz. En muchos casos, los enum contienen identificadores de elementos html. Por ello, si los identificadores opcionales no son idénticos, fallará la validación aunque la lógica esté bien.",
        //     "warning3": "Ejemplo: Si usted agrega un campo de selección múltiple, deberá validar las opciones posibles en la propiedad enum. Si su html tiene elementos con id = \'input_nuevo_campo1\', id = \'input_nuevo_campo2\', y el Schema, en su característica particular tiene un enum : [\'input_nuevo_campo2\',\'input_nuevo_campo\'], es posible seleccionar \'input_nuevo_campo1\' y fallará la valdiación interna",
        //     "warning4": "Si modifica en Schema, actualice el mismo en la base de datos. Debe tener en cuenta que el Schema usado para validar en este caso es el de id = 1 en la base da datos",
        //     "importante": "Evite modificar las características existentes"},
        //     "title": "Detalle de discapacidad",
        //     "description": "Un detalle de discapacidad de un usuario",
        //     "type": "object",
        //     "properties": {
            
        //     "percepcion_discapacidad":{
        //     "description": "¿Considera que presenta algún tipo de discapacidad o limitación?",
        //     "required": [
        //     "key_percepcion",
        //     "descripcion"
        //     ],
        //     "properties": {
        //     "key_percepcion": {
        //     "type": "string"
        //     },
        //     "descripcion": {
        //     "type": "string"
        //     }
        //     },
        //     "maxProperties": 2,
        //     "aditionalProperties": false
            
        //     },
        //     "condicion_adquisicion": {
        //     "description": "Define la condición de adquisición de la discapacidad",
        //     "required": [
        //     "key_condicion",
        //     "condicion"
        //     ],
        //     "properties": {
        //     "key_condicion": {
        //     "enum": [
        //     "cond_adquisicion"
        //     ],
        //     "type": "string"
        //     },
        //     "condicion": {
        //     "type": "array",
        //     "items":{
        //     "type":"string",
        //     "enum": [
        //     "Enfermedad general",
        //     "Enfermedad general de orden genético",
        //     "Enfermedad general de orden hereditario",
        //     "Accidente",
        //     "Consumo de sustancias psicoactivas",
        //     "Víctima de violencias",
        //     "Víctima de conflicto armado",
        //     "Otra"
        //     ]
        //     },
        //     "maxItems": 8,
        //     "uniqueItems": true
        //     },
        //     "key_otra_condicion": {
        //     "enum": [
        //     "otro_cond_adquisicion"
        //     ],
        //     "type": "string"
        //     },
        //     "otra_condicion": {
        //     "type": "string"
        //     }
        //     },
        //     "maxProperties": 4,
        //     "aditionalProperties": false
        //     },
        //     "diagnostico_discapacidad": {
        //     "description": "¿Cuenta con un diagnóstico de su discapacidad?",
        //     "required": [
        //     "key_diagnostico",
        //     "tiene_diagnostico"
        //     ],
        //     "properties": {
        //     "key_diagnostico": {
        //     "enum": [
        //     "check_diagnostico"
        //     ],
        //     "type": "string"
        //     },
        //     "tiene_diagnostico": {
        //     "enum": [
        //     0,
        //     1
        //     ],
        //     "type": "number"
        //     },
        //     "key_descripcion": {
        //     "enum": [
        //     "textarea_diagnostico"
        //     ],
        //     "type": "string"
        //     },
        //     "descripcion": {
        //     "type": "string"
        //     }
        //     },
        //     "maxProperties": 4,
        //     "aditionalProperties": false
        //     },
        //     "tipo_discapacidad": {
        //     "description": "Define tipo de discapacidad",
        //     "required": [
        //     "key_tipo",
        //     "tipo_discapacidad"
        //     ],
        //     "properties": {
        //     "key_tipo": {
        //     "enum": [
        //     "tipo_discapacidad"
        //     ],
        //     "type": "string"
        //     },
        //     "tipo_discapacidad": {
            
        //     "type": "array",
        //     "items":{
        //     "type":"string",
        //     "enum": [
        //     "Cognitiva",
        //     "Psicosocial",
        //     "Física",
        //     "Sensorial",
        //     "Múltiple",
        //     "Otra"
        //     ]
        //     },
        //     "maxItems": 6,
        //     "uniqueItems": true
            
        //     },
        //     "key_otro_tipo": {
        //     "enum": [
        //     "otra_discapacidad"
        //     ],
        //     "type": "string"
        //     },
        //     "otro_tipo": {
        //     "type": "string"
        //     }
        //     },
        //     "maxProperties": 4,
        //     "aditionalProperties": false
        //     },
        //     "certificado_invalidez": {
        //     "description": "¿Tiene certificado de invalidez?",
        //     "required": [
        //     "key_certificado",
        //     "tiene_certificado"
        //     ],
        //     "properties": {
        //     "key_certificado": {
        //     "enum": [
        //     "check_certificado_invalidez"
        //     ],
        //     "type": "string"
        //     },
        //     "tiene_certificado": {
        //     "enum": [
        //     0,
        //     1
        //     ],
        //     "type": "number"
        //     },
        //     "key_porcentaje": {
        //     "enum": [
        //     "input_porcentaje_inv"
        //     ],
        //     "type": "string"
        //     },
        //     "porcentaje": {
        //     "minimum": 0,
        //     "maximum": 100,
        //     "type": "number"
        //     }
        //     },
        //     "maxProperties": 4,
        //     "aditionalProperties": false
        //     },
        //     "dificultad_permanente_funciones": {
        //     "description": "¿De las siguientes funciones en cuáles presenta usted dificultad permanente?",
        //     "type": "array",
        //     "items": {
        //     "$ref": "#/definitions/dificults"
        //     },
        //     "maxItems": 9,
        //     "minItems": 0,
        //     "uniqueItems": true
        //     },
        //     "condicion_salud_organos": {
        //     "description": "En los siguientes órganos y/o sistemas, ¿Cuáles presenta alguna condición de salud a tener en cuenta?",
        //     "type": "array",
        //     "items": {
        //     "$ref": "#/definitions/conditions",
        //     "maxItems": 12,
        //     "minItems": 0,
        //     "uniqueItems": true
        //     }
        //     },
        //     "necesidades_situaciones": {
        //     "description": "Indica en cuales de estas situaciones usted experimenta una necesidad diferente o dificultad",
        //     "type": "array",
        //     "items": {
        //     "$ref": "#/definitions/necessities",
        //     "maxItems": 15,
        //     "minItems": 0,
        //     "uniqueItems": true
        //     }
        //     },
        //     "factores_impacto": {
        //     "description": "Frente a la limitación de su participación y óptimo desempeño, el factor que más le impacta en los diferentes escenarios de la vida universitarias es/son",
        //     "type": "array",
        //     "items": {
        //     "$ref": "#/definitions/factor"
        //     },
        //     "maxItems": 11,
        //     "minItems": 0,
        //     "uniqueItems": true
        //     },
        //     "posibilidad_actividades": {
        //     "description": "Desde su diversidad funcional, las exigencias de cada escenario de la vida en la Universidad, así como las condiciones con las que cuenta actualmente, indique las posibilidades con las que cuenta para llevar a cabo las siguientes actividades cotidianas:",
        //     "type": "array",
        //     "items": {
        //     "$ref": "#/definitions/possibilities"
        //     },
        //     "maxItems": 21,
        //     "minItems": 0,
        //     "uniqueItems": true
        //     },
        //     "apoyo_principal_cotidiano": {
        //     "description": "Para realizar actividades cotidianas y académicas en su casa, ¿en quién se apoya?",
        //     "required": [
        //     "key_apoyo",
        //     "apoyo_cotidiano"
        //     ],
        //     "maxProperties": 4,
        //     "properties": {
        //     "key_apoyo": {
        //     "type": "string",
        //     "enum": [
        //     "input_radio_oa1",
        //     "input_radio_oa2",
        //     "input_radio_oa3",
        //     "input_radio_oa4",
        //     "input_radio_otro_oa"
        //     ]
        //     },
        //     "key_otro_apoyo": {
        //     "type": "string",
        //     "enum": [
        //     "input_otro_apoyo"
        //     ]
        //     },
        //     "apoyo": {
        //     "type": "string",
        //     "enum": [
        //     "Amigos",
        //     "Pareja",
        //     "Familia",
        //     "Servicio de salud",
        //     "Otro"
        //     ]
        //     },
        //     "otro_apoyo": {
        //     "type": "string"
        //     }
        //     },
        //     "if": {
        //     "properties": {
        //     "key_apoyo": {
        //     "enum": [
        //     "input_radio_otro_oa"
        //     ]
        //     }
        //     }
        //     },
        //     "then": {
        //     "required": [
        //     "otro_apoyo",
        //     "key_otro_apoyo"
        //     ]
        //     }
        //     },
        //     "medio_transporte": {
        //     "description": "Para desplazarse a la universida usted principalmente",
        //     "required": [
        //     "key_transoporte",
        //     "transporte"
        //     ],
        //     "maxProperties": 4,
        //     "properties": {
        //     "key_transoporte": {
        //     "type": "string",
        //     "enum": [
        //     "input_radio_ot1",
        //     "input_radio_ot2",
        //     "input_radio_ot3",
        //     "input_radio_otro_ot"
        //     ]
        //     },
        //     "key_otro_transporte": {
        //     "type": "string",
        //     "enum": [
        //     "input_otro_transporte"
        //     ]
        //     },
        //     "transporte": {
        //     "type": "string",
        //     "enum": [
        //     "Paga transporte privado",
        //     "Usa el transporte público",
        //     "Tiene transporte propio",
        //     "Otro"
        //     ]
        //     },
        //     "otro_transporte": {
        //     "type": "string"
        //     }
        //     },
        //     "if": {
        //     "properties": {
        //     "key_transoporte": {
        //     "enum": [
        //     "input_radio_otro_ot"
        //     ]
        //     }
        //     }
        //     },
        //     "then": {
        //     "required": [
        //     "otro_transporte",
        //     "key_otro_transporte"
        //     ]
        //     }
        //     },
        //     "participa_asociacion": {
        //     "description": "Participa de alguna organización o asociación con otras personas con discapacidad o con condiciones similares a las que usted experimenta",
        //     "required": [
        //     "key_participa",
        //     "participa"
        //     ],
        //     "maxProperties": 4,
        //     "properties": {
        //     "participa": {
        //     "type": "number",
        //     "enum": [
        //     0,
        //     1
        //     ]
        //     },
        //     "key_participa": {
        //     "type": "string",
        //     "enum": [
        //     "check_org"
        //     ]
        //     },
        //     "key_asociacion": {
        //     "type": "string",
        //     "enum": [
        //     "input_org"
        //     ]
        //     },
        //     "asociacion": {
        //     "type": "string"
        //     }
        //     },
        //     "if": {
        //     "properties": {
        //     "participa": {
        //     "enum": [
        //     1
        //     ]
        //     }
        //     }
        //     },
        //     "then": {
        //     "required": [
        //     "asociacion",
        //     "key_asociacion"
        //     ]
        //     }
        //     },
        //     "actividades_otros": {
        //     "description": "¿Realiza actividades con otras personas con discapacidad?",
        //     "required": [
        //     "key_realiza",
        //     "realiza"
        //     ],
        //     "maxProperties": 4,
        //     "properties": {
        //     "realiza": {
        //     "type": "number",
        //     "enum": [
        //     0,
        //     1
        //     ]
        //     },
        //     "key_realiza": {
        //     "type": "string",
        //     "enum": [
        //     "check_actividades_otros"
        //     ]
        //     },
        //     "key_actividad": {
        //     "type": "string",
        //     "enum": [
        //     "input_actividades_otros"
        //     ]
        //     },
        //     "actividad": {
        //     "type": "string"
        //     }
        //     },
        //     "if": {
        //     "properties": {
        //     "realiza": {
        //     "enum": [
        //     1
        //     ]
        //     }
        //     }
        //     },
        //     "then": {
        //     "required": [
        //     "actividad",
        //     "key_actividad"
        //     ]
        //     }
        //     },
        //     "apoyo_institucion": {
        //     "description": "¿Alguna institución le ha proporcionado apoyo por su situación de discapacidad?",
        //     "required": [
        //     "key_apoya",
        //     "apoyo"
        //     ],
        //     "maxProperties": 6,
        //     "properties": {
        //     "apoyo": {
        //     "type": "number",
        //     "enum": [
        //     0,
        //     1
        //     ]
        //     },
        //     "key_apoya": {
        //     "type": "string",
        //     "enum": [
        //     "check_apoyo_institu"
        //     ]
        //     },
        //     "key_apoyo_institu": {
        //     "type": "string",
        //     "enum": [
        //     "input_apoyo"
        //     ]
        //     },
        //     "apoyo_institu": {
        //     "type": "string"
        //     },
        //     "key_institucion": {
        //     "type": "string",
        //     "enum": [
        //     "input_institucion"
        //     ]
        //     },
        //     "institucion": {
        //     "type": "string"
        //     }
        //     },
        //     "if": {
        //     "properties": {
        //     "apoyo": {
        //     "enum": [
        //     1
        //     ]
        //     }
        //     }
        //     },
        //     "then": {
        //     "required": [
        //     "key_institucion",
        //     "key_apoyo_institu",
        //     "institucion",
        //     "apoyo_institu"
        //     ]
        //     }
        //     }
        //     },
        //     "definitions": {
        //     "possibilities": {
        //     "required": [
        //     "key_actividad",
        //     "actividad",
        //     "key_posibilidad",
        //     "posibilidad",
        //     "key_apoyo",
        //     "tipo_apoyo"
        //     ],
        //     "maxProperties": 8,
        //     "properties": {
        //     "key_otra_actividad": {
        //     "type": "string",
        //     "enum": [
        //     "input_otro1"
        //     ]
        //     },
        //     "key_apoyo": {
        //     "type": "string",
        //     "enum": [
        //     "input_tipo1",
        //     "input_tipo2",
        //     "input_tipo3",
        //     "input_tipo4",
        //     "input_tipo5",
        //     "input_tipo6",
        //     "input_tipo7",
        //     "input_tipo8",
        //     "input_tipo9",
        //     "input_tipo10",
        //     "input_tipo11",
        //     "input_tipo12",
        //     "input_tipo13",
        //     "input_tipo14",
        //     "input_tipo15",
        //     "input_tipo16",
        //     "input_tipo17",
        //     "input_tipo18",
        //     "input_tipo19",
        //     "input_tipo20",
        //     "input_tipo_otro"
        //     ]
        //     },
        //     "key_posibilidad": {
        //     "type": "string",
        //     "enum": [
        //     "input_posib1",
        //     "input_posib2",
        //     "input_posib3",
        //     "input_posib4",
        //     "input_posib5",
        //     "input_posib6",
        //     "input_posib7",
        //     "input_posib8",
        //     "input_posib9",
        //     "input_posib10",
        //     "input_posib11",
        //     "input_posib12",
        //     "input_posib13",
        //     "input_posib14",
        //     "input_posib15",
        //     "input_posib16",
        //     "input_posib17",
        //     "input_posib18",
        //     "input_posib19",
        //     "input_posib20",
        //     "input_posib_otro"
        //     ]
        //     },
        //     "key_actividad": {
        //     "type": "string",
        //     "enum": [
        //     "check_pos1",
        //     "check_pos2",
        //     "check_pos3",
        //     "check_pos4",
        //     "check_pos5",
        //     "check_pos6",
        //     "check_pos7",
        //     "check_pos8",
        //     "check_pos9",
        //     "check_pos10",
        //     "check_pos11",
        //     "check_pos12",
        //     "check_pos13",
        //     "check_pos14",
        //     "check_pos15",
        //     "check_pos16",
        //     "check_pos17",
        //     "check_pos18",
        //     "check_pos19",
        //     "check_pos20",
        //     "check_otra_posibilidad"
        //     ]
        //     },
        //     "posibilidad": {
        //     "type": "string",
        //     "enum": [
        //     "No realiza",
        //     "Sin apoyo",
        //     "Con apoyo"
        //     ]
        //     },
        //     "actividad": {
        //     "type": "string"
            
        //     },
        //     "tipo_apoyo": {
        //     "type": "string"
        //     },
        //     "otra_actividad": {
        //     "type": "string"
        //     }
        //     },
        //     "if": {
        //     "properties": {
        //     "key_actividad": {
        //     "enum": [
        //     "check_otra_posibilidad"
        //     ]
        //     }
        //     }
        //     },
        //     "then": {
        //     "required": [
        //     "key_otra_actividad",
        //     "otra_actividad"
        //     ]
        //     }
        //     },
        //     "factor": {
        //     "required": [
        //     "key_factor",
        //     "escenario"
        //     ],
        //     "maxProperties": 4,
        //     "properties": {
        //     "key_otro_factor": {
        //     "type": "string",
        //     "enum": [
        //     "input_factor2_7",
        //     "input_factor4"
        //     ]
        //     },
        //     "otro_factor": {
        //     "type": "string"
        //     },
        //     "key_factor": {
        //     "type": "string",
        //     "enum": [
        //     "check_factor1",
        //     "check_factor2",
        //     "check_factor2_1",
        //     "check_factor2_2",
        //     "check_factor2_3",
        //     "check_factor2_4",
        //     "check_factor2_5",
        //     "check_factor2_6",
        //     "check_factor2_7",
        //     "check_factor3",
        //     "check_factor4"
        //     ]
        //     },
        //     "escenario": {
        //     "type": "string",
        //     "enum": [
        //     "Condición de discapacidad",
        //     "Características del contexto universitario",
        //     "Ausencia o deficiencia de dispositivos, equipos o ayudas tecnológicas",
        //     "La ausencia o deficiencia de condiciones de accesibilidad a los espacios físicos",
        //     "La ausencia o deficiencia de condiciones de accesibilidad a los materiales impresos y pagina web de la universidad",
        //     "La ausencia o deficiencia de personas que apoyen el desarrollo de las actividades",
        //     "Las actitudes negativas de las personas que no se disponen a apoyar",
        //     "La ausencia o programas o servicios para personas con discapacidad en la Universidad",
        //     "Otros, ¿cuáles?",
        //     "Condición psicoemocional",
        //     "Otra ¿Cuál?"
        //     ]
        //     }
        //     },
        //     "if": {
        //     "properties": {
        //     "key_factor": {
        //     "enum": [
        //     "check_factor4",
        //     "check_factor2_7"
        //     ]
        //     }
        //     }
        //     },
        //     "then": {
        //     "required": [
        //     "key_otro_factor",
        //     "otro_factor"
        //     ]
        //     }
        //     },
        //     "necessities": {
        //     "required": [
        //     "key_situacion",
        //     "situacion",
        //     "key_necesidad",
        //     "necesidad"
        //     ],
        //     "maxProperties": 4,
        //     "properties": {
        //     "key_situacion": {
        //     "type": "string",
        //     "enum": [
        //     "check_nec1",
        //     "check_nec2",
        //     "check_nec3",
        //     "check_nec4",
        //     "check_nec5",
        //     "check_nec6",
        //     "check_nec7",
        //     "check_nec8",
        //     "check_nec9",
        //     "check_nec10",
        //     "check_nec11",
        //     "check_nec12",
        //     "check_nec13",
        //     "check_nec14",
        //     "check_nec15"
        //     ]
        //     },
        //     "key_necesidad": {
        //     "type": "string",
        //     "enum": [
        //     "input_nec1",
        //     "input_nec2",
        //     "input_nec3",
        //     "input_nec4",
        //     "input_nec5",
        //     "input_nec6",
        //     "input_nec7",
        //     "input_nec8",
        //     "input_nec9",
        //     "input_nec10",
        //     "input_nec11",
        //     "input_nec12",
        //     "input_nec13",
        //     "input_nec14",
        //     "input_nec15"
        //     ]
        //     },
        //     "situacion": {
        //     "type": "string",
        //     "enum": [
        //     "Cursos",
        //     "Clases magistrales",
        //     "Laboratorios",
        //     "Continuar secuencias numéricas",
        //     "Talleres",
        //     "Conferencias",
        //     "Prácticas deportivas",
        //     "Actividades de ocio",
        //     "Movilizarse de un lugar a otro",
        //     "Audiciones, conciertos, teatro o exposiciones",
        //     "Citas y actividades en los servicios de salud",
        //     "Asambleas o actividades gremiales",
        //     "El consumo de alimentos en las cafeterías",
        //     "Tramites académicos, financieros o administrativos",
        //     "Otra ¿Cuál?"
        //     ]
        //     },
        //     "necesidad": {
        //     "type": "string"
        //     }
        //     }
        //     },
        //     "conditions": {
        //     "required": [
        //     "key_organo",
        //     "organo",
        //     "key_condicion",
        //     "condicion"
        //     ],
        //     "maxProperties": 4,
        //     "properties": {
        //     "key_organo": {
        //     "type": "string",
        //     "enum": [
        //     "check_cond1",
        //     "check_cond2",
        //     "check_cond3",
        //     "check_cond4",
        //     "check_cond5",
        //     "check_cond6",
        //     "check_cond7",
        //     "check_cond8",
        //     "check_cond9",
        //     "check_cond10",
        //     "check_cond11",
        //     "check_cond12"
        //     ]
        //     },
        //     "key_condicion": {
        //     "type": "string",
        //     "enum": [
        //     "input_cond1",
        //     "input_cond2",
        //     "input_cond3",
        //     "input_cond4",
        //     "input_cond5",
        //     "input_cond6",
        //     "input_cond7",
        //     "input_cond8",
        //     "cinput_cond9",
        //     "input_cond10",
        //     "input_cond11",
        //     "input_cond12"
        //     ]
        //     },
        //     "organo": {
        //     "type": "string",
        //     "enum": [
        //     "Ojos",
        //     "Oídos",
        //     "Cuerdas vocales, labios, lengua, paladar",
        //     "Brazos/manos",
        //     "Piernas",
        //     "Piel",
        //     "Cerebro",
        //     "Sistema nervioso",
        //     "Sistema cardio-respiratorio",
        //     "Sistema genital, urinario, reproductor",
        //     "Sistema digestivo",
        //     "Otro ¿Cuál?"
        //     ]
        //     },
        //     "condicion": {
        //     "type": "string"
        //     }
        //     }
        //     },
        //     "dificults": {
        //     "required": [
        //     "key_funcion",
        //     "funcion",
        //     "dificultad",
        //     "key_dificultad"
        //     ],
        //     "maxProperties": 4,
        //     "properties": {
        //     "key_funcion": {
        //     "type": "string",
        //     "enum": [
        //     "check_func1",
        //     "check_func2",
        //     "check_func3",
        //     "check_func4",
        //     "check_func5",
        //     "check_func6",
        //     "check_func7",
        //     "check_func8",
        //     "check_func9"
        //     ]
        //     },
        //     "key_dificultad": {
        //     "type": "string",
        //     "enum": [
        //     "input_func1",
        //     "input_func2",
        //     "input_func3",
        //     "input_func4",
        //     "input_func5",
        //     "input_func6",
        //     "input_func7",
        //     "input_func8",
        //     "input_func9"
        //     ]
        //     },
        //     "funcion": {
        //     "type": "string",
        //     "enum": [
        //     "Visión",
        //     "Audición",
        //     "Voz y habla",
        //     "Movimiento del cuerpo o de alguna parte del cuerpo",
        //     "Cognición",
        //     "Estado socio-emocional",
        //     "Micción, relaciones sexuales, reproducción",
        //     "Masticación y/o deglución",
        //     "Otra ¿Cuál?"
        //     ]
        //     },
        //     "dificultad": {
        //     "type": "string"
        //     }
        //     }
        //     }
        //     },
        //     "required": [
        //     "tipo_discapacidad",
        //     "diagnostico_discapacidad",
        //     "condicion_adquisicion",
        //     "certificado_invalidez",
        //     "dificultad_permanente_funciones",
        //     "condicion_salud_organos",
        //     "necesidades_situaciones",
        //     "factores_impacto",
        //     "posibilidad_actividades",
        //     "apoyo_principal_cotidiano",
        //     "medio_transporte",
        //     "participa_asociacion",
        //     "actividades_otros",
        //     "apoyo_institucion"
        //     ]
        //     }' );


        //     $DB->update_record('talentospilos_json_schema', $new_schema);    

        // $event = new stdClass();
        // $event->name_event          = 'save_academics_data_sp';
        // $event->description   = "Guardar los datos académicos adicionales de un estudiante.";
        // $event->funcionalidad = 'student_profile';

        // if( !$DB->record_exists('talentospilos_events_to_logs',array('name_event'=> $event->name_event))){
        //     $DB->insert_record('talentospilos_events_to_logs', $event, true);
        // }

        // $event = new stdClass();
        // $event->name_event          = 'edit_academics_data_sp';
        // $event->description   = "Editar los datos académicos adicionales de un estudiante.";
        // $event->funcionalidad = 'student_profile';

        // if( !$DB->record_exists('talentospilos_events_to_logs',array('name_event'=> $event->name_event))){
        //     $DB->insert_record('talentospilos_events_to_logs', $event, true);
        // }

        //*****************************************************************************************//
        // Creación de tablas: Se crean tablas necesarias para el sistema de seguridad             //
        // Versión: 20190510****0                                                                  //
        //*****************************************************************************************//

        // Define table talentospilos_acciones to be created.
        $table = new xmldb_table('talentospilos_acciones');

        // Adding fields to table talentospilos_acciones.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('alias', XMLDB_TYPE_CHAR, '60', null, XMLDB_NOTNULL, null, null);
        $table->add_field('nombre', XMLDB_TYPE_CHAR, '60', null, null, null, null);
        $table->add_field('descripcion', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('id_tipo_accion', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('registra_log', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('eliminado', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('fecha_hora_eliminacion', XMLDB_TYPE_DATETIME, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('id_usuario_eliminador', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('fecha_hora_registro', XMLDB_TYPE_DATETIME, null, null, XMLDB_NOTNULL, null, "now()");

        // Adding keys to table talentospilos_acciones.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table talentospilos_acciones.
        $table->add_index('index_acciones_alias', XMLDB_INDEX_NOTUNIQUE, array('alias'));

        // Conditionally launch create table for talentospilos_acciones.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table talentospilos_tipos_accion to be created.
        $table = new xmldb_table('talentospilos_tipos_accion');

        // Adding fields to table talentospilos_tipos_accion.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('alias', XMLDB_TYPE_CHAR, '60', null, XMLDB_NOTNULL, null, null);
        $table->add_field('descripcion', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table talentospilos_tipos_accion.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table talentospilos_tipos_accion.
        $table->add_index('unique_index_tipo_accion_alias', XMLDB_INDEX_UNIQUE, array('alias'));

        // Conditionally launch create table for talentospilos_tipos_accion.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table talentospilos_log_acciones to be created.
        $table = new xmldb_table('talentospilos_log_acciones');

        // Adding fields to table talentospilos_log_acciones.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('id_usuario', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('id_accion', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('parametros', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('salida', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('fecha_hora_registro', XMLDB_TYPE_DATETIME, null, null, XMLDB_NOTNULL, null, "now()");

        // Adding keys to table talentospilos_log_acciones.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table talentospilos_log_acciones.
        $table->add_index('index_log_acciones_id_usuario', XMLDB_INDEX_NOTUNIQUE, array('id_usuario'));
        $table->add_index('index_log_acciones_id_accion', XMLDB_INDEX_NOTUNIQUE, array('id_accion'));

        // Conditionally launch create table for talentospilos_log_acciones.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table talentospilos_roles to be created.
        $table = new xmldb_table('talentospilos_roles');

        // Adding fields to table talentospilos_roles.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('alias', XMLDB_TYPE_CHAR, '60', null, XMLDB_NOTNULL, null, null);
        $table->add_field('nombre', XMLDB_TYPE_CHAR, '60', null, null, null, null);
        $table->add_field('descripcion', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('id_rol_padre', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '-1');
        $table->add_field('eliminado', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('fecha_hora_eliminacion', XMLDB_TYPE_DATETIME, null, null, null, null, null);
        $table->add_field('id_usuario_eliminador', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('fecha_hora_registro', XMLDB_TYPE_DATETIME, null, null, XMLDB_NOTNULL, null, "now()");

        // Adding keys to table talentospilos_roles.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table talentospilos_roles.
        $table->add_index('index_roles_alias', XMLDB_INDEX_NOTUNIQUE, array('alias'));

        // Conditionally launch create table for talentospilos_roles.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }


        upgrade_block_savepoint(true, 2019040212300, 'ases');
        return $result;

    }
}


?>
