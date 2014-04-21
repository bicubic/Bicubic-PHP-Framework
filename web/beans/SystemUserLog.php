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
    private $systemuser;
    private $servertime;
    private $remoteaddress;
    private $remotehost;
    private $remoteport;
    private $httpreferer;
    private $httplang;
    private $httpcharset;
    private $httphost;
    private $httpuseragent;
    private $https;
    private $querystring;
    private $userlatitude;
    private $uselongitude;
    private $usedevicemodel;
    private $usedeviceos;
    private $usedeviceversion;
    private $usecountry;
    private $uselanguage;
    private $usebatterylevel;
    private $payload;

    function __construct($id = null) {
        $this->id = $id;
    }

    public function __getProperties() {
        return array(
            "id" => ["name" => "id", "type" => PropertyTypes::$_LONG, "required" => true, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "systemuser" => ["name" => "systemuser", "type" => PropertyTypes::$_LONG, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "servertime" => ["name" => "servertime", "type" => PropertyTypes::$_LONG, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "remoteaddress" => ["name" => "remoteaddress", "type" => PropertyTypes::$_STRING, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "remotehost" => ["name" => "remotehost", "type" => PropertyTypes::$_STRING, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "remoteport" => ["name" => "remoteport", "type" => PropertyTypes::$_STRING, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "httpreferer" => ["name" => "httpreferer", "type" => PropertyTypes::$_STRING, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "httplang" => ["name" => "httplang", "type" => PropertyTypes::$_STRING, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "httpcharset" => ["name" => "httpcharset", "type" => PropertyTypes::$_STRING, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "httphost" => ["name" => "httphost", "type" => PropertyTypes::$_STRING, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "httpuseragent" => ["name" => "httpuseragent", "type" => PropertyTypes::$_STRING, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "https" => ["name" => "https", "type" => PropertyTypes::$_STRING, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "querystring" => ["name" => "querystring", "type" => PropertyTypes::$_STRING, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "userlatitude" => ["name" => "userlatitude", "type" => PropertyTypes::$_STRING, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "uselongitude" => ["name" => "uselongitude", "type" => PropertyTypes::$_STRING, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "usedevicemodel" => ["name" => "usedevicemodel", "type" => PropertyTypes::$_STRING, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "usedeviceos" => ["name" => "usedeviceos", "type" => PropertyTypes::$_STRING, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "usedeviceversion" => ["name" => "usedeviceversion", "type" => PropertyTypes::$_STRING, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "usecountry" => ["name" => "usecountry", "type" => PropertyTypes::$_STRING, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "uselanguage" => ["name" => "uselanguage", "type" => PropertyTypes::$_STRING, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "usebatterylevel" => ["name" => "usebatterylevel", "type" => PropertyTypes::$_STRING, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "payload" => ["name" => "payload", "type" => PropertyTypes::$_STRING, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
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