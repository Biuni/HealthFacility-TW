<?php

class ServiceController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
		$id = $this->_getParam('id', null);
    	$service = new Application_Model_Service();
    	$staff = new Application_Model_Staff();

    	$serviceDetails = $service->find($id);

    	$this->view->assign(
    		'service',
    		$this->extractResult($serviceDetails)
    	);
    	$this->view->assign(
    		'staff',
    		$this->extractResult($staff->find($serviceDetails[0]['staff']))
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

