<?php

class Model_Corporativo_Organizacao extends Zend_Db_Table 
{
	protected $_schema  = 'corporativo';
	protected $_name    = 'organizacao';
	protected $_primary = array('id_organizacao');
}