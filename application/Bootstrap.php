<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected $_logger;
	protected $_view;

	/**
	* Initalize the layout metadata
	* and the CSS / Javascript files
	*/
	protected function _initViewSettings()
    {
        define('APP_URL', 'http://localhost/HealthFacility/public');

        $this->bootstrap('view');
        $this->_view = $this->getResource('view');
        $this->_view->headMeta()->setCharset('UTF-8');
        $this->_view->headMeta()->appendName('viewport', 'width=device-width,initial-scale=1,shrink-to-fit=no');

        $this->_view->headTitle('Health Facility | Gianluca Bonifazi - Progetto Tecnologie Web');

        $this->_view->headLink()->appendStylesheet(APP_URL.'/css/bootstrap.min.css');
        $this->_view->headLink()->appendStylesheet(APP_URL.'/css/style.css');

        $this->_view->InlineScript()->appendFile(APP_URL.'/js/jquery-3.3.1.slim.min.js');
        $this->_view->InlineScript()->appendFile(APP_URL.'/js/popper.min.js');
        $this->_view->InlineScript()->appendFile(APP_URL.'/js/bootstrap.min.js');
    }

	/**
	* Initalize the logger
	*/
    protected function _initLogging()
    {
        $writer = new Zend_Log_Writer_Stream(APPLICATION_PATH . '/data/log/logger.log');        
        $logger = new Zend_Log($writer);

        Zend_Registry::set('log', $logger);

        $this->_logger = $logger;
    	$this->_logger->info('Bootstrap ' . __METHOD__);
    }

	/**
	* Initalize database connection
	*/
	protected function _initDbParms()
    {
		require_once(APPLICATION_PATH . '/configs/connection.php');

		$db = new Zend_Db_Adapter_Pdo_Mysql(array(
    		'host'     => DB_HOST,
    		'username' => DB_USER,
    		'password' => DB_PWD,
    		'dbname'   => DB_NAME,
            'charset'  => 'utf8'
		));
		
		Zend_Db_Table_Abstract::setDefaultAdapter($db);
	}

}

