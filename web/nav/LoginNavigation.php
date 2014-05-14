<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */

require_once("nav/AccountNavigation.php");
require_once("int/ForgotEmail.php");
require_once("int/NewEmailEmail.php");

class LoginNavigation extends AccountNavigation {

    function __construct(Application $application) {
        parent::__construct($application);
    }

    public function login() {
        $this->application->setMainTemplate("login", "login", $this->lang('lang_login'));
        $loginToken = $this->application->createRandomString(64);
        $this->application->setSessionParam("LoginNavigation_logintoken", $loginToken);
        $params = array(
            new SystemUser(),
            new Param("loginToken", $loginToken)
        );
        $this->application->setFormTemplate("login", $params, "login", "loginSubmit");
        $this->application->setHTMLVariableTemplate('LINK-FORGOT', $this->application->getSecureAppUrl("login", "forgot"));
        $this->application->render();
    }

    public function loginSubmit() {
        $data = new AtomManager($this->application->data);
        $data->data->begin();
        $systemUser = $this->application->getFormObject(new SystemUser());
        $dbSystemUser = $data->getRecord(new SystemUser(null, $systemUser->getEmail()));
        if (!$dbSystemUser) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_loginerror'));
        }
        if (crypt($systemUser->getPassword(), $dbSystemUser->getPassword()) != $dbSystemUser->getPassword()) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_loginerror'));
        }
        $dbSystemUser->setSessiontoken($this->application->createRandomString(1024));
        if (!$data->updateRecord($dbSystemUser)) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_servererror'));
        }
        $this->loginSet($dbSystemUser);
        $data->data->commit();
        $this->application->secureRedirect("private", "hello");
    }

    public function logout() {
        $this->loginUnset();
        $this->application->secureRedirect("login", "login");
    }

    public function validate() {
        $data = new AtomManager($this->application->data);
        $data->data->begin();
        $token = $this->application->getUrlParam("token", PropertyTypes::$_STRING1024);
        $dbSystemUser = new SystemUser();
        $dbSystemUser->setConfirmemailtoken($token);
        $dbSystemUser = $data->getRecord($dbSystemUser);
        if (!$dbSystemUser) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_tokenerror'));
        }
        $dbSystemUser->setConfirmemailtoken(null);
        if (!$data->updateRecord($dbSystemUser)) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_servererror'));
        }
        $data->data->commit();
        $this->application->message($this->lang('lang_emailconfirmationsuccess'));
    }

    public function resendValidation() {
        $data = new AtomManager($this->application->data);
        $data->data->begin();
        $token = $this->application->getUrlParam("token", PropertyTypes::$_STRING1024);
        $dbSystemUser = new SystemUser();
        $dbSystemUser->setConfirmemailtoken($token);
        $dbSystemUser = $data->getRecord($dbSystemUser);
        if (!$dbSystemUser) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_tokenerror'));
        }
        $email = new ConfirmationEmail($dbSystemUser, $this);
        $email->send();
        $data->data->commit();
        $this->application->message($this->lang('lang_emailconfirmationresent'));
    }

    public function forgot() {
        $this->application->setMainTemplate("login", "forgot", $this->lang('lang_forgot'));
        $forgotToken = $this->application->createRandomString(64);
        $this->application->setSessionParam("LoginNavigation_forgottoken", $forgotToken);
        $params = array(
            new SystemUser(),
            new Param("forgotToken", $forgotToken)
        );
        $this->application->setFormTemplate("forgot", $params, "login", "forgotSubmit");
        $this->application->render();
    }

    public function forgotSubmit() {
        $data = new AtomManager($this->application->data);
        $data->data->begin();
        $formToken = $this->application->getFormParam("forgotToken", PropertyTypes::$_STRING64);
        $forgotToken = $this->application->getSessionParam("LoginNavigation_forgottoken");
        if ($formToken != $forgotToken) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_tokenerror'));
        }
        $systemUser = $this->application->getFormObject(new SystemUser(), false);
        if (!$systemUser->getEmail()) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_emailerror'));
        }
        $dbSystemUser = $data->getRecord(new SystemUser(null, $systemUser->getEmail()));
        if (!$dbSystemUser) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_emailerror'));
        }
        $dbSystemUser->setForgottoken($this->application->createRandomString(1024));
        if (!$data->updateRecord($dbSystemUser)) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_servererror'));
        }
        $email = new ForgotEmail($dbSystemUser, $this);
        $email->send();
        $data->data->commit();
        $this->application->message($this->lang('lang_emailforgotsent'));
    }

    public function forgotValidate() {
        $data = new AtomManager($this->application->data);
        $data->data->begin();
        $token = $this->application->getUrlParam("token", PropertyTypes::$_STRING1024);
        $dbSystemUser = new SystemUser();
        $dbSystemUser->setForgottoken($token);
        $dbSystemUser = $data->getRecord($dbSystemUser);
        if (!$dbSystemUser) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_tokenerror'));
        }
        $this->application->setMainTemplate("login", "password", $this->lang('lang_newpassword'));
        $this->application->setSessionParam("LoginNavigation_forgottoken", $forgotToken);
        $params = array(
            new SystemUser(),
            new Param("token",$token),
            new Param("confirmpassword"),
        );
        $this->application->setFormTemplate("newpassword", $params, "login", "forgotValidateSubmit");
        $data->data->commit();
        $this->application->render();
    }
    
    public function forgotValidateSubmit() {
        $data = new AtomManager($this->application->data);
        $data->data->begin();
        $token = $this->application->getFormParam("token", PropertyTypes::$_STRING1024);
        $systemUser = $this->application->getFormObject(new SystemUser(), false);
        $confirmPassword = $this->application->getFormParam("confirmpassword", PropertyTypes::$_STRING2048);
        if (!$systemUser->getPassword()) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_passworderror'));
        }
        if ($systemUser->getPassword() !== $confirmPassword) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_passworderror'));
        }
        $dbSystemUser = new SystemUser();
        $dbSystemUser->setForgottoken($token);
        $dbSystemUser = $data->getRecord($dbSystemUser);
        if (!$dbSystemUser) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_tokenerror'));
        }
        
        $dbSystemUser->setForgottoken(null);
        $dbSystemUser->setPassword($this->application->blowfishCrypt($systemUser->getPassword(), 10));
        if (!$data->updateRecord($dbSystemUser)) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_servererror'));
        }
        $data->data->commit();
        $this->application->message($this->lang('lang_emailforgotsuccess'));
    }
    
    public function account() {
        $user = $this->loginCheck();
        if (!$user) {
            $this->application->error($this->lang('lang_servererror'));
        }
        $this->application->setMainTemplate("login", "account", $this->lang('lang_account'));
        $params = array(
            $user,
        );
        $this->application->setFormTemplate("editprofile", $params, "login", "profileSubmit");
        $params = array(
            new Param("email", $user->getEmail()),
        );
        $this->application->setFormTemplate("editemail", $params, "login", "emailSubmit");
        $params = array(
            new Param("currentpassword"),
            new Param("newpassword"),
            new Param("confirmpassword"),
        );
        $this->application->setFormTemplate("editpassword", $params, "login", "passwordSubmit");
        $this->application->render();
    }
    
    public function profileSubmit() {
        $user = $this->loginCheck();
        if (!$user) {
            $this->application->error($this->lang('lang_servererror'));
        }
        $data = new AtomManager($this->application->data);
        $data->data->begin();
        $formUser = $this->application->getFormObject(new SystemUser(), false);
        if (!$formUser->getName()) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_nameerror'));
        }
        $dbSystemUser = $data->getRecord(new SystemUser($user->getId()));
        if (!$dbSystemUser) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_servererror'));
        }
        $dbSystemUser->setName($formUser->getName());
        $dbSystemUser->setUsercountry($formUser->getUsercountry());
        $dbSystemUser->setUserlang($formUser->getUserlang());
        if (!$data->updateRecord($dbSystemUser)) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_servererror'));
        }
        $data->data->commit();
        $this->application->alterLang($this->item(Lang::$_LANGVALUE, $dbSystemUser->getUserlang()));
        $this->application->message($this->lang('lang_profilesuccess'));
    }
    
    public function passwordSubmit() {
        $user = $this->loginCheck();
        if (!$user) {
            $this->application->error($this->lang('lang_servererror'));
        }
        $data = new AtomManager($this->application->data);
        $data->data->begin();
        $currentPassword = $this->application->getFormParam("currentpassword", PropertyTypes::$_STRING2048);
        $newPassword = $this->application->getFormParam("newpassword", PropertyTypes::$_STRING2048);
        $confirmPassword = $this->application->getFormParam("confirmpassword", PropertyTypes::$_STRING2048);
        if ($newPassword != $confirmPassword) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_passworderror'));
        }
        $dbSystemUser = $data->getRecord(new SystemUser($user->getId()));
        if (!$dbSystemUser) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_servererror'));
        }
        if (crypt($currentPassword, $dbSystemUser->getPassword()) != $dbSystemUser->getPassword()) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_passworderror'));
        }
        $dbSystemUser->setPassword($this->application->blowfishCrypt($newPassword, 10));
        if (!$data->updateRecord($dbSystemUser)) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_servererror'));
        }
        $data->data->commit();
        $this->application->message($this->lang('lang_passwordsuccess'));
    }

    public function emailSubmit() {
        $user = $this->loginCheck();
        if (!$user) {
            $this->application->error($this->lang('lang_servererror'));
        }
        $data = new AtomManager($this->application->data);
        $data->data->begin();
        $newEmail = $this->application->getFormParam("email", PropertyTypes::$_STRING256);
        $dbSystemUser = $data->getRecord(new SystemUser($user->getId()));
        if (!$dbSystemUser) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_servererror'));
        }
        $dbSystemUser->setChangeemailtoken($this->application->createRandomString(1024));
        $dbSystemUser->setNewemail($newEmail);
        if (!$data->updateRecord($dbSystemUser)) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_servererror'));
        }
        $email = new NewEmailEmail($dbSystemUser, $this);
        $email->send();
        $data->data->commit();
        $this->application->message($this->lang('lang_emailchangesent'));
    }
    
    public function emailValidate() {
        $data = new AtomManager($this->application->data);
        $data->data->begin();
        $token = $this->application->getUrlParam("token", PropertyTypes::$_STRING1024);
        $dbSystemUser = new SystemUser();
        $dbSystemUser->setChangeemailtoken($token);
        $dbSystemUser = $data->getRecord($dbSystemUser);
        if (!$dbSystemUser) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_tokenerror'));
        }
        $this->application->setMainTemplate("login", "email", $this->lang('lang_changeemailvalidate'));
        $params = array(
            new SystemUser(),
            new Param("token", $token),
        );
        $this->application->setFormTemplate("newemail", $params, "login", "emailValidateSubmit");
        $data->data->commit();
        $this->application->render();
    }
    
    public function emailValidateSubmit() {
        $data = new AtomManager($this->application->data);
        $data->data->begin();
        $token = $this->application->getFormParam("token", PropertyTypes::$_STRING1024);
        $systemUser = $this->application->getFormObject(new SystemUser(), false);
        if (!$systemUser->getPassword()) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_passworderror'));
        }
        $dbSystemUser = new SystemUser();
        $dbSystemUser->setChangeemailtoken($token);
        $dbSystemUser = $data->getRecord($dbSystemUser);
        if (!$dbSystemUser) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_tokenerror'));
        }
        if (crypt($systemUser->getPassword(), $dbSystemUser->getPassword()) != $dbSystemUser->getPassword()) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_passworderror'));
        }
        $dbSystemUser->setEmail($dbSystemUser->getNewemail());
        $dbSystemUser->setChangeemailtoken(null);
        $dbSystemUser->setNewemail(null);
        if (!$data->updateRecord($dbSystemUser)) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_servererror'));
        }
        $data->data->commit();
        $this->application->message($this->lang('lang_emailchangesuccess'));
    }
    
    

}

?>
