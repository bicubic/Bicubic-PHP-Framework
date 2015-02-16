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
    private $originalObject;
    private $title;
    private $singleTitle;
    public $name;

    function __construct(Application $application, DataObject $object, $name, $title, $singleTitle) {
        parent::__construct($application);
	$this->originalObject = $object;
        $this->object = $this->application->getSessionParam(SESSION_OBJECT);
        if (!$this->object || get_class($this->object) != get_class($object)) {
            $this->object = $object;
            $this->order = $this->application->getSessionParam(SESSION_ORDER);
        }
        if (!$this->order) {
            $this->order = new OrderParam("id", ObjectOrder::$_DESC);
        }
        $this->title = $title;
        $this->singleTitle = $singleTitle;
        $this->name = $name;
    }

    public function records() {
        $this->application->setMainTemplate("bicubic", "empty", $this->title);
        $this->application->setVariableTemplate("NAVIGATION-CONTENT", $this->objectTable($this->object, $this->order, [new LinkParam($this->application->name, "bicubic-$this->name-edit", [], 'lang_edit'), new LinkParam($this->application->name, "bicubic-$this->name-delete", [], 'lang_delete')], [new LinkParam($this->application->name, "bicubic-$this->name-export", [], 'lang_export'), new LinkParam($this->application->name, "bicubic-$this->name-import", [], 'lang_import'), new LinkParam($this->application->name, "bicubic-$this->name-add", [], 'lang_add')], new LinkParam($this->application->name, "bicubic-$this->name-json"), new LinkParam($this->application->name, "bicubic-$this->name-search"), new LinkParam($this->application->name, "bicubic-$this->name-reorder")));
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
        $originalObject = $this->originalObject;
        $this->application->setVariableTemplate("NAVIGATION-CONTENT", $this->objectForm($originalObject, "bicubic-$this->name-addSubmit"));
        $this->application->render();
    }

    public function addSubmit() {
	$originalObject = $this->originalObject;
        $object = $this->application->getFormObject($originalObject);
        $data = new TransactionManager($this->application->data);
        $data->data->begin();
        if (!$data->insertRecord($object)) {
            $data->data->rollback();
            $this->error('lang_servererror');
        }
        $data->data->commit();
        $this->application->redirect($this->application->name, "bicubic-$this->name");
    }
    
    public function export() {
	$this->objectExport($this->object);
    }
    
    public function import() {
        $this->application->setMainTemplate("bicubic", "import", $this->singleTitle);
	$originalObject = $this->originalObject;
        $this->application->setVariableTemplate("FORM-ID",  $this->application->navigation . get_class($originalObject));
	$this->application->setVariableTemplate("FORM-ACTION", $this->application->getAppUrl($this->application->name, "bicubic-$this->name-importSubmit"));
        $this->application->render();
    }
    
    public function importSubmit() {
	$data = new TransactionManager($this->application->data);
	$originalObject = $this->originalObject;
	if(!$this->objectImportSubmit($originalObject, $data)) {
	    $data->data->rollback();
            $this->error('lang_servererror', $this->importError);
	}
	$data->data->commit();
	$this->application->redirect($this->application->name, "bicubic-$this->name");
    }

    public function edit() {
        $id = $this->application->getUrlParam("id", PropertyTypes::$_INT);
	$originalObject = $this->originalObject;
	$originalObject->setId($id);
        $data = new TransactionManager($this->application->data);
        $object = $data->getRecord($originalObject);
        if (!$object) {
            $this->error('lang_idnotvalid');
        }
        $this->application->setMainTemplate("bicubic", "empty", $this->singleTitle);
        $this->application->setVariableTemplate("NAVIGATION-CONTENT", $this->objectForm($object, "bicubic-$this->name-editSubmit"));
        $this->application->render();
    }

    public function editSubmit() {
	$originalObject = $this->originalObject;
        $object = $this->application->getFormObject($originalObject);
        $data = new TransactionManager($this->application->data);
        $data->data->begin();
        if (!$data->updateRecord($object)) {
            $data->data->rollback();
            $this->error('lang_servererror');
        }
        $data->data->commit();
        $this->application->redirect($this->application->name, "bicubic-$this->name");
    }

    public function delete() {
        $id = $this->application->getUrlParam("id", PropertyTypes::$_INT);
        $originalObject = $this->originalObject;
	$originalObject->setId($id);
        $data = new TransactionManager($this->application->data);
        $data->data->begin();
        $object = $data->getRecord($originalObject);
        if (!$object) {
            $data->data->rollback();
            $this->error('lang_idnotvalid');
        }
        if (!$data->deleteRecord($object)) {
            $data->data->rollback();
            $this->error('lang_servererror');
        }
        $data->data->commit();
        $this->application->redirect($this->application->name, "bicubic-$this->name");
    }

}
