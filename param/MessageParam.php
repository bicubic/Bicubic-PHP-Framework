<?php

/**
 * Parámetro de mensages
 *
 * @author     Juan Francisco Rodríguez <jrodriguez@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    Bicubic Comercial Licence
 * @license
 * @framework  2.1
 */
class MessageParam {

    /**
     * un array de mensajes
     * @var array
     */
    public $messages;
    /**
     * un link
     * @var string
     */
    public $link;
    /**
     * El texto del link
     * @var string
     */
    public $linkText;

    /**
     * Constructor
     * @param array $messages el array de mensajes
     * @param string $link el link
     * @param string $linkText  el texto del link
     */
    function __construct($messages, $link = null, $linkText = null) {
        $this->messages = $messages;
        $this->link = $link;
        $this->linkText = $linkText;
    }
}
?>
