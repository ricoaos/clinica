<?php

class App_Controller_Action extends Zend_Controller_Action
{
    /**
     * Pre-dispatch routines
     *
     * Called before action method. If using class with
     * {@link Zend_Controller_Front}, it may modify the
     * {@link $_request Request object} and reset its dispatched flag in order
     * to skip processing the current action.
     *
     * @return void
     */
    public function preDispatch()
    {
    	define('BASE_URL', $this->view->baseUrl());
    	
        $sResource = $this->getRequest()->getControllerName();
        if ('auth' == $sResource) {
            return;
        }

        if (!Zend_Auth::getInstance()->hasIdentity()) {
            return $this->_redirect('auth');
        }
    }
}