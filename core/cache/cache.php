<?php
/**
 * @package	block_ases
 * @subpackage	core.cache
 * @author 	Jeison Cardona Gómez
 * @copyright 	(C) 2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const VERSION = 1; //Current version.
const TABLE_SCHEMA = "public";

require_once( __DIR__ . "/../../../../config.php");
require_once( __DIR__ . "/../module_loader.php");

global $DB_PREFIX;
$DB_PREFIX = $GLOBALS[ 'CFG' ]->prefix;
require_once( __DIR__ . "/v" . VERSION . "/entrypoint.php");

/* Interface function
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * @see cache_put_value(...) in entrypoint.php
 */
function core_cache_put_value( $key, $value = NULL, $description = NULL ){
    return cache_put_value($key, $value, $description);
}

/* Interface function
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * @see cache_get_value(...) in entrypoint.php
 */
function core_cache_get_value( $key ){
    return cache_get_value( $key );
}

/* Interface function
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * @see cache_delete(...) in entrypoint.php
 */
function core_cache_delete( $key ){
    return cache_delete( $key );
}

/* Interface function
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * @see cache_update_value(...) in entrypoint.php
 */
function core_cache_update_value( $key, $value = NULL ){
    return cache_update_value($key, $value);
}

/* Interface function
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * @see cache_update_description(...) in entrypoint.php
 */
function core_cache_update_description( $key, $description = NULL ){ 
    return cache_update_description( $key, $description );
}

/* Interface function
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * @see cache_is_supported(...) in entrypoint.php
 */
function core_cache_is_supported(){
    return cache_is_supported();
}

/* Interface function
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * @see cache_key_exist(...) in entrypoint.php
 */
function core_cache_key_exist( $key ){
    return cache_key_exist($key);
}

/* Interface function
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * @see cache_get_obj(...) in entrypoint.php
 */
function core_cache_get_obj( $key ){
    return cache_get_obj( $key );
}

?>
