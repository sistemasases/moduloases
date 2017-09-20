<<<<<<< HEAD
<?php
require_once(dirname(__FILE__). '/../../../config.php');
require('query.php');
    global $DB;
    $updateAsig = "UPDATE {talentospilos_monitor_estud} SET id_semestre = 5 WHERE id_semestre IS NULL";
  

    echo $DB->execute($updateAsig);
=======
<?php
require_once(dirname(__FILE__). '/../../../config.php');
require('query.php');
    global $DB;
    $updateAsig = "UPDATE {talentospilos_monitor_estud} SET id_semestre = 5 WHERE id_semestre IS NULL";
  

    echo $DB->execute($updateAsig);
>>>>>>> 97c7d23d80c7365c0b40027b0d4abac40b2e33b4
