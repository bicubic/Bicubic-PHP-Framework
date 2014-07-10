<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */

require_once("nav/AccountNavigation.php");

class HelloNavigation extends AccountNavigation {

    function __construct(Application $application) {
        parent::__construct($application);
    }

    public function hello() {
        $this->checkSignedInUser("private", "home");
        $this->application->setMainTemplate("hello", "hello");
        $this->application->setVariableTemplate("SIGNUP-FORM", $this->makeSignUpForm());
        $this->application->render();
    }

    public function helloJson() {
        $json = new ObjectJson();
        $json->myhello = $this->lang('text_helloworld');
        $this->application->renderToJson($json);
    }

    public function helloPrivate() {
        $this->application->setMainTemplate("hello", "helloprivate");
        $this->application->setHtmlVariableTemplate("USER", $this->application->user->getName());
        $this->application->render();
    }

}

