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

    function __construct($id = null, $email = null, $password = null) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
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


}

?>