<?php
  require_once(dirname(__FILE__). '/../../../config.php');
    
  global $DB;
  $students = $DB->get_records_sql("SELECT id FROM {talentospilos_usuario} WHERE NOT IN (SELECT id_estudiante FROM {talentospilos_est_estadoases})");
  $state = $DB->get_record_sql("SELECT id FROM {talentospilos_estados_ases} WHERE nombre = 'ACTIVO/SEGUIMIENTO'")->id;
  foreach($students as $id){
      $record = new stdClass;
      $record->id_estudiante = $id->id;
      $record->id_estado_ases = $state;
      $record->fecha = 1506876433; 
      echo $DB->insert_record('talentospilos_est_estadoases',$record,false);
  }

