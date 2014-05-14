<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
class SystemUserLog extends DataObject {

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
    private $uselatitude;
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
            "id" => ["name" => "id", "type" => PropertyTypes::$_LONG, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
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
            "uselatitude" => ["name" => "uselatitude", "type" => PropertyTypes::$_STRING, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
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

    public function getSystemuser() {
        return $this->systemuser;
    }

    public function getServertime() {
        return $this->servertime;
    }

    public function getRemoteaddress() {
        return $this->remoteaddress;
    }

    public function getRemotehost() {
        return $this->remotehost;
    }

    public function getRemoteport() {
        return $this->remoteport;
    }

    public function getHttpreferer() {
        return $this->httpreferer;
    }

    public function getHttplang() {
        return $this->httplang;
    }

    public function getHttpcharset() {
        return $this->httpcharset;
    }

    public function getHttphost() {
        return $this->httphost;
    }

    public function getHttpuseragent() {
        return $this->httpuseragent;
    }

    public function getHttps() {
        return $this->https;
    }

    public function getQuerystring() {
        return $this->querystring;
    }

    public function getUselatitude() {
        return $this->uselatitude;
    }

    public function getUselongitude() {
        return $this->uselongitude;
    }

    public function getUsedevicemodel() {
        return $this->usedevicemodel;
    }

    public function getUsedeviceos() {
        return $this->usedeviceos;
    }

    public function getUsedeviceversion() {
        return $this->usedeviceversion;
    }

    public function getUsecountry() {
        return $this->usecountry;
    }

    public function getUselanguage() {
        return $this->uselanguage;
    }

    public function getUsebatterylevel() {
        return $this->usebatterylevel;
    }

    public function getPayload() {
        return $this->payload;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setSystemuser($systemuser) {
        $this->systemuser = $systemuser;
    }

    public function setServertime($servertime) {
        $this->servertime = $servertime;
    }

    public function setRemoteaddress($remoteaddress) {
        $this->remoteaddress = $remoteaddress;
    }

    public function setRemotehost($remotehost) {
        $this->remotehost = $remotehost;
    }

    public function setRemoteport($remoteport) {
        $this->remoteport = $remoteport;
    }

    public function setHttpreferer($httpreferer) {
        $this->httpreferer = $httpreferer;
    }

    public function setHttplang($httplang) {
        $this->httplang = $httplang;
    }

    public function setHttpcharset($httpcharset) {
        $this->httpcharset = $httpcharset;
    }

    public function setHttphost($httphost) {
        $this->httphost = $httphost;
    }

    public function setHttpuseragent($httpuseragent) {
        $this->httpuseragent = $httpuseragent;
    }

    public function setHttps($https) {
        $this->https = $https;
    }

    public function setQuerystring($querystring) {
        $this->querystring = $querystring;
    }

    public function setUselatitude($uselatitude) {
        $this->uselatitude = $uselatitude;
    }

    public function setUselongitude($uselongitude) {
        $this->uselongitude = $uselongitude;
    }

    public function setUsedevicemodel($usedevicemodel) {
        $this->usedevicemodel = $usedevicemodel;
    }

    public function setUsedeviceos($usedeviceos) {
        $this->usedeviceos = $usedeviceos;
    }

    public function setUsedeviceversion($usedeviceversion) {
        $this->usedeviceversion = $usedeviceversion;
    }

    public function setUsecountry($usecountry) {
        $this->usecountry = $usecountry;
    }

    public function setUselanguage($uselanguage) {
        $this->uselanguage = $uselanguage;
    }

    public function setUsebatterylevel($usebatterylevel) {
        $this->usebatterylevel = $usebatterylevel;
    }

    public function setPayload($payload) {
        $this->payload = $payload;
    }



}

?>