<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
//add custom require here
require_once("data/AtomManager.php");
require_once("beans/Constant.php");
require_once("beans/SystemUser.php");
require_once("beans/SystemUserLog.php");

class LangFactory {

    public static function getAvailableLangList() {
        return array("es", "en", "pt");
    }

    public static function getDefaultLang() {
        return "es";
    }

}

//manage applications here, do not rename this class or functions
class ApplicationFactory {

    public static function makeScriptApplication($app, $config, $lang) {
        $application = null;
        return $application;
    }

    public static function defaultScriptApplication($config, $lang) {
        echo "wrong param " . $config['param_app'] . "\n";
    }

    public static function makeWebApplication($app, $config, $lang) {
        $application = null;
        switch ($app) {
            case "private": {
                    require_once("app/PrivateApplication.php");
                    $application = new PrivateApplication($config, $lang);
                    break;
                }
            case "json": {
                    require_once("app/JsonApplication.php");
                    $application = new JsonApplication($config, $lang);
                    break;
                }
            default : {
                    require_once("app/HomeApplication.php");
                    $application = new HomeApplication($config, $lang);
                }
        }
        return $application;
    }

    public static function defaultWebApplication($config, $lang) {
    }

}
