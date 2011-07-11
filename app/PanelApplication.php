<?php

/**
 * Aplicación del Panel Principal
 *
 * @author     Juan Francisco Rodríguez <jrodriguez@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    Bicubic Comercial Licence
 * @license
 * @framework  2.1
 */
class PanelApplication extends MainApplication {

    /**
     * El panel principal
     * @param array $config el array de la configuracion
     * @param array $lang el array del lenguaje
     */
    function __construct($config, $lang) {
        parent::__construct($config, $lang, "panel");
    }

    /**
     * Ejecuta la aplicación
     */
    public function execute() {
        //Session
        session_start();

//        $this->user = $this->loginCheck();
//        if ($this->user === false) {
//            $this->redirect("login", "logout");
//        }
        //Navigation
        $this->navigation = $this->getUrlParam($this->config['param_navigation'], "letters");
        switch ($this->navigation) {
            case "insertApp" : {
                    require_once("nav/AppNavigation.php");
                    $navigation = new AppNavigation($this);
                    $navigation->insertSubmit();
                    break;
                }
            case "application" : {
                    require_once("nav/AppNavigation.php");
                    $navigation = new AppNavigation($this);
                    $navigation->insert();
                    break;
            }
            case "editApp" : {
                    require_once("nav/AppNavigation.php");
                    $navigation = new AppNavigation($this);
                    $navigation->edit();
                    break;
            }
            case "editAppSubmit" : {
                    require_once("nav/AppNavigation.php");
                    $navigation = new AppNavigation($this);
                    $navigation->editSubmit();
                    break;
            }
            default : {
                    $this->error($this->lang['error_navnotfound']);
                    break;
                }
        }
    }

    /**
     * Setea la vista dentro del template
     * @param string $navigationFolder la carpeta de la vista
     * @param string $navigationFile el nombre de los archivos de la vista
     * @param string $title el titulo de la vista
     */
    public function setMainTemplate($navigationFolder, $navigationFile, $title="") {
        parent::setMainTemplate($navigationFolder, $navigationFile, $title);
        /**
         * Links Menu
         */
        $this->setHTMLVariableTemplate('TEMPLATE-MENU-EMPRESASEMISORAS', $this->getAppUrl("panel", "empresasemisoras"));
    }

}

?>
