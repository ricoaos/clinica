<?php

/**
 * @abstract Monta Agenda
 * @author Weidman Ferreira
 * @access 20/10/2015
 * 
 * @param id 		: Id do Elemento input
 * 
 * @example 		: $this->Agenda(array('id'=>'dtInicio', 'size'=>'50', 'class'=>'required', 'format'=>'yy-mm-dd', 'value'=>'19/07/2012', 'mostradatavencida' => 0))
 * 
 */

class App_View_Helper_Agenda extends Zend_View_Helper_Abstract
{ 
	public function agenda ($dadosConsulta)
	{
		if ($dadosConsulta):

			echo "<div id='dados' name='dados'></div> <div class='panel panel-primary' id='spr_0'>";
		
				foreach ($dadosConsulta['medicos'] as $id2 => $medicos):
				
					$duracao_atendimento = $medicos['duracao_atendimento'];
					$qtdConsultasHora = (int)(60/$medicos['duracao_atendimento']);
					$minutoInicial = (int)$medicos['duracao_atendimento']; 
					$atendimentos = isset($medicos['atendimentos']) ? $medicos['atendimentos'] : array();
					
					echo "	<div class='panel-heading'>
								<h4 class='panel-title'><i class='fa-user-md'></i> Agenda de <span style='font-size:20px; font-weight: bold'>{$medicos['nome']}</span></h4>
							</div>
							<div class='panel-body'>
								<table class='table table-striped table-hover table-fixed-layout non-responsive'>
									<tbody>";
                                        if (isset($medicos['agenda'])) :  
                                        
    				                       foreach ($medicos['agenda'] as $dadosAgenda) :
    					                           
    				                            echo "<tr><td colspan='4'><div style='font-weight:bold'> <i class='fa-time'></i> Agenda de ".$dadosAgenda['horainicial'] .' às '. $dadosAgenda['horafinal'] ."</div></td></tr>";
    				                            
        										for($i=(int)substr($dadosAgenda['horainicial'],0,2); $i<=(int)substr($dadosAgenda['horafinal'],0,2)-1; $i++) :
        											$min = 0; for($x=0;$x<=$qtdConsultasHora-1;$x++) :
        											$procedimento = null; $obs = null; $paciente = null; $class= 'label label-primary mr10';
        											$confirm = 'im-star s20';
        											$horario = str_pad($i,2,'0',STR_PAD_LEFT).':'.str_pad($min,2,'0',STR_PAD_LEFT);
        
        											if(array_key_exists($horario,$atendimentos)){
        												$procedimento = $atendimentos[$horario]['nmProcedimento'];
        												$obs = $atendimentos[$horario]['nmStatus'];
        												$paciente = $atendimentos[$horario]['nmCliente'];
        												$class = 'label label-success mr10';
        												$confirm = 'im-star3 s20';
        											}
        											
        											echo "	<tr>
        														<td class='email-select input-mini'>
        															<label class='checkbox'><span class='{$class}'><i class='br-clock'></i> {$horario}</span></label>
        														</td>
        														<td class='email-star input-mini'><i class='{$confirm}'></i>
        														</td>
        														<td class='email-subject'>";
        															if($paciente == null):
        															     $horario = str_replace(':', '', $horario);
                                                                        echo sprintf('<button class="btn btn-sm btn-primary btn-alt" type="button" onclick="agendarConsulta(%s, %s);"><i class="br-plu"></i> Agendar</button>', $medicos['idmedico'], $horario);
        																//echo "<button class='btn btn-sm btn-primary btn-alt' type='button' onclick='agendarConsulta({$medicos['idmedico']}, {$horario}, '".$medicos['data']."');'><i class='br-plus'></i> Agendar</button>";
        															else :
        																echo "<span data-toggle='popover' title='{$paciente}' data-content='Telefone Fixo: (61) 3299-1187 Telefone Celular: (61) 9844-1187 Convênio: Particular' class='avatar'><img alt='' title='Contato: (61) 8441-1902' src='".BASE_URL."/img/fotos/".$atendimentos[$horario]['idCliente'].".jpg' class='chat-avatar'></span> $paciente";
        															endif;
        														echo "</td>
        														<td class='email-intro'>
        															<a href='#'>
        																<span class='label label-pink mr10'>{$procedimento}</span>{$obs}
        															</a>
        														</td>
        													</tr>"; 
        												$min+=$duracao_atendimento;
        											endfor;
        										endfor;
    				                        endforeach;
                                        endif;
									echo "</tbody>
								</table>
							</div><hr>";
									
						endforeach;
					echo "</div>";
					
			else :
			
			echo '
				<div class="alert alert-danger fade in">
					<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
					<strong>Ops!</strong> Nenhum médico encontrado.
				</div>';
			endif;
		echo "</div>";
		
	}

}