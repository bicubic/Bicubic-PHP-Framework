<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * HistorialData
 * 
 * @author     Claudio Retamal Vega <claudio@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    Bicubic Comercial Licence
 * @license
 * @framework  2.1
 */
class HistorialData {
    private $data;
    public $error;
    
    function __construct(Data $data) {
        $this->data = $data;
    }
    
    public function getHistorials(Historial $historial){
        $data = array();
        $data = $this->data->select($historial);
        return $data;
    }
    
    public function getHistorial(Historial $historial){
        $historial = $this->data->selectOne($historial);
        return $historial;
    }
    
    public function insertHistorial(Historial $historial){
        $this->data->begin();
        if(!$this->data->insert($historial)){
            $this->error = $this->data->getError();
            $this->data->rollback();
            return false;
        }
        $this->data->commit();
        return true;
    }
    
    public function updateHistorial(Historial $historial){
        $this->data->begin();
        if(!$this->data->update($historial)){
            $this->error = $this->data->getError();
            $this->data->rollback();
            return false;
        }
        $this->data->commit();
        return true;
    }
    
    public function deleteHistorial(Historial $historial){
        $this->data->begin();
        if(!$this->data->delete($historial)){
            $this->error = $this->data->getError();
            $this->data->rollback();
            return false;
        }
        $this->data->commit();
        return true;
    }
}

?>
