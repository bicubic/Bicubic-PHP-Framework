<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @framework  2.2
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
