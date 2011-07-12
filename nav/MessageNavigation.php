<?php
/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @framework  2.2
 */

class MessageNavigation extends Navigation{
    
    /**
     * Constructor
     * @param MainApplication $mainApplication la aplicacion principal
     */
    function __construct(Application $application) {
        parent::__construct($application);
    }

    /**
     * Renderea un mensage guardado en session
     */
    public function message() {
        //Global information
        $this->application->setMainTemplate("message", "message", $this->application->lang['object_message'], "message");
        $messageParam = $this->application->getSessionParam("message");
        if (!isset($messageParam)) {
            $this->application->error($this->application->lang['word_message'] . " " . $this->application->lang['error_notvalid']);
        }
        foreach($messageParam->messages as $text) {
            $this->application->setHTMLArrayTemplate(array('MESSAGE-TEXT' => $text));
            $this->application->parseTemplate('MESSAGES');
        }
        if (isset($messageParam->link) && isset($messageParam->linkText)) {
            $this->application->setHTMLVariableTemplate("MESSAGE-LINK", $messageParam->link);
            $this->application->setHTMLVariableTemplate("MESSAGE-LINK-TEXT", $messageParam->linkText);
        }
        //render
        $this->application->render();
    }

}
?>
