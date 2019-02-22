<?php
require_once(__DIR__.'/../../managers/lib/reflection.php');
trait from_std_object_or_array {
    public function __construct()
    {
    }

    /**
     * from_std_object_or_array constructor.
     * @param $stdObjectOrArrayFrom
     * @throws ErrorException
     */
    public function from_std_object_or_array($stdObjectOrArrayFrom) {
       \reflection\assign_properties_to($stdObjectOrArrayFrom, $this);
      
    }

    /**
     * Create class instances based in a given objects or arrays
     *
     * Only the common property are assigned, if does not exist any
     * common property the object is created, but the properties
     * are null, or take the value assigned in class constructor
     * @param $multipleStdObjectsOrArrays
     * @return array
     * @throws ErrorException
     */
    public static function make_objects_from_std_objects_or_arrays($multipleStdObjectsOrArrays) {
        
        $instances = array();
        foreach($multipleStdObjectsOrArrays as $stdObjectOrArray ) {

            array_push($instances, \reflection\make_from_std_object($stdObjectOrArray, get_called_class()));
            
        }
       
        return $instances;      
     }
}
?>