<<<<<<< HEAD
<?php

require_once(dirname(__FILE__).'/../../../../config.php');
require_once(dirname(__FILE__).'/../instance_management/instance_lib.php');

 //metodo apra borrar archivos de un folder
 
function deleteFilesFromFolder($folderPath){
    $files = glob($folderPath.'/*'); // get all file names
    foreach($files as $file){ // iterate files
          if(is_file($file))  unlink($file); // delete file
    }
}

function createZip($patchFolder,$patchStorageZip){
    // Get real path for our folder
    $rootPath = realpath($patchFolder);
    
    // Initialize archive object
    $zip = new ZipArchive();
    $zip->open($patchStorageZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    
    // Create recursive directory iterator
    /** @var SplFileInfo[] $files */
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($rootPath),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    
    foreach ($files as $name => $file)
    {
        // Skip directories (they would be added automatically)
        if (!$file->isDir())
        {
            // Get real and relative path for current file
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($rootPath) + 1);
    
            // Add current file to archive
            $zip->addFile($filePath, $relativePath);
        }
    }
    
    // Zip archive will be created only after closing object
    $zip->close();
}
=======
<?php

require_once(dirname(__FILE__).'/../../../../config.php');
require_once(dirname(__FILE__).'/../instance_management/instance_lib.php');

 //metodo apra borrar archivos de un folder
 
function deleteFilesFromFolder($folderPath){
    $files = glob($folderPath.'/*'); // get all file names
    foreach($files as $file){ // iterate files
          if(is_file($file))  unlink($file); // delete file
    }
}

function createZip($patchFolder,$patchStorageZip){
    // Get real path for our folder
    $rootPath = realpath($patchFolder);
    
    // Initialize archive object
    $zip = new ZipArchive();
    $zip->open($patchStorageZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    
    // Create recursive directory iterator
    /** @var SplFileInfo[] $files */
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($rootPath),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    
    foreach ($files as $name => $file)
    {
        // Skip directories (they would be added automatically)
        if (!$file->isDir())
        {
            // Get real and relative path for current file
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($rootPath) + 1);
    
            // Add current file to archive
            $zip->addFile($filePath, $relativePath);
        }
    }
    
    // Zip archive will be created only after closing object
    $zip->close();
}
>>>>>>> db_management
?>