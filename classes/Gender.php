<?php

class Gender {
    public const FEMALE = 'F';
    public const MALE = 'M';
    /**
     * Return the current males with readable description in an array
     * @return array Array where the keys are the Gender option and the values are a readable word representing the gender
     */
    public static  function get_options() {
        return array(Gender::FEMALE => 'Mujer', Gender::MALE => 'Hombre');
    }
}

?>