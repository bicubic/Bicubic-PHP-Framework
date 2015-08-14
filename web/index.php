<?php

/*
 * The MIT License
 *
 * Copyright 2015 Juan Francisco Rodríguez.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

//base
require_once("config.php");
//default php config
date_default_timezone_set($config['code_time_zone']);
error_reporting($config['code_error_report']);
set_time_limit($config['code_time_out']);
//lib
require_once("lib/ext/pear/Sigma.php");
require_once("lib/ext/simple_html_dom.php");
require_once("lib/ext/pear/Date.php");
require_once("lib/ext/pear/Mail.php");
require_once("lib/ext/thread/singleton.class.php");
//bicubic
require_once("lib/bicubic/LibConstant.php");
require_once("lib/bicubic/Lang.php");
require_once("lib/bicubic/Country.php");
require_once("lib/bicubic/Application.php");
require_once("lib/bicubic/ScriptApplication.php");
require_once("lib/bicubic/Data.php");
require_once("lib/bicubic/DataObject.php");
require_once("lib/bicubic/Navigation.php");
require_once("lib/bicubic/MaintainerNavigation.php");
require_once("lib/bicubic/ObjectParamList.php");
require_once("lib/bicubic/Param.php");
require_once("lib/bicubic/LinkParam.php");
require_once("lib/bicubic/OrderParam.php");
require_once("lib/bicubic/SQLData.php");
require_once("lib/bicubic/MySQLData.php");
require_once("lib/bicubic/PostgreSQLData.php");
require_once("lib/bicubic/MandrillEmail.php");
require_once("lib/bicubic/SMTPEmail.php");
require_once("lib/bicubic/TransactionManager.php");
//json
require_once("lib/bicubic/ErrorJson.php");
require_once("lib/bicubic/ObjectJson.php");
require_once("lib/bicubic/SuccessJson.php");
//custom
require_once("factory.php");
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
//filter langs
$allLangs = Lang::$_ENUM;
$filterLangs = array();
foreach (LangFactory::getAvailableLangList() as $key => $value) {
    $filterLangs [$value] = $allLangs[$value];
}
Lang::$_ENUM = $filterLangs;
Lang::$_DEFAULT = LangFactory::getDefaultLang();
//set languaje
$langfile = Lang::$_DEFAULT;
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
if (!array_key_exists($langfile, Lang::$_ENUM)) {
    $langfile = Lang::$_DEFAULT;
}
//Lang reload
if (file_exists("lang/lang.$langfile.php")) {
    require_once("lang/lang.$langfile.php");
} else {
    $langfile = Lang::$_DEFAULT;
    require_once("lang/lang.$langfile.php");
}

$config["lang"] = $langfile;
//set temp folder
$config['server_temp_folder'] = sys_get_temp_dir() . "/";
//set working foler
$url = array_key_exists('REQUEST_URI', $_SERVER) ? $_SERVER['REQUEST_URI'] : ""; //returns the current URL
$port = array_key_exists('SERVER_PORT', $_SERVER) ? $_SERVER['SERVER_PORT'] : 80; //returns the current port
$parts = explode('/', $url);
$dir = array_key_exists('SERVER_NAME', $_SERVER) ? $_SERVER['SERVER_NAME'] : "";
if ($port != 80 && $port != 443) {
    $dir .= ":$port";
}
for ($i = 0; $i < count($parts) - 1; $i++) {
    $dir .= $parts[$i] . "/";
}
$dir = rtrim($dir, "/");
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || (array_key_exists('SERVER_PORT', $_SERVER) ? $_SERVER['SERVER_PORT'] == 443 : false)) ? "https" : "http";
$config['web_folder'] = "$protocol://$dir/";
if ($config['sslavailable']) {
    $config['web_secure_url'] = "https://$dir/index.php";
} else {
    $config['web_secure_url'] = "http://$dir/index.php";
}
//Set Input Data into GET
if (isset($argv)) {
    for ($i = 1; $i < count($argv); $i++) {
	$request = preg_split('/=/', $argv[$i]);
	$_GET[$request[0]] = $request[1];
    }
    //fix base url for test
    $config['web_folder'] = $config['urlbase'];
    $config['web_secure_url'] = $config['urlbase'];
    $app = $application->getUrlParam($config['param_app'], PropertyTypes::$_LETTERS);
    switch ($app) {
	case "script": {
		$application = new ScriptApplication($config, $lang);
		$application->execute();
		break;
	    }
	default : {
		$application = ApplicationFactory::makeScriptApplication($app, $config, $lang);
		if ($application) {
		    $application->execute();
		} else {
		    ApplicationFactory::defaultScriptApplication($config, $lang);
		}
	    }
    }
} else {
    $app = $application->getUrlParam($config['param_app'], PropertyTypes::$_LETTERS, false);
    $application = ApplicationFactory::makeWebApplication($app, $config, $lang);
    if ($application) {
	$application->execute();
    } else {
	ApplicationFactory::defaultWebApplication($config, $lang);
    }
}
