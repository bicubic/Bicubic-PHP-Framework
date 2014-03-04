<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
require_once 'data/AtomManager.php';

class LoginNavigation extends Navigation {

    function __construct(LoginApplication $application) {
        parent::__construct($application);
    }

    public function login() {
        $this->application->setMainTemplate("login", "login", $this->lang('title_login'));
        $loginToken = $this->application->createRandomString(64);
        $this->application->setSessionParam("loginToken", $loginToken);
        $params = array(
            new SystemUser(),
            new Param("loginToken", $loginToken)
        );
        $this->application->setFormTemplate("login", $params, "login", "loginSubmit", true);
        $this->application->render();
    }

    public function loginSubmit() {
        $data = new AtomManager($this->application->data);
        $data->data->begin();
        $formToken = $this->application->getFormParam("loginToken", "string64");
        $loginToken = $this->application->getSessionParam("loginToken", "string64");
        if ($formToken != $loginToken) {
            $data->data->rollback();
            $this->application->error($this->lang('error_token_notvalid'));
        }
        $systemUser = $this->application->getFormObject(new SystemUser());
        if ($systemUser->getEmail() === null) {
            $data->data->rollback();
            $this->application->error($this->lang('error_usernamenotvalid'));
        }
        if ($systemUser->getPassword() === null) {
            $data->data->rollback();
            $this->application->error($this->lang('error_passwordnotvalid'));
        }
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
        $dbSystemUser->setToken($this->application->createRandomString(64));
        if (!$data->updateRecord($dbSystemUser)) {
            $data->data->rollback();
            $this->application->error($this->lang('error_server_error') . " " . $data->error);
        }
        $this->application->loginSet($dbSystemUser);
        $data->data->commit();
        $this->application->secureRedirect("private", "hello");
    }

    public function logout() {
        $this->application->loginUnset();
        $this->application->secureRedirect("login", "login");
    }

}

?>
