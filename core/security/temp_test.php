<?php

require_once( __DIR__ . "/../module_loader.php" );

module_loader( "security" );


//print_r(core_secure_create_call('holamundo', 'front', $name = "Acción testeo 3", $description = "issue 1854", $log = 1));
//print_r(core_secure_create_role('sistemas'));
//print_r(core_secure_assign_call_to_role('holamundo', 'sistemas'));


$singularizations = array(
	'id_semestre' => 10,
	'id_instancia' => 657784	
);

//print_r(core_secure_assign_role_to_user(1, 'sistemas', strtotime("2020-03-24 00:00:00"), strtotime("2020-04-01 00:00:00"), $singularizations));
$data = new StdClass();
core_secure_render($data, 1, $singularizations, strtotime("2020-03-25 00:00:00"));
print_r($data);
