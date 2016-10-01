<?php

class Model_Usuario_UsuarioPerfil2 extends Zend_Db_Table 
{	
    protected $_schema  = 'usuario';
    protected $_name    = 'usuario_perfil';
    protected $_primary = array('id_perfil', 'id_usuario', 'id_organizacao');    
}