<?php

class Model_Corporativo_MedicoConvenio extends Zend_Db_Table
{
	protected $_schema  = 'corporativo';
	protected $_name    = 'medico_convenio';
	protected $_primary = array('id_medico', 'id_convenio');

	/**
	* @abstract getConveniosMedicos
	* @author Weidman Ferreira
	* @version 15/10/2015
	*
	* Busca os convenios dos Medicos
	* @param (array) especialidade, medico, procedimento
	*
	**/
	
	public function getConveniosMedicos($parms = null){
		$mMedicoConvenio = new Model_Corporativo_MedicoConvenio;
			$select = $mMedicoConvenio->select()->setIntegrityCheck(false)->distinct()
			->from(array('mc' => 'corporativo.medico_convenio'), array('id_medico', 'id_convenio'))
			->join(array('cv' => 'corporativo.convenio'), 'cv.id_convenio=mc.id_convenio',array('st_nome as nmConvenio'))
			->join(array('mp' => 'corporativo.medico_procedimento'), 'mp.id_medico = mc.id_medico',array())
			->join(array('p'  => 'corporativo.procedimento'),'p.id_procedimento = mp.id_procedimento', array())
			->join(array('me' => 'corporativo.medico_especialidade'),'me.id_medico = mc.id_medico', array())
			->join(array('pf' => 'corporativo.entidade_pf'), 'pf.id_entidade = mc.id_medico', array('st_nome as nmMedico'))
			->join(array('mh' => 'corporativo.medico_horario'), 'mh.id_medico = mc.id_medico', array())
			->join(array('om' => 'corporativo.organizacao_medico'), 'om.idmedico = mc.idmedico', array())
			->where('om.idorganizacao = ?', App_Identity::getOrganizacao())
			->where('mh.status = ?', 1)
			->where('mc.status =?', 1);
		if (isset($parms['idespecialidade']) and $parms['idespecialidade'] != null) $select->where('me.idespecialidade = ?', $parms['idespecialidade']);
		if (isset($parms['idmedico']) and $parms['idmedico'] != null) $select->where('mc.idmedico = ?', $parms['idmedico']);
		//if (isset($parms['idprocedimento']) and $parms['idprocedimento'] != null) $select->where('mh.status = ?', $parms['idprocedimento']);
		
		return $mMedicoConvenio->fetchAll($select);
	}
	
}