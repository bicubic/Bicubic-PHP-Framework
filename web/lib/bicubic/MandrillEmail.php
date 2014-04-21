<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
class MandrillEmail {

    public $to;
    public $from;
    public $fromName;
    public $subject;
    public $error;

    function __construct($to, $from, $fromName, $subject) {
        $this->to = $to;
        $this->from = $from;
        $this->fromName = $fromName;
        $this->subject = $subject;
    }

    function send($key, $template, $vars = array()) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://mandrillapp.com/api/1.0/messages/send-template.json");
        curl_setopt($ch, CURLOPT_POST, 1);
        $jsonObject = new ObjectJson();
        $jsonObject->key = $key;
        $jsonObject->template_name = $template;
        $jsonObject->template_content = array();
        $jsonObject->message = new ObjectJson();
        $jsonObject->message->subject = $this->subject;
        $jsonObject->message->from_email = $this->from;
        $jsonObject->message->from_name = $this->fromName;
        $jsonObject->message->to = array();
        $toObject = new ObjectJson();
        $toObject->email = $this->to;
        $jsonObject->message->to [] = $toObject;
        $jsonObject->async = true;
        $jsonObject->message->global_merge_vars = array();
        foreach ($vars as $key => $value) {
            $var_content = new ObjectJson();
            $var_content->name = $key;
            $var_content->content = $value;
            $jsonObject->message->global_merge_vars [] = $var_content;
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($jsonObject));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
        return true;
    }
}
?>