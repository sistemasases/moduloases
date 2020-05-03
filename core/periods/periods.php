<?php
/**
 * @package		block_ases
 * @subpackage	core.periods
 * @author 		Jeison Cardona Gómez
 * @copyright 	(C) 2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const VERSION = 1; //Current version.

require_once( __DIR__ . "/../../../../config.php");
require_once( __DIR__ . "/../module_loader.php");

global $PERIODS_TABLENAME;
$PERIODS_TABLENAME = $GLOBALS[ 'CFG' ]->prefix . "talentospilos_semestre";
require_once( __DIR__ . "/v" . VERSION . "/entrypoint.php");

/**
 * Interface to periods_get_current_period
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * @see periods_get_current_period(...) in entrypoint.php
 * 
 * @return  stdClass | null Return the last period.
 */
function core_periods_get_current_period(){
    return periods_get_current_period();
}

/**
 * Interface to periods_get_period_by_id
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see periods_get_period_by_id(...) in entrypoint.php
 *  
 * @param integer $period_id Period ID.
 * 
 * @throws Exception If doesn't exist a period with the given ID.
 * 
 * @return stdClass Period object
 */
function core_periods_get_period_by_id( int $period_id ){
    return periods_get_period_by_id( $period_id );
}


/**
 * Interface to periods_get_period_by_name
 * 
 * @author David Santiago Cortés <david.cortes@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see periods_get_period_by_name(...) in entrypoint.php
 *  
 * @param integer $period_name Period name.
 * 
 * @throws Exception If doesn't exist a period with the given name.
 * 
 * @return stdClass Period object
 */
function core_periods_get_period_by_name(string $period_name){
    return periods_get_period_by_name($period_name);
}

/**
 * Interface to periods_get_all_periods
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see periods_get_all_periods(...) in entrypoint.php
 * 
 * @return array List of periods.
 */
function core_periods_get_all_periods(){
    return periods_get_all_periods();
}

/**
 * Interface to periods_get_last_period
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see periods_get_last_period(...) in entrypoint.php
 * 
 * @return stdClass Last period.
 */
function core_periods_get_last_period(){
    return periods_get_last_period();
}


/**
 * Interface to periods_check_if_exist
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see periods_check_if_exist(...) in entrypoint.php
 * 
 * @param integer $period_id Period ID.
 * 
 * @return bool True if exist.
 */
function core_periods_check_if_exist( int $period_id ){
    return periods_check_if_exist( $period_id );
}

/**
 * Interface to periods_get_period_by_date
 *
 * @author David S. Cortés <david.cortes@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see periods_get_period_by_date(...) in entrypoint.php
 *
 * @param time/string $fecha_inicio
 * @param time/string $fecha_fin
 * @param bool $relax_query. If set to true, the function returns all periods
 * between the start and end date. If set to false, it will return an exact match.
 *
 * @return Period object
 * @throws Exception if there's no period between the given interval.
 */
function core_periods_get_period_by_date( $fecha_inicio, $fecha_fin, $relax_query=false ){
	return periods_get_period_by_date( $fecha_inicio, $fecha_fin, $relax_query=false );
}

/**
 * Interface to periods_update_period
 *
 * @author David S. Cortés <david.cortes@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @see periods_update_period(...) in entrypoint.php
 *
 * @param Array $period_info
 * @param integer $period_id
 *
 * @return bool True if operation ended succesfully, False otherwise
 */
function core_periods_update_period( $period_info, $period_id ){
	return periods_update_period( $period_info, $period_id);
}

/**
 * Inteface to periods_create_period
 *
 * @param time $fecha_inicio. Period's start date.
 * @param time $fecha_fin. Period's end date.
 * @param string $nombre. Period's name.
 *
 * @return stdClass of new period.
 * @throws Exception if there is already a period with the given name.
 */
function core_periods_create_period( $nombre, $fecha_inicio, $fecha_fin ){
	return periods_create_period($nombre, $fecha_inicio, $fecha_fin);
}
?>
