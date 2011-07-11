<?php

/**
 * Bicubic PHP Framework
 * Param Class
 *
 * @author     Juan Rodríguez-Covili <jrodriguez@bicubic.cl>
 * @copyright  2010 Bicubic Technology - http://www.bicubic.cl
 * @license    Bicubic Comercial Licence
 * @license    
 * @framework  2.1
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
