<?php 
/**
 * @package	block_ases
 * @subpackage	core.cache
 * @author 	Jeison Cardona Gómez
 * @copyright 	(C) 2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* Function that determine if the table talentospilos_cache exist in the current
 * database.
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * @return bool True if exist, in other way, False.
 */
function cache_is_supported(){
    
    global $DB;
    global $DB_PREFIX;
    
    $table_name = $DB_PREFIX . "talentospilos_cache";
    
    $query  ="
        SELECT 
            table_name
        FROM 
            information_schema.tables
        WHERE 
            table_type = 'BASE TABLE' AND 
            table_schema = '" . TABLE_SCHEMA . "' AND
            table_name = '$table_name'";
    
    return ( $DB->get_records_sql( $query ) ? true : false );
    
}

/* Function that determine if a given key exist.
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @param integer|string $key 
 * 
 * @throws Exception If the provided argumen is null.
 * 
 * @return bool True if exist, in other way, False.
 */
function cache_key_exist( $key ){
    
    if( is_null( $key ) ){
        throw new Exception( "Key cannot be null", -1 );
    }else{
        global $DB;
        return ( $DB->record_exists("talentospilos_cache", array( "clave" => $key )) ? true : false );
    }
    
}

function general_cache_validation($key){
    
    if( !cache_is_supported() ){
        throw new Exception( "Cache isn't supported", -1 );
    }
    
    if( !cache_key_exist( $key ) ){
        throw new Exception( "Key '$key' doesn't exist", -1 );
    }
    
}

function cache_put_value( $key, $value = NULL, $description = NULL ){
    
    if( !cache_is_supported() ){
        throw new Exception( "Cache isn't supported", -1 );
    }
    
    if( cache_key_exist( $key ) ){
        throw new Exception( "Key '$key' already exist in cache", -1 );
    }
    
    global $DB;
        
    $obj_cache = new stdClass();
    $obj_cache->clave = $key;
    $obj_cache->valor = $value;
    $obj_cache->descripcion = $description;
    
    return $DB->insert_record("talentospilos_cache", $obj_cache, true);
    
}

function cache_update_value( $key, $value = NULL ){
    
    general_cache_validation($key);
    
    global $DB;
        
    $obj_cache = cache_get_obj( $key );
    $obj_cache->valor = $value;
    
    return $DB->update_record("talentospilos_cache", $obj_cache);
    
}

function cache_update_description( $key, $description = NULL ){
    
    general_cache_validation($key);
    
    global $DB;
        
    $obj_cache = cache_get_obj( $key );
    $obj_cache->descripcion = $description;
    $obj_cache->fecha_hora_actualizado = "now()";
    
    return $DB->update_record("talentospilos_cache", $obj_cache);
    
}

function cache_get_obj( $key ){
    
    general_cache_validation($key);
    
    global $DB;
    
    $obj_cache = $DB->get_record("talentospilos_cache", array( "clave" => $key ));
    return $obj_cache;
    
}

function cache_get_value( $key ){
    
    general_cache_validation($key);
    return cache_get_obj( $key )->valor;
    
}

function cache_delete( $key ){
    
    general_cache_validation($key);
    
    global $DB;
    return $DB->delete_records("talentospilos_cache", array( "clave" => $key ));
}


?>