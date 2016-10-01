<?php

class Model_Corporativo_Procedimento extends Zend_Db_Table 
{	
    protected $_schema  = 'corporativo';
    protected $_name    = 'procedimento';
    protected $_primary = array('id_procedimento');    
}