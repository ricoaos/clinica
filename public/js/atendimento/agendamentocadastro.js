$(document).ready(function() {
	
	$("#fone1, #fone2").mask('(99)9999-9999');
	
	$('.select2').select2({
		theme:'classic'
	});   
    
	$.ajaxSetup({
		type:"POST",
		datatype: "html",
		beforeSend:function(){$('#loading').html('Aguarde...');},
		complete:function(){$('#loading').html('');},
		error:function(){
			alert("Ocorreu um erro");
		}
	});

	// Carrega Especialidades dos medicos
	$('#idmedico').change(function(){
		
		$('#idespecialidade, #idprocedimento, #idconvenio, #idplano').val(null).trigger("change");
		
		if ($(this).val() == "") {
			
			$.ajax({
				url: baseUrl+'/atendimento/agendamento/getfiltroagendamento',
				success: function(response){
					var stmt = JSON.parse(response);
					
					var select ='<optgroup label="Médico"><option value="">Médico</option></optgroup>';
					$.each(stmt.result.medico, function(i, item1){
						select +='<option value="'+item1.idmedico+'">'+item1.nmMedico+'</option>';
					});
					$('#idmedico').html(select);
					
					var select2 ='<option value="">Especialidade</option>';
					$.each(stmt.result.especialidade, function(i, item2){
						select2 +='<option value="'+item2.idespecialidade+'">'+item2.nmEspecialidade+'</option>';
					});
					$('#idespecialidade').html(select2);

					var select3 ='<option value="">Procedimento</option>';
					$.each(stmt.result.procedimento, function(i, item3){
						select3 +='<option value="'+item3.idprocedimento+'">'+item3.nmProcedimento+'</option>';
					});
					$('#idprocedimento').html(select3);
				}
			});
			
			return;
		}
		
		$.ajax({
			url: baseUrl+'/atendimento/agendamento/getespecialidadesbymedico',
			data: $("#formAgendamento").serialize(),
			success: function(response){
				var stmt = JSON.parse(response);
				
				var select ='<option value="">Especialidade</option>';
				$.each(stmt.resposta, function(i, item){
					select +='<option value="'+item.idespecialidade+'">'+item.nmEspecialidade+'</option>';
				});
				
				$('#idespecialidade').html(select);
			}
		});
		
	});

	// Carrega os Procedimentos
	$('#idespecialidade').change(function(){
		$.ajax({
			url: baseUrl+'/atendimento/agendamento/getprocedimentosmedico',
			data: $("#formAgendamento").serialize(),
			success: function(response){
			var stmt = JSON.parse(response);
			$('#idprocedimento').removeAttr('disabled');
				var select ='<option value="">Procedimento</option>';
				$.each(stmt.resposta, function(i, item){
					select +='<option value="'+item.idprocedimento+'">'+item.nmProcedimento+'</option>';
				});
				
				$('#idprocedimento').html(select);
			}
		});		
	});
	
	// 
	$('#idprocedimento').change(function(){
		
		$('#idconvenio, #idplano').empty();
    	$('#idconvenio').html('');
    	
    	$.ajax({
    		url: baseUrl+'/atendimento/agendamento/getconvbyproc',
    		data: $("#formAgendamento").serialize(),
    		success: function(response){
    			var stmt = JSON.parse(response);
    			$('#idconvenio').removeAttr('disabled');
    			
				var select ='<option value="">Convênio</option>';
				$.each(stmt.resposta, function(i, item){
					select +='<option value="'+item.idconvenio+'">'+item.nmConvenio+'</option>';
				});
				
				$('#idconvenio').html(select);
    		}
    	});
    });
    
	
    $('#idconvenio').change(function(){
    	
    	$('#idplano').empty();
    	
    	$.ajax({
    		url: baseUrl+'/atendimento/agendamento/getplanosbymedico',
    		data: $("#formAgendamento").serialize(),
    		success: function(response){
    			var stmt = JSON.parse(response);
    			$('#idplano').removeAttr('disabled');
    			
    			var select ='<option value="">Plano</option>';
				$.each(stmt.resposta, function(i, item){
					select +='<option value="'+item.idplano+'">'+item.mnPlano+'</option>';
				});
				
				$('#idplano').html(select);
    		}
    	});
    });
    
    $('#calendario').datepicker({
        //defaultDate: "+5d",
        changeMonth: true,
        numberOfMonths:1 ,
        minDate:"+0",
		dateFormat: "yy-mm-dd",
		altField: "#alternate",
		language: "pt-BR",
		//beforeShowDay: $.datepicker.noWeekends, // Desabilita finais de semana
		//selectWeek: true,
		//firstDay: 1,
        onSelect: function(selectedDate) {
        	
    		if($("#idespecialidade").val() == '' || $("#idprocedimento").val() == '' || $("#idconvenio").val() == ''){
    			alert("Favor selecionar os parâmetros de pesquisa!");
    			return false;
    		}
        	
    		$("#dt").val(selectedDate);
    		refreshAgenda(selectedDate);
        },
		/*
		beforeShowDay: function(day) {
	        var day = day.getDay();
	        if (day == 2 || day == 5) {
	            return [false, "somecssclass"]
	        } else {
	            return [true, "someothercssclass"]
	        }
		}   
		*/     
    });  
    
    $("#btnPesquisar").click(function(){
    	$.ajax({
    		url: baseUrl+'/corporativo/cliente/getpacientebycpf',
    		data: 'st_cpf='+$("#cpf").val(),
    		success: function(response){  
    			//var resp = JSON.parse(response.result);
    			//var result[0].st_nome
    			$('#dados').html(response.result[0].st_nome);
    		}
    	});
    	
    });
    
    $("#btnAgendar").click(function(){
    	$.ajax({
    		url: baseUrl+'/atendimento/agendamento/cadastro',
    		data: $("#formRealizaAgendamento").serialize(),
    		success: function(response){  
    			//var resp = JSON.parse(response.result);
    			//var result[0].st_nome
    			refreshAgenda($("#dt").val());
    			alert(response);
    			$('#myModal2').modal('hide');
    		}
    	});
    });
    
});

function refreshAgenda(data){
	$.ajax({
		url: baseUrl+'/atendimento/agendamento/getagendabydata',
		data: $("#formAgendamento").serialize()+'&data='+data,
		success: function(response){
			$('#resultado').html(response);
		}
	});	
}

function agendarConsulta(idMedico, hora){
	
	if($("#idespecialidade").val() == '' || $("#idprocedimento").val() == '' || $("#idconvenio").val() == ''){
		alert("Favor selecionar os parâmetros de pesquisa!");
		return false;
	}
	
	var data = $("#calendario").val();
	$("#xidmedico").val(idMedico);
	$("#viewData").html('Dr. Alfredo nunes <'+idMedico+'>, dia 09/11/2015 às 08:00h');
	$("#hrConsulta").val(hora);
	$("#xdataatendimento").val($('#dt').val() + ' 09:00');
    // Setando os valores
    $('#myModal2').modal();
}   