<?

require_once("../../config.php");

$checked_groups=split(',',$_POST['group_checked']);
$user_id=$_POST['select_usuarios'];
$group_count=count($checked_groups);
$db->startTrans();
$q ="delete from grupos_usuarios where id_usuario=$user_id;";
$q.="delete from permisos_usuarios where id_usuario=$user_id;";
sql($q) or fin_pagina();
$q="";
//echo $_POST['tree_checked']."<br>";
if ($checked_groups[0]!="")
{
	$q ="select distinct id_permiso from ";
	$q.="grupos ";
	$q.="join permisos_grupos using(id_grupo) ";
	$q.="where grupos.id_grupo in ({$_POST['group_checked']}) ";
	$q.="order by id_permiso ";
	$r=sql($q) or fin_pagina();
	
	//busco los permisos que ya pertenecen a alguno de los grupos para no duplicar los permisos
	//como permisos de usuario
		while (!$r->EOF) 
		{
			$id_p=$r->fields['id_permiso'];
			//CASO 1: un unico id
			//CASO 2: al principio
			//CASO 3: en algun lado que no sea el final
			//CASO 4: al final							
			
			//EXP original
			//$_POST['tree_checked']=ereg_replace("($id_p)|($id_p,)|(,$id_p$)","",$_POST['tree_checked']);
			$_POST['tree_checked']=ereg_replace("(^$id_p$)|(^$id_p,)|(,$id_p$)","",$_POST['tree_checked']);
			$_POST['tree_checked']=ereg_replace("(,$id_p,)",",",$_POST['tree_checked']);
//			echo $_POST['tree_checked']."<br>";
			$r->movenext();
		}
	$q ="";
	for ($i=0; $i < $group_count; $i++) 
		$q.="insert into grupos_usuarios (id_usuario,id_grupo) values ($user_id,{$checked_groups[$i]});";
}
//busco los permisos de grupo que tenia antes 
$checked_ids=split(',',$_POST['tree_checked']);
//echo $r->recordcount()."<br>";	print_r($checked_ids);die;
$checked_count=count($checked_ids);
if ($checked_ids[0]!="")
{
	for ($i=0; $i < $checked_count; $i++)
		$q.="insert into permisos_usuarios (id_permiso,id_usuario) values ({$checked_ids[$i]},$user_id);";
}
if ($q!="")
	sql($q) or fin_pagina();
	actualizar_permisos_bd($user_id);
$db->completeTrans();
?>