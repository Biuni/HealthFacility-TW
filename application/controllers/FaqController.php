<?php

class FaqController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	$faq = new Application_Model_Faq();
    	$this->view->assign('faq', $faq->get());
    }


}

