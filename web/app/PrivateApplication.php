<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
class PrivateApplication extends Application {

    function __construct($config, $lang, Data $data = null, $name = "private") {
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
            $this->secureRedirect("login", "login");
        }
        $this->navigation = $this->getUrlParam($this->config('param_navigation'), PropertyTypes::$_LETTERS, false);
        switch ($this->navigation) {
            case "hello" : {
                    require_once("nav/HelloNavigation.php");
                    $navigation = new HelloNavigation($this);
                    $navigation->helloPrivate();
                    break;
                }
            default : {
                    $this->error($this->lang('lang_navnotfound'));
                    break;
                }
        }
    }

    public function setMainTemplate($navigationFolder, $navigationFile, $title = "") {
        parent::setMainTemplate($navigationFolder, $navigationFile, $title);
        if ($this->user) {
            $this->setHTMLVariableTemplate('LINK-ACCOUNT', $this->getSecureAppUrl("login", "account"));
            $this->setHTMLVariableTemplate('LINK-LOGOUT', $this->getSecureAppUrl("login", "logout"));
            $this->parseTemplate("USER");
            if ($this->user->getConfirmemailtoken()) {
                $this->setHTMLVariableTemplate('LINK-REVALIDATE', $this->getSecureAppUrl("login", "revalidate", array(new Param("token", $this->user->getConfirmemailtoken()))));
                $this->parseTemplate("REVALIDATE");
            }
        }
    }

}

?>