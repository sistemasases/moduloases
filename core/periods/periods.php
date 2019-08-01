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

/* Interface function
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * @see periods_get_current_period(...) in entrypoint.php
 */
function core_periods_get_current_period(){
	return periods_get_current_period();
}

?>
