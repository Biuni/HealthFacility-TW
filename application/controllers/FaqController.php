<?php

/**
* FaqController
* 
* This is the controller class
* of the faq page.
*
* @author     Gianluca Bonifazi
* @category   controllers 
* @copyright  Univpm (c) 2018
*/

class FaqController extends Zend_Controller_Action
{
    /**
    * Initialize action controller
    */
    public function init()
    {}
    
    /**
    * indexAction
    *
    * @link       /faq
    * @category   actions
    */
    public function indexAction()
    {
        // Initialize the faq model
        $faq = new Application_Model_Faq();
        // Assign the view's variable used
        // to print the all the faq
        $this->view->assign('faq', $this->extractResult($faq->get()));
    }
    
    /**
    * extractResult
    *
    * @category   utilities
    */
    public function extractResult($result)
    {
        $data = array();
        $rowsetArray = $result->toArray();
        foreach ($rowsetArray as $column => $value) {
            $data[$column] = $value;
        }
        return $data;
    }
    
}

