<?php 
require_once(dirname(__FILE__).'/../../../config.php');
function xmldb_block_ases_upgrade($oldversion = 0) {
    global $DB;
    $dbman = $DB->get_manager();
    $result = true;
    if ($oldversion < 2018012217129) {
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
    $field = new xmldb_field('tracking_status', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'program_status');
    // Conditionally launch add field id_instancia.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
    // ************************************************************************************************************
    // Actualización:
    // Se modifica el nombre del campo estado_seguimiento en la tabla {talentospilos_user_extended}. Se pasa de estado_seguimiento a tracking_status
    // Versión en la que se incluye: 2018011911069
    // ************************************************************************************************************
    // Rename field id_estado_icetex on table talentospilos_est_est_icetex to tracking_status.
    //$table = new xmldb_table('talentospilos_user_extended');
    //$field = new xmldb_field('estado_seguimiento', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'program_status');
    // Launch rename field id_estado_icetex.
    //$dbman->rename_field($table, $field, 'tracking_status');
    // ************************************************************************************************************
    // Actualización:
    // Se crea la tabla {talentospilos_instancia_cohorte}. Con los campos
    //          id --> Autoincremental
    //          id_cohorte --> Identificador de la cohorte 
    //          id_instancia --> Identificador de la instancia relacionada a la cohorte
    // Versión en la que se incluye: 2018011911069
    // ************************************************************************************************************
    
    // Define table talentospilos_inst_cohorte to be created.
    $table = new xmldb_table('talentospilos_inst_cohorte');
    // Adding fields to table talentospilos_inst_cohorte.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('id_cohorte', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    $table->add_field('id_instancia', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    // Adding keys to table talentospilos_inst_cohorte.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    // Conditionally launch create table for talentospilos_inst_cohorte.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
    // ************************************************************************************************************
    // Actualización:
    // Se crea la tabla {talentospilos_history_academ}. Con los campos
    //          id --> Autoincremental
    //          id_estudiante --> Identificador del estudiante ASES
    //          id_semestre --> Identificador del semestre o periodo académico. Apunta a {talentospilos_semestre}
    //          id_programa --> Identificador del programa académico. Apunta a {talentospilos_programa}
    //          promedio_semestre --> Promedio semestral
    //          promedio_acumulado --> Promedio acumulado 
    //          json_materias --> Materias relacionadas al período académico
    // Versión en la que se incluye: 2018011911069
    // ************************************************************************************************************
    // Define table talentospilos_history_academ to be created.
    $table = new xmldb_table('talentospilos_history_academ');
    // Adding fields to table talentospilos_history_academ.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    // Adding keys to table talentospilos_history_academ.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    // Conditionally launch create table for talentospilos_history_academ.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
    // Define field id_estudiante to be added to talentospilos_history_academ.
    $table = new xmldb_table('talentospilos_history_academ');
    $field = new xmldb_field('id_estudiante', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');
    // Conditionally launch add field id_estudiante.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
    // Define field id_semestre to be added to talentospilos_history_academ.
    $table = new xmldb_table('talentospilos_history_academ');
    $field = new xmldb_field('id_semestre', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id_estudiante');
    // Conditionally launch add field id_semestre.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
    // Define field id_programa to be added to talentospilos_history_academ.
    $table = new xmldb_table('talentospilos_history_academ');
    $field = new xmldb_field('id_programa', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id_semestre');
    // Conditionally launch add field id_programa.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
    // Define field promedio_semestre to be added to talentospilos_history_academ.
    $table = new xmldb_table('talentospilos_history_academ');
    $field = new xmldb_field('promedio_semestre', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'id_programa');
    // Conditionally launch add field promedio_semestre.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
    // Define field promedio_acumulado to be added to talentospilos_history_academ.
    $table = new xmldb_table('talentospilos_history_academ');
    $field = new xmldb_field('promedio_acumulado', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'promedio_semestre');
    // Conditionally launch add field promedio_acumulado.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
    // Define field json_materias to be added to talentospilos_history_academ.
    $table = new xmldb_table('talentospilos_history_academ');
    $field = new xmldb_field('json_materias', XMLDB_TYPE_TEXT, null, null, null, null, null, 'promedio_acumulado');
    // Conditionally launch add field json_materias.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
    // Define key fk_estudiante (foreign) to be added to talentospilos_history_academ.
    $table = new xmldb_table('talentospilos_history_academ');
    $key = new xmldb_key('fk_estudiante', XMLDB_KEY_FOREIGN, array('id_estudiante'), 'talentospilos_usuario', array('id'));
    // Launch add key fk_estudiante.
    $dbman->add_key($table, $key);
    // Define key fk_semestre (foreign) to be added to talentospilos_history_academ.
    $table = new xmldb_table('talentospilos_history_academ');
    $key = new xmldb_key('fk_semestre', XMLDB_KEY_FOREIGN, array('id_semestre'), 'talentospilos_semestre', array('id'));
    // Launch add key fk_semestre.
    $dbman->add_key($table, $key);
    // Define key fk_programa (foreign) to be added to talentospilos_history_academ.
    $table = new xmldb_table('talentospilos_history_academ');
    $key = new xmldb_key('fk_programa', XMLDB_KEY_FOREIGN, array('id_programa'), 'talentospilos_programa', array('id'));
    // Launch add key fk_programa.
    $dbman->add_key($table, $key);
    // Define key unique_key (unique) to be added to talentospilos_history_academ.
    $table = new xmldb_table('talentospilos_history_academ');
    $key = new xmldb_key('unique_key', XMLDB_KEY_UNIQUE, array('id_estudiante', 'id_semestre', 'id_programa'));
    // Launch add key unique_key.
    $dbman->add_key($table, $key);
    // ************************************************************************************************************
    // Actualización:
    // Se crea la tabla {talentospilos_history_cancel}. Con los campos
    //          id --> Autoincremental
    //          id_history --> Identificador del histórico académico
    //          fecha_cancelacion --> Fecha en la que se realiza la cancelación del semestre
    // Versión en la que se incluye: 2018011911069
    // ************************************************************************************************************
    // Define table talentospilos_history_cancel to be created.
    $table = new xmldb_table('talentospilos_history_cancel');
    // Adding fields to table talentospilos_history_cancel.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('id_history', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    $table->add_field('fecha_cancelacion', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    // Adding keys to table talentospilos_history_cancel.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_key('fk_history', XMLDB_KEY_FOREIGN, array('id_history'), 'talentospilos_history_academ', array('id'));
    // Conditionally launch create table for talentospilos_history_cancel.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
    // Define field id_history to be added to talentospilos_history_cancel.
    $table = new xmldb_table('talentospilos_history_cancel');
    $field = new xmldb_field('id_history', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null, 'id');
    // Conditionally launch add field id_history.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
    // Define field fecha_cancelacion to be added to talentospilos_history_cancel.
    $table = new xmldb_table('talentospilos_history_cancel');
    $field = new xmldb_field('fecha_cancelacion', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null, 'id_history');
    // Conditionally launch add field fecha_cancelacion.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
    // Define key fk_history (foreign) to be added to talentospilos_history_cancel.
    $table = new xmldb_table('talentospilos_history_cancel');
    $key = new xmldb_key('fk_history', XMLDB_KEY_FOREIGN, array('id_history'), 'talentospilos_history_academ', array('id'));
    // Launch add key fk_history.
    $dbman->add_key($table, $key);
    // ************************************************************************************************************
    // Actualización:
    // Se crea la tabla {talentospilos_history_bajos}. Con los campos
    //          id --> Autoincremental
    //          id_history --> Identificador del histórico académico
    //          numero_bajo --> Cantidad de bajos registrados
    // Versión en la que se incluye: 2018011911069
    // ************************************************************************************************************
    // Define table talentospilos_history_bajos to be created.
    $table = new xmldb_table('talentospilos_history_bajos');
    // Adding fields to table talentospilos_history_bajos.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    // Adding keys to table talentospilos_history_bajos.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    // Conditionally launch create table for talentospilos_history_bajos.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
    // Define field id_history to be added to talentospilos_history_bajos.
    $table = new xmldb_table('talentospilos_history_bajos');
    $field = new xmldb_field('id_history', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null, 'id');
    // Conditionally launch add field id_history.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
    // Define field numero_bajo to be added to talentospilos_history_bajos.
    $table = new xmldb_table('talentospilos_history_bajos');
    $field = new xmldb_field('numero_bajo', XMLDB_TYPE_INTEGER, '3', null, null, null, null, 'id_history');
    // Conditionally launch add field numero_bajo.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
    // Define key fk_history (foreign) to be added to talentospilos_history_bajos.
    $table = new xmldb_table('talentospilos_history_bajos');
    $key = new xmldb_key('fk_history', XMLDB_KEY_FOREIGN, array('id_history'), 'talentospilos_history_academ', array('id'));
    // Launch add key fk_history.
    $dbman->add_key($table, $key);
    // ************************************************************************************************************
    // Actualización:
    // Se crea la tabla {talentospilos_history_estim}. Con los campos
    //          id --> Autoincremental
    //          id_history --> Identificador del histórico académico
    //          puesto_ocupado --> Puesto ocupado por el estudiante en el semestre  
    // Versión en la que se incluye: 2018011911069
    // ************************************************************************************************************
    // Define table talentospilos_history_estim to be created.
    $table = new xmldb_table('talentospilos_history_estim');
    // Adding fields to table talentospilos_history_estim.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    // Adding keys to table talentospilos_history_estim.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    // Conditionally launch create table for talentospilos_history_estim.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
    // Define field id_history to be added to talentospilos_history_estim.
    $table = new xmldb_table('talentospilos_history_estim');
    $field = new xmldb_field('id_history', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null, 'id');
    // Conditionally launch add field id_history.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
    // Define field puesto_ocupado to be added to talentospilos_history_estim.
    $table = new xmldb_table('talentospilos_history_estim');
    $field = new xmldb_field('puesto_ocupado', XMLDB_TYPE_INTEGER, '3', null, null, null, null, 'id_history');
    // Conditionally launch add field puesto_ocupado.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
    // Define key fk_history (foreign) to be added to talentospilos_history_estim.
    $table = new xmldb_table('talentospilos_history_estim');
    $key = new xmldb_key('fk_history', XMLDB_KEY_FOREIGN, array('id_history'), 'talentospilos_history_academ', array('id'));
    // Launch add key fk_history.
    $dbman->add_key($table, $key);
    // ************************************************************************************************************
    // Actualización:
    // Se crea la tabla {talentospilos_res_estudiante}. Con los campos
    //          id --> Autoincremental
    //          monto_estudiante --> Identificador del histórico académico
    //          id_semestre --> Identificador del semestre académico
    //          id_estudiante --> Identificador asociado al estudiante ASES
    //          id_resolucion --> Identificador de la resolución asociada al estudiante
    // Versión en la que se incluye: 2018011911069
    // ************************************************************************************************************
    // Define table talentospilos_res_estudiante to be dropped.
    $table = new xmldb_table('talentospilos_res_estudiante');
    // Conditionally launch drop table for talentospilos_res_estudiante.
    if ($dbman->table_exists($table)) {
        $dbman->drop_table($table);
    }
    // Define table talentospilos_res_estudiante to be created.
    $table = new xmldb_table('talentospilos_res_estudiante');
    // Adding fields to table talentospilos_res_estudiante.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    // Adding keys to table talentospilos_res_estudiante.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    // Conditionally launch create table for talentospilos_res_estudiante.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
    // Define field monto_estudiante to be added to talentospilos_res_estudiante.
    $table = new xmldb_table('talentospilos_res_estudiante');
    $field = new xmldb_field('monto_estudiante', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'id');
    // Conditionally launch add field monto_estudiante.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
    // Define field id_semestre to be added to talentospilos_res_estudiante.
    $table = new xmldb_table('talentospilos_res_estudiante');
    $field = new xmldb_field('id_semestre', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'monto_estudiante');
    // Conditionally launch add field id_semestre.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
    // Define field id_estudiante to be added to talentospilos_res_estudiante.
    $table = new xmldb_table('talentospilos_res_estudiante');
    $field = new xmldb_field('id_estudiante', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id_semestre');
    // Conditionally launch add field id_estudiante.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
    // Define field id_resolucion to be added to talentospilos_res_estudiante.
    $table = new xmldb_table('talentospilos_res_estudiante');
    $field = new xmldb_field('id_resolucion', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id_estudiante');
    // Conditionally launch add field id_resolucion.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
    // Define key foreign_key_semestre (foreign) to be added to talentospilos_res_estudiante.
    $table = new xmldb_table('talentospilos_res_estudiante');
    $key = new xmldb_key('foreign_key_semestre', XMLDB_KEY_FOREIGN, array('id_semestre'), 'talentospilos_semestre', array('id'));
    // Launch add key foreign_key_semestre.
    $dbman->add_key($table, $key);
    // Define key foreign_key_estudiante (foreign) to be added to talentospilos_res_estudiante.
    $table = new xmldb_table('talentospilos_res_estudiante');
    $key = new xmldb_key('foreign_key_estudiante', XMLDB_KEY_FOREIGN, array('id_estudiante'), 'talentospilos_usuario', array('id'));
    // Launch add key foreign_key_estudiante.
    $dbman->add_key($table, $key);
    // Define key foreign_key_res_icetex (foreign) to be added to talentospilos_res_estudiante.
    $table = new xmldb_table('talentospilos_res_estudiante');
    $key = new xmldb_key('foreign_key_res_icetex', XMLDB_KEY_FOREIGN, array('id_resolucion'), 'talentospilos_res_icetex', array('id'));
    // Launch add key foreign_key_res_icetex.
    $dbman->add_key($table, $key);
    // ************************************************************************************************************
    // Actualización:
    // Se crea la tabla {talentospilos_res_icetex}. Con los campos
    //          id --> Autoincremental
    //          codigo_resolucion --> Identificador del histórico académico
    //          monto_total --> Identificador del semestre académico
    //          fecha_resolucion --> Identificador asociado al estudiante ASES
    // Versión en la que se incluye: 2018011911069
    // ************************************************************************************************************
    // Define table talentospilos_res_icetex to be dropped.
    $table = new xmldb_table('talentospilos_res_icetex');
    // Conditionally launch drop table for talentospilos_res_icetex.
    if ($dbman->table_exists($table)) {
        $dbman->drop_table($table);
    }
    // Define table talentospilos_res_icetex to be created.
    $table = new xmldb_table('talentospilos_res_icetex');
    // Adding fields to table talentospilos_res_icetex.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    // Adding keys to table talentospilos_res_icetex.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    // Conditionally launch create table for talentospilos_res_icetex.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
    // Define field codigo_resolucion to be added to talentospilos_res_icetex.
    $table = new xmldb_table('talentospilos_res_icetex');
    $field = new xmldb_field('codigo_resolucion', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'id');
    // Conditionally launch add field codigo_resolucion.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
    // Define field monto_total to be added to talentospilos_res_icetex.
    $table = new xmldb_table('talentospilos_res_icetex');
    $field = new xmldb_field('monto_total', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null, 'codigo_resolucion');
    // Conditionally launch add field monto_total.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
    // Define field fecha_resolucion to be added to talentospilos_res_icetex.
    $table = new xmldb_table('talentospilos_res_icetex');
    $field = new xmldb_field('fecha_resolucion', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null, 'monto_total');
    // Conditionally launch add field fecha_resolucion.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
    // Define key unique_cod_res (unique) to be added to talentospilos_res_icetex.
    $table = new xmldb_table('talentospilos_res_icetex');
    $key = new xmldb_key('unique_cod_res', XMLDB_KEY_UNIQUE, array('codigo_resolucion'));
    // Launch add key unique_cod_res.
    $dbman->add_key($table, $key);
    // ************************************************************************************************************
    // Actualización:
    // Se añade el campo cod_instancia a la tabla {talentospilos_instancia}
    // Versión en la que se incluye: 2018011914179
    // ************************************************************************************************************
    // Define field cod_instancia to be added to talentospilos_instancia.
    $table = new xmldb_table('talentospilos_instancia');
    $field = new xmldb_field('cod_instancia', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'change', 'seg_socioeducativo');
    // Conditionally launch add field cod_instancia.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
    //*************************************************************************************************************
    // ************************************************************************************************************
    // ************************************************************************************************************
    // Actualización:
    // Se añade la tabla talentospilos_df_formularios
    // Versión en la que se incluye: Pendiente
    // ************************************************************************************************************
    
    // Define table talentospilos_df_formularios to be created.
    $table = new xmldb_table('talentospilos_df_formularios');
    // Adding fields to table talentospilos_df_formularios.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('nombre', XMLDB_TYPE_CHAR, '140', null, XMLDB_NOTNULL, null, null);
    $table->add_field('descripcion', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('method', XMLDB_TYPE_CHAR, '140', null, null, null, null);
    $table->add_field('action', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    $table->add_field('enctype', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('fecha_hora_registro', XMLDB_TYPE_DATETIME, null, null, null, null, "now()");
    // Adding keys to table talentospilos_df_formularios.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    // Conditionally launch create table for talentospilos_df_formularios.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
    //end tp_formularios
    
    // ************************************************************************************************************
    // Actualización:
    // Se añade la tabla talentospilos_tipo_campo
    // Versión en la que se incluye: Pendiente
    // ************************************************************************************************************
    // Define table talentospilos_df_tipo_campo to be created.
    $table = new xmldb_table('talentospilos_df_tipo_campo');
    // Adding fields to table talentospilos_df_tipo_campo.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('campo', XMLDB_TYPE_CHAR, '140', null, null, null, null);
    $table->add_field('fecha_hora_registro', XMLDB_TYPE_DATETIME, null, null, null, null, "now()");
    // Adding keys to table talentospilos_df_tipo_campo.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    // Conditionally launch create table for talentospilos_df_tipo_campo.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
    //end tp_tipo_campo
    // ************************************************************************************************************
    // Actualización:
    // Se añade la tabla talentospilos_preguntas
    // Versión en la que se incluye: Pendiente
    // ************************************************************************************************************
    
    // Define table talentospilos_df_preguntas to be created.
    $table = new xmldb_table('talentospilos_df_preguntas');
    // Adding fields to table talentospilos_df_preguntas.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('tipo_campo', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    $table->add_field('opciones_campo', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('atributos_campo', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('enunciado', XMLDB_TYPE_CHAR, '200', null, XMLDB_NOTNULL, null, null);
    $table->add_field('fecha_hora_registro', XMLDB_TYPE_DATETIME, null, null, XMLDB_NOTNULL, null, "now()");
    // Adding keys to table talentospilos_df_preguntas.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_key('fk_preguntas_id_tipo_pregunta', XMLDB_KEY_FOREIGN, array('tipo_campo'), 'talentospilos_df_tipo_campo', array('id'));
    // Conditionally launch create table for talentospilos_df_preguntas.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
    //end tp_preguntas
    // ************************************************************************************************************
    // Actualización:
    // Se añade la tabla talentospilos_df_form_preg
    // Versión en la que se incluye: Pendiente
    // ************************************************************************************************************
    // Define table talentospilos_df_form_preg to be created.
    $table = new xmldb_table('talentospilos_df_form_preg');
    // Adding fields to table talentospilos_df_form_preg.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('id_formulario', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    $table->add_field('id_pregunta', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    $table->add_field('posicion', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    $table->add_field('fecha_hora_registro', XMLDB_TYPE_DATETIME, null, null, XMLDB_NOTNULL, null, "now()");
    // Adding keys to table talentospilos_df_form_preg.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    // Conditionally launch create table for talentospilos_df_form_preg.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
    //end tp_formulario_preguntas
    // ************************************************************************************************************
    // Actualización:
    // Se añade la tabla talentospilos_df_reglas
    // Versión en la que se incluye: Pendiente
    // ************************************************************************************************************
    
    // Define table talentospilos_df_reglas to be created.
    $table = new xmldb_table('talentospilos_df_reglas');
    // Adding fields to table talentospilos_df_reglas.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('regla', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
    // Adding keys to table talentospilos_df_reglas.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    // Conditionally launch create table for talentospilos_df_reglas.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
    //end tp_reglas
    // ************************************************************************************************************
    // Actualización:
    // Se añade la tabla talentospilos_df_respuestas
    // Versión en la que se incluye: Pendiente
    // ************************************************************************************************************
    // Define table talentospilos_df_respuestas to be created.
    $table = new xmldb_table('talentospilos_df_respuestas');
    // Adding fields to table talentospilos_df_respuestas.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('id_pregunta', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    $table->add_field('respuesta', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    $table->add_field('fecha_hora_registro', XMLDB_TYPE_DATETIME, null, null, null, null, "now()");
    // Adding keys to table talentospilos_df_respuestas.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_key('fk_respuestas_id_pregunta', XMLDB_KEY_FOREIGN, array('id_pregunta'), 'talentospilos_df_preguntas', array('id'));
    // Conditionally launch create table for talentospilos_df_respuestas.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
    //end tp_respuestas
    
    // ************************************************************************************************************
    // Actualización:
    // Se añade la tabla talentospilos_df_form_resp
    // Versión en la que se incluye: Pendiente
    // ************************************************************************************************************
    
    // Define table talentospilos_df_form_resp to be created.
    $table = new xmldb_table('talentospilos_df_form_resp');
    // Adding fields to table talentospilos_df_form_resp.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('id_formulario', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    $table->add_field('id_monitor', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    $table->add_field('id_estudiante', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    $table->add_field('fecha_hora_registro', XMLDB_TYPE_DATETIME, null, null, XMLDB_NOTNULL, null, "now()");
    // Adding keys to table talentospilos_df_form_resp.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_key('fk_form_resp_id_formulario', XMLDB_KEY_FOREIGN, array('id_formulario'), 'talentospilos_df_formularios', array('id'));
    // Conditionally launch create table for talentospilos_df_form_resp.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
    //end tp_formulario_respuestas
    
    // ************************************************************************************************************
    // Actualización:
    // Se añade la tabla talentospilos_df_form_solu
    // Versión en la que se incluye: Pendiente
    // ************************************************************************************************************
 
    // Define table talentospilos_df_form_solu to be created.
    $table = new xmldb_table('talentospilos_df_form_solu');
    // Adding fields to table talentospilos_df_form_solu.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('id_formulario_respuestas', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    $table->add_field('id_respuesta', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    $table->add_field('fecha_hora_registro', XMLDB_TYPE_DATETIME, null, null, XMLDB_NOTNULL, null, "now()");
    // Adding keys to table talentospilos_df_form_solu.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_key('fk_form_solu_id_form_resp', XMLDB_KEY_FOREIGN, array('id_formulario_respuestas'), 'talentospilos_df_form_resp', array('id'));
    $table->add_key('fk_form_solu_id_resp', XMLDB_KEY_FOREIGN, array('id_respuesta'), 'talentospilos_df_respuestas', array('id'));
    // Conditionally launch create table for talentospilos_df_form_solu.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
    //end tp_soluciones
    // ************************************************************************************************************
    // Actualización:
    // Se añade la tabla talentospilos_df_reg_form_pr
    // Versión en la que se incluye: Pendiente
    // ************************************************************************************************************
    // Define table talentospilos_df_reg_form_pr to be created.
    $table = new xmldb_table('talentospilos_df_reg_form_pr');
    // Adding fields to table talentospilos_df_reg_form_pr.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('id_formulario', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    $table->add_field('id_regla', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    $table->add_field('id_form_pregunta_a', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    $table->add_field('id_form_pregunta_b', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    // Adding keys to table talentospilos_df_reg_form_pr.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_key('fk_reg_form_pr_formularios', XMLDB_KEY_FOREIGN, array('id_formulario'), 'talentospilos_df_formularios', array('id'));
    $table->add_key('fk_reg_form_pr_id_reglas', XMLDB_KEY_FOREIGN, array('id_regla'), 'talentospilos_df_reglas', array('id'));
    $table->add_key('fk_reg_form_pr_id_form_pregunta_a', XMLDB_KEY_FOREIGN, array('id_form_pregunta_a'), 'talentospilos_df_form_preg', array('id'));
    $table->add_key('fk_reg_form_pr_id_form_pregunta_b', XMLDB_KEY_FOREIGN, array('id_form_pregunta_b'), 'talentospilos_df_form_preg', array('id'));
    // Conditionally launch create table for talentospilos_df_reg_form_pr.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
    //end tp_reglas_form_preguntas
    // ************************************************************************************************************
    // Actualización:
    // Se añade la tabla talentospilos_df_per_form_pr
    // Versión en la que se incluye: Pendiente
    // ************************************************************************************************************
    // Define table talentospilos_df_per_form_pr to be created.
    $table = new xmldb_table('talentospilos_df_per_form_pr');
    // Adding fields to table talentospilos_df_per_form_pr.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('id_formulario_pregunta', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    $table->add_field('permisos', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    // Adding keys to table talentospilos_df_per_form_pr.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_key('fk_per_form_pr_id_form_preg', XMLDB_KEY_FOREIGN, array('id_formulario_pregunta'), 'talentospilos_df_form_preg', array('id'));
    // Conditionally launch create table for talentospilos_df_per_form_pr.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
    //end tp_permisos_formulario_preguntas
    // ************************************************************************************************************
    // Actualización:
    // Se añade la tabla talentospilos_df_disp_fordil
    // Versión en la que se incluye: Pendiente
    // ************************************************************************************************************
    // Define table talentospilos_df_disp_fordil to be created.
    $table = new xmldb_table('talentospilos_df_disp_fordil');
    // Adding fields to table talentospilos_df_disp_fordil.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('id_formulario', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    $table->add_field('disparadores', XMLDB_TYPE_TEXT, null, null, null, null, null);
    // Adding keys to table talentospilos_df_disp_fordil.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_key('fk_disparadores_id_formulario', XMLDB_KEY_FOREIGN_UNIQUE, array('id_formulario'), 'talentospilos_df_formularios', array('id'));
    // Conditionally launch create table for talentospilos_df_disp_fordil.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
    //end tp_disparadores_formulario_diligenciado
    // ************************************************************************************************************
    // Actualización:
    // Se insertan registros para los tipos de campo 
    // Versión en la que se incluye: Pendiente
    // ************************************************************************************************************
    
    //Registro de los tipos de campos  
    $campo_textfield = new stdClass();
    $campo_textfield->campo                = 'TEXTFIELD';
    $campo_textfield->fecha_hora_registro  = 'now()';
    $campo_textarea = new stdClass();
    $campo_textarea->campo                 = 'TEXTAREA';
    $campo_textarea->fecha_hora_registro   = 'now()';
    $campo_date = new stdClass();
    $campo_date->campo                     = 'DATE';
    $campo_date->fecha_hora_registro       = 'now()';
    $campo_time = new stdClass();
    $campo_time->campo                     = 'TIME';
    $campo_time->fecha_hora_registro       = 'now()';
    $campo_radio = new stdClass();
    $campo_radio->campo                    = 'RADIOBUTTON';
    $campo_radio->fecha_hora_registro      = 'now()';
    $campo_check = new stdClass();
    $campo_check->campo                    = 'CHECKBOX';
    $campo_check->fecha_hora_registro      = 'now()';
    $records = array();
    array_push($records, $campo_textfield);
    array_push($records, $campo_textarea);
    array_push($records, $campo_date);
    array_push($records, $campo_time);
    array_push($records, $campo_radio);
    array_push($records, $campo_check);
    $DB->insert_records('talentospilos_df_tipo_campo', $records);
    // ************************************************************************************************************
    // Actualización:
    // Se insertan registros para las reglas de los formularios
    // Versión en la que se incluye: Pendiente
    // ************************************************************************************************************
    $regla_mayor_que = new stdClass();
    $regla_mayor_que->regla    = '>';
    $regla_menor_que = new stdClass();
    $regla_menor_que->regla    = '<';
    $regla_igual = new stdClass();
    $regla_igual->regla        = 'EQUAL';
    $regla_diferente = new stdClass();
    $regla_diferente->regla    = 'DIFFERENT';
    $regla_depende = new stdClass();
    $regla_depende->regla      = 'DEPENDS';
    $regla_enlazado = new stdClass();
    $regla_enlazado->regla     = 'BOUND';
    $records = array();
    array_push($records, $regla_mayor_que);
    array_push($records, $regla_menor_que);
    array_push($records, $regla_igual);
    array_push($records, $regla_diferente);
    array_push($records, $regla_depende);
    array_push($records, $regla_enlazado);
    $DB->insert_records('talentospilos_df_reglas', $records);
    // Ases savepoint reached.
    upgrade_block_savepoint(true, 2018012217129, 'ases');
   
    return $result;
    }
}
?>