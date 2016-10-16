<?php

class Usuario_UsuarioController extends App_Controller_Action
{
	public function init()
	{
		$this->idOrganizacao = App_Identity::getOrganizacao();
		$this->grupo = App_Identity::getGrupo();
		$this->idUsuario = App_Identity::getIdUsuario();
		$this->mUsuario = new Model_Usuario_Usuario();
	}
	
    public function indexAction()
    {
    	//Busca as informações cadastradas
    	if($this->_request->getParam('id'))
    	{
    		list($date,$id) = explode('@',base64_decode($this->_request->getParam('id')));
    		$this->view->dadospagina = self::getdadoscadastrados($id);
    	}
    	
    	if($this->_request->isPost())
    	{
    		$post = $this->_request->getPost();
    		$dtcadastro = date('Y-m-d H:i:s');
    		list($dd,$mm,$YY) = explode('/',$post["dt_nascimento"]);
    		$dados = array(
    				'st_usuario'           => strtolower($post['st_usuario']),
    				'st_senha'             => md5($post["password"]),
    				'cd_grupo'             => $this->grupo,
    				'nm_usuario'           => strtoupper($post['nm_usuario']),
    				'ds_sexo'              => $post['st_sexo'],
    				'dt_nascimento'        => $YY.'-'.$mm.'-'.$dd,
    				'nr_telefone'          => preg_replace('/\D+/', '', $post["st_fonecontato"]),
    				'nr_celular'           => preg_replace('/\D+/', '', $post["st_fonecelular"]),
    				'nr_cpf'               => preg_replace('/\D+/', '', $post["st_cpf"]),
    				'nr_rg'                => $post['st_rg'],
    				'ds_email'             => strtolower($post['st_email']),
    				'sn_foto'              => !empty($post["imagem"]) ? 1 : (!empty($post['id_foto'])? $post['id_foto'] : 0),
    				'st_usuario_cadastro'  => $this->idUsuario
    		);
    		
    		if(empty($post['usuario'])){
    			$dados['dt_cadastro'] = $dtcadastro;
    			$dados['id_ativo'] = 1;
    			$dados['id_organizacao_atual'] = null;
    			$rsUsuario = $this->mUsuario->insert($dados);
    			$this->view->dadospagina = self::getdadoscadastrados($rsUsuario);
    			
    		}else{
    			
    			$dados['id_ativo'] = $post['id_ativo'];
    			if(empty($post["password"])){
    				unset($dados['st_senha']);
    			}
    			
    			$where = $this->mUsuario->getAdapter()->quoteInto('id_usuario = ?', $post["usuario"]);
    			$this->mUsuario->update($dados, $where);
    		    $this->view->dadospagina = self::getdadoscadastrados($post["usuario"]);
    		}
    		
    		//Realiza o decode da imagem e grava no diretorio informado
    		if(!empty($post["imagem"])){
    			$idFoto = empty($post["usuario"]) ? $rsUsuario : $post["usuario"];
    			list($tipo,$conteudo) = explode(",", $post["imagem"]);
    			if(!file_put_contents(APPLICATION_PATH . '/../public/img/fotos/usuario/'.$idFoto.".png", base64_decode($conteudo))){
    				throw new Exception("Não gravou a foto");
    			}
    		}
    	}
    	
    	$mOrganizacao = new Model_Corporativo_Organizacao();
    	$rsOrganizacao = $mOrganizacao->fetchAll(array('cd_grupo = ?' => $this->grupo))->toArray();
    	$this->view->organizacao = $rsOrganizacao;
    	
    	$mUsuarioPerfil = new Model_Sistema_Perfil();
    	$rsPerfis = $mUsuarioPerfil->fetchAll()->toArray();
    	$this->view->perfil = $rsPerfis;    	
    }
    
    /**
     * 
     */
    public function acessoAction()
    {
    	if($this->_request->isPost())
    	{
    		$post = $this->_request->getPost();
    		$dtcadastro = date('Y-m-d H:i:s');
    		$mUsuarioOrg = new Model_Usuario_UsuarioOrganizacao();
    		$rsPerfis = $mUsuarioOrg->fetchAll(array('cd_usuario=?'=>$post['id_usuario'],'cd_organizacao=?'=>$post['id_organizacao'],'cd_grupo=?'=>$this->grupo))->toArray();
    		
    		if(!empty($rsPerfis))
    		{   			
    			$msg="Já existe um cadastro com esses parâmetros";
    		}else{
    			
    			$dados = array(
    					'cd_usuario' => $post['id_usuario'],
    					'cd_organizacao' => $post['id_organizacao'],
    					'cd_perfil' => $post['id_perfil'],
    					'cd_grupo' => $this->grupo,
    					'sn_ativo' => 1,
    					'dt_cadastro' => $dtcadastro,
    					'cd_user_cadastro' => $this->idUsuario
    			);
    			
    			$mUsuarioOrg->insert($dados);
    			$data = array('id_organizacao_atual'=>$post['id_organizacao']);
    			$where = $this->mUsuario->getAdapter()->quoteInto('id_usuario = ?', $post["id_usuario"]);
    			$this->mUsuario->update($data, $where);
    			
    			$msg="Cadastrado";
    		}
    		
    		$this->_helper->layout->disableLayout();
    		$this->getHelper('viewRenderer')->setNoRender();
    		$this->getResponse()->setBody(json_encode(array('result' => $msg)));
    	}
    }
    
    /**
     * 
     */
    public function deleteperfilAction()
    {
    	if($this->_request->isPost())
    	{
    		$post = $this->_request->getPost();
    		$mUsuarioOrg = new Model_Usuario_UsuarioOrganizacao();
    		$rsPerfil = $mUsuarioOrg->deleteReg($post);
    		$this->getHelper('viewRenderer')->setNoRender();
    	}
    }
    
    /**
     *
     * Enter description here ...
     */
    public function listagemAction()
    {
    	$rsUsuario = $this->mUsuario->fetchAll(array('cd_grupo = ?' => $this->grupo), '',30)->toArray();
    	$this->view->rsUsuario = $rsUsuario;
    }
    
    /**
     *
     * @param unknown $params
     * @return string
     */
    public function getdadoscadastrados($params)
    {
    	$dadospagina = $this->mUsuario->fetchAll(array('id_usuario = ?' => $params))->toArray();
    	$mUsuarioOrg = new Model_Usuario_UsuarioOrganizacao();
    	$rsPerfis = $mUsuarioOrg->getPerfilByParams($params,$this->grupo)->toArray();
    	list($YY,$mm,$dd) = explode('-',$dadospagina[0]["dt_nascimento"]);
    	$dadospagina[0]["dt_nascimento"] = $dd.'/'.$mm.'/'.$YY;
    	$dados = array(
    			'geral'  => $dadospagina[0],
    			'perfil' => $rsPerfis
    	) ;
    	
    	return $dados;
    }
}