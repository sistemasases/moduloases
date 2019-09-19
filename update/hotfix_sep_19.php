<?php

// Hotfix caso calvache

require_once(dirname(__FILE__). '/../../../config.php');	
global $DB;												

$query = "
	UPDATE {talentospilos_user_extended}
	SET id_ases_user = 6741
	WHERE id = 7773
";

print_r( 
	$DB->execute( $query ) 
);
