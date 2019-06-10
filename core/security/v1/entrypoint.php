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
 * @return array
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
            $unsolved_secured_calls = array_intersect($calls, $stored_alias_actions);
        }
    }
        
    return $unsolved_secured_calls;
}

?>