<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 5/10/18
 * Time: 03:04 PM
 */

/**
 * Class AsesErrorFactory
 * Clase base para las factorias de errores, estan tienen la responsabilidad de agrupar los errores y
 * de crearlos, con el fin de tener centralizado el manejo de errores y determinar que tipo de error es un error
 * arbitrario dado.
 *
 * Las constantes definidas solamente pueden ser codigos de error, no debe añadir otra cosa como constante
 * mas que dichos codigos, los cuales deben ser preferiblemente numericos.
 *
 * Por supuesto se es libre de crear una instancia de la clase AsesError sin hacer una factoria, sin embargo si el
 * error es generico y nota que este debe ser usado en más de un archivo php entonces deberia consierar añadirlo a alguna
 * factoria existente o bien crear una nueva factoria si este no encaja en ninguna de las existentes
 */
abstract class AsesErrorFactory
{
    /**
     * Return the error codes than are the type of the factory
     * @return array Error codes than are part of the factory
     * @throws \ReflectionException if the class does not exist.
     */
    public function get_error_codes(): array {
        $oClass = new ReflectionClass(get_called_class());
        return array_values($oClass->getConstants());
    }


}