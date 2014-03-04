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
require_once 'data/AtomManager.php';

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
        $this->application->setMainTemplate("login", "login", $this->lang('title_login'));
        $loginToken = $this->application->createRandomString(64);
        $this->application->setSessionParam("loginToken", $loginToken);
        // Formulario de ingreso
        $params = array(
            new SystemUser(),
            new Param("loginToken", $loginToken)
        );
        $this->application->setFormTemplate("login", $params, "login", "loginSubmit", true);
        //finito
        $this->application->render();
    }

    /**
     * Valida el formulario de login
     */
    public function loginSubmit() {
        //data
        $data = new AtomManager($this->application->data);
        $data->data->begin();
        //Valida el Token de Session
        $formToken = $this->application->getFormParam("loginToken", "string64");
        $loginToken = $this->application->getSessionParam("loginToken", "string64");
        if ($formToken != $loginToken) {
            $data->data->rollback();
            $this->application->error($this->lang('error_token_notvalid'));
        }
        //data del systemuser
        $systemUser = $this->application->getFormObject(new SystemUser());
        //Valida el nombre de uusario o emial y la contraseña
        if ($systemUser->getEmail() === null) {
            $data->data->rollback();
            $this->application->error($this->lang('error_usernamenotvalid'));
        }
        if ($systemUser->getPassword() === null) {
            $data->data->rollback();
            $this->application->error($this->lang('error_passwordnotvalid'));
        }
        //chequea que esten los datos correctos
        $dbSystemUser = new SystemUser();
        $dbSystemUser->setEmail($systemUser->getEmail());
        $dbSystemUser = $data->getRecord($dbSystemUser);
        if (!isset($dbSystemUser)) {
            $data->data->rollback();
            $this->application->error($this->lang('error_login'));
        }
        if (crypt($systemUser->getPassword(), $dbSystemUser->getPassword()) != $dbSystemUser->getPassword()) {
            $data->data->rollback();
            $this->application->error($this->lang('error_login'));
        }
        //Ingresa el Token de session
        $dbSystemUser->setToken($this->application->createRandomString(64));
        if (!$data->updateRecord($dbSystemUser)) {
            $data->data->rollback();
            $this->application->error($this->lang('error_server_error') . " " . $data->error);
        }
        $this->application->loginSet($dbSystemUser);
        
        //finito
        $data->data->commit();
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
