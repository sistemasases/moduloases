<?php 
/**
 * @package	block_ases
 * @subpackage	core.periods
 * @author 	Jeison Cardona Gómez
 * @copyright 	(C) 2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Function that return the current (last by end date) period
 * @author  Jeison CArdona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since  1.0.0
 * @return  stdClass | null Return the last period.
 */
function periods_get_current_period(){

    global $DB;
    global $PERIODS_TABLENAME;
    
    $query  ="
        SELECT 
            *
        FROM 
            $PERIODS_TABLENAME
        ORDER BY 
            fecha_fin DESC 
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
 * Returns a period given its name.
 * 
 * @param string $period_name 
 * @throws Exception if semester does not exists
 * @return stdClass Period object 
 */
function periods_get_period_by_name($period_name):stdClass
{
    global $DB;
    global $PERIODS_TABLENAME;

    $query = "
        SELECT *
        FROM $PERIODS_TABLENAME
        WHERE nombre = '$period_name'";
    
    $result = $DB->get_record_sql( $query );
    if( !property_exists($result, 'nombre') ) {
        throw new Exception(
            "Period with name '$period_name' does not exist", -1
        );
    }
    else {
        return $result;
    }
}

/** 
 * Function that return all periods.
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @return array List of periods.
 */
function periods_get_all_periods($fecha_inicio=null, $fecha_fin=null):array
{
    global $DB;
    global $PERIODS_TABLENAME;
    
    if (isset($fecha_inicio, $fecha_fin)) {
        $query = "SELECT * 
        FROM $PERIODS_TABLENAME
        WHERE fecha_inicio = $fecha_inicio 
        and fecha_fin = $fecha_fin";
    }
    else {
        $query = "SELECT * 
        FROM $PERIODS_TABLENAME
        ORDER BY fecha_fin DESC";
    }
    
    return $DB->get_records_sql( $query );
}

/**
 * Function that return the last period in the database.
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @return stdClass Last period. 
 */
function periods_get_last_period():stdClass
{
    global $DB;
    global $PERIODS_TABLENAME;
    
    $query = "SELECT * 
        FROM $PERIODS_TABLENAME
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
 * @todo test that it works.
 * @return bool True if succesfull, False otherwise.
 *
 * @throws Exception's stacktrace.
 */
function periods_update_period( $period_info, $period_id ){
	global $DB;
    	global $PERIODS_TABLENAME;
	
	try {
		$period = new stdClass();
		
		$period->id = $period_id;
		$period->nombre = $period_info[1];
		$period->fecha_inicio = $period_info[2];
		$period->fecha_fin = $period_info[3];
		
		$result = $DB->update_record($PERIODS_TABLENAME, $period);
		return $result;
	} catch (Exception $ex){
		return $ex->getMessage();
	}
}
?>
