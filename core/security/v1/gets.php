<?php
/**
 * @package		block_ases
 * @subpackage	core.security
 * @author 		Jeison Cardona Gómez
 * @copyright 	(C) 2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once( __DIR__ . "/query_manager.php");

/**
 * Function that returns an action given an id or alias.
 *
 * @see get_db_manager() in query_manager.php
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @param mixed $in, this input param can be and id (Integer) or alias (String)
 *
 * @return object|null
*/
function _core_security_get_action( $in ){

	global $DB_PREFIX;

	$params = [];
	$criteria = null;
	$tablename = $DB_PREFIX . "talentospilos_acciones";

	if( $in ){
		if( is_numeric($in) ){
			$criteria = "id";
		}else if( is_string( $in ) ){
			$criteria = "alias";
		}else{
			return null;
		}
	}else{
		return null;
	}

	array_push($params, $in);

	$manager = get_db_manager();
	$action = $manager( $query = "SELECT * FROM $tablename WHERE $criteria = $1 AND eliminado = 0", $params, $extra = null );
	return ( count( $action ) == 1 ? $action[0] : null );

}

/**
 * Function that returns actions given a role_id.
 *
 * @see get_db_manager() in query_manager.php
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @param integer $user_id
 *
 * @return array
*/
function _core_security_get_role_actions( $role_id, $type = null ){

	global $DB_PREFIX;

	$params = [];
	$type_filter = null;
	$tablename = $DB_PREFIX . "talentospilos_roles_acciones";
	$actions_tablename = $DB_PREFIX . "talentospilos_acciones";

	if( is_numeric($role_id) ){
		array_push($params, $role_id);
		if( !is_null( $type ) ){
			if( is_numeric( $type ) ){
				$type_filter = "AND id_tipo_accion = $2";
				array_push($params, $type);
			}else{
				return null;
			}
		}
	}else{
		return null;
	}

	$manager = get_db_manager();
	$query = "SELECT * 
	FROM $actions_tablename AS A 
	INNER JOIN $tablename AS RA 
	ON RA.id_accion = A.id 
	WHERE RA.id_rol = $1 AND RA.eliminado = 0 AND A.eliminado = 0 $type_filter";

	$role_actions = $manager( $query, $params, $extra = null );
	return ( count( $role_actions ) >= 1 ? $role_actions : null );

}

/**
 * Function that returns actions.
 *
 * @see get_db_manager() in query_manager.php
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @param integer|string Action type id or alias 
 *
 * @return array
*/
function _core_security_get_actions( $type = null ){

	global $DB_PREFIX;

	$type_filter = null;
	$params = [];
	$tablename = $DB_PREFIX . "talentospilos_acciones";

	if( !is_null( $type ) ){
            $action_type = _core_security_get_action_type($type);
            $type_filter = "AND id_tipo_accion = $1";
            array_push($params, $action_type['id']);
	}

	$manager = get_db_manager();
	$actions = $manager( $query = "SELECT * FROM $tablename WHERE eliminado = 0 $type_filter", $params, $extra = null );
	return ( count( $actions ) >= 1 ? $actions : null );

}

/**
 * Function that returns actions types.
 *
 * @see get_db_manager() in query_manager.php
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @param integer $user_id
 *
 * @return array
*/
function _core_security_get_action_type( $alias = null ){

	global $DB_PREFIX;
        
	$params = [];
	$alias_filter = null;
	$tablename = $DB_PREFIX . "talentospilos_tipos_accion";

	if( !is_null( $alias ) ){
		if( is_string( $alias ) ){
			if( $alias != "" ){
				$alias_filter = "WHERE alias = $1";
				array_push($params, $alias);
			}else{
				return null;
			}
		}
	}

	$manager = get_db_manager();
	$type = $manager( $query = "SELECT * FROM $tablename $alias_filter", $params, $extra = null );
	return ( count( $type ) >= 1 ? $type : null );

}

/**
 * Function that return a role given an user id.
 *
 * Singularizers are extra filters.
 *
 * Example:
 *
 * 	array(
 * 		'filter_1' => "value",
 *              'filter_2' => "value"
 * 	)
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @param integer $user_id
 * @param integer $time_context
 * @param array $singularizers
 *
 * @return object|null
*/
function _core_security_get_user_rol( $user_id, $time_context = null, $singularizers = null ){

    global $DB_PREFIX;

    $params = [];
    $tablename = $DB_PREFIX . "talentospilos_usuario_rol";

    if( !is_numeric($user_id) ){
        return false;
    }

    if( is_null( $time_context ) ){
        $time_context = time();
    }
        
    array_push($params, $user_id);

    $manager = get_db_manager();
    $user_roles = $manager( $query = "SELECT * FROM $tablename WHERE id_usuario = $1 AND eliminado = 0", $params, $extra = null );
    
    foreach ($user_roles as $key => $u_rol) {
        
        $rol = new stdClass();
        		
        if( 
            ( $u_rol['usar_intervalo_alternativo'] == 0 ) && 
            ( !is_null( $u_rol['fecha_hora_inicio'] ) ) && 
            ( !is_null( $u_rol['fecha_hora_fin'] ) )
        ){
            $rol->start = strtotime($u_rol['fecha_hora_inicio']);
            $rol->end = strtotime($u_rol['fecha_hora_fin']);
        }else if( 
            ( $u_rol['usar_intervalo_alternativo'] == 1 ) && 
            ( !is_null( $u_rol['usar_intervalo_alternativo'] ) )
        ){
            
            $alternative_interval = _core_security_solve_alternative_interval( $u_rol['intervalo_validez_alternativo'] );
            if( $alternative_interval ){
                $rol->start = strtotime($alternative_interval['fecha_hora_inicio']);
                $rol->end = strtotime($alternative_interval['fecha_hora_fin']);
            }
        }
                
        $valid_singularization = true;

        if( !is_null($u_rol['singularizador']) ){
           
            $db_singularizers = (array) json_decode($u_rol['singularizador']);
            if( count( $singularizers ) === count( $db_singularizers ) ){
                
                foreach ($db_singularizers as $key => $db_singularization) {
                    if( array_key_exists($key, $singularizers) ){
                        if( $db_singularization !== $singularizers[ $key ] ){
                            $valid_singularization = false;
                            break;
                        }
                    }else{
                        $valid_singularization = false;
                        break;
                    }
                }
                
            }else{
                 $valid_singularization = false;
            }      
        }

        if( ($time_context >= $rol->start) && ($time_context <= $rol->end) && $valid_singularization ){
            return $u_rol;
        }
    }

    return null;
}

/**
 * Function that given a role id or alias, return the role array
 * 
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see get_db_manager() in query_manager.php
 * 
 * @param integer|string Role id or alias
 *
 * @return array
*/
function _core_security_get_role( $in ){

	global $DB_PREFIX;

	$params = [];
	$tablename = $DB_PREFIX . "talentospilos_roles";
        $criteria = "id";
        
	if( !is_numeric( $in ) ){
            $criteria = "alias";
	}

	array_push($params, $in);

	$manager = get_db_manager();
        $query = $query = "SELECT * FROM $tablename WHERE $criteria = $1 AND eliminado = 0";
	$role = $manager( $query, $params, $extra = null );
	
	return ( count( $role ) == 1 ? $role[0] : null );

}

/**
 * Function that, given a role name return a role object.
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @param string $role_name Role name to find.
 * 
 * @return NULL | array Null if no exist.
 */
function _core_security_get_previous_system_role( $role_name ){
    
    global $DB_PREFIX;
            
    $manager = get_db_manager();
    
    $tablename = $DB_PREFIX . "talentospilos_rol";
    $query = "SELECT * FROM $tablename WHERE nombre_rol = $1";
    $result = $manager( $query, [ $role_name ] );
    return ( count( $result ) == 1 ? $result[0] : null );
    
}

/**
 * Function that check role assignations in the previous system.
 * 
 * Singularizer
 *
 * estado (DEFAULT = 1)
 * id_semestre (DEFAULT = current )
 * id_jefe 
 * id_instancia (REQUIRED)
 * id_programa
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see core_periods_get_current_period( ... ) in core periods.
 * @see get_db_manager( ... ) in query_manager.php
 * 
 * @param integer $user_id Moodle user id.
 * @param string $role Role alias (Name in the previous system).
 * @param object $singularizer Filter to select.
 * 
 * @return NULL | array NULL if the assignation doesn't exist.
 */
function _core_user_assigned_in_previous_system( $user_id, $role, $singularizer ){
    
    global $DB_PREFIX;
            
    $manager = get_db_manager();

    $obj_role = _core_security_get_previous_system_role( $role );
    
    $period_id = ( isset($singularizer['id_semestre']) ? $singularizer['id_semestre'] : core_periods_get_current_period()->id );
    $where = 
    	" id_instancia = " . $singularizer['id_instancia'] . 
    	" AND id_rol = " . $obj_role['id'] . 
    	" AND estado = 1 " . 
    	" AND id_semestre = '$period_id' " . 
    	" AND id_usuario = '$user_id' ";

    foreach ($singularizer as $key => $value) {
    	if( $key == "id_semestre" ){ 
    		continue; 
    	}else{
    		$where .= "AND $key = '$value'";
    	}
    }
    
    $tablename = $DB_PREFIX . "talentospilos_user_rol";
    $query = "SELECT * FROM $tablename WHERE $where";
    $result = $manager( $query, [] );

    return ( count( $result ) == 1 ? $result[0] : null );

}

/**
 * Function that get all inherited role from a given role.
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see _core_security_get_role( ... ) in gets.php
 * @see get_db_manager( ... ) in query_manager.php
 * 
 * @param mixed $role Role id or alias.
 * 
 * @throws Exception If the given role doesn't exist in the system.
 * 
 * @return array Inherited roles.
 */
function _core_security_get_inherited_roles( $role ): array
{
    
    $db_role = _core_security_get_role( $role );                                // Get role data.
    if( is_null( $role ) ){                                                     // Throw new exception is the given role doesn't exist.
        throw new Exception( "Role '$role' doesn't exist.", -1 );               // Exception if role doesn't exist
    }
    
    global $DB_PREFIX;                                                          // Moodle DB prefix. Ex. mdl_                           
    $tablename = $DB_PREFIX . "talentospilos_roles";                            // Moodle tablename. Ex. mdl_talentospilos_user
    $params = [ $db_role['id'] ];                                               // Params to query. [0] Role id.
    
    $manager = get_db_manager();                                                // Security core database manager.
    return $manager(                                                            // Roles that inherit from role given.
        "SELECT * FROM $tablename WHERE id_rol_padre = $1 AND eliminado = 0",    // DB query to get inherited roles.
        $params                                                                 // Query params. $1 is assigned to $params[0]
    );
    
}

/**
 * Function that return every assignation over a given role, even if was removed.
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co> 
 * @since 1.0.0
 * 
 * @param mixed $role Role ID or alias.
 * 
 * @return array|null List of assignations whit the given role. Null if empty.
 */
function _core_security_get_historical_role_assignation( $role ){
    
    $db_role = _core_security_get_role( $role );                                // Get role data.
    if( is_null( $db_role ) ){                                                     // Throw new exception is the given role doesn't exist.
        throw new Exception( "Role '$role' doesn't exist.", -1 );               // Exception if role doesn't exist
    }
    
    return get_db_records(                                                      // Get every assignation, deleted records included.
            "talentospilos_usuario_rol",                                        // Tablename without prefix.
            [ 'id_rol' => $db_role['id'] ]                                      // Criteria
    );          
    
}

/**
 * Function that return a role-action tuple.
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see get_db_records( ... ) in query_manager.php
 * 
 * @param integer $role_id Role ID.
 * @param integer $action_id Action ID.
 * 
 * @return NULL|array Array with role-action tuple data.
 */
function _core_security_get_action_role( int $role_id, int $action_id ){
    $role_action = get_db_records(                                              // Get role-action tuple.
        "talentospilos_roles_acciones",                                         // Tablename without prefix.
        [ 'id_rol' => $role_id, 'id_accion' => $action_id, 'eliminado' => 0 ]   // Criteria
    ); 
    return ( is_null( $role_action ) ? NULL : $role_action[0] );                
}

?>
