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
 * @see cache_get_obj(...) in this file.
 * @see cache_delete(...) in this file.
 * 
 * @param integer|string $key Key.
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
        global $DB_PREFIX;
            
        $tablename = $DB_PREFIX . "talentospilos_cache";
           
        $data_cache = $DB->get_record_sql( "SELECT id, fecha_hora_expiracion, now() AS current_time FROM $tablename WHERE clave = '$key'" );
            
        if( property_exists($data_cache, "id") ){
                
            $current_db_time = strtotime($data_cache->current_time);
            $expiration_time = strtotime($data_cache->fecha_hora_expiracion);
                        
            if( $expiration_time != "" ){
                if( $expiration_time > $current_db_time ){
                    return true;
                }else{
                    $DB->delete_records("talentospilos_cache", array( "clave" => $key ));
                    return false;
                }
            }
            
            return true;
                
        }else{
            return false;
        }
        
    }
    
}

/* Function that determine if cache is supported and a given key exist
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see cache_is_supported(...) in this file.
 * @see cache_key_exist(...) in this file.
 * 
 * @param integer|string $key Key.
 * 
 * @throws Exception If cache isn't supported.
 * @throws Exception If a given key doesn't exist.
 * 
 * @return void
 */
function general_cache_validation($key){
    
    if( !cache_is_supported() ){
        throw new Exception( "Cache isn't supported", -1 );
    }
    
    if( !cache_key_exist( $key ) ){
        throw new Exception( "Key '$key' doesn't exist", -2 );
    }
    
}

/* Function that store in the cache a value asociated to key and a optional 
 * description.
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @param integer|string $key Key.
 * @param integer|string $value Value to store.
 * @param integer|string $description Description.
 * 
 * @throws Exception If cache isn't supported.
 * @throws Exception If a given key exist in cache.
 * 
 * @return integer Record id.
 */
function cache_put_value( $key, $value = NULL, $description = NULL ){
    
    if( !cache_is_supported() ){
        throw new Exception( "Cache isn't supported", -1 );
    }
    
    if( cache_key_exist( $key ) ){
        throw new Exception( "Key '$key' already exist in cache", -3 );
    }
    
    global $DB;
        
    $obj_cache = new stdClass();
    $obj_cache->clave = $key;
    $obj_cache->valor = $value;
    $obj_cache->descripcion = $description;
    
    return $DB->insert_record("talentospilos_cache", $obj_cache, true);
    
}

/* Function that given a key, update its asociated value.
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see general_cache_validation(...) in this file.
 * @see cache_get_obj(...) in this file.
 * 
 * @param integer|string $key Key.
 * @param integer|string $value Value to update.
 * 
 * @return integer
 */
function cache_update_value( $key, $value = NULL ){
    
    general_cache_validation($key);
    
    global $DB;
        
    $obj_cache = cache_get_obj( $key );
    $obj_cache->valor = $value;
    
    return $DB->update_record("talentospilos_cache", $obj_cache);
    
}

/* Function that given a key, update its asociated description.
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see general_cache_validation(...) in this file.
 * @see cache_get_obj(...) in this file.
 * 
 * @param integer|string $key Key.
 * @param integer|string $description Description to update.
 * 
 * @return integer
 */
function cache_update_description( $key, $description = NULL ){
    
    general_cache_validation($key);
    
    global $DB;
        
    $obj_cache = cache_get_obj( $key );
    $obj_cache->descripcion = $description;
    $obj_cache->fecha_hora_actualizado = "now()";
    
    return $DB->update_record("talentospilos_cache", $obj_cache);
    
}

/* Function that given a key, return a cache object.
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see general_cache_validation(...) in this file.
 * 
 * @param integer|string $key Key.
 * 
 * @return stdClass Cache object
 */
function cache_get_obj( $key ){
    
    general_cache_validation($key);
    
    global $DB;
    
    $obj_cache = $DB->get_record("talentospilos_cache", array( "clave" => $key ));
        
    return (property_exists($obj_cache, "id") ? $obj_cache : null);
    
}

/* Function that given a key, return its asociated value.
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see general_cache_validation(...) in this file.
 * @see cache_get_obj(...) in this file.
 * 
 * @param integer|string $key Key.
 * 
 * @return string Value stored
 */
function cache_get_value( $key ){
    
    general_cache_validation($key);
    return cache_get_obj( $key )->valor;
    
}

/* Function that remove a cache entry.
 * 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @see general_cache_validation(...) in this file.
 * 
 * @param integer|string $key Key.
 * 
 * @return integer
 */
function cache_delete( $key ){
    
    general_cache_validation($key);
    
    global $DB;
    return $DB->delete_records("talentospilos_cache", array( "clave" => $key ));
}


?>