<<<<<<< HEAD
<?php

require_once('query.php');

function send_email(){

    $emailToUser = new stdClass;
    $emailFromUser = new stdClass;
    
    $user = get_full_user(2);
    
    $emailToUser->email = 'iadergg@gmail.com';
    $emailToUser->firstname = $user->firstname;
    $emailToUser->lastname = $user->lastname;
    $emailToUser->maildisplay = true;
    $emailToUser->mailformat = 1;
    $emailToUser->id = $user->id; 

    print_r($emailToUser);

    $emailFromUser->email = 'iader.garcia@correounivalle.edu.co';
    $emailFromUser->firstname = $user->firstname;
    $emailFromUser->lastname = $user->lastname;
    $emailFromUser->maildisplay = true;
    $emailFromUser->mailformat = 1;
    $emailFromUser->id = $user->id; 

    print_r($emailFromUser);
    
    $subject="This is a testing message";
    $messageText="This is a testing message. This is a body message";
    $messageHtml="";
    
    $email_result = email_to_user($emailToUser, $emailFromUser, $subject, $messageText, $messageHtml, ", ", true);
    
    print_r($email_result);
}

=======
<?php

require_once('query.php');

function send_email(){

    $emailToUser = new stdClass;
    $emailFromUser = new stdClass;
    
    $user = get_full_user(2);
    
    $emailToUser->email = 'iadergg@gmail.com';
    $emailToUser->firstname = $user->firstname;
    $emailToUser->lastname = $user->lastname;
    $emailToUser->maildisplay = true;
    $emailToUser->mailformat = 1;
    $emailToUser->id = $user->id; 

    print_r($emailToUser);

    $emailFromUser->email = 'iader.garcia@correounivalle.edu.co';
    $emailFromUser->firstname = $user->firstname;
    $emailFromUser->lastname = $user->lastname;
    $emailFromUser->maildisplay = true;
    $emailFromUser->mailformat = 1;
    $emailFromUser->id = $user->id; 

    print_r($emailFromUser);
    
    $subject="This is a testing message";
    $messageText="This is a testing message. This is a body message";
    $messageHtml="";
    
    $email_result = email_to_user($emailToUser, $emailFromUser, $subject, $messageText, $messageHtml, ", ", true);
    
    print_r($email_result);
}

>>>>>>> 97c7d23d80c7365c0b40027b0d4abac40b2e33b4
send_email();