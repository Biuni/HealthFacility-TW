<?php

/**
* Application_Model_Chat
* 
* This is the model class
* of the chat.
*
* @author     Gianluca Bonifazi
* @category   models 
* @copyright  Univpm (c) 2018
*/

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
    * Fetches all messages.
    */
    public function get($user_id)
    {
        $select = $this->_dbTable->select()->from(array(
            'c' => 'chat'
        ), array(
            'c.message',
            'c.time',
            'c.user'
        ))->joinInner(array(
            'u' => 'user'
        ), 'u.user_id = c.user', array(
            'u.name',
            'u.surname'
        ))->where('c.user_chat_id = ?', $user_id)->order('time ASC')->setIntegrityCheck(false);

        // return: The row results of the Zend_Db_Adapter fetch mode.
        return $this->_dbTable->fetchAll($select);
    }
    
    /**
    * Inserts a new message.
    */
    public function create($array)
    {
        // return: The primary key of the row inserted.
        return $this->_dbTable->insert($array);
    }
    
    /**
    * getLastMessage
    *
    * Get only the messages
    * stored in the database
    * after the $data value.
    *
    * @return     message|time|user|name|surname
    * @category   query
    */
    public function getLastMessage($user_id, $data)
    {
        $select = $this->_dbTable->select()->from(array(
            'c' => 'chat'
        ), array(
            'c.message',
            'c.time',
            'c.user'
        ))->joinInner(array(
            'u' => 'user'
        ), 'u.user_id = c.user', array(
            'u.name',
            'u.surname'
        ))->where('c.user_chat_id = ?', $user_id)->where('c.time >= ?', $data)->order('c.time ASC')->setIntegrityCheck(false);

        return $this->_dbTable->fetchAll($select);
    }
}

