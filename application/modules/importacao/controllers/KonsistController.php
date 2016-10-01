<?php
set_time_limit(0);
error_reporting(E_ALL);
ini_set('display_errors', 1);

class Importacao_KonsistController extends App_Controller_Action
{

    public function init()
    {
        $this->dbAntigo = new Zend_Db_Adapter_Pdo_Pgsql(array('host' => 'localhost', 'username' => 'postgres', 'password' => 'x', 'dbname' => 'temp2'));
        //$this->dbNovo   = new Zend_Db_Adapter_Pdo_Pgsql(array('host' => 'srv.xge.com.br', 'username' => 'onclinica_dev', 'password' => 'd3vClinica', 'dbname' => 'onclinica_dev'));
        $this->dbNovo   = new Zend_Db_Adapter_Pdo_Pgsql(array('host' => 'localhost', 'username' => 'postgres', 'password' => 'x', 'dbname' => 'onclinica_dev'));
    }

    public function clienteAction()
    {
        $tbAntigo   = new Zend_Db_Table(array('db' => $this->dbAntigo, 'name' => 'arq_paciente'));
        $tbNovo     = new Zend_Db_Table(array('db' => $this->dbNovo, 'name' => 'entidade_pf', 'schema' => 'corporativo', 'primary' => 'id_entidade'));

        // Conta quantos registros tem porque estoura a memória
        $qtTotal = $this->dbAntigo->query('select count(0) as qt from arq_paciente')->fetchColumn();
        $qtRegistrosPorInteracao = 2000;
        $qtInteracoes = ceil($qtTotal / $qtRegistrosPorInteracao);

        $aDicionario = array(
            'x_id'      => 'cod_paciente',
            'st_nome'   => 'nom_paciente',
            'st_cpf'    => 'num_cpf',
        );

        $inicio = 0;
        for ($i=0; $i <= $qtInteracoes; $i++) {
            $rowSetAntigo = $tbAntigo->fetchAll(null, 'cod_paciente' , $qtRegistrosPorInteracao, $inicio);
            $inicio += $qtRegistrosPorInteracao;
            foreach ($rowSetAntigo as $rowAntigo) {
                $rowNovo = $this->_prepararRow($rowAntigo, $tbNovo->createRow(), $aDicionario);
                $rowNovo->st_nome = trim($rowAntigo->nom_paciente);
                $rowNovo->st_nome_sondex = soundex($rowNovo->st_nome);

                if (empty($rowNovo->dt_cadastro)) {
                    $rowNovo->dt_cadastro = date('Y-m-d');
                }
                $rowNovo->save();
            }

        }
    }

    public function medicoAction()
    {
        $tbAntigo   = new Zend_Db_Table(array('db' => $this->dbAntigo, 'name' => 'arq_medico'));
        $tbNovoMed  = new Zend_Db_Table(array('db' => $this->dbNovo, 'name' => 'medico', 'schema' => 'corporativo', 'primary' => 'id_medico'));
        $tbNovoEnt  = new Zend_Db_Table(array('db' => $this->dbNovo, 'name' => 'entidade_pf', 'schema' => 'corporativo', 'primary' => 'id_entidade'));

        // Conta quantos registros tem porque estoura a memória
        $qtTotal = $this->dbAntigo->query('select count(0) as qt from arq_medico')->fetchColumn();
        $qtRegistrosPorInteracao = 2000;
        $qtInteracoes = ceil($qtTotal / $qtRegistrosPorInteracao);

        $aDicionario = array(
            'x_med_id'  => 'id_medico',
            'st_nome'   => 'nom_medico',
            'st_cpf'    => 'num_cpf',
        );

        $inicio = 0;
        for ($i=0; $i <= $qtInteracoes; $i++) {
            $rowSetAntigo = $tbAntigo->fetchAll(null, 'id_medico' , $qtRegistrosPorInteracao, $inicio);
            $inicio += $qtRegistrosPorInteracao;
            foreach ($rowSetAntigo as $rowAntigo) {
                $rowNovo = $this->_prepararRow($rowAntigo, $tbNovoEnt->createRow(), $aDicionario);
                $rowNovo->st_nome = trim($rowAntigo->nom_medico);
                $rowNovo->st_nome_sondex    = soundex($rowNovo->st_nome);
                $rowNovo->st_nome_metaphone = metaphone($rowNovo->st_nome);

                if (empty($rowNovo->dt_cadastro)) {
                    $rowNovo->dt_cadastro = date('Y-m-d');
                }
                $rowNovo->save();

                $rNovoMedico = $tbNovoMed->createRow();
                $rNovoMedico->id_medico = $rowNovo->id_entidade;
                $rNovoMedico->st_crm    = $rowAntigo->num_crm;
                $rNovoMedico->x_med_id  = $rowAntigo->id_medico;
                $rNovoMedico->save();
            }

        }
    }

    public function consultaAction()
    {
        $tbAntigo   = new Zend_Db_Table(array('db' => $this->dbAntigo, 'name' => 'arq_agendal'));
        $tbNovo     = new Zend_Db_Table(array('db' => $this->dbNovo, 'name' => 'consulta', 'schema' => 'consulta', 'primary' => 'id_consulta'));

        $aMedicos = array();
        $rowSet = $this->dbNovo->select()->from('corporativo.medico')->query()->fetchAll();
        foreach ($rowSet as $row) {
            $aMedicos[$row['x_med_id']] = $row['id_medico'];
        }

        $aProcedimentos = array();
        $rowSet = $this->dbNovo->select()->from('corporativo.procedimento')->query()->fetchAll();
        foreach ($rowSet as $row) {
            $aProcedimentos[$row['st_codigo']] = $row['id_procedimento'];
        }
        unset($rowSet, $row);

        // carregado dinamicamente
        $aPacientes = array();

        // Conta quantos registros tem porque estoura a memória
        $qtTotal = $this->dbAntigo->query('select count(0) as qt from arq_agendal where cod_paciente is not null')->fetchColumn();
        $qtRegistrosPorInteracao = 2000;
        $qtInteracoes = ceil($qtTotal / $qtRegistrosPorInteracao);

        $aDicionario = array(
            'x_id'      => 'chave',
            'st_observacao' => 'mem_obs',
            'dt_agendamento' => 'dat_marcacao',
        );

        $tbPacienteNovo = new Zend_Db_Table(array('db' => $this->dbNovo, 'name' => 'entidade_pf', 'schema' => 'corporativo', 'primary' => 'id_entidade'));

        $inicio = 0;
        for ($i=0; $i <= $qtInteracoes; $i++) {
            $rowSetAntigo = $tbAntigo->fetchAll(array('cod_paciente is not null'), 'chave' , $qtRegistrosPorInteracao, $inicio);

            //id_medico
            //cod_paciente
            // id_convenio
            //des_hora
            // dat_marcacao
            // dat_agenda
            $inicio += $qtRegistrosPorInteracao;
            foreach ($rowSetAntigo as $rowAntigo) {
                $rowNovo = $this->_prepararRow($rowAntigo, $tbNovo->createRow(), $aDicionario);
                $rowNovo->id_organizacao = 1;
                $rowNovo->cs_situacao    = '?';
                $rowNovo->st_observacao  = trim(utf8_encode($rowNovo->st_observacao));

                if (!isset($aPacientes[$rowAntigo->cod_paciente])) {
                    $idEntidade = $tbPacienteNovo->fetchRow(array('x_id = ' . $rowAntigo->cod_paciente))->id_entidade;
                    $aPacientes[$rowAntigo->cod_paciente] = $idEntidade;
                }
                $rowNovo->id_paciente     = $aPacientes[$rowAntigo->cod_paciente];
                if (isset($aProcedimentos[$rowAntigo->cod_procedimento])) {
                    $rowNovo->id_procedimento = $aProcedimentos[$rowAntigo->cod_procedimento];
                }
                $rowNovo->id_medico       = $aMedicos[$rowAntigo->id_medico];

                if (empty($rowNovo->dt_cadastro)) {
                    $rowNovo->dt_cadastro = date('Y-m-d');
                }
                $rowNovo->save();
            }
        }
die;
    }

    private function _prepararRow($rowAntigo, Zend_Db_Table_Row $rowNovo, array $aDicionario)
    {
        foreach ($aDicionario as $campoNovo => $campoAntigo) {
            if (empty($campoAntigo)) {
                continue;
            }
            $rowNovo->$campoNovo = $rowAntigo->$campoAntigo;
        }
        return $rowNovo;
    }
}
