<?php

require_once( __DIR__ . "/../module_loader.php" );

module_loader( "security" );

/*function hello_world( $times ){
	$output = [];
	for($i = 0; $i < $times; $i++ ) { array_push( $output, "hello world" ); }
	return $output;
}
$context = [
	'hello_world' => [
		'action_alias' => 'say_hello',
		'params_alias' => null
	]
];
$singularizations = array(
	'singularizador_1' => "99",
	'singularizador_2' => "55555"
);*/
//print_r( core_secure_call( "hello_world", [5], $context, 73380, $singularizations) );

//$data = new stdClass();
//core_secure_render( $data, 73380, null);
//print_r( $data );

//print_r( core_secure_template_checker( __DIR__ . "/../../templates" ) );

//print_r(_core_security_check_role( 73380, 4, $time_context = null, $singularizations = null ));

//print_r( core_secure_call_checker( __DIR__ . "/../../managers" ) );

//print_r( core_secure_create_call( "test_aliasxy", "back", $name = NULL, $description = NULL, $log = 0 ) );

//print_r( _core_security_get_actions( "back" ) );

//print_r(secure_remove_call( "say_hello", 107089 ) );

//print_r( core_secure_create_call( "say_hello", "back", $name = NULL, $description = NULL, $log = 0 ) );


//print_r(core_secure_create_role("rootx", -1, "Super user", "Super usuario") );

//print_r( get_table_structure("mdl_talentospilos_rol") );
//print_r( get_table_constrains("mdl_talentospilos_rol") );

//print_r( secure_assign_role_to_user( 15, "root" ) );


//print_r( core_secure_assign_role_to_user( 73380, "monitor_ps", strtotime("2019-09-27 15:00:00"), strtotime("2019-09-27 15:00:01") ) );

//print_r( core_secure_assign_role_to_user( 73380, 'profesional_ps', strtotime("2019-09-27 15:00:00"), strtotime("2019-09-27 15:00:01"), [ "id_instancia" => 450299, "id_semestre" => 8 ] ));
//print_r( core_secure_assign_role_to_user( 73380, 'profesional_ps', strtotime("2019-09-27 15:00:00"), strtotime("2019-09-27 15:00:01"), [ "id_instancia" => 450299 ] ));
//print_r( core_secure_assign_role_to_user( 73380, 'root', strtotime("2019-09-27 15:00:00"), strtotime("2019-09-27 15:00:01"), [ "id_instancia" => 450299, "id_semestre" => 99, 999 => 999 ] ));

//print_r(_core_security_get_user_rol( 73380,  strtotime("2019-09-27 15:00:00"), [ "id_instancia" => 450299, "id_semestre" => 99, 999 => 999 ] ) );
//print_r(secure_remove_role_to_user( 73380, "root",  strtotime("2019-09-27 15:00:00"), [ "id_instancia" => 450299, "id_semestre" => 99, 999 => 999 ] ));
//print_r(_core_security_get_user_rol( 73380,  strtotime("2019-09-27 15:00:00"), [ "id_instancia" => 450299, "id_semestre" => 99, 999 => 999 ] ) );

//print_r(_core_security_get_user_rol( 73380,  strtotime("2019-09-27 15:00:00"), [ "id_instancia" => 450299, "id_semestre" => 8 ] ) );
//print_r(secure_remove_role_to_user( 73380, "profesional_ps",  strtotime("2019-09-27 15:00:00"), [ "id_instancia" => 450299, "id_semestre" => 8 ] ));
//print_r(_core_security_get_user_rol( 73380,  strtotime("2019-09-27 15:00:00"), [ "id_instancia" => 450299, "id_semestre" => 8 ] ) );

//print_r(secure_remove_role_to_user( 73380, "profesional_ps",  strtotime("2019-09-27 15:00:00"), [ "id_instancia" => 450299, "id_semestre" => 9 ] ));

/*print_r(
        
    core_secure_update_role_to_user( 73380, "root", 1,
        strtotime("2019-09-27 15:00:00"), [ "id_instancia" => 450299, "id_semestre" => 99, 999 => 999 ],
        $start_datetime = strtotime("2019-09-27 15:00:00"), $end_datetime = strtotime("2019-12-27 15:00:00"), $singularizer = [ "id_instancia" => 450299, "id_semestre" => 99, 999 => 9991 ], $use_alternative_interval = false, $alternative_interval = NULL 
    )
        
);*/

/*print_r(
    _core_security_check_subroles( "profesional_ps", 3 )
);*/

/*print_r(
    secure_remove_role( "profesional_ps", 73380 )
);*/


//print_r( core_secure_create_call( "create_user", "back", $name = "Create a new user.", $description = "--", $log = 1 ) );

//print_r( core_secure_assign_call_to_role( "create_user", "profesional_ps" ) );

//print_r( core_secure_remove_call_role( "create_user", "profesional_ps", 73380 ) );

//print_r( core_secure_update_role( "profesional_ps", 's', 'NULLs' ) );

//print_r( core_secure_update_action( "create_user", NULL, "", true ) );


//print_r( core_secure_find_key( $explicit_hexed_rule = "99999" ) );
$id_semestre = 10;

$alt_interval = '{
    
    "col_name_interval_start" : "fecha_inicio",
    "col_name_interval_end"   : "fecha_fin",
    "table_ref"               : {
    
        "record_id" : ' . $id_semestre . ',
        "name"      : "mdl_talentospilos_semestre"
        
    }
}';

$singularization_test_user = [ "id_instancia" => 563336, "id_semestre" => $id_semestre ];

/* print_r(
	core_secure_assign_role_to_user(
		161037,
		'sistemas',
		strtotime("2019-05-21 00:00:00"),
		strtotime("2020-12-31 23:59:59"),
		$singularization_test_user,
		true,
		$alt_interval
	)
);  */

print_r(
	core_secure_assign_role_to_user(
		161037,
		'root',
		strtotime("2020-02-04 00:00:00"), // <-- este va a ser el $time_context que usará _core_security_get_user_rol
		strtotime("2020-12-31 23:59:59"),
		$singularization_test_user,
		false,
		NULL
	)
); 


//echo '<img src="data:image/jpeg;base64,' . secure_generate_image( 9156798765456, $height = 3, $total = 80, $step_size = 10, $separator_size = 5 ) .'" />';

?>