<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * ContentApp
 * 
 * @author     Claudio Retamal Vega <claudio@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    Bicubic Comercial Licence
 * @license
 * @framework  2.1
 */
class ContentData {
    private $data;
    public $error;
    
    function __construct(Data $data) {
        $this->data = $data;
    }
    
    public function getContents(Content $content){
        $data = array();
        $data = $this->data->select($content);
        return $data;
    }
    
    public function getContent(Content $content){
        $content = $this->data->selectOne($content);
        return $content;
    }
    
    public function insertContent(Content $content){
        $this->data->begin();
        if(!$this->data->insert($content)){
            $this->error = $this->data->getError();
            $this->data->rollback();
            return false;
        }
        $this->data->commit();
        return true;
    }
    
    public function updateContent(Content $content){
        $this->data->begin();
        if(!$this->data->update($content)){
            $this->error = $this->data->getError();
            $this->data->rollback();
            return false;
        }
        $this->data->commit();
        return true;
    }
    
    public function deleteContent(Content $content){
        $this->data->begin();
        if(!$this->data->delete($content)){
            $this->error = $this->data->getError();
            $this->data->rollback();
            return false;
        }
        $this->data->commit();
        return true;
    }
}

?>
