<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @framework  2.2
 */

require_once("data/SystemUserData.php");

class PrivateApplication extends Application {

    /**
     * Constructor
     * @param array $config el array de configuración
     * @param array $lang  el array de lenguaje
     */
    function __construct($config, $lang) {
        $data = new PostgreSQLData($config['database_host'], $config['database_user'], $config['database_password'], $config['database_database'], $lang);
        parent::__construct($config, $lang, $data, "private");
    }

    /**
     * Ejecuta la aplicación
     */
    public function execute() {
        //Session
        session_start();
        $this->user = $this->loginCheck();
        if($this->user === false) {
            $this->secureRedirect("login", "login");
        }
        //Navigation
        $this->navigation = $this->getUrlParam($this->config['param_navigation'], "letters");
        switch ($this->navigation) {
            case "hello" : {
                    require_once("nav/HelloNavigation.php");
                    $messageNavigation = new HelloNavigation($this);
                    $messageNavigation->helloPrivate();
                    break;
                }
            default : {
                    $this->error($this->lang['error_navnotfound']);
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
        
        $this->setHTMLVariableTemplate('LINK-LOGOUT', $this->getSecureAppUrl("login", "logout"));
    }

}

?>