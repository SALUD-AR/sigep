<?

require_once "../../config.php";
require_once("./permisos.class.php");


class Tree extends DomDocument {
    private $xpath;
    function __construct($doc, $ver = "1.0", $encode = "iso-8859-1") {
        parent::__construct($ver, $encode);
        $this->preserveWhiteSpace = false;
//        $this->loadXML($doc);//para leer de un string
        $this->load($doc);//para leer de un archivo
        $this->xpath = new DOMXPath($this);
    }
    function AgregarItem($padre,$id,$titulo) {
		if ($nodo = $this->BuscarItem($padre)) {
			$item = $this->createElement("item");
			$item->setAttribute("id",utf8_encode($id));
			$item->setAttribute("text",utf8_encode($titulo));
			$nodo->appendChild($item);
		}
		else {
			$item = $this->createElement("item");
			$item->setAttribute("id","error");
			$item->setAttribute("text",utf8_encode("ERROR: No se pudo agregar el item '$titulo' a '$padre'"));
			$this->documentElement->appendChild($item);
		}
    }
    function BorrarItem($id) {
		if ($nodo = $this->BuscarItem($id)) {
			$nodo->parentNode->removeChild($nodo);
		}
    }	
    function SeleccionarItem($id) {
		if ($nodo = $this->BuscarItem($id)) {
			$nodo->setAttribute("checked","1");
		}
		else {
			/*
			$item = $this->createElement("item");
			$item->setAttribute("id","error");
			$item->setAttribute("text",utf8_encode("ERROR: No se encontró el item '$id'"));
			$this->documentElement->appendChild($item);
			*/
		}
    }
	function BuscarItem($id) {
		$query = "//item[@id = '$id']";
		$entries = $this->xpath->query($query);
		return $entries->item(0);
	}
	/**
	 * agrega el atributo checked=1 a el resultado de la expresion xpath
	 *
	 * @param unknown_type $xpathExp
	 */
	function SeleccionarItems($xpathExp)
	{
		$entries = $this->xpath->query($xpathExp);
		for ($i=0; $i < $entries->length; $i++)
			$entries->item($i)->setAttribute("checked","1");
	}
}

function xml_vacio() {
	echo "<?xml version='1.0' encoding='iso-8859-1'?>";
	echo "<tree id='0' />";
	exit;
}

//Header("Content-type:text/xml");
//Header("Content-type:text/plain");

switch ($_GET['get']) {
	case 'all':{
/*		$arbol=new ArbolOfPermisos("root");		
		$arbol->createTree();
		$arbol->saveXML();
*/
            ob_clean();
                    Header("Content-type:text/xml");
				readfile("./permisos.xml");
        }
	break;
	//Para recuperar la primer solicitud de usuario
	case 'usr1'://recupero el arbol de permisos para el usuario
				$XMLtree=new Tree("./permisos.xml");
				
				//USAR UN OBJETO user COMO VARIABLE DE SESION
				$newUser=user::createById($_GET['usr_id']);
				$i=0;				
				$str="";
				while ($i < $newUser->permisos->length)
				{
					$str.=$coma."@id='".$newUser->permisos[$i++]->id."'";
					$coma=" or ";
				}
				if($str!="")
					$XMLtree->SeleccionarItems("//item[$str]");
                                ob_clean();
                    Header("Content-type:text/xml");
				echo $XMLtree->saveXML();
	break;
	case 'usr':{//recupero el arbol de permisos para el usuario
                
				$newUser=user::createById($_GET['usr_id']);
				$i=0;				
				//JSON format, retorno un arreglo con los ids de permisos
                                ob_clean();
                header('Content-Type: application/json');
				echo "[";
				$coma="";
				while ($i < $newUser->permisos->length)
				{
					echo $coma.$newUser->permisos[$i++]->id;
					$coma=",";
				}
				echo "];\n";
        }
	break;
	case 'grp1'://recupero el arbol de permisos para el grupo dado

				$q ="select p.id_permiso,p.uname,p.desc ";
				$q.="from grupos ";
				$q.="join permisos_grupos pg using(id_grupo) ";
				$q.="join permisos p using(id_permiso) where id_grupo={$_GET['grp_id']} ";
				$q.="order by id_permiso ";
				$r=sql($q) or fin_pagina();
				$XMLtree=new Tree("./permisos.xml");
				$str="";
				while (!$r->EOF) {
					$str.=$coma."@id='".$r->fields['id_permiso']."'";
					$coma=" or ";
					$r->movenext();
				}
				if($str!="")
					$XMLtree->SeleccionarItems("//item[$str]");
                                ob_clean();
                    Header("Content-type:text/xml");
				echo $XMLtree->saveXML();
	break;
	case 'grp'://recupero el arbol de permisos para el grupo dado
				$q ="select p.id_permiso,p.uname,p.desc ";
				$q.="from grupos ";
				$q.="join permisos_grupos pg using(id_grupo) ";
				$q.="join permisos p using(id_permiso) where id_grupo={$_GET['grp_id']} ";
				$q.="order by id_permiso ";
				$r=sql($q) or fin_pagina();
				//JSON format, retorno un arreglo con los ids de permisos
                                ob_clean();
                                header('Content-Type: application/json');
                                
				echo "[";
				$coma="";
				while (!$r->EOF)
				{
					echo $coma.$r->fields['id_permiso'];
					$coma=",";
					$r->movenext();
				}
				echo "];\n";
	break;
	case 'menu'://recupero todo el arbol de permiso para mostrar el menu
		//ENVIAR EL XML SOLO DE LOS ITEMS QUE SE PUEDEN MOSTRAR EN EL MENU
		$arbol=new ArbolOfPermisos("root");		

//		SIN CONTROL DE PERMISOS		
		$arbol->createTree();
		
//USAR UN OBJETO user COMO VARIABLE DE SESION

//		CON CONTROL DE PERMISOS
//		$arbol->createTree(user::createById($_ses_user['id']));
//		ALTERNATIVAMENTE
//		$arbol->createTree(new user($_ses_user['login']));
		
		$arbol->saveXMLMenu();
	break;
	/**
	 * Busqueda de permisos por un campo determinado
	 * @param string field indica el campo en el q se debe buscar
	 * @param string key es la palabra a buscar
	 * @return Array un arreglo en notacion JSON (JavaScript Object Notation) con los ids encontrados
	 */
	case 'item':{
		$q ="select id_permiso from permisos where ";
		switch ($_GET['field']) {
			case '1'://desc
				$field="\"desc\""; 
			break;
			case '3'://dir
				$field="dir"; 
			break;
			case '2'://uname
			default:
				$field="uname"; 
			break;
		};
		$q.="$field ilike '%".$_GET['key']."%' order by id_permiso,\"desc\"";
		$r=sql($q) or fin_pagina();
                
                ob_clean();
                header('Content-Type: application/json');
                
		echo "[";
		$coma="";
		while (!$r->EOF) 
		{
			echo $coma.$r->fields['id_permiso'];
			$coma=",";
			$r->movenext();
		}
		echo "];\n";}
		break;//del case item
		
	//recupera los usuarios q poseen un permiso determinado
	case 'usuarios':
		$q = "SELECT u.id_usuario,u.login,u.nombre||' '||u.apellido as nombre,u.comentarios ";
		$q.= "from usuarios u ";
		$q.= "join permisos_usuarios using(id_usuario) ";
		$q.= "where id_permiso={$_GET['id']}";
		$q.= "union ";
		$q.= "SELECT u2.id_usuario,u2.login,u2.nombre||' '||u2.apellido as nombre,u2.comentarios ";
		$q.= "from usuarios u2 ";
		$q.= "join grupos_usuarios using(id_usuario) ";
		$q.= "join permisos_grupos using(id_grupo) ";
		$q.= "where id_permiso={$_GET['id']}";
		$q.= "order by nombre ";
		$r=sql($q) or fin_pagina();
		$xmlDocument=new DOMDocument("1.0","iso-8859-1");
		$root=$xmlDocument->createElement('tree');
		$root->setAttribute("id",0);

		while (!$r->EOF) 
		{
			$usr=$xmlDocument->createElement('item');
			$usr->setAttribute("id",$r->fields['id_usuario']);
			$usr->setAttribute("text",utf8_encode($r->fields['nombre']."({$r->fields['login']})"));
			$root->appendChild($usr);
			$r->movenext();
		}
		$xmlDocument->appendChild($root);
                ob_clean();
                    Header("Content-type:text/xml");
		echo $xmlDocument->saveXML();	
	break;
	//recupera los grupos q poseen un permiso determinado
	case 'grupos':
		$q = "SELECT id_grupo,uname ";
		$q.= "from grupos ";
		$q.= "join permisos_grupos using(id_grupo) ";
		$q.= "where id_permiso={$_GET['id']}";
		$q.= "order by uname ";
		$r=sql($q) or fin_pagina();
		$xmlDocument=new DOMDocument("1.0","iso-8859-1");
		$root=$xmlDocument->createElement('tree');
		$root->setAttribute("id",0);

		while (!$r->EOF) 
		{
			$usr=$xmlDocument->createElement('item');
			$usr->setAttribute("id",$r->fields['id_grupo']);
			$usr->setAttribute("text",utf8_encode($r->fields['uname']."({$r->fields['id_grupo']})"));
			$root->appendChild($usr);
			$r->movenext();
		}
		$xmlDocument->appendChild($root);
                ob_clean();
                    Header("Content-type:text/xml");
		echo $xmlDocument->saveXML();	
	break;
	default:
		break;
}