<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @framework  2.2
 */
class SearchParam {

    public $name;
    public $identity;

    //Constructor
    //@param $variable the key variable
    //@param $value the value
    function __construct($identity) {
        $this->identity = $identity;
    }

}

class SearchParamIdentity {

    public static $_EQUALS = "=";

    public static $_LIKE = "LIKE";

    public static $_GREATER = ">";

    public static $_EQUALGREATER = ">=";

    public static $_MINOR = "<";

    public static $_EQUALMINOR = "<=";

    public static $_DIFFERENT = "<>";

}

?>
