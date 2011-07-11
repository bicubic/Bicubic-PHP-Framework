<?php

/**
 * Aplicación de Acceso
 *
 * @author     Juan Francisco Rodríguez <jrodriguez@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    Bicubic Comercial Licence
 * @license
 * @framework  2.1
 */
class LoginApplication extends MainApplication {

    /**
     * Constructor
     * @param array $config el array de configuración
     * @param array $lang  el array de lenguaje
     */
    function __construct($config, $lang) {
        parent::__construct($config, $lang, "login");
    }

    /**
     * Ejecuta la aplicación
     */
    public function execute() {
        //Session
        session_start();
        $this->user = $this->loginCheck();
        //Navigation
        $this->navigation = $this->getUrlParam($this->config['param_navigation'], "letters");
        switch ($this->navigation) {
            case "login" : {
                    if ($this->user !== false) {
                        $this->redirect("panel", "panel");
                    }
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
            case "message" : {
                    require_once("nav/MessageNavigation.php");
                    $messageNavigation = new MessageNavigation($this);
                    $messageNavigation->message();
                    break;
                }
            default : {
                    $this->criticalError($this->lang['error_navnotfound']);
                    break;
                }
        }
    }

    /**
     * Setea la vista que se usará dentro del template
     * @param string $navigationFolder la carpeta de la vista
     * @param string $navigationFile el nombre de los archivos de la vista
     * @param string $title el título del browser
     */
    public function setMainTemplate($navigationFolder, $navigationFile, $title="") {
        parent::setMainTemplate($navigationFolder, $navigationFile, $title);
    }

}

?>