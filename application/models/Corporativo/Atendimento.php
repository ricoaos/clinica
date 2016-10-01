<?php

class Model_Corporativo_Atendimento extends Zend_Db_Table
{
	protected $_schema  = 'corporativo';
	protected $_name    = 'atendimento';
	protected $_primary = array('id_atendimento');
	
	/**
	 * @abstract getAtendimentosByParams
	 * @author Weidman Ferreira
	 * @version 27/10/2015
	 *
	 * Busca Consultas Agendadas Por Parametros
	 * @param (array) $params
	 *
	 **/
	
	public function getAtendimentosByParams($params = null){
		
		$mAtendimento = new Model_Corporativo_Atendimento;
		$select = $mAtendimento->select()->setIntegrityCheck(false)->distinct()
			->from(array('ma' => 'corporativo.atendimento'), array("to_char(ma.dataatendimento,'DD/MM/YYYY') as data", "to_char(ma.dataatendimento,'HH:MI') as hora", "ma.entidade_pf", "ma.status", "ma.observacao"))
			->join(array('te' => 'apoio.tb_especialidade'),'ma.id_especialidade = ma.id_especialidade', array())
			->join(array('tp' => 'apoio.tb_procedimento'),'tp.id_procedimento = ma.id_procedimento', array('tp.nome as nmProcedimento', 'tp.id_procedimento'))
			->join(array('pl' => 'apoio.tb_plano'), 'pl.id_plano = ma.id_plano', array('pl.nome as nmPlano', 'ma.id_plano'))
			->join(array('pf' => 'corporativo.entidade_pf'), 'pf.id_entidade = ma.id_medico', array('st_nome as nmMedico'))
			->join(array('pf1'=> 'corporativo.entidade_pf'), 'pf1.id_entidade = ma.entidade_pf', array('st_nome as nmCliente'))
			->join(array('mp' => 'corporativo.medico_plano'), 'mp.id_plano = ma.id_plano', array())
			->join(array('pa' => 'corporativo.medico_periodoatendimento'), 'pa.id_medico = ma.id_medico', array())
			->join(array('om' => 'corporativo.organizacao_medico'), 'om.id_medico = ma.id_medico', array('om.id_organizacao'))
			->join(array('oh' => 'corporativo.organizacao_horario'), 'oh.id_organizacao = om.id_organizacao', array())
			->where(sprintf("'%s' BETWEEN pa.datainicial AND pa.datafinal", $params['data']))
			->where('om.id_organizacao = ?', App_Identity::getOrganizacao())
			->where('ma.ativo = ?', '1');
			
			if(isset($params['data']) and $params['data'] != null) $select->where("to_char(ma.dataatendimento, 'YYYY-MM-DD') = ?", $params['data']);
			if(isset($params['idmedico']) and $params['idmedico'] != null) $select->where("ma.idmedico = ?", $params['idmedico']);
			
		return $mAtendimento->fetchAll($select);
	}
}