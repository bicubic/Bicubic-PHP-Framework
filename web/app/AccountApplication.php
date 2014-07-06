<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
class AccountApplication extends Application {

    public $user;

    function __construct($config, $lang, Data $data = null, $name = "account") {
        if (!$data) {
            $data = new PostgreSQLData($config);
        }
        parent::__construct($config, $lang, $data, $name);
    }

    public function execute() {
        parent::execute();
        require_once("nav/LoginNavigation.php");
        $navigation = new LoginNavigation($this);
        $this->user = $navigation->loginCheck();
        if (!$this->user) {
            $this->redirect("login", "login");
        }
        $this->navigation = $this->getUrlParam($this->config('param_navigation'), PropertyTypes::$_LETTERS, false);
        switch ($this->navigation) {
            case "logout" : {
                    require_once("nav/LoginNavigation.php");
                    $navigation = new LoginNavigation($this);
                    $navigation->logout("home", "home");
                    break;
                }
            case "revalidate" : {
                    require_once('nav/LoginNavigation.php');
                    $navigation = new LoginNavigation($this);
                    $navigation->resendValidation();
                    break;
                }
            case "account" : {
                    require_once('nav/LoginNavigation.php');
                    $navigation = new LoginNavigation($this);
                    $navigation->account();
                    break;
                }
            case "profileSubmit" : {
                    require_once('nav/LoginNavigation.php');
                    $navigation = new LoginNavigation($this);
                    $navigation->profileSubmit();
                    break;
                }
            case "passwordSubmit" : {
                    require_once('nav/LoginNavigation.php');
                    $navigation = new LoginNavigation($this);
                    $navigation->passwordSubmit();
                    break;
                }
            case "emailSubmit" : {
                    require_once('nav/LoginNavigation.php');
                    $navigation = new LoginNavigation($this);
                    $navigation->emailSubmit();
                    break;
                }
        }
    }

}

?>