<?php

/**
 * Bicubic PHP Framework
 * 
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @framework  2.2
 */
//base
require_once("config/config.php");
require_once("error.php");
date_default_timezone_set("America/Santiago");
//lib
require_once("lib/ext/pear/Sigma.php");
require_once("lib/ext/simple_html_dom.php");
require_once("lib/ext/pear/Date.php");
require_once("lib/ext/pear/Mail.php");
require_once("lib/ext/thread/singleton.class.php");
//bicubic
require_once("lib/bicubic/Application.php");
require_once("lib/bicubic/Data.php");
require_once("lib/bicubic/DataObject.php");
require_once("lib/bicubic/SQLData.php");
require_once("lib/bicubic/PostgreSQLData.php");
require_once("lib/bicubic/Navigation.php");
require_once("lib/bicubic/Parser.php");
require_once("lib/bicubic/HTMLParser.php");
require_once("lib/bicubic/XMLParser.php");
require_once("lib/bicubic/Param.php");
require_once("lib/bicubic/Publication.php");
require_once("lib/bicubic/Email.php");
require_once("lib/bicubic/LogData.php");
require_once("lib/bicubic/SearchParam.php");
require_once("lib/bicubic/TransactionManager.php");
//beans
require_once("beans/SystemUser.php");
require_once("beans/Constant.php");
//json
require_once("json/ErrorJson.php");
require_once("json/LoginErrorJson.php");
require_once("json/ObjectJson.php");
require_once("json/SuccessJson.php");
//URL params value
$config['param_app'] = "app";
$config['param_navigation'] = "nav";
$config['param_id'] = "id";
$config['param_page'] = "page";
$config['param_compress'] = "cp";
$config['param_lang'] = "lang";
//set languaje
$langfile = Lang::$_DEFAULT;
require_once("lang/lang.$langfile.php");
$application = new Application($config, $lang, null, null);
//sacamos el lang del browser
if (array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER)) {
    $langfile = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
}
//sacamos el lang de la url
$urllocale = $application->getUrlParam($config['param_lang'], "string256", false);
if (isset($urllocale)) {
    $langfile = $urllocale;
}
//sacamos el lang de facebook Crowl
$fblocale = $application->getUrlParam("fb_locale", "string", false);
if (isset($fblocale)) {
    $langfile = substr($fblocale, 0, 2);
}
//vemos si existe en lang
if (!array_key_exists($langfile, Lang::$_ENUM)) {
    $langfile = Lang::$_DEFAULT;
}
//cargamos denuevo el lang por si es distinto al default
require_once("lang/lang.$langfile.php");
$config["lang"] = $langfile;
//set temp folder
$config['server_temp_folder'] = sys_get_temp_dir() . "/";
//set working foler
$url = $_SERVER['REQUEST_URI']; //returns the current URL
$parts = explode('/',$url);
$dir = $_SERVER['SERVER_NAME'];
for ($i = 0; $i < count($parts) - 1; $i++) {
 $dir .= $parts[$i] . "/";
}
$dir = rtrim($dir, "/");
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";
$config['web_folder'] = "$protocol://$dir/";
$config['web_url'] = "$protocol://$dir/index.php";
if($config['sslavailable']) {
    $config['web_secure_url'] = "https://$dir/index.php";
}
else {
    $config['web_secure_url'] = "http://$dir/index.php";
}

//Set Input Data into GET
if (isset($argv)) {
    for ($i = 1; $i < count($argv); $i++) {
        $request = preg_split('/=/', $argv[$i]);
        $_GET[$request[0]] = $request[1];
    }
    $application = new Application($config, $lang, null, null);
    $app = $application->getUrlParam($config['param_app'], "letters");
    switch ($app) {
        case "script": {
                require_once("app/ScriptApplication.php");
                $application = new ScriptApplication($config, $lang);
                break;
            }
    }
    $application->execute();
} else {
    $application = new Application($config, $lang, null, null);
    $app = $application->getUrlParam($config['param_app'], "letters");
    switch ($app) {
        case "home": {
                require_once("app/HomeApplication.php");
                $application = new HomeApplication($config, $lang);
                break;
            }
        case "json": {
                require_once("app/JsonApplication.php");
                $application = new JsonApplication($config, $lang);
                break;
            }
        case "login": {
                require_once("app/LoginApplication.php");
                $application = new LoginApplication($config, $lang);
                break;
            }
        case "private": {
                require_once("app/PrivateApplication.php");
                $application = new PrivateApplication($config, $lang);
                break;
            }
        default: {
                $application->secureRedirect("home", "home");
                break;
            }
    }
    $application->execute();
}
?>