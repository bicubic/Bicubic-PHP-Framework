<?php
/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @framework  2.2
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
