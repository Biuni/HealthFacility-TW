<?php

class Application_Model_Chat
{
	private $_dbTable;
	private $_primaryKey;

	public function __construct()
	{
		$this->_dbTable = new Application_Model_DbTable_Chat();
		$this->_primaryKey = $this->_dbTable->getPrimary();
	}

	/**
	* Fetches all FAQs.
	*/
	public function get($user_id)
	{
		$select = $this->_dbTable->select()
				->from(array('c' => 'chat'), array('c.message', 'c.time', 'c.user'))
				->joinInner(array('u' => 'user'), 'u.user_id = c.user', array('u.name', 'u.surname'))
				->where('c.user_chat_id = ?', $user_id)
				->order('time ASC')
				->setIntegrityCheck(false);
		// return: The row results of the Zend_Db_Adapter fetch mode.
		return $this->_dbTable->fetchAll($select);
	}

}

