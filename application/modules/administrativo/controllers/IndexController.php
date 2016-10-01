<?php

class Administrativo_IndexController extends App_Controller_Action
{
	public function init()
	{
		$this->idOrganizacao = App_Identity::getOrganizacao();
		$this->grupo = App_Identity::getGrupo();
		$this->idUsuario = App_Identity::getIdUsuario();
	}
	
    public function indexAction()
    {
        //Zend_Debug::dump(Zend_Auth::getInstance()->getIdentity()); 
        //die;
    }

    public function mudarCenarioAction()
    {
    	$idOrganizacao = $this->_getParam('org');
    	$idUsuario = App_Identity::getIdUsuario();
    	
    	$session = new Zend_Session_Namespace('temp');
    	if ($idOrganizacao) {
    		$mUsuario = new Model_Usuario_Usuario();
    	
    		$mUsuario->gravarIdentity($idOrganizacao, $idUsuario);
    		$mUsuario->update(array('id_organizacao_atual' => $idOrganizacao), array('id_usuario = ?' => $idUsuario));
    	
    		return $this->_redirect($session->sUrlOrigem);
    	}
    	    	
    	$mUsuarioOrg = new Model_Usuario_UsuarioOrganizacao();
    	$rsPerfis = $mUsuarioOrg->fetchAll(array('cd_usuario = ?' => $idUsuario, 'sn_ativo = ?' => '1', 'cd_grupo = ?' => $this->grupo));
    	    	
    	$aOrganizacoes = array();
    	foreach ($rsPerfis as $rOrganizacao) {
    		$aOrganizacoes[] = $rOrganizacao->cd_organizacao;
    	}
    	
    	$mOrganizacao = new Model_Corporativo_Organizacao();
    	$rsOrganizacoes = $mOrganizacao->fetchAll(array('id_organizacao in (?)' => $aOrganizacoes));
    	$this->view->rsOrganizacoes = $rsOrganizacoes;
    	
    	// Recuperar a URL de origem
    	$baseUrl = $this->view->serverUrl() . $this->view->baseUrl();
    	$session->sUrlOrigem = substr($_SERVER['HTTP_REFERER'], strlen($baseUrl) + 1);
    }
}