<?php

class Corporativo_ClinicaController extends App_Controller_Action
{
    public function init()
    {
        $this->idOrganizacao = App_Identity::getOrganizacao();
        $this->mOrganizacao = new Model_Corporativo_Organizacao();
    }

    /**
     * Insere e altera dados da organização
     * @throws ErrorException
     * @throws Exception
     */
    public function indexAction()
    {
    	//Busca as informações cadastradas
	    if($this->_request->getParam('id'))
	    {
	        $this->view->dadospagina = self::getdadoscadastrados(base64_decode($this->_request->getParam('id')));
	    }
    	
        if($this->_request->isPost())
        {     
            $post= $this->_request->getPost();
        	//realiza o upload do logotipo caso ele seja enviado    		
    		$_UP = array();
    		$_UP['pasta'] = APPLICATION_PATH . '/../public/img/logo/';
    		$_UP['tamanho'] = 1024 * 1024 * 2; // 2Mb
    		$_UP['extensoes'] = array('jpg', 'png','jpeg');
    		
    		set_error_handler(function ($errno, $errstr, $errfile, $errline ) {
    		    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    		});
    		
    		$nmArquivo = null;
		    if(file_exists($_FILES['arquivo']['tmp_name'])){
		    
		        $arquivo = $_FILES['arquivo']['name'];
		        list($nome,$extensao) = explode('.', $arquivo);
		        $nmArquivo = preg_replace('/\D+/', '', $post["st_cnpj"]).'.'.$extensao;
		    
		        if(array_search($extensao, $_UP['extensoes']) === false) throw new Exception("Por favor, envie arquivos com as seguintes extensões: jpg, jpeg ou png!");
		    
		        if($_UP['tamanho'] < $_FILES['arquivo']['size']) throw new Exception("O arquivo de upload é maior do que o limite de 2MB.");
		    
		        if(!move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $nmArquivo)) throw new Exception("Não foi possível enviar o arquivo, tente novamente!");
		    }
		    
		    //Formata dos dados para inserir ou alterar as informações
		    $dtcadastro = date('Y-m-d H:i:s');
		    $dados = array(
		        'st_razao_social'       => strtoupper($post['st_razao_social']),
		        'st_nome_fantasia'      => strtoupper($post['st_nome_fantasia']),
		        'st_cnpj'               => preg_replace('/\D+/', '', $post["st_cnpj"]),
		        'st_email'              => $post['st_email'],
		        'st_fonecontato'        => preg_replace('/\D+/', '', $post["st_fonecontato"]),
		        'st_cnes'               => $post['st_cnes'],
		        'st_responsavel'        => strtoupper($post['st_responsavel']),
		        'st_inscricao_estadual' => preg_replace('/\D+/', '', $post["st_inscricao_estadual"]),
		        'st_cep'                => preg_replace('/\D+/', '', $post["st_cep"]),
		        'st_tipo_logradouro'    => $post['st_tipo_logradouro'],
		        'st_estado'             => $post['st_estado'],
		        'st_logradouro'         => $post['st_logradouro'],
		        'st_complemento'        => $post['st_complemento'],
		        'st_numero'             => $post['st_numero'],
		        'st_bairro'             => $post['st_bairro'],
		        'st_cidade'             => $post['st_cidade'],
		        'st_observacao'         => $post['st_observacao'],
		        'id_municipio'          => empty($post['id_municipio']) ? null : $post['id_municipio'],
		        'id_ativo'              => isset($post['id_ativo']) ? 1 : 0,
		        'st_codigo'             => 'NDA',
		        'st_logo'               => $nmArquivo
		    );
		    	    
    		try {
    		    
    		    if(empty($post["idorganizacao"])){
    		    	
    		        $dados['dt_cadastro']= $dtcadastro;
    		        $dados['id_ativo'] = 1; 
    		        if(!$rsOrganizacao = $this->mOrganizacao->insert($dados)){
    		            if(unlink($_UP['pasta'].$nmArquivo))$nmArquivo = null;
    		            throw new Exception("Erro ao gravar registro, tente gravar novamente!");
    		        }
    		        
    		        $this->view->dadospagina = self::getdadoscadastrados($rsOrganizacao);
    		        
    		    }else{

    		        $where = $this->mOrganizacao->getAdapter()->quoteInto('idorganizacao = ?', $post["idorganizacao"]);
    		        if(!$rsOrganizacao = $this->mOrganizacao->update($dados,$where)){
    		            if(unlink($_UP['pasta'].$nmArquivo))$nmArquivo = null;
    		            throw new Exception("Erro ao Alterar registro, tente alterar novamente!");
    		        }
    		        
    		        $this->view->dadospagina = self::getdadoscadastrados($post["idorganizacao"]);
    		    }
    		    
                $msg="Registro gravado com sucesso!";
                
                
    		} catch (Exception $e) {
    			$msg = $e->getMessage();
    		}
    		
    		$this->view->msg = $msg;
        }
        
        //busca as especialidades cadastradas
        $mEspecialidade = new Model_Apoio_TbEspecialidade();
        $rsEspecialidade = $mEspecialidade->fetchAll()->toArray();
        //busca os procedimentos cadastrados
        $mProcedimento = new Model_Apoio_TbProcedimento();
        $rsProcedimento = $mProcedimento->fetchAll()->toArray();
		//busca os convenios cadastrados
        $mConvenio = new Model_Apoio_TbConvenio();
        $where = $mConvenio->getAdapter()->quoteInto('ativo = ?', '1');
        $rsConvenio = $mConvenio->fetchAll($where)->toArray();
        
        $this->view->rsEspecialidade = $rsEspecialidade;
        $this->view->rsProcedimento = $rsProcedimento;
        $this->view->rsConvenio = $rsConvenio;
    }
    
    /**
     * Busca as informações para preenchimento do grid
     */
    public function listagemAction()
    {
        $rsOrganizacao = $this->mOrganizacao->fetchAll()->toArray();
        $this->view->rsOrganizacao = $rsOrganizacao;
    }
    
    /**
     * Inativa o registro
     */
    public function inativarregistroAction()
    {
        if($this->_request->getParam('id'))
        {
            $where = $this->mOrganizacao->getAdapter()->quoteInto('idorganizacao = ?', base64_decode($this->_request->getParam('id')));
            $this->mOrganizacao->update(array('id_ativo'=> 0),$where);
            $this->_redirect('corporativo/clinica/listagem');
        }
    }
    
    /**
     * Busca os plano conforme o convênio informado.
     */
    public function planosconvenioAction()
    {
    	if($this->_request->isPost())
    	{
    		$convenio = $this->_request->getPost();
    		$mPlano = new Model_Apoio_TbPlano();
			$rows = $mPlano->fetchAll(array('idconvenio = ?'=> $convenio))->toArray();
    		$this->_helper->layout->disableLayout();
            $this->getHelper('viewRenderer')->setNoRender();
            $this->getResponse()->setBody(json_encode(array('result' => $rows)));
    	}
    }
    
    /**
     * Insere, altera ou exclui as especialidade referente a organização
     */
    public function orgespecialidadeAction()
    {
        if($this->_request->isPost())
        {
        	$post = $this->_request->getPost();
        	$arrayBanco = array();
            $arrayInativos = array();
			
            $mOrgEspecialidade = new Model_Corporativo_OrganizacaoEspecialidade();
			$getEspecialidades = $mOrgEspecialidade->fetchAll(array('id_organizacao=?'=>$post["id_organizacao"]))->toArray();
           
            foreach($getEspecialidades as $dados)
			{
				if($dados['id_ativo']== 0){
					$arrayInativos[] = $dados['id_especialidade'];
				}
				
				$arrayBanco[]=$dados['id_especialidade'];
            }
            
            $arraychecados = !empty($post['id_especialidade']) ? $post['id_especialidade'] : array(null);
                      
            $excluir = array_diff($arrayBanco, $arraychecados);
            $inserir = array_diff($arraychecados, $arrayBanco);
            $alterar = array_intersect($arraychecados, $arrayInativos);      
			echo 'inserir';
            Zend_Debug::dump($inserir);
            echo 'excluir';
            Zend_Debug::dump($excluir);
            echo 'alterar';
            Zend_Debug::dump($alterar);
            
            //die;
                        
            if(!empty($inserir))
            {
            	foreach ($inserir as $especialidade){
            		$post['id_especialidade'] = $especialidade;
            		$post['dt_cadastro'] = date('Y-m-d H:i:s');
            		$post['id_ativo'] = 1;
            		$resultset = $mOrgEspecialidade->insert($post);
            	}
            }
            
            if(!empty($excluir))
            {
            	foreach ($excluir as $especialidade2){
            		$post['id_especialidade'] = $especialidade2;
            		$args['id_ativo'] = 0;
            		$where = $this->mOrganizacao->getAdapter()->quoteInto('id_organizacao = ?', $post["id_organizacao"]);
            		$where = $this->mOrganizacao->getAdapter()->quoteInto('id_especialidade = ?', $post['id_especialidade']);
	                $resultset = $mOrgEspecialidade->update($args,$where);
            	}
            }
            
            if(!empty($alterar))
            {
            	foreach ($alterar as $especialidade3){
            		$post['id_especialidade'] = $especialidade3;
            		$args['id_ativo'] = 1;
            		$where = $this->mOrganizacao->getAdapter()->quoteInto('id_organizacao = ?', $post["id_organizacao"]);
            		$where = $this->mOrganizacao->getAdapter()->quoteInto('id_especialidade = ?', $post['id_especialidade']);
	                $resultset = $mOrgEspecialidade->update($args,$where);
            	}
            }
                   
            //$this->_helper->layout->disableLayout();
            $this->getHelper('viewRenderer')->setNoRender();
        }
    }

    /**
     * Insere, altera ou exclui as procedimentos referente a organização
     */
    public function orgprocedimentoAction()
    {

        if($this->_request->isPost())
        {
            Zend_Debug::dump($_POST);
            
            $this->_helper->layout->disableLayout();
            $this->getHelper('viewRenderer')->setNoRender();
        }
    }
    
    /**
     * Insere, altera ou exclui os convênios e planos referente a organização
     */
    public function orgconvenioAction()
    {

        if($this->_request->isPost())
        {
            Zend_Debug::dump($_POST);
        
            $this->_helper->layout->disableLayout();
            $this->getHelper('viewRenderer')->setNoRender();
        }
    }
    
    /**
     * Função que busca as informações cadastradas de uma organização
     * @param unknown $params
     * @return string
     */
    public function getdadoscadastrados($params)
    {
        $dadospagina = $this->mOrganizacao->fetchAll(array('idorganizacao=?'=>$params))->toArray();
        return $dadospagina[0];
    }
}