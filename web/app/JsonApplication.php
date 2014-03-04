<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @framework  2.2
 */
class JsonApplication extends Application {

    /**
     * Constructor
     * @param array $config el array de configuracion
     * @param array $lang el array de lenguaje
     */
    function __construct($config, $lang) {
        parent::__construct($config, $lang, null, "json");
    }

    /**
     * Ejecuta la aplicación
     */
    public function execute() {
        parent::execute();
        //Navigation
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

    /**
     * Gatilla un error
     * @param string $message  el mensaje del error
     */
    public function error($message) {
        $this->setMainTemplate("message", "error");
        $this->setJsonVariableTemplate('MESSAGE-TEXT', $message);
        $this->render();
    }

}

?>
