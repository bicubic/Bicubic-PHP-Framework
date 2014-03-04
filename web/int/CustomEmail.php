<?php

/**
 * Bicubic PHP Framework
 * WineClrEmail Class
 *
 * @author     Juan Rodríguez-Covili <jrodriguez@bicubic.cl>
 * @copyright  2010 Bicubic Technology - http://www.bicubic.cl
 * @license    Bicubic Comercial Licence
 * @license
 * @framework    1.2
 */
class CustomEmail extends BicubicEmail {


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
