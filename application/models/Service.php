<?php

class Application_Model_Service
{

	private $_dbTable;
	private $_primaryKey;

	public function __construct()
	{
		$this->_dbTable = new Application_Model_DbTable_Service();
		$this->_primaryKey = $this->_dbTable->getPrimary();
	}

	/**
	* Fetches all services.
	*/
	public function get()
	{
		// return: The row results of the Zend_Db_Adapter fetch mode.
		return $this->_dbTable->fetchAll();
	}

	/**
	* Fetches services by primary key. The argument specifies
	* one or more primary key value(s). To find multiple
	* rows by primary key, the argument must be an array.
	*/
	public function find($id)
	{
		// return: Row(s) matching the criteria.
		return $this->_dbTable->find($id);
	}

	/**
	* Inserts a new service.
	*/
	public function create($array)
	{
		// return: The primary key of the row inserted.
		return $this->_dbTable->insert($array);
	}

	/**
	* Updates existing service.
	*/
	public function update($array, $id)
	{
		// return: The number of rows updated.
		return $this->_dbTable->update($array, "$this->_primaryKey = $id");
	}

	/**
	* Deletes existing service.
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
	public function selectByDepartment($id)
	{
		$select = $this->_dbTable->select()
								 ->from('service', array('service_id', 'name'))
								 ->where("department = $id");
		// return: The row results of the Zend_Db_Adapter fetch mode.
        return $this->_dbTable->fetchAll($select);
	}

	/**
	* Select services by department
	*/
	public function checkServiceOpen($service_id, $date)
	{
		$schedule = $this->_dbTable->find($service_id)->toArray()[0]['schedule'];

		$result = 1;
		$timestamp = strtotime($date);
		$dayId = date('w', $timestamp);
		$parsedSchedule = json_decode($schedule)->schedule[$dayId];
		$bookingHour = date('H:i', $timestamp);

		// If all day is closed
		if ($parsedSchedule->m_opening == null && $parsedSchedule->a_opening == null) {
			$result = 0;
		// If not all day is closed
		// Check if the morning is closed
		} elseif ($parsedSchedule->m_opening == null) {
			if($bookingHour < $parsedSchedule->a_opening) {
				$result = 0;
			} else if ($bookingHour >= $parsedSchedule->a_closing) {
				$result = 0;
			}
		// If the morning isn't closed
		// Check if the afternoon is closed
		} else if($parsedSchedule->a_opening == null) {
			if($bookingHour < $parsedSchedule->m_opening) {
				$result = 0;
			} else if ($bookingHour >= $parsedSchedule->m_closing) {
				$result = 0;
			}
		// If the structure is open morning
		// and afternoon check all times
		} else {
			if ($bookingHour < $parsedSchedule->m_opening) {
				$result = 0;
			} else if($bookingHour >= $parsedSchedule->m_closing && $bookingHour < $parsedSchedule->a_opening) {
				$result = 0;
			} else if ($bookingHour >= $parsedSchedule->a_closing) {
				$result = 0;
			}
		}

		return $result;
	}


}

