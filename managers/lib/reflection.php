<?php
namespace reflection;

/**
 * Class __casting
 *
 * @param string|Class $class_name
 * @param object $sourceObject
 * @return object
 */
function __cast($class_name_or_class, $sourceObject)
{
    
    if(!class_exists($class_name_or_class)) {
        throw new \ErrorException("The class $class_name_or_class does not exist");
    }
    $instance = new $class_name_or_class();
    $sourceReflection = new \ReflectionObject($sourceObject);
    $class_nameReflection = new \ReflectionObject($instance);
    $sourceProperties = $sourceReflection->getProperties();
    
    foreach ($sourceProperties as $sourceProperty) {
        $sourceProperty->setAccessible(true);
        $name = $sourceProperty->getName();
       
        $value = $sourceProperty->getValue($sourceObject);
        if ($class_nameReflection->hasProperty($name)) {
           
            $propDest = $class_nameReflection->getProperty($name);
            $propDest->setAccessible(true);
            $propDest->setValue($instance,$value);
        } else {
            
            $instance->$name = $value;
        }
    }
    
    return $instance;
}
/**
 * Assign all content of std object to class instance
 * @param class_instance $instanceTo Class instance to assign
 * @param stdObject|array $stdObjectOrArrayFrom Std object than have the properties to extract
 * @throws \ErrorException if the properties of the instance and std object are distinct
 */
function assign_properties_to($stdObjectOrArrayFrom, $instanceTo) {
    $stdObject = (object) $stdObjectOrArrayFrom;
    if (get_properties($stdObject) != get_properties($instanceTo)) {
        throw new \ErrorException("Both obejects should be have the exactly the same properties");
    }
    $sourceReflection = new \ReflectionObject($stdObject);
    $sourceProperties = $sourceReflection->getProperties();
    foreach ($sourceProperties as $sourceProperty) {
        $sourceProperty->setAccessible(true);
        $name = $sourceProperty->getName();
        $value = $sourceProperty->getValue($stdObject);
        $instanceTo->$name = $value;
      
        }
}
/**
 * Return object or class properties in array
 * @param stdObject|string Object or class  name
 * @return array Array of string than contains the property names
 */
function get_properties($stdObj_or_class){
    if(is_string($stdObj_or_class)){
      if(!class_exists($stdObj_or_class)) {
        throw new \ErrorException("The class $stdObj_or_class does not exist");
     } else {
         return array_keys(get_class_vars($stdObj_or_class));
     }        
    } else {
        return array_keys(get_object_vars($stdObj_or_class));
    }
}

 
/**
 * Validate std object contrastand it properties with the properties of a given class
 * @param stdObject | classInstance $obj Object to validate
 * @param string | Class Class to constrastand the $obj
 * @return bool If the stdObj have the same properties than the class (without taking into account accessibility) return true, false otherwise
 */ 
function valid_std_object($obj, $class_name_or_class) {
    if(!class_exists($class_name_or_class)) {
        throw new \ErrorException("The class $class_name_or_class does not exist");
    }
    $properties_obj = \reflection\get_properties($obj);
    $class_properties = \reflection\get_properties($class_name_or_class);
    $properties_diff = array_diff($class_properties, $properties_obj);
    return empty($properties_diff);
}
/**
 * Return an instance of a given class based in a stdObj or array
 * @param stdObj | array $stdObj_or_array Source object
 * @param string | class Class to make instance based in stdObj
 * @return class Instance of object based in the properties of stdObj or array of type class
 */ 
function make_from_std_object($stdObj_or_array, $class) {
 
    if(!class_exists($class)) {
        throw new \ErrorException("The class $class does not exist");
    }
    if (is_array($stdObj_or_array)) {
        $stdObj_or_array = (object)$stdObj_or_array;
    }
 
    $class_nameReflection = new \ReflectionObject(new $class);
   
    if (!\reflection\valid_std_object($stdObj_or_array, $class)) {
        
        throw new \Exception("El nuevo objeto no tiene las mismas propiedades que la clase '$class'");
    } else {
        
        return \reflection\__cast($class, $stdObj_or_array);
    }
}

?>