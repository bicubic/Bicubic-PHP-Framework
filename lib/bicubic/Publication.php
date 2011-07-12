<?php
/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @framework  2.2
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
