<?php
class Corporativo_ClienteController extends App_Controller_Action
{
	public function init()
	{
		$this->idOrganizacao = App_Identity::getOrganizacao();
		$this->mVwPaciente = new Model_Consulta_VwPaciente();
	}

	/**
	 * 
	 * @throws Exception
	 */
	public function indexAction()
	{
		//Busca as informações cadastradas
	    if($this->_request->getParam('id'))
	    {
	    	list($date,$id) = explode('@',base64_decode($this->_request->getParam('id')));
	        $this->view->dadospagina = self::getdadoscadastrados($id);
	    }
	    	    
	    //Realiza a inserção das informações 
		if($this->_request->isPost())
    	{
    	    $mEntidadePf = new Model_Corporativo_EntidadePf();
    	    $mPaciente = new Model_Consulta_Paciente();
    		$post = $this->_request->getPost();
    		$dtcadastro = date('Y-m-d H:i:s');
    		list($dd,$mm,$YY) = explode('/',$post["dt_nascimento"]);
    		$dados = array(
				'st_cpf'         => preg_replace('/\D+/', '', $post["st_cpf"]),
				'st_nome'        => strtoupper($post["st_nome"]),
    			'st_nome_sondex' => soundex($post["st_nome"]),
    			'st_nome_metaphone' => metaphone($post["st_nome"]),
				'dt_nascimento'  => $YY.'-'.$mm.'-'.$dd,
				'id_foto'        => !empty($post["imagem"]) ? 1 : (!empty($post['id_foto'])? $post['id_foto'] : 0),
				'id_estrangeiro' => empty($post["id_estrangeiro"]) ? 0 : 1,
				'id_sms'         => empty($post["id_sms"]) ? 0 : 1,
				'st_sexo'        => $post['st_sexo'],
				'st_email'       => $post['st_email'],
				'st_observacao'  => $post['st_observacao'],
                'st_nome_mae'    => strtoupper($post['st_nome_mae']),
    		    'st_nome_pai'    => strtoupper($post['st_nome_pai']),
				'st_fonecontato' => preg_replace('/\D+/', '', $post["st_fonecontato"])
    		);
    		
    		try {
    			
    		    if(empty($post["id_entidade"])){
    		    	
    		        $dados['dt_cadastro']= $dtcadastro;
        			$cdEntidade = $mEntidadePf->insert($dados);
        			$args = array('id_paciente' => $cdEntidade,'id_organizacao' => $this->idOrganizacao,'dt_cadastro' => $dtcadastro,'id_ativo' => '1');
        			$resultPaciente = $mPaciente->insert($args); 
        			
    		    }else{

    		        $where = $mEntidadePf->getAdapter()->quoteInto('id_entidade = ?', $post["id_entidade"]);
    		        $mEntidadePf->update($dados,$where);
    		        $where2 = $mPaciente->getAdapter()->quoteInto('id_paciente = ?', $post["id_entidade"]);
    		        $mPaciente->update(array('id_ativo'=>$post['id_ativo']), $where2);
    		        $this->view->dadospagina = self::getdadoscadastrados($post["id_entidade"]);
    		    }
    		    
    			//Realiza o decode da imagem e grava no diretorio informado
    			if(!empty($post["imagem"])){
					                   
    			    $idFoto = empty($post["id_entidade"]) ? $cdEntidade : $post["id_entidade"];
                    list($tipo,$conteudo) = explode(",", $post["imagem"]);
                    if(!file_put_contents(APPLICATION_PATH . '/../public/img/fotos/'.$idFoto.".png", base64_decode($conteudo))){
                        throw new Exception(1);
                    }
                }
                
                $msg=2;
                
    		} catch (Exception $e) {
    			$msg = $e->getMessage();
    		}
    		
    		$this->view->msg = $msg;
    	}
	}
	
	/**
	 * Insere os dados complementares do paciente
	 * @throws Exception
	 */
	public function insertcomplementoAction() 
	{
		if($this->_request->isPost())
		{
			$post = $this->_request->getPost();
			$post["st_cep"] = preg_replace('/\D+/', '', $post["st_cep"]);
			$post["st_fonefixo"] = preg_replace('/\D+/', '', $post["st_fonefixo"]);
			$post["st_fonecelular"] = preg_replace('/\D+/', '', $post["st_fonecelular"]);
			$mEntidadePf = new Model_Corporativo_EntidadePf();
			$where = $mEntidadePf->getAdapter()->quoteInto('id_entidade = ?', $post["id_entidade"]);
			$mEntidadePf->update($post,$where);
			$this->view->dadospagina = self::getdadoscadastrados($post["id_entidade"]);
			$this->render('index');
		}
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function listagemAction()
	{
		$rsPacientes = $this->mVwPaciente->fetchAll(array('id_organizacao = ?' => $this->idOrganizacao), '',30)->toArray();
		$this->view->rsPacientes = $rsPacientes;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function inativarregistroAction()
	{
		if($this->_request->getParam('id'))
		{
			list($date,$id) = explode('@',base64_decode($this->_request->getParam('id')));
			$mPaciente = new Model_Consulta_Paciente();
			$where = $mPaciente->getAdapter()->quoteInto('id_paciente = ?', $id );
			$mPaciente->update(array('id_ativo'=> 0),$where);
			$this->_redirect('corporativo/cliente/listagem');
		}
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getpacientebycpfAction()
	{
		if($this->_request->isPost())
    	{
    		$cpf = $this->_request->getPost();
    		$mEntidadePf = new Model_Corporativo_EntidadePf();
			$rows = $mEntidadePf->fetchAll(array('st_cpf = ?'=> $cpf))->toArray();
    		$this->_helper->layout->disableLayout();
            $this->getHelper('viewRenderer')->setNoRender();
            $this->getResponse()->setBody(json_encode(array('result' => $rows)));
    	}
	}
	
	/**
	 * 
	 *
	 */
	public function getregistrosimilarAction()
	{
		if($this->_request->isPost())
		{
			$string = $this->_request->getPost();
			$mEntidadePf = new Model_Corporativo_EntidadePf();
			$rows = $mEntidadePf->fetchAll(array('st_nome_sondex=?'=>soundex($string['nome'])))->toArray();
			$a = metaphone($string['nome']);
			$result = array();
			foreach ($rows as $dados){
				$b = metaphone($dados['st_nome']);
				similar_text($a,$b,$percent);
				if($percent > 85){
				    if(isset($dados["dt_nascimento"]))list($YY,$mm,$dd) = explode('-',$dados["dt_nascimento"]);
					$result[] = array(
						'id_foto'       => $dados['id_foto'],
						'id_entidade'   => $dados['id_entidade'],
						'st_nome'       => $dados['st_nome'],
						'dt_nascimento' => isset($dados["dt_nascimento"]) ? $dd.'/'.$mm.'/'.$YY : '',
					    'dt_cadastro'   => substr($dados['dt_cadastro'],8,2).'/'.substr($dados['dt_cadastro'],5,2).'/'.substr($dados['dt_cadastro'],0,4),
					    'st_cpf'        => !empty($dados['st_cpf']) ? substr($dados['st_cpf'],0,3).'.'.substr($dados['st_cpf'],3,3).'.'.substr($dados['st_cpf'],6,3).'-'.substr($dados['st_cpf'],9) : ''
					);
				}
			}
			
			$this->_helper->layout->disableLayout();
			$this->getHelper('viewRenderer')->setNoRender();
            $this->getResponse()->setBody(json_encode(array('result' => $result)));
		}
	}
	
	/**
	 * 
	 * @param unknown $params
	 * @return string
	 */
	public function getdadoscadastrados($params)
	{
	    $mEntidadePf = new Model_Consulta_Paciente();
	    $dadospagina = $mEntidadePf->getDataPaciente($params);
		list($YY,$mm,$dd) = explode('-',$dadospagina[0]["dt_nascimento"]);
	    $dadospagina[0]["dt_nascimento"] = $dd.'/'.$mm.'/'.$YY;
		return $dadospagina[0];
	}
}