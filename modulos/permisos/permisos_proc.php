<?

require_once("../../config.php");
/**
 * Archivo con algunas clases para manejar la parte de los permisos
 */
require_once("./permisos.class.php");
$changes=trim($_POST['changes']);
$cant=0;//cantidad de nodos en el arbol

/**
 * Recupera los nodos hijos del nodo(ArbolOfPermisos) que se paso como parametro(recorre todo el arreglo $nodos)
 * @param ArbolOfPermisos $nodo
 * @param integer $parentId
 * @param array $nodos
 */
function getChilds(ArbolOfPermisos &$nodo,$parentId,&$nodos)
{
	$cant=count($keys=array_keys($nodos));
	//por cada clave en el arreglo
	for ($j=0; $j < $cant; $j++) 
	{
		//echo '$nodos['.$keys[$j].'][5]='."{$nodos[$keys[$j]][5]} ------ \$parentId=$parentId<br>";
		
			if(key_exists($keys[$j],$nodos))
				$pos=$keys[$j];
			else 
				continue;
		
		//si es hijo del nodo $nodo
		if($nodos[$pos][5]==$parentId)
		{
			//si el nodo es nuevo para agregar en la BD
			if ($nodos[$pos][0]=="add")
				$new_nodo=new ArbolOfPermisos($nodos[$pos][3],$nodos[$pos][4],$nodos[$pos][2]);
			elseif ($nodos[$pos][0]=="upd")
				$new_nodo=new ArbolOfPermisos($nodos[$pos][3],$nodos[$pos][4],$nodos[$pos][2],$nodos[$pos][1]);
			
			$new_nodo->dir=$nodos[$pos][6];
			$nodo->appendChild($new_nodo);
			$parent=$nodos[$pos][1];
			unset($nodos[$pos]);
			//recupero recursivamente los nodos hijos del nuevo permiso
			getChilds($new_nodo,$parent,$nodos);
		}
	}
}

//Ids de los grupos a los que se dara permisos por defecto a los nuevos items de permisos
$default_group_ids=array(1);
if ($changes!='')
{
		$db->startTrans();
		$nodos1=split('#',$changes);
//		print_r($nodos);echo "<br>";
		foreach ($nodos1 as $i => $value)
		{
			//					0				1				2				3					4					5					6
			//$value2='opCode,nodeId,nodeType,nodeName,nodeDesc,nodeParentId,dir'
			$arr_tmp=split(",",$value);
			//Los indexo por id de nodo
			$nodos[$arr_tmp[1]]=$arr_tmp;
		}	
//		print_r($nodos);echo "<br>";
		$keys=array_keys($nodos);
		$cant=count($keys);
		//por cada clave en el arreglo
		for ($j=0; $j < $cant; $j++)
		{
			if(key_exists($keys[$j],$nodos))
				$i=$keys[$j];
			else 
				continue;

			//si el padre no se agrego (o sea q existe en el arreglo aun)
			//y el padre es un nodo nuevo tambien (entonces este buscara sus hijos con getChilds)
			if(key_exists($nodos[$i][5],$nodos) && $nodos[$nodos[$i][5]][0]=='add')
				continue;

			switch ($nodos[$i][0]) 
			{
				case 'add':
					$nodo=new ArbolOfPermisos($nodos[$i][3],$nodos[$i][4],$nodos[$i][2]);
					$nodo->dir=$nodos[$i][6];
					$parent=$nodos[$i][5];
					$nodeId=$nodos[$i][1];
					unset($nodos[$i]);
					getChilds($nodo,$nodeId,$nodos);
					$nodo->saveDB($parent);
//					print_r($nodo); echo "<br>";
					
					/* En esta parte se agregan los permisos nuevos automaticamente a los grupos de usuarios por defecto */
					$ids=split(",",$nodo->getTreeIds(dbOp_Ins));
					foreach ($default_group_ids as $id_group)
					{ 
						$q="";
						foreach ($ids as $id) 
							$q.="insert into permisos_grupos (id_grupo,id_permiso) values ($id_group,$id);";
							
							
					}
					if (is_array($ids) && is_array($default_group_ids))
						sql($q) or fin_pagina();
					/* fin de la parte de agregar permisos auto a los grupos por defecto*/	
					
					
					/*COMO AGREGO UN NUEVO PERMISO O PAGINA AL ARBOL ACTUALIZO LOS PERMISos de los programadores*/
					$sql="select distinct id_usuario 
					      from permisos.grupos join permisos.grupos_usuarios using (id_grupo)
					      where id_grupo in (".join(",",$default_group_ids).")";
					$res=sql($sql) or fin_pagina();
					if ($res->recordcount()>0) {
						  $checked_users=array();
						  $i=0;
						  while (!$res->EOF) {
						    $checked_users[$i++]=$res->fields['id_usuario'];
						    $res->Movenext();
						  }
					if ($checked_users[0]!="") 
							  actualizar_permisos_bd($checked_users);
					     }
						
				break;
				case 'del':	
					$checked_users=obtener_id_usuarios($nodos[$i][1]); 
								
					ArbolOfPermisos::removeDBTree($nodos[$i][1],true);
					//actualizo los permisos en la bd de cada usuario para el menu
					if ($checked_users[0]!="") 
							  actualizar_permisos_bd($checked_users);
					unset($nodos[$i]);
				break;
				case 'upd':       
                                    
				    $checked_users=obtener_id_usuarios($nodos[$i][1]); 
				    $nodo=new ArbolOfPermisos($nodos[$i][3],$nodos[$i][4],$nodos[$i][2],$nodos[$i][1]);
					$nodo->dir=$nodos[$i][6];
					$nodo->saveDB($nodos[$i][5]);
					//actualizo los permisos en la bd de cada usuario para el menu
                                        
					if ($checked_users[0]!="") {
                                        
							  actualizar_permisos_bd($checked_users);
                                        }
					unset($nodos[$i]);
					
				break;
			}
		}
		$db->completeTrans();
		
		
		
		/**
	 	* Se genera nuevamente el archivo estatico XML del arbol de permisos
	 	*/
		$arbol=new ArbolOfPermisos("root");		
		$arbol->createTree();
		ob_start();
		$arbol->saveXML();
		file_put_contents("permisos.xml",ob_get_contents());
		ob_end_clean();
}
//die('Ya se guardo');

?>