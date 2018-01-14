<?

require_once("../../config.php");
$usr_id=$_GET['usr_id'] or $usr_id=-1;

$q ="select g.id_grupo,uname,case when gu.id_grupo is not null then 1 else 0 end as acces ";
$q.="from grupos g ";

//Si se paso el id de usuario, recupero los grupos del usuario, sino todos los grupos
$q.="left join grupos_usuarios gu on g.id_grupo=gu.id_grupo AND gu.id_usuario=$usr_id ";
$q.="order by g.uname";
$r=sql($q) or fin_pagina();

$xmlDocument=new DOMDocument();//este no permite acentos
$root=$xmlDocument->createElement('tree');
$root->setAttribute("id",0);
$i=1;
/*
while (!$r->EOF) 
{
	$grp=$xmlDocument->createElement('grupo');
	$grp->setAttribute("id",$r->fields['id_grupo']);
	$grp->appendChild($xmlDocument->createElement("uname",$r->fields['uname']));
	$grp->appendChild($xmlDocument->createElement("desc",'Desc nodo '.$i++));
	$root->appendChild($grp);
	$r->movenext();
}
*/
while (!$r->EOF) 
{
	//Recupero los permisos de grupo
	$q ="select * from permisos_grupos where id_grupo=".$r->fields['id_grupo'];
	$r2=sql($q) or fin_pagina();
	$str_permisos="";
	$add_coma="";
	while (!$r2->EOF) 
	{
		$str_permisos.=$add_coma.$r2->fields['id_permiso'];
		$add_coma=",";
		$r2->movenext();
	}
	
	$grp=$xmlDocument->createElement('item');
	$grp->setAttribute("id",$r->fields['id_grupo']);
	$grp->setAttribute("text",utf8_encode($r->fields['uname']."({$r->fields['id_grupo']})"));
	if ($r->fields['acces']) 
		$grp->setAttribute("checked","true");
		
	$permisos=$xmlDocument->createElement("userdata",$str_permisos);
	$permisos->setAttribute("name","permisos");
	$grp->appendChild($permisos);
	$root->appendChild($grp);
	$r->movenext();
}
$xmlDocument->appendChild($root);

      
Header("Content-type:text/xml");
echo $xmlDocument->saveXML();

?>