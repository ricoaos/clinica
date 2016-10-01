<?php

class Model_Consulta_VwPaciente extends Zend_Db_Table
{
    protected $_schema = 'consulta';
    protected $_name   = 'vw_paciente';
    protected $_primary = array('id_organizacao', 'id_entidade');
    
    function getPacienteByParams(){
        if ($this->_request->getPost()){
            $this->_helper->layout->disableLayout();
            $this->getHelper('viewRenderer')->setNoRender();
                        
        }
    }
}
