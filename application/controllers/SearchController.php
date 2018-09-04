<?php

class SearchController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	$searchJson = [];
        $department = new Application_Model_Department();
        $service = new Application_Model_Service();

        // Get all departments
    	$departmentDetails = $this->extractResult($department->get());
		foreach ($departmentDetails as $departmentDett) {
			$serviceArray = [];
			// Get all services of the department
    		$serviceDetails = $this->extractResult($service->selectByDepartment($departmentDett['department_id']));
			foreach ($serviceDetails as $serviceDet) {
    			$serviceArray[] = $serviceDet;
    		}
    		// Push services into department
    		$departmentDett['service'] = $serviceArray;
    		//Push all into search JSON
    		$searchJson[] = $departmentDett;
		}
		
        $this->view->assign(
            'search',
            json_encode($searchJson)
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

