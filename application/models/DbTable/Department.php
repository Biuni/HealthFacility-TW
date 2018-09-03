<?php

class Application_Model_DbTable_Department extends Zend_Db_Table_Abstract
{

    protected $_name = 'department';
    protected $_primary = 'department_id';

	/**
	* Get the name of primary key.
	*/
    public function getPrimary()
    {
    	return $this->_primary;
    }

}

