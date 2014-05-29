<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
class NewEmailEmail {

    private $systemUser;
    private $mandrillEmail;
    private $key;
    private $template;
    private $link;
    private $text;
    
    function __construct(SystemUser $systemUser, Navigation $navigation) {
        $this->mandrillEmail = new MandrillEmail($systemUser->getEmail(), $navigation->config('web_contact_email'), $navigation->config('web_contact_name'), $navigation->lang('lang_emailchangesubject'));
        $this->systemUser = $systemUser;
        $this->key = $navigation->config('mandrill_key');
        $this->template = $navigation->config('mandrill_template');
        $this->link = $navigation->application->getAppUrl("login","emailValidate",array(new Param("token", $systemUser->getChangeemailtoken())));
        $this->text = $navigation->lang('lang_emailchangetext');
    }

    public function send() {
        $vars = array(
            "MESSAGENAME" => $this->systemUser->getName(),
            "MESSAGETEXT" => $this->text,
            "MESSAGELINK" =>  $this->link,
        );
        $this->mandrillEmail->send($this->key, $this->template, $vars);
    }
    

}

?>