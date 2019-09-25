<?php

class Estamento {
    const PUBLICO = 'PUBLICO';
    const PRIVADO = 'PRIVADO';
    const NO_DEFINIDO = 'NO DEFINIDO';

    public static function get_options() {
        return array(
            Estamento::NO_DEFINIDO=>Estamento::NO_DEFINIDO, 
            Estamento::PRIVADO=>Estamento::PRIVADO, 
            Estamento::PUBLICO=>Estamento::PUBLICO
        );
    }
}
?>