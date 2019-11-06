<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Ases block
 *
 * @author     Jeison Cardona Gómez
 * @package    block_ases
 * @copyright  2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__). '/../../../../config.php');
require_once($CFG->dirroot.'/blocks/ases/core/security/security.php');

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

?>
