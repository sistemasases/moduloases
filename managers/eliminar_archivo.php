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
>>>>>>> 97c7d23d80c7365c0b40027b0d4abac40b2e33b4
?>