/**
 * Verifica se o cpf é valido
 * @param Objcpf
 * @returns {Boolean}
 */
function ValidarCPF(Objcpf){
    var cpf = Objcpf; 
    var digitoDigitado = eval(cpf.charAt(9)+cpf.charAt(10));
    var soma1=0;
    var soma2=0;
    var vlr =11;
    
    if( (cpf.length != 11) || (cpf == "00000000000") || (cpf == "11111111111") || (cpf == "22222222222") || (cpf == "33333333333") ||
    	(cpf == "44444444444") || (cpf == "55555555555") || (cpf == "66666666666") || (cpf == "77777777777") ||
    	(cpf == "88888888888") || (cpf == "99999999999") ){
    		 return false;
    }

    for(i=0;i<9;i++){
            soma1+=eval(cpf.charAt(i)*(vlr-1));
            soma2+=eval(cpf.charAt(i)*vlr);
            vlr--;
    }       
    soma1 = (((soma1*10)%11)==10 ? 0:((soma1*10)%11));
    soma2=(((soma2+(2*soma1))*10)%11);
    var digitoGerado=(soma1*10)+soma2;
    if(digitoGerado!=digitoDigitado){
    	return false;
    }      
    return true;         
}

/**
 * Verifica se o CNPJ é valido
 * @param cnpj
 * @returns {Boolean}
 */
function ValidaCNPJ(cnpj) {
	
    cnpj = cnpj.replace(/[^\d]+/g, '');
    if (cnpj == '') return false;
    if (cnpj.length != 14)
        return false;
    // Elimina CNPJs invalidos conhecidos
    if (cnpj == "00000000000000" ||
        cnpj == "11111111111111" ||
        cnpj == "22222222222222" ||
        cnpj == "33333333333333" ||
        cnpj == "44444444444444" ||
        cnpj == "55555555555555" ||
        cnpj == "66666666666666" ||
        cnpj == "77777777777777" ||
        cnpj == "88888888888888" ||
        cnpj == "99999999999999")
        return false;

    // Valida DVs
    tamanho = cnpj.length - 2
    numeros = cnpj.substring(0, tamanho);
    digitos = cnpj.substring(tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2)
            pos = 9;
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(0))
        return false;

    tamanho = tamanho + 1;
    numeros = cnpj.substring(0, tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2)
            pos = 9;
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(1))
        return false;

    return true;
}

/**
 * Valida a data de nascimento 
 * @param $valor
 * @param $element
 * @returns
 */
function dateValidate($valor,$element){
			
	if($valor){
		$erro = "";
		var expReg = /^((0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[012])\/[1-2][0-9]\d{2})$/;
		
		if($valor.match(expReg)){
			var $dia = parseFloat($valor.substring(0,2))< 10 ? '0'+parseFloat($valor.substring(0,2)) : parseFloat($valor.substring(0,2));
			var $mes = parseFloat($valor.substring(3,5))< 10 ? '0'+parseFloat($valor.substring(3,5)) : parseFloat($valor.substring(3,5));
			var $ano = parseFloat($valor.substring(6,10));
			
			var $dataAtual = new Date();
			var $anoMaxNasc = $dataAtual.getFullYear();
			var $mesMaxNasc = $dataAtual.getMonth()+1;
			var $diaMaxNasc = $dataAtual.getDate();
			var $diferenca = parseInt($anoMaxNasc-$ano);
			
			var datadig = $ano+''+$mes+''+$dia;
			var dataatu = $anoMaxNasc+''+$mesMaxNasc+''+$diaMaxNasc;
											
			if ($diferenca >120){
				$erro = 'A data deve está incorreta, pois com a data informada, o paciente possui '+$diferenca+' anos, bem superior a média de idade da população.';
			}
			
			if (datadig > dataatu){
				$erro = 'Data incorreta! data superior a data atual.';
			}
			
			if(($mes==4 && $dia>30) || ($mes==6 && $dia>30) || ($mes==9 && $dia>30) || ($mes==11 && $dia>30)){
				$erro = 'Data incorreta! O mês especificado só possui 30 dias.';
			}else{
				if($ano%4!=0 && $mes==2 && $dia>28){
					$erro = 'Data incorreta!! O mês especificado na data '+$valor+' contém 28 dias.';
				}else{
					if($ano%4==0 && $mes==2 && $dia>29){
						$erro = 'Data incorreta!! O mês especificado na data '+$valor+' contém 29 dias.';
					}
				}
			}
		}else{
			$erro = 'Formato de Data para '+$valor+' é inválido';
		}
		
		if($erro){
			
			$("#"+$element).val('');
			alert($erro);
			
		}else{
			return $(this);
		}
	}else{
		return $(this);
	}
}