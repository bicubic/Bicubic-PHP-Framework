<?php

/**
 * Bicubic PHP Framework
 * Index File 
 *
 * @author     Juan Rodríguez-Covili <jrodriguez@bicubic.cl>
 * @copyright  2010 Bicubic Technology - http://www.bicubic.cl
 * @license    Bicubic Comercial Licence
 * @license    Licenced for ONEMI, Chile
 * @framework  2.1
 */
//base
require_once("config.php");
require_once("lang.php");
require_once("error.php");
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

//beans
require_once("beans/App.php");
require_once("beans/Historial.php");
require_once("beans/Content.php");
require_once("beans/Requirement.php");

//params
require_once("param/MessageParam.php");
//Main Application
require_once("app/MainApplication.php");
//Datas
require_once("data/AppData.php");


//Set Input Data into GET
if (isset($argv)) {
    for ($i = 1; $i < count($argv); $i++) {
        $request = preg_split('/=/', $argv[$i]);
        $_GET[$request[0]] = $request[1];
    }
}
//Determines the $application to run
$application = new MainApplication($config, $lang);
$app = $application->getUrlParam($config['param_app'], "letters");
if (isset($app)) {
    switch ($app) {
        case "login": {
                require_once("app/LoginApplication.php");
                $application = new LoginApplication($config, $lang);
                break;
            }
        case "panel": {
                require_once("app/PanelApplication.php");
                $application = new PanelApplication($config, $lang);
                break;
            }
        case "profile": {
                require_once("app/ProfileApplication.php");
                $application = new ProfileApplication($config, $lang);
                break;
            }
        case "json": {
                require_once("app/JsonApplication.php");
                $application = new JsonApplication($config, $lang);
                break;
            }
        default: {
                $application->criticalError($lang['error_appnotfound']);
            }
    }
}
//Runs the $application navigation
$application->execute();
?>
