<?

require_once("../../config.php");

if (count($_GET))
{ $db->StartTrans();
	switch ($_GET['cmd']) {
		case 'del_grp':
		    $query="select id_usuario from permisos.grupos_usuarios where id_grupo={$_GET['grp_id']}";
		    $res=$db->Execute($query);
			if ($res->recordcount()>0) {
			  $checked_users=array();
			  $i=0;
			  while (!$res->EOF) {
			    $checked_users[$i++]=$res->fields['id_usuario'];
			    $res->Movenext();
			  }
  			  
		    }
		
		     
		    $q ="delete from grupos where id_grupo={$_GET['grp_id']}";
		    $r=$db->Execute($q);
		    if ($checked_users[0]!="") 
  			  actualizar_permisos_bd($checked_users);
			if ($r)
				echo 1;
			else 
				echo 0;
			
			break;
	//inserta un nuevo grupo
	case 'ins_grp':
		$q="select nextval('grupos_id_grupo_seq') as id_grupo ";
		$r=$db->Execute($q);
		$id_grupo=$r->fields['id_grupo'];
		$_GET['grp_name']=ucfirst(strtolower($_GET['grp_name'])); 
		$q="insert into grupos (id_grupo,uname) values ($id_grupo,'{$_GET['grp_name']}') ";
		$r=$db->Execute($q);
		if ($r)
			echo $id_grupo;
		else 
		{
			if (stripos($db->ErrorMsg(),"unique")!==false)
				echo -1;//Clave duplicada		
			else
				echo 0;
		}
	break;
	}	
$db->CompleteTrans();		
}
elseif (count($_POST))
{
	$checked_ids=split(',',$_POST['tree_checked']);
	$checked_users=split(',',$_POST['users_checked']);
	$grp_id=$_POST['select_grupos'];
	$users_count=count($checked_users);
	$checked_count=count($checked_ids);
	
	$db->startTrans();
	
	//borro los antiguos permisos del grupo
	if ($grp_id) {
	//obtengo todos los usuarios del grupo
	$query="select distinct id_usuario from permisos.permisos_grupos
            join permisos.grupos_usuarios using(id_grupo) where id_grupo=$grp_id";
		
	$res=sql($query,"query")or fin_pagina();

	if ($res->recordcount()>0) {
	    $check_users=array();
		$i=0;
		while (!$res->EOF) {
		   $check_users[$i++]=$res->fields['id_usuario'];
		   $res->Movenext();
		}
	}
	} 
	
	//borra los permisos del grupo
	$q ="delete from permisos_grupos where id_grupo=$grp_id;";
	
	//inserta los nuevos permisos al grupo 
	if($checked_ids[0]!="")
		for ($i=0; $i < $checked_count; $i++) 
			$q.="insert into permisos_grupos (id_permiso,id_grupo) values ({$checked_ids[$i]},$grp_id);";

	//borro los usuarios que estaban asociados con ese grupo
	$q.="delete from grupos_usuarios where id_grupo=$grp_id;";
	if($checked_users[0]!="")
	{
		//Borro los permisos q pueden tener los usuarios y q ya estan en los grupos seleccionados
		//ya que si esta como permiso de grupo, no debe estar como permiso de usuario y viceversa	
		$q.="delete from permisos_usuarios ";
		$q.="where id_usuario in ({$_POST['users_checked']}) AND id_permiso in ";
		$q.="(select distinct id_permiso ";
		$q.="from grupos ";
		$q.="join permisos_grupos using(id_grupo) ";
		$q.="where grupos.id_grupo=$grp_id); ";
		
		for ($i=0; $i < $users_count; $i++) 
			$q.="insert into grupos_usuarios (id_usuario,id_grupo) values ({$checked_users[$i]},$grp_id);";
	}
	
	sql($q) or fin_pagina();

	if ($db->completeTrans()) {
	    if ($check_users[0]!="") 
	            actualizar_permisos_bd($check_users);
	    if ($checked_users[0]!="")
               actualizar_permisos_bd($checked_users);
	}
	
}