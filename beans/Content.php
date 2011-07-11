<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Content
 * 
 * @author     Claudio Retamal Vega <claudio@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    Bicubic Comercial Licence
 * @license
 * @framework  2.1
 */
class Content implements DataObject {
    private $id;
    private $name;
    private $file;
    private $requirements;
    private $contentsApp;
    
    function __construct() {
        $this->requirements = array();
    }
    
    public function __getProperties() {
        return array(
            "id",
            "name",
            "file",
            "contentsApp",
            "requirements_array_Requirement"
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
    function setFile($value){
        $this->file = $value;
    }
    function getFile(){
        return $this->file;
    }
    function setContentsApp($value){
        $this->contentsApp = $value;
    }
    function getContentsApp(){
        return $this->contentsApp;
    }
    function setRequirements(array $value){
        $this->requirements = $value;
    }
    function getRequirements(){
        return $this->requirements;
    }
    function addRequirement(Requirement $value){
        $this->requirements[count($this->requirements)] = $value;
    }
    function getRequirement($index){
        if(array_key_exists($this->requirements, $index)){
            return $this->requirements[$index];
        }
        return false;
    }
}

?>
