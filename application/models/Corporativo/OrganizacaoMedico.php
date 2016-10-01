<?php

class Model_Corporativo_OrganizacaoMedico extends Zend_Db_Table 
{
	protected $_schema  = 'corporativo';
	protected $_name    = 'organizacao_medico';
	protected $_primary = array('id_organizacao', 'idmedico');
}