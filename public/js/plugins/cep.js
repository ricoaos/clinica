/*
 * Realiza a busca do logradouro conforme o cep informado
 */
function getLogradouro(cep){
	var cep = $('#st_cep').val().replace(/\D/g,"");
    $.ajax({
        url : baseUrl+'/apoio/cep/getlogbycep',
        data: 'cep='+cep,
        dataType: 'json',
        type:"POST",
        success: function(response){
        	
        	var objResult = response.result;
        	
            if(objResult != ''){
                $("#st_numero,#st_complemento").attr('readonly',false).css({'background':'#FFF'});
                $('#st_logradouro').val(objResult.logradouro);
                $('#st_bairro').val(objResult.bairro);
                $('#st_cidade').val(objResult.cidade);
                $('#st_estado').val(objResult.siglaEstado);
                $("#st_tipo_logradouro").val(objResult.tipologradouro);
                $("#id_municipio").val(objResult.municipio);
                $("#st_numero").focus();
            }else{
                alert("Cep inválido");
            }
        }
   });
}