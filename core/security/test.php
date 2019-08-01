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


//print_r( core_secure_assign_role_to_user( 73380, 'profesional_ps', strtotime("2019-09-27 15:00:00"), strtotime("2019-09-27 15:00:01"), [ "id_instancia" => 450299 ] ));

?>