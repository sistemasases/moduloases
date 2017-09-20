<<<<<<< HEAD
<?php

require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

$sql_query = "SELECT nombre_rol FROM {talentospilos_rol}";
$result = $DB->get_records_sql($sql_query);

=======
<?php

require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

$sql_query = "SELECT nombre_rol FROM {talentospilos_rol}";
$result = $DB->get_records_sql($sql_query);

>>>>>>> 97c7d23d80c7365c0b40027b0d4abac40b2e33b4
echo json_encode($result);