<?php

/*
 * Copyright (C)  Juan Francisco Rodríguez
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
require_once("lib/google/recaptchalib.php");

class AccountNavigation extends Navigation {

    protected function checkSignedInUser($callBackApp, $callBackNav) {
	$user = $this->loginCheck();
	if ($user) {
	    $this->application->redirect($callBackApp, $callBackNav);
	}
    }

    protected function makeSignUpForm() {
	$result = $this->application->setCustomTemplate("login", "signup");
	$this->application->setVariableCustomTemplate($result, "SIGNUP-ID", "signup");
	$this->application->setVariableCustomTemplate($result, "SIGNUP-ACTION", $this->application->getAppUrl($this->application->name, "signUpSubmit"));
	$this->application->setHTMLVariableCustomTemplate($result, "SIGNUP-NAME-SYSTEMUSER-NAME", "SystemUser_name");
	$this->application->setHTMLVariableCustomTemplate($result, "SIGNUP-NAME-SYSTEMUSER-EMAIL", "SystemUser_email");
	$this->application->setHTMLVariableCustomTemplate($result, "SIGNUP-NAME-SYSTEMUSER-PASSWORD", "SystemUser_password");
	$this->application->setHTMLVariableCustomTemplate($result, "SIGNUP-NAME-CONFIRMPASSWORD", "confirmpassword");
	$this->application->setHTMLVariableCustomTemplate($result, "SIGNUP-NAME-SYSTEMUSER-USERCOUNTRY", "SystemUser_usercountry");
	$this->application->setHTMLVariableCustomTemplate($result, "SIGNUP-NAME-SYSTEMUSER-USERLANG", "SystemUser_userlang");
	$this->application->setVariableCustomTemplate($result, "HTML-RECAPTCHA", recaptcha_get_html($this->config('recaptcha_publickey')), null, $this->config('sslavailable'));
	$countries = $this->sortByLang($this->item(Country::$_ENUM, $this->config('lang')));
	foreach ($countries as $key => $value) {
	    $this->application->setHTMLArrayCustomTemplate($result, [
		"USERCOUNTRY-NAME" => $value,
		"USERCOUNTRY-VALUE" => $key,
	    ]);
	    $this->application->parseCustomTemplate($result, "USERCOUNTRIES");
	}
	uasort(Lang::$_ENUM, array("Navigation", "compareLangStrings"));
	foreach (Lang::$_ENUM as $key => $value) {
	    $this->application->setHTMLArrayCustomTemplate($result, [
		"USERLANG-NAME" => $value,
		"USERLANG-VALUE" => $key,
	    ]);
	    $this->application->parseCustomTemplate($result, "USERLANGS");
	}
	return $this->application->renderCustomTemplate($result);
    }

    public function signUpSubmit($callBackApp, $callBackNav) {
	$data = new AtomManager($this->application->data);
	$data->data->begin();
	$systemUser = $this->application->getFormObject(new SystemUser());
	$confirmPassword = $this->application->getFormParam("confirmpassword", PropertyTypes::$_STRING2048);
	$resp = recaptcha_check_answer($this->config('recaptcha_privatekey'), $_SERVER["REMOTE_ADDR"], $this->application->getFormParam("recaptcha_challenge_field", PropertyTypes::$_STRING), $this->application->getFormParam("recaptcha_response_field", PropertyTypes::$_STRING));
	if (!$resp->is_valid) {
	    $data->data->rollback();
	    $this->application->error($this->lang('lang_recaptchaerror'));
	}
	if (!$systemUser->getName()) {
	    $data->data->rollback();
	    $this->application->error($this->lang('lang_nameerror'));
	}
	if (!$systemUser->getUsercountry()) {
	    $data->data->rollback();
	    $this->application->error($this->lang('lang_usercountryerror'));
	}
	if (!$systemUser->getUserlang()) {
	    $data->data->rollback();
	    $this->application->error($this->lang('lang_userlangerror'));
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
	$newSystemUser->setUsercountry($systemUser->getUsercountry());
	$newSystemUser->setUserlang($systemUser->getUserlang());
	$newSystemUser->setEmail($systemUser->getEmail());
	$newSystemUser->setPassword($this->application->blowfishCrypt($systemUser->getPassword(), 10));
	$newSystemUser->setSessiontoken($this->application->createRandomString(1024));
	$newSystemUser->setConfirmemailtoken($this->application->createRandomString(1024));
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
	//email
	$result = $this->application->setCustomTemplate("email", "email");
	$this->application->setHTMLVariableCustomTemplate($result, 'EMAIL-NAME', $dbSystemUser->getName());
	$this->application->setHTMLVariableCustomTemplate($result, 'EMAIL-TEXT', $this->lang('lang_emailconfirmationtext'));
	$this->application->setHTMLVariableCustomTemplate($result, 'EMAIL-LINK', $this->application->getAppUrl("home", "validate", array(new Param("token", $dbSystemUser->getConfirmemailtoken()))));
	$html = $this->application->renderCustomTemplate($result);
	$this->application->sendEmail($dbSystemUser->getEmail(), $this->lang('lang_emailconfirmationsubject'), $html);
	//finito
	$this->loginSet($dbSystemUser);
	$data->data->commit();
	$this->application->redirect($callBackApp, $callBackNav);
    }

    protected function makeLoginForm() {
	$result = $this->application->setCustomTemplate("login", "login");
	$this->application->setVariableCustomTemplate($result, "LOGIN-ID", "login");
	$this->application->setVariableCustomTemplate($result, "LOGIN-ACTION", $this->application->getAppUrl($this->application->name, "loginSubmit"));
	$this->application->setHTMLVariableCustomTemplate($result, 'LOGIN-NAME-SYSTEMUSER-EMAIL', 'SystemUser_email');
	$this->application->setHTMLVariableCustomTemplate($result, 'LOGIN-NAME-SYSTEMUSER-PASSWORD', 'SystemUser_password');
	$this->application->setHTMLVariableCustomTemplate($result, 'LINK-FORGOT', $this->application->getAppUrl($this->application->name, "forgot"));
	return $this->application->renderCustomTemplate($result);
    }

    public function loginSubmit($callBackApp, $callBackNav) {
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
	$this->application->redirect($callBackApp, $callBackNav);
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
	    $data->data->commit();
	    $this->application->alterLang($dbUser->getUserlang());
	    return $dbUser;
	}
	$data->data->rollback();
	return false;
    }

}
