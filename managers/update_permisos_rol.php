<<<<<<< HEAD
<?php
require_once(dirname(__FILE__). '/../../../config.php');
require('query.php');
    
global $DB;
if(isset($_POST['funciones']) && isset($_POST['rol'])){
    $funciones = $_POST['funciones'];
    foreach($funciones as $permiso){
        
    }   
=======
<?php
require_once(dirname(__FILE__). '/../../../config.php');
require('query.php');
    
global $DB;
if(isset($_POST['funciones']) && isset($_POST['rol'])){
    $funciones = $_POST['funciones'];
    foreach($funciones as $permiso){
        
    }   
>>>>>>> 97c7d23d80c7365c0b40027b0d4abac40b2e33b4
}