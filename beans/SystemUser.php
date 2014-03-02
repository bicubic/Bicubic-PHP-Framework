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
    private $category;
    private $option;

    function __construct() {
    }

    /**
     * Propiedades de BD
     * @return array las propiedades a serializar en la BD
     */
    public function __getProperties() {
        return array(
            "id"        => array("name" => "id",        "type" => "long",       "required" => false, "serializable" => true,  "updatenull" => true,  "hidden"    => true),
            "name"      => array("name" => "name",      "type" => "string256",  "required" => true,  "serializable" => true,  "updatenull" => true                      ),
            "username"  => array("name" => "username",  "type" => "string256",  "required" => true,  "serializable" => true,  "updatenull" => true                      ),
            "password"  => array("name" => "password",  "type" => "string1024", "required" => true,  "serializable" => true,  "updatenull" => true                      ),
            "email"     => array("name" => "email",     "type" => "string256",  "required" => true,  "serializable" => true,  "updatenull" => true                      ),
            "token"     => array("name" => "token",     "type" => "string1024", "required" => false, "serializable" => true,  "updatenull" => false, "private"   => true),
            "category"  => array("name" => "category",  "type" => "int",        "required" => false, "serializable" => false, "updatenull" => true,  "list"      => true),
            "option"    => array("name" => "option",    "type" => "int",        "required" => false, "serializable" => false, "updatenull" => true,  "shortlist" => true),
        );
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
    
    public function getCategory() {
        return $this->category;
    }
    
    public function getCategoryList() {
        return ExampleList::$_ENUM;
    }

    public function setCategory($category) {
        $this->category = $category;
    }

    public function getOption() {
        return $this->option;
    }

    public function setOption($option) {
        $this->option = $option;
    }

    public function getOptionList() {
        return ExampleList::$_ENUM;
    }

}

?>
