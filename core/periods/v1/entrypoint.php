<?php 
/**
 * @package	    block_ases
 * @subpackage	core.periods
 * @author 	    Jeison Cardona Gómez
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

?>