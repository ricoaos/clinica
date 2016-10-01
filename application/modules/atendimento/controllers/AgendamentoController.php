<?php

class Atendimento_AgendamentoController extends App_Controller_Action
{	
	public function init(){
		
	}
	
	public function cadastroAction(){
		
	    if ($this->_request->getPost()){
	        $Model_Corporativo_Atendimento = new Model_Corporativo_Atendimento;
	        $post = $this->_request->getPost();

	        $dados = array(
                'entidade_pf'    => $post['xentidade_pf'],
                'idmedico'    => $post['xidmedico'],
	            'idespecialidade'    => $post['xidespecialidade'],
	            'idplano'    => $post['xidplano'],
	            'idprocedimento'    => $post['xidprocedimento'],
	            'dataatendimento'    => $post['xdataatendimento']
	        );
	        
	        $save = $Model_Corporativo_Atendimento->insert($dados);
	        $msg = $save ? "Consulta Agendada com sucesso!" : "Erro ao agendar consulta! tente novamente";
	        
	        $this->_helper->layout->disableLayout();
	        $this->getHelper('viewRenderer')->setNoRender();
	        $this->getResponse()->setBody($msg);	        
	    }
	    
	    // Dia da semana
	    $diasemana = date('w', strtotime(date('Y-m-d')));
	    
		// Medicos organizacao
		$Model_Corporativo_MedicoAgenda = new Model_Corporativo_MedicoAgenda;
		$getMedicosAgendaByData = $Model_Corporativo_MedicoAgenda->getMedicosAgendaByData(array('data' => date('Y-m-d'), 'diasemana' => $diasemana))->toArray();
		
		// Atendimentos
		$Model_Corporativo_Atendimento = new Model_Corporativo_Atendimento;
		
		foreach ($getMedicosAgendaByData as $id => $reg){
			
		    $getAgendaByMedico = $Model_Corporativo_MedicoAgenda->getAgendaByMedico(array('idmedico' => $reg['idmedico'], 'diasemana' => $diasemana));
		    
			$dadosConsulta['medicos'][$reg['idmedico']] = array(
				'nome'                => $reg['nmMedico'],
				'idmedico'            => $reg['idmedico'],
				'duracao_atendimento' => $reg['duracao_atendimento'],
				'diasDaSemana'        => '',
				'nmEspecialidade'     => $reg['nmOrganizacao'],
				'expediente_inicial'  => $reg['expediente_inicial'],
				'expediente_final'    => $reg['expediente_final'],
			    'data'                => date('Y-m-d'),
			    'agenda'              => $getAgendaByMedico->toArray()
			);
			
			$parms = array('idmedico' => $reg['idmedico'], 'data' => date('Y-m-d'));
			$atendimentos = $Model_Corporativo_Atendimento->getAtendimentosByParams($parms)->toArray();
			
			// Atendimentos do medico
			if ($atendimentos){
				foreach ($atendimentos as $id2 => $dados){
					$dadosConsulta['medicos'][$reg['idmedico']]['atendimentos'][$dados['hora']] = array(
						'data'        =>  $dados['data'],
						'hora'        =>  $dados['hora'],
						'tipo'        =>  '2',
						'idStatus'    =>  $dados['status'],
						'nmStatus'    =>  'Confirmado',
						'obs'         =>  $dados['observacao'],
						'idCliente'   =>  $dados['entidade_pf'],
						'nmCliente'   =>  $dados['nmCliente'],
						'email'       =>  null,
						'idConvenio'  =>  null,
						'nmConvenio'  =>  null,
						'idPlano'     =>  $dados['idplano'],
						'nmPlano'     =>  $dados['nmPlano'],
						'idProcedimento'=>  $dados['idprocedimento'],
						'nmProcedimento'=>  $dados['nmProcedimento'],
						'valor' =>  null
					);
				}
			}
			
		}

		$this->view->dadosConsulta = $dadosConsulta;
		
		$mEspecialidade = new Model_Corporativo_MedicoEspecialidade;
		$this->view->medicos = $mEspecialidade->getMedicosAgendaOpen();
		
		$rsEspecialidade = $mEspecialidade->getEspecialidadesMedicosAtivos();
		$this->view->especialidade = $rsEspecialidade;
	}
	
	/**
	 * @abstract Busca Agenda do medico por data
	 * @version 15/10/2015
	 * @author Weidman Ferreira
	 */
	
	public function getagendabydataAction(){
		
		$this->_helper->layout->disableLayout();
		
		if ($this->_request->isPost()){
		    
		    $diasemana = date('w', strtotime($this->_request->getPost('data')));
		    
		    $data = $this->_request->getPost();
		    $data['diasemana'] = $diasemana;
		    
			// Medicos organizacao
			$Model_Corporativo_MedicoAgenda = new Model_Corporativo_MedicoAgenda;
			$getMedicosAgendaByData = $Model_Corporativo_MedicoAgenda->getMedicosAgendaByData($data)->toArray();
			
			//Zend_Debug::dump($data);
			// Atendimentos
			$Model_Corporativo_Atendimento = new Model_Corporativo_Atendimento;
			$dadosConsulta = array();
			
			if($getMedicosAgendaByData) {
			    
    			foreach ($getMedicosAgendaByData as $id => $reg){
    				
    			    $getAgendaByMedico = $Model_Corporativo_MedicoAgenda->getAgendaByMedico(array('idmedico' => $reg['idmedico'], 'diasemana' => $diasemana));
    			    
    				$dadosConsulta['medicos'][$reg['idmedico']] = array(
    					'nome'                => $reg['nmMedico'],
    					'idmedico'            => $reg['idmedico'],
    					'duracao_atendimento' => $reg['duracao_atendimento'],
    					'diasDaSemana'        => '',
    					'nmEspecialidade'     => $reg['nmOrganizacao'],
    					'expediente_inicial'  => $reg['expediente_inicial'],
    					'expediente_final'    => $reg['expediente_final'],
    				    'data'                => $this->_request->getPost('data'),
    				    'agenda'              => $getAgendaByMedico->toArray()
        			);
        		
        			$parms = array('idmedico' => $reg['idmedico'], 'data' => $this->_request->getPost('data'));
        			$atendimentos = $Model_Corporativo_Atendimento->getAtendimentosByParams($parms)->toArray();
        		
        			// Atendimentos do medico
        			if ($atendimentos){
        				foreach ($atendimentos as $id2 => $dados){
        					$dadosConsulta['medicos'][$reg['idmedico']]['atendimentos'][$dados['hora']] = array(
    							'data'        =>  $dados['data'],
    							'hora'        =>  $dados['hora'],
    							'tipo'        =>  '2',
    							'idStatus'    =>  $dados['status'],
    							'nmStatus'    =>  'Confirmado',
    							'obs'         =>  $dados['observacao'],
    							'idCliente'   =>  $dados['entidade_pf'],
    							'nmCliente'   =>  $dados['nmCliente'],
    							'email'       =>  null,
    							'idConvenio'  =>  null,
    							'nmConvenio'  =>  null,
    							'idPlano'     =>  $dados['idplano'],
    							'nmPlano'     =>  $dados['nmPlano'],
    							'idProcedimento'=>  $dados['idprocedimento'],
    							'nmProcedimento'=>  $dados['nmProcedimento'],
    							'valor' =>  null
        					);
        				}
        			}
        			
                }
            }
    		
    		$this->view->dadosConsulta = $dadosConsulta;
    	}
    	
    }
    
    /**
     * @abstract getfiltrosagendamento
     * @author Weidman Ferreira
     * @version 27/10/2015
     * 
     * Busca dados agendamento
     */
    
	public function getfiltroagendamentoAction(){
		$mEspecialidade = new Model_Corporativo_MedicoEspecialidade;
		$mMedicoProcedimento = new Model_Corporativo_MedicoProcedimento;

		$stmt = array(
			'result' => array(
				'medico' => $mEspecialidade->getMedicosAgendaOpen()->toArray(),
				'especialidade' => $mEspecialidade->getEspecialidadesMedicosAtivos()->toArray(),
				'procedimento' => $mMedicoProcedimento->getProcedimentosMedico(null)->toArray()
			)
		);
		
		$this->_helper->layout->disableLayout();
		$this->getHelper('viewRenderer')->setNoRender();
		$this->getResponse()->setBody(json_encode($stmt));
	}
	
    /**
     * @abstract getEspecialidadesByMedico
     * @author Weidman Ferreira
     * @version 15/10/2015
     *
     * Busca as Especialidades do Medicos
     *
     **/
    
	public function getespecialidadesbymedicoAction(){
		if ($this->_request->isPost()){
			$mMedicoEspecialidade = new Model_Corporativo_MedicoEspecialidade;
			$rMedicoEspecialidade = $mMedicoEspecialidade->getEspecialidadesMedicosAtivos($this->_request->getPost('idmedico'));
			$this->_helper->layout->disableLayout();
			$this->getHelper('viewRenderer')->setNoRender();
			$this->getResponse()->setBody(json_encode(array('resposta' => $rMedicoEspecialidade->toArray())));
		}
	}
	
    /**
     * @abstract getProcedimentosByMedico
     * @author Weidman Ferreira
     * @version 15/10/2015
     *
     * Busca Procedimentos do Medicos
     *
     **/
    
    public function getprocedimentosmedicoAction(){    
        if ($this->_request->isPost()){    
            $mMedicoProcedimento = new Model_Corporativo_MedicoProcedimento;
            $rMedicoProcedimento = $mMedicoProcedimento->getProcedimentosMedico($this->_request->getPost());           
            $this->_helper->layout->disableLayout();
            $this->getHelper('viewRenderer')->setNoRender();
            $this->getResponse()->setBody(json_encode(array('resposta' => $rMedicoProcedimento->toArray())));
        }    
    }   
    
    /**
     * @abstract getconvbyproc
     * @author Weidman Ferreira
     * @version 15/10/2015
     *
     * Busca Convenios do Medico/Medicos por procedimento
     *
     **/
    
    public function getconvbyprocAction(){
        if ($this->_request->isPost()){
            $mMedicoPlano = new Model_Corporativo_MedicoPlano;
            $rMedicoConvenio = $mMedicoPlano->getConveniosMedico($this->_request->getPost());
            $this->_helper->layout->disableLayout();
            $this->getHelper('viewRenderer')->setNoRender();
            $this->getResponse()->setBody(json_encode(array('resposta' => $rMedicoConvenio->toArray())));
        }
    } 
    
    /**
     * @abstract getplanosbymedico
     * @author Weidman Ferreira
     * @version 27/10/2015
     * 
     * Busca os Planos de um Convenio
     */
    
    public function getplanosbymedicoAction(){
    	if ($this->_request->isPost()){
    		$mMedicoPlano = new Model_Corporativo_MedicoPlano;
    		$rgetPlanosMedico = $mMedicoPlano->getPlanosMedico($this->_request->getPost());
    		$this->_helper->layout->disableLayout();
    		$this->getHelper('viewRenderer')->setNoRender();
    		$this->getResponse()->setBody(json_encode(array('resposta' => $rgetPlanosMedico->toArray())));
    	}
    }
    
}