<?php

class Model_Sistema_Perfil extends Zend_Db_Table 
{	
    protected $_schema  = 'usuario';
    protected $_name    = 'perfil';
    protected $_primary = array('id_perfil');    
}

