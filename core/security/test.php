<?php

require_once( __DIR__ . "/../module_loader.php" );

module_loader( "security" );

function hello_world( $times ){
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
);



//print_r( core_secure_call( "hello_world", [5], $context, 73380, $singularizations) );

$data = new stdClass();
secure_render( $data, 73380, $singularizations);
print_r( $data );

?>