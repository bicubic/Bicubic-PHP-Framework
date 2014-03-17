<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
class SystemAdmin extends DataObject {

    private $id;
    private $name;
    private $email;
    private $password;
    private $sessiontoken;

    function __construct($id = null, $email = null, $password = null) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
    }

    public function __getProperties() {
        return array(
            "id" => ["name" => "id", "type" => PropertyTypes::$_LONG, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => true, "private" => false],
            "name" => ["name" => "name", "type" => PropertyTypes::$_STRING256, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "email" => ["name" => "email", "type" => PropertyTypes::$_EMAIL, "required" => true, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "password" => ["name" => "password", "type" => PropertyTypes::$_PASSWORD, "required" => true, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "sessiontoken" => ["name" => "sessiontoken", "type" => PropertyTypes::$_STRING1024, "required" => false, "serializable" => true, "updatenull" => false, "hidden" => true, "private" => true],
        );
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getSessiontoken() {
        return $this->sessiontoken;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setSessiontoken($sessiontoken) {
        $this->sessiontoken = $sessiontoken;
    }

}

?>