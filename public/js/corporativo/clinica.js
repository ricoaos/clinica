$(document).ready(function(){
	
	$("#st_cnpj").mask('99.999.999/9999-99');
	$("#st_fonecontato").mask('(99)9999-9999');
	$("#st_cep").mask('99.999-999');
	$("#st_inscricao_estadual").mask('99.999.999/999-99');
	$("#st_logradouro,#st_bairro,#st_cidade,#st_estado,#st_tipo_logradouro").attr('readonly',true).css({'background':'#CCC'});
	$("#modalmensagem").modal();
	
	$("#st_cep").change(function(){
		
		 getLogradouro($("#st_cep").val());
	});
	  
	$("#st_cep").click(function(){
      
       $("#st_logradouro,#st_bairro,#st_cidade,#st_estado,#st_tipo_logradouro,#st_numero,#st_complemento").val('').attr('readonly',true).css({'background':'#CCC'});
       $("#st_cep").val('');
	});
   
	$("#st_cnpj").change(function(){
		
		var cnpj = $(this).val().replace(/\D/g,"");
		
		if(!ValidaCNPJ(cnpj)){
			$('#modalVal').modal();
			$("#st_cnpj").val('');
			return;
		};
	});
   
	$("#idconvenio").change(function(){
		$.ajax({
		   url: baseUrl+'/corporativo/clinica/planosconvenio',
	       data: 'idconvenio='+$(this).val(),
	       dataType: 'json',
	       type:"POST",
	       success: function(response){
	    	   
	    	   linha='';
	    	   
	    	   $.each(response.result, function(i, item){
		    	   linha += '<div class="col-lg-3 col-md-3">';
				   linha += 	'<div class="icheckbox_flat-blue" style="position: relative;">';
	               linha +=     	'<input type="checkbox" value='+item.idplano+' style="position: absolute; opacity: 1;" id="id_plano[]" name="id_plano[]"/>';
	               linha +=     '</div>"'+item.nome+'"';
	               linha += '</div>';
	    	   });
	    	   
	    	   $("#plano").html(linha);
	       }
	   });
	});
              
});