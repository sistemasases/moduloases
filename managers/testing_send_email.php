<?php

require_once('query.php');

function send_email(){

    $emailToUser = new stdClass;
    $emailToAnotherUser = new stdClass;
    $emailFromUser = new stdClass;

    //User who will receive the email
    $emailToUser->email = 'jhonier.caleroa@gmail.com';
    $emailToUser->firstname = 'Jhonier';
    $emailToUser->lastname = 'Calero';
    $emailToUser->maildisplay = true;
    $emailToUser->mailformat = 1;
    $emailToUser->id = -99; 
    $emailToUser->firstnamephonetic = "";
    $emailToUser->lastnamephonetic = "";
    $emailToUser->middlename = "";
    $emailToUser->alternatename = "";

    print_r($emailToUser);

    //User who will receive the email
    $emailToAnotherUser->email = 'moreno.juan@correounivalle.edu.co';
    $emailToAnotherUser->firstname = 'Juan Pablo';
    $emailToAnotherUser->lastname = 'Moreno';
    $emailToAnotherUser->maildisplay = true;
    $emailToAnotherUser->mailformat = 1;
    $emailToAnotherUser->id = -99;
    $emailToAnotherUser->firstnamephonetic = "";
    $emailToAnotherUser->lastnamephonetic = "";
    $emailToAnotherUser->middlename = "";
    $emailToAnotherUser->alternatename = "";

    print_r($emailToAnotherUser);

    //User who sends the email
    $emailFromUser->email = 'jhonier.calero@correounivalle.edu.co';
    $emailFromUser->firstname = 'Andres';
    $emailFromUser->lastname = 'Rodas';
    $emailFromUser->maildisplay = true;
    $emailFromUser->mailformat = 1;
    $emailFromUser->id = -99; 
    $emailFromUser->firstnamephonetic = "";
    $emailFromUser->lastnamephonetic = "";
    $emailFromUser->middlename = "";
    $emailFromUser->alternatename = "";

    print_r($emailFromUser);

    $subject="This is a testing message";
    $messageText="This is a testing message. This is a body message";
    $messageHtml="";

    $email_result = email_to_user($emailToUser, $emailFromUser, $subject, $messageText, $messageHtml, ", ", true);
    $email_result2 = email_to_user($emailToAnotherUser, $emailFromUser, $subject, $messageText, $messageHtml, ", ", true);

    print_r($email_result);
    print_r($email_result2);
}

send_email();