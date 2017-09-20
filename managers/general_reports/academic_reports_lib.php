<?php
/*
 * Consultas modulo reportes academicos.
 */
require_once(__DIR__ . '/../../../../config.php');
require_once $CFG->dirroot.'/blocks/ases/managers/lib/student_lib.php'; 

function getReportStudents(){

	$string_html = "<table id = 'students'>
				<thead>
					<tr>
                        <th> Nombre Completo </th>
                        <th> Curso </th>
                    </tr>
				</thead>";

	$string_html.= "</table>";
	return $string_html;
}

?>
