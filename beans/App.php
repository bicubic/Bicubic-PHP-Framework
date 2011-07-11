<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * App
 * 
 * @author     Claudio Retamal Vega <claudio@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    Bicubic Comercial Licence
 * @license
 * @framework  2.1
 */
class App implements DataObject{
    
    private $id;
    private $name;
    private $company;
    private $contents;
    private $protocol;
    
    function __construct() {
        $this->contents = array();
    }
    
    public function __getProperties() {
        return array(
            "id",
            "name",
            "company",
            "contents_array_Content",
            "protocol"
        );
    }
    
    function setId($value){
        $this->id = $value;
    }
    function getId(){
        return $this->id;
    }
    function setName($value){
        $this->name = $value;
    }
    function getName(){
        return $this->name;
    }
    function setCompany($value){
        $this->company = $value;
    }
    function getCompany(){
        return $this->company;
    }
    function setContents(array $value){
        $this->contents = $value;
    }
    function getContents(){
        return $this->contents;
    }
    function setProtocol($value){
        $this->protocol = $value;
    }
    function getProtocol(){
        return $this->protocol;
    }
    function addContent(Content $value){
        $this->contents[count($this->contents)] = $value;
    }
    function getContent($index){
        if(array_key_exists($index, $this->contents)){
            return $this->contents[$index];
        }
        return false;
    }
    
}
/**
 * 
 */
class Protocol {
    public static $_APPLE = "apple";
    public static $_STANDALONE = "standalone";
    public static $_GAMESERVER = "gameserver";
    public static $_ANDROID = "android";
}
?>
