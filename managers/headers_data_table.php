<<<<<<< HEAD
<?php

    $array = array();
    
    if(isset($_POST['chk']))
    {
        foreach($_POST['chk'] as $chk)
        {
            array_push($array, array("data" => $chk));
        };
    }
    
    echo json_encode($array);

?>
=======
<?php

    $array = array();
    
    if(isset($_POST['chk']))
    {
        foreach($_POST['chk'] as $chk)
        {
            array_push($array, array("data" => $chk));
        };
    }
    
    echo json_encode($array);

?>
>>>>>>> 97c7d23d80c7365c0b40027b0d4abac40b2e33b4
