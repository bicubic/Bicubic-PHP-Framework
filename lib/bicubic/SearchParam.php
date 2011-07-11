<?php

/**
 * Bicubic PHP Framework
 * SearchParam Class
 *
 * @author     Juan Rodríguez-Covili <jrodriguez@bicubic.cl>
 * @copyright  2010 Bicubic Technology - http://www.bicubic.cl
 * @license    Bicubic Comercial Licence
 * @license    
 * @framework  2.1
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
