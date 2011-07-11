<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * ContentRequestNavigation
 * 
 * @author     Claudio Retamal Vega <claudio@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    Bicubic Comercial Licence
 * @license
 * @framework  2.1
 */
class ContentRequestNavigation extends Navigation {
   
    function __construct(JsonApplication $application) {
        parent::__construct($application);
    }
    
    public function appleRequest(){
        
    }
    
    public function standAloneRequest(){
        $pos = 0;
        $file = null;
        $valid = false;
        $reqid = array();
        
        $json = $this->application->getJsonParam(true);
        $this->application->setMainTemplate("contentrequest", "answer");
        if($this->validateRequestJson($json)){
            $app = new App();
            $app->setId($json["appId"]);
            foreach ($json["requirements"] as $stdClass){
                $subObject = get_object_vars($stdClass);
                $reqid[$pos] = $subObject["id"];
                $pos++;
            }
            $object = $this->application->data->selectOne($app);
            if(isset($object)){
                if($object->getProtocol() == "standalone"){
                    $contents = $object->getContents();
                    
                    foreach ($contents as $content){
                        if($json["contentId"] == $content->getId()){
                            $requirements = $content->getRequirements();
                            $file = $content->getFile();
                            $pos = 0;
                            $this->application->setJsonVariableTemplate("ERROR-MESSAGE", "");
                            foreach($requirements as $require){
                                if(array_key_exists($pos, $reqid) && $require->getId() == $reqid[$pos]){
                                    $valid = true;
                                } else {
                                    $this->application->setJsonVariableTemplate("ANSWER-STATUS", $this->application->lang['error_statusfailed']);
                                    $this->application->setJsonVariableTemplate("ERROR-MESSAGE", $this->application->lang['error_requirementsnotvalid']);
                                    $valid = false;
                                    $data = null;
                                    break;
                                }
                                $pos++;
                            }
                            break;
                        } else { 
                           $this->application->setJsonVariableTemplate("ANSWER-STATUS", $this->application->lang['error_statusfailed']);
                           $this->application->setJsonVariableTemplate("ERROR-MESSAGE", $this->application->lang['error_invalidcontent']); 
                        }
                    }

                } else {
                    $this->application->setJsonVariableTemplate("ANSWER-STATUS", $this->application->lang['error_statusfailed']);
                    $this->application->setJsonVariableTemplate("ERROR-MESSAGE", $this->application->lang['error_wrongprotocol']);
                }
            } else {
                $this->application->setJsonVariableTemplate("ANSWER-STATUS", $this->application->lang['error_statusfailed']);
                $this->application->setJsonVariableTemplate("ERROR-MESSAGE", $this->application->lang['error_invalidid']);
            }
        } else {
            $this->application->setJsonVariableTemplate("ANSWER-STATUS", $this->application->lang['error_statusfailed']);
            $this->application->setJsonVariableTemplate("ERROR-MESSAGE", $this->application->lang['error_jsonnotvalid']);
        }
        if($valid){
            $reader = fopen($file, "r");
            $databinary = fread($reader, filesize($file));
            fclose($reader);
            $data = base64_encode($databinary);
            $this->application->setJsonVariableTemplate("ANSWER-STATUS", $this->application->lang['text_statusok']);
            $this->application->setJsonVariableTemplate("CONTENT-FILE", $data);
        }
        $this->application->render();
        
    }
    
    private function validateRequestJson($json){
        
        $json["appId"] = $this->application->filter($json['appId'], "int");
        if($json["appId"] == null){
            $this->application->setJsonVariableTemplate("ANSWER-STATUS", $this->application->lang['error_statusfailed']);
            $this->application->setJsonVariableTemplate("ERROR-MESSAGE", $this->application->lang['error_invalidid']);
            return false;
        }
        $json["contentId"] = $this->application->filter($json["contentId"], "int");
        if($json["contentId"] == null){
            $this->application->setJsonVariableTemplate("ANSWER-STATUS", $this->application->lang['error_statusfailed']);
            $this->application->setJsonVariableTemplate("ERROR-MESSAGE", $this->application->lang['error_invalidid']);
            return false;
        }
        foreach ($json["requirements"] as $stdClass){
            $subObject = get_object_vars($stdClass);
            $subObject["id"] = $this->application->filter($subObject["id"], "int");
            if($subObject["id"] == null){
               $this->application->setJsonVariableTemplate("ANSWER-STATUS", $this->application->lang['error_statusfailed']);
               $this->application->setJsonVariableTemplate("ERROR-MESSAGE", $this->application->lang['error_invalidid']);
               return false;
            }
        }
        if (count($json) > 3){
            $this->application->setJsonVariableTemplate("ANSWER-STATUS", $this->application->lang['error_statusfailed']);
            $this->application->setJsonVariableTemplate("ERROR-MESSAGE", $this->application->lang['error_jsonnotvalid']);
            return false;
        }
        return true;
    }
    
    public function pruebaselect(){
        $appData = new AppData($this->application->data);
        $app = new App();
        $apps = $appData->getApps($app);
        var_dump($apps);
        
    }
    
}

?>
