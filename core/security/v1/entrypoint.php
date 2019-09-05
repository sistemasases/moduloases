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
 * @see get_db_records( ... ) in query_manager.php
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

    $_user = get_db_records( "user", ['id'], [$user_id] );
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
 * @see _core_check_inherited_role( ... ) in checker.php
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
    
    if( is_null( get_db_records( "user", ["id"], [$executed_by] ) ) ){
        throw new Exception( "The user who executes the action ('$executed_by') does not exist", -3 );
    }
    
    if( !isset($singularizer['id_semestre']) ){
        $singularizer['id_semestre'] = core_periods_get_current_period()->id;
    }
    
    $inherited_role = _core_check_inherited_role($role);
    
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
    
    if( is_null( get_db_records( "user", ["id"], [$executed_by] ) ) ){
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


?>