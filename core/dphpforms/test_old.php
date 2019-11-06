<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require( "/usr/local/www/apache24/data/moodle35/blocks/ases/managers/dphpforms/dphpforms_response_recorder.php" );

echo dphpforms_generate_html_recorder( 'seguimiento_pares', "sistemas", -1, -1  );