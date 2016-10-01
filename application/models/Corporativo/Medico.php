<?php

class Model_Corporativo_Medico extends Zend_Db_Table 
{
	protected $_schema  = 'corporativo';
	protected $_name    = 'medico';
	protected $_primary = array('id_medico');
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 */
	public function getDataMedico($params)
	{	
	    $mEntidade = new Model_Corporativo_EntidadePf;
	    $select = $mEntidade->select()->setIntegrityCheck(false)
	    ->from(array('pf' => 'corporativo.entidade_pf'), array("*"))
	    ->joinleft(array('md' => 'corporativo.medico'),'md.id_medico = pf.id_entidade', array('md.st_crm','md.st_tipo_conselho','id_estado','id_ativo'))
	    ->where("pf.id_entidade = ?", $params);
	    return $mEntidade->fetchAll($select)->toArray();
	}
}