<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @framework  2.2
 */
class Param {

    /**
     * Nombre
     * @var String
     */
    public $name;
    /**
     * Valor
     * @var Object
     */
    public $value;

    /**
     * Constructor
     * @param String $name el nombre del parámetro
     * @param Valor $value el valor del parámetro, vacío por defecto
     */
    function __construct($name, $value="") {
        $this->name = $name;
        $this->value = $value;
    }

}
?>
