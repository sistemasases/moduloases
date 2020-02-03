<?php



require_once( __DIR__ . "/../module_loader.php" );

module_loader( "security" );


/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Al realizar una prueba que asinge o actualice el rol a un usuario utilizando un intervalo alternativo
 * el que exista o no la tabla que se referencia dentro del intervalo alternativo no impide que se inserte
 * una nueva fila en mdl_talentospilos_usuario_rol
 * 
 * Para replicar:
 */

$id_semestre = 9;

$alt_interval = '{
    
    "col_name_interval_start" : "fecha_inicio",
    "col_name_interval_end"   : "fecha_fin",
    "table_ref"               : {
    
        "record_id" : ' . $id_semestre . ',
        "name"      : "mdl_talentospilos_semestre"
        
    }
}';


$singularization_test_user = [ "id_instancia" => 563336, "id_semestre" => $id_semestre ];


//print_r(_core_security_solve_alternative_interval($alt_interval));
print_r(
        core_secure_assign_role_to_user(
                16103,
                'sistemas',
                strtotime("2020-01-28 09:00:00"),
                strtotime("2020-02-29 00:00:00"),
                $singularization_test_user,
                True,
                $alt_interval
        )
);