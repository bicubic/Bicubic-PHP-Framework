<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
class PrivateApplication extends LoginApplication {

    function __construct($config, $lang, Data $data = null, $name = "private") {
        if (!$data) {
            $data = new PostgreSQLData($config);
        }
        parent::__construct($config, $lang, $data, $name);
    }

    public function execute() {
        parent::execute();
        $this->user = $this->loginCheck();
        if ($this->user === false) {
            $this->secureRedirect("login", "login");
        }
        $this->navigation = $this->getUrlParam($this->config('param_navigation'), "letters");
        switch ($this->navigation) {
            case "hello" : {
                    require_once("nav/HelloNavigation.php");
                    $messageNavigation = new HelloNavigation($this);
                    $messageNavigation->helloPrivate();
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
        $this->setHTMLVariableTemplate('LINK-LOGOUT', $this->getSecureAppUrl("login", "logout"));
    }

}

?>