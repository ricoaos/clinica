<?php

class Model_Corporativo_MedicoProcedimento extends Zend_Db_Table 
{
	protected $_schema  = 'corporativo';
	protected $_name    = 'medico_procedimento';
	protected $_primary = array('idprocedimento','idmedico');

	/**
	* @abstract getProcedimentosMedico
	* @author Weidman Ferreira
	* @version 15/10/2015
	*
	* Busca os procedimentos dos Medicos
	* @param (string) idmedico
	*
	**/
	
	public function getProcedimentosMedico($dados = null){
		$mMedicoProcedimento = new Model_Corporativo_MedicoProcedimento;
		$select = $mMedicoProcedimento->select()->setIntegrityCheck(false)
			->distinct()
			->from(array('mp' => 'corporativo.medico_procedimento'), array())
			->join(array('tp'  => 'apoio.tb_procedimento'),'tp.idprocedimento = mp.idprocedimento', array('nome as nmProcedimento', 'idprocedimento'))
			->join(array('me' => 'corporativo.medico_especialidade'),'me.idmedico = mp.idmedico', array())
			->join(array('pf' => 'corporativo.entidade_pf'), 'pf.id_entidade = mp.idmedico', array())
			->join(array('ma' => 'corporativo.medico_agenda'), 'ma.idmedico = me.idmedico', array())
			->join(array('tpl' => 'corporativo.medico_plano'), 'tpl.idmedico = me.idmedico', array())
			->group(array('tp.idprocedimento', 'mp.idmedico', 'pf.st_nome'))
			->join(array('om' => 'corporativo.organizacao_medico'), 'om.idmedico = mp.idmedico', array())
			->where('om.idorganizacao = ?', App_Identity::getOrganizacao())
			->where('mp.ativo = ?', '1')
			->where('me.ativo = ?', '1')
			->where('ma.ativo = ?', '1')
			->where('pf.x_med_id is not null');
			if (isset($dados['idmedico']) and $dados['idmedico'] != null) $select->where('mp.idmedico = ?', $dados['idmedico']);
			if (isset($dados['idespecialidade']) and $dados['idespecialidade'] != null) $select->where('me.idespecialidade = ?', $dados['idespecialidade']);
		return $mMedicoProcedimento->fetchAll($select);
	}

}