<?php
/**
 * Bicubic PHP Framework
 * Publication Class
 *
 * @author     Juan Rodríguez-Covili <jrodriguez@bicubic.cl>
 * @copyright  2010 Bicubic Technology - http://www.bicubic.cl
 * @license    Bicubic Comercial Licence
 * @license    
 * @framework  2.1
 */
abstract class Publication {

    public $username, $password, $message, $timeout;

    function __construct($username, $password, $message, $timeout) {
        $this->username = $username;
        $this->password = $password;
        $this->message = $message;
        $this->timeout = $timeout;
    }

    abstract function post();

    function send() {
        $result = $this->post();
        return $result;
    }

}
?>
