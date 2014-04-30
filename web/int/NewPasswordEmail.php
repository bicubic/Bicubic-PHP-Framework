<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
class NewPasswordEmail {

    private $systemUser;
    private $mandrillEmail;
    private $key;
    private $template;
    private $text;
    
    function __construct(SystemUser $systemUser, $newpassword, Navigation $navigation) {
        $this->mandrillEmail = new MandrillEmail($systemUser->getEmail(), $navigation->config('web_contact_email'), $navigation->config('web_contact_name'), $navigation->lang('lang_emailnewpasswordsubject'));
        $this->systemUser = $systemUser;
        $this->key = $navigation->config('mandrill_key');
        $this->template = $navigation->config('mandrill_template');
        $this->text = sprintf($navigation->lang('lang_emailnewpasswordtext'), $newpassword);
    }

    public function send() {
        $vars = array(
            "MESSAGENAME" => $this->systemUser->getName(),
            "MESSAGETEXT" => $this->text,
            "MESSAGELINK" => "",
        );
        $this->mandrillEmail->send($this->key, $this->template, $vars);
    }
    

}

?>