<?php
/**
 * Bicubic PHP Framework
 * Parser Class
 *
 * @author     Juan Rodríguez-Covili <jrodriguez@bicubic.cl>
 * @copyright  2010 Bicubic Technology - http://www.bicubic.cl
 * @license    Bicubic Comercial Licence
 * @license    
 * @framework  2.1
 */
abstract class Parser {

    public $tag;

    public $timeout;

    public $url;

    function __construct($url, $timeout) {
        $this->url = $url;
        $this->timeout = $timeout;
    }

    public abstract function getParsedElement($item);

    public abstract function retrieve($timelimit);

}

class ParseElement {
    
    public $time;

}
?>
