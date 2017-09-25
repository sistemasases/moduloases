<<<<<<< HEAD
<?php

require_once(dirname(__FILE__). '/../../../config.php');

global $USER;

$sesskey = $USER->sesskey;

=======
<?php

require_once(dirname(__FILE__). '/../../../config.php');

global $USER;

$sesskey = $USER->sesskey;

>>>>>>> db_management
echo $sesskey;