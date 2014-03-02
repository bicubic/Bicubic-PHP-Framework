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
            "id"        => ["name" => "id",        "type" => "long",       "required" => false, "serializable" => true,  "updatenull" => true,   "hidden" => true,  "private" => false],
            "name"      => ["name" => "name",      "type" => "string256",  "required" => true,  "serializable" => true,  "updatenull" => true,   "hidden" => false, "private" => false],
            "username"  => ["name" => "username",  "type" => "string256",  "required" => true,  "serializable" => true,  "updatenull" => true,   "hidden" => false, "private" => false],
            "password"  => ["name" => "password",  "type" => "password",   "required" => true,  "serializable" => true,  "updatenull" => true,   "hidden" => false, "private" => false],
            "email"     => ["name" => "email",     "type" => "email",      "required" => true,  "serializable" => true,  "updatenull" => true,   "hidden" => false, "private" => false],
            "token"     => ["name" => "token",     "type" => "string1024", "required" => false, "serializable" => true,  "updatenull" => false,  "hidden" => false, "private" => true ],
            "category"  => ["name" => "category",  "type" => "list",       "required" => true,  "serializable" => false, "updatenull" => true,   "hidden" => false, "private" => false],
            "option"    => ["name" => "option",    "type" => "shortlist",  "required" => true,  "serializable" => false, "updatenull" => true,   "hidden" => false, "private" => false],
        );
    }
    
    public function __isComplete() {
        if(!$this->token) {
            $this->token = "";
        }
        
        return parent::__isComplete();
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
