<?php

/**
 * Navegación de Acceso
 *
 * @author     Juan Francisco Rodríguez <jrodriguez@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    Bicubic Comercial Licence
 * @license
 * @framework  2.1
 */
require_once 'data/SystemUserData.php';

class LoginNavigation extends Navigation {

    /**
     * Constructor
     * @param MainApplication $loginApplication aplicación
     */
    function __construct(LoginApplication $application) {
        parent::__construct($application);
    }

    /**
     * Formulario de Login y Registro
     */
    public function login() {
        $this->application->setMainTemplate("login", "login", $this->application->lang['title_login']);
        $loginToken = $this->application->createRandomString(64);
        $this->application->setSessionParam("loginToken", $loginToken);
        // Formulario de ingreso
        $params = array(
            new SystemUser(),
            new Param("loginToken", $loginToken),
        );
        $this->application->setFormTemplate("login", $params, "login", "loginSubmit", true);
        $this->application->render();
    }

    /**
     * Valida el formulario de login
     */
    public function loginSubmit() {
        //Main Template
        $this->application->setMainTemplate("login", "login");
        //Valida el Token de Session
        $formToken = $this->application->getFormParam("loginToken", "string64");
        $loginToken = $this->application->getSessionParam("loginToken", "string64");
        if($formToken != $loginToken) {
            $this->application->error($this->application->lang['error_token_notvalid']);
        }
        //data del systemuser
        $data = new SystemUserData($this->application->data);
        $systemUser = new SystemUser();
        //Valida el nombre de uusario o emial y la contraseña
        $systemUser->setUsername($this->application->getFormParam("SystemUser_username", "string16"));
        if ($systemUser->getUsername() === null) {
            $this->application->error($this->application->lang['error_usernamenotvalid']);
        }
        $systemUser->setPassword($this->application->getFormParam("SystemUser_password", "string16"));
        if ($systemUser->getPassword() === null) {
            $this->application->error($this->application->lang['error_passwordnotvalid']);
        }
        //chequea que esten los datos correctos
        $systemUser = $data->getSystemUser($systemUser);
        if ($systemUser->getId() === null) {
            $this->application->error($this->application->lang['error_login']);
        }
        //Ingresa el Token de session
        $systemUser->setToken($this->application->createRandomString(64));
        if (!$data->updateSystemUser($systemUser)) {
            $this->application->error($this->application->lang['error_token'] . " " . $data->error);
        }
        //Inicia la session
        $rememberme = $this->application->getFormParam("rememberme", "int");
        $this->application->loginSet($systemUser);
        //Si se valida se retorna exito
        $this->application->secureRedirect("private", "hello");
    }

    /**
     * Termina la sesión
     */
    public function logout() {
        //Logout
        $this->application->loginUnset();
        //redirect
        $this->application->secureRedirect("login", "login");
    }

}

?>
