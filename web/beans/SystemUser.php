<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
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

    public function __getProperties() {
        return array(
            "id" => ["name" => "id", "type" => PropertyTypes::$_LONG, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => true, "private" => false],
            "name" => ["name" => "name", "type" => PropertyTypes::$_STRING256, "required" => true, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "username" => ["name" => "username", "type" => PropertyTypes::$_STRING256, "required" => true, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "password" => ["name" => "password", "type" => PropertyTypes::$_PASSWORD, "required" => true, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "email" => ["name" => "email", "type" => PropertyTypes::$_EMAIL, "required" => true, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "token" => ["name" => "token", "type" => PropertyTypes::$_STRING1024, "required" => false, "serializable" => true, "updatenull" => false, "hidden" => false, "private" => true],
            "category" => ["name" => "category", "type" => PropertyTypes::$_LIST, "required" => true, "serializable" => false, "updatenull" => true, "hidden" => false, "private" => false],
            "option" => ["name" => "option", "type" => PropertyTypes::$_SHORTLIST, "required" => true, "serializable" => false, "updatenull" => true, "hidden" => false, "private" => false],
        );
    }

    public function __isComplete() {
        if (!$this->token) {
            $this->token = "";
        }
        return parent::__isComplete();
    }

    public function __getList(TransactionManager $data, $paramname) {
        switch ($paramname) {
            case "category" : {
                    return ExampleList::$_ENUM;
                }
            case "option" : {
                    return ExampleList::$_ENUM;
                }
        }
        return [];
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getToken() {
        return $this->token;
    }

    public function getCategory() {
        return $this->category;
    }

    public function getOption() {
        return $this->option;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setToken($token) {
        $this->token = $token;
    }

    public function setCategory($category) {
        $this->category = $category;
    }

    public function setOption($option) {
        $this->option = $option;
    }

}

?>