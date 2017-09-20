<<<<<<< HEAD
<?php

require_once('query.php');

if(isset($_POST['fun'])){
    if($_POST['fun'] == 'get_professional'){
        echo get_assigned_professional($_POST['idStudent']);    
    }else if($_POST['fun'] == 'get_practicante'){
        echo get_assigned_pract($_POST['idStudent']);
    }else if($_POST['fun'] == 'get_monitor'){
        echo get_assigned_monitor($_POST['idStudent']);
    }
}

=======
<?php

require_once('query.php');

if(isset($_POST['fun'])){
    if($_POST['fun'] == 'get_professional'){
        echo get_assigned_professional($_POST['idStudent']);    
    }else if($_POST['fun'] == 'get_practicante'){
        echo get_assigned_pract($_POST['idStudent']);
    }else if($_POST['fun'] == 'get_monitor'){
        echo get_assigned_monitor($_POST['idStudent']);
    }
}

>>>>>>> 97c7d23d80c7365c0b40027b0d4abac40b2e33b4
