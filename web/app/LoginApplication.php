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
        $this->navigation = $this->getUrlParam($this->config('param_navigation'), "letters");
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
            default : {
                $this->secureRedirect("login", "logout");
                break;
            }
        }
    }

    public function setMainTemplate($navigationFolder, $navigationFile, $title = "") {
        parent::setMainTemplate($navigationFolder, $navigationFile, $title);
        $this->setHTMLVariableTemplate('LINK-HOME', $this->getSecureAppUrl("home", "home"));
    }

    public function loginCheck() {
        //Check Params
        $login = $this->getSessionParam("BAClogin");
        $user = $this->getSessionParam("BACuser");
        $rememberme = $this->getSessionParam("BACrememberme");
        if (!isset($login)) {
            return false;
        }
        if (!isset($user)) {
            return false;
        }
        if (!$login) {
            return false;
        }
        //Check time out
        if (!$rememberme && $this->config('web_time_out') > 0) {
            $time = $this->getSessionParam("BACtime");
            if (!isset($time)) {
                return false;
            }
            if ($time + $this->config('web_time_out') < time()) {
                return false;
            }
            $this->setSessionParam("time", time());
        }

        if ($user !== false) {
            if ($this->data != null) {
                $data = new TransactionManager($this->data);
                $dataBaseUser = $data->getRecord($user);
                if (isset($dataBaseUser) && $user->getToken() === $dataBaseUser->getToken()) {
                    return $user;
                }
            } else {
                return $user;
            }
        }
        return false;
    }

    public function loginSet($user, $rememberme = false) {
        $this->setSessionParam("BAClogin", true);
        $this->setSessionParam("BACuser", $user);
        $this->setSessionParam("BACtime", time());
        $this->setSessionParam("BACrememberme", $rememberme);
    }

    public function loginUnset() {
        $this->killSessionParam("BAClogin");
        $this->killSessionParam("BACuser");
        $this->killSessionParam("BACtime");
        $this->killSessionParam("BACrememberme");
        session_destroy();
    }

}

?>