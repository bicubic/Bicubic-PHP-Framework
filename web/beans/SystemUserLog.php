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
            "id" => [               "name" => "id",                 "lang" => "lang_id",                "type" => PropertyTypes::$_LONG,    "required" => false, "default" => null, "serializable" => true, "index" => false,   "reference" => null,            "updatenull" => true, "hidden" => false, "private" => false, "unique" => false, "table" => true],
            "systemuser" => [       "name" => "systemuser",         "lang" => "lang_systemuser",        "type" => PropertyTypes::$_LONG,    "required" => false, "default" => null, "serializable" => true, "index" => true,    "reference" => "systemuser",    "updatenull" => true, "hidden" => false, "private" => false, "unique" => false, "table" => true],
            "servertime" => [       "name" => "servertime",         "lang" => "lang_servertime",        "type" => PropertyTypes::$_LONG,    "required" => false, "default" => null, "serializable" => true, "index" => false,   "reference" => null,            "updatenull" => true, "hidden" => false, "private" => false, "unique" => false, "table" => true],
            "remoteaddress" => [    "name" => "remoteaddress",      "lang" => "lang_remoteaddress",     "type" => PropertyTypes::$_STRING,  "required" => false, "default" => null, "serializable" => true, "index" => false,   "reference" => null,            "updatenull" => true, "hidden" => false, "private" => false, "unique" => false, "table" => true],
            "remotehost" => [       "name" => "remotehost",         "lang" => "lang_remotehost",        "type" => PropertyTypes::$_STRING,  "required" => false, "default" => null, "serializable" => true, "index" => false,   "reference" => null,            "updatenull" => true, "hidden" => false, "private" => false, "unique" => false, "table" => true],
            "remoteport" => [       "name" => "remoteport",         "lang" => "lang_remoteport",        "type" => PropertyTypes::$_STRING,  "required" => false, "default" => null, "serializable" => true, "index" => false,   "reference" => null,            "updatenull" => true, "hidden" => false, "private" => false, "unique" => false, "table" => true],
            "httpreferer" => [      "name" => "httpreferer",        "lang" => "lang_httpreferer",       "type" => PropertyTypes::$_STRING,  "required" => false, "default" => null, "serializable" => true, "index" => false,   "reference" => null,            "updatenull" => true, "hidden" => false, "private" => false, "unique" => false, "table" => true],
            "httplang" => [         "name" => "httplang",           "lang" => "lang_httplang",          "type" => PropertyTypes::$_STRING,  "required" => false, "default" => null, "serializable" => true, "index" => false,   "reference" => null,            "updatenull" => true, "hidden" => false, "private" => false, "unique" => false, "table" => true],
            "httpcharset" => [      "name" => "httpcharset",        "lang" => "lang_httpcharset",       "type" => PropertyTypes::$_STRING,  "required" => false, "default" => null, "serializable" => true, "index" => false,   "reference" => null,            "updatenull" => true, "hidden" => false, "private" => false, "unique" => false, "table" => true],
            "httphost" => [         "name" => "httphost",           "lang" => "lang_httphost",          "type" => PropertyTypes::$_STRING,  "required" => false, "default" => null, "serializable" => true, "index" => false,   "reference" => null,            "updatenull" => true, "hidden" => false, "private" => false, "unique" => false, "table" => true],
            "httpuseragent" => [    "name" => "httpuseragent",      "lang" => "lang_httpuseragent",     "type" => PropertyTypes::$_STRING,  "required" => false, "default" => null, "serializable" => true, "index" => false,   "reference" => null,            "updatenull" => true, "hidden" => false, "private" => false, "unique" => false, "table" => true],
            "https" => [            "name" => "https",              "lang" => "lang_https",             "type" => PropertyTypes::$_STRING,  "required" => false, "default" => null, "serializable" => true, "index" => false,   "reference" => null,            "updatenull" => true, "hidden" => false, "private" => false, "unique" => false, "table" => true],
            "querystring" => [      "name" => "querystring",        "lang" => "lang_querystring",       "type" => PropertyTypes::$_STRING,  "required" => false, "default" => null, "serializable" => true, "index" => false,   "reference" => null,            "updatenull" => true, "hidden" => false, "private" => false, "unique" => false, "table" => true],
            "uselatitude" => [      "name" => "uselatitude",        "lang" => "lang_uselatitude",       "type" => PropertyTypes::$_STRING,  "required" => false, "default" => null, "serializable" => true, "index" => false,   "reference" => null,            "updatenull" => true, "hidden" => false, "private" => false, "unique" => false, "table" => true],
            "uselongitude" => [     "name" => "uselongitude",       "lang" => "lang_uselongitude",      "type" => PropertyTypes::$_STRING,  "required" => false, "default" => null, "serializable" => true, "index" => false,   "reference" => null,            "updatenull" => true, "hidden" => false, "private" => false, "unique" => false, "table" => true],
            "usedevicemodel" => [   "name" => "usedevicemodel",     "lang" => "lang_usedevicemodel",    "type" => PropertyTypes::$_STRING,  "required" => false, "default" => null, "serializable" => true, "index" => false,   "reference" => null,            "updatenull" => true, "hidden" => false, "private" => false, "unique" => false, "table" => true],
            "usedeviceos" => [      "name" => "usedeviceos",        "lang" => "lang_usedeviceos",       "type" => PropertyTypes::$_STRING,  "required" => false, "default" => null, "serializable" => true, "index" => false,   "reference" => null,            "updatenull" => true, "hidden" => false, "private" => false, "unique" => false, "table" => true],
            "usedeviceversion" => [ "name" => "usedeviceversion",   "lang" => "lang_usedeviceversion",  "type" => PropertyTypes::$_STRING,  "required" => false, "default" => null, "serializable" => true, "index" => false,   "reference" => null,            "updatenull" => true, "hidden" => false, "private" => false, "unique" => false, "table" => true],
            "usecountry" => [       "name" => "usecountry",         "lang" => "lang_usecountry",        "type" => PropertyTypes::$_STRING,  "required" => false, "default" => null, "serializable" => true, "index" => false,   "reference" => null,            "updatenull" => true, "hidden" => false, "private" => false, "unique" => false, "table" => true],
            "uselanguage" => [      "name" => "uselanguage",        "lang" => "lang_uselanguage",       "type" => PropertyTypes::$_STRING,  "required" => false, "default" => null, "serializable" => true, "index" => false,   "reference" => null,            "updatenull" => true, "hidden" => false, "private" => false, "unique" => false, "table" => true],
            "usebatterylevel" => [  "name" => "usebatterylevel",    "lang" => "lang_usebatterylevel",   "type" => PropertyTypes::$_STRING,  "required" => false, "default" => null, "serializable" => true, "index" => false,   "reference" => null,            "updatenull" => true, "hidden" => false, "private" => false, "unique" => false, "table" => true],
            "payload" => [          "name" => "payload",            "lang" => "lang_payload",           "type" => PropertyTypes::$_STRING,  "required" => false, "default" => null, "serializable" => true, "index" => false,   "reference" => null,            "updatenull" => true, "hidden" => false, "private" => false, "unique" => false, "table" => true],
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