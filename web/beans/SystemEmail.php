<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
class SystemEmail extends DataObject {

    private $id;
    private $broadcast;
    private $broadcastcountry;
    private $broadcastlang;
    private $to;
    private $from;
    private $subject;
    private $body;
    private $sent;
    
    

    function __construct($id = null) {
        $this->id = $id;
    }

    public function __getProperties() {
        return array(
            "id" => ["name" => "id", "type" => PropertyTypes::$_LONG, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => true, "private" => false],
            "broadcast" => ["name" => "broadcast", "type" => PropertyTypes::$_SHORTLIST, "required" => true, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "broadcastcountry" => ["name" => "broadcastcountry", "type" => PropertyTypes::$_STRING2, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "broadcastlang" => ["name" => "broadcastlang", "type" => PropertyTypes::$_STRING2, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "to" => ["name" => "to", "type" => PropertyTypes::$_EMAIL, "required" => false, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "from" => ["name" => "from", "type" => PropertyTypes::$_EMAIL, "required" => true, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "subject" => ["name" => "subject", "type" => PropertyTypes::$_STRING1024, "required" => true, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "body" => ["name" => "body", "type" => PropertyTypes::$_STRING, "required" => true, "serializable" => true, "updatenull" => true, "hidden" => false, "private" => false],
            "sent" => ["name" => "sent", "type" => PropertyTypes::$_INT, "required" => true, "serializable" => true, "updatenull" => true, "hidden" => true, "private" => true],
        );
    }

    public function __isComplete() {
        if (!$this->broadcast) {
            $this->broadcast = 0;
        }
        if (!$this->sent) {
            $this->sent = 0;
        }
        if ($this->broadcast) {
            if ($this->to) {
                return false;
            }
        }
        if (!$this->broadcast) {
            if (!$this->to) {
                return false;
            }
        }
        return parent::__isComplete();
    }

    public function __getList(TransactionManager $data, $paramname) {
        switch ($paramname) {
            case "broadcast" : {
                    return ObjectBoolean::$_ENUM;
                }
        }
        return [];
    }

    public function getId() {
        return $this->id;
    }

    public function getBroadcast() {
        return $this->broadcast;
    }

    public function getBroadcastcountry() {
        return $this->broadcastcountry;
    }

    public function getBroadcastlang() {
        return $this->broadcastlang;
    }

    public function getTo() {
        return $this->to;
    }

    public function getFrom() {
        return $this->from;
    }

    public function getSubject() {
        return $this->subject;
    }

    public function getBody() {
        return $this->body;
    }

    public function getSent() {
        return $this->sent;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setBroadcast($broadcast) {
        $this->broadcast = $broadcast;
    }

    public function setBroadcastcountry($broadcastcountry) {
        $this->broadcastcountry = $broadcastcountry;
    }

    public function setBroadcastlang($broadcastlang) {
        $this->broadcastlang = $broadcastlang;
    }

    public function setTo($to) {
        $this->to = $to;
    }

    public function setFrom($from) {
        $this->from = $from;
    }

    public function setSubject($subject) {
        $this->subject = $subject;
    }

    public function setBody($body) {
        $this->body = $body;
    }

    public function setSent($sent) {
        $this->sent = $sent;
    }



}

?>