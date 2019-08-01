<?php 
/**
 * @package		block_ases
 * @subpackage	core.security
 * @author 		Jeison Cardona Gómez
 * @copyright 	(C) 2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once( __DIR__ . "/query_manager.php");
require_once( __DIR__ . "/gets.php");
require_once( __DIR__ . "/general_functions.php");
require_once( __DIR__ . "/checker.php");

module_loader("periods");

const VALID_CHARACTERS = [
    'a','b','c','d','e','f','g',
    'h','i','j','k','l','m','n',
    'o','p','q','r','s','t','u',
    'v','w','x','y','z','0','1',
    '2','3','4','5','6','7','8',
    '9','_'
];

/**
 * Function that processes a secure call request.
 *
 * This function requieres a context to execute, an example of this context:
 *
 * $context = array(
 * 	'fun_name' => array( 
 *		'action_alias' => 'one_alias',
 *		'params_alias' => "one_alias"
 *	)
 * )
 *
 * @see get_action( $in ) in gets.php.
 * @see user_exist( $user_id ) in this file.
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @param string $function_name, Function name.
 * @param array $args, Arguments.
 * @param array $context, Context for execution.
 * @param integer $user_id, User that execute the function.
 *
 * @return array
 */
function secure_Call( $function_name, $args = null, $context = null, $user_id = null, $singularizations = null, $time_context = null ){

	if( is_null( $time_context ) ){
		$time_context = time();
	}

	//Context validation
	if( is_null( $context ) ){	
		throw new Exception( "Undefined context" ); 
	}else{
		if( !array_key_exists( $function_name , $context) ){
			throw new Exception( "Function '$function_name' does not exist in the context" ); 
		}
	}
	
	//Action validation
	$action = _core_security_get_action( $context[ $function_name ]['action_alias'] );

	if( is_null( $action ) ){
		return array(
			'status' => -1,
			'status_message' => 'unregulated action',
			'data_response' => null
		);
	}else{

		if( is_null( $user_id ) ){
			throw new Exception( "User rol cannot be null" ); 
		}else{

			if( $user_id <= -1 ){
				return array(
					'status' => -1,
					'status_message' => 'invalid user ID',
					'data_response' => null
				);
			}else{

				if( _core_security_user_exist( $user_id ) ){

					$user_rol = _core_security_get_user_rol( $user_id, $time_context, $singularizations );

					if( $user_rol ){

						if( _core_security_can_be_executed( $user_rol['id'], $action['id'] ) ){

							$defined_user_functions = get_defined_functions()['user'];

							if( in_array( $function_name, $defined_user_functions ) ){

								$to_return = call_user_func_array( $function_name, $args );

								if( $action['registra_log'] == 1 ){
									_core_security_register_log( $user_id, $action['id'], $args, $to_return );
								}

								return $to_return;
								
							}else{
								throw new Exception( "Function " . $function_name . " was not declared." );
							}

						}else{
							return array(
								'status' => -1,
								'status_message' => 'forbidden, access is denied',
								'data_response' => null
							);
						}

					}else{
						return array(
							'status' => -1,
							'status_message' => 'forbidden, access is denied',
							'data_response' => null
						);
					}
					
				}else{
					return array(
						'status' => -1,
						'status_message' => 'invalid user ID',
						'data_response' => null
					);
				}

			}

		}

	}

}

/**
 * ...
 *
 * @see get_db_manager() in query_manager.php
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @param object $data, reference to empty stdClass object
 * @param integer $user_id
 * @param array $singularizations
 * @param integer $time_context
 *
 * @return void
*/
function secure_render( &$data, $user_id = null, $singularizations = null, $time_context = null ){
	
	if( is_null( $time_context ) ){
		$time_context = time();
	}

	if( is_null( $user_id ) ){
		throw new Exception( "User rol cannot be null" ); 
	}else{

		if( $user_id <= -1 ){
			return array(
				'status' => -1,
				'status_message' => 'invalid user ID',
				'data_response' => null
			);
		}else{

			if( _core_security_user_exist( $user_id ) ){

				$user_rol = _core_security_get_user_rol( $user_id, $time_context, $singularizations );
				
				if( $user_rol ){

					$actions_type = _core_security_get_actions_types();
					$type_id = null;
					foreach ($actions_type as $key => $type) {
						if( $type['alias'] == "front" ){
							$type_id = $type['id'];
							break;
						}
					}
					$actions = _core_security_get_role_actions( $user_rol['id_rol'], $type_id );
					foreach ($actions as $key => $action) {
						$alias_action = $action['alias'];
						$data->$alias_action = true;
					}

				}else{
					return array(
						'status' => -1,
						'status_message' => 'forbidden, access is denied',
						'data_response' => null
					);
				}

			}else{
				return array(
					'status' => -1,
					'status_message' => 'invalid user ID',
					'data_response' => null
				);
			}

		}

	}
}

/**
 * ...
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @param string $templates_dir, Folder with mustache files.
 *
 * @return array
*/
function secure_template_checker( $templates_dir ){
    

	$fileList = glob( $templates_dir . '/*');
	$unsolved_secure_blocks = [];

	$actions_type = _core_security_get_action_type( 'front' );
	$actions = _core_security_get_actions( $actions_type['id'] );
	$alias_actions = array_map( function($in){ return $in['alias']; }, $actions );

	foreach($fileList as $filename){

	    $template = file_get_contents( $filename );
	    $positions = _strpos_all( $template, "{{#" . CORE_PREFIX . "_" );
	    
	    foreach ($positions as $key => $value) {

	    	$alias_tag = "";
	    	for( $i = $value ; $i < strlen( $template ); $i++ ) { 

	    		if($template[ $i ] == '}'){
	    			break;
	    		}

	    		if( in_array($template[ $i ], VALID_CHARACTERS) ){
	    			$alias_tag .= $template[ $i ];
	    		}
		    }
		    if( $alias_tag != "" ){

		    	if( !in_array($alias_tag, $alias_actions) ) {
		    		array_push($unsolved_secure_blocks, $alias_tag);
		    	}
		   		
		    }
	    }
	    
	}
	
	return $unsolved_secure_blocks;
}

/**
 * Function that find secure_call declarations that does not exist at the database.
 * 
 * @see _core_security_get_config_actions( ... )  in general_functions.php                       
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @param string $managers_dir Folder with managers.
 *
 * @return array List of call aliases that does not exist at the database
*/
function secure_call_checker( $managers_dir ){
        
    $managers = array_filter(glob( $managers_dir . '/*'), 'is_dir');
    $unsolved_secured_calls = [];

    $actions_type_back = _core_security_get_action_type( 'back' );
    $stored_actions = _core_security_get_actions( $actions_type_back['id'] );
    $stored_alias_actions = array_map( function($in){ return $in['alias']; }, $stored_actions );

    foreach($managers as $manager){
        $config_file = $manager . "/" . basename( $manager ) . ".xml";
        if( file_exists( $config_file ) ){
            $calls = _core_security_get_config_actions( $config_file );
            $unsolved_secured_calls = array_merge( 
                $unsolved_secured_calls,
                array_diff($calls, $stored_alias_actions) 
            );
        }
    }
        
    return array_values( $unsolved_secured_calls );
}

/**
 * Function that insert at the DB a new action.
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * @see _core_security_get_action( ... ) in gets.php
 * @see _core_security_get_action_type( ... ) in gets.php
 * @see get_db_manager( ... ) in query_manager.php
 * 
 * @param string $alias Action alias
 * @param integer|string $action_type Identifier or type alias (back or front)
 * @param string $name Action name
 * @param string $description Action description
 * @param integer $log Allow store every return
 * 
 * @return integer|null If the operation was correct, it will be return 1
 */
function secure_create_call($alias, $action_type, $name = NULL, $description = NULL, $log = 0){
    
    // Check if no exist at the DB
    if( is_null( _core_security_get_action( $alias ) ) ){
        
        $_action_type = _core_security_get_action_type( $action_type );
        
        if( $_action_type ){
            global $DB_PREFIX;
        
            $manager = get_db_manager();

            $tablename = $DB_PREFIX . "talentospilos_acciones";
            $params = [
                $alias, $name, $description, $_action_type['id'], $log
            ];

            $query = "INSERT INTO $tablename "
                    . "(alias, nombre, descripcion, id_tipo_accion, registra_log)"
                    . "VALUES($1, $2, $3, $4, $5)";

            return $manager( $query, $params, $extra = null );
        }else{
            return null;
        }
                
    }else{
        return null;
    }
    
}

/**
 * Function that given an action id or alias, delete it from the database
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @sicen 1.0.0
 * 
 * @see _core_security_get_action( ... ) in tgets.php
 * @see get_db_manager( ... ) in query_manager.php
 * 
 * @param integer|string $alias Action alias or identifier
 * @param integer $user_id User Moodle id
 * 
 * @return integer|null If the operation was correct, return 1
 */
function secure_remove_call( $alias, $user_id ){
   
    $action = _core_security_get_action($alias);
    if( !is_null( $action ) ){
        
        global $DB_PREFIX;
        
        $user = NULL;
        $manager = get_db_manager();
        
        if( is_numeric($user_id) ){
            
            $tablename = $DB_PREFIX . "user";
            $params = [ $user_id ];
            $user = $manager( "SELECT * FROM $tablename WHERE id = $1", $params, $extra = null );
            
        }
        
        if( $user ){
            
            $tablename = $DB_PREFIX . "talentospilos_acciones";
            $params = [ $action['id'] ];
            $query = "UPDATE $tablename SET eliminado = 1, fecha_hora_eliminacion = 'now()', id_usuario_eliminador = $user_id "
                    . "WHERE id = $1";

            return $manager( $query, $params, $extra = null );    
        }
        
    }
    
    return null;
    
}

/**
 * Function that insert at the DB a new role.
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see get_db_manager( ... ) in query_manager.php
 * @see _core_security_get_role( ... ) in gets.php
 * 
 * @param string $alias Role alias
 * @param integer|string $father_role Role id or alias
 * @param string $name Role name
 * @param string $description
 * 
 * @return integer|null If the operation was correct, return 1
 */
function secure_create_role( $alias, $father_role = -1, $name = NULL, $description = NULL ){
    
    global $DB_PREFIX;
    
    $role = _core_security_get_role( $alias );
    if( is_null( $role ) ){
        
        $_father_role = _core_security_get_role( $father_role );
        //-1 means no father
        ( $father_role ? $_father_role_id = ($_father_role ? $_father_role['id'] : -1) : null);
        
        $tablename  = $DB_PREFIX . "talentospilos_roles";
        $params = [
            $alias, $name, $description, $_father_role_id
        ];
            
        $manager = get_db_manager();
        $query = "INSERT INTO $tablename(alias, nombre, descripcion, id_rol_padre) VALUES ( $1, $2, $3, $4 )";
        return $manager( $query, $params, $extra = null );
        
    }
    
    return null;   
    
}

/**
 * Function ...
 * 
 * @param integer $user_id Moodle user id
 * @param integer|string Role id or alias
 * @param datetime $start_datetime
 * @param datetime $end_datetime
 * @param object $alternative_interval
 * @param boolean $use_alternative_interval
 * @param object $singularizator
 * 
 */
function secure_assign_role_to_user( $user_id, $role, $start_datetime = NULL, $end_datetime = NULL, $alternative_interval = NULL, $use_alternative_interval = 0, $singularizator = NULL ){

    if( ( $use_alternative_interval === 0 && $start_datetime === NULL ) ||
        ( $use_alternative_interval === 0 && $end_datetime === NULL ) ){
        return null;
    }else{
        if( ($start_datetime >= $end_datetime) ){ return null; }
    }
    
    $_user = get_db_records( "user", ['id'], [$user_id] );
    $_role = _core_security_get_role( $role ); // Rol at the master system (Secutiry Core)
      
    if( $_user && $_role ){
        
        if( SUPPORT_TO_PREVIOUS_SYSTEM ){
            
            //Validation if the role exist at the previous system role
            if ( _core_security_get_previous_system_role( $_role['alias'] ) ){
                /*Asignar en sistema previo*/
                secure_assign_role_to_user_previous_system( $user_id, $_role['alias'], [ "id_instancia" => 450299 ]);
            }
            
        }
     
        if( is_null(_core_security_get_user_rol( $user_id, $start_datetime, $singularizator )) ){
            
            global $DB_PREFIX;
            
            $manager = get_db_manager();
            $date_format = "Y-m-d H:i:s";
            
            $tablename = $DB_PREFIX . "talentospilos_usuario_rol";
            $params = [ $user_id, $_role['id'], date( $date_format, $start_datetime),  date( $date_format, $end_datetime), $alternative_interval, json_encode($use_alternative_interval), $singularizator ];
            $query = "INSERT INTO $tablename ( id_usuario, id_rol, fecha_hora_inicio, fecha_hora_fin, intervalo_validez_alternativo,usar_intervalo_alternativo,singularizador) "
                    . "VALUES ( $1, $2, $3, $4, $5, $6, $7 )";
            
            return $manager( $query, $params, $extra = null );
            
        }
        
    }
    
    return null;
}

function secure_assign_role_to_user_previous_system( $user_id, $role, $singularizator ){

	/*Singularizator
     *
     * estado (DEFAULT = 1)
     * id_semestre (DEFAULT = current )
     * id_jefe 
     * id_instancia (REQUIRED)
     * id_programa
     * 
     */
    
    if( !isset( $singularizator['id_instancia'] ) ){
        throw new Exception( "id_instancia must be defined at sigularizator", -1 );
    }

    $role_id = -1;

    switch ( gettype( $role ) ) {
    	case 'string':
    		$role_id = _core_security_get_previous_system_role( $role );
    		break;
    	case 'object':
    		$role_id = $role->id;
    		break;
    	case 'integer':
    		break;
    	default:
    		throw new Exception( "role must be an id, role alias or role obj", -2 );
    		break;
    }
    
    if( !isset($singularizator['id_semestre']) ){
		$singularizator['id_semestre'] = core_periods_get_current_period()->id;
    }

    if( !_core_user_asigned_in_previous_system( $user_id, $role, $singularizator ) ){
    	global $DB_PREFIX;
            
        $manager = get_db_manager();
        $period_id = ( isset($singularizator['id_semestre']) ? $singularizator['id_semestre'] : core_periods_get_current_period()->id );
            
        $tablename = $DB_PREFIX . "talentospilos_user_rol";
        $params = [ 
        	(int) $role_id, 
        	(int) $user_id, 
        	$estado = 1, 
        	(int) $period_id,  
        	(isset($singularizator['id_jefe']) ? (int) $singularizator['id_jefe'] : NULL),
        	(int) $singularizator['id_instancia'],
        	(isset($singularizator['id_programa']) ? (int) $singularizator['id_programa'] : NULL)
        ];
        $query = "INSERT INTO $tablename ( id_rol, id_usuario, estado, id_semestre, id_jefe, id_instancia, id_programa) "
        		. "VALUES ( $1, $2, $3, $4, $5, $6, $7 )";

    	$manager( $query, $params, $extra = null );
    }
   
}


?>