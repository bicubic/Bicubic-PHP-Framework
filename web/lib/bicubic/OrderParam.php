<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
class OrderParam  {

    public $order;
    public $property;

    function __construct($property, $order) {
        $this->property = $property;
        $this->order = $order;
    }

}

?>
