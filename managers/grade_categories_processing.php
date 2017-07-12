<?php

require_once('query.php');

 if(isset($_POST['Asignatura'])&&($_POST['Grupo'])&&($_POST['Metodo']=="Cargar")){
        
        $retorno = getCategoriesWithShortname($_POST['Asignatura'],$_POST['Grupo']);
        
        echo json_encode($retorno);
        
 }
 
 if(isset($_POST['Asignatura'])&&isset($_POST['Grupo'])&&$_POST['Metodo']=="Verificar"){
     
     $idUsuariosCurso=verificarSPPEnGrupo($_POST['Asignatura'],$_POST['Grupo']);
     echo $idUsuariosCurso;
     
 }
 
 if(isset($_POST['Asignatura'])&&isset($_POST['Estudiante'])&&$_POST['Metodo']=="Tabla")
 {
   $htmlTabla=getCoursegradelib_grade_categories($_POST['Asignatura'],$_POST['Grupo'],$_POST['Estudiante']);
   
   echo $htmlTabla;
  
 }

