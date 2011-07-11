<?php

/**
 * Aplicación de peticiones JSon
 *
 * @author     Juan Francisco Rodríguez <jrodriguez@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    Bicubic Comercial Licence
 * @license
 * @framework  2.1
 */
class JsonApplication extends MainApplication {

    /**
     * Constructor
     * @param array $config el array de configuracion
     * @param array $lang el array de lenguaje
     */
    function __construct($config, $lang) {
        parent::__construct($config, $lang, "json");
    }

    /**
     * Ejecuta la aplicación
     */
    public function execute() {
        //Session
        session_start();
        //Navigation
        $this->navigation = $this->getUrlParam($this->config['param_navigation'], "letters");
        switch ($this->navigation) {
            case "testApp" : {
                    require_once('nav/AppNavigation.php');
                    $navigation = new AppNavigation($this);
                    $navigation->test();
                    break;
                }
            case "standalonerequest" : {
                    require_once('nav/ContentRequestNavigation.php');
                    $navigation = new ContentRequestNavigation($this);
                    $navigation->standAloneRequest();
                    break;
                }
            case "pruebaselect" : {
                    require_once('nav/ContentRequestNavigation.php');
                    $navigation = new ContentRequestNavigation($this);
                    $navigation->pruebaselect();
                    break;
                }
            default : {
                    $this->error($this->lang['error_navnotfound']);
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
