<?php

class Corporativo_MedicoController extends App_Controller_Action
{
    public function init()
    {
        $this->idOrganizacao = App_Identity::getOrganizacao();
        $this->mVwMedico = new Model_Corporativo_VwMedico();
    }
    
    /**
     * 
     */
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
	        $mEntidadePf = new Model_Corporativo_EntidadePf();
    	    $mPaciente = new Model_Consulta_Paciente();
    	    $mMedico = new Model_Corporativo_Medico();
    	    $mOrgMedico = new Model_Corporativo_OrganizacaoMedico();
    		$post = $this->_request->getPost();
    		$dtcadastro = date('Y-m-d H:i:s');
    		list($dd,$mm,$YY) = explode('/',$post["dt_nascimento"]);
    		$dados = array(
                "id_foto"            => !empty($post["imagem"]) ? 1 : (!empty($post['id_foto'])? $post['id_foto'] : 0),
                "st_nome"            => strtoupper($post['st_nome']),
    		    'st_nome_sondex'     => soundex($post["st_nome"]),
    		    'st_nome_metaphone'  => metaphone($post["st_nome"]),
    		    "dt_nascimento"      => $YY.'-'.$mm.'-'.$dd,
    		    'id_estrangeiro'     => 0,
                "st_sexo"            => $post['st_sexo'],
                "st_fonecontato"     => preg_replace('/\D+/', '', $post["st_fonecontato"]),
                "id_sms"             => empty($post["id_sms"]) ? 0 : 1,
                "st_cpf"             => preg_replace('/\D+/', '', $post["st_cpf"]),
                "st_email"           => $post['st_email'],
                "st_fonefixo"        => preg_replace('/\D+/', '', $post["st_fonefixo"]),
                "st_fonecelular"     => preg_replace('/\D+/', '', $post["st_fonecelular"]),
                "st_religiao"        => $post['st_religiao'],
                "st_rg"              => $post['st_rg'],
                "st_cep"             => preg_replace('/\D+/', '', $post["st_cep"]),
                "st_tipo_logradouro" => $post['st_tipo_logradouro'],
                "st_estado"          => $post['st_estado'],
                "st_logradouro"      => $post['st_logradouro'],
                "st_complemento"     => $post['st_complemento'],
                "st_numero"          => $post['st_numero'],
                "st_bairro"          => $post['st_bairro'],
                "st_cidade"          => $post['st_cidade'],
                "st_observacao"      => $post['st_observacao'],
    		    "st_nome_mae"        => strtoupper($post['st_nome_mae']),
    		    "st_nome_pai"        => strtoupper($post['st_nome_pai']),
                "id_municipio"       => empty($post['id_municipio']) ? null : $post['id_municipio']
            );

		    try {
		           if(empty($post["id_entidade"])){
	    		    	
	    		        $dados['dt_cadastro']= $dtcadastro;
	        			$cdEntidade = $mEntidadePf->insert($dados);
	        			$args1 = array('id_medico' => $cdEntidade,'st_crm' => $post['st_crm'],'st_tipo_conselho' => $post['st_tipo_conselho'],'id_estado' => $post['uf_conselho'], 'id_ativo' => '1');
	        			$rsMedico = $mMedico->insert($args1);
	        			$args2 = array('id_paciente' => $cdEntidade,'id_organizacao' => $this->idOrganizacao,'dt_cadastro' => $dtcadastro,'id_ativo' => '1');
	        			$resultPaciente = $mPaciente->insert($args2); 
	        			$args3 = array('idorganizacao' => $this->idOrganizacao,'idmedico' => $cdEntidade,'datainclusao' => $dtcadastro);
	        			$rsOrgMed = $mOrgMedico->insert($args3);
	        			
	    		    }else{
	
	    		       	$where = $mEntidadePf->getAdapter()->quoteInto('id_entidade = ?', $post["id_entidade"]);
	    		        $mEntidadePf->update($dados,$where);    		        
	    		        $where2 = $mMedico->getAdapter()->quoteInto('id_medico = ?', $post["id_entidade"]);
	    		        $mMedico->update(array('st_crm' => $post['st_crm'],'st_tipo_conselho' => $post['st_tipo_conselho'],'id_estado' => $post['uf_conselho'],'id_ativo'=>$post['id_ativo']), $where2);        			
	        			
	    		        $result = $mOrgMedico->fetchAll(array('idorganizacao=?' => $this->idOrganizacao,'idmedico=?' => $post["id_entidade"]))->toArray();
	    		        if(empty($result)){
	    		        	$args = array('idorganizacao' => $this->idOrganizacao,'idmedico' => $post["id_entidade"],'datainclusao' => $dtcadastro);
	        				$rsOrgMed = $mOrgMedico->insert($args);
	    		    	}
	    		    	
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
	    
        $mEspecialidade = new Model_Apoio_TbEspecialidade();
        $rsEspecialidade = $mEspecialidade->fetchAll()->toArray();
        
        $mProcedimento = new Model_Apoio_TbProcedimento();
        $rsProcedimento = $mProcedimento->fetchAll()->toArray();
        
        $mTipoconselho = new Model_Apoio_TbTipoConselho();
        $rsTipoconselho = $mTipoconselho->fetchAll()->toArray();
        
        $mEstado = new Model_Cep_Estado();
        $rsEstado = $mEstado->fetchAll()->toArray();
        
        $mConvenio = new Model_Apoio_TbConvenio();
        $where = $mConvenio->getAdapter()->quoteInto('ativo = ?', '1');
        $rsConvenio = $mConvenio->fetchAll($where)->toArray();
        
        $mPlano = new Model_Apoio_TbPlano();
        $where = $mPlano->getAdapter()->quoteInto('ativo = ?', '1');
        $rsPlano = $mPlano->fetchAll($where)->toArray();
        
        $this->view->rsEspecialidade = $rsEspecialidade;
        $this->view->rsProcedimento = $rsProcedimento;
        $this->view->rsTipoconselho = $rsTipoconselho;
        $this->view->rsConvenio = $rsConvenio;
        $this->view->rsEstado = $rsEstado;
    }
    
    /**
     * 
     */
    public function listagemAction()
    {
        $rsMedico = $this->mVwMedico->fetchAll(array('idorganizacao = ?' => $this->idOrganizacao))->toArray();
        $this->view->rsMedico = $rsMedico;
    }
    
    /**
     * 
     * @param unknown $params
     * @return string
     */
	public function getdadoscadastrados($params)
	{
        $mMedico = new Model_Corporativo_Medico();
		$dadospagina = $mMedico->getDataMedico($params);
		list($YY,$mm,$dd) = explode('-',$dadospagina[0]["dt_nascimento"]);
	    $dadospagina[0]["dt_nascimento"] = $dd.'/'.$mm.'/'.$YY;
		return $dadospagina[0];
	}
}