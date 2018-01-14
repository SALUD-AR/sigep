<?

require_once("../../config.php");
$grp_id=$_GET['grp_id'] or $grp_id=-1;

$q = "SELECT u.id_usuario,u.login,u.nombre||' '||u.apellido as nombre,u.comentarios,";
$q.= "case when gu.id_grupo is not null then 1 else 0 end as acces  ";
$q.= "FROM usuarios u ";
$q.= "join phpss_account on phpss_account.username=u.login ";

//Si se paso el id de grupo, recupero los usuarios q pertenecen al grupo, sino todos los usuarios
$q.="left join grupos_usuarios gu on u.id_usuario=gu.id_usuario AND gu.id_grupo=$grp_id ";
$q.="where active='true' ";
$q.="order by nombre ";

$r=sql($q) or fin_pagina();

$xmlDocument=new DOMDocument("1.0","iso-8859-1");
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
	$usr=$xmlDocument->createElement('item');
	$usr->setAttribute("id",$r->fields['id_usuario']);
	$usr->setAttribute("text",utf8_encode($r->fields['nombre']."({$r->fields['login']})"));

	if ($r->fields['acces']) 
		$usr->setAttribute("checked","true");
	$root->appendChild($usr);
	$r->movenext();
}
$xmlDocument->appendChild($root);


Header("Content-type:text/xml");
echo $xmlDocument->saveXML();

?>