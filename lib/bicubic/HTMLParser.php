<?php

/**
 * Bicubic PHP Framework
 * HTMLParser Class
 *
 * @author     Juan Rodríguez-Covili <jrodriguez@bicubic.cl>
 * @copyright  2010 Bicubic Technology - http://www.bicubic.cl
 * @license    Bicubic Comercial Licence
 * @license    
 * @framework  2.1
 */
abstract class HTMLParser extends Parser {

    function __construct($url, $tag, $timeout) {
        parent::__construct($url, $timeout);
        $this->tag = $tag;
    }

    public function retrieve($timelimit) {
        $content = array();
        $html = file_get_html($this->url, $this->timeout);
        $items = $html->find($this->tag);
        $now = time();
        foreach ($items as $item) {
            $parseElement = $this->getParsedElement($item);
            if (isset($parseElement)) {
                if ($now - $parseElement->time > $timelimit) {
                    break;
                }
                array_push($content, $parseElement);
            }
        }
        return $content;
    }

}
?>
