<?php

require_once(dirname(__FILE__).'/../../../config.php');


function xmldb_block_ases_install_recovery(){
    xmldb_block_ases_install();
}

/**
 * Función que se ejecuta al instalar el módulo Ases
 * 
 * @see xmldb_block_ases_install()
 * @return void
 */
function xmldb_block_ases_install()
{
	global $DB;
	
	$roles_array = array('administrativo', 'reportes', 'profesional_ps', 'monitor_ps', 'estudiante_t', 'sistemas', 'practicante_ps');
	$descripcion_roles_array = array('Actualizar ficha', 'Rol general para directivos y demas personas que tengan permiso de lectura', 'Profesional Psicoeducativo', 'Monitor Psicoeducativo con estudiantes a cargo', 'Estudiante talentos pilos', 'Rol desarrollador','Practicante Psicoeducativo con monitores a cargo');
	$funcionalidades_array = array('carga_csv','reporte_general','f_general','f_academica','f_asistencia','f_socioeducativa_pro','f_socioeducativa_mon', 'gestion_roles');
	$descripcion_funcionalidades_array = array('Carga de información a tablas de la base de datos','Reporte general de estudiantes pilos','Ficha general de un estudiante pilos','Ficha académica de un estudiante pilos','Ficha asistencia de un estudiante pilos','Ficha psicosocial de un estudiante pilos desde un profesional', 'Ficha psicosocial de un estudiante pilos desde un monitor','Gestiona los roles de los usuarios');
	$permisos_array = array('C','R','U','D');
	$descripcion_permisos_array = array('Crear','Leer','Actualizar','Borrar');
	
    for($i = 0; $i < count($roles_array); $i++){
        $record = new stdClass; 
        $record->nombre_rol = $roles_array[$i];
        $record->descripcion = $descripcion_roles_array[$i];
        $DB->insert_record('talentospilos_rol', $record, false);
    }
    
    for($i = 0; $i < count($funcionalidades_array); $i++){
        $record = new stdClass; 
        $record->nombre_func = $funcionalidades_array[$i];
        $record->descripcion = $descripcion_funcionalidades_array[$i];
        $DB->insert_record('talentospilos_funcionalidad', $record, false);
    }
    
    for($i = 0; $i < count($permisos_array); $i++){
        $record = new stdClass; 
        $record->permiso = $permisos_array[$i];
        $record->descripcion = $descripcion_permisos_array[$i];
        $DB->insert_record('talentospilos_permisos', $record, false);
    }
    
    for($i = 1; $i <= 7; $i++){
        for($j = 1; $j <= 4; $j++)
        {
            $record->id_rol = 6;
            $record->id_permiso = $j;
            $record->id_funcionalidad = $i;
            $DB->insert_record('talentospilos_permisos_rol', $record, false);
        }
    }
    
    $record->id_rol = 1;
    $record->id_permiso = 2;
    $record->id_funcionalidad = 2;
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 1;
    $record->id_permiso = 2;
    $record->id_funcionalidad = 3;
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 1;
    $record->id_permiso = 3;
    $record->id_funcionalidad = 3;
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    //permiso para monitor
    $record->id_rol = 4;
    $record->id_permiso = 2; //leer
    $record->id_funcionalidad = 7; //f_socioeducativa_monitor
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 4;
    $record->id_permiso = 1; //crear
    $record->id_funcionalidad = 7; //f_socioeducativa_monitor
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 4;
    $record->id_permiso = 3; //actualizar
    $record->id_funcionalidad = 7; //f_socioeducativa_monitor
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    // **************************************************
    // ** permisos para rol profesional psicosocial **
    // **************************************************
    $record->id_rol = 3;
    $record->id_permiso = 2; //leer
    $record->id_funcionalidad = 6; //f_socioeducativa_profesional
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 3;
    $record->id_permiso = 1; //crear
    $record->id_funcionalidad = 6; //f_socioeducativa_profesional
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 3;
    $record->id_permiso = 3; //actualizar
    $record->id_funcionalidad = 6; //f_socioeducativa_profesional
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 3;
    $record->id_permiso = 2; //leer
    $record->id_funcionalidad = 2; //reporte_general
    $DB->insert_record('talentospilos_permisos_rol', $record, false);

    $record->id_rol = 3;
    $record->id_permiso = 2; //leer
    $record->id_funcionalidad = 3; //f_general
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 3;
    $record->id_permiso = 3; //actualizar
    $record->id_funcionalidad = 3; //f_general
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 3;
    $record->id_permiso = 2; //leer
    $record->id_funcionalidad = 4; //f_academica
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    //permisos para rol reportes
    $record->id_rol = 2;
    $record->id_permiso = 2; //leer
    $record->id_funcionalidad = 2; //reporte_general
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 2;
    $record->id_permiso = 2; //leer
    $record->id_funcionalidad = 3; //f_general
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 2;
    $record->id_permiso = 2; //leer
    $record->id_funcionalidad = 5; //f_asistencia
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    //permisos para la gestión de monitores y practicantes
    $record->id_rol = 3;
    $record->id_permiso = 2; //leer
    $record->id_funcionalidad = 9; //gestion_monitores_practicantes
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 3;
    $record->id_permiso = 3; //actualizar
    $record->id_funcionalidad = 9; //gestion_monitores_practicantes
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    // **************************************************
    // ** permisos para rol practicante psicoeducativo **
    // **************************************************
    $record->id_rol = 7;
    $record->id_permiso = 2; //leer
    $record->id_funcionalidad = 2; //reporte_general
    $DB->insert_record('talentospilos_permisos_rol', $record, false);

    $record->id_rol = 7;
    $record->id_permiso = 2; //leer
    $record->id_funcionalidad = 6; //f_socioeducativa_profesional
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 7;
    $record->id_permiso = 1; //crear
    $record->id_funcionalidad = 6; //f_socioeducativa_profesional
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 7;
    $record->id_permiso = 3; //actualizar
    $record->id_funcionalidad = 6; //f_socioeducativa_profesional
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    set_config('block_ases_late_install', 1);
}
	 


