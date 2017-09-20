<<<<<<< HEAD
<?php
$directorio_escaneado = scandir('../view/archivos_subidos');
$archivos = array();
foreach ($directorio_escaneado as $item) {
   if ($item != '.' and $item != '..') {
      $archivos[] = $item;
   }
}
echo json_encode($archivos);
=======
<?php
$directorio_escaneado = scandir('../view/archivos_subidos');
$archivos = array();
foreach ($directorio_escaneado as $item) {
   if ($item != '.' and $item != '..') {
      $archivos[] = $item;
   }
}
echo json_encode($archivos);
>>>>>>> 97c7d23d80c7365c0b40027b0d4abac40b2e33b4
?>