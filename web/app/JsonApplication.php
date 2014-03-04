<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
class JsonApplication extends Application {

    function __construct($config, $lang, Data $data = null, $name = "json") {
        if (!$data) {
            $data = new PostgreSQLData($config);
        }
        parent::__construct($config, $lang, $data, $name);
    }

    public function execute() {
        parent::execute();
        $this->navigation = $this->getUrlParam($this->config('param_navigation'), "letters");
        switch ($this->navigation) {
            case "hello" : {
                    require_once('nav/HelloNavigation.php');
                    $navigation = new HelloNavigation($this);
                    $navigation->helloJson();
                    break;
                }
            default : {
                    $this->error($this->lang('error_navnotfound'));
                    break;
                }
        }
    }

    public function error($message) {
        $this->setMainTemplate("message", "error");
        $this->setJsonVariableTemplate('MESSAGE-TEXT', $message);
        $this->render();
    }

}

?>