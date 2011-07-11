<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * AppData
 * 
 * @author     Claudio Retamal Vega <claudio@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    Bicubic Comercial Licence
 * @license
 * @framework  2.1
 */
class AppData {
    private $data;
    public $error;
    
    function __construct(Data $data) {
        $this->data = $data;
    }
    
    public function getApps(App $app){
        $data = array();
        $data = $this->data->select($app);
        return $data;
    }
    
    public function getApp(App $app){
        $app = $this->data->selectOne($app);
        return $app;
    }
    
    public function insertApp(App $app){
        $this->data->begin();
        if(!$this->data->insert($app)){
            $this->error = $this->data->getError();
            $this->data->rollback();
            return false;
        }
        $this->data->commit();
        return true;
    }
    
    public function updateApp(App $app){
        $this->data->begin();
        if(!$this->data->update($app)){
            $this->error = $this->data->getError();
            $this->data->rollback();
            return false;
        }
        $this->data->commit();
        return true;
    }
    
    public function deleteApp(App $app){
        $this->data->begin();
        if(!$this->data->delete($app)){
            $this->error = $this->data->getError();
            $this->data->rollback();
            return false;
        }
        $this->data->commit();
        return true;
    }
}

?>
