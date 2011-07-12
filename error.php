<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @framework  2.2
 */

require_once("lib/ext/pear/Sigma.php");
require_once("lib/ext/simple_html_dom.php");

error_reporting(E_ALL);
//error_reporting(E_ERROR | E_PARSE | E_NOTICE | E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE);
set_time_limit(60 * 20);
$debug = true;
set_error_handler('phpError');
date_default_timezone_set("America/Santiago");

function phpError($errornumber, $errormessage, $errorfile, $errorrow) {
    global $config;
    global $lang;
    global $debug;
    
    if (!(error_reporting() & $errornumber)) {
        return;
    }
    $tpl =new HTML_Template_Sigma();
    $tpl->loadTemplateFile($config['folder_template'] . "error/template.html");
    $tpl->addBlockfile("TEMPLATE-CONTENT", "error", $config['folder_navigation'] . "message/message.html");
    $tpl->setVariable('MESSAGE-TEXT', $lang['error_global']);
    $tpl->parse('MESSAGES');
    if ($debug) {
        $tpl->setVariable('MESSAGE-TEXT', "$errornumber: $errormessage AT $errorfile Line:$errorrow");
        $tpl->parse('MESSAGES');
    }
    $tpl->touchBlock("error");
    $tpl->show();
    exit();
}
?>
