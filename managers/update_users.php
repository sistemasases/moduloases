<?php
require_once(dirname(__FILE__). '/../../../config.php');
require('query.php');
    global $DB;
    
    echo "users";
    
    $updateUser1 = "UPDATE {user_info_data} SET data = '152' WHERE (userid = 111523 and fieldid = 2)";
    $updateUser2 = "UPDATE {user_info_data} SET data = '887' WHERE (userid = 106622 and fieldid = 2)";
    $updateUser3 = "UPDATE {user_info_data} SET data = '205' WHERE (userid = 103206 and fieldid = 2)";
    $updateUser4 = "UPDATE {user_info_data} SET data = '133' WHERE (userid = 103256 and fieldid = 2)";
    echo $DB->execute($updateUser1);
    echo $DB->execute($updateUser2);
    echo $DB->execute($updateUser3);
    echo $DB->execute($updateUser4);
    
    