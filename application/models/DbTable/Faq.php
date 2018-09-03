<?php

class Application_Model_DbTable_Faq extends Zend_Db_Table_Abstract
{

    protected $_name = 'faq';
    protected $_primary = 'faq_id';

	/**
	* Get the name of primary key.
	*/
    public function getPrimary()
    {
    	return $this->_primary;
    }

}

