<?php 

require_once(dirname(__FILE__).'/../../../config.php');

function xmldb_block_ases_upgrade($oldversion = 0) {
    global $DB;
    $dbman = $DB->get_manager();

    $result = true;

    if ($oldversion < 2018010911186) {

        /*
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
    */

    
 //tp_formularios
 // Define table talentospilos_df_formularios to be dropped.
 $table = new xmldb_table('talentospilos_df_formularios');

 // Conditionally launch drop table for talentospilos_df_formularios.
 if ($dbman->table_exists($table)) {
     $dbman->drop_table($table);
 }
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
 
 //tp_tipo_campo
 // Define table talentospilos_df_tipo_campo to be dropped.
 $table = new xmldb_table('talentospilos_df_tipo_campo');

 // Conditionally launch drop table for talentospilos_df_tipo_campo.
 if ($dbman->table_exists($table)) {
     $dbman->drop_table($table);
 }

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

 //tp_preguntas
  // Define table talentospilos_df_preguntas to be dropped.
  $table = new xmldb_table('talentospilos_df_preguntas');

  // Conditionally launch drop table for talentospilos_df_preguntas.
  if ($dbman->table_exists($table)) {
      $dbman->drop_table($table);
  }
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

 //tp_formulario_preguntas
  // Define table talentospilos_df_form_preg to be dropped.
  $table = new xmldb_table('talentospilos_df_form_preg');

  // Conditionally launch drop table for talentospilos_df_form_preg.
  if ($dbman->table_exists($table)) {
      $dbman->drop_table($table);
  }
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

 //tp_reglas
 // Define table talentospilos_df_reglas to be dropped.
 $table = new xmldb_table('talentospilos_df_reglas');

 // Conditionally launch drop table for talentospilos_df_reglas.
 if ($dbman->table_exists($table)) {
     $dbman->drop_table($table);
 }
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

 //tp_respuestas
 // Define table talentospilos_df_respuestas to be dropped.
 $table = new xmldb_table('talentospilos_df_respuestas');

 // Conditionally launch drop table for talentospilos_df_respuestas.
 if ($dbman->table_exists($table)) {
     $dbman->drop_table($table);
 }
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

 //tp_formulario_respuestas
  // Define table talentospilos_df_form_resp to be dropped.
  $table = new xmldb_table('talentospilos_df_form_resp');

  // Conditionally launch drop table for talentospilos_df_form_resp.
  if ($dbman->table_exists($table)) {
      $dbman->drop_table($table);
  }
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

 //tp_formulario_soluciones
  // Define table talentospilos_df_form_solu to be dropped.
  $table = new xmldb_table('talentospilos_df_form_solu');

  // Conditionally launch drop table for talentospilos_df_form_solu.
  if ($dbman->table_exists($table)) {
      $dbman->drop_table($table);
  }
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

 //tp_reglas_formulario_preguntas
 // Define table talentospilos_df_reg_form_pr to be dropped.
 $table = new xmldb_table('talentospilos_df_reg_form_pr');

 // Conditionally launch drop table for talentospilos_df_reg_form_pr.
 if ($dbman->table_exists($table)) {
     $dbman->drop_table($table);
 }
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

 //tp_permisos_formulario_preguntas
 // Define table talentospilos_df_per_form_pr to be dropped.
 $table = new xmldb_table('talentospilos_df_per_form_pr');

 // Conditionally launch drop table for talentospilos_df_per_form_pr.
 if ($dbman->table_exists($table)) {
     $dbman->drop_table($table);
 }
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

 //tp_disparadores_formulario_diligenciado
 // Define table talentospilos_df_disp_fordil to be dropped.
 $table = new xmldb_table('talentospilos_df_disp_fordil');

 // Conditionally launch drop table for talentospilos_df_disp_fordil.
 if ($dbman->table_exists($table)) {
     $dbman->drop_table($table);
 }

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
    upgrade_block_savepoint(true, 2018010911186, 'ases');
   
    return $result;
    }
}
?>