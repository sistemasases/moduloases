<?php
$directorio_escaneado = scandir('../view/archivos_subidos');
$archivos = array();
foreach ($directorio_escaneado as $item) {
   if ($item != '.' and $item != '..') {
      $archivos[] = $item;
   }
}
echo json_encode($archivos);
?>