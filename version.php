<?php
    $plugin->component = 'block_ases';  
    
    /**
     * PARA ARREGLAR VERSIÓN EN PRODUCCIÓN
     * UPDATE mdl_config_plugins SET value=2021032313380 WHERE id=1659;
     * 
     * También proveo un script que hace lo mismo, para mayor
     * comodidad desde el cli: php fix_version.php 
     * con php 7.4
     * 
     * David S. Cortés
     */

    $plugin->version=  22021042120260;

    $plugin->requires = 2010112400;
