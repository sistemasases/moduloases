<<<<<<< HEAD
<?php

require_once(dirname(__FILE__). '/../../../../config.php');

global $DB;

$sql_query = "SELECT nombre_rol FROM {talentospilos_rol}";
$result = $DB->get_records_sql($sql_query);

=======
<?php

require_once(dirname(__FILE__). '/../../../../config.php');

global $DB;

$sql_query = "SELECT nombre_rol FROM {talentospilos_rol}";
$result = $DB->get_records_sql($sql_query);

>>>>>>> db_management
echo json_encode($result);