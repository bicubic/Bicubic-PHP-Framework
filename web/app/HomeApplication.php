<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
class HomeApplication extends Application {

    function __construct($config, $lang, Data $data = null, $name = "home") {
        if (!$data) {
            $data = new PostgreSQLData($config);
        }
        parent::__construct($config, $lang, $data, $name);
    }

    public function execute() {
        parent::execute();
        $this->navigation = $this->getUrlParam($this->config('param_navigation'), "letters");
        switch ($this->navigation) {
            case "home" : {
                    require_once('nav/HomeNavigation.php');
                    $navigation = new HomeNavigation($this);
                    $navigation->home();
                    break;
                }
//            case "object" : {
//                    $navigation = new Navigation($this);
//                    $navigation->objectForm(new SystemUser(), "objectSubmit");
//                    break;
//                }
//            case "objectSubmit" : {
//                    $navigation = new Navigation($this);
//                    $navigation->objectFormSubmit(new SystemUser(), "object");
//                    break;
//                }
            default : {
                    $this->redirect("home", "home");
                    break;
                }
        }
    }

    public function setMainTemplate($navigationFolder, $navigationFile, $title = "") {
        parent::setMainTemplate($navigationFolder, $navigationFile, $title);
    }

}

?>