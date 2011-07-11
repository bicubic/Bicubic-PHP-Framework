<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * AppNavigation
 * 
 * @author     Claudio Retamal Vega <claudio@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    Bicubic Comercial Licence
 * @license
 * @framework  2.1
 */
class AppNavigation extends Navigation{
    
    function __construct(PanelApplication $application) {
        parent::__construct($application);
    }
  
    public function insert(){
        $this->application->setMainTemplate("app", "form", $this->application->lang['title_app']);
        $params = array(
            new App()
        );
        $this->application->setFormTemplate("appform", $params, "panel", "insertApp");
        $appData = new AppData($this->application->data);
        $app = new App();
        $apps = $appData->getApps($app);
        foreach ($apps as $app) {
            $this->application->setHTMLArrayTemplate(array(
                "APP-ID" => $app->getId(),
                "APP-NAME" => $app->getName(),
                "APP-COMPANY" => $app->getCompany(),
                "APP-PROTOCOL" => $app->getProtocol(),
                "LINK-EDIT" => $this->application->getSecureAppUrl($this->application->name, "editApp", array(new Param($this->application->config['param_id'], $app->getId()))),
                "LINK-DELETE" => $this->application->getSecureAppUrl($this->application->name, "deleteApp", array(new Param($this->application->config['param_id'], $app->getId()))),
            ));
            $this->application->parseTemplate("APPS");
        }
        $this->application->render();
    }
    
    public function edit(){
        $this->application->setMainTemplate("app", "edit", $this->application->lang['title_editapp']);
        $app = new App();
        $app->setId($this->application->getUrlParam($this->application->config['param_id'], "int", true));
        $appData = new AppData($this->application->data);
        $app = $appData->getApp($app);
         if ($app->getId() == null) {
            $this->application->error($this->application->lang['error_notvalid']);
        }
        $params = array(
            new App()
        );
        $this->application->setFormTemplate("form", $params, "panel", "editApp");
        $this->application->setHTMLVariableTemplate("App_id", $app->getName());
        $this->application->setHTMLVariableTemplate("App_name", $app->getName());
        $this->application->setHTMLVariableTemplate("App_company", $app->getCompany());
        $this->application->setHTMLVariableTemplate("App_protocol", $app->getProtocol());
        $this->application->render();
        //$this->application->setFormTemplate("form", $params, "panel", "editApp");
    }
    
    public function insertSubmit(){
        $app = new App();
        $app->setName($this->application->getFormParam("App_name", "string64", true));
        $app->setCompany($this->application->getFormParam("App_company","string64", true));
        $app->setProtocol($this->application->getFormParam("App_protocol","string64", true));
        $appData = new AppData($this->application->data);
        if(!$appData->insertApp($app)){
            $this->application->error($this->application->lang['error_database'] . " " . $appData->error);
        }
        $this->application->secureRedirect($this->application->name, "application");
        
    }
    
    
    
    public function editSubmit(){
        $app = new App();
        $app->setName($this->application->getFormParam("App_name", "string64", true));
        $app->setCompany($this->application->getFormParam("App_company","string64", true));
        $app->setProtocol($this->application->getFormParam("App_protocol","string64", true));
        $appData = new AppData($this->application->data);
        if(!$appData->updateApp($app)){
            $this->application->error($this->application->lang['error_database'] . " " . $appData->error);
        }
        $this->application->secureRedirect($this->application->name, "editAppSubmit");
        
    }
}

?>
