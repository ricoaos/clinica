<?php

class Model_Usuario_Usuario extends Zend_Db_Table
{
    protected $_schema  = 'usuario';
    protected $_name    = 'usuario';
    protected $_primary = array('id_usuario');

	/**
	 * @param unknown $sUser
	 * @param unknown $sPassword
	 * @return boolean
	 */
    public function logar($sUser, $sPassword)
    {
        if (!$sUser || !$sPassword) {
            return 1;
        }

        //$filtro = 'md5(?)';
        $oAuthAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter(), 'usuario.usuario', 'st_usuario', 'st_senha');
        $oAuthAdapter->setIdentity($sUser);
        $oAuthAdapter->setCredential($sPassword);

        $oAuth   = Zend_Auth::getInstance();
        $oResult = $oAuth->authenticate($oAuthAdapter);
        
        // limpa o identity porque ele é gerado pelo método gravarIdentity
        $oAuth->clearIdentity();

        if ($oResult->isValid()) {
            $rowObject  = $oAuthAdapter->getResultRowObject(null, array('st_senha'));

            if($rowObject->id_ativo == false){
            	return 3;
            }
            
           // Zend_Debug::dump($rowObject);die;
            $idOrganizacao = $rowObject->id_organizacao_atual;
            if(!empty($idOrganizacao))
            {
	            $this->gravarIdentity($idOrganizacao, $rowObject->id_usuario);
	            return 0;
	            
            }else{
            	
            	return 2;
            }
        }
        return 1;
    }

    /**
     * 
     */
    public function deslogar()
    {
        Zend_Auth::getInstance()->clearIdentity();

        $aclSessao = new Zend_Session_Namespace('acl');
        $aclSessao->unsetAll();
    }

    /**
     * @param unknown $idOrganizacao
     * @param unknown $idUsuario
     */
    public function gravarIdentity($idOrganizacao, $idUsuario)
    {
        $row = $this->fetchRow(array('id_usuario = ?' => $idUsuario));
        
        $mGrupo = new Model_Grupo_Grupo();
        $rowGrupo = $mGrupo->fetchRow(array('cd_grupo=?'=> $row->cd_grupo));

        //$mEntidade = new Model_Corporativo_EntidadePf();
        //$rEntidade = $mEntidade->fetchRow(array('id_entidade = ?' => $idUsuario));
        
        //Zend_Debug::dump($rEntidade);die;

        // carrega perfis
        //$mUsuarioPerfil = new Model_Sistema_UsuarioPerfil();
        //$rsPerfis = $mUsuarioPerfil->fetchAll(array('id_organizacao = ?' => $idOrganizacao, 'id_usuario = ?' => $idUsuario));

        $mOrganizacao = new Model_Corporativo_Organizacao();
        $rowOrganizacao = $mOrganizacao->fetchRow(array('id_organizacao = ?' => $idOrganizacao));

        $identity = (object) $row->toArray();
        $identity->grupo = (object) $rowGrupo->toArray();
        //$identity->st_nome        = $rowEntidade->st_nome;
        //$identity->st_email       = $rowEntidade->st_email;
        $identity->organizacao    = (object) $rowOrganizacao->toArray();
        //$identity->aPerfis        = $aPerfis;
        unset($identity->st_senha);

        $oAuth = Zend_Auth::getInstance();
        $oAuth->clearIdentity();
        $oAuth->getStorage()->write($identity);
    }
}