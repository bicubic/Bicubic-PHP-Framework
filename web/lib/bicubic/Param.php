<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
class Param {

    public $name;
    public $value;

    function __construct($name, $value = "") {
        $this->name = $name;
        $this->value = $value;
    }

}

?>
