<?php
/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @framework  2.2
 */
class HelloNavigation extends Navigation{
    
    function __construct(Application $application) {
        parent::__construct($application);
    }
    
    public function helloJson(){
        $this->application->setMainTemplate("hello", "hello", "", "json");
        $this->application->setJsonVariableTemplate("HELLO", $this->application->lang['text_helloworld']);
        $this->application->render();
    }
  
    public function hello(){
        $this->application->setMainTemplate("hello", "hello", $this->application->lang['text_helloworld']);
        $this->application->setHtmlVariableTemplate("HELLO", $this->application->lang['text_helloworld']);
        $this->application->render();
    }
    
    
}

?>
