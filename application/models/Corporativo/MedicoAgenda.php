<?php

class Model_Corporativo_MedicoAgenda extends Zend_Db_Table
{
	protected $_schema  = 'corporativo';
	protected $_name    = 'medico_agenda';
	protected $_primary = array('id_medico_agenda');
	
	/**
	 * @abstract getMedicosAgendaByData
	 * @author Weidman Ferreira
	 * @version 27/10/2015
	 *
	 * Busca Agenda dos Medicos
	 * @param (array) $params
	 *
	 **/
	
	public function getMedicosAgendaByData($params = null){
		$mMedicoEspecialidade = new Model_Corporativo_MedicoEspecialidade;
		$select = $mMedicoEspecialidade->select()->setIntegrityCheck(false)->distinct()
    		->from(array('me'  => 'corporativo.medico_especialidade'), array('idespecialidade','idmedico'))
    		->join(array('te'  => 'apoio.tb_especialidade'),'me.idespecialidade = te.idespecialidade', array('nome as nmEspecialidade'))
     		->join(array('pc' => 'corporativo.medico_procedimento'), 'pc.idmedico = me.idmedico',array('pc.duracao_atendimento'))
     		->join(array('tp'  => 'apoio.tb_procedimento'),'tp.idprocedimento = pc.idprocedimento', array('tp.nome as nmProcedimento'))
     		->join(array('pf' => 'corporativo.entidade_pf'), 'pf.id_entidade = me.idmedico', array('st_nome as nmMedico'))
    		->join(array('pa' => 'corporativo.medico_periodoatendimento'), 'me.idmedico = pa.idmedico', array())
    		->join(array('ma' => 'corporativo.medico_agenda'), 'ma.idmedico = me.idmedico', array())
    		->join(array('mp' => 'corporativo.medico_plano'), 'mp.idmedico = me.idmedico', array())
    		->join(array('om' => 'corporativo.organizacao_medico'), 'om.idmedico = me.idmedico', array('om.idorganizacao'))
    		->join(array('o'  => 'corporativo.organizacao'), 'o.idorganizacao = om.idorganizacao', array('o.st_nome_fantasia as nmOrganizacao'))
     		->join(array('oh' => 'corporativo.organizacao_horario'), 'oh.idorganizacao = om.idorganizacao', array('expediente_inicial', 'expediente_final'))
     		->where(sprintf("'%s' BETWEEN to_char(pa.datainicial,'YYYY-MM-DD') AND to_char(pa.datafinal, 'YYYY-MM-DD')", $params['data']))
     		->where('om.idorganizacao = ?', App_Identity::getOrganizacao())
     		->where('me.ativo = ?', '1')
    		->where('pa.ativo = ?', '1')
    		->where('ma.ativo = ?', '1')
    		->where('pf.x_med_id is not null');
    
    		if(isset($params['idprocedimento']) and $params['idprocedimento'] != null) $select->where('pc.idprocedimento = ?', $params['idprocedimento']);
     		if(isset($params['idmedico']) and $params['idmedico'] != null) $select->where('me.idmedico = ?', $params['idmedico']);
     		if(isset($params['diasemana']) and $params['diasemana'] != null) $select->where('ma.diasemana = ?', $params['diasemana']);
     		if(isset($params['idplano']) and $params['idplano'] != null) $select->where('mp.idplano = ?', $params['idplano']);
    		
		return $mMedicoEspecialidade->fetchAll($select);
	}
	
	/**
	 * Busca Agendas dos medicos
	 * 
	 **/

	public function getAgendaByMedico($params = null){
	    $mMedicoAgenda = new Model_Corporativo_MedicoAgenda;
	    $select = $mMedicoAgenda->select()->setIntegrityCheck(false)->distinct()
            ->from(array('ma' => 'corporativo.medico_agenda', array('horainicial', 'horafinal', 'diasemana')))
            ->join(array('om' => 'corporativo.organizacao_medico'), 'om.idmedico = ma.idmedico', array())
			->where('om.id_organizacao = ?', App_Identity::getOrganizacao())
            ->where('ma.ativo = ?', '1')
            ->order('ma.horainicial');
	    
	    if(isset($params['idmedico']) and $params['idmedico'] != null) $select->where('ma.idmedico = ?', $params['idmedico']);
	    if(isset($params['diasemana']) and $params['diasemana'] != null) $select->where('ma.diasemana = ?', $params['diasemana']);
	    return $mMedicoAgenda->fetchAll($select);
	}
}