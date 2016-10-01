<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected static $DB, $config;

	protected function _initAutoLoader()
	{
	    $modelLoader = new Zend_Application_Module_Autoloader(array(
	        'namespace' => '',
	        'basePath'  => APPLICATION_PATH));
	    return $modelLoader;
	}
	
	protected function _initConfg()
	{
		//self::$DB = new Zend_Config_Ini(APPLICATION_PATH . '/configs/database.ini');
	}

    protected function _initDoctype()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
    }

    protected function _initAuth()
    {
        return;
    	Zend_Registry::set('config', self::$config);
    }

    public function _initSession()
    {
        return;
        $session = new Zend_Session_Namespace('Sis');
        Zend_Registry::set('session' ,$session);
    }

}