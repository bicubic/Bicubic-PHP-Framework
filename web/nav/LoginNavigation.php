<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
require_once("int/ConfirmationEmail.php");
require_once("int/ForgotEmail.php");
require_once("int/NewPasswordEmail.php");
require_once("lib/google/recaptchalib.php");

class LoginNavigation extends Navigation {

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
        $formToken = $this->application->getFormParam("loginToken", PropertyTypes::$_STRING64);
        $loginToken = $this->application->getSessionParam("LoginNavigation_logintoken");
        if ($formToken != $loginToken) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_tokenerror'));
        }
        $systemUser = $this->application->getFormObject(new SystemUser());
        if (!$systemUser->getEmail()) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_emailerror'));
        }
        if (!$systemUser->getPassword()) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_passworderror'));
        }
        $dbSystemUser = $data->getRecord(new SystemUser(null, $systemUser->getEmail()));
        if (!$dbSystemUser) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_loginerror'));
        }
        if (crypt($systemUser->getPassword(), $dbSystemUser->getPassword()) != $dbSystemUser->getPassword()) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_loginerror'));
        }
        $dbSystemUser->setSessiontoken($this->application->createRandomString(64));
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

    public function signUpSubmit() {
        $data = new AtomManager($this->application->data);
        $data->data->begin();
        $systemUser = $this->application->getFormObject(new SystemUser());
        $confirmPassword = $this->application->getFormParam("confirmpassword", PropertyTypes::$_STRING32);
        $resp = recaptcha_check_answer($this->config('recaptcha_privatekey'), $_SERVER["REMOTE_ADDR"], $this->application->getFormParam("recaptcha_challenge_field", PropertyTypes::$_STRING), $this->application->getFormParam("recaptcha_response_field", PropertyTypes::$_STRING));
        if (!$resp->is_valid) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_recaptchaerror'));
        }
        if (!$systemUser->getName()) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_nameerror'));
        }
        if (!$systemUser->getEmail()) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_emailerror'));
        }
        if (!$systemUser->getPassword()) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_passworderror'));
        }
        if ($systemUser->getPassword() !== $confirmPassword) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_passworderror'));
        }
        $dbSystemUser = $data->getRecord(new SystemUser(null, $systemUser->getEmail()));
        if ($dbSystemUser) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_emailalreadyexist'));
        }
        $newSystemUser = new SystemUser();
        $newSystemUser->setName($systemUser->getName());
        $newSystemUser->setEmail($systemUser->getEmail());
        $newSystemUser->setPassword($this->application->blowfishCrypt($systemUser->getPassword(), 10));
        $newSystemUser->setSessiontoken($this->application->createRandomString(64));
        $newSystemUser->setConfirmemailtoken($this->application->createRandomString(64));
        $newSystemUser->setId($data->insertRecord($newSystemUser));
        if (!$newSystemUser->getId()) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_servererror'));
        }
        $dbSystemUser = $data->getRecord(new SystemUser($newSystemUser->getId()));
        if (!$dbSystemUser) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_servererror'));
        }
        $email = new ConfirmationEmail($dbSystemUser, $this);
        $email->send();
        $this->loginSet($dbSystemUser);
        $data->data->commit();
        $this->application->secureRedirect("private", "hello");
    }

    public function validate() {
        $data = new AtomManager($this->application->data);
        $data->data->begin();
        $token = $this->application->getUrlParam("token", PropertyTypes::$_STRING64);
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
        $token = $this->application->getUrlParam("token", PropertyTypes::$_STRING64);
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
        $systemUser = $this->application->getFormObject(new SystemUser());
        if (!$systemUser->getEmail()) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_emailerror'));
        }
        $dbSystemUser = $data->getRecord(new SystemUser(null, $systemUser->getEmail()));
        if (!$dbSystemUser) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_emailerror'));
        }
        $dbSystemUser->setForgottoken($this->application->createRandomString(64));
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
        $token = $this->application->getUrlParam("token", PropertyTypes::$_STRING64);
        $dbSystemUser = new SystemUser();
        $dbSystemUser->setForgottoken($token);
        $dbSystemUser = $data->getRecord($dbSystemUser);
        if (!$dbSystemUser) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_tokenerror'));
        }
        $newPassword = $this->application->createRandomString(8);
        $dbSystemUser->setForgottoken(null);
        $dbSystemUser->setPassword($this->application->blowfishCrypt($newPassword, 10));
        if (!$data->updateRecord($dbSystemUser)) {
            $data->data->rollback();
            $this->application->error($this->lang('lang_servererror'));
        }
        $email = new NewPasswordEmail($dbSystemUser, $newPassword, $this);
        $email->send();
        $data->data->commit();
        $this->application->message($this->lang('lang_emailforgotsuccess'));
    }

    public function loginSet(SystemUser $user) {
        $this->application->setSessionParam("LoginApplication_login", true);
        $this->application->setSessionParam("LoginApplication_user", $user);
        $this->application->setSessionParam("LoginApplication_time", time());
    }

    public function loginUnset() {
        $this->application->killSessionParam("LoginApplication_login");
        $this->application->killSessionParam("LoginApplication_user");
        $this->application->killSessionParam("LoginApplication_time");
        session_destroy();
    }

    public function loginCheck() {
        $data = new AtomManager($this->application->data);
        $data->data->begin();
        $login = $this->application->getSessionParam("LoginApplication_login");
        $user = $this->application->getSessionParam("LoginApplication_user");
        $time = $this->application->getSessionParam("LoginApplication_time");
        $currentTime = time();
        if (!$login || !$user || !$time || ($time + $this->config('web_time_out') < $currentTime)) {
            $data->data->rollback();
            return false;
        }
        $dbUser = $data->getRecord(new SystemUser($user->getId()));
        if ($dbUser && $dbUser->getSessiontoken() === $user->getSessiontoken()) {
            $this->application->setSessionParam("LoginApplication_time", $currentTime);
            $this->application->setSessionParam("LoginApplication_user", $dbUser);
            $systemUserLog = new SystemUserLog();
            $systemUserLog->setHttpcharset(array_key_exists('HTTP_ACCEPT_CHARSET', $_SERVER) ? $_SERVER['HTTP_ACCEPT_CHARSET'] : NULL);
            $systemUserLog->setHttphost(array_key_exists('HTTP_HOST', $_SERVER) ? $_SERVER['HTTP_HOST'] : NULL);
            $systemUserLog->setHttplang(array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : NULL);
            $systemUserLog->setHttpreferer(array_key_exists('HTTP_REFERER', $_SERVER) ? $_SERVER['HTTP_REFERER'] : NULL);
            $systemUserLog->setHttps(array_key_exists('HTTPS', $_SERVER) ? $_SERVER['HTTPS'] : NULL);
            $systemUserLog->setHttpuseragent(array_key_exists('HTTP_USER_AGENT', $_SERVER) ? $_SERVER['HTTP_USER_AGENT'] : NULL);
            $systemUserLog->setQuerystring((array_key_exists('PHP_SELF', $_SERVER) ? $_SERVER['PHP_SELF'] : NULL) . ("?") . (array_key_exists('QUERY_STRING', $_SERVER) ? $_SERVER['QUERY_STRING'] : NULL));
            $systemUserLog->setRemoteaddress(array_key_exists('REMOTE_ADDR', $_SERVER) ? $_SERVER['REMOTE_ADDR'] : NULL);
            $systemUserLog->setRemotehost(array_key_exists('REMOTE_HOST', $_SERVER) ? $_SERVER['REMOTE_HOST'] : NULL);
            $systemUserLog->setRemoteport(array_key_exists('REMOTE_PORT', $_SERVER) ? $_SERVER['REMOTE_PORT'] : NULL);
            $systemUserLog->setServertime(time());
            $systemUserLog->setSystemuser($dbUser->getId());
            $systemUserLog->setUsebatterylevel($this->application->getFormParam("devicebattery", PropertyTypes::$_STRING, false));
            $systemUserLog->setUsecountry($this->application->getFormParam("usercountry", PropertyTypes::$_STRING, false));
            $systemUserLog->setUsedevicemodel($this->application->getFormParam("devicemodel", PropertyTypes::$_STRING, false));
            $systemUserLog->setUsedeviceos($this->application->getFormParam("deviceos", PropertyTypes::$_STRING, false));
            $systemUserLog->setUsedeviceversion($this->application->getFormParam("deviceversion", PropertyTypes::$_STRING, false));
            $systemUserLog->setUselongitude($this->application->getFormParam("longitude", PropertyTypes::$_STRING, false));
            $systemUserLog->setUselatitude($this->application->getFormParam("latitude", PropertyTypes::$_STRING, false));
            $systemUserLog->setUselanguage($this->config("lang"));
            $systemUserLog->setPayload("loginCheck");
            if (!$data->insertRecord($systemUserLog)) {
                $data->data->rollback();
                $this->application->error($this->lang('lang_servererror'));
            }
            if($systemUserLog->getUsecountry()) {
                $dbUser->setUsercountry($systemUserLog->getUsecountry());
            }
            if($systemUserLog->getUselanguage()) {
                $dbUser->setUserlang($systemUserLog->getUselanguage());
            }
            if (!$data->updateRecord($dbUser)) {
                $data->data->rollback();
                $this->application->error($this->lang('lang_servererror'));
            }
            $data->data->commit();
            return $dbUser;
        }
        $data->data->rollback();
        return false;
    }

}

?>
