<?php 
/**
 * @package	block_ases
 * @subpackage	core.periods
 * @author 	David Santiago Cortés
 * @copyright 	(C) 2021 David S. Cortés <david.cortes@correounivalle.edu.co>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Function that return the current (last by end date) period according to its instanceid field.
 * If no instace_id is given then NULL or 450299 assumed.
 * @author  David Santiago Cortés <david.cortes@correounivalle.edu.co>
 * @since  2.0.0
 * @param int $instance_id Period's instance id
 * @return  stdClass | null Return the last period.
 */
function periods_get_current_period( $instance_id  ){

    global $DB;
    global $PERIODS_TABLENAME;
    
    $query  ="
            SELECT * FROM $PERIODS_TABLENAME 
            WHERE id_instancia=$instance_id
            ORDER BY fecha_fin DESC
            LIMIT 1";
    
    $result = $DB->get_record_sql( $query );
    return ( isset($result->id) ? $result : null );
}


/**
 * Function that return a period by ID.
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @param integer $period_id Period ID.
 * 
 * @throws Exception If doesn't exist a period with the given ID.
 * 
 * @return stdClass Period object
 */
function periods_get_period_by_id( int $period_id ):stdClass
{
    global $DB;
    global $PERIODS_TABLENAME;
    
    $query = "
        SELECT * 
        FROM $PERIODS_TABLENAME
        WHERE id = '$period_id'";
    
    $result = $DB->get_record_sql( $query );
    if( !property_exists( $result, 'id' ) ){
        throw new Exception( 
            "Period with ID '$period_id' does not exist.", -1 
        );
    }else{
        return $result;
    }
}

/**
 * Returns a period given its name and instance id.
 * Since periods before (including) 2019B does not have an instanceid,
 * additional checks takes place.
 *
 * @author David S. Cortés - <david.cortes@correounivalle.edu.co>
 * @since 2.0.0
 *
 * @param string $period_name
 * @param int $instance_id 
 * @return object 
 */
function periods_get_period_by_name($period_name, $instance_id)
{
    global $DB;
    global $PERIODS_TABLENAME;

    $query = "
        SELECT *
        FROM $PERIODS_TABLENAME
        WHERE nombre = '$period_name' ";


    if (strpos($period_name, '202') !== false) {
        $query .= "AND id_instancia = $instance_id";         
    } 

    $result = $DB->get_record_sql( $query );
    
    return $result;
}

/**
 * Returns a period given its start and end dates
 *
 * @author David S. Cortés - <david.cortes@correounivalle.edu.co>
 *
 * @param time $fecha_inicio
 * @param time $fecha_fin	
 * @param int $instance_id. Period's instance
 * @param bool $relax_query. If set to true, the function returns all periods
 * between the start and end date. If set to false, it will return an exact match.
 *
 * @return stdClass
 * @throws Exception if there's no period with those dates.
 */
function periods_get_period_by_date($fecha_inicio, $fecha_fin, $relax_query=false, $instance_id=null)
{
	global $DB;
	global $PERIODS_TABLENAME;

	$query = "SELECT * FROM $PERIODS_TABLENAME "; 
    if (is_null($instance_id)) {
       $query .= "WHERE id_instancia is NULL "; 
    } else {
        $query .= "WHERE id_instancia = $instance_id ";  
    }


    if (is_null($fecha_fin)) {
	    $fecha_fin = date('Y-m-d');
    }

	if( $relax_query ){
        $query .= 
            "AND fecha_inicio >= '$fecha_inicio' 
            AND fecha_fin <= '$fecha_fin'
            ORDER BY fecha_fin";
        
	    $result = $DB->get_records_sql( $query );
	}
	else {
		$query .= "AND fecha_inicio = '$fecha_inicio' AND fecha_fin = '$fecha_fin'";
        
        $result = $DB->get_record_sql( $query );
        
        if( !property_exists($result, 'id') ) {
		    throw new Exception ( 
				"Period(s) with start date '$fecha_inicio' and end date '$fecha_fin' 
				does not exists.", -1
			);
	    }
	}

    return $result;
}

/** 
 * Todos los periodos antes del 2019-2 no tienen instancia, por ende
 * la forma de traer todos los periodos sin que hayan duplicados es la sgte:
 *
 * - Traer todos los períodos que no tienen instancia (del 2019B para atrás)
 * - Traer todos los periodos del 2020A en adelante con la instancia proporcionada.
 * - Unir ambos resultados con array_merge.
 *
 * El parametro $instance_id lo dejé opcional por si en algún momento algún miembro
 * del equipo necesita o quiere usar la función y ver absolutamente todos los periodos.
 * Esto solo para temas de debugging.
 * 
 * @author David S. Cortés <david.cortes@correounivalle.edu.co>
 * @since 2.0.0
 * 
 * @param int Instance id
 * @return array List of periods.
 */
function periods_get_all_periods( $instance_id='NULL' ):array
{
    global $DB;
    global $PERIODS_TABLENAME; 
    
    try {
        
        $p2019b = periods_get_period_by_name('2019B', $instance_id); 
        $p2015a = periods_get_period_by_name('2015A', $instance_id);
        $periods_before_2019B = periods_get_period_by_date($p2015a->fecha_inicio, $p2019b->fecha_fin, true);

        $query = 
            "SELECT * FROM $PERIODS_TABLENAME 
            WHERE id_instancia = $instance_id
            ORDER BY fecha_fin";

        $periods = $DB->get_records_sql( $query );

        $all_periods = array_merge($periods_before_2019B, $periods);
        
        return $all_periods;

    } catch(Exception $ex) {
        throw new Exception($ex->getMessage()); 
    }
}

/**
 * Function that return the last period in the database under a certain
 * instance.
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @return stdClass Last period. 
 */
function periods_get_last_period( int $instance_id ):stdClass
{
    global $DB;
    global $PERIODS_TABLENAME;
    
    $query = "
        SELECT * 
        FROM $PERIODS_TABLENAME 
        WHERE id_instancia = $instance_id
        ORDER BY fecha_fin DESC LIMIT 1";
    
    return $DB->get_record_sql( $query );
}

/**
 * Function that check if exist a period given an ID.
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @param integer $period_id Period ID.
 * 
 * @return bool True if exist.
 */
function periods_check_if_exist( int $period_id ): bool
{
    try {
        periods_get_period_by_id( $period_id );
        return true;
    } catch (Exception $exc) {
        return false;
    }
}

/**
 * Function that updates a given period's information.
 *
 * @author David S. Cortés <david.cortes@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @param Array $period_info New information.
 * @param integer $period_id Period ID.
 *
 * @return True if operation was successful, false otherwise.
 *
 * @throws Exception.
 */
function periods_update_period( $period_info, $period_id ){
	global $DB;
    	global $PERIODS_TABLENAME;
	try {
		$period = new stdClass();
		
		$period->id = (int)$period_id;
		$period->nombre = $period_info[1];
		$period->fecha_inicio = $period_info[2];
		$period->fecha_fin = $period_info[3];
		if($period->id == 0){
            trigger_error('ASES Notificacion: actualizar periodo en la BD con id 0');
            return 0;
        }
		$result = $DB->update_record(substr($PERIODS_TABLENAME,4), $period);
		return $result;
	} catch (Exception $ex){
		throw new Exception($ex);
	}
}

/**
 * Creates a new period.
 *
 * @param string $name. Period's name.
 * @param time $fecha_inicio. Period's start date.
 * @param time $fecha_fin. Period's end date.
 *
 * @return stdClass with new period.
 * @throws Exception if there is an existing period with the same name.
 */
function periods_create_period( $nombre, $fecha_inicio, $fecha_fin, $instance_id ){
	global $DB;
	global $PERIODS_TABLENAME;

	try {
		if( !periods_get_period_by_name($nombre, $instance_id) ){
			$new_period = new stdClass();
			$new_period->nombre = $nombre;
			$new_period->fecha_inicio = $fecha_inicio;
			$new_period->fecha_fin = $fecha_fin;
            $new_period->id_instancia = $instance_id;

			$result = $DB->insert_record(substr($PERIODS_TABLENAME, 4), $new_period);

			return $result;
		}
		else {
			Throw new Exception("Ya existe periodo con ese nombre en la instancia dada", -1);
		}
	}
	catch ( Exception $ex ){
		return $ex->getMessage();
	}
}
