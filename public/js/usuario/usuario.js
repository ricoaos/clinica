$(document).ready(function(){
	
	$("#st_cpf").mask('999.999.999-99');
	$("#dt_nascimento").mask('99/99/9999');
	$("#wrapper_booth").hide();
          
    $("#foto").click(function(){
    	photobooth();
    });

	$("#dt_nascimento").change(function(){
		dateValidate($(this).val(),'dt_nascimento');
	});
    
    $("#btnLimpar").click(function(){
    	window.location.href = baseUrl+'/usuario/usuario/index';
    });

	$("#st_cpf").change(function(){
		
		var cpf = $(this).val().replace(/\D/g,"");
		
		if(!ValidarCPF(cpf)){
			$('#modalVal').modal();
			$("#st_cpf").val('');
			return;
		};
	});
	
	$("#btnAddPerfil").click(function(){
		
		$.ajax({
			url: baseUrl+'/usuario/usuario/acesso',
			data: $("#formPerfil").serialize(),
			dataType: 'json',
	        type:"POST",
			success:function(response){	
				alert(response.result);
			}
		});
	});
});

function excluirRegistro(org,user){
		
	$.ajax({
		url: baseUrl+'/usuario/usuario/deleteperfil',
		data: 'organizacao='+org+'&usuario='+user,
		dataType: 'json',
        type:"POST",
		success:function(response){	
			alert("Registro Deletado");
		}
	});
	
}