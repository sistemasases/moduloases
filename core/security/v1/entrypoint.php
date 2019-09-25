<?php 
/**
 * @package	block_ases
 * @subpackage	core.security
 * @author 	Jeison Cardona Gómez
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
* Consider this function to read the rest of the documentation.
 *
 * function hello_world( $times ){
 *		to_return = [];
 * 		for($i = 0; $i < $times; $i++ ) { array_push( $output, "hello world" ); }
 *		return $to_return;
 * }
 *
 * What is a context?
 *
 * A context provide the information necesary for the execution. A set of secure_calls can be defined at 
 * the same context.
 *
 * Context structure:
 *
 * array(
 * 	'fun1_name' => array( 
 *		'action_alias' => 'one_alias',
 *		'params_alias' => "one_alias"
 *	),
 * 'fun2_name' => array( 
 *		'action_alias' => 'one_alias',
 *		'params_alias' => "one_alias"
 *	)
 * );
 *
 * Example:
 * 
 * array(
 *	'hello_world' => array(
 *		'action_alias' => 'say_hello',
 *		'params_alias' => "any"
 *	)
 * )
 *
 * Singularization: An user can be assigned to multiples roles, with a differentiate factor, the singularizations,
 * this allow pick up the current rol, work as a flag, for example, an user can stay in multiple chat rooms, but
 * this user cannot have the same role at all chat rooms, sigularize this user can be possible with the next definition
 * of a singularization.
 *
 * In this example, the user with the identifier 9999, have two roles inside the same system.
 *
 * User id: 		9999
 * Rol id:			5 (standard member)
 * sigularization:	[	
 *						"chat_room" => "Family"
 *					]	
 *
 * User id: 		9999
 * Rol id:			1 (admin member)
 * sigularization:	[	
 *						"chat_room" => "Sales"	
 *					]	
 *
 * Powerful example: in this case, the user 9999 have the role 'standard' between in a interval at the day,  and admin 
 * the rest of the time, every day. The singularizations keys and values need be defined at the user-role relationship.
 *
 * User id: 		9999
 * Rol id:			1 (standard member)
 * sigularization:	[	
 *						"start" => "00:00:00",
 *						"end" => "06:00:00"	
 *					]
 * User id: 		9999
 * Rol id:			1 (admin member)
 * sigularization:	[	
 *						"start" => "06:00:01",
 *						"end" => "23:59:59"			
 *					]
 *
 * Time context: The system need determine the "current" role valid, how can be possible be coordinate with many 
 * time source, for example: database server time, web server time, fixed time, etc. The "current" time need be 
 * provided, if not is provided, the system determine it with time() function.
 *
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
 * Function that checks if new permissions are declared in every template.
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see _core_security_get_action_type( ... ) in gets.php
 * @see _core_security_get_actions( ... ) in gets.php
 *
 * @param string $templates_dir Folder with mustache files.
 *
 * @return array List of new permissions.
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
 * @see _core_security_get_action_type( ... ) in gets.php
 * @see _core_security_get_actions( ... ) in gets.php
 * @see _core_security_get_config_actions( ... ) in general_functions.php
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
 * @see _core_security_get_action( ... ) in gets.php
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
 * Function that assign a role to a given user.
 * 
 * Singularizer is a set of key-value tuples with the objective of differentiate
 * assignations in the system, for example, the next list of assignations, are 
 * different everyone to each others: 
 * 
 * user_id: 15, Singularizer:  { "filter_1":111, "filter_2":"ABC"  }    <br>  
 * user_id: 15, Singularizer:  { "filter_1":111  }                      <br>  
 * user_id: 15, Singularizer:  { "filter_2":"ABC"  }                    <br>    
 * user_id: 15, Singularizer:  { "ff_1":111  }                          
 * 
 * If $use_alternative_interval true, then an alternative_interval must be defined.
 * Example of an alternative interval definition:
 *
 * {
 *	"table_ref": { "name":"table_name", "record_id": 1 },
 *	"col_name_interval_start": "col_start", 
 *	"col_name_interval_end": "col_end" 
 * }
 * 
 * table_ref is the table of reference.                                     <br>  
 * col_name_interval_start is the name where the start date time is stored. <br>  
 * col_name_interval_start is the name where the end date time is stored.   <br>  
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * 
 * @see _core_security_get_role( ... ) in gets.php
 * @see _core_security_get_user_rol( ... ) in gets.php
 * @see _core_security_get_previous_system_role( ... ) in gets.php
 * @see secure_assign_role_to_user_previous_system( ... ) in entrypoint.php
 * @see get_db_manager( ... ) in query_manager.php
 * @see get_db_records( ... ) in query_manager.php
 * 
 * @param integer $user_id User id.
 * @param integer|string $role Role id or alias.
 * @param datetime $start_datetime Start date time.
 * @param datetime $end_datetime End date time.
 * @param object $singularizer Singularizer is a set of key-value tuples with the objective of differentiate assignations in the system. See the example above..
 * @param boolean $use_alternative_interval If true, then an alternative_interval must be defined.
 * @param object $alternative_interval Definition de alternative interval, used to take as valid interval of existence an interval stored in other table. See the example above.
 * @throws Exception If an inherit role is to be assigned with a key-value tuple that doesn't exist at the table talentospilos_user_rol or if exist but its value isn't valid.
 * 
 * @return integer|NULL 1 if okay, null if  assignation already exist.
 */
function secure_assign_role_to_user( $user_id, $role, $start_datetime = NULL, $end_datetime = NULL, $singularizer = NULL, $use_alternative_interval = false, $alternative_interval = NULL ){

    if( ( $use_alternative_interval === false && $start_datetime === NULL ) ||
        ( $use_alternative_interval === false && $end_datetime === NULL ) ){
        return null;
    }else{
        if( ($start_datetime >= $end_datetime) ){ return null; }
    }

    $_user = get_db_records( "user", ['id' => $user_id] );
    $_role = _core_security_get_role( $role ); // Rol at the master system (Secutiry Core)
      
    if( $_user && $_role ){
     
        if( is_null(_core_security_get_user_rol( $user_id, $start_datetime, $singularizer )) ){

        	if( SUPPORT_TO_PREVIOUS_SYSTEM ){
            
	            //Validation if the role exist at the previous system role
	            if ( _core_security_get_previous_system_role( $_role['alias'] ) ){
	                /*Asignar en sistema previo*/
	                secure_assign_role_to_user_previous_system( $user_id, $_role['alias'], $singularizer);
	            }
	            
	        }
            
            global $DB_PREFIX;
            
            $manager = get_db_manager();
            $date_format = "Y-m-d H:i:s";
            
            //Valid format
            $use_alternative_interval = ( $use_alternative_interval ? 1 : 0 );
            $alternative_interval = ( is_null($alternative_interval) ? NULL : json_encode($alternative_interval) );
            $singularizer = ( is_null($singularizer) ? NULL : json_encode($singularizer) );
            
            $tablename = $DB_PREFIX . "talentospilos_usuario_rol";
            $params = [ $user_id, $_role['id'], date( $date_format, $start_datetime),  date( $date_format, $end_datetime), $alternative_interval, $use_alternative_interval, $singularizer ];
            $query = "INSERT INTO $tablename ( id_usuario, id_rol, fecha_hora_inicio, fecha_hora_fin, intervalo_validez_alternativo, usar_intervalo_alternativo, singularizador) "
                    . "VALUES ( $1, $2, $3, $4, $5, $6, $7 )";
            
            return $manager( $query, $params, $extra = null );
            
        }
        
    }
    
    return null;
}
/**
 * Function that assign a role to an user in the previous system.
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see _core_security_get_previous_system_role( ... ) in gets.php
 * @see core_periods_get_current_period( ... ) in core of periods.
 * @see _core_user_assigned_in_previous_system( ... ) in gets.php
 * @see get_db_manager( ... ) in query_manager.php
 * 
 * @param integer $user_id Moodle user id.
 * @param integer|string|object $role Role id or alias (name).
 * @param object $singularizer Filters to select.
 * 
 * @throws Exception If id_instancia doesn't exist in singularizer.
 * @throws Exception If role param isn't an integer, string or object
 * 
 * @return integer Response of DB manager.
 */
function secure_assign_role_to_user_previous_system( $user_id, $role, $singularizer ){

    /*Singularizer
     *
     * estado (DEFAULT = 1)
     * id_semestre (DEFAULT = current )
     * id_jefe 
     * id_instancia (REQUIRED)
     * id_programa
     * 
     */
    
    if( !isset( $singularizer['id_instancia'] ) ){
        throw new Exception( "id_instancia must be defined at sigularizator", -1 );
    }

    $role_id = -1;
    
    switch ( gettype( $role ) ) {
    	case 'string':
            $role_id = _core_security_get_previous_system_role( $role )['id'];
            break;
    	case 'object':
            $role_id = $role->id;
            break;
    	case 'integer':
            break;
    	default:
            throw new Exception( "role must be an id, role alias or role obj", -2 );
    }
    
    if( !isset($singularizer['id_semestre']) ){
        $singularizer['id_semestre'] = core_periods_get_current_period()->id;
    }

    if( !_core_user_assigned_in_previous_system( $user_id, $role, $singularizer ) ){
    	global $DB_PREFIX;
            
        $manager = get_db_manager();
        $period_id = ( isset($singularizer['id_semestre']) ? $singularizer['id_semestre'] : core_periods_get_current_period()->id );
            
        $tablename = $DB_PREFIX . "talentospilos_user_rol";
        $params = [ 
        	(int) $role_id, 
        	(int) $user_id, 
        	$estado = 1, 
        	(int) $period_id,  
        	(isset($singularizer['id_jefe']) ? (int) $singularizer['id_jefe'] : NULL),
        	(int) $singularizer['id_instancia'],
        	(isset($singularizer['id_programa']) ? (int) $singularizer['id_programa'] : NULL)
        ];
        $query = "INSERT INTO $tablename ( id_rol, id_usuario, estado, id_semestre, id_jefe, id_instancia, id_programa) "
        		. "VALUES ( $1, $2, $3, $4, $5, $6, $7 )";

    	$manager( $query, $params, $extra = null );
    }
   
}

/**
 * Function that remove a role to an user in the system.
 * 
 * Singularizer values
 *
 *  id_semestre (DEFAULT = current )
 *  id_instancia (REQUIRED)
 * 
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see core_periods_get_current_period( ... ) in core of periods.
 * @see _core_security_check_inherited_role( ... ) in checker.php
 * @see _core_security_get_user_rol( ... ) in gets.php
 * @see _core_user_assigned_in_previous_system( ... ) in gets.php
 * @see get_db_manager( ... ) in query_manager.php
 * @see get_db_records( ... ) in query_manager.php
 * @see secure_remove_role_from_user_previous_system( ... ) in entrypoint.php
 * 
 * @param integer $user_id Moodle user id.
 * @param integer|string|object $role Role id or alias (name).
 * @param integer $start_datetime Unix time.
 * @param integer $executed_by Moodle user id.
 * @param object $singularizer Filters to select.
 * 
 * @throws Exception If id_instancia doesn't exist in singularizer.
 * @throws Exception If executed_by is null.
 * @throws Exception If executed_by doesn't exist in {prefix}_user.
 * 
 * @return void
 */
function secure_remove_role_to_user( $user_id, $role, $start_datetime, $executed_by, $singularizer ){
        
    if( !isset( $singularizer['id_instancia'] ) ){
        throw new Exception( "id_instancia must be defined at singularizer", -1 );
    }
    
    if( is_null( $executed_by ) ){
        throw new Exception( "The user who executes must be provided.", -2 );
    }
    
    if( is_null( get_db_records( "user", ["id" => $executed_by] ) ) ){
        throw new Exception( "The user who executes the action ('$executed_by') does not exist", -3 );
    }
    
    if( !isset($singularizer['id_semestre']) ){
        $singularizer['id_semestre'] = core_periods_get_current_period()->id;
    }
    
    $inherited_role = _core_security_check_inherited_role($role);
    
    $remove_master_system = false;
    $remove_previous_system = false;
    
    $assignation_in_master_system = _core_security_get_user_rol( $user_id, $start_datetime, $singularizer );
    $assignation_in_previous_system = NULL;
    
    try{
        //Used to track inconsistencies.
        $assignation_in_previous_system = _core_user_assigned_in_previous_system( $user_id, $role, $singularizer );
    }catch( Exception $ex ){}
    
    if( $inherited_role && $assignation_in_master_system && $assignation_in_previous_system ){
        // Case 1: Asignation exist in both systems
        $remove_master_system = true;
        $remove_previous_system = true;
        
    }else if( !$inherited_role && $assignation_in_master_system && !$assignation_in_previous_system ){
        // Case 2: Asignation exist only in master system and it isn't an inherited role
        $remove_master_system = true;
       
    }else if( !$inherited_role && !$assignation_in_master_system && $assignation_in_previous_system ){
        // Case 3: Asignation exist only in previous system and it isn't an inherited role
        $remove_previous_system = true;
        
    }else if( $inherited_role && $assignation_in_master_system && !$assignation_in_previous_system ){
        // Case 4: Asignation exist only in master system and it is an inherited role
        $remove_master_system = true;
        
    }else if( $inherited_role && !$assignation_in_master_system && $assignation_in_previous_system ){
        // Case 5: Asignation exist only in previous system and it is an inherited role
        $remove_previous_system = true;
    }else{
        // Case 6: Asignation doesn't exist.
        return true;
    }
    
    if( $remove_master_system ){
        global $DB_PREFIX;
            
        $manager = get_db_manager();
        $tablename = $DB_PREFIX . "talentospilos_usuario_rol";
        $params = [
            $estado = 1,
            $remove_datetime = "now()",
            $executed_by,
            $assignation_in_master_system['id']
        ];
        $query = "UPDATE $tablename SET eliminado = $1, fecha_hora_eliminacion = $2, id_usuario_eliminador = $3 WHERE id = $4";
    	$manager( $query, $params, $extra = null );
    }
    
    if( $remove_previous_system ){
        secure_remove_role_from_user_previous_system( $user_id, $role, $singularizer );
    }
    
}

/**
 * Function that remove a role to an user in the previous system.
 * 
 * Singularizer values
 *
 *  id_semestre (DEFAULT = current )
 *  id_instancia (REQUIRED)
 * 
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see _core_security_get_previous_system_role( ... ) in gets.php
 * @see core_periods_get_current_period( ... ) in core of periods.
 * @see _core_user_assigned_in_previous_system( ... ) in gets.php
 * @see get_db_manager( ... ) in query_manager.php
 * 
 * @param integer $user_id Moodle user id.
 * @param integer|string|object $role Role id or alias (name).
 * @param object $singularizer Filters to select.
 * 
 * @throws Exception If id_instancia doesn't exist in singularizer.
 * @throws Exception If role param isn't an integer, string or object
 * 
 * @return integer Response of DB manager. 
 */
function secure_remove_role_from_user_previous_system( $user_id, $role, $singularizer ){
    
    if( !isset( $singularizer['id_instancia'] ) ){
        throw new Exception( "id_instancia must be defined at singularizer", -1 );
    }

    $role_id = -1;
    
    switch ( gettype( $role ) ) {
    	case 'string':
            $role_id = _core_security_get_previous_system_role( $role )['id'];
            break;
    	case 'object':
            $role_id = $role->id;
            break;
    	case 'integer':
            break;
    	default:
            throw new Exception( "role must be an id, role alias or role obj", -2 );
    }
    
    if( !isset($singularizer['id_semestre']) ){
        $singularizer['id_semestre'] = core_periods_get_current_period()->id;
    }

    $assignation = _core_user_assigned_in_previous_system( $user_id, $role, $singularizer );
    if( $assignation ){
    	global $DB_PREFIX;
            
        $manager = get_db_manager();
        $period_id = ( isset($singularizer['id_semestre']) ? $singularizer['id_semestre'] : core_periods_get_current_period()->id );
            
        $tablename = $DB_PREFIX . "talentospilos_user_rol";
        $params = [ 
            $estado = 0,
            $assignation['id'], 
            $period_id
        ];
        $query = "UPDATE $tablename SET estado = $1 WHERE id = $2 AND id_semestre = $3";
    	return $manager( $query, $params, $extra = null );
    }
}

/**
 * Function that update a role to an user in the system.
 * 
 * Singularizer values
 *
 *  id_semestre (DEFAULT = current )
 *  id_instancia (REQUIRED)
 * 
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see get_db_records( ... ) in query_manager.php
 * @see _core_security_get_user_rol( ... ) in gets.php
 * @see _core_user_assigned_in_previous_system( ... ) in gets.php
 * @see _core_security_solve_alternative_interval( ... ) in general_functions.php
 * @see secure_remove_role_to_user( ... ) in entrypoint.php
 * @see secure_assign_role_to_user( ... ) in entrypoint.php
 * 
 * @param integer $user_id Moodle user id.
 * @param integer|string|object $role Role id or alias (name).
 * @param integer $executed_by Moodle id.
 * @param integer $old_start_datetime Unix time.
 * @param integer $old_singularizer Filters to select.
 * @param integer $start_datetime New start time (Unix time).
 * @param integer $end_datetime New end time (Unix time).
 * @param integer $singularizer New filter to select.
 * @param boolean $use_alternative_interval Indicates if an alternative interval must be used.
 * @param json $alternative_interval JSON with the data about the new interval.
 * 
 * @throws Exception If executed_by is null.
 * @throws Exception If doesn't exist an assignation.
 * @throws Exception If executed_by doesn't exist at {prefix}_user.
 * @thross Exception If alternative_interval doesn't have a valid JSON structure.
 * @thross Exception If alternative_interval keys aren't valid.
 * @throws Exception If the new assignation collides with other record.
 * 
 * @return void.
 */
function secure_update_role_to_user( $user_id, $role, $executed_by, 
        $old_start_datetime = NULL, $old_singularizer = NULL,
        $start_datetime = NULL, $end_datetime = NULL, $singularizer = NULL, $use_alternative_interval = false, $alternative_interval = NULL 
    ){
    
    if( is_null( $executed_by ) ){
        throw new Exception( "The user who executes must be provided.", -2 );
    }
    
    if( is_null( get_db_records( "user", ["id" => $executed_by] ) ) ){
        throw new Exception( "The user who executes the action ('$executed_by') does not exist", -3 );
    }
    
    $old_assignation_in_master_system = _core_security_get_user_rol( $user_id, $old_start_datetime, $old_singularizer );
    $old_assignation_in_previous_system = NULL;
    
    try{
        //Used to track inconsistencies.
        $old_assignation_in_previous_system = _core_user_assigned_in_previous_system( $user_id, $old_start_datetime, $old_singularizer );
    }catch( Exception $ex ){}
    
    if( !$old_assignation_in_master_system ){
        throw new Exception( "The user '$user_id' does not have an assignation.", -1 );
    }
    
    if( $use_alternative_interval ){
        try{
            $alt_interval = _core_security_solve_alternative_interval( $alternative_interval_json );
            if( $alt_interval ){
                $start_datetime = strtotime($alt_interval['fecha_hora_inicio']);
                $end_datetime = strtotime($alt_interval['fecha_hora_fin']);
                if( $start_datetime >= $end_datetime ){
                    throw new Exception( "Invalid interval.", -2 );
                }
            }else{
                throw new Exception( "Invalid value to alternative interval or it doesn't exist.", -3 );
            }
        }catch( Exception $ex ){
            throw new Exception( "Invalid value to alternative interval." -4 );
        }
    }
    
    $new_assignation_in_master_system = _core_security_get_user_rol( $user_id, $start_datetime, $singularizer );
    $new_assignation_in_previous_system = NULL;
    
    try{
        //Used to track inconsistencies.
        $new_assignation_in_previous_system = _core_user_assigned_in_previous_system( $user_id, $start_datetime, $singularizer );
    }catch( Exception $ex ){}
        
    if( !is_null( $new_assignation_in_master_system ) ){
        if( $old_assignation_in_master_system['id'] != $new_assignation_in_master_system['id'] ){
            throw new Exception( "The assignment collides with the record '" . $new_assignation_in_master_system['id'] . "'.", -1 );
        }
    }
    
    secure_remove_role_to_user( $user_id, $role, $old_start_datetime, $executed_by, $old_singularizer );
    secure_assign_role_to_user( $user_id, $role, $start_datetime, $end_datetime, $singularizer, $use_alternative_interval, $alternative_interval );
    
}

/**
 * Function that remove a given role by ID or alias.
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see _core_security_check_inherited_roles( ... ) in checker.php
 * @see _core_security_check_role_in_use( ... ) in checker.php
 * @see _core_security_get_role( ... ) in gets.php
 * @see get_db_manager( ... ) in query_manager.php
 * 
 * @param mixed $role mixed Role ID or alias.
 * @param int $exceuted_by User that remove the role.
 * 
 * @throws Exception If the given role has inheritance.
 * @throws Exception If the given role has at least one historical assignation.
 * 
 * @return integer Result of execute the update query.
 */
function secure_remove_role( $role, int $exceuted_by )
{
    $is_father = _core_security_check_inherited_roles( $role );                 // Check if the given role has inherited roles.
    if( !$is_father ){
        
        if( _core_security_check_role_in_use( $role ) ){                        // Exception. If the given role has inheritance.
            throw new Exception( 
                "Cannot be removed a role with at least 
                 one historical assignation.",
                -1 
            );      
        }
        
        $db_role = _core_security_get_role( $role );                            // Get role data.
        
        global $DB_PREFIX;                                                      // Moodle prefix. Ex. mdl_
        $manager = get_db_manager();                                            // Security core database manager.
        $tablename = $DB_PREFIX . "talentospilos_roles";                        // Moodle tablename with Moodle prefix. Ex. mdl_talentospilos_usuario
        $params = [ $db_role['id'],  1, "now()", $exceuted_by ];                // [0] Role id. [1] Status: 1 = Removed. [2] Time when it was removed. [3] User id that makes the acction
            
        $query = "UPDATE $tablename " .                                          // Query to remove in a logical way the record from the Database. See $param var.
            "SET eliminado = $2, " .                                            // Existence status, 0 = exist, 1 = no exist. See $param var.
            "   fecha_hora_eliminacion = $3, ".                                 // Time when was removed. See $param var.
            "   id_usuario_eliminador = $4  ".                                  // User that remove. See $param var.
            "WHERE  ".
            "    id = $1 AND eliminado = 0";                                    // Criteria.
            
        return $manager( $query, $params );                                     // Return of excute the query with Security core database manager.
            
    }else{
        throw new Exception( "A role with inheritance cannot be removed.", -2 );// Exception. If the given role has inheritance.
    }
}

/**
 * Function that assign to a given role the permission over an action (Call).
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see _core_security_get_action( ... ) in gets.php
 * @see _core_security_get_role( ... ) in gets.php
 * @see is_empty_exception( ... ) in general_functions.php
 * @see _core_security_get_role_actions( ... ) in gets.php
 * @see get_db_manager( ... ) in query_manager.php
 * 
 * @param integer|string $call Action (call) ID or alias.
 * @param integer|string $role Role ID or alias.
 * 
 * @throws Exception If the given call already was assigned.
 * 
 * @return integer Result of INSERT with query manager.
 */
function secure_assign_call_to_role( $call, $role )
{
    
    $obj_action = _core_security_get_action( $call );                           // Get action (call) data from database.
    $obj_role = _core_security_get_role( $role );                               // Get role data from database
    is_empty_exception( [ 'action' => $obj_action , 'role' => $obj_role ] );    // Check if role and action exist.
    
    $actions_by_role = _core_security_get_role_actions( $obj_role['id'] );      // Get action assigned to a given role.
    
    $exist = false;                                                             // Assigment exist.
    foreach ( $actions_by_role as &$action ){                                   // Check every acction if is equal to the new acction to assign.
        if( $action['alias'] == $obj_action['alias'] ){                         // Check an action if is equal to the new acction to assign.
            $exist = true;                                                      
            break;
        }
    }
    
    if( !$exist ){                                                              // If doesn't exist the role-action tuple.
        
        global $DB_PREFIX;                                                      // Moodle prefix. Ex. mdl_
        $tablename = $DB_PREFIX . "talentospilos_roles_acciones";               // Moodle tablename with prefix. Ex. mdl_talentospilos_usuarios.
        $manager = get_db_manager();                                            // Get Security core database manager.
        $params = [ $obj_role['id'], $obj_action['id'] ];                       // [0] Role ID. [1] Action (call) ID.
        $query = "INSERT INTO $tablename (id_rol, id_accion) VALUES($1, $2)";   // Query to INSERT a new role-acction tuple.
        
        return $manager( $query, $params );                                     // Return the query execution.
        
    }
        
    throw new Exception( "Assignment already exists", -1 );                     // Exception if already assigned.
    
}

/**
 * Function that remove a tuple role-action(call)
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see _core_security_get_role( ... ) in gets.php
 * @see _core_security_get_action( ... ) in gets.php
 * @see is_empty_exception( ... ) in general_functions.php
 * @see _core_security_check_action_role( ... ) in checker.php
 * @see get_db_manager( ... ) in query_manager
 * 
 * @param integer|string $call Action(call) ID or alias.
 * @param integer|string $role Role ID or alias.
 * @param integer $exec_by User ID that remove the tuple.
 * 
 * @throws Exception If the tuple role-action(call) doesn't exist.
 * 
 * @return mixed Query manager return of execute the update query.
 */
function secure_remove_call_role( $call, $role, int $exec_by )
{
    $obj_role = _core_security_get_role( $role );                               // Get role data from database
    $obj_action = _core_security_get_action( $call );                           // Get action (call) data from database.
    
    is_empty_exception( [ 'role' => $obj_role, 'action' => $obj_action ] );     // Check if role and action exist.
    
    if(_core_security_check_action_role($obj_role['id'], $obj_action['id']) ){
        
        global $DB_PREFIX;                                                      // Moodle prefix. Ex. mdl_
        $manager = get_db_manager();                                            // Security core database manager.
        $tablename = $DB_PREFIX . "talentospilos_roles_acciones";               // Moodle tablename with Moodle prefix. Ex. mdl_talentospilos_usuario
        $params = [ $obj_role['id'], $obj_action['id'],  1, "now()", $exec_by ];// [0] Role id. [1] Action ID. [2] Status: 1 = Removed. [3] Time when it was removed. [4] User id that makes the acction
            
        $query = "UPDATE $tablename " .                                         // Query to remove in a logical way the record from the Database. See $param var.
            "SET eliminado = $3, " .                                            // Existence status, 0 = exist, 1 = no exist. See $param var.
            "   fecha_hora_eliminacion = $4, ".                                 // Time when was removed. See $param var.
            "   id_usuario_eliminador = $5  ".                                  // User that remove. See $param var.
            "WHERE  ".
            "    id_rol = $1 AND id_accion = $2 AND eliminado = 0";             // Criteria.
            
        return $manager( $query, $params );      
        
    }else{
        throw new Exception( "Tuple action(call)-role doesn't exist.", -1);
    }
    
}


/**
 * Function that update name or description from a role. You cannot update the role father or alias.
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @param mixed $role Role ID or alias.
 * @param string $name New name. Use NULL if you won't update this value, or '' if you want update this value to NULL.
 * @param string $description Use NULL if you won't update this value, or '' if you want update this value to NULL.
 * 
 * @throws Exception If the name and description are both NULL. NULL means no change, use '' if you want update this value to NULL.
 * 
 * @return mixed Result of query manager.
 */
function secure_update_role( $role, string $name = NULL, string $description = NULL ){
    
    if(is_null( $name ) && is_null( $description ) ){                           // Throw an exception if no changes.
        throw new Exception( 
            "NULL means 'without changes', ".                                   // Exception message.
            "if you want update to NULL use an ".
            "empty string '' instead.", 
             -1                                                                 // Exception code.
        );
    }
    
    $db_role = _core_security_get_role( $role );                                // Get role data.
    is_empty_exception( ['db_role' => $db_role] );                              // Check is data isn't empty or NULL.
    
    $new_name = (                                                               // Check for a new name.
        is_null( $name ) ?                                                      // If the new name is NULL, means no change.
        $db_role['nombre'] :                                                    // If true old name keep.
        ( 
            $name === '' ?                                                      // Check if the new name is NULL.
            NULL :                                                              // If new name is an empty string, the new name is NULL.
            $name                                                               // If new name is a non empty string, $new_name = $name.
        )                                         
    );
    
    $new_description = (                                                        // Check for a new name.
        is_null( $description ) ?                                               // If the new name is NULL, means no change.
        $db_role['descripcion'] :                                               // If true old name keep.
        ( 
            $description === '' ?                                               // Check if the new name is NULL.
            NULL :                                                              // If new name is an empty string, the new name is NULL.
            $description                                                        // If new name is a non empty string, $new_name = $name.
        )                                         
    );
    
    
    global $DB_PREFIX;                                                          // Moodle prefix. Ex. mdl_
    $manager = get_db_manager();                                                // Security core database manager.
    $tablename = $DB_PREFIX . "talentospilos_roles";                            // Moodle tablename with Moodle prefix. Ex. mdl_talentospilos_usuario
    $params = [ $db_role['id'], $new_name, $new_description ];                  // [0] Role id. [1] New role name. [2] New role description.
            
    $query = "UPDATE $tablename " .                                             // Query to update a given role in the Database. See $param var.
        "SET nombre = $2, " .                                                   // New name.
        "   descripcion = $3 ".                                                 // New description.
        "WHERE  ".
        "    id = $1 AND eliminado = 0";                                        // Criteria.
            
    return $manager( $query, $params );  
    
}


/**
 * Function that update name or description from an action. You cannot update the action alias.
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @param mixed $call Action(call) ID or alias.
 * @param string $name New name. Use NULL if you won't update this value, or '' if you want update this value to NULL.
 * @param string $description Use NULL if you won't update this value, or '' if you want update this value to NULL.
 * @param integer $log Use NULL if you won't update this value.
 * 
 * @throws Exception If the name and description are both NULL. NULL means no change, use '' if you want update this value to NULL.
 * 
 * @return mixed Result of query manager.
 */
function secure_update_action( $call, string $name = NULL, string $description = NULL, bool $log = NULL ){
    
    if(is_null( $name ) && is_null( $description ) && is_null($log) ){                           // Throw an exception if no changes.
        throw new Exception( 
            "NULL means 'without changes', ".                                   // Exception message.
            "if you want update to NULL use an ".
            "empty string '' instead.", 
             -1                                                                 // Exception code.
        );
    }
    
    $db_call = _core_security_get_action( $call );                              // Get role data.
    is_empty_exception( ['db_call' => $db_call] );                              // Check is data isn't empty or NULL.
    
    $new_name = (                                                               // Check for a new name.
        is_null( $name ) ?                                                      // If the new name is NULL, means no change.
        $db_call['nombre'] :                                                    // If true old name keep.
        ( 
            $name === '' ?                                                      // Check if the new name is NULL.
            NULL :                                                              // If new name is an empty string, the new name is NULL.
            $name                                                               // If new name is a non empty string, $new_name = $name.
        )                                         
    );
    
    $new_description = (                                                        // Check for a new name.
        is_null( $description ) ?                                               // If the new name is NULL, means no change.
        $db_call['descripcion'] :                                               // If true old name keep.
        ( 
            $description === '' ?                                               // Check if the new name is NULL
            NULL :                                                              // If new name is an empty string, the new name is NULL.
            $description                                                        // If new name is a non empty string, $new_name = $name
        )                                         
    );
    
    $new_log = (                                                                // Check for a new log configuration.
        is_null( $log ) ?                                                       // If the new log is NULL, means no change.
        $db_call['registra_log'] :                                              // If true old name keep
        ($new_log ? 1 : 0 )                                                     // Explicit conversion.
    );
    
    global $DB_PREFIX;                                                          // Moodle prefix. Ex. mdl_
    $manager = get_db_manager();                                                // Security core database manager.
    $tablename = $DB_PREFIX . "talentospilos_acciones";                         // Moodle tablename with Moodle prefix. Ex. mdl_talentospilos_usuario
    $params = [ $db_call['id'], $new_name, $new_description, $new_log ];        // [0] Action id. [1] New action name. [2] New action description. [3] New log configuration.
            
    $query = "UPDATE $tablename " .                                             // Query to update a given action in the Database. See $param var.
        "SET nombre = $2, " .                                                   // New name.
        "   descripcion = $3, ".                                                // New description.
        "   registra_log = $4 ".                                                // New log configuration.
        "WHERE  ".
        "    id = $1 AND eliminado = 0";                                        // Criteria.
            
    return $manager( $query, $params );  
    
}

/**
 * Function that find one 'key' for sign a transaction.
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @param string $explicit_hexed_rule Hash rule in string.
 * 
 * @return string Key.
 */
function secure_find_key( string $explicit_hexed_rule = NULL ): string
{
    if( is_null($explicit_hexed_rule) ){
        throw new Exception( "Rule cannot be empty", -1 );
    }
    if( $explicit_hexed_rule == "" ){
        throw new Exception( "Rule cannot be empty", -1 );
    }
    if( !valid_explicit_hex_value( $explicit_hexed_rule ) ){
        throw new Exception( "Sorry, the rule isn't a hex value.", -2 );
    }
    if( strlen($explicit_hexed_rule) > 5 ){
        throw new Exception( "Security exception. Max. rule size is 5.", -3 );
    }
    
    $rule_size = strlen( $explicit_hexed_rule );
    $iteration_key_size_control = 6;                                            // Minimun characters size for a key.
    $total_characters = 62;                                                     // 0-9a-zA-Z Number of valid characters for a key.
    $max_iterations = combinations_with_repetition(                             // Key size optimizator
        $total_characters, $iteration_key_size_control
    );
    
    $iteration_counter = 0;
    while( true ){
        
        $tmp_key = generate_random_string( $iteration_key_size_control );       // Get candidate (random string) for key.
        $hash = hash("sha512", $tmp_key);                                       // Hash of calculate.
        
        if( $explicit_hexed_rule == substr($hash, 0, $rule_size ) ){
            return $tmp_key;
        }else{
            $iteration_counter++;
            if( $iteration_counter === $max_iterations ){
                $iteration_key_size_control++;
                $max_iterations = combinations_with_repetition(
                    $total_characters, $iteration_key_size_control
                );
            }
        }
        
    }
    
}

?>