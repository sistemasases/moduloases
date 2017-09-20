<<<<<<< HEAD
<?php
require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

=======
<?php
require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

>>>>>>> 97c7d23d80c7365c0b40027b0d4abac40b2e33b4
echo $DB->insert_record('talentospilos_semestre', array('nombre'=>'2017B','fecha_inicio'=>'2017-08-01','fecha_fin'=>'2017-12-31'));