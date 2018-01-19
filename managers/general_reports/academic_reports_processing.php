<?php
require_once('academic_reports_lib.php');
 
 if(isset($_POST['student'])&&isset($_POST['type'])&&$_POST['type']=="load_loses"){

        $loses = get_loses_by_student($_POST['student']);
        echo $loses;
    }


?>
