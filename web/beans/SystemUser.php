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
    private $newemail;
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
            "name" => ["name" => "name", "type" => PropertyTypes::$_STRING256, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "email" => ["name" => "email", "type" => PropertyTypes::$_EMAIL, "required" => true, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "password" => ["name" => "password", "type" => PropertyTypes::$_PASSWORD, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "sessiontoken" => ["name" => "sessiontoken", "type" => PropertyTypes::$_STRING1024, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "confirmemailtoken" => ["name" => "confirmemailtoken", "type" => PropertyTypes::$_STRING1024, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "forgottoken" => ["name" => "forgottoken", "type" => PropertyTypes::$_STRING1024, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "changeemailtoken" => ["name" => "changeemailtoken", "type" => PropertyTypes::$_STRING1024, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "newemail" => ["name" => "newemail", "type" => PropertyTypes::$_STRING256, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "usercountry" => ["name" => "usercountry", "type" => PropertyTypes::$_LIST, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "userlang" => ["name" => "userlang", "type" => PropertyTypes::$_LIST, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
        );
    }

    public function __isComplete() {
        if (!$this->sessiontoken) {
            $this->sessiontoken = "";
        }
        return parent::__isComplete();
    }

    public function __getList($paramname, Application $application) {
        switch ($paramname) {
            case "usercountry" : {
                    $navigation = new Navigation($application);
                    Country::$_ENUM = $navigation->sortByLang(Country::$_ENUM);
                    return Country::$_ENUM;
                }
            case "userlang" : {
                    $navigation = new Navigation($application);
                    Lang::$_ENUM = $navigation->sortByLang(Lang::$_ENUM);
                    return Lang::$_ENUM;
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

    public function getNewemail() {
        return $this->newemail;
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

    public function setNewemail($newemail) {
        $this->newemail = $newemail;
    }

    public function setUsercountry($usercountry) {
        $this->usercountry = $usercountry;
    }

    public function setUserlang($userlang) {
        $this->userlang = $userlang;
    }

}

?>