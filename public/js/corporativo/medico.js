$(document).ready(function(){
	
	$("#st_cpf").mask('999.999.999-99');
	$("#st_fonefixo,#st_fonecelular,#st_fonecontato").mask('(99)9999-9999');
	$("#st_cep").mask('99.999-999');
	$("#dt_nascimento").mask('99/99/9999');
	$("#wrapper_booth").hide();
	$("#st_logradouro,#st_bairro,#st_cidade,#st_estado,#st_tipo_logradouro").attr('readonly',true).css({'background':'#CCC'});
              
    $("#foto").click(function(){
    	photobooth();
    });
   
  //Realiza a busca de nomes similares ao digitado.
    $("#st_nome").change(function(){
    	
    	$.ajax({
        	url: baseUrl+'/corporativo/cliente/getregistrosimilar',
        	data: 'nome='+$("#st_nome").val(),
            dataType: 'json',
            type:"POST",
            success: function(response){
            
            	var objResult = response.result;
            	var linha= "";
            	
            	if(objResult != ''){
            		            		
            		linha += '<div class="panel-body">';
            		linha += '<table class="table table-striped table-hover table-fixed-layout non-responsive">';
            		linha += '<tbody>';
            		            		
            		$.each(objResult, function(i, item){
	            		var imagem = item.id_foto != 1 ? baseUrl+'/assets/img/user.png' : baseUrl+'/img/fotos/'+item.id_entidade+'.png';
	            		var url = baseUrl+'/corporativo/medico/index/id/'+btoa(item.dt_nascimento+'@'+item.id_entidade);
	            		linha += '<tr>';
	            		linha +=   '<td class="email-subject input-mini"" >';
	            		linha +=   		'<a href="'+url+'"><img width="50" class="chat-avatar" src="'+imagem+'"></a>';
	            		linha +=   '</td>';
	            		linha +=   	'<td class="email-subject input-mini">';
	            		linha +=        '<span class="help-block">Registro</span>';
	            		linha +=   		'<label class="checkbox">'+item.id_entidade+'</label>';
         				linha +=   '</td>';
	            		linha +=   	'<td class="email-subject">';
	            		linha +=        '<span class="help-block">Paciente</span>';
	            		linha +=   		'<label class="checkbox">'+item.st_nome+'</label>';
         				linha +=   '</td>';
	            		linha +=   	'<td class="email-subject">';
	            		linha +=        '<span class="help-block">CPF</span>';
	            		linha +=   		'<label class="checkbox">'+item.st_cpf+'</label>';
         				linha +=   '</td>';
	            		linha +=   	'<td class="email-subject">';
	            		linha +=        '<span class="help-block">Data Nascimento</span>';
	            		linha +=   		'<label class="checkbox">'+item.dt_nascimento+'</label>';
         				linha +=   '</td>';
         				linha +=   	'<td class="email-subject">';
         				linha +=        '<span class="help-block">Cadastrado em</span>';
	            		linha +=   		'<label class="checkbox">'+item.dt_cadastro+'</label>';
         				linha +=   '</td>';
	            		linha += '</tr>';
					});
	            	
	            	linha += '</tbody>';		
	            	linha += '</table>';
	            	linha += '<div>';
	            	
	            	$("#RegSimilar").html(linha);
	            	$("#modalSimilar").modal();
            	}
            }
        });
	});  
    
    
	$("#st_cep").change(function(){
		
		 getLogradouro($("#st_cep").val());
    });
	
	$("#dt_nascimento").change(function(){
		dateValidate($(this).val(),'dt_nascimento');
	});
   
    $("#st_cep").click(function(){
       
        $("#st_logradouro,#st_bairro,#st_cidade,#st_estado,#st_tipo_logradouro,#st_numero,#st_complemento").val('').attr('readonly',true).css({'background':'#CCC'});
        $("#st_cep").val('');
    });

	$("#st_cpf").change(function(){
		
		var cpf = $(this).val().replace(/\D/g,"");
		
		if(!ValidarCPF(cpf)){
			$('#modalVal').modal();
			$("#st_cpf").val('');
			return;
		};

        $.ajax({
        	url: baseUrl+'/corporativo/cliente/getpacientebycpf',
        	data: 'cpf='+cpf,
            dataType: 'json',
            type:"POST",
            success: function(response){
            	
            	var objResult = response.result;
            	var linha = '';
            	
            	if(objResult != ''){
            		
            		linha += '<div class="panel-body">';
            		linha += '<table class="table table-striped table-hover table-fixed-layout non-responsive">';
            		linha += '<tbody>';
            		
	            	$.each(objResult, function(i, item){
	            		var imagem = item.id_foto != 1 ? baseUrl+'/assets/img/user.png' : baseUrl+'/img/fotos/'+item.id_entidade+'.png';
	            		var url = baseUrl+'/corporativo/cliente/index/id/'+btoa(item.dt_nascimento+'@'+item.id_entidade);
	            		linha += '<tr>';
	            		linha +=   '<td class="email-subject input-mini" >';
	            		linha +=   		'<a href="'+url+'"><img width="50" class="chat-avatar" src="'+imagem+'"></a>';
	            		linha +=   '</td>';
	            		linha +=   	'<td class="email-subject input-mini">';
	            		linha +=        '<span class="help-block">Registro</span>';
	            		linha +=   		'<label class="checkbox">'+item.id_entidade+'</label>';
         				linha +=   '</td>';
	            		linha +=   	'<td class="email-subject">';
	            		linha +=        '<span class="help-block">Paciente</span>';
	            		linha +=   		'<label class="checkbox">'+item.st_nome+'</label>';
         				linha +=   '</td>';
	            		linha +=   	'<td class="email-subject">';
	            		linha +=        '<span class="help-block">CPF</span>';
	            		linha +=   		'<label class="checkbox">'+item.st_cpf+'</label>';
         				linha +=   '</td>';
	            		linha +=   	'<td class="email-subject">';
	            		linha +=        '<span class="help-block">Data Nascimento</span>';
	            		linha +=   		'<label class="checkbox">'+item.dt_nascimento+'</label>';
         				linha +=   '</td>';
         				linha +=   	'<td class="email-subject">';
         				linha +=        '<span class="help-block">Cadastrado em</span>';
	            		linha +=   		'<label class="checkbox">'+item.dt_cadastro+'</label>';
         				linha +=   '</td>';
	            		linha += '</tr>';
					});
	            	
	            	linha += '</tbody>';		
	            	linha += '</table>';
	            	linha += '<div>';
	            	
	            	$("#RegCpf").html(linha);            		
            		$('#modalCPF').modal();
            	}
            }
        });
	});
});