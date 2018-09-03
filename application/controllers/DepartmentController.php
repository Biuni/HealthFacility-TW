<?php

class DepartmentController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	$department = new Application_Model_Department();

    	$this->view->assign(
    		'department',
    		$this->extractResult($department->get())
    	);
    }

    public function detailsAction()
    {
    	$department = new Application_Model_Department();
    	$service = new Application_Model_Service();
		$id = $this->_getParam('id', null);

    	$this->view->assign(
    		'department',
    		$this->extractResult($department->find($id))
    	);
    	$this->view->assign(
    		'service',
    		$this->extractResult($service->selectByDepartment($id))
    	);

    }

    // -----------------------------------
	/**
	* Clean an prettifier SQL query result
	*/
    public function extractResult($result){
    	$data = [];
    	$rowsetArray = $result->toArray();
		foreach ($rowsetArray as $column => $value) {
			$data[$column] = $value;
		}
		return $data;
    }

}

