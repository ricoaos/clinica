<?php

class Model_Corporativo_VwMedico extends Zend_Db_Table 
{
	protected $_schema  = 'corporativo';
	protected $_name    = 'vw_medico';
	protected $_primary = array('idorganizacao', 'id_entidade');
}