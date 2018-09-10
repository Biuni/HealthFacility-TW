<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	public $_logger;
	protected $_view;

	/**
	* Initialize the layout metadata
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

        $this->_view->headLink(array('rel' => 'favicon', 'href' => APP_URL.'/favicon.ico'));
        
        $this->_view->headLink()->appendStylesheet(APP_URL.'/css/fontawesome.min.css');
        $this->_view->headLink()->appendStylesheet(APP_URL.'/css/bootstrap.min.css');
        $this->_view->headLink()->appendStylesheet(APP_URL.'/css/bootstrap-datepicker.min.css');
        $this->_view->headLink()->appendStylesheet(APP_URL.'/css/dataTables.bootstrap4.min.css');
        $this->_view->headLink()->appendStylesheet(APP_URL.'/css/style.css');

        $this->_view->InlineScript()->appendFile(APP_URL.'/js/jquery-2.2.4.min.js');
        $this->_view->InlineScript()->appendFile(APP_URL.'/js/popper.min.js');
        $this->_view->InlineScript()->appendFile(APP_URL.'/js/moment.min.js');
        $this->_view->InlineScript()->appendFile(APP_URL.'/js/moment-timezone.min.js');
        $this->_view->InlineScript()->appendFile(APP_URL.'/js/bootstrap.min.js');
        $this->_view->InlineScript()->appendFile(APP_URL.'/js/bootstrap-datepicker.min.js');
        $this->_view->InlineScript()->appendFile(APP_URL.'/js/jquery.dataTables.min.js');
        $this->_view->InlineScript()->appendFile(APP_URL.'/js/main.js');
    }

	/**
	* Initialize the logger
	*/
    protected function _initLogging()
    {
        $writer = new Zend_Log_Writer_Stream(APPLICATION_PATH . '/data/log/logger.log');        
        $logger = new Zend_Log($writer);

        Zend_Registry::set('Log', $logger);

        $this->_logger = $logger;
    	$this->_logger->info('Bootstrap => ' . __METHOD__);
    }

    /**
    * Initialize the ACL plugin
    */
    protected function _initFrontControllerPlugin()
    {
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Application_Plugin_Acl());
    }

	/**
	* Initialize database connection
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

