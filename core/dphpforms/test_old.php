<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require( "/var/www/html/moodle366/blocks/ases/managers/dphpforms/dphpforms_response_recorder.php" );
require( "/var/www/html/moodle366/blocks/ases/managers/dphpforms/dphpforms_record_updater.php" );

//echo dphpforms_generate_html_recorder( 'seguimiento_pares', "sistemas", -1, -1  );
echo dphpforms_generate_html_updater( 1, "sistemas", 50115 );