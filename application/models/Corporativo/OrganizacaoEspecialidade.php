<?php

class Model_Corporativo_OrganizacaoEspecialidade extends Zend_Db_Table
{
	protected $_schema  = 'corporativo';
	protected $_name    = 'organizacao_especialidade';
	protected $_primary = array( 'id_especialidade', 'id_organizacao');
}