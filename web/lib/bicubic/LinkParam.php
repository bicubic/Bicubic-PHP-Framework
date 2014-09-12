<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
class LinkParam  {

    public $app;
    public $nav;
    public $params;
    public $lang;
    public $class;

    function __construct($app, $nav, $params = array(), $lang = null, $class = null) {
        $this->app = $app;
        $this->nav = $nav;
        $this->params = $params;
        $this->lang = $lang;
        $this->class = $class;
    }

}

