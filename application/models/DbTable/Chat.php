<?php

class Application_Model_DbTable_Chat extends Zend_Db_Table_Abstract
{

    protected $_name = 'chat';
    protected $_primary = 'msg_id';

	/**
	* Get the name of primary key.
	*/
    public function getPrimary()
    {
    	return $this->_primary;
    }

}

