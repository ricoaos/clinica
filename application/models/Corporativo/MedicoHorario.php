<?php

class Model_Corporativo_MedicoHorario extends Zend_Db_Table 
{	
    protected $_schema  = 'corporativo';
    protected $_name    = 'medico_horario';
    protected $_primary = array('id_medico_horario');    
}