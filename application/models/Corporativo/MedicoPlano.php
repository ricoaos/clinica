<?php

class Model_Corporativo_MedicoPlano extends Zend_Db_Table
{
	protected $_schema  = 'corporativo';
	protected $_name    = 'medico_plano';
	protected $_primary = array('idmedico', 'idplano');
	
	/**
	* Busca Convenios do(s) medico(s)
	* @author Weidman Ferreira
	* @version 26/10/2015
	* 
	* @param (array) especialidade, medico, procedimento
	**/
	
	public function getConveniosMedico($parms = null){
		$mTbConvenio = new Model_Corporativo_MedicoPlano;
		$select = $mTbConvenio->select()->setIntegrityCheck(false)->distinct()
			->from(array('mp' => 'corporativo.medico_plano'), array())
			->join(array('tpl' => 'apoio.tb_plano'), 'tpl.idplano=mp.idplano',array())
			->join(array('tc' => 'apoio.tb_convenio'), 'tc.idconvenio=tpl.idconvenio',array('idconvenio','nome as nmConvenio'))
			->join(array('pc' => 'corporativo.medico_procedimento'), 'pc.idmedico = mp.idmedico',array())
			->join(array('tp'  => 'apoio.tb_procedimento'),'tp.idprocedimento = pc.idprocedimento', array())
			->join(array('me' => 'corporativo.medico_especialidade'),'me.idmedico = mp.idmedico', array())
			->join(array('pf' => 'corporativo.entidade_pf'), 'pf.id_entidade = mp.idmedico', array())
			->join(array('ma' => 'corporativo.medico_agenda'), 'ma.idmedico = mp.idmedico', array())
			->join(array('om' => 'corporativo.organizacao_medico'), 'om.idmedico = mp.idmedico', array())
			->where('om.idorganizacao = ?', App_Identity::getOrganizacao())
			->where('ma.ativo = ?', '1');
			if (isset($parms['idespecialidade']) and $parms['idespecialidade'] != null) $select->where('me.idespecialidade = ?', $parms['idespecialidade']);
			if (isset($parms['idmedico']) and $parms['idmedico'] != null) $select->where('mp.idmedico = ?', $parms['idmedico']);
			//if (isset($parms['id_procedimento']) and $parms['id_procedimento'] != null) $select->where('pc.idprocedimento = ?', $parms['id_procedimento']);
		return $mTbConvenio->fetchAll($select);
	}
	
	/**
	* Busca Planos do medico/medicos
	* @author Weidman Ferreira
	* @version 15/10/2015
	* 
	* @param (array) convenio, medico
	**/
	
	public function getPlanosMedico($parms = null){
		$mTbConvenio = new Model_Corporativo_MedicoPlano;
		$select = $mTbConvenio->select()->setIntegrityCheck(false)->distinct()
		->from(array('mp' => 'corporativo.medico_plano'), array('idplano'))
		->join(array('tpl' => 'apoio.tb_plano'), 'tpl.idplano=mp.idplano',array('nome as mnPlano'))
		->join(array('tc' => 'apoio.tb_convenio'), 'tc.idconvenio=tpl.idconvenio',array())
		->join(array('pc' => 'corporativo.medico_procedimento'), 'pc.idmedico = mp.idmedico',array())
		->join(array('tp'  => 'apoio.tb_procedimento'),'tp.idprocedimento = pc.idprocedimento', array())
		->join(array('me' => 'corporativo.medico_especialidade'),'me.idmedico = mp.idmedico', array())
		->join(array('pf' => 'corporativo.entidade_pf'), 'pf.id_entidade = mp.idmedico', array())
		->join(array('ma' => 'corporativo.medico_agenda'), 'ma.idmedico = mp.idmedico', array())
		->join(array('om' => 'corporativo.organizacao_medico'), 'om.idmedico = mp.idmedico', array())
		->where('om.idorganizacao = ?', App_Identity::getOrganizacao())
		->where('ma.ativo = ?', '1');
		
		if (isset($parms['idconvenio']) and $parms['idconvenio'] != null) $select->where('tpl.idconvenio = ?', $parms['idconvenio']);
		if (isset($parms['idmedico']) and $parms['idmedico'] != null) $select->where('mp.idmedico = ?', $parms['idmedico']);
		if (isset($parms['idespecialidade']) and $parms['idespecialidade'] != null) $select->where('me.idespecialidade = ?', $parms['idespecialidade']);
		if (isset($parms['idprocedimento']) and $parms['idprocedimento'] != null) $select->where('pc.idprocedimento = ?', $parms['idprocedimento']);
		
		$select->group('mp.idplano')
			->group('tpl.nome');
		return $mTbConvenio->fetchAll($select);
	}

}