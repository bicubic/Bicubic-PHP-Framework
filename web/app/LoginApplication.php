<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @framework  2.2
 */



class LoginApplication extends Application {
    //logged user object
    public $user;
    /**
     * Constructor
     * @param array $config el array de configuración
     * @param array $lang  el array de lenguaje
     */
    function __construct($config, $lang) {
        $data = new PostgreSQLData($config['database_host'], $config['database_user'], $config['database_password'], $config['database_database'], $lang);
        parent::__construct($config, $lang, $data, "login");
    }

    /**
     * Ejecuta la aplicación
     */
    public function execute() {
        parent::execute();
        //Navigation
        $this->navigation = $this->getUrlParam($this->config('param_navigation'), "letters");
        switch ($this->navigation) {
            case "login" : {
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
            case "logout" : {
                    require_once("nav/LoginNavigation.php");
                    $navigation = new LoginNavigation($this);
                    $navigation->logout();
                    break;
                }
            default : {
                    $this->secureRedirect("login", "login");
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
    
    /**
     * Verifica que exista un session de login activa
     * @return El usuario de la session activa
     */
    public function loginCheck() {
        //Check Params
        $login = $this->getSessionParam("BAClogin");
        $user = $this->getSessionParam("BACuser");
        $rememberme = $this->getSessionParam("BACrememberme");
        if (!isset($login)) {
            return false;
        }
        if (!isset($user)) {
            return false;
        }
        if (!$login) {
            return false;
        }
        //Check time out
        if (!$rememberme && $this->config('web_time_out') > 0) {
            $time = $this->getSessionParam("BACtime");
            if (!isset($time)) {
                return false;
            }
            if ($time + $this->config('web_time_out') < time()) {
                return false;
            }
            $this->setSessionParam("time", time());
        }

        if ($user !== false) {
            if ($this->data != null) {
                $data = new TransactionManager($this->data);
                $dataBaseUser = $data->getRecord($user);
                if (isset($dataBaseUser) && $user->getToken() === $dataBaseUser->getToken()) {
                    return $user;
                }
            } else {
                return $user;
            }
        }
        return false;
    }

    /**
     * Setea los datos de session del login
     * @param SystemUser $user <p>El usuario de session</p>
     * @return Nada
     */
    public function loginSet($user, $rememberme = false) {
        $this->setSessionParam("BAClogin", true);
        $this->setSessionParam("BACuser", $user);
        $this->setSessionParam("BACtime", time());
        $this->setSessionParam("BACrememberme", $rememberme);
    }

    /**
     * Elimina los datos de session del login
     * @return Nada
     */
    public function loginUnset() {
        $this->killSessionParam("BAClogin");
        $this->killSessionParam("BACuser");
        $this->killSessionParam("BACtime");
        $this->killSessionParam("BACrememberme");

        session_destroy();
    }
}
?>