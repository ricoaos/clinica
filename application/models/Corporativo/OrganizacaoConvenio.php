<?php

class Model_Corporativo_OrganizacaoConvenio extends Zend_Db_Table 
{
	protected $_schema  = 'corporativo';
	protected $_name    = 'organizacao_convenio';
	protected $_primary = array( 'id_organizacao', 'id_convenio');
}