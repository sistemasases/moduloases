<<<<<<< HEAD
<?php

require_once('query.php');

if(isset($_POST['id'])){
    
    $array = getRiskByStudent($_POST['id']);
    
    echo json_encode($array);
=======
<?php

require_once('query.php');

if(isset($_POST['id'])){
    
    $array = getRiskByStudent($_POST['id']);
    
    echo json_encode($array);
>>>>>>> 97c7d23d80c7365c0b40027b0d4abac40b2e33b4
}