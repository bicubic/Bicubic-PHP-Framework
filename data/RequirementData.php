<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * RequirementData
 * 
 * @author     Claudio Retamal Vega <claudio@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    Bicubic Comercial Licence
 * @license
 * @framework  2.1
 */
class RequirementData {
    private $data;
    public $error;
    
    function __construct(Data $data) {
        $this->data = $data;
    }
    
    public function getRequirements(Requirement $requirement){
        $data = array();
        $data = $this->data->select($requirement);
        return $data;
    }
    
    public function getRequirement(Requirement $requirement){
        $requirement = $this->data->selectOne($requirement);
        return $requirement;
    }
    
    public function insertRequirement(Requirement $requirement){
        $this->data->begin();
        if(!$this->data->insert($requirement)){
            $this->error = $this->data->getError();
            $this->data->rollback();
            return false;
        }
        $this->data->commit();
        return true;
    }
    
    public function updateRequirement(Requirement $requirement){
        $this->data->begin();
        if(!$this->data->update($requirement)){
            $this->error = $this->data->getError();
            $this->data->rollback();
            return false;
        }
        $this->data->commit();
        return true;
    }
    
    public function deleteRequirement(Requirement $requirement){
        $this->data->begin();
        if(!$this->data->delete($requirement)){
            $this->error = $this->data->getError();
            $this->data->rollback();
            return false;
        }
        $this->data->commit();
        return true;
    }
}

?>
