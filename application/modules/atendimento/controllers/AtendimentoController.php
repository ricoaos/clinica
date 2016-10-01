<?php

class Atendimento_AtendimentoController extends App_Controller_Action{
    
    private $db;
    
    public function init(){}
    
    public function cadastroAction(){
        
        // Dados de consulta
        $dadosConsulta = array(
            'evento' =>array(
                'entidade'  =>  array(
                    'nome'  =>  'Clínica CED',
                    'expedienteInicial' =>  '06:00',
                    'expedienteFinal'   =>  '18:00'                    
                ),
                'medicos'   =>  array(
                    array(
                        'nome'  =>  'Júlio Andrade Neto',
                        'configuracoes' =>  array(
                            'duracaoProcedimento'  =>  (int)20,
                            'diasDaSemana'  =>  array('4'),
                            'especialidade' =>  'Clinica Medica',
                            'expedienteInicial' =>  '07:00',
                            'expedienteFinal'   =>  '10:00'
                        ),                     
                    // Atendimentos                    
                    'atendimentos'  =>  array(
                        // Atendimento 1
                        '08:00' => array(
                            'data'  =>  '10/10/2015',
                            'hora'  =>  '10:20',
                            'tipo'  =>  '2',
                            'status'=>  array(
                                'id'    =>  2,
                                'nome'  =>  'Confirmado'
                            ),                                 
                            'obs'   =>  'Paciente Desmaiando', 
                            'cliente'   =>  array(
                                'id'    =>  50,
                                'nome'  => 'Marcela dos Santos',
                                'email' =>  'marcelasantos@gmail.com',
                                'convenio'  =>  array(
                                    'id'    =>  56,
                                    'nome'  =>  'Amil'
                                ),
                                'plano'  =>  array(
                                    'id'    =>  87,
                                    'nome'  =>  'Amil 500'
                                )
                            ),
                            'procedimento'  =>  array(
                                'id'    =>  34,
                                'nome'  =>  'Consulta Médica',
                                'valor' =>  '100,00'
                            )                            
                        ),
                        // Atendimento 2
                        '08:40' => array(
                            'data'  =>  '10/10/2015',
                            'hora'  =>  '07:20',
                            'tipo'  =>  '2',
                            'status'=>  array(
                                'id'    =>  2,
                                'nome'  =>  'Confirmado'
                            ),
                            'obs'   =>  'Paciente Dores',
                            'cliente'   =>  array(
                                'id'    =>  54,
                                'nome'  => 'Junior Maia Seixas',
                                'email' =>  'juniorseixas@gmail.com',
                                'convenio'  =>  array(
                                    'id'    =>  56,
                                    'nome'  =>  'Blue Life'
                                ),
                                'plano'  =>  array(
                                    'id'    =>  20,
                                    'nome'  =>  'Blue 500'
                                )
                            ),
                            'procedimento'  =>  array(
                                'id'    =>  34,
                                'nome'  =>  'Consulta Médica',
                                'valor' =>  '100,00'
                            )
                        ), 

                        // Atendimento 3
                        '09:20' => array(
                            'data'  =>  '10/10/2015',
                            'hora'  =>  '07:30',
                            'tipo'  =>  '2',
                            'status'=>  array(
                                'id'    =>  2,
                                'nome'  =>  'Confirmado'
                            ),
                            'obs'   =>  'Paciente Dores',
                            'cliente'   =>  array(
                                'id'    =>  59,
                                'nome'  => 'Amanda Figueira',
                                'email' =>  'amandafigueira@gmail.com',
                                'convenio'  =>  array(
                                    'id'    =>  56,
                                    'nome'  =>  'Blue Life'
                                ),
                                'plano'  =>  array(
                                    'id'    =>  20,
                                    'nome'  =>  'Blue 500'
                                )
                            ),
                            'procedimento'  =>  array(
                                'id'    =>  34,
                                'nome'  =>  'Consulta Médica',
                                'valor' =>  '100,00'
                            )
                        ),

                        // Atendimento 4
                        '10:00' => array(
                            'data'  =>  '10/10/2015',
                            'hora'  =>  '09:00',
                            'tipo'  =>  '2',
                            'status'=>  array(
                                'id'    =>  2,
                                'nome'  =>  'Agendado'
                            ),
                            'obs'   =>  'Paciente com Dores',
                            'cliente'   =>  array(
                                'id'    =>  58,
                                'nome'  => 'Bill Gates',
                                'email' =>  'billgates@gmail.com',
                                'convenio'  =>  array(
                                    'id'    =>  56,
                                    'nome'  =>  'Blue Life'
                                ),
                                'plano'  =>  array(
                                    'id'    =>  20,
                                    'nome'  =>  'Blue 500'
                                )
                            ),
                            'procedimento'  =>  array(
                                'id'    =>  34,
                                'nome'  =>  'Eco-Color Doppler',
                                'valor' =>  '100,00'
                                )
                            )
                        )
                    )
                )
            )
        );
        
        $this->view->dadosConsulta = $dadosConsulta;
    }
    
}