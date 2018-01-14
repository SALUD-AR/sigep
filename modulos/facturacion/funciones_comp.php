<?
function valida_ingreso_ambulatorio_modo_1($id_comprobante,$prestacion,$cantidad){
	 
	//traigo el codigo que estoy facturando
	$query="select codigo from facturacion.nomenclador 
			where id_nomenclador='$prestacion'";	             
	$res_codigo_nomenclador=sql($query, "Error 1") or fin_pagina();	
	$codigo=$res_codigo_nomenclador->fields['codigo'];
	
	if(trim($codigo) == 'CT-C021'){
		//debo saber si alguna vez se al facturo beneficiario el "CT-C020"
		
		//traigo el id_smiafiliados para buscar el codigo "CT-C020"
		$query="select id_smiafiliados from facturacion.comprobante 
				where id_comprobante='$id_comprobante'";	             
		$res_codigo_nomenclador=sql($query, "Error 1") or fin_pagina();	
		$id_smiafiliados=$res_codigo_nomenclador->fields['id_smiafiliados'];
		
		//busco el codigo "CT-C020"
		$query="SELECT id_prestacion				
				FROM nacer.smiafiliados
  				INNER JOIN facturacion.comprobante ON (nacer.smiafiliados.id_smiafiliados = facturacion.comprobante.id_smiafiliados)
  				INNER JOIN facturacion.prestacion ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)
  				INNER JOIN facturacion.nomenclador ON (facturacion.prestacion.id_nomenclador = facturacion.nomenclador.id_nomenclador)
  				where smiafiliados.id_smiafiliados='$id_smiafiliados' and codigo='CT-C020'";
  		$cant_pres=sql($query, "Error 3") or fin_pagina();
   		if ($cant_pres->RecordCount()>=1)return 1;
  		else return 0;
	}
	else return 1;
}
