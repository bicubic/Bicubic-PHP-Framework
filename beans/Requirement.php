<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Requirement
 * 
 * @author     Claudio Retamal Vega <claudio@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    Bicubic Comercial Licence
 * @license
 * @framework  2.1
 */
class Requirement implements DataObject {
    private $id;
    private $name;
    private $requirementsContent;
    
    function __construct() {
        
    }
    
    public function __getProperties() {
        return array(
            "id",
            "name",
            "requirementsContent"
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
    function setRequirementsContent($value){
        $this->requirementsContent = $value;
    }
    function getRequirementsContent(){
        return $this->requirementsContent;
    }
}

?>
