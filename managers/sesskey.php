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

>>>>>>> 97c7d23d80c7365c0b40027b0d4abac40b2e33b4
echo $sesskey;