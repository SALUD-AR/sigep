<?php
require_once("../../config.php");

variables_form_busqueda("rep_def_listado");

$fecha_hoy=date("Y-m-d H:i:s");
$fecha_hoy=fecha($fecha_hoy);

$orden = array(
        "default" => "4",
        "default_up" => "0",
        "1" => "apellido",
        "2" => "documento" ,     
        "3" => "fecha_dia",      
        "4" => "id_rep_def"      
       );
$filtro = array(
		    "documento" => "DNI",
        "apellido" => "Apellido"                       
       );


$sql_tmp="
SELECT
registro.rep_def.id_rep_def,
registro.rep_def.documento,
registro.rep_def.nombre as nom_pers,
registro.rep_def.apellido,
registro.rep_def.fecha_inf,
registro.rep_def.hist_cli,
registro.rep_def.fecha_dia,
registro.rep_def.usuario,
registro.rep_def.id_rep_def,
nacer.efe_conv.nombre,
nacer.efe_priv.nombre as establecimiento_privado
FROM
registro.rep_def
left JOIN nacer.efe_conv ON registro.rep_def.cuie = nacer.efe_conv.cuie
left JOIN nacer.efe_priv ON registro.rep_def.establecimiento_privado = nacer.efe_priv.id_efe_priv";


echo $html_header;
echo "<link rel=stylesheet type='text/css' href='$html_root/lib/bootstrap-3.3.1/css/custom-bootstrap.css'>";
?>
<div class="newstyle-full-container">
<form name=form1 action="rep_def_listado.php" method=POST>

<div class="row-fluid" align="center">
        <div class="span8" >
           <?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
           <input class="btn" type=submit name="buscar" value='Buscar' >
           <input class="btn" type='button' name="nuevo" value='Nuevo Dato' onclick="document.location='rep_def_admin.php';" >
        </div>
</div>

<?$result = sql($sql) or die;?>

<hr>
  <div class="pull-right paginador">
      <?=$total_muletos?> Denuncias.
      <?=$link_pagina?>
  </div>
  
  <table class="table table-striped table-advance table-hover">
    <thead>
      <tr>
        <th><a href='<?=encode_link("rep_def_listado.php",array("sort"=>"4","up"=>$up))?>'>ID</a></th>
        <th><a href='<?=encode_link("rep_def_listado.php",array("sort"=>"2","up"=>$up))?>'>DNI</a></th>
        <th><a href='<?=encode_link("rep_def_listado.php",array("sort"=>"1","up"=>$up))?>'>Apellido</a></th>      	
        <th>Nombre</th>
        <th>Fecha Informe</th>
        <th>Historia Clinica</th>   
        <th>Establecimiento</th>        
        <th><a href='<?=encode_link("rep_def_listado.php",array("sort"=>"3","up"=>$up))?>'>Fecha de Carga</a></th>   
        <th>Usuario</th>       
      </tr>
    </thead>
 <?
   while (!$result->EOF){
   	$ref = encode_link("rep_def_admin.php",array("id_rep_def"=>$result->fields['id_rep_def'],"pagina_listado"=>"rep_def_listado.php"));
    $onclick_elegir="location.href='$ref'";?>
     
     <tr>   
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['id_rep_def']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['documento']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['apellido']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nom_pers']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=Fecha($result->fields['fecha_inf'])?></td> 
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['hist_cli']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nombre'].$result->fields['establecimiento_privado']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=Fecha($result->fields['fecha_dia'])?></td> 
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['usuario']?></td>      
     </tr>
	<?$result->MoveNext();
    }?>
    
</table>
</form>
<?echo fin_pagina();// aca termino ?>
