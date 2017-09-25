<<<<<<< HEAD
<?php
if (isset($_POST['archivo'])) {
   $archivo = $_POST['archivo'];
   if (file_exists("../view/archivos_subidos/$archivo")) {
      unlink("../view/archivos_subidos/$archivo");
      echo 1;
   } else {
      echo 0;
   }
}
=======
<?php
if (isset($_POST['archivo'])) {
   $archivo = $_POST['archivo'];
   if (file_exists("../view/archivos_subidos/$archivo")) {
      unlink("../view/archivos_subidos/$archivo");
      echo 1;
   } else {
      echo 0;
   }
}
>>>>>>> db_management
?>