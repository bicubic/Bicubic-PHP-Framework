<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
class LoginApplication extends Application {

    public $user;

    function __construct($config, $lang, Data $data = null, $name = "login") {
        if (!$data) {
            $data = new PostgreSQLData($config);
        }
        parent::__construct($config, $lang, $data, $name);
    }

    public function execute() {
        parent::execute();
        $this->navigation = $this->getUrlParam($this->config('param_navigation'), PropertyTypes::$_LETTERS, false);
        switch ($this->navigation) {
            case "login" : {
                    require_once("nav/LoginNavigation.php");
                    $navigation = new LoginNavigation($this);
                    $navigation->login();
                    break;
                }
            case "loginSubmit" : {
                    require_once('nav/LoginNavigation.php');
                    $navigation = new LoginNavigation($this);
                    $navigation->loginSubmit();
                    break;
                }
            case "logout" : {
                    require_once("nav/LoginNavigation.php");
                    $navigation = new LoginNavigation($this);
                    $navigation->logout();
                    break;
                }
            case "signUpSubmit" : {
                    require_once('nav/LoginNavigation.php');
                    $navigation = new LoginNavigation($this);
                    $navigation->signUpSubmit();
                    break;
                }
            case "validate" : {
                    require_once('nav/LoginNavigation.php');
                    $navigation = new LoginNavigation($this);
                    $navigation->validate();
                    break;
                }
            case "revalidate" : {
                    require_once('nav/LoginNavigation.php');
                    $navigation = new LoginNavigation($this);
                    $navigation->resendValidation();
                    break;
                }
            case "forgot" : {
                    require_once("nav/LoginNavigation.php");
                    $navigation = new LoginNavigation($this);
                    $navigation->forgot();
                    break;
                }
            case "forgotSubmit" : {
                    require_once('nav/LoginNavigation.php');
                    $navigation = new LoginNavigation($this);
                    $navigation->forgotSubmit();
                    break;
                }
            case "forgotValidate" : {
                    require_once('nav/LoginNavigation.php');
                    $navigation = new LoginNavigation($this);
                    $navigation->forgotValidate();
                    break;
                }
            case "forgotValidateSubmit" : {
                    require_once('nav/LoginNavigation.php');
                    $navigation = new LoginNavigation($this);
                    $navigation->forgotValidateSubmit();
                    break;
                }
            case "editaccount" : {
                    require_once('nav/LoginNavigation.php');
                    $navigation = new LoginNavigation($this);
                    $navigation->editAccount();
                    break;
                }
            case "editaccountSubmit" : {
                    require_once('nav/LoginNavigation.php');
                    $navigation = new LoginNavigation($this);
                    $navigation->editAccountSubmit();
                    break;
                }
            case "editpassword" : {
                    require_once('nav/LoginNavigation.php');
                    $navigation = new LoginNavigation($this);
                    $navigation->editPassword();
                    break;
                }
            case "editpasswordSubmit" : {
                    require_once('nav/LoginNavigation.php');
                    $navigation = new LoginNavigation($this);
                    $navigation->editPasswordSubmit();
                    break;
                }
            case "editEmail" : {
                    require_once('nav/LoginNavigation.php');
                    $navigation = new LoginNavigation($this);
                    $navigation->editEmail();
                    break;
                }
            case "editEmailSubmit" : {
                    require_once('nav/LoginNavigation.php');
                    $navigation = new LoginNavigation($this);
                    $navigation->editEmailSubmit();
                    break;
                }
            case "editEmailValidate" : {
                    require_once('nav/LoginNavigation.php');
                    $navigation = new LoginNavigation($this);
                    $navigation->editEmailValidate();
                    break;
                }
            case "editEmailValidateSubmit" : {
                    require_once('nav/LoginNavigation.php');
                    $navigation = new LoginNavigation($this);
                    $navigation->editEmailValidateSubmit();
                    break;
                }
            default : {
                    $this->secureRedirect("login", "logout");
                    break;
                }
        }
    }

    public function setMainTemplate($navigationFolder, $navigationFile, $title = "") {
        parent::setMainTemplate($navigationFolder, $navigationFile, $title);
        $this->setHTMLVariableTemplate('LINK-HOME', $this->getSecureAppUrl("home", "hello"));
    }

}

?>