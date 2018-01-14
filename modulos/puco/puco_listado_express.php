<?php
require_once("../../config.php");
include("../nacer/secure/securimage.php");
$img = new Securimage();
$valid = $img->check($_POST['code']);
?>

<script type="text/javascript">
function onEnter(ev)
 {  if(ev==13)
    { document.form1.submit();
    } 
 }

function valida_entero(){
 if(document.all.documento.value==""){
	 alert("Debe completar el campo numero de documento");
	 document.all.documento.focus();
	 return false;
	 }else{
 		var documento=document.all.documento.value;
		if(isNaN(documento)){
			alert('El dato ingresado en numero de documento debe ser entero y no contener espacios');
			document.all.documento.focus();
			return false;
	 	}
	 }
}
</script>

<?echo $html_header;?>

<form name=form1 action="puco_listado_express.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      	<td align=center>
          	<b>Documento: <input type="text" size="30" value="" name="documento" onKeyUp="onEnter(event.keyCode);"/>
         	Codigo de la Imagen: <input type="text" name="code" size="12" />
          	&nbsp;&nbsp;<input type=submit name="buscar" value='Buscar' accesskey='1' onclick='return valida_entero()'>      		
      	</td>
    </tr>  
	
	<tr>
      	<td align=center>      
      		<img id="siimage" src="../nacer/secure/securimage_show.php?sid=<?php echo md5(time()) ?>" />
      		<a tabindex="-1" href="#" title="Cambiar Imagen" onclick="document.getElementById('siimage').src = '../nacer/secure/securimage_show.php?sid=' + Math.random(); return false"><img src="../nacer/secure/images/refresh.gif" alt="Cambiar Imagen" border="0" onclick="this.blur()" align="bottom" /></a>
      	</td>
      </tr>
	  
</table>

<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  
  <tr>
    <td id=mo>DOCUMENTO</td>      	
  	<td Id=mo>NOMBRE</td>      	
    <td id=mo>OBRA SOCIAL</td>  
  </tr>
    <?	
		$documento=$_POST['documento'];
    	
		if((strlen($documento)>=7)&&($valid == true)){
    			$sql_tmp="SELECT puco.documento,puco.nombre,obras_sociales.nombre AS obra_social
				FROM puco.puco
				INNER JOIN puco.obras_sociales ON (puco.puco.cod_os = puco.obras_sociales.cod_os)
				WHERE (puco.puco.documento ='$documento')";
				$query=sql($sql_tmp,"ERROR al realizar la consulta")or fin_pagina();
    					
    			if($query->recordCount()==0){?> 
    				   	<tr>   
						     <td align="center" colspan="3">NO SE ENCONTRARON DATOS</td> 						      
						</tr> 
				<?}
				else{ 
					while (!$query->EOF) {?>
						    <tr <?=atrib_tr()?>>   
						     <td><?=$query->fields['documento']?></td>      
						     <td><?=$query->fields['nombre']?></td>
						     <td ><?=$query->fields['obra_social']?></td> 
						    </tr>    
							<?$query->MoveNext();
					}//FIN WHILE
			  	}//fin else  
    	 }  
    	 else{?>
    	 	<tr> 
    	 		<?php if((strlen($documento)<7)&&($valid != true)){ ?>
    	 			<td align="center" colspan="3">DEBE INGRESAR UN DOCUMENTO DE AL MENOS 7 DIGITOS - INGRESE UN CODIGO DE IMAGEN CORRECTO</td> 	
    	 		<?php }
    	 		else if((strlen($documento)<7)&&($valid == true)){?>	
    	 			<td align="center" colspan="3">DEBE INGRESAR UN DOCUMENTO DE AL MENOS 7 DIGITOS</td>     	 		
    	 		<?php } 
    	 		else if((strlen($documento)>=7)&&($valid != true)){?>
    	 			<td align="center" colspan="3">INGRESE UN CODIGO DE IMAGEN CORRECTO</td>   	 	
    	 		<?php }?>				      
			</tr>   	 	
    	 <?}?>  	
</table>
</form>
</body>
</html>

<?echo fin_pagina();// aca termino ?>