<?php
/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
//constants
define('BICUBIC_TIMEOUT', 300); 
define('BICUBIC_TIMEZONE', 'America/Santiago'); 
define('BICUBIC_ERRORREPORT', E_ALL & ~E_STRICT); //E_ERROR | E_PARSE | E_NOTICE | E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE
//base
require_once("config/config.php");
//default php config
date_default_timezone_set(BICUBIC_TIMEZONE);
error_reporting(BICUBIC_ERRORREPORT);
set_time_limit(BICUBIC_TIMEOUT);
//lib
require_once("lib/ext/pear/Sigma.php");
require_once("lib/ext/simple_html_dom.php");
require_once("lib/ext/pear/Date.php");
require_once("lib/ext/pear/Mail.php");
require_once("lib/ext/thread/singleton.class.php");
//bicubic
require_once("lib/bicubic/LibConstant.php");
require_once("lib/bicubic/Application.php");
require_once("lib/bicubic/Data.php");
require_once("lib/bicubic/DataObject.php");
require_once("lib/bicubic/Navigation.php");
require_once("lib/bicubic/ObjectParamList.php");
require_once("lib/bicubic/Param.php");
require_once("lib/bicubic/SQLData.php");
require_once("lib/bicubic/PostgreSQLData.php");
require_once("lib/bicubic/MandrillEmail.php");
require_once("lib/bicubic/SMTPEmail.php");
require_once("lib/bicubic/TransactionManager.php");
//json
require_once("lib/bicubic/ErrorJson.php");
require_once("lib/bicubic/ObjectJson.php");
require_once("lib/bicubic/SuccessJson.php");
//data
require_once ("data/AtomManager.php");
//beans
require_once("beans/Constant.php");
require_once("beans/SystemUser.php");
require_once("beans/SystemUserLog.php");
//Params
$config['param_app'] = "app";
$config['param_navigation'] = "nav";
$config['param_id'] = "id";
$config['param_compress'] = "cp";
$config['param_lang'] = "lang";
//Folders
$config['folder_template'] = "templates/";
$config['folder_navigation'] = "views/";
$config['folder_uploads'] = "uploads/";
$config['folder_images'] = "images/";
//set languaje
$langfile = Lang::$_LANGVALUE[Lang::$_DEFAULT];
require_once("lang/lang.$langfile.php");
$application = new Application($config, $lang, null, null);
//Lang from browser
if (array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER)) {
    $langfile = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
}
//Lang from URL
$urllocale = $application->getUrlParam($config['param_lang'], PropertyTypes::$_STRING256, false);
if (isset($urllocale)) {
    $langfile = $urllocale;
}
//Lang from Facebook
$fblocale = $application->getUrlParam("fb_locale", PropertyTypes::$_STRING, false);
if (isset($fblocale)) {
    $langfile = substr($fblocale, 0, 2);
}
//Check Lang
if (!array_key_exists($langfile, Lang::$_LANGKEY)) {
    $langfile = Lang::$_LANGVALUE[Lang::$_DEFAULT];
}
//Lang reload
require_once("lang/lang.$langfile.php");
$config["lang"] = $langfile;
//set temp folder
$config['server_temp_folder'] = sys_get_temp_dir() . "/";
//set working foler
$url = array_key_exists('REQUEST_URI', $_SERVER) ? $_SERVER['REQUEST_URI'] : ""; //returns the current URL
$parts = explode('/',$url);
$dir =  array_key_exists('SERVER_NAME', $_SERVER) ? $_SERVER['SERVER_NAME'] : "";
for ($i = 0; $i < count($parts) - 1; $i++) {
 $dir .= $parts[$i] . "/";
}
$dir = rtrim($dir, "/");
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || (array_key_exists('SERVER_PORT', $_SERVER) ? $_SERVER['SERVER_PORT'] == 443 : false)) ? "https" : "http";
$config['web_folder'] = "$protocol://$dir/";
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
    $app = $application->getUrlParam($config['param_app'], PropertyTypes::$_LETTERS);
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
    $app = $application->getUrlParam($config['param_app'], PropertyTypes::$_LETTERS, false);
    switch ($app) {
        case "home": {
                require_once("app/HomeApplication.php");
                $application = new HomeApplication($config, $lang);
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
        case "json": {
                require_once("app/JsonApplication.php");
                $application = new JsonApplication($config, $lang);
                break;
            }
        default: {
                $application->secureRedirect("home", "hello");
                break;
            }
    }
    $application->execute();
}
?>