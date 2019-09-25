<?php
/**
 * @package	block_ases
 * @subpackage	core.security
 * @author 	Jeison Cardona Gómez
 * @copyright 	(C) 2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//General configuration
const VERSION = 1; //Current version.

require_once( __DIR__ . "/../../../../config.php");
require_once( __DIR__ . "/../module_loader.php");

global $DB_PREFIX;
$DB_PREFIX = $GLOBALS[ 'CFG' ]->prefix;

//Specific configuration
const CORE_PREFIX = "core_secure_render"; // example: {{#core_secure_render_block_to_protect}}

/** @var SUPPORT_TO_PREVIOUS_SYSTEM Flag to operate the old system in 
 * a parallel way */
const SUPPORT_TO_PREVIOUS_SYSTEM = TRUE;

require_once( __DIR__ . "/v" . VERSION . "/entrypoint.php");

/**
 * Function that given a function name, array of arguments, context, user id, singularizations and time, 
 * checks if the user ave permission to execute the function, if can, secure call returns the executed 
 * function, makes a log of this, if this is indicated, else, prevent that function can be executed. 
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
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @see secure_Call( ... ) in entrypoint.php
 *
 * @param string $function_name
 * @param array $args
 * @param array $context
 * @param integer $user_id
 * @param array $singularizations
 * @param integer $time_context
 *
 * @return mixed
 *
 */
function core_secure_call( $function_name, $args = null, $context = null, $user_id = null, $singularizations = null, $time_context = null ){
    return secure_Call( $function_name, $args, $context, $user_id, $singularizations, $time_context ); 
}

function core_secure_render( &$data, $user_id = null, $singularizations = null, $time_context = null ){
    return secure_render( $data, $user_id, $singularizations, $time_context ); 
}

function core_secure_template_checker( $dir ){
    return secure_template_checker( $dir );
}

/**
 * Interface to secure_call_checker
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see secure_call_checker( ... ) in entrypoint.php
 * 
 * @param $managers_dir Managers location, typical location '/block/ases/managers'
 * 
 * @return array List of call aliases that does not exist at the database
 */
function core_secure_call_checker( $managers_dir ){
    return secure_call_checker( $managers_dir );
}

/**
 * Interface to secure_create_call
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see secure_create_call( ... ) in entrypoint.php
 * 
 * @param string $alias Action alias
 * @param integer|string $action_type Identifier or type alias (back or front)
 * @param string $name Action name
 * @param string $description Action description
 * @param integer $log Allow store every return
 * 
 * @return integer|null If the operation was correct, it will be return 1
 */
function core_secure_create_call($alias, $action_type, $name = NULL, $description = NULL, $log = 0){
    return secure_create_call($alias, $action_type, $name, $description, $log);
}



/**
 * Interface to secure_remove_call
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see secure_remove_call( ... ) in entrypoint.php
 * 
 * @param integer|string $alias Action alias or identifier
 * @param integer $user_id User Moodle id
 * 
 * @return integer|null If the operation was correct, return 1
 */
function core_secure_remove_call( $alias, $user_id ){
    return secure_remove_call( $alias, $user_id );
}

/**
 * Interface to secure_create_role
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see secure_create_role( ... ) in entrypoint.php
 * 
 * @param string $alias Role alias
 * @param integer|string $father_role Role id or alias
 * @param string $name Role name
 * @param string $description
 * 
 * @return integer|null If the operation was correct, return 1
 */
function core_secure_create_role( $alias, $father_role = -1, $name = NULL, $description = NULL ){
    return secure_create_role( $alias, $father_role, $name, $description );
}

/**
 * Interface to secure_assign_role_to_user
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see secure_assign_role_to_user( ... ) in entrypoint.php
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
function core_secure_assign_role_to_user( $user_id, $role, $start_datetime = NULL, $end_datetime = NULL, $singularizer = NULL, $use_alternative_interval = false, $alternative_interval = NULL ){
    return secure_assign_role_to_user( $user_id, $role, $start_datetime, $end_datetime, $singularizer, $use_alternative_interval, $alternative_interval );
}

/**
 * Interface to secure_remove_role_to_user
 *  
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see secure_remove_role_to_user( ... ) in entrypoint.php
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
function core_secure_remove_role_to_user( $user_id, $role, $start_datetime, $executed_by, $singularizer = NULL ){
    return secure_remove_role_to_user( $user_id, $role, $start_datetime, $executed_by, $singularizer );
}

function core_secure_update_role_to_user( $user_id, $role, $executed_by, 
        $old_start_datetime = NULL, $old_singularizer = NULL,
        $start_datetime = NULL, $end_datetime = NULL, $singularizer = NULL, $use_alternative_interval = false, $alternative_interval = NULL 
   ){
    return secure_update_role_to_user( $user_id, $role, $executed_by, $old_start_datetime, $old_singularizer, $start_datetime, $end_datetime, $singularizer, $use_alternative_interval, $alternative_interval );
}

/**
 * Interface to secure_remove_role
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see secure_remove_role( ... ) in entrypoint.php
 * 
 * @param mixed $role mixed Role ID or alias.
 * @param int $exceuted_by User that remove the role.
 * 
 * @return integer Result of execute the update query.
 */
function core_secure_remove_role( $role, int $exceuted_by ){
    return secure_remove_role( $role, $exceuted_by );
}

/**
 * Interface to secure_assign_call_to_role
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see secure_assign_call_to_role( ... ) in entrypoint.php
 * 
 * @param integer|string $call Action (call) ID or alias.
 * @param integer|string $role Role ID or alias.
 * 
 * @return integer Result of INSERT with query manager.
 */
function core_secure_assign_call_to_role( $call, $role ){
    return secure_assign_call_to_role( $call, $role );
}

/**
 * Interface to secure_remove_call_role
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see secure_remove_call_role( ... ) in entrypoint.php
 * 
 * @param integer|string $call Action(call) ID or alias.
 * @param integer|string $role Role ID or alias.
 * @param integer $exec_by User ID that remove the tuple.
 * 
 * @return mixed Query manager return of execute the update query.
 */
function core_secure_remove_call_role( $call, $role, int $exec_by ){
    return secure_remove_call_role( $call, $role, $exec_by );
}


/**
 * Interface to core_secure_update_role
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @see  secure_update_role( ... ) in entrypoint.php
 * 
 * @param mixed $role Role ID or alias.
 * @param string $name New name. Use NULL if you won't update this value, or '' if you want update this value to NULL.
 * @param string $description Use NULL if you won't update this value, or '' if you want update this value to NULL.
 *  
 * @return mixed Result of query manager.
 */
function core_secure_update_role( $role, string $name = NULL, string $description = NULL ){
    return secure_update_role( $role, $name, $description );
}


/**
 * Interface to core_secure_update_action
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @see  secure_update_action( ... ) in entrypoint.php
 * 
 * @param mixed $call Action(call) ID or alias.
 * @param string $name New name. Use NULL if you won't update this value, or '' if you want update this value to NULL.
 * @param string $description Use NULL if you won't update this value, or '' if you want update this value to NULL.
 * @param integer $log Use NULL if you won't update this value.
 * 
 * @return mixed Result of query manager.
 */
function core_secure_update_action( $call, string $name = NULL, string $description = NULL, bool $log = NULL ){
    return secure_update_action( $call, $name, $description, $log );
}

?>
