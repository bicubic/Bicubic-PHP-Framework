<?php

/**
 * Bicubic PHP Framework
 * XMLParser Class
 *
 * @author     Juan Rodríguez-Covili <jrodriguez@bicubic.cl>
 * @copyright  2010 Bicubic Technology - http://www.bicubic.cl
 * @license    Bicubic Comercial Licence
 * @license    
 * @framework  2.1
 */
abstract class XMLParser extends Parser {

    function __construct($url, $tag, $timeout) {
        parent::__construct($url, $timeout);
        $this->tag = $tag;
    }

    public function retrieve($timelimit) {
        $content = array();
        $doc = new DOMDocument();
        $context = stream_context_create(array('http' => array('timeout' => $this->timeout)));
        $xml = file_get_contents($this->url, 0, $context);
        $doc->loadXML($xml);
        $items = $doc->getElementsByTagName($this->tag);
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
