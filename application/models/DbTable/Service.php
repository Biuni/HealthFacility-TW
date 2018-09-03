<?php

class Application_Model_DbTable_Service extends Zend_Db_Table_Abstract
{

    protected $_name = 'service';
    protected $_primary = 'service_id';

	/**
	* Get the name of primary key.
	*/
    public function getPrimary()
    {
    	return $this->_primary;
    }

}

