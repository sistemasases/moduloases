<?php

class Gender {
    const FEMALE = 'F';
    const MALE = 'M';
    /**
     * Return the current genders with readable description in an array
     * @return array Array where the keys are the Gender option and the values are a readable word representing the gender
     */
    public static  function get_options() {
        return array(
            Gender::MALE => 'Masculino',
            Gender::FEMALE => 'Femenino'
        );
    }
}

?>