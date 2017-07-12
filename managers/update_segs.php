<?php
require_once(dirname(__FILE__). '/../../../config.php');
require('query.php');
    global $DB;
    
    $update1 = 'update {talentospilos_seg_estudiante} set id_estudiante = 210 where id_estudiante = 109';
    $update2 = 'update {talentospilos_seg_estudiante} set id_estudiante = 213 where id_estudiante = 119';

    
    // $update1 = 'update {talentospilos_seg_estudiante} set id_estudiante = 827 where id_seguimiento = 22288';

    
    $DB->execute($update1);
    $DB->execute($update2);
   