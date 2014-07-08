<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
define('SESSION_ORDER', "MaintainerNavigation_order");
define('SESSION_OBJECT', "MaintainerNavigation_object");

class MaintainerNavigation extends Navigation {

    private $object;
    private $title;
    private $singleTitle;
    public  $name;

    function __construct(Application $application, DataObject $object, $name, $title, $singleTitle) {
        parent::__construct($application);
        $this->order = $this->application->getSessionParam(SESSION_ORDER);
        if (!$this->order) {
            $this->order = new OrderParam("id", ObjectOrder::$_DESC);
        }
        $this->object = $this->application->getSessionParam(SESSION_OBJECT);
        if (!$this->object) {
            $this->object = $object;
        }
        $this->title = $title;
        $this->singleTitle = $singleTitle;
        $this->name = $name;
    }

    public function records() {
        $this->application->setMainTemplate("bicubic", "empty", $this->title);
        $this->application->setVariableTemplate("NAVIGATION-CONTENT", $this->objectTable($this->object, $this->order, [new LinkParam($this->application->name, "bicubic-$this->name-edit", [], 'lang_edit'), new LinkParam($this->application->name, "bicubic-$this->name-delete", [], 'lang_delete')], [new LinkParam($this->application->name, "bicubic-$this->name-add", [], 'lang_add')], new LinkParam($this->application->name, "bicubic-$this->name-json"), new LinkParam($this->application->name, "bicubic-$this->name-search"), new LinkParam($this->application->name, "bicubic-$this->name-reorder")));
        $this->application->render();
    }

    public function json() {
        $this->objectTableJson($this->object, [new LinkParam($this->application->name, "bicubic-$this->name-edit", [], 'lang_edit'), new LinkParam($this->application->name, "bicubic-$this->name-delete", [], 'lang_delete')], $this->order);
    }

    public function search() {
        $obj = $this->objectTableFilter($this->object);
        $this->application->setSessionParam(SESSION_OBJECT, $obj);
        $this->application->redirect($this->application->name, "bicubic-$this->name");
    }

    public function reorder() {
        $order = $this->objectTableOrder();
        $this->application->setSessionParam(SESSION_ORDER, $order);
        $this->application->redirect($this->application->name, "bicubic-$this->name");
    }

    public function add() {
        $this->application->setMainTemplate("bicubic", "empty", $this->singleTitle);
        $className = get_class($this->object);
        $this->application->setVariableTemplate("NAVIGATION-CONTENT", $this->objectForm(new $className(), "bicubic-$this->name-addSubmit"));
        $this->application->render();
    }
    
    public function addSubmit() {
        $className = get_class($this->object);
        $object = $this->application->getFormObject(new $className());
        $data = new TransactionManager($this->application->data);
        $data->data->begin();
        if(!$data->insertRecord($object)) {
            $data->data->rollback();
            $this->error('lang_servererror');
        }
        $data->data->commit();
        $this->application->redirect($this->application->name, "bicubic-$this->name");
    }
    
    public function edit() {
        $id = $this->application->getUrlParam("id", PropertyTypes::$_INT);
        $className = get_class($this->object);
        $object = (new $className($id));
        $data = new TransactionManager($this->application->data);
        $object = $data->getRecord($object);
        if(!$object) {
            $this->error("lang_idnotvalid");
        }
        $this->application->setMainTemplate("bicubic", "empty", $this->singleTitle);
        $this->application->setVariableTemplate("NAVIGATION-CONTENT", $this->objectForm($object, "bicubic-$this->name-editSubmit"));
        $this->application->render();
    }
    
    public function editSubmit() {
        $className = get_class($this->object);
        $object = $this->application->getFormObject(new $className());
        $data = new TransactionManager($this->application->data);
        $data->data->begin();
        if(!$data->updateRecord($object)) {
            $data->data->rollback();
            $this->error('lang_servererror');
        }
        $data->data->commit();
        $this->application->redirect($this->application->name, "bicubic-$this->name");
    }
    
    public function delete() {
        $id = $this->application->getUrlParam("id", PropertyTypes::$_INT);
        $className = get_class($this->object);
        $object = (new $className($id));
        $data = new TransactionManager($this->application->data);
        $data->data->begin();
        $object = $data->getRecord($object);
        if(!$object) {
            $data->data->rollback();
            $this->error("lang_idnotvalid");
        }
        if(!$data->deleteRecord($object)) {
            $data->data->rollback();
            $this->error('lang_servererror');
        }
        $data->data->commit();
        $this->application->redirect($this->application->name, "bicubic-$this->name");
    }
}