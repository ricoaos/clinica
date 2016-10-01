<?php
class Model_Consulta_Paciente extends Zend_Db_Table
{
    protected $_schema = 'consulta';
    protected $_name   = 'paciente';
    protected $_primary = array('id_paciente', 'id_organizacao');
    
    public function getDataPaciente($params)
    {
        $mEntidade = new Model_Corporativo_EntidadePf;
        $select = $mEntidade->select()->setIntegrityCheck(false)
        ->from(array('pf' => 'corporativo.entidade_pf'), array("*"))
        ->joinLeft(array('pc' => 'consulta.paciente'),'pc.id_paciente = pf.id_entidade')
        ->where("pf.id_entidade = ?", $params);
        return $mEntidade->fetchAll($select)->toArray();
    }
}