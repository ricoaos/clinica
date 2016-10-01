<?php

class Administrativo_AuthController extends App_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->layout->disableLayout();
        $mUsuario = new Model_Usuario_Usuario();

        $msg = 0;
        // verifica se submeteu o formulário
        if ($this->getRequest()->isPost()) {
            $filter    = new Zend_Filter_StripTags();
            $sUser     = $filter->filter($this->_request->getPost('usuario'));
            $sPassword = $filter->filter($this->_request->getPost('senha'));

            try {
            	$return = $mUsuario->logar($sUser, md5($sPassword));
                if ($return == 0) {
                    return $this->_redirect('administrativo/painel');
                }
                
                $msg = $return;
                
            } catch (Ev_Exception $e) {
                return $this->_addMessage($e->getMessage(), 'auth');
            }
        }

        $this->view->msg = $msg;
        $mUsuario->deslogar();
    }
}
