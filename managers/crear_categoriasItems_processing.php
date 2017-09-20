<<<<<<< HEAD
<?php

require_once('query.php');

 if(isset($_POST['course'])&&isset($_POST['parent'])&&isset($_POST['fullname'])&&isset($_POST['agregation'])&&($_POST['tipo']=="Categoria")&&isset($_POST['peso'])){
        
        $retorno = insertarCategoria($_POST['course'],$_POST['parent'],$_POST['fullname'],$_POST['agregation'],$_POST['peso']);
        
        echo $retorno;
        
 }
 
 if(isset($_POST['course'])&&isset($_POST['parent'])&&isset($_POST['fullname'])&&($_POST['tipo']=="Item")&&isset($_POST['peso'])){
        
        $retorno = insertarItem($_POST['course'],$_POST['parent'],$_POST['fullname'],$_POST['peso'],true);
        
        echo $retorno;
        
 }
 
 if(isset($_POST['course'])&&$_POST['tipo']=="cargarCat"){

        $cursos = getCategories($_POST['course']);
        
        echo json_encode($cursos);
    }
=======
<?php

require_once('query.php');

 if(isset($_POST['course'])&&isset($_POST['parent'])&&isset($_POST['fullname'])&&isset($_POST['agregation'])&&($_POST['tipo']=="Categoria")&&isset($_POST['peso'])){
        
        $retorno = insertarCategoria($_POST['course'],$_POST['parent'],$_POST['fullname'],$_POST['agregation'],$_POST['peso']);
        
        echo $retorno;
        
 }
 
 if(isset($_POST['course'])&&isset($_POST['parent'])&&isset($_POST['fullname'])&&($_POST['tipo']=="Item")&&isset($_POST['peso'])){
        
        $retorno = insertarItem($_POST['course'],$_POST['parent'],$_POST['fullname'],$_POST['peso'],true);
        
        echo $retorno;
        
 }
 
 if(isset($_POST['course'])&&$_POST['tipo']=="cargarCat"){

        $cursos = getCategories($_POST['course']);
        
        echo json_encode($cursos);
    }
>>>>>>> 97c7d23d80c7365c0b40027b0d4abac40b2e33b4
