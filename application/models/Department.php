<?php

class Application_Model_Department
{

	private $_dbTable;
	private $_primaryKey;

	public function __construct()
	{
		$this->_dbTable = new Application_Model_DbTable_Department();
		$this->_primaryKey = $this->_dbTable->getPrimary();
	}

	/**
	* Fetches all departments.
	*/
	public function get()
	{
		// return: The row results of the Zend_Db_Adapter fetch mode.
		return $this->_dbTable->fetchAll();
	}

	/**
	* Fetches departments by primary key. The argument specifies
	* one or more primary key value(s). To find multiple
	* rows by primary key, the argument must be an array.
	*/
	public function find($id)
	{
		// return: Row(s) matching the criteria.
		return $this->_dbTable->find($id);
	}

	/**
	* Inserts a new department.
	*/
	public function create($array)
	{
		// return: The primary key of the row inserted.
		return $this->_dbTable->insert($array);
	}

	/**
	* Updates existing department.
	*/
	public function update($array, $id)
	{
		// return: The number of rows updated.
		return $this->_dbTable->update($array, "$this->_primaryKey = $id");
	}

	/**
	* Deletes existing department.
	*/
	public function delete($id)
	{
		// return: The number of rows deleted.
		return $this->_dbTable->delete($id);
	}


	// -----------------------------
	// Custom method for this model
	// -----------------------------

	/**
	* Select services by department
	*/
	public function paginator($page)
	{
		$adapter = new Zend_Paginator_Adapter_DbSelect($this->_dbTable->select()->from('department'));
		$paginator = new Zend_Paginator($adapter);

		$paginator->setItemCountPerPage(5)->setCurrentPageNumber($page);

		return $paginator;
	}
	/**
	* Count number of rows
	*/
	public function countDepartments()
	{
		$countRows = $this->_dbTable->select()->from('department', array('rows' => 'COUNT(*)'));
		return $this->_dbTable->fetchRow($countRows);
	}

}

