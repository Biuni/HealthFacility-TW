<?php

/**
* SearchController
* 
* This is the controller class
* of the search page.
*
* @author     Gianluca Bonifazi
* @category   controllers 
* @copyright  Univpm (c) 2018
*/

class SearchController extends Zend_Controller_Action
{
    /**
    * Initialize action controller
    */
    public function init()
    {}
    
    /**
    * indexAction
    *
    * @link       /search
    * @category   actions
    */
    public function indexAction()
    {
        // Initialize the department and service model
        $department = new Application_Model_Department();
        $service = new Application_Model_Service();

        // Initialize an empty array
        $searchJson = array();
        
        // Get all departments
        $departmentDetails = $this->extractResult($department->get());
        foreach ($departmentDetails as $departmentDett) {
            $serviceArray = array();
            // Get all services of the department
            $serviceDetails = $this->extractResult($service->selectByDepartment($departmentDett['department_id']));
            foreach ($serviceDetails as $serviceDet) {
                $serviceArray[] = $serviceDet;
            }
            // Push services into department
            $departmentDett['service'] = $serviceArray;
            //Push all result into search JSON
            $searchJson[] = $departmentDett;
        }
        
        // Assign the view's variable used
        // to store JSON into javascript variable
        $this->view->assign('search', json_encode($searchJson));
        
    }

    /**
    * extractResult
    *
    * @category   utilities
    */
    public function extractResult($result)
    {
        // Extract the result of a query
        // and return an array with the values
        $data = array();
        $rowsetArray = $result->toArray();
        foreach ($rowsetArray as $column => $value) {
            $data[$column] = $value;
        }
        return $data;
    }
    
}