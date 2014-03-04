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
    private $email;
    private $password;
    private $sessiontoken;
    private $confirmemailtoken;
    private $forgottoken;
    private $changeemailtoken;
    private $changepasswordtoken;
    private $usercountry;
    private $userlang;

    function __construct($id = null, $email = null, $password = null) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
    }

    public function __getProperties() {
        return array(
            "id" => ["name" => "id", "type" => PropertyTypes::$_LONG, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => true, "private" => false],
            "name" => ["name" => "name", "type" => PropertyTypes::$_STRING256, "required" => true, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "email" => ["name" => "email", "type" => PropertyTypes::$_EMAIL, "required" => true, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "password" => ["name" => "password", "type" => PropertyTypes::$_PASSWORD, "required" => true, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "sessiontoken" => ["name" => "sessiontoken", "type" => PropertyTypes::$_STRING1024, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "confirmemailtoken" => ["name" => "confirmemailtoken", "type" => PropertyTypes::$_STRING1024, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "forgottoken" => ["name" => "forgottoken", "type" => PropertyTypes::$_STRING1024, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "changeemailtoken" => ["name" => "changeemailtoken", "type" => PropertyTypes::$_STRING1024, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "changepasswordtoken" => ["name" => "changepasswordtoken", "type" => PropertyTypes::$_STRING1024, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "usercountry" => ["name" => "usercountry", "type" => PropertyTypes::$_LIST, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "userlang" => ["name" => "userlang", "type" => PropertyTypes::$_STRING2, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
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

    public function getEmail() {
        return $this->email;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getSessiontoken() {
        return $this->sessiontoken;
    }

    public function getConfirmemailtoken() {
        return $this->confirmemailtoken;
    }

    public function getForgottoken() {
        return $this->forgottoken;
    }

    public function getChangeemailtoken() {
        return $this->changeemailtoken;
    }

    public function getChangepasswordtoken() {
        return $this->changepasswordtoken;
    }

    public function getUsercountry() {
        return $this->usercountry;
    }

    public function getUserlang() {
        return $this->userlang;
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

    public function setConfirmemailtoken($confirmemailtoken) {
        $this->confirmemailtoken = $confirmemailtoken;
    }

    public function setForgottoken($forgottoken) {
        $this->forgottoken = $forgottoken;
    }

    public function setChangeemailtoken($changeemailtoken) {
        $this->changeemailtoken = $changeemailtoken;
    }

    public function setChangepasswordtoken($changepasswordtoken) {
        $this->changepasswordtoken = $changepasswordtoken;
    }

    public function setUsercountry($usercountry) {
        $this->usercountry = $usercountry;
    }

    public function setUserlang($userlang) {
        $this->userlang = $userlang;
    }

}

?>