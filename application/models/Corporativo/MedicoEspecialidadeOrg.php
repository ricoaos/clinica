<?php

class Model_Corporativo_MedicoEspecialidade extends Zend_Db_Table 
{
	protected $_schema  = 'corporativo';
	protected $_name    = 'medico_especialidade';
	protected $_primary = array('id_especialidade', 'id_medico');
}