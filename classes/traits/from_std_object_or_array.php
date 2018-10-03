<?php
require_once(__DIR__.'/../../managers/lib/reflection.php');
trait from_std_object_or_array {
    public function from_std_object_or_array($stdObjectOrArrayFrom) {
       \reflection\assign_properties_to($stdObjectOrArrayFrom, $this);
      
    }
    public static function make_objects_from_std_objects_or_arrays($multipleStdObjectsOrArrays) {
        
        $instances = array();
        foreach($multipleStdObjectsOrArrays as $stdObjectOrArray ) {

            array_push($instances, \reflection\make_from_std_object($stdObjectOrArray, get_called_class()));
            
        }
       
        return $instances;      
     }
}
?>