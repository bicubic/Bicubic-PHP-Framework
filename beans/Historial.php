<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Historial
 * 
 * @author     Claudio Retamal Vega <claudio@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    Bicubic Comercial Licence
 * @license
 * @framework  2.1
 */
class Historial implements DataObject {
    private $id;
    private $date;
    private $user;
    private $content;
    
    function __construct() {
        $this->content = new Content();
    }
    
    public function __getProperties() {
        return array(
            "id",
            "date",
            "user",
            "content_object"
        );
    }
    
    function setId($value){
        $this->id = $value;
    }
    function getId(){
        return $this->id;
    }
    function setDate($value){
        $this->date = $value;
    }
    function getDate(){
        return $this->date;
    }
    function setUser($value){
        $this->user = $value;
    }
    function getUser(){
        return $this->user;
    }
    function setContent(Content $value){
        $this->content = $value;
    }
    function getContent(){
        return $this->content;
    }
    
}

?>
