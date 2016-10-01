<?php

class Model_Corporativo_MedicoEspecialidade extends Zend_Db_Table
{
	protected $_schema  = 'corporativo';
	protected $_name    = 'medico_especialidade';
	protected $_primary = array('idmedico', 'idespecialidade');
	
	/**
	* @abstract getEspecialidadesMedicosAtivos
	* @author Weidman Ferreira
	* @version 15/10/2015
	* 
	* Busca Especialidades do(s) Medico(s)
	* @param (array) medico
	* 
	**/
	
	public function getEspecialidadesMedicosAtivos($id_medico = null){
		$mMedicoEspecialidade = new Model_Corporativo_MedicoEspecialidade;
		$select = $mMedicoEspecialidade->select()->setIntegrityCheck(false)->distinct()
			->from(array('me'  => 'corporativo.medico_especialidade'), array('idespecialidade'))
			->join(array('te'  => 'apoio.tb_especialidade'),'me.idespecialidade = te.idespecialidade', array('nome as nmEspecialidade'))
			->join(array('pf' => 'corporativo.entidade_pf'), 'pf.id_entidade = me.idmedico', array())
			->join(array('pa' => 'corporativo.medico_periodoatendimento'), 'me.idmedico = pa.idmedico', array())
			->join(array('ma' => 'corporativo.medico_agenda'), 'ma.idmedico = me.idmedico', array())
			->join(array('mp' => 'corporativo.medico_plano'), 'mp.idmedico = me.idmedico', array())
			->join(array('om' => 'corporativo.organizacao_medico'), 'om.idmedico = me.idmedico', array())
			->where('om.idorganizacao = ?', App_Identity::getOrganizacao())
			->where('CURRENT_DATE BETWEEN pa.datainicial AND pa.datafinal')
			->where('me.ativo = ?', '1')
			->where('pa.ativo = ?', '1')
			->where('ma.ativo = ?', '1')
			->where('pf.x_med_id is not null');
			if($id_medico) $select->where('me.idmedico = ?', $id_medico);
		return $mMedicoEspecialidade->fetchAll($select);
	}

	/**
	* @abstract getMedicosAgendaOpen
	* @author Weidman Ferreira
	* @version 15/10/2015
	*
	* Busca agenda aberta dos Medicos
	*
	**/
	
	public function getMedicosAgendaOpen(){
		$mEspecialidade = new Model_Corporativo_MedicoEspecialidade;
		$select = $mEspecialidade->select()
			->setIntegrityCheck(false)->distinct()
			->from(array('me'  => 'corporativo.medico_especialidade'), array())
			->join(array('te'  => 'apoio.tb_especialidade'),'me.idespecialidade = te.idespecialidade', array())
			->join(array('pf' => 'corporativo.entidade_pf'), 'pf.id_entidade = me.idmedico', array('st_nome as nmMedico', 'id_entidade as idmedico'))
			->join(array('pa' => 'corporativo.medico_periodoatendimento'), 'me.idmedico = pa.idmedico', array())
			->join(array('ma' => 'corporativo.medico_agenda'), 'ma.idmedico = me.idmedico', array())
			->join(array('mp' => 'corporativo.medico_plano'), 'mp.idmedico = me.idmedico', array())
			->join(array('om' => 'corporativo.organizacao_medico'), 'om.idmedico = me.idmedico', array())
			->where('om.idorganizacao = ?', App_Identity::getOrganizacao())
			->where('CURRENT_DATE BETWEEN pa.datainicial AND pa.datafinal')
			->where('me.ativo = ?', '1')
			->where('pa.ativo = ?', '1')
			->where('ma.ativo = ?', '1')
			->where('pf.x_med_id is not null');
		return $mEspecialidade->fetchAll($select);
	}

}