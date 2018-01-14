<?

require_once(LIB_DIR."/class.gacz.php");
/**
 * Carpeta contenedora y separadora
 */
define("Modulo",1);
/**
 * Pagina de menu
 */
define("PaginaMenu",2);
/**
 * Carpeta
 */
define("PaginaFuera",3);
/**
 * permiso simple, no puede tener hijos en el arbol
 */
define("Permiso",4);

/**
 * No se ha hecho ninguna operacion de BD aun
 */
define("dbOp_None",0);
/**
 * Se inserto
 */
define("dbOp_Ins",1);
/**
 * Se actualizo
 */
define("dbOp_Upd",2);
/**
 * Se elimino de la BD
 */
define("dbOp_Del",3);
/**
 * Cualquier operacion o ninguna operacion
 */
define("dbOp_Any",4);
/**
 * Se busco en la BD
 */
define("dbOp_Sel",5);

/**
 * Esta clase representa un permiso de usuario o de grupo
 * y posee metodos para guardarse y cargarse desde y hacia la BD
 */
class permiso extends BaseClass {
	
	/**
	 * Id_permiso
	 * @var Integer
	 */
	private $_id;
	/**
	 * Nombre unico del permiso en la BD
	 * @var String
	 */
	private $_name;
	/**
	 * Descripcion del Permiso
	 * @var String
	 */
	private $_desc;
	/**
	 * Id del tipo de permiso
	 * @var Integer
	 */
	private $_idTipo;
	/**
	 * Directorio donde se encuentra el archivo en caso de ser una pagina
	 * @var string
	 */
	private $_dir;
	/**
	 * Indica la ultima operacion realizada en la BD (Ver constantes dbOp_* )
	 * propiedad de solo lectura 'lastDbOp'
	 * @var integer
	 */
	private $_lastDbOp=dbOp_None;
	/**
	 * Arreglo con las imagenes que se pueden usar dependiendo del tipo
	 * estan indexadas por el id de tipo 
	 * @var array
	 */
	private static $_imagenes=array();
	/**
	 * Crea un permiso nuevo o ya existente (id!=0)
	 * @param String $name el Nombre unico del permiso en la base de datos
	 * @param String $desc la descripcion del permiso, para que sirve
	 * @param Integer $id el id unico del permiso en la BD
	 * @param Constante $tipo ver los posibles valores de constantes
	 */		
	function __construct( $name,  $desc="",$tipo=Permiso, $id=-1)
	{
//				echo "tipo_permiso: $tipo\n";

		$this->_id=$id;	
		$this->set_name($name);
		$this->set_desc($desc);
		$this->_idTipo=$tipo;
		$this->_imagenes=array();
		
		//recupero los distintos tipos de la BD
		if (count(permiso::$_imagenes)==0) 
			$this->getImagenes();
		
		//si solo se paso el nombre unico del permiso, trata de recuperarlo de la BD
		if ($this->desc=="" && $this->_id==-1)
			$this->loadDB();
	}
	/**
	 * Busca un permiso en la BD por el id pasado como parametro
	 * @param Integer $id
	 * @return permiso
	 */
	public static function createById($id)
	{
		$q ="select * ";
		$q.="from permisos ";
		$q.="join tipo_permiso using(id_tipo) where id_permiso=$id ";
		$r=sql($q);
		if ($r)
		{
			$new_nodo=new permiso($r->fields['uname'],$r->fields['desc'],$r->fields['id_tipo'],$r->fields['id_permiso']);
			$new_nodo->dir=$r->fields['dir'];
			return $new_nodo;
		}
		else 
			return null;		
	}
	protected function loadDB()
	{ 
		$q ="select * ";
		$q.="from permisos ";
		$q.="join tipo_permiso using(id_tipo) where uname='{$this->name}' ";
		$r=sql($q) or die($q);
//		print_r($r->fields);die;
		if ($r->recordCount())
		{
			$this->_id=$r->fields['id_permiso'];	
			//$this->_name=$r->fields['uname'];	
			$this->desc=$r->fields['desc'];
			$this->setTipo($r->fields['id_tipo']);
			$this->dir=$r->fields['dir'];
		}
		else 			
			$this->desc=$this->name;
			
		$this->_lastDbOp=dbOp_Sel;
//print_r($this);die;
	}
	/**
	 * Modifica el tipo de permiso
	 * @param Integer $id_tipo
	 */
	function setTipo($id_tipo)
	{
			$this->_idTipo=$id_tipo;
	}
	/**
	 * Recupera la tabla de tipos en la variable de Clase
	 * permiso::$_imagenes
	 */
	private function getImagenes()
	{
		$q ="select * from tipo_permiso";
		$r=sql($q) or fin_pagina();
		while (!$r->EOF)
		{
			permiso::$_imagenes[$r->fields['id_tipo']]=array($r->fields['img1_src'],$r->fields['img2_src'],$r->fields['img3_src']);
			$r->movenext();
		}
	}
	/**
	 * retorna el id de tipo 
	 * @return Integer
	 */
	function getTipo()
	{
		return $this->_idTipo;
	}
	/**
	 * Asigna el nombre unico del permiso
	 * @param String $name
	 */
	function set_name($name)
	{
		$this->_name=trim($name);
	}
	/**
	 * Asigna la descripcion del permiso
	 * @param String $desc
	 */
	function set_desc($desc)
	{
		$this->_desc=trim($desc);
	}
	/**
	 * asigna el ID unico para el permiso
	 * @param Integer $id
	 */
	function set_id($id)
	{
		if ($id <=0)
		{
			trigger_error(get_class($this)."->id EL ID DEBE SER UN ENTERO MAYOR QUE CERO");
			return ;
		}
		$this->_id=$id;
	}
	/**
	 * Asigna el valor a la propiedad dir
	 * @param string $dir
	 */
	function set_dir($dir)
	{
			$this->_dir=trim($dir);
	}
	/**
	 * Recupera el valor de la propiedad dir
	 * @return string
	 */
	function get_dir()
	{
		return $this->_dir;
	}
	
	/**
	 * Recupera una imagen de la lista de imagenes
	 * @param Integer $index
	 * @return String
	 */
	function getImg($index)
	{
		//return $this->_imagenes[$index];
		return permiso::$_imagenes[$this->getTipo()][$index];
	}
	/**
	 * Retorna el nombre unico del permiso
	 * @return String
	 */
	function get_name()
	{
		return $this->_name;
	}
	/**
	 * retorna la descripcion del permiso
	 * @return String
	 */
	function get_desc()
	{
		return $this->_desc;
	}
	/**
	 * retorna el ID unico del permiso
	 * @return Integer
	 */
	function get_id()
	{
		//Verifico si tiene id
		if ($this->check_id())
			return $this->_id;
		else //sino lo inserto
		{
			$this->insert();
			return $this->_id;
		}
	}
/**
 * Funcion q retorna la ultima operacion realizada en la BD
 * @return constant int
 */function get_lastDbOp()
	{
		return $this->_lastDbOp;
	}
	
	private function insert()
	{
		global $_ses_user;
		$fecha=date("Y-m-j H:i:s");
		//Si no esta usando el cambio de usuario
		$user=$_ses_user['original_login']!=""?$_ses_user['original_login']:$_ses_user['login'];
		$q="insert into permisos (uname,\"desc\",id_tipo,dir,create_user,create_date) ";
		$q.="values ('{$this->_name}','{$this->_desc}',{$this->_idTipo},'{$this->_dir}','$user','$fecha')";
		sql($q) or die($q.$this->_name." ".$this->_desc." ".$this->_id." ".$this->_dir);
		
		//TODO: hacer que la recuperacion automatica del id sea opcional
		$this->getDBid();
		
		$this->_lastDbOp=dbOp_Ins;
	}
	private function update()
	{
		global $_ses_user;
		$fecha=date("Y-m-j H:i:s");
		//Si no esta usando el cambio de usuario
		$user=$_ses_user['original_login']!=""?$_ses_user['original_login']:$_ses_user['login'];
		
		$q ="update permisos set ";
		$q.="uname='{$this->name}',";
		$q.="\"desc\"='{$this->desc}',";
		$q.="id_tipo={$this->_idTipo},";
		$q.="dir='{$this->dir}',";
		$q.="modif_date='$fecha',";
		$q.="modif_user='$user' ";
		$q.="where id_permiso={$this->_id}";
		sql($q) or fin_pagina();
		$this->_lastDbOp=dbOp_Upd;
	}
	private function delete()
	{
			$q="delete from permisos where uname='{$this->name}'";
			sql($q) or fin_pagina();
			$this->_lastDbOp=dbOp_Del;			
	}
	/**
	 * Recupera el Id del permiso de la Bd
	 * retorna true si se recupero false sino
	 * @return boolean
	 */
	private function getDBid()
	{
		$q="select id_permiso from permisos where uname='{$this->_name}'";
		$r=sql($q) or fin_pagina();
		$this->_lastDbOp=dbOp_Sel;
		if ($r && $r->recordCount())
		{
			$this->_id=$r->fields['id_permiso'];
			return true;
		}
		return false;
	}
	
	/**
	 * Guarda el permiso en la BD (inserta o actualiza)
	 * @return bool retorna true si se inserto (o actualizo) correctamente, false si hubo algun error
	 */
	function saveDB()
	{
		if ($this->_id!=-1)
			$this->update();
		else
			$this->insert();
	}
	/**
	 * Elimina el permiso de la Base de Datos
	 * @return bool indica si se pudo eliminar o no
	 */
	function removeDB()
	{
		$this->delete();
	}
	/**
	 * Checkea si el permiso existe en la Base de Datos (solo si no tiene ID), si existe recupera el id
	 * @return bool indicando si tiene o se pudo recuperar el id
	 */
	private function check_id()
	{
		//chequea si el permiso existe
		if ($this->_id==-1)
			return $this->getDBid();
		else 
			return true;		
	}
		
	
 /**
	 * Recupera todos los usuarios que poseen este permiso asociado directamente (sin verificar los grupos)
	 * @return userArray
	 *
	private function getUsers()
	{
		//si existe en la BD, recupero los usuarios
		if ($this->_existDB)
		{
			$q="select pu.id_pu,u.id_usuario,u.login,u.nombre,u.apellido ";
			$q.="from permisos_usuarios pu join usuarios u using(id_usuario) ";
			$q.="where id_permiso={$this->_id}";
			$r=sql($q);
			if ($r)
			{
				$i=0;
				while (!$r->EOF)
				{
					//creo un objeto con la propiedades 
					$user=$this->_users[$i++]=new BaseClass();
					$user->id_usuario=$r->fields["id_usuario"];
					$user->login=$r->fields["login"];
					$user->nombre=$r->fields["nombre"];
					$user->apellido=$r->fields["apellido"];
					$r->moveNext();					
				}
			}
		}
		return count($this->_users);
	}	
	*/
}

abstract class virtualArray extends BaseClass implements ArrayAccess {
	protected $_group;
	protected $_length;
	
	//Funciones declaradas en la interface ArrayAccess
	//bool function offsetExists($index);
	//mixed function offsetGet($index);
	//void function offsetSet($index,$new_value)
	//void function offsetUnset($index)
//	function getAll();
//	function getItemByField(String $dbField, $value);
}

define("parent_user","user");
define("parent_group","group");

class ArrayOfPermisos extends virtualArray {
	private $_indexes;//indices para agilizar las busquedas
	private $_parent;//el objeto que contiene los permisos ( puede se un usuario o un grupo)
		
	/**
	 * Este es un objeto que contiene un grupo de permisos
	 * @param Constante $idType
	 */
	function __construct(&$parent)
	{
	  $this->parent=$parent;
		$this->_group=array();
		$this->_indexes=array("id_permiso"=>array());
	}
	/**
	 * Carga el grupo de permisos, dependiendo del tipo de contenedor,
	 * ya sea un objeto tipo usuario o para un objeto tipo grupo(conjunto de permisos)	
	 */
	function loadDB()
	{
		$this->_group=array();
		switch (get_class($this->parent)) {
			//recupero todos los permisos de un usuario
		 	case parent_user:
				$q ="select p.id_permiso,p.uname,p.desc,p.dir,p.id_tipo ";
				$q.="from usuarios ";
				$q.="join permisos_usuarios pu using(id_usuario) ";
				$q.="join permisos p using(id_permiso) where id_usuario={$this->parent->id_usuario} ";
								
				$q.="union ";
				$q.="select p.id_permiso,p.uname,p.desc,p.dir,p.id_tipo ";
				$q.="from usuarios u2 ";
				$q.="join grupos_usuarios gu on gu.id_usuario=u2.id_usuario AND u2.id_usuario={$this->parent->id_usuario} ";
				$q.="join permisos_grupos pg using(id_grupo) ";
				$q.="join permisos p using(id_permiso) ";
				$q.="order by id_permiso ";
				
		 	break;
		 	//recupero todos los permisos de un grupo
		 	case parent_group:
				$q ="select p.id_permiso,p.uname,p.desc,p.dir ";
				$q.="from grupos ";
				$q.="join permisos_grupos pg using(id_grupo) ";
				$q.="join permisos p using(id_permiso) where id_grupo={$this->parent->id_grupo} ";
				$q.="order by id_permiso ";
	 		break;
		}
//		echo $q;echo "<br>";
		$r=sql($q) or fin_pagina();;
		$i=0;
		
		while ($r && !$r->EOF)
		{
			//creo un objeto permiso por cada entrada del resultset
			$this->_group[$i]=new permiso($r->fields['uname'],$r->fields['desc'],$r->fields['id_tipo'],$r->fields['id_permiso']);
			$this->_group[$i]->dir=$r->fields['dir'];
			//indexo los campos mediante un arreglo asociativo
			$this->_group[$r->fields['uname']]=&$this->_group[$i];
			$this->_indexes['id_permiso'][$r->fields['id_permiso']]=&$this->_group[$i];
			$i++;
			$r->moveNext();
		}
		$this->_length=$i;
	}
	/**
	 * Guarda el conjunto de permisos en la Bd
	 * @return bool si se pudo guardar retotna true, sino false
	 */
	function saveDB()
	{
		$qi="";
		switch (get_class($this->parent)) {
			case parent_user:
				$qi.="delete from permisos_usuarios where id_usuario={$this->parent->id};";
			for ($i=0; $i < $this->length; $i++)
				$qi.="insert into permisos_usuarios (id_permiso,id_usuario) values ({$this[$i]->id},{$this->parent->id});";
			break;
			case parent_group:
				$qi.="delete from permisos_grupos where id_grupo={$this->parent->id};";
			for ($i=0; $i < $this->length; $i++)
				$qi.="insert into permisos_grupos (id_permiso,id_grupo) values ({$this[$i]->id},{$this->parent->id});";
			break;			
		}
		if ($this->length)
		{
		 if (!sql($qi))
		 	return false;
		}
		return true;
	}
	function set_parent(&$parent)
	{ 
		//controlo que sea un objeto parent permitido
	  switch (get_class($parent)) {
	   	case parent_user:
	   	case parent_group:
	   		$this->_parent=$parent;
   		break;
	   	default:
	  		trigger_error(get_class($this)."::set_parent() : tipo no valido para el objeto parent");
   		break;
	   }
	}
	function get_parent()
	{
		return $this->_parent;
	}
	/**
	 * verifica si el indice es valido
	 *
	 * @param mixed $index
	 * @return bool
	 */
	function offsetExists($index)
	{
		if(is_object($this->_group[$index]))
			return true;
		else
			return false;
	}
	function offsetGet($index)
	{
		if($this->offsetExists($index))
			return $this->_group[$index];
		else 
			return null;
	}
	function offsetSet($index,$new_value)
	{
		if (is_object($new_value)) 
		{
			if (!$this->offsetExists($index))
				$this->_length++;
			
			$this->_group[$index]=$new_value;
			$this->_group[$new_value->name]=&$this->_group[$index];
			$this->_indexes['id_permiso'][$new_value->id]=&$this->_group[$index];
		}
	}
	function offsetUnset($index)
	{	
		if($this->offsetExists($index))
		{
			//borro los indices
			unset($this->_indexes['id_permiso'][$this->_group[$index]->id]);	
			unset($this->_group[$this->_group[$index]->name]);	
			//borro el permiso
			unset($this->_group[$index]);	
		}
	}
	/**
	 * Use la propiedad length: Retorna la cantidad de elementos en el arreglo 
	 * @return Integer
	 */
	function get_length()
	{
		return $this->_length;
	}
	/**
	 * Recupera un objeto del arreglo por su nombre unico
	 *
	 * @param unknown_type $uname
	 * @return unknown
	 */
	function getByName($uname)
	{  if (is_object($this->_group[$uname])) 
			return 	$this->_group[$uname];			
		else 
			return null;
	}
	
	function getById($id_permiso)
	{
		if (is_object($this->_indexes['id_permiso'][$id_permiso])) 
			return 	$this->_indexes['id_permiso'][$id_permiso];			
		else 
			return null;
	}
	
	/**
	 * Recupera el nombre del modulo de una pagina
	 *
	 * @param unknown_type $uname
	 * @return var
	 */
	function getmoduloByName($uname)
	{  if (is_object($this->_group[$uname])) 
			return 	$this->_group[$uname]->get_dir();			
		else 
			return null;
	}
	
	
	/**
	 * Recupera la descripcion del modulo de una pagina
	 *
	 * @param unknown_type $uname
	 * @return var
	 */
	
	function getdescByName($uname)
	{  if (is_object($this->_group[$uname])) 
			return 	$this->_group[$uname]->get_desc();			
		else 
			return null;
	}
		
/**
	 * Recupera la ruta deuna pagina
	 *
	 * @param unknown_type $uname
	 * @return var
	 */
function getpathByName($uname) {
   	if (is_object($this->_group[$uname])) {
	   $id=$this->_group[$uname]->get_id();	
	   while($id!=0) {
  	   $sql="select \"desc\",id_permiso_padre from permisos.arbol 
  	      join  permisos.permisos on arbol.id_permiso_padre=permisos.id_permiso 
          where id_permiso_hijo=$id";
  	   $res=sql($sql,"$sql") or fin_pagina();
  	   $id=$res->fields['id_permiso_padre'];
  	    if ($res->fields["desc"])
  	      $path=$res->fields["desc"]." > ".$path;
  	}
    return $path;
  }
  else return "";
}

}  //fin class ArrayOfPermisos

class user extends BaseClass {
	private $_id_usuario;
	public $login;
//private id_provincia es not null en la BD	
	public $nombre;
	public $apellido;
	public $iniciales;
	public $mail;
	public $telefono;
	public $celular;
	public $direccion;
	public $pagina_inicio;
	public $firma1;
	public $firma2;
	public $firma3;
	public $accesos;//array
	/**
	 * Contiene el conjunto de permisos individuales del usuario, no los permisos compartidos o de grupo
	 * @var ArrayOfPermisos
	 */
	public $permisos;
	/**
	 * Contiene los permisos compartidos, permisos de grupo
	 * @var ArrayOfGrupos
	 */
	//public $grupos;

	function __construct($login,$nombre="",$apellido="")
	{
		$this->login=trim($login);		
		$this->nombre=trim($nombre);
		$this->apellido=trim($apellido);
		
		//si solo se paso el login trata de recuperar los datos de la BD, asumiendo que el usuario ya existe
		if($this->nombre=="" && $this->apellido=="")
			$this->loadDB();
		else 
			$this->_id_usuario=0;//nuevo usuario
	}
	/**
	 * Crea un nuevo objeto usuario a partir de su ID
	 *
	 * @param integer $id es el id unico que posee el usuario en la BD
	 * @return user
	 */
	static function createById($id)
	{
		$q ="select login from usuarios where id_usuario=$id";
		$r=sql($q) or fin_pagina();
		return (new user($r->fields['login']));
	}
	function loadDB()
	{
		$q ="select id_usuario,nombre,apellido,iniciales,mail,pagina_inicio,telefono,celular,direccion,firma1,firma2,firma3,";
		$q.="acceso1,acceso2,acceso3,acceso4,acceso5,acceso6,acceso7,acceso8,acceso9,acceso10,acceso11,acceso12 ";
		$q.="from usuarios ";
		$q.="where login='{$this->login}' ";
		
		$r=sql($q) or fin_pagina();
		if($r)
		{
			$this->_id_usuario=$r->fields['id_usuario'];
			$this->nombre=$r->fields['nombre'];
			$this->apellido=$r->fields['apellido'];
			$this->iniciales=$r->fields['iniciales'];
			$this->mail=$r->fields['mail'];
			$this->pagina_inicio=$r->fields['pagina_inicio'] or $this->pagina_inicio="mensajes";
			$this->telefono=$r->fields['telefono'];
			$this->celular=$r->fields['celular'];
			$this->direccion=$r->fields['direccion'];
			$this->firma1=$r->fields['firma1'];
			$this->firma2=$r->fields['firma2'];
			$this->firma3=$r->fields['firma3'];
			$this->accesos=array();
			for ($i=0; $i < 12; $i++)
				$this->accesos[$i]=$r->fields['acceso'.($i+1)];
			$this->permisos=new ArrayOfPermisos($this);
			$this->permisos->loadDB();
		}
	}
	
	/**
	 * Retorna un arreglo con los accesos directos del uusario
	 *
	 * @return array
	 */
	function get_Accesos() {   
		return $this->accesos;
	}
	
	
	function set_id_usuario($id)
	{
		if ($id > 0)
			$this->_id_usuario=$id;
		else 
			trigger_error(get_class($this)."::id_usuario no es un valor valido '$id'");
	}
	/**
	 * Recupera el id del usuario
	 *
	 * @return integer
	 */
	function get_id_usuario()
	{  
		return $this->_id_usuario;
	}
	
	/**
	*Recupera la pagina de inicio del uusario
	*
	** @return text
	*/
	function get_pagina_inicio() {
		return $this->pagina_inicio;
	}
	
}

//-2(No esta definido y no se consulto la BD) 
define("parentUNDEFINED",-2);
//-1 (no tiene parent y se consulto en la BD)
define("parentNONE",-1);
class ArbolOfPermisos extends permiso {
	/**
	 * es un arreglo con los objetos ArbolOfPermisos que son hijos
	 * @var ArbolOfPermisos
	 */
	public $childs;
	/**
	 * Id del permiso padre 
	 * @var Integer
	 */
	private $_parent=parentUNDEFINED;
	
	/**
	 * Constructor
	 *
	 * @param String $name
	 * @param String $desc
	 * @param Integer $id
	 * @param Constante $tipo
	 */
	function __construct($name,$desc="",$tipo=Modulo,$id=-1)
	{
//		echo "tipo_arbol: $tipo\n";
		parent::__construct($name,$desc,$tipo,$id);
//		$this->_parent=$this->get_parent();
//		$this->loadDB();
		$this->childs=array();
	}
	/**
	 * Devuelve el permiso hijo en la posicion $index
	 *
	 * @param Integer $index
	 * @return permiso
	 */
	function getChild($index)
	{
		return $this->childs[$index];
	}
	/**
	 * retorna el id del permiso padre
	 * @return Integer
	 */
	function get_parent()
	{
		//si el permiso existe en la BD
		if ($this->id > 0 ) 
		{
			//Si no esta definido
			if ($this->_parent==parentUNDEFINED)
			{
				$q="select id_permiso_padre from arbol where id_permiso_hijo={$this->id} ";
				$r=sql($q) or die($q);
				if ($r->recordcount())
					return $r->fields['id_permiso_padre'];
				else 
					return parentNONE; //No tiene parent
			}
			return $this->_parent;
		}
		return parentNONE; //No tiene parent
	}
	/**
	 * asigna el ID unico del permiso padre
	 * @param Integer $id_parent
	 */
	function set_parent($id_parent)
	{
		if ($id_parent >= 0)
			$this->_parent=$id_parent;
		else 
			trigger_error(get_class($this)."::set_parent() el id_parent debe ser un valor >=0");
	}	
	
	/**
	 * TODO: Hacer que a la funcion createTree se le pase un parametro opcional 1....N que indique la cantidad 
	 * de niveles de profundidad a generar, el valor por defecto sera 0 y este indicara que genere todos los
	 * niveles del arbol
	 */
	
	/**
	 * Crea el arbol (desde la BD) que tiene como nodo padre el objeto actual
	 * @param user $usr
	 * @param integer $level
	 */
	function createTree($usr=null)
	{
		$this->_createTree($r=null,$usr);
	}
	/**
	 * Crea el arbol (desde la BD) que tiene como nodo padre el objeto actual
	 * @param  ADORecordSet $r
	 * @param user $usr el usuario a controlar los permisos
	 * @param integer $level
	 */
	private function _createTree(&$r=null,$usr=null)
	{
		if (!$r)
		{
			//Recupero el arbol de permisos, los hijos
			$q ="select a.*,p.uname,p.desc,p.dir,tp.* ";
			$q.="from arbol a ";
			$q.="join permisos p on a.id_permiso_hijo=p.id_permiso ";
			$q.="join tipo_permiso tp using(id_tipo) ";
//			$q.="order by id_permiso_padre,id_permiso_hijo ";
//		Ordenados por descripcion				
			$q.="order by id_permiso_padre,\"desc\" ";
			$r=sql($q) or fin_pagina();
		}
		//Para que me de el subarbol dependiendo del permiso actual
		//busco el id_permiso dentro del resulset
		while (!$r->EOF && $this->id > $r->fields['id_permiso_padre']) {
			$r->movenext();			
		}
		
		$i=0;
		while (!$r->EOF && $this->id==$r->fields['id_permiso_padre']) 
		{
			//controla los permisos q tiene el usuario
			if($usr!=null && $usr->permisos->getByName($r->fields['uname'])!=null)
			{
				$new_node=new ArbolOfPermisos($r->fields['uname'],$r->fields['desc'],$r->fields['id_tipo'],$r->fields['id_permiso_hijo']);
				$new_node->dir=$r->fields['dir'];
				$this->appendChild($new_node);
				$i++;
			}
			//por defecto, recupero todo el arbol
			elseif ($usr==null)
			{
				$new_node=new ArbolOfPermisos($r->fields['uname'],$r->fields['desc'],$r->fields['id_tipo'],$r->fields['id_permiso_hijo']);
				$new_node->dir=$r->fields['dir'];
				$this->appendChild($new_node);
				$i++;
			}
//			print_r($r->fields); echo "<br>";
			$r->movenext();
		}
			//creo los subarboles de los hijos
			for($j=0 ; $j < $i; $j++)
			{
				$r->movefirst();
				$this->childs[$j]->_createTree($r,$usr);
			}
	}
	
	function LimpiarMenu()
	{
		if ($this->childCount() == 0) return false;
		for ($i=0; $i < $this->childCount(); $i++)
		{
			if ($this->childs[$i]==null) continue;
				//si es tipo paginaFuera o permiso, lo salteo
				if ($this->childs[$i]->getTipo()==1 && !$this->childs[$i]->LimpiarMenu()) {
					$this->removeChild($i);
				}
		}
		if ($this->getTipo()==1 && $this->childCount_null() > 0) return true;
		else return false;
		return true;
	}
		/**
	 * Crea el arbol (desde la BD) que tiene como nodo padre el objeto actual
	 * @param user $usr
	 * @param integer $level
	 */
	function createMenu($usr=null)
	{
		$this->_createMenu($r=null,$usr);
		$this->LimpiarMenu();
	}
	/**
	 * Crea el arbol (desde la BD) que tiene como nodo padre el objeto actual
	 * @param  ADORecordSet $r
	 * @param user $usr el usuario a controlar los permisos
	 * @param integer $level
	 */
	private function _createMenu(&$r=null,$usr=null)
	{
		if (!$r)
		{
			//Recupero el arbol de permisos, los hijos
			$q ="select a.*,p.uname,p.desc,p.dir,tp.* ";
			$q.="from arbol a ";
			$q.="join permisos p on a.id_permiso_hijo=p.id_permiso ";
			$q.="join tipo_permiso tp using(id_tipo) ";
			$q.="where tp.id_tipo = 1 or tp.id_tipo = 2 ";		
			$q.="order by id_permiso_padre,\"desc\" ";
			$r=sql($q) or fin_pagina();
		}
		//Para que me de el subarbol dependiendo del permiso actual
		//busco el id_permiso dentro del resulset
		while (!$r->EOF && $this->id > $r->fields['id_permiso_padre']) {
			$r->movenext();			
		}
		
		$i=0;
		while (!$r->EOF && $this->id==$r->fields['id_permiso_padre']) 
		{
			//controla los permisos q tiene el usuario
			if($usr!=null && ($usr->permisos->getByName($r->fields['uname'])!=null || $r->fields['id_tipo'] == 1))
			{
				$new_node=new ArbolOfPermisos($r->fields['uname'],$r->fields['desc'],$r->fields['id_tipo'],$r->fields['id_permiso_hijo']);
				$new_node->dir=$r->fields['dir'];
				$this->appendChild($new_node);
				$i++;
			}
			//por defecto, recupero todo el arbol
			elseif ($usr==null)
			{
				$new_node=new ArbolOfPermisos($r->fields['uname'],$r->fields['desc'],$r->fields['id_tipo'],$r->fields['id_permiso_hijo']);
				$new_node->dir=$r->fields['dir'];
				$this->appendChild($new_node);
				$i++;
			}
//			print_r($r->fields); echo "<br>";
			$r->movenext();
		}
			//creo los subarboles de los hijos
			for($j=0 ; $j < $i; $j++)
			{
				$r->movefirst();
				$this->childs[$j]->_createMenu($r,$usr);
			}
	}

	/**
	 * Envia al browser la representacion XML del arbol
	 * @param boolean $root
	 */
	function saveXML($level=0)
	{
            
		if ($level==0)
		{
			echo "<?xml version='1.0' encoding='iso-8859-1'?>\n";  
			//ORIGINAL 
			echo "<tree id='{$this->id}' withoutImages='yes' ";
			echo "text='{$this->desc}' ";
			echo "child='".($this->childCount_null()?1:0)."' ";
			echo ">\n";
		}
		else 
		{
			echo "<item id='{$this->id}' ";
			echo "text='{$this->desc}' ";
			echo "child='".($this->childCount_null()?1:0)."' ";
			echo "im0='{$this->getImg(0)}' im1='{$this->getImg(1)}' im2='{$this->getImg(2)}' ";
			echo ">\n";
		}
			echo "<userdata name='nodeType'>{$this->getTipo()}</userdata>\n";
			echo "<userdata name='name'>{$this->name}</userdata>\n";
			echo "<userdata name='dir'>{$this->dir}</userdata>\n";
		if ($this->childCount())
		{
			for ($i=0; $i < $this->childCount(); $i++)
			   if ($this->childs[$i]!=null)
				$this->childs[$i]->saveXML($level+1);
		}

		if ($level==0)
			echo "</tree>";
		else 
			echo "</item>\n";
	}
	/**
	 * Envia al navegador un XML con el menu permitido para el usuario
	 * @param integer $level indica el nivel de anidamiento del nodo
	 */
	function saveXMLMenu($level=0)
	{ global $html_root;
		if ($level==0)
		{
			echo "<?xml version='1.0' encoding='iso-8859-1'?>\n";  
			//ORIGINAL echo "<tree id='{$this->id}'>";
			echo "<tree id='0' name='{$this->name}' withoutImages='yes' >\n";
		}
		else 
		{
			echo "<item id='{$this->id}' ";
			echo "text='{$this->desc}' ";
			
			//si es tipo paginamenu o paginafuera
			if ($this->getTipo()==2 || $this->getTipo()==3)
			{
				$parametros=array();
				$pagina=$this->name; 
			
				$div=split("\?",$pagina);
				$page=$div[0].".php";
				    if ($div[1]) $page.="?".$div[1];	
				
			    //$lnk=$html_root."/modulos/".$this->dir."/".$page;
			    $lnk="/modulos/".$this->dir."/".$page;
				echo "href='".$lnk."' ";
				echo "target='frame2' ";
			}
			//primer fila
			if ($level==1) 
				echo "first_row='estilo_fila'";

			echo ">\n";
		}

		if ($this->childCount() > 0)
		{
			for ($i=0; $i < $this->childCount(); $i++)
			{
				if ($this->childs[$i] == null) continue;
			//si es tipo paginaFuera o permiso, lo salteo
				if ($this->childs[$i]->getTipo()==3 || $this->childs[$i]->getTipo()==4)
					continue;
				//if ($level==0 && $i > 0)
						//echo "<divider id='div_2'/>";
				$this->childs[$i]->saveXMLMenu($level+1);
			}
		}

		if ($level==0)
			echo "</tree>";
		else {
			echo "</item>\n";
			if ($level==1) echo "<divider id='div_2'/>\n";
			}
	}
	function saveBootstrapMenu($menu, $level=0)
	{ global $html_root;
        $item = null;

		if ($level!=0)
		{
			if ($this->getTipo()==2 || $this->getTipo()==1)
			{
				$parametros=array();
				$pagina=$this->name; 
			
				$div=split("\?",$pagina);
				$page=$div[0].".php";
				if ($div[1]) 
				{
					$page.="?".$div[1];
				}
				if ($this->dir != "") {
			    	$lnk=$html_root."/modulos/".$this->dir."/".$page;
			    }
			    else {
			    	$lnk='';
			    }
                $item = $menu->add($this->desc, $lnk);
			}
		}

		if ($this->childCount() > 0)
		{
			for ($i=0; $i < $this->childCount(); $i++)
			{
				if ($this->childs[$i] == null) 
				{
					continue;
				}
				//si es tipo paginaFuera o permiso, lo salteo
				if ($this->childs[$i]->getTipo()==3 || $this->childs[$i]->getTipo()==4)
				{
					continue;
				}
                if (is_null($item)) {
                    $item = $this->childs[$i]->saveBootstrapMenu($menu, $level+1);
                }
                else {
                    $item = $this->childs[$i]->saveBootstrapMenu($item, $level+1);
                }
			}
		}
        return $menu;
	}
	/**
	 * Carga el id del permiso padre en caso de q el permiso ya exista
	 *
	 */
	protected function loadDB()
	{   parent::loadDB();
		$q ="select id_permiso_padre ";
		$q.="from arbol ";
		$q.="where id_permiso_hijo={$this->id} ";
		
		$r=sql($q) or die($q);
		if ($r->recordCount())
			$this->_parent=$r->fields['id_permiso_padre'];	
		else
			$this->_parent=parentNONE;//No existe en el arbol		
	}
	/**
	 * Busca un permiso en la BD por el id pasado como parametro
	 * @param Integer $id
	 * @return ArbolOfPermisos
	 */
	public static function createById($id)
	{
		$q ="select * ";
		$q.="from permisos ";
		$q.="join tipo_permiso using(id_tipo) ";
		$q.="left join arbol on arbol.id_permiso_hijo=permisos.id_permiso ";
		$q.="where id_permiso=$id ";
		$r=sql($q);
		if ($r)
		{
			$new_nodo=new ArbolOfPermisos($r->fields['uname'],$r->fields['desc'],$r->fields['id_tipo'],$r->fields['id_permiso']);
			$new_nodo->dir=$r->fields['dir'];
			return $new_nodo;
		}
		else 
			return null;		
	}
	/**
	 * Guarda el arbol en la BD y lo cuelga del id_permiso_padre
	 * @param Integer $id_parent
	 */
	function saveDB($id_parent=false)
	{
		parent::saveDB();
		if ($id_parent!==false && $id_parent >= 0) 
		{
			//Si no es la raiz
			if($this->id!=0)
			{
				//lo borro en caso de que el parent viejo sea distinto del nuevo
				$q ="delete from arbol where id_permiso_hijo={$this->id};";
				$q.="insert into arbol (id_permiso_padre,id_permiso_hijo) values ($id_parent,{$this->id});";
				sql($q) or fin_pagina();
			}
		}
		else //Si no se pasa el parent no lo inserta en el arbol, se supone q existe			
		{
	
			//Si no es la raiz
			if($this->id!=0)
			{
				//busco si existe como hijo de algun permiso antes de insertar en el arbol
				$q ="select * from arbol where id_permiso_hijo=$this->id";
				$r=sql($q) or die($q);
				//si no tiene parent lo inserto con parent por defecto la raiz
				if ($r->recordcount()==0)
				{
					$q ="insert into arbol (id_permiso_padre,id_permiso_hijo) values (0,{$this->id});";
					sql($q) or fin_pagina();
				}
					
			}
		}
		$j=$this->childCount();
		for ($i=0; $i < $j; $i++)
			 if ($this->childs[$i]!= null)
		     $this->childs[$i]->saveDB($this->id);
	}
	/**
	 * Retorna la cantidad de permisos hijos del nodo
	 * @return Integer
	 */
	function childCount()
	{
		return count($this->childs);
	}
	
	function childCount_null() {
	  $j=0;
      foreach ($this->childs as $campo => $descripcion)
          if ($descripcion) $j++;
      return $j;	
	}
	/**
	 * Agrega un permiso como hijo del nodo actual
	 * @param ArbolOfPermisos $permiso
	 */
	function appendChild(ArbolOfPermisos &$permiso)
	{
		$this->childs[]=$permiso;
	}
	/**
	 * Elimina el permiso hijo en la posicion $index
	 * @param Integer $index
	 */
	function removeChild($index)
	{
//		unset($this->childs[$index]);
		$this->childs[$index]=null;
	}
	/**
	 * Funcion q retorna un string con todos los ids de nodos del arbol, separados por coma 
	 * (notacion prefijo)
	 * @param const int $lastDbOp ultima operacion realizada en la BD (ver constantes dbOp_*)
	 */
	function getTreeIds($lastDbOp=dbOp_Any)
	{
		return $this->_getTreeIds("",$lastDbOp);
	}

	/**
	 * Funcion recursiva q retorna un string con todos los ids de nodos del arbol, separados por coma 
	 * (notacion prefijo)
	 * @param string $add_coma
	 * @param string $nodeStatus Estado de los nodos (new,upd,del,all)
	 */
	private function _getTreeIds($str="",$lastDbOp=dbOp_Any)
	{
		if ($lastDbOp==dbOp_Any || $this->get_lastDbOp()==$lastDbOp)
		{
			if ($str!="")
				$str.=",";
			$str.=$this->id;
		}
		for ($i=0; $i < $this->childCount(); $i++)
		    if ($this->childs[$i]!=null)  
			$str=$this->childs[$i]->_getTreeIds($str,$lastDbOp);
		return $str;
	}
	/**
	 * Recupera un arbol desde la base de datos
	 *
	 * @param Integer $id
	 * @param bool $withChilds
	 * @return ArbolOfPermisos
	 */
	public static function getDBTree($id,$withChilds=false)
	{
		$obj=parent::createById($id);
		if ($obj)
		{
			$obj=new ArbolOfPermisos($obj->name,$obj->desc,$obj->getTipo(),$obj->id);
			if ($withChilds) $obj->createTree();
			return $obj;
		}
		else 
			return null;		
	}
	/**
	 * Elimina un arbol de la Base de datos
	 * @param integer $id
	 * @param bool $remove_childs indica si se deben eliminar los nodos que dependen del mismo
	 */
	public static function removeDBTree($id,$remove_childs=false)
	{ 
		if ($remove_childs)
		{
			//recupero los hijos para eliminarlos
			$q ="select id_permiso_hijo from arbol where id_permiso_padre=$id";
			$r =sql($q) or fin_pagina();
			if ($r->recordcount())
			{
				//elimino los permisos hijos
				while (!$r->EOF)
				{				
					ArbolOfPermisos::removeDBTree($r->fields['id_permiso_hijo'],$remove_childs);
					$r->movenext();
				}
			}
		}
		    
        //elimino el arbol actual
		$q ="delete from permisos where id_permiso=$id";//si las claves foraneas se borran en cascada
		sql($q) or fin_pagina();			  
	   
			
	}
}

class HTMLArbolPermisos extends HtmlBaseClass 
{
	/**
	 * Boton buscar
	 * @var HtmlButton
	 */
	public $bbuscar;
	/**
	 * Boton expandir
	 * @var HtmlButton
	 */
	public $bexpandir;
	/**
	 * Boton guardar
	 * @var HtmlButton
	 */
	public $bguardar;
	/**
	 * Combo de busqueda
	 * @var HtmlOptionList
	 */
	public $select_search;
	/**
	 * Imagen atras
	 * @var HtmlImage
	 */
	public $img_prev;
	/**
	 * Imagen adelante
	 * @var HtmlImage
	 */
	public $img_next;
	/**
	 * Especifica si se deben mostrar o no
	 * @var bool
	 */
	public $checkboxes;
	/**
	 * Habilita o no los checkbox de 3 estados
	 * @var bool
	 */
	public $ThreeStateCheckboxes;
	/**
	 * Nombre de funcion Javascript
	 * @var string
	 */
	public $onclickhandler;
	/**
	 * URL q contiene el archivo XML para cargar en el arbol
	 * @var string
	 */
	public $url;
	/**
	 * Flag q indica si se debe cargar la url cuando se inicia el arbol
	 * o sea, apenas se manda al navegador
	 * @var bool
	 */
	public $loadOnInit;

	function __construct()
	{
		$this->id='permisos';
		$this->style="height:93%;background-color:#f5f5f5;border :1px solid Silver; overflow:auto;";
		$this->bbuscar=new HtmlButton("bbuscar","Buscar");
		$this->bbuscar->set_event("onclick","fnBuscar(document.all.ikeyword.value,document.all.select_searchBy.value)");
		$this->bbuscar->set_attribute("title","Haga click para Buscar los Permisos");
		
		$this->bexpandir=new HtmlButton("bexpandir","Expandir Rama");
		$this->bexpandir->set_attribute("title","Expande la rama seleccionada");
		$this->bexpandir->set_event("onclick","if ((openTreeId=tree.getSelectedItemId())!=0) tree.openAllItems(openTreeId);");
		$this->bexpandir->style="width:100px";
		
		
		$this->bguardar=new HtmlButton("bguardar","Guardar Permisos","submit");
		
		$this->select_search=new HtmlOptionList("select_searchBy");
		$this->select_search->add_option("Descripción",1);
		$this->select_search->add_option("Nombre",2);
		$this->select_search->add_option("Directorio",3);
		
		$this->img_prev=new HtmlImage("../../imagenes/atras_dis.gif","img_prev");
		$this->img_prev->onclick="fnMoveTo(--b_index)";
		$this->img_prev->style="cursor:hand";
		$this->img_prev->set_attribute("title","Anterior");
		$this->img_prev->disabled=true;

		$this->img_next=new HtmlImage("../../imagenes/adelante_dis.gif","img_next");
		$this->img_next->onclick="fnMoveTo(++b_index)";
		$this->img_next->style="cursor:hand";
		$this->img_next->set_attribute("title","Siguiente");
		$this->img_next->disabled=true;
		
		$this->checkboxes=false;
		$this->ThreeStateCheckboxes=false;
		$this->onclickhandler="";
		$this->loadOnInit=false;
	}
	function toBrowser()
	{
		echo '<table width="100%" height="100%" style="max-height:400px" border="0" cellpadding="0" cellspacing="0">
			<tr>
			<td height="90%" style="max-height:400px" width="50%">
				<div id="'.$this->id.'" style="'.$this->style.';max-height:400px" ></div>
				<div style="max-height:400px;background-color:#f5f5f5;border :1px solid Silver;overflow:auto;">
				<table width="100%">
					<tr>
						<td align="center">Buscar <input type="text" name="ikeyword" onkeypress="if (window.event.keyCode==13) {document.all.bbuscar.click();return false;} "/> en ';
				$this->select_search->toBrowser();
				echo '&nbsp;';
				$this->bbuscar->toBrowser();
				$this->img_prev->toBrowser();
				echo "&nbsp;";
				$this->img_next->toBrowser();
				
				echo'&nbsp;<span id="span_counter">&nbsp;&nbsp;&nbsp;&nbsp;</span>
						</td>
					</tr>
				</table>
				</div>
			</td>
			</tr>
		<tr>
			<td align="center">';
		$this->bexpandir->toBrowser();
		$this->bguardar->toBrowser();
		echo "\n";
		$this->initTree();
		echo '</td></tr></table>';
		
	}
	private function initTree()
	{
		global $html_root;
		echo "<SCRIPT>\n";
		echo '
		tree=new dhtmlXTreeObject("'.$this->id.'","100%","100%",0);
		tree.setImagePath("'.$html_root.'/imagenes/tree/");
		tree.enableCheckBoxes('.($this->checkboxes?'true':'false').');
		tree.enableThreeStateCheckboxes('.($this->ThreeStateCheckboxes?'true':'false').');';
		echo "tree.enableSmartXMLParsing(1);\n";
		if ($this->onclickhandler!="") echo "tree.setOnClickHandler($this->onclickhandler);\n";
		if ($this->loadOnInit)	echo "tree.loadXML('$this->url');\n";
		echo "\n</script>\n";
	}
	/**
	 * Inserta los Script necesarios para utilizar el arbol con sus tags de <SCRIPT></SCRIPT>
	 * @param boolean $withJStags
	 */
	function insertScripts()
	{
		global $html_root;
		//echo "<link rel='STYLESHEET' type='text/css' href='$html_root/lib/dhtmlXTree.css'>\n";
		echo "<script  src='$html_root/lib/dhtmlXCommon.js'></script>";
		echo "<script  src='$html_root/lib/dhtmlXTree.js'></script>";
		echo "<script  src='$html_root/lib/dhtmlXProtobar.js'></script>";
		
		echo "<script>\n";
		echo "var	busqueda=new Array();\n";
		echo "var b_index=-1;\n";
		echo "function fnMoveTo(index)
					{
					
						var imgp=document.getElementById('img_prev');
						var imgn=document.getElementById('img_next');
						if(imgn.disabled && index+1 < busqueda.length)
						{
							imgn.disabled=0;
							imgn.src='../../imagenes/adelante.gif';
						}
							
						if(!imgp.disabled)
						{
							//si llego al primero
							 if(index==0)
							 {
								imgp.src='../../imagenes/atras_dis.gif';
							 	imgp.disabled=1;
							 }
						}
						else
						{
							//si avanzo al segundo
							if(index == 1)
							{
							 	imgp.disabled=0;
								imgp.src='../../imagenes/atras.gif';
							}
						}
						
						if(!imgn.disabled)
						{
							//si llego al ultimo
							if (index==busqueda.length-1)
							{
								imgn.disabled=1;
								imgn.src='../../imagenes/adelante_dis.gif';
							}
						}
						else
						{
							//si es el penultimo
							if (index==busqueda.length-2)
							{
								imgn.disabled=0;		
								imgn.src='../../imagenes/adelante.gif';
							}
						}
						
						if (index < busqueda.length && index >=0)
						{
							tree.selectItem(busqueda[index]);
							tree.focusItem(busqueda[index]);
							document.getElementById('span_counter').innerText=(index+1)+'/'+busqueda.length;
							document.getElementById('span_counter').title='Total de registros '+busqueda.length;
						}
					}";
echo 
"/************************************* AJAX Begin ***************************************************/
var req1;//para buscar un permiso
function doRequest1(url) {
   if (window.XMLHttpRequest) {
       req1 = new XMLHttpRequest();
   } else if (window.ActiveXObject) {
       req1 = new ActiveXObject('Microsoft.XMLHTTP');
   }
   req1.open('GET', url, true);
   req1.onreadystatechange = onLoad1;
   req1.send(null);   
}
function onLoad1() {
    // only if req shows 'loaded'
    if (req1.readyState == 4) {
        // only if 'OK'
        if (req1.status == 200) {
            // ...processing statements go here...
            busqueda=eval(req1.responseText);
//            busqueda.next=
//            alert('Se encontraron '+busqueda.length+' resultados');
            if (busqueda.length)
            	fnMoveTo(++b_index);
            else
            {
							document.getElementById('span_counter').innerText='0/0';
							document.getElementById('span_counter').title='No se encontraron coincidencias';		
            }
            	
        } else {
            alert('There was a problem retrieving the XML data:\\n' +req1.statusText);
        }
    }
}
//Busca solo en los items q estan guardados en la BD y en los q se hayan agregado sin guardar NO (para hacer)
function fnBuscar(val,searchBy)
{
	if (val=='' || val.length <= 2)
	{
		alert('Por favor ingresa un criterio de búsqueda válido (2 o más caracteres)');
		return;
	}
	busqueda=new Array();
	b_index=-1;
	doRequest1('./permisos_xml.php?get=item&field='+searchBy+'&key='+val);
}
/*************************************** AJAX End ***************************************************/
";
	echo"	function fnOnClick()
				{
                                $(\"span[id='span_id']\").html('(id='+this.id+')');
					//span_id.innerText='(id='+this.id+')';
			
					//si es un permiso que no puede tener hijos
					switch (parseInt(tree.getUserData(this.id,'nodeType')))
					{
						case 4: 
						document.getElementById('bagregar').disabled=true;
						break;
						default:
							document.getElementById('bagregar').disabled=false;
					}
					fnClear();
					//window.event.cancelBubble=true;//para que no tome el onclick del div
				}";
	echo "</script>\n";
	}
}
?>