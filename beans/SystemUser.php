<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @framework  2.2
 */
class SystemUser extends DataObject {

    private $id;
    private $name;
    private $username;
    private $password;
    private $email;
    private $token;

    function __construct() {
        
    }

    /**
     * Propiedades de BD
     * @return array las propiedades a serializar en la BD
     */
    public function __getProperties() {
        return array(
            "id"        => array("name" => "id",        "type" => "long",       "hidden" => true),
            "name"      => array("name" => "name",      "type" => "string256",  ),
            "username"  => array("name" => "username",  "type" => "string256",  "option" => Usernames::$_ENUM),
            "password"  => array("name" => "password",  "type" => "string1024", ),
            "email"     => array("name" => "email",     "type" => "string256",  "category" => Usernames::$_ENUM),
            "token"     => array("name" => "token",     "type" => "string1024", "private" => true),
        );
    }
    
    
    public function __isComplete() {
        if(!isset($this->email)) {
            return false;
        }
        return true;
    }
    
    public function __isChild() {
        return false;
    }

    /**
     * Setea el valor de una propiedad
     * @param long $value <p>EL valor de la propiedad</p>
     */
    function setId($value) {
        $this->id = $value;
    }

    /**
     * Obtiene el valor de una propiedad
     * @return el valor de la propiedad o null si la propiedad no existe
     */
    function getId() {
        return $this->id;
    }
    
    /**
     * Setea el valor de una propiedad
     * @param long $value <p>EL valor de la propiedad</p>
     */
    function setName($value) {
        $this->name = $value;
    }

    /**
     * Obtiene el valor de una propiedad
     * @return el valor de la propiedad o null si la propiedad no existe
     */
    function getName() {
        return $this->name;
    }

    /**
     * Setea el valor de una propiedad
     * @param long $value <p>EL valor de la propiedad</p>
     */
    function setUsername($value) {
        $this->username = $value;
    }

    /**
     * Obtiene el valor de una propiedad
     * @return el valor de la propiedad o null si la propiedad no existe
     */
    function getUsername() {
        return $this->username;
    }

    /**
     * Setea el valor de una propiedad
     * @param long $value <p>EL valor de la propiedad</p>
     */
    function setPassword($value) {
        $this->password = $value;
    }

    /**
     * Obtiene el valor de una propiedad
     * @return el valor de la propiedad o null si la propiedad no existe
     */
    function getPassword() {
        return $this->password;
    }

    /**
     * Setea el valor de una propiedad
     * @param long $value <p>EL valor de la propiedad</p>
     */
    function setEmail($value) {
        $this->email = $value;
    }

    /**
     * Obtiene el valor de una propiedad
     * @return el valor de la propiedad o null si la propiedad no existe
     */
    function getEmail() {
        return $this->email;
    }

    /**
     * Setea el valor de una propiedad
     * @param long $value <p>EL valor de la propiedad</p>
     */
    function setToken($value) {
        $this->token = $value;
    }

    /**
     * Obtiene el valor de una propiedad
     * @return el valor de la propiedad o null si la propiedad no existe
     */
    function getToken() {
        return $this->token;
    }

}

?>
