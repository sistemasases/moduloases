<?php
namespace reflection;

/**
 * Class __casting
 *
 * @param string|Class $class_name
 * @param object $sourceObject
 * @return object
 * @throws \ErrorException if the properties of the instance and std object are distinct
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
    $class_name_to = get_class($instanceTo);
    $std_object_properties = get_properties($stdObject);
    $shared_properties = array_intersect($std_object_properties, get_properties($instanceTo));
    $std_object_properties_string = implode(', ', get_properties($stdObject));
    if (!$shared_properties) {

        throw new \ErrorException("Cannot assign the given object to instance of $class_name_to because doesent have the shared properties, the object given have the properties $std_object_properties_string");
    }
    $sourceReflection = new \ReflectionObject($stdObject);
    $sourceProperties = $sourceReflection->getProperties();
    foreach ($sourceProperties as $sourceProperty) {
        $sourceProperty->setAccessible(true);
        $name = $sourceProperty->getName();
        $value = $sourceProperty->getValue($stdObject);
      
        if (in_array($name, $shared_properties) /*&& $value != ''*/) {
            $instanceTo->$name = $value;
        }
      
    }
}
/**
 * Return object or class properties in array
 * @param stdObject|string Object or class  name
 * @return array Array of string than contains the property names
 * @throws \ErrorException if the argument passed is class name and this does not exist
 */
function get_properties($stdObj_or_class){
    if(is_string($stdObj_or_class)){
      if(!class_exists($stdObj_or_class)) {
        throw new \ErrorException("The class $stdObj_or_class does not exist");
     } else {
         return array_keys(get_class_vars($stdObj_or_class));
     }        
    } else {
        if(is_array($stdObj_or_class)) {
            $stdObj_or_class = (object) $stdObj_or_class;
        }
        return array_keys(get_object_vars($stdObj_or_class));
    }
}
/**
 * Return column title based in standard object property name
 * ej. ('userEmail' ->  'User Email'}.
 * @param   string  $property_name  Standard property name, with words separed by underscores or capital letters. examples: userEmail, user_email
 * @return string return standard datatable column title based in property name ej. user_email -> 'User Email'.
 */
function property_name_to_description(string $property_name): string {

    if (strpos($property_name, '_') != false) {
        $separed_words_chain_by_spaces = preg_replace("/_+/", " ", $property_name);
        $column_title = ucwords($separed_words_chain_by_spaces);
    }
    else if(preg_match("/[A-Z]+/", $property_name)) {
        $separed_words_chain_by_spaces = preg_replace('/([A-Z])/', ' $1', $property_name);
        $column_title = ucwords($separed_words_chain_by_spaces);
    } else {
        $column_title = ucwords($property_name);
    }
    // All words should be in uppercase

    return $column_title;
}

/**
 * Return the property descriptions infered from property name
 * @param $element mixed
 * @param $skip_properties array Array of property names for skip in description return
 * @see property_name_to_description()
 * @return array List of descriptions based in element properties or keys
 * **If $element is a object, only descriptions for public properties are returned
 */
function get_object_properties_description($element, $skip_properties=array()): array {
    $object = (object) $element;
    $object_properties = null;
    $sourceProperties = null;
    $descriptions = [];

    $sourceProperties = new \ReflectionObject($object);
    $object_properties = $sourceProperties->getProperties();
    /* @var $object_property \ReflectionProperty */
    foreach($object_properties as $object_property) {
        $object_property_name = $object_property->name;
        /* Only public properties should be converted to table columns*/
        if($object_property->isPublic() && !in_array($object_property_name, $skip_properties)) {
            $description = property_name_to_description($object_property_name);
            array_push($descriptions, $description);
        }
    }

    return $descriptions;
}
 
/**
 * Validate std object contrastand it properties with the properties of a given class
 * @param stdObject | classInstance $obj Object to validate
 * @param string | Class Class to constrastand the $obj
 * @return bool If the stdObj have the same properties than the class (without taking into account accessibility) return true, false otherwise
 * @throws \ErrorException if the argument passed is class name and this does not exist
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
 * @throws \ErrorException if the argument passed is class name and this does not exist
 */
function make_from_std_object($stdObj_or_array, $class, $allow_non_equal_objects = true) {
 
    if(!class_exists($class)) {
        throw new \ErrorException("The class $class does not exist");
    }
    if (is_array($stdObj_or_array)) {
        $stdObj_or_array = (object)$stdObj_or_array;
    }
 
    $class_nameReflection = new \ReflectionObject(new $class);
   
    if (!$allow_non_equal_objects && !\reflection\valid_std_object($stdObj_or_array, $class)) {
        
        throw new \Exception("The object does not have the same properties than the class '$class'");
    } else {
        
        return \reflection\__cast($class, $stdObj_or_array);
    }
}

?>