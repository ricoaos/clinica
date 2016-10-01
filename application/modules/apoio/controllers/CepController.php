<?php

class Apoio_CepController extends App_Controller_Action
{
	public function getlogbycepAction()
	{
		$this->mVwCep = new Model_Cep_VwCep();
		$cep = $this->_request->getPost();
		$rsLogradouro = $this->mVwCep->fetchAll(array('cep = ?'=>$cep))->toArray();
		$result = array();
		foreach ($rsLogradouro as $dados)
		{			
			$complemento = empty($dados['st_nome_complemento']) ? '' : $dados['st_nome_complemento'];
			$result=array(
				'estado'     => $dados['estado'],
				'siglaEstado'=> $dados['sigla'],
				'cidade'     => $dados['cidade'],
				'bairro'     => $dados['bairro'],
				'logradouro' => $dados['logradouro'].' '.$complemento,
				'tipologradouro' => $dados['tipo'],
				'municipio'  => $dados['id_municipio']
			);
		}
		
		$this->_helper->layout->disableLayout();
		$this->getHelper('viewRenderer')->setNoRender();
		$this->getResponse()->setBody(json_encode(array('result' => $result)));
	}
}