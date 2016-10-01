<?php

class Model_Sistema_UsuarioPerfil3 extends Zend_Db_Table 
{	
    protected $_schema  = 'usuario';
    protected $_name    = 'usuario_perfil';
    protected $_primary = array('id_perfil', 'id_usuario', 'id_organizacao');    
}