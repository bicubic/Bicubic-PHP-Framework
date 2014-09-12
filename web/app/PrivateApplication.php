<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */

require_once("app/AccountApplication.php");

class PrivateApplication extends AccountApplication {

    function __construct($config, $lang, Data $data = null, $name = "private") {
        if (!$data) {
            $data = new PostgreSQLData($config);
        }
        parent::__construct($config, $lang, $data, $name);
    }

    public function execute() {
        parent::execute();
        $this->navigation = $this->getUrlParam($this->config('param_navigation'), PropertyTypes::$_LETTERS, false);
        switch ($this->navigation) {
            case "home" : {
                    require_once("nav/HelloNavigation.php");
                    $navigation = new HelloNavigation($this);
                    $navigation->helloPrivate();
                    break;
                }
            default : {
                    $this->redirect($this->name, "home");
                    break;
                }
        }
    }

    public function setMainTemplate($navigationFolder, $navigationFile, $title = "") {
        parent::setMainTemplate($navigationFolder, $navigationFile, $title);
        $this->setHTMLVariableTemplate('LINK-ACCOUNT', $this->getAppUrl($this->name, "account"));
        $this->setHTMLVariableTemplate('LINK-LOGOUT', $this->getAppUrl($this->name, "logout"));
        $this->parseTemplate("USER");
        if ($this->user->getConfirmemailtoken()) {
            $this->setHTMLVariableTemplate('LINK-REVALIDATE', $this->getAppUrl($this->name, "revalidate", array(new Param("token", $this->user->getConfirmemailtoken()))));
            $this->parseTemplate("REVALIDATE");
        }
    }

}

