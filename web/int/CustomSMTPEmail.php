<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
class CustomSMTPEmail extends SMTPEmail {

    private $text;
    
    function __construct($to, $from, $fromName, $header, $text) {
        parent::__construct($to, $from, $fromName, $header);
        $this->text = $text;
    }

    public function getBody() {
        $htmltext = str_replace("\n", "<br>", $this->text);
        $htmltext .= "<br><br>";
        return $htmltext;
    }

    public function getPlainBody() {
        return $this->text;
    }

}

?>