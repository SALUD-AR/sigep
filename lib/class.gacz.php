<?


//require_once("../../config.php");
define("XML_VERSION","1.0");
define("XML_ENCODING","utf-8");
//NOTA 1 sobre XML: 
//el codificar los strings con utf8_encode() evita que de errores de parser de XML cuando los caracteres no son
//validos o son caracteres reservados, pero si el string no esta dentro de un CDDATAsection, 
//los caracteres no validos seran removidos del string final
//NOTA 2 sobre XML:
//no hace falta decodificar (utf8_decode()) los strings al leer el archivo xml, el parser XML lo hace automaticamente

/*****************************************************************************
 * clase BaseClass: para que toda nueva clase herede de esta
 * la clases hijas no necesitan implementar los metodos __set __get, 
 * basta con definir una funcion set_nbrepropiedad y get_nbrepropiedad para leer y escribir respectivamente
 * con esta clase tambien se pueden agregar nuevas propiedades, al objeto (como en Javascript)
 * esta ultima caracteristica se puede desactivar en la clase hija poniendo el flag _allowNewProperties en false
 * Creado: viernes 20/05/05
 ****************************************************************************/
class BaseClass 
{
	//arreglo usado para mantener el valor de las propiedades agregadas al objeto (no de la clase)
	protected $_new_properties = array();
	//esta variable se usa para indicarle a la clase si permitira agregar nuevas propiedades en tiempo de ejecucion
	protected $_allowNewProperties = true;

	function __get($prop_name) 
	{
		//existe la funcion de acceso para el atributo ??
		if (method_exists ($this, "get_".$prop_name))
			return call_user_method("get_".$prop_name, $this);
		// es un atributo privado (empieza con '_' ) ??
		else if (substr($prop_name, 0, 1) == "_")
			trigger_error(get_class($this)."::__get(\$prop_name=$prop_name) : es privada de la clase", E_USER_ERROR);
		// es un atributo sin funcion de acceso ??
		else if (array_key_exists($prop_name, get_object_vars($this))) 
			return $this->{$prop_name};
		// es un atributo nuevo creado ?? 
		else if ($this->_allowNewProperties && isset($this->_new_properties[$prop_name])) 
			return $this->_new_properties[$prop_name];
		//error de acceso, la propiedad no se puede establecer
		else
			trigger_error(get_class($this)."::__get(\$prop_name=$prop_name,\$prop_value=$prop_value) : la propiedad no existe ", E_USER_ERROR);
	}
	function __set($prop_name, $prop_value) 
	{
		// existe la funcion de acceso ??
		if (method_exists ($this, "set_".$prop_name)) 
			// llamar a la funcion de acceso
			return call_user_method("set_".$prop_name, $this, $prop_value);
		// es un atributo privado (empieza con '_' )??
		else if (substr($prop_name, 0, 1) == "_")
			return false;
		// es un atributo sin funcion de acceso ?	
		else if (array_key_exists($prop_name, get_object_vars($this))) 
		{
			$this->{$prop_name}=$prop_value;
			return true;
		}// se puede crear un nuevo atributo ??
		else if ($this->_allowNewProperties) 
		{
			$this->_new_properties[$prop_name] = $prop_value ;
			return true;
		}//error de acceso, la propiedad no se puede establecer
		else
			trigger_error(get_class($this)."::__set(\$prop_name=$prop_name,\$prop_value=$prop_value) : No se permite añadir nuevas propiedades ", E_USER_ERROR);
	}
}

/*****************************************************************************
 * clase global HTMLBaseClass: para propiedades comunes HTML de los objetos
 * Creado: lunes 21/02/05
 ****************************************************************************/
class HtmlBaseClass extends BaseClass 
{
  public $id;//String o numero
	public $style;//String, definicion de estilo en linea
	public $class;//String, nombre de la clase css
	public $name;//String, nombre del objeto
	public $disabled;//bool, true: desactivado, false: activado
	public $readonly;//bool, true: solo lectura, false: lectura/modificacion
	
	//se usan dos arreglos para separar los eventos de los atributos de estado o diseño, se podria usar uno solo
	private $_attributes;//array de atributos HTML como bgcolor,style etc, etc
	private $_events;//array de eventos HTML como onclick, onmouseover, etc, etc
	
	//eventos comunes
	public $onclick;//string,
	public $ondblclick;//string,
	public $onmousedown;//string,
	public $onmouseup;//string,
	public $onmouseover;//string,
	public $onmouseout;//string,
	public $onfocus;//string,
	public $onblur;//string,

	//el orden de los parametros es pq se supone se usa con mayor frecuencia de izq a der
  function __construct($style="",$class="",$id="")
	{
  	$this->id=$id;
  	$this->style=$style;
  	$this->class=$class;
  	$this->disabled=false;
  	$this->readonly=false;
  	$this->_attributes=array();
  	$this->_events=array();
	}

  //imprime la/s propiedad/es
  protected function toBrowser()
  {
		//primero va el @class pq el @style puede sobreescribir propiedades usadas en class o en id
 		$this->print_me();
  }
  
 	//esta funcion añade un evento al objeto
 	function set_event($evName,$evScript)
 	{
 		if (($evName=strtolower($evName))!="" && $evScript!="")
 		{
	 		switch ($evName) 
	 		{
	 			case "onclick":
	 			case "ondblclick":
	 			case "onmousedown":
	 			case "onmouseup":
	 			case "onmouseover":
	 			case "onmouseout":
	 			case "onfocus":
	 			case "onblur":
	 			$this->{$evName}=$evScript;//lo asigno a la variable atributo del objeto
	 			return true;//se establecio satisfactoriamente
	 			
	 			default: 
	 			//si esta en _attributes no lo pongo en _events
	 			if (!isset($this->_attributes[$evName]))
	 			{
					$this->_events[$evName]=$evScript;
					return true;
	 			}
	 			else
	 				return false;//se encontro como atributo
	 		}
 		}
 		else 
 			trigger_error(get_class($this)."::set_event(\$evName,\$evScript) : No se permiten eventos vacios ", E_USER_ERROR);
 	}

	//agrega atributos validos HTML o personales(nuevos) al objeto
	//@return true si se agrego false de otro modo
 	function set_attribute($atName,$atValue)
 	{
 		if (($atName=strtolower($atName))!="" && $atValue!="")
 		{
	 		switch ($atName) 
	 		{
	 			//atributos
	 			case "name":
	 			case "id":
	 			case "class":
	 			case "style":
	 			$this->{$atName}=$atValue;//lo asigno a la variable atributo del objeto
	 			break;

	 			case "disabled":
	 			case "readonly":
	 			$this->{$atName}=$atValue?true:false;//lo asigno a la variable atributo del objeto pero solo como bool
	 			return true;

	 			default:
	 			//si esta en events no lo pongo en attributes
	 			if (!isset($this->_events[$atName]))
	 			{
					$this->_attributes[$atName]=$atValue;
					return true;
	 			}
	 			else
	 				return false;//se encontro como evento
	 		}
 		}
		else
			trigger_error(get_class($this)."::set_attribute(\$atName,\$atValue) : No se permiten atributos vacios ", E_USER_ERROR);
 	}

 	protected function print_me()
 	{
 		$add_space="";//separa los diferentes metodos
 		if ($this->name) {echo "name=\"".htmlentities($this->name)."\"";$add_space=" ";}
 		if ($this->id) {echo $add_space."id=\"".htmlentities($this->id)."\"";$add_space=" ";}
 		if ($this->readonly)  {echo $add_space.'readonly="true"'; $add_space=" ";}
 		if ($this->disabled)  {echo $add_space.'disabled="true"'; $add_space=" ";}
 		//primero va el class pq, el style puede sobreescribir propiedades del class
 		if ($this->class){echo $add_space."class=\"".htmlentities($this->class)."\"";$add_space=" ";}
 		if ($this->style) {echo $add_space."style=\"".htmlentities($this->style)."\"";$add_space=" ";}
 		
 		//imprimo los demas atributos no expuestos
 		foreach ($this->_attributes as  $name => $value)
 		{
 			echo $add_space.$name.'="'.htmlentities($value).'"';
 			$add_space=" ";
 		}
 		
 		//imprimo eventos expuestos como atributos del objeto
 		if ($this->onclick) {echo $add_space."onclick=\"".htmlentities($this->onclick)."\"";$add_space=" ";}
 		if ($this->ondblclick) {echo $add_space."ondblclick=\"".htmlentities($this->ondblclick)."\"";$add_space=" ";}
 		if ($this->onmousedown) {echo $add_space."onmousedown=\"".htmlentities($this->onmousedown)."\"";$add_space=" ";}
 		if ($this->onmouseup) {echo $add_space."onmouseup=\"".htmlentities($this->onmouseup)."\"";$add_space=" ";}
 		if ($this->onmouseover) {echo $add_space."onmouseover=\"".htmlentities($this->onmouseover)."\"";$add_space=" ";}
 		if ($this->onmouseout) {echo $add_space."onmouseout=\"".htmlentities($this->onmouseout)."\"";$add_space=" ";}
 		if ($this->onfocus) {echo $add_space."onfocus=\"".htmlentities($this->onfocus)."\"";$add_space=" ";}
 		if ($this->onblur) {echo $add_space."onblur=\"".htmlentities($this->onblur)."\"";$add_space=" ";}
 		
 		//imprimo atributos no expuestos
 		foreach ($this->_events as  $name => $value)
 		{
 			echo $add_space.$name.'="'.htmlentities($value).'"';
 			$add_space=" ";
 		}
 	}
}

/*****************************************************************************
 * Clase option simula un objeto <OPTION />
 * Creado: lunes 21/02/05
 *****************************************************************************/
class HtmlOption extends HtmlBaseClass 
{
	public $value;//String o numero
	public $text;//String o numero
  public $selected;//boolean o cualquier valor equivalente
  
  public function __construct($text,$value=false,$selected=false)
  {
  	$this->selected=$selected;
  	$this->text=$text;
  	$this->value=($value===false)?$text:$value;
  	parent::__construct();
  }
  
  //imprime la opcion al browser y baja  a la siguiente fila
  public function toBrowser()
  {
  	echo "<OPTION ";
  	parent::toBrowser();
		//elimino las comillas dobles de las variables
  	if ((string)$this->text != (string)$this->value) 
  		echo " value=\"".str_replace('"',"", $this->value)."\"";
  	if ($this->selected)
  		echo " selected";
  	echo ">";
   	echo $this->text;
   	echo "</OPTION>";

  }
}

/*****************************************************************************
 * classs optionlist BY GACZ
 * Creado: lunes 21/02/05
 *****************************************************************************/
class HtmlOptionList extends HtmlBaseClass 
{
	/**
	 * Arreglo de objetos tipo HtmlOption
	 * @var HtmlOption
	 */
	public $options;//arreglo que contiene los objetos Options
	public $size;//integer si es mayor que cero es un select tipo lista y no tipo menu
	public $catch_name;//boolean, indica si toma el valor de una variable con nombre this->name desde el browser y lo selecciona
										 //solo sirve cuando el objeto es del tipo menu

	private $_length;//integer me dice la longitud del select, usar la propiedad de abajo
//property $length;//integer me dice la longitud del select

	private $_selectedIndex;//integer me dice la opcion seleccionada, usar la propiedad
//property $selectedIndex;//integer me dice la opcion seleccionada

	public $multiselect;//bool, indica si se podran seleccionar varias opciones al mismo tiempo
	
	public function __construct($name,$size=0,$style="",$class="")
 	{
 		$this->options=array();
 		$this->disabled=false;
 		$this->catch_name=false;
 		$this->_length=0;
		$this->size=$size; 		
	  $this->_selectedIndex=-1;
	  $this->multiselect=false;
	  $this->_events=array();
 		if ($name!="")
 			$this->name=$name;
 		else 
 			trigger_error(get_class($this).": Se esperaba un nombre para el objeto ", E_USER_ERROR);
 		
 		parent::__construct($style,$class);
 	}

 	function __set($prop_name,$prop_value) 
 	{
 		switch ($prop_name)
 		{
	 		case "selectedIndex":
	 			//si esta dentro del rango
	 			if (0 <= $prop_value &&  $prop_value < $this->length)
	 			{
	 				//si NO es multiselect
	 				if (!$this->multiselect)
	 				{
		 				//deselecciono el seleccionado actual
		 				if ($this->selectedIndex!=-1)
		 					$this->options[$this->selectedIndex]->selected=false;
		 					
		 				$this->_selectedIndex=$prop_value;
	 				}
	 				//selecciono el nuevo en caso de multiselect y no multiselect
	 				$this->options[$prop_value]->selected=true;
	 			}
	 			//solo para deseleccionar
	 			elseif ($prop_value==-1)
		 				$this->_selectedIndex=$prop_value;
		 	break;
	 		case "length":
	 		trigger_error(sprintf('Atributo de solo lectura: %s::%s ', get_class($this), $prop_name), E_USER_ERROR);
/*	 		
	 			//en caso de incrementar la longitud, se crean la opciones necesaria para completar
 				//die ("LENGTH={$this->length}  PROPVALUE=$prop_value<br>");
	 			if ($this->length < $prop_value)	
	 			{
	 				for ($i=$this->length; $i < $prop_value; $i++)
	 					$this->options[$this->_length++]=new HtmlOption("");//$this->add_option('');
	 				//$this->_length=$prop_value;
	 			}
	 			//en caso de decremento, se eliminan los que sean necesarios desde el final
	 			elseif($prop_value >= 0)
	 			{
	 				for ($i=$this->_length-1; $i >= $prop_value; $i--)
	 					$this->del_option($i);
	 				$this->_length=$prop_value;
	 			}
*/
	 		break;
			//la propiedad no existe, 'da error tambien al invocar variables definidas'
			//default:	trigger_error(sprintf('Llamada a un atributo no definido: %s::%s ', get_class($this), $prop_name), E_USER_ERROR);
 	 		}
 	}
 	function __get($prop_name) 
 	{
 		switch ($prop_name)
 		{
	 		//si es multiselect siempre se retorna -1
	 		case "selectedIndex":	return ($this->multiselect?-1:$this->_selectedIndex);
	 		case "length": return $this->_length;
	 		default:	return null;
 		}
 	}

	//añade una nueva opcion al select
	//@text string, es el texto visible al usuario
	//@value string, es el valor de la opcion, si no se pone se asigna el valor de text
	//@position integer(0..n), es la posicion en la que se desea insertar la opcion, por defecto se agregan al final
	//@replace bool, si es true se reemplaza la opcion en la posicion indicada, 
	//							 sino se desplazan hacia abajo las opciones a partir de position
 	public function add_option($text,$value=false,$selected=false,$position=-1,$replace=true)
 	{
 		//position fuera de rango, lo agrego al final
 		if ($position < 0 || $position > $this->length)
 			$position=$this->_length++;
 		//si no esta fuera de rango, no se quiere reemplazar y la cantidad de opciones es mayor o igual	a uno (1)
 		elseif (!$replace && $cant=$this->_length++)
 		{
 			//muevo las opciones para hacer espacio a la nueva
 			while ($cant > $position)
 				$this->options[$cant]=$this->options[--$cant];
 		}
 		
 		//si es multiselect, le dejo seleccionar cualquiera
 		if ($this->multiselect)
			$this->options[$position]=new HtmlOption($text,$value,$selected);
		else
		{
			//siempre falso seleccionado
			$this->options[$position]=new HtmlOption($text,$value,false); 
		 //la seleccion se hace mediante la propiedad selectedIndex
		 if ($selected)
			$this->selectedIndex=$position;
		}																															 
 	}
 	public function toBrowser()
 	{
 		echo "<SELECT ";
 		parent::toBrowser();//imprimo las propiedades
 		if ($this->multiselect)
 			echo " multiple";
 		else
 		{
 			//me aseguro que solo UNO este seleccionado
 			//se puede dar el caso que se hayan seleccionado varios a traves de this->options[i]->selected=true
 			$this->clearSelection();
 			if ($this->selectedIndex!=-1)
 			$this->options[$this->selectedIndex]->selected=true;
 		}
 		if ($this->size) 
 			echo " size=".intval($this->size);
 		echo ">\n";
 		//genero las opciones del objeto
 		for ($i=0; $i < $this->length; $i++)
 		{
 			//si el valor de la opcion es el mismo de la variable con el mismo nombre del objeto_select
 			if ($this->catch_name && ($this->options[$i]->value==$GLOBALS[$this->name] || $this->options[$i]->value==$_POST[$this->name]))
 				$this->options[$i]->selected=true;
			
 			$this->options[$i]->toBrowser(); 		
			echo "\n";
 		}
 		echo "</SELECT>";
 	}
 	
 //@resultset objeto, devuelto por la funcion sql()									
 //@aproperties array, key='nbre_propiedad_objeto_option' => value='nbre_clave_resultset' 
 	public function optionsFromResulset(&$resultset,$aproperties)
 	{
 		while (!$resultset->EOF)
 		{
			$ooption=$this->options[$this->_length++]=new HtmlOption("no importa el nombre");
	 		foreach ($aproperties as $property => $akey)
	 		{
	 			$ooption->{$property}=$resultset->fields[$akey];
		 		if ($property=="selected" && $resultset->fields[$akey])
		 				$this->_selectedIndex=$this->_length-1;
	 		}
	 		$resultset->movenext();
 		}
 	}
 	
 	public function setSelected($attr_value,$attr_name="value")
 	{
 		for ($i=0; $i < $this->length; $i++)
 		{
 			if ($this->options[$i]->{$attr_name}==$attr_value)
 			{
 				$this->selectedIndex=$i;
 				if (!$this->multiselect)
 					break;
 			}
 		}
 	}
 	//@inline=true indica q @script es la definicion de un metodo en linea o @inline=false indica q @script es el nombre de una funcion
 	function add_event($name,$script,$inline=true)
 	{
 		parent::set_event($name,$script,$inline);
 	}
 	
 	//esta funcion pone todas las opciones con el atributo selected=false
 	function clearSelection()
 	{
 		foreach ($this->options as $opt)
 			$opt->selected=false;
 	}
}

/**
 * Genera un objeto boton en HTML
 */
class HtmlButton extends HtmlBaseClass
{
	public $text;//texto visible al usuario
	public $type;//default tipo button
//	property $value es un alias para text

	public function __construct($name,$text,$type="button")
	{
		parent::__construct();
		$this->name=$name;
		$this->text=$text;
		$this->type=$type;
	}
	
	public function toBrowser()
	{
		switch (strtolower($this->type)) 
		{
			case "button":
			case "submit":
			case "reset":
				echo "<input type=\"$this->type\" ";
			break;
			default:
				echo "<input type=\"button\" ";
			break;
		}
		echo "value=\"".htmlentities($this->text)."\" ";
		parent::toBrowser();
		echo ">\n";
	}
	//Funcion utilizada para obtener el valor de la propiedad @value
	protected function get_value()
	{
		return $this->text;
	}
	//Funcion utilizada para setear el valor de la propiedad @value
	protected function set_value($prop_value)
	{
		if (is_string($prop_value))
			return $this->text=$prop_value;
		else 
			trigger_error(get_class($this)."::__set_value(\$prop_value=$prop_name) : no es un string", E_USER_ERROR);			
	}
}

/**
 * Genera una imagen HTML
 */
class HtmlImage extends HtmlBaseClass 
{
	/**
	 * Ruta de la imagen NOTA: usar propiedad $objName->src
	 * @var string
	 */
	private  $_src;
	/**
	 * Constructor
	 *
	 * @param string $src Ruta donde se encuentra la imagen
	 * @param string $imgID id de la imagen en caso q sea necesario referenciarla
	 */
	function __construct($src,$imgID="")
	{
		parent::__construct("","",$imgID);
		$this->_src=$src;
		$this->set_attribute("src",$this->_src);
	}
	function toBrowser()
	{
		echo "<img ";
		parent::toBrowser();
		echo "/>\n";
	}
	function get_src()
	{
		return $this->_src;
	}
	function set_src($value)
	{
		$this->_src=$value;
		$this->set_event("src",$this->_src);
	}
}

class HtmlTextBox extends HtmlBaseClass 
{
	public $title;
	public $text;
	function __construct($name,$text="",$id="")
	{
		parent::__construct("","",$id);
		$this->name=$name;
		$this->text=$text;
	}
	function toBrowser()
	{
		echo "<input ";
		parent::toBrowser();
		echo " type=\"text\" ";
		echo "name=\"".htmlentities($this->name)."\" ";
		echo "title=\"".htmlentities($this->title)."\" ";
		echo "value=\"".htmlentities($this->text)."\" ";
		echo ">";
	}
}

class HtmlMenuItem extends HtmlBaseClass 
{
	public $value;//integer
	public $text;//string, puede contener texto html
	
	function __construct($text,$value=false,$id=false)
	{
		parent::__construct();
		if ($value!==false)
			$this->value=(integer)$value;//se requiere un valor entero
		$this->text=$text;
		if ($id!==false)
			$this->id=$id;
	}	
	function toBrowser()
	{
		echo "<LI value=\"".htmlentities($this->value).'" ';
		parent::toBrowser();
		echo ">".$this->text;
		echo "</LI>\n";
	}
	function set_attribute($atName,$atValue)
	{
		if (strtolower($atName)=="value")
			$this->value=$atValue;
		else
			parent::set_attribute($atName,$atValue);
	}
}

class HtmlMenu extends HtmlBaseClass 
{
	public $items; //array de objetos tipo HtmlMenuItem
	private $_length; //integer, indica la cantidad de items que hay
	//eventos
	public $onshow;//string, contiene codigo Javascript
	public $onhide;//string, contiene codigo Javascript
	//eventos comunes a todos los items, a menos que cada item lo redefina
	public $onitemclick;//string, contiene codigo Javascript
	//cuando el mouse pasa sobre el item
	public $onitemover;//string, contiene codigo Javascript
	public $onitemout;//string, contiene codigo Javascript
	
	function __construct($id)
	{
		parent::__construct();
		$this->items=array();
		$this->id=$id;
		$this->_length=0;
	}
	
	function add_item($text,$value,$id=false)
	{
		$this->items[$this->_length++]=new HtmlMenuItem($text,$value,$id);
	}

	protected function get_length()
	{
		return $this->_length;
	}
	function toBrowser()
	{
		echo "<DIV style=\"background-color:white\" "; parent::toBrowser(); echo ">\n";
		foreach ($this->items as $item) 
		{
			if ($item->onclick=="")
				$item->onclick=$this->onitemclick;
			if ($item->onmouseover=="")
				$item->onmouseover=$this->onitemover;
			if ($item->onmouseout=="")
				$item->onmouseout=$this->onitemout;
			
			$item->toBrowser();
		}
		echo "</DIV>\n";
	}
}

class HtmlOptionListBtn extends HtmlOptionList 
{
	public $window;//objeto
	public $button;//objeto HtmlButton
	
	//@obutton es el objeto asociado con el select
	public function __construct($name,&$obutton=false)
	{
		if ($obutton===false)
			$this->button=new HtmlButton($name."_btn",$name."_btn");
		else
			$this->button=$obutton;
		}
}

class JsWindow
{
	public $url;//string
	public $target;//string posibles valores _blank,_media,_parent,_search,_self,_top
	public $center;//bool indica si la ventana se debe mostrar centrada (tiene prioridad sobre topOffset y leftOffset)
	public $leftOffset;//integer posicion de la ventana en pixeles
	public $topOffset;//integer posicion de la ventana en pixeles
	public $width;//integer tamaño en pixeles
	public $height;//integer tamaño en pixeles
	public $fullScreen;//bool
	public $maximized;//bool indica si la ventana se debe mostrar maximizada o no
	public $resizable;//bool indica si se puede cambiar el tamaño de la ventana
	public $titleBar;//bool true=titlebar visible, false=novisible
	public $toolBar;//bool true=visible, false=novisible
	public $menuBar;//bool true=visible, false=novisible
	public $locationBar;//bool true=visible, false=novisible
	public $linkBar;//bool true=visible, false=novisible (el nombre de la propiedad es directories en JScript)
	public $statusBar;//bool true=visible, false=novisible
	public $scrollBars;//bool true=visible, false=novisible
	public $channelMode;//bool true=active, false=noactive
	public $replaceHistory;//bool true=al invocarse remplaza la entrada actual en el History del navegador false=default
	public $varName;//string nombre de la variable JScript que manejará la ventana (si es vacio no se retorna en esa variable)
	public $stringQuote;//char indica con que se deben encerrar los strings
	public $openOnce;//bool indica si debe chequear si la ventana esta abierta en caso afirmativo se abre en la misma ventana
										//para poder hacer este chequeo, la variable @varname debe tener un valor
	private $params;//un arreglo con informacion de los parametros q se pasaran a la ventana
										
	//esta variable se usa para pasarle parametros adicionales a la ventana									
//	public $parameters;//array, la clave es el nombre de la variable y el valor es el valor del parametro

	public function __construct($url="",$target='_blank',$width=640,$height=480)
	{
		$this->url=$url;
//		$this->parameters=array();
		$this->target=$target;
		$this->center=true;
		$this->leftOffset=0;
		$this->topOffset=0;
		$this->height=$height;
		$this->width=$width;
		$this->fullScreen=false;
		$this->maximized=false;
		$this->titleBar=true;
		$this->toolBar=true;
		$this->menuBar=true;
		$this->locationBar=true;
		$this->linkBar=false;
		$this->statusBar=true;
		$this->channelMode=false;
		$this->replaceHistory=false;
		$this->stringQuote="'";
		$this->resizable=true;
		$this->scrollBars=true;
		$this->openOnce=false;
	}
	/**
	 * Agrega o cambia parametros q se pasaran a la url de la ventana
	 *
	 * @param string $param_name el nombre del parametro
	 * @param string $param_value un string o el nombre de una variable JavaScript
	 * @param bool $flag_JsVar indica si el valor del parametro se debe evaluar como una variable JavaScript
	 */
	public function setParam($param_name,$param_value,$flag_JsVar=false)
	{
		if ($param_value=="")
			unset($this->params[$param_name]);
		else
			$this->params[$param_name]=array("jsvar"=>$flag_JsVar,"value"=>$param_value);
	}
	/**
	 * Devuelve un string con formato de parametros en URL
	 *
	 * @return string
	 */
	private function getParamString()
	{
		$str="";
		$separator="?";
		if (is_array($this->params)) {
			foreach ($this->params as $name => $param)
			{
				$str.=$separator."$name=".($param['jsvar']?$this->stringQuote."+".$param['value']:$param['value'].$this->stringQuote);
				$separator="+".$this->stringQuote."&";
			}
		}
		return $str;
	}
	
	//retorna un string con codigo javascript que abre la ventana
	public function open()
	{
		return $this->toString();
	}
	public function toString($withJScriptags=false)
	{
		ob_start();
		$this->toBrowser($withJScriptags);
		$str=ob_get_contents();
		ob_clean();
		return $str;		
	}
	public function toBrowser($withJScriptags=false)
	{
		$comilla=$this->stringQuote;
		if ($withJScriptags)
			echo "<script language='JScript'>";
		
		if ($this->varName && $this->openOnce)
		{
			//si la variable (no esta definida -> abrir ventana nueva
			//o la propiedad closed no esta definida
			//si ambas estan definidas y la propiedad closed==true
			echo "if (typeof($this->varName)=='undefined' || typeof($this->varName.closed)=='undefined' || $this->varName.closed) \n";
			echo "{\n";
		}
			
		if ($this->varName)
			echo $this->varName."=";
			if ($url_parametros=$this->getParamString())
				echo "window.open({$comilla}$this->url"."$url_parametros,{$comilla}$this->target{$comilla},";
			else 
				echo "window.open({$comilla}$this->url{$comilla},{$comilla}$this->target{$comilla},";
		
		echo "{$comilla}";

		//flags
		echo "fullscreen=".($this->fullScreen?1:0);
		echo ",titlebar=".($this->titleBar?1:0);
		echo ",toolbar=".($this->toolBar?1:0);
		echo ",menubar=".($this->menuBar?1:0);
		echo ",location=".($this->locationBar?1:0);
		echo ",directories=".($this->linkBar?1:0);
		echo ",status=".($this->statusBar?1:0);
		echo ",channelmode=".($this->channelMode?1:0);
		echo ",resizable=".($this->resizable?1:0);
		echo ",scrollbars=".($this->scrollBars?1:0);
		
		if (!$this->fullScreen)
		{
			$left=$this->leftOffset;
			$top=$this->topOffset;
			$height=$this->height;
			$width=$this->width;
			
			if ($this->center)
			{
				$top="{$comilla}+parseInt((screen.height-$height)/2.5)+{$comilla}";
				$left="{$comilla}+parseInt((screen.width-$width)/2)+{$comilla}";
			}
			if ($this->maximized)
			{
				$height="{$comilla}+screen.height+{$comilla}";
				$width="{$comilla}+screen.width+{$comilla}";
				$top=0;
				$left=0;
				if ($this->varName=="")
					$maximized=".resizeTo(screen.width,screen.height)";
				else
					$maximized=";".$this->varName.".resizeTo(screen.width,screen.height)";
			}
			//tamaño
			echo ",height=$height,width=$width";

			//posicion
			echo ",left=$left,top=$top";
			
		}
		
		echo "{$comilla},";
		
		echo ($this->replaceHistory?"true":"false");
		echo ")$maximized;";
		
		if ($this->varName && $this->openOnce)
		{
			echo "\n}\n";
			echo "else\n";
			echo "{\n";
			echo "$this->varName.focus();\n";
			echo "}\n";
		}

		
		if ($withJScriptags)
			echo "</script>";
	}
	
}

class XMListReader
{
	public $bg_color_odd;//integer color pares en HEX
	public $bg_color_even;//integer color impares en HEX
	public $bg_color_head;//integer color encabezado en HEX
	public $font_color_head;//integer color fuente de encabezado en HEX
	public $font_color_odd;//integer color fuente de pares en HEX
	public $font_color_even;//integer color fuente de impares en HEX
	public $report; //bool, indica si se debe crear el reporte de busqueda o no
	public $screen_width;//integer, el ancho de la pantalla en pixels
	
	private $_screen_ratio; //float, indica el porcentaje que la tabla ocupara en la pantalla
	private $_col_widths;//array of floats, el indice del arreglo corresponde con la columna 0...n
	private $_src; //string, puede ser un XML o una ruta a un archivo XML
	private $_src_type; //string, puede ser file o XML
	private $_xml; //object, objeto que se crea con la funciones simplexml
	

	//si filepath=1 significa que $XML_src es una ruta al archivo, sino es un string con XML
	//el pasaje de parametros por direccion es simplemente para evitar que se repliquen datos en caso de 
	//que @XML_src sea un string con el XML
	function __construct (&$XML_src,$file=0)
	{
		$this->_col_widths=array();
		$this->screen_width=1024;
		$this->_screen_ratio=1;
		$this->_src=$XML_src;
		$this->_src_type=($file)?"FILE":"XML";
		//$this->bg_color_even=0x0D;//amarillo original
		//$this->bg_color_even=0x1A;//amarillo palido
		$this->bg_color_even=0x2B;//amarillo un poco mas fuerte
		//$this->bg_color_even=0xFFFF99;//amarillo un poco mas fuerte en RGB
		$this->font_color_even='black';
		$this->bg_color_odd='white';
		$this->font_color_odd='black';

		if ($this->_src_type=="XML")
		{
			$this->_xml=simplexml_load_string($XML_src);
//			$domxml = new DOMDocument(XML_VERSION,XML_ENCODING);//para cargarlo como objeto DOM
//			$domxml->loadXML($XML_src);
//			echo $domxml->saveXML();die;
		}
		else
			$this->_xml=simplexml_load_file($XML_src); 
	}
	function setScreenPercent($percentaje=100)
	{
		$this->_screen_ratio=$percentaje/100;
	}
	function setScreenRatio($ratio=1)
	{
		$this->_screen_ratio=$ratio;
	}
	
	function sendHTML()
	{
		$xml=&$this->_xml;//creo un alias de la variable interna
		$screen_width=number_format($this->_screen_ratio*100,2,".","");
		echo "<table width=$screen_width% cellspacing=0 cellpadding=2 class='bordes'>\n";
		echo "<tr id=ma>
						<td align=left colspan=2><b>Total:</b> ".$xml->encabezado->recordcount." ".$xml->encabezado->tipo."</td>
						<td align=left>". ((string)$xml->encabezado->suma!=""?"<b>{$xml->encabezado->suma}</b>":"&nbsp")."</td>
						<td align=right colspan=2>". urldecode(utf8_decode($xml->encabezado->link_pagina))."</td>
				</tr>\n";
		echo "</table>\n";
		echo "<table width='$screen_width%' class='bordes'>\n";
		
		foreach ($xml->titulos->row as $row)
		{
			//crea los titulos de la tabla
			echo "<tr id=mo>\n";
			
			//PARA LOS TITULOS SE IMPRIME SOLO EL PRIMER DATA Y CON UN LINK EN CASO DE QUE TENGA
			foreach ($row->col as $col)
			{
				$data=utf8_decode($col->data);
				$link=utf8_decode($col->data->link);
				$comentario=utf8_decode($col->data->tip);
				echo "<td><a id=mo ";
				if ($link!="") echo "href=\"$link\"";
				echo "><b>$data</b></a></td>\n";
			}
			echo "</tr>\n";
		}
		//crea la lista
		foreach ($xml->lista->row as $row) 
		{
			$link=utf8_decode($row->link);
			$comentario=utf8_decode($row->tip);
	
			echo "<tr ";
			echo atrib_tr();		
			if ($comentario)
				echo " title=\"$comentario\"";
			if ($link)
				echo " onclick=\"document.location.href='$link'\"";
			//creo los eventos de la fila
			foreach ($row->htmlevents->children() as $eventname => $script)
				echo " $eventname=\"$script\"";
			echo ">\n";
			foreach ($row->col as $col) 
			{
				echo "<td ";
				//puede tener multiples data y de diferentes tipos
				foreach ($col->data as $odata)
				{
				$data_type=utf8_decode($odata['type']);
				$link=utf8_decode($odata->link);
				$data=utf8_decode($odata);
				switch ($data_type)
				{
					case 'money':
						$simbol=utf8_decode($odata->simbol);
						$data="<table border=0 cellspacing=2 cellpadding=0 width=100%><tr><td align=left>$simbol</td><td align=right>".formato_money($odata)."</td></tr></table>\n";
					break;
					case 'obj':
						$data=urldecode($odata);//es un objeto y se debe enviar tal cual esta
					break;
					case 'date':
						echo "align=center";
						$data=Fecha($odata);//es tipo fechaDB
					break;
				}
				echo ">\n";
				if ($link!="") 
					echo "<a href='$link'>$data</a>\n";			 	 
				else 
					echo $data;
				}					
				echo "</td>\n";
			}
			echo "</tr>\n";
		}
		echo "</table>\n";
	}
	function sendExcel($filename)
	{
	$max_width[1024]=135.5*$this->_screen_ratio;//ancho visible de la pantalla en px para un Excel
	$max_width[800]=106.5*$this->_screen_ratio;//ancho visible de la pantalla en px para un Excel
	$xml =&$this->_xml;
	require_once LIB_DIR."/excel/class.writeexcel_workbook.inc.php";
	require_once LIB_DIR."/excel/class.writeexcel_worksheet.inc.php";
	$tmpname = tempnam("/tmp", $filename);
	$libro =& new writeexcel_workbook($tmpname);
	$hoja =& $libro->addworksheet('Hoja 1');

	$formatos['encabezado']=& $libro->addformat(array(bold => 1, italic => 0,	color => 'white',	size => 10,	bg_color => 'black', align => 'center',	border=> 1));

//	$formatos[1]->set_text_wrap(); //funka
//	$formatos[0]->set_text_wrap(); //funka
//	$formatos[0]->set_shrink();
//	$libro->set_custom_color(0,);//para colores en RGB

	//formatos pares
	$formatos['text'][0]=& $libro->addformat(array(num_format => '@', top=> 1, bottom=> 1, text_wrap => 1));
	$formatos['float'][0]=& $libro->addformat(array(num_format => '#,##0.00', top=> 1, bottom=> 1));
//	$formatos['date'][0]=& $libro->addformat(array(num_format => ' dd mmmm yyy', top=> 1, bottom=> 1));
	$formatos['money']['$'][0]=& $libro->addformat(array(num_format => '$#,##0.00', top=> 1, bottom=> 1));
	$formatos['money']['U$S'][0]=& $libro->addformat(array(num_format => '[$USD] #,##0.00', top=> 1, bottom=> 1));
	
	//formatos impares
	$formatos['text'][1]=& $libro->addformat(array(num_format => '@', bg_color => $this->bg_color_even, top=> 1, bottom=> 1, text_wrap => 1, size => 10));
	$formatos['float'][1]=& $libro->addformat(array(num_format => '#,##0.00', bg_color => $this->bg_color_even, top=> 1, bottom=> 1));
	$formatos['money']['$'][1]=& $libro->addformat(array(num_format => '$#,##0.00', bg_color => $this->bg_color_even, top=> 1, bottom=> 1));
	$formatos['money']['U$S'][1]=& $libro->addformat(array(num_format => '[$USD] #,##0.00', top=> 1, bottom=> 1, bg_color => $this->bg_color_even));

	$formatos['filtro']=& $libro->addformat(array(num_format => '@', merge => 1, align => 'left', bold => 0, italic => 1 ));
	$formatos['busqueda']=& $libro->addformat(array(num_format => '@', merge => 1, align => 'left', bold => 1));
	
	$i=1;//numero de fila en el excel

	$keyword=utf8_decode($xml->search->keyword);
	$field=utf8_decode($xml->search->field);
	if ($keyword)
	{
		$hoja->write("B$i","Palabra buscada: '$keyword' en $field",$formatos['busqueda']);
		$i++;
	}
	$once=1;
	foreach ($xml->search->filter as $filter)
	{
		if ($once)
		{
			$hoja->write("B$i","Filtros Activos",$formatos['busqueda']);
			$i++;
			$once=0;
		}
		
		$name=utf8_decode($filter->name);
		$value=utf8_decode($filter->value);
		$hoja->write("B$i","$name: $value",$formatos['filtro']);
		$hoja->merge_cells($i, 1, $i, 4);
		$i++;
	}
	
	//FILA/s de titulos
	$j=0;//contador de columnas
	foreach ($xml->titulos->row as $row)
	{
		foreach ($row->col as $col)
		{
			if ($col['xls_skip']=='true')
				continue;
			$hoja->write(chr(65+$j).$i,utf8_decode($col->data),$formatos['encabezado']);//65 equivale a la letra A en ascii
			if ($col['width'])
			{
				ereg("(.*)(px|%)",(string)$col['width'],$width);
				//var_dump($width); die;
				switch ($width[2])
				{
					case 'px':$hoja->set_column($j,$j,$width[1]); //para resolucion 1024x768
					break;
					case '%':$hoja->set_column($j,$j,($width[1]/100)*$max_width[$this->screen_width]); //para resolucion 1024x768
					break;
				}
			}
			$j++;
		}
	}

	$i++;
	//Lista
	foreach ($xml->lista->row as $row) 
	{
		$j=0;
		foreach ($row->col as $col)
		{
			if ($col['xls_skip']=='true')
				continue;
			$text=utf8_decode($col->data);
			$celda=chr(65+$j++).$i;//65 equivale a la letra A en ascii
			switch ((string)$col->data['type']) 
			{
			 	case "money":
			 	$hoja->write($celda,$text,$formatos['money'][(string)$col->data->simbol][$i%2]);
			 	break;
			 	case "integer":
//			 	$hoja->write($celda,$text,$formatos[($i%2).(string)$col->data->simbol]);
			 	break;
			 	case "float":
//			 	$hoja->write($celda,$text,$formatos[($i%2).(string)$col->data->simbol]);
			 	break;
			 	case "date":
			 	$hoja->write($celda,Fecha($text),$formatos['text'][$i%2]);
			 	break;
			 	case "obj":
//			 	$hoja->write($celda,$text,$formatos[($i%2).(string)$col->data->simbol]);
			 	break;
			 	default://tipo text
			 	$hoja->write($celda,$text,$formatos['text'][$i%2]);
		 		break;
			}
		}
		$i++;
	}
	$libro->close();
	if (isset($_SERVER["HTTPS"])) {
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: must-revalidate"); // HTTP/1.1
		header("Cache-Control: post-check=0, pre-check=0", false);
	}

	header("Pragma: ");
	header("Cache-Control: ");
	header("Content-Type: application/xls");
	header("Content-Transfer-Encoding: binary");
	header("Content-Disposition: attachment; filename=\"$filename\"");
	$fh=fopen($tmpname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($tmpname);
	die;		
	}
}

class XMListGenerator
{
	public $busqueda;//objeto tipo S
	public $encabezado;//objeto tipo E
	public $titulos;//objeto tipo XMList
	public $lista;//objeto tipo XMLilst
	public $pie;
	
	function __construct()
	{
		$this->busqueda=new S();
		$this->encabezado=new E();
		$this->titulos=new XMList('titulos');
		$this->lista=new XMList('lista');
	}
	protected function &getXMLDOMDOC()
	{
		$xmlDocument=new DOMDocument(XML_VERSION,XML_ENCODING);//este no permite acentos
		$root=$xmlDocument->createElement('root');
		$root->appendChild($this->busqueda->getDOMnode($xmlDocument));
		$root->appendChild($this->encabezado->getDOMnode($xmlDocument));
		$root->appendChild($this->titulos->getDOMnode($xmlDocument));
		$root->appendChild($this->lista->getDOMnode($xmlDocument));
		$xmlDocument->appendChild($root);
		return $xmlDocument;
	}
	//retorna un string XML
	function &saveXML()
	{
		$xmldoc=$this->getXMLDOMDOC();
		return $xmldoc->saveXML();
	}
	
}

//clase de Search
class S
{
	public $keyword;//string
	public $campo_buscado;//string
	private $filtros=array();
	
	function __construct($keyword='',$campo_buscado='Todos')
	{
		$this->keyword=$keyword;
		$this->campo_buscado=$campo_buscado;
	}
	
	//añade un filtro si este no existe, o lo sobresscribe si tiene el mismo nombre
	function setFilter($name,$value)
	{
		if($name)
			$this->filtros[$name]=$value;
		else
			die('Error el filtro debe tener nombre');
	}
	function &getDOMnode(&$DOMDocumentOwner)
	{
		$parent=&$DOMDocumentOwner;
		$domelement=$parent->createElement('search');
		$tmp=$parent->createElement('keyword');
		$tmp->appendChild($parent->createCDATASection(utf8_encode($this->keyword)));
		$domelement->appendChild($tmp);
		$tmp=$parent->createElement('field');
		$tmp->nodeValue=utf8_encode($this->campo_buscado);
		$domelement->appendChild($tmp);
		
		foreach ($this->filtros as $name => $value)
		{
			$tmp=$parent->createElement('filter');

			$tmp2=$parent->createElement('name');
			$tmp2->nodeValue=utf8_encode($name);
			$tmp->appendChild($tmp2);
			
			$tmp2=$parent->createElement('value');
			$tmp2->nodeValue=utf8_encode($value);
			$tmp->appendChild($tmp2);

			$domelement->appendChild($tmp);
		}
		return $domelement;
	}
}

//clase de Encabezados
class E 
{
	public $recordcount;//integer
	public $tipo_registros;//string
	private $_lkp;//string, linkpagina codificado
	public $suma;//string, de la forma "$1.000,56 U$S500,95"
	
	public function __construct($recordcount=0,$tipo_registros='Registros',$link_pagina=null,$suma=null)
	{
		$this->recordcount=$recordcount;
		$this->tipo_registros=$tipo_registros;
		$this->_lkp=urlencode($link_pagina);
		$this->suma=$suma;
	}
	function __get($property)
	{
		if ($property=='link_pagina')
			return urldecode($this->_lkp);
		else 
			return null;
	}	
	function __set($property,$value)
	{
		if ($property=='link_pagina')
		{
			$this->_lkp=urlencode($value);
			return $value;
		}
		else 
			return null;
	}	
	function &getDOMnode(&$DOMDocumentOwner)
	{
		$parent=&$DOMDocumentOwner;
		$domelement=$parent->createElement('encabezado');

		$tmp=$parent->createElement('recordcount');
		$tmp->nodeValue=$this->recordcount;
		$domelement->appendChild($tmp);

		$tmp=$parent->createElement('tipo');
		$tmp->nodeValue=$this->tipo_registros;
		$domelement->appendChild($tmp);

		$tmp=$parent->createElement('suma');
		$tmp->nodeValue=$this->suma;
		$domelement->appendChild($tmp);

		$tmp=$parent->createElement('link_pagina');
		$tmp->nodeValue=$this->_lkp;
		
		$domelement->appendChild($tmp);
		return $domelement;
	}
}

//clase de Titulos y Listado ambos son de este tipo
class XMList 
{
	private $rows=array();//arreglo de objetos R
	private $lenght=0;//integer
	public $nodeName;//string

	function __construct($nodeName)
	{
		$this->nodeName=$nodeName;
	}
	
	function &addRow($id=null,$link=null,$tip=null)
	{
		$this->rows[$this->lenght++]=new R($id,$link,$tip);
		return $this->rows[$this->lenght-1];
	}
	function getLenght()
	{
		return $this->lenght;
	}
	function &getRow($index)
	{
		if ($this->lenght > $index)
			return $this->rows[$index];
		else
			return null;
	}
	function &getDOMnode(&$DOMDocumentOwner)
	{
		$parent=&$DOMDocumentOwner;
		$domelement=$parent->createElement($this->nodeName);
		
		for ($i=0; $i < $this->lenght ; $i++)
			$domelement->appendChild($this->rows[$i]->getDOMnode($parent));	
			
		return $domelement;
	}
}

//clase de una fila
class R extends XMLBaseClass 
{
	//PARA OBTENER EL NOMBRE DE CLASE DE UN OBJETO
	//get_class($object);
	
	protected $props=array('cols'=>array());//cols es un arreglo de objetos clase C
	private $lenght=0;//integer
	public $id=null;//string
	public $attributes=array();//arreglo de attributos de la row
	
	function __construct($id=null,$link=null,$tip=null)
	{
		$this->id=$id;
		$this->tip=$tip;
		$this->link=$link;
	}
	
	//retorna un objeto de tipo C
	function &addCol($dataValue='',$arrColAttributes=array())
	{
		$this->props['cols'][$this->lenght++]=new C($dataValue,$arrColAttributes);
		return  $this->props['cols'][$this->lenght-1];
	}
	//retorna 
	function getLenght()
	{
		return $this->lenght;
	}
	function &getDOMnode(&$DOMDocumentOwner)
	{
		$parent=&$DOMDocumentOwner;
		$domelement=$parent->createElement('row');
		//agrego el link
		$domelement->appendChild($this->getLinkNode($parent));
		//agrego el tip
		$domelement->appendChild($this->getTipNode($parent));
		//agrego los eventos
		$domelement->appendChild($this->getHtmlEventsNode($parent));
		//seteo los atributos
		$this->setAttributes($domelement);
		if (!($this->id===null))
			$domelement->setAttribute('id',$this->id);

		for($i=0; $i < $this->lenght; $i++)
			$domelement->appendChild($this->props['cols'][$i]->getDOMnode($parent));
		
		return $domelement;
	}
	function __get($property)
	{
		if (array_key_exists($property,$this->props))
		{
			switch ($property)
			{
				case 'cols':
				return $this->props[$property];
			}
		}
		else 	
			return null;//XMLchilds::__get($property);
	}
}

//clase de una columna
class C extends XMLBaseClass 
{
	//atributos conocidas 
	//	bool xls_skip,sirve para que la columna no salga en el excel por ejemplo si data es tipo obj
	public $data=array();//array of objetos tipo D

	function __construct($datavalue='',$arrayAttributes=array())
	{
		$this->data[0]=new D($datavalue);
		if (is_array($arrayAttributes))
			$this->attributes=$arrayAttributes;
	}
	function setColAttribute($name,$value)
	{
		$this->attributes[$name]=$value;
	}
	function &getDOMnode(&$DOMDocumentOwner)
	{
		$parent=&$DOMDocumentOwner;
		$domelement=$parent->createElement('col');
		//agrego el link
		$domelement->appendChild($this->getLinkNode($parent));
		//agrego el tip
		$domelement->appendChild($this->getTipNode($parent));
		//agrego los eventos
		$domelement->appendChild($this->getHtmlEventsNode($parent));
		//seteo los atributos
		$this->setAttributes($domelement);

		foreach ($this->data as $objdata)
			$domelement->appendChild($objdata->getDOMnode($parent));
		
		return $domelement;
	}
}
//clase data
class D extends XMLBaseClass 
{
	public $value;//string codificado con utf-8
	public $datatype='text';//string, otros posibles valores money,obj,integer,float,date(aun no implementado)
	public $simbol='$';//solo si datatype=money de otro modo se ignora

	function __construct($value='',$datatype='text',$simbol='$')
	{
		$this->value=$value;
		$this->datatype=$datatype;
		$this->simbol=$simbol;
	}
	function &getDOMnode(&$DOMDocumentOwner)
	{
		//LA MAYORIA DE LOS STRINGS DEBEN SER CODIFICADOS EN UTF8, 
		//POR QUILOMBOS CON ACENTOS Y CARACTERES RAROS QUE JODE EL XML
		
		$parent=&$DOMDocumentOwner;
		$domelement=$parent->createElement('data');
		
		if ($this->datatype=='obj')
			$domelement->nodeValue=urlencode($this->value);
		else
		  $domelement->appendChild($parent->createCDATASection(utf8_encode($this->value)));		
		//agrego el link
		$domelement->appendChild($this->getLinkNode($parent));
		//agrego el tip
		$domelement->appendChild($this->getTipNode($parent));
		//agrego los eventos
		$domelement->appendChild($this->getHtmlEventsNode($parent));

		if ($this->datatype=='money')
		 $domelement->appendChild($parent->createElement('simbol',$this->simbol));
	 
		//seteo los atributos
		$this->setAttributes($domelement);
		$domelement->setAttribute('type',$this->datatype);

		return $domelement;
	}
}

//clase de propiedades comunes a varias clases hijas
abstract class XMLBaseClass
{
	public $link=null;//string 
	public $tip=null;//string, se debe codificar con utf-8 al exportar como xml
	public $htmlEvents;//array of string, eventos en linea HTML ej. onclick, onmouseover, ondblclick, oncontextpopup, etc, etc, etc
	//atributos conocidas 
	//	bool xls_skip, sirve para que la columna no salga en el excel por ejemplo si data es tipo obj
	//  string type, sirve para saber el tipo de data por ejemplo
	public $attributes;//array of string, atributos especificos dependiendo del tipo de nodo

	//guarda un evento en el arre
	function setHtmlEvent($name,$script)
	{
		$this->htmlEvents[$name]=$script;
	}
	//solo se usa por las clases derivadas
	protected function &getHtmlEventsNode(&$DOMDocumentOwner)
	{
		$parent=&$DOMDocumentOwner;
		$htmlEvents=$parent->createElement('htmlevents');
		if(!is_array($this->htmlEvents))
			$this->htmlEvents=array();
		//creo los hijos de htmlevents
		foreach ($this->htmlEvents as $name => $value)
		{
			$child=$parent->createElement($name);
			$child->appendChild($parent->createCDATASection($value));
			$htmlEvents->appendChild($child);
		}

		return $htmlEvents;//retorno un objeto dom con los eventos unicamente
	}
	protected function &getLinkNode(&$DOMDocumentOwner)
	{
		$parent=&$DOMDocumentOwner;
		$domelement=$parent->createElement('link');
		$domelement->appendChild($parent->createCDATASection($this->link));
		return $domelement;
	}
	protected function &getTipNode(&$DOMDocumentOwner)
	{
		$parent=&$DOMDocumentOwner;
		$domelement=$parent->createElement('tip');
		$domelement->appendChild($parent->createCDATASection(utf8_encode($this->tip)));
		return $domelement;
	}
	protected function setAttributes(&$DOMElement)
	{
		if(!is_array($this->attributes))
			$this->attributes=array();
		foreach ($this->attributes as $nombre => $valor)
			$DOMElement->setAttribute($nombre,$valor);
	}
}

class HtmlDataType
{
	function __construct($datatype="string",$style="")
	{
		switch ($datatype) 
		{
			case float:				
				return number_format($this->data,2,",",".");
			break;
			case integer:				
			case string:				
			case obj:				
				
			break;
		
			default:
				break;
		}
	}
	
}

class HtmlTableData extends HtmlBaseClass
{
	//Posibles tipos de dato:
	private $_validDataTypes=array(string,integer,money,dbdate,object);
	//Posibles formatos para las fechas
	private $_validDateFormats=array(S,SH,SHM,SHM,L,LH,LHM);
	//Posibles valores de las propiedades
	private $_validProps=array(
												simbol,//string valido si el datatype es money
												dateFormat//string valido si datatype es dbdate
												);
	public $_props;//array, las propiedades dependen del tipo de dato almacenado VER la funcion __get() y __set()
	public $data;
	function __construct($data,$dataType="string",$style="",$class="",$id="")
	{
		parent::__construct($style,$class,$id);
		$this->data=$data;
		if ($this->isValidDataType($dataType))
			$this->dataType=$dataType;
		else
			die("Construct: El tipo de dato elegido no es valido");
		return $this;
	}
	function __get($prop)
	{
		if ($this->isValidProp($prop))
			return $this->_props[$prop]	;
		else
			die("__get: La propiedad '$prop' no es valida ");
	}	
	function __set($prop,$value)
	{
		if ($this->isValidProp($prop,$value))
			return $this->_props[$prop]=$value;
		else
			die("__set: la propiedad '$prop' no es valida");
	}
	//checkea que el tipo de dato @datatype sea valido 
	private function 	isValidDataType($datatype)
	{
		return (isset($this->_validDataTypes[$datatype]));
	}
	//checkea que el nombre de propiedad @propName sea valido y el valor @value concuerde con el tipo de datos del objeto
	private function isValidProp($propName,$value=false)
	{
		if (isset($this->_validProps[$propName]))
		{
			//si no se paso el parametro @value
			if ($value===false) return true;
			
			//Checkeo las propiedades que dependen de el tipo de datos
			switch ($prop) 
			{
					//datatype debe ser money
					case simbol:
						if ($this->dataType=="money") 
							return true;				
						else 
							die("isValidProp: dataType no es money, la propiedad simbol no es valida");
					break;
					//datatype debe ser dbdate OR date
					case dateFormat:
					case dateformat:
					if ($this->dataType=='dbdate' && $this->isValidDateFormat($value))
						return true;
					elseif ($this->dataType=='dbdate') 
						die("isValidProp: el formato de fecha no es valido");
					else
						die("isValidProp: dataType no es dbdate OR date, la propiedad dateFormat no es valida");
					break;
			}
		}
		return false;
	}
	
	//checkea que los formatos de tipo date sean validos
	function isValidDateFormat($format)
	{
		return isset($this->_validDateFormats[$format]);
	}
	function toBrowser()
	{
		echo "<TD ".parent::toBrowser().">";
		switch ($this->data) 
		{
			case dbdate:				
				echo "<table><tr><td>".$this->data->simbol."</td><td>".number_format($this->data,2,",",".")."</td></tr></table>";
			break;
			case float:				
				echo number_format($this->data,2,",",".");
			break;
			case integer:				
			case string:				
			case obj:
			default:
				echo $this->data;										
			break;
		}
		echo "</TD>\n";
	}
}

class HtmlTableRow extends HtmlBaseClass
{
	public $rowIndex;//integer, indice de la fila dentro de la tabla 
	public $cells;//array of HtmlTableData objects,
	public $colCount;//integer, indica la cantidad de columnas en la fila
	
	function __construct($colCount)
	{
		$this->cells=new ArrayObject();
		
	}
	
}
class HtmlTableCol extends HtmlBaseClass
{
	public $colIndex;//indice de la columna dentro de la tabla 
	public $cells;//array of HtmlTableData objects,
	
	
}
class HtmlTableHeader extends HtmlBaseClass
{
	
	
}
class HtmlTableFooter extends HtmlBaseClass
{
	
	
}
class HtmlTable extends HtmlBaseClass
{
	public $dataSet;//resultado de una consulta SQL
	public $cols;//array of HtmlTableCol objects	
	public $rows;//array of HtmlTableRows objects	
	
}


//Constantes de tipos definido en la BD
define("SqlInteger",1,false);
define("SqlInt",1,false);
define("SqlFloat",2,false);
define("SqlMoney",2,false);
define("SqlString",3,false);
define("SqlText",3,false);
define("SqlTimestamp",4,false);
define("SqlDate",5,false);
define("SqlBoolean",6,false);
define("SqlBool",6,false);

class SqlParameter extends BaseClass 
{
	public $value;//mixed, valor que se guardara o consultara en la BD
	public $name;//string, nombre del parametro
//property $SqlDataType;	
  private $_SqlDataType;	//un valor de los definidos arriba, default string

  public function _construct($name,$value="",$sqldatatype=SqlString)
  {
  	$this->name=$name;
  	$this->value=$value;
  	$this->SqlDataType=$sqldatatype;
  }
  
	public function get_SqlDataType($prop_name)
	{
		return $this->_SqlDataType;
	}
	public function set_SqlDataType($prop_value)
	{
		switch ($prop_value) 
		{
			case SqlString:
			case SqlInteger:
			case SqlFloat:
			case SqlDate:
			case SqlTimestamp:
			case SqlBool:
			$this->_SqlDataType=$prop_value;
			break;
			default:
				trigger_error(get_class($this)."::SqlDataType : el tipo $prop_value no es valido", E_USER_ERROR);
		}
	}
}

class Collection extends BaseClass 
{
	private $_collect;//arreglo con los objetos de la coleccion
	private $_forceType;//bool, indica que todos los objetos deben ser ser chequeados por tipoS
	private $_collectAllowedTypes;//arreglo que contiene los tipos permitidos en la coleccion
}

class SqlQuery extends BaseClass 
{
	public $query;//string, los parametros tienen se ponen con arroba @nbre_parametro
	public $parameters; //array, de Objetos typo SqlParameter
	private $_parametersLentgh; //integer, indica la cantidad de parametros
	
	public function _construct($q)
	{
		$this->query=$q;
		$this->parameters=array();
		$this->parametersLength=0;
	}
	public function addParameter($name,$value="",$sqldatatype=SqlString)
	{
		
	}
	
	//ejecuta el query del objeto
	public function exec()
	{
		
	}
	
}

//Definen el orden de prioridad de las variables
define("sessChkMode_Default",1,false);
define("sessChkMode_GetParPostSess",1,false);//por default
define("sessChkMode_GetPostParSess",2,false);
//define("GetParSessPost",1);
//define("GetPostSessPar",1);
define("sessChkMode_ParGetPostSess",3,false);
define("sessChkMode_ParPostGetSess",4,false);

define("sessChkMode_PostParGetSess",5,false);
define("sessChkMode_PostGetParSess",6,false);

define("sessChkPOST","post",false);
define("sessChkGET","get",false);
define("sessChkParametros","parametros",false);
define("sessChkSess","sess",false);

//define("GetSessParPost",1);
//define("GetSessPostPar",1);
//define("SessGetParPost",1);
//define("SessGetPostPar",1);
class sessVarGroup extends BaseClass 
{
	protected $id;
	public $chkMode;//debe ser uno de los valores predefinidos, sirve para checkear las prioridades de las variables
	private $_global_vars;//bool, indica si las variables del objeto estan registradas globalmente
	public $autoRegisterVars;//bool, true indica que registrara las variables c/vez que se llama loadVars()
	
	//@id string, es un identificador unico para el grupo de variables
	//@aIniVars array, es el grupo de variables que se crearan inicialmente en el objeto
	public function __construct($id,$aIniVars=null,$chkMode=false)
	{
		$this->id="_ses_groupID_".$id;
		$this->chkMode=$chkMode!=""?$chkMode:sessChkMode_Default;
		//por defecto las variables no se registraran en el entorno
		$this->_global_vars=false;
		if (is_array($aIniVars))
			$this->_new_properties=$aIniVars;
	}
	//guarda las variables en la BD
	public function saveVars()
	{
		//no hace falta serializacion, lo hace la funcion phpss_svars_set()
		//phpss_svars_set($this->id, serialize($this->_new_properties));
		phpss_svars_set($this->id, $this->_new_properties);
	}
	
	//Lee las variables desde la BD
	public function loadVars()
	{	
		global $parametros,$_GET,$_POST;
		$sessArr=phpss_svars_get($this->id);
		switch ($this->chkMode) 
		{
			//Prioridades $_GET -> $parametros -> $_POST -> $sess_group_vars
			case sessChkMode_Default:
			case sessChkMode_GetParPostSess:
			foreach ($sessArr as $key => $value) 
			{
				if ($this->setProp($key,sessChkGET))
					continue;
				elseif ($this->setProp($key,sessChkParametros)) 
					continue;
				elseif ($this->setProp($key,sessChkPOST))
					continue;
				elseif ($this->setProp($key,sessChkSess)) 
					continue;
			}
			break;
		}
		//no hace falta deserializacion, lo hace la funcion phpss_svars_get()
		//$this->_new_properties=unserialize(phpss_svars_get($this->id));
		//$this->_new_properties=phpss_svars_get($this->id);
	}
	//Almacena el valor en una propiedad de nombre @propname, 
	//y toma el valor desde donde se indique en la variable @chkType
	private function setProp($propname,$chkType=sessChkPOST)
	{
		switch ($chkType)
		{
			case sessChkGET:
				if (isset($_GET[$propname]) && ((string)$_GET[$propname])!="")
				{
					$this->_new_properties[$propname]=$_GET[$propname];
					return true;
				}
			break;
			case sessChkParametros:
				if (isset($parametros[$propname]) && ((string)$parametros[$propname]!=""))
				{
					$this->_new_properties[$propname]=$parametros[$propname];
					return true;
				}
			break;
			case sessChkPOST:
				if (isset($_POST[$propname]) && ((string)$_POST[$propname]!=""))
				{
					$this->_new_properties[$propname]=$_POST[$propname];
					return true;
				}
			break;
			case sessChkSess:
				if (isset($sessArr[$propname]) && ((string)$sessArr[$propname]!=""))
				{
					$this->_new_properties[$propname]=$sessArr[$propname];
					return true;
				}
			break;
		}
		return false;

	}
	//guarda y retorna el valor guardado
	public function setVar($varname,$value)
	{
		$this->__set($varname,$value);
	}
	//retorna la variable por direccion
	public function &getVar($varname)
	{
		return $this->__get($varname);
	}
	//Registra las variables del objeto como variables de entorno
	//para accederlas directamente por su nombre y no a traves del objeto
	//NOTA: USAR CON PRECAUCION YA QUE PUEDE SOBREESCRIBIR VARIABLES YA DEFINIDAS
	public function registerVars()
	{
		
	}
	//retorna un arreglo (copia) con todas las variables miembro(publicas) del objeto
	public function varsToArray()
	{
		return $this->_new_properties;	
	}
	//toma todas las variables del arreglo y genera variables miembros del objeto
	//@aSource array, arreglo que contiene las variables a cargar en el objeto
	public function getVarsFromArray($aSource)
	{
		$this->_new_properties=array_merge($this->_new_properties,$aSource);
	}
}

class FormBusqueda extends BaseClass 
{
	/**
	 * El campo de texto de busqueda
	 * @var HtmlTextBox
	 */
	public $textField;
	/**
	 * El combo de seleccion de filtro
	 * @var HtmlOptionList
	 */
	public $filtro;
	/**
	 * EL boton de buscar
	 * @var HtmlButton
	 */
	public $btn;
	/**
	 * El query de consulta
	 * @var string
	 */
	public $sql;
	/**
	 * Los campos por los q se puede ordenar
	 * @var array
	 */
	private $_sortFields;	
	/**
	 * Contiene los links para cada columna a ordenar
	 *
	 * @var array
	 */
	private $_sortLinks;
	/**
	 * indica cual es el campo actual de ordenamiento
	 * @var string
	 */
	private $_currentSortField;
	/**
	 * Indica si el ordenamiento es ascendente
	 * @var bool
	 */
	public $sortAsc;	
	/**
	 * Items por pagina q se mostraran
	 * @var integer
	 */
	public $itemspp;
	/**
	 * Contiene el string de condiciones adicionales
	 * @var string
	 */
	private $_where;
	/**
	 * Cantidad total de paginas
	 * @var integer
	 */
	private $_totalPages;
	/**
	 * Cantidad total de registros
	 * @var integer
	 */
	private $_totalRecords;
	
	
	function __construct()
	{
		$this->filtro=new HtmlOptionList("filter");
		$this->filtro->add_option("Todos los campos","all");
		
		$this->textField=new HtmlTextBox("keyword");
		$this->textField->set_attribute("size",20);
		$this->textField->set_attribute("maxlength",150);

		$this->btn=new HtmlButton("bbuscar","Buscar","submit");
	}
	
	function get_pageLink()
	{
		
	}
	/**
	 * Agrega un campo para ordenar
	 * @param string $fieldName nombre del campo para ordenar
	 * @param bool $asc si true se ordena ascendente sino descendente
	 * @param bool $selectedSort si true se ordenara por este campo
	 */
	function sortBy($fieldName,$asc=true,$selectedSort=false)
	{
		if (!array_key_exists($fieldName,$this->_sortFields))
			trigger_error(get_class($this)."::sortBy(\$fieldName,\$asc,\$selectedSort) \$fieldName=$fieldName no existe en los filtros de ordenamiento");
		$this->_sortFields[$fieldName]=$asc?"asc":"desc";
		if($selectedSort)
			$this->_currentSortField=$fieldName;
	}
	/**
	 * Importa los filtros q se usaban para la vieja funcion form_busqueda()
	 * @param array $filtros
	 */
	function importFilters($filtros)
	{
		if(!is_array($filtros))
			trigger_error(get_class($this)."::importFilters(\$arrayFiltros) Se esperaba un arreglo como entrada");
		foreach ($filtros as $key => $value)
			$this->filtro->add_option($value,$key);
	}
	/**
	 * Importa el arreglo de orden q se usaba para la vieja funcion form_busqueda()
	 * @param array $aSort arreglo de campos por los cuales se podria requerir ordenar
	 * @param bool $up si $up=true entonces se ordena de forma ascendente, sino descendente
	 */
	function importSort($aSort,$up="")
	{
		foreach ($aSort as $key => $value) 
		{
			if ($key=="default")
				$this->_currentSortField=$aSort[$key];
			else 
				$this->_sortFields[$value]="asc";
		}
		//si no tenia ninguno por defecto, selecciono el primer campo
		if ($this->_currentSortField=="")
		{
			$keys=array_keys($aSort);
			$this->_currentSortField=$aSort[$keys[0]];
		}
		$this->_sortFields[$this->_currentSortField]=$this->sortAsc || $up!="" && $up ?"asc":"desc";
	}
	function toBrowser()
	{
//		echo "<input type=hidden name=form_busqueda value=1>";
		echo "<b>Buscar:&nbsp;</b>";
		$this->textField->toBrowser();
		echo "<b>&nbsp;en:&nbsp;</b>";
		$this->filtro->toBrowser();
	}	
}

/* IMPORTANTE LEER SOBRE _GET, _SET y _CALL
----------------------------------------------------------------------------------------------------------
This is the syntax of __get(), __set() and __call():

__get ( [string property_name] , [mixed return_value] )
__set ( [string property_name] , [mixed value_to_assign] )
__call ( [string method_name] , [array arguments] , [mixed return_value] )

__call() seems to work with PHP 4.3.0
----------------------------------------------------------------------------------------------------------
ver http://ar2.php.net/manual/es/language.oop5.overloading.php
		http://ar2.php.net/manual/es/ref.overload.php#overload.examples
Esta extension es experimental en PHP 5
----------------------------------------------------------------------------------------------------------
One thing about __get is that if you return an array it doesn't work directly within a foreach...
class Foo {
  function __get($prop,&$ret) { 
   $ret = array(1,2,3);
   return true;
  }
}
overload ('Foo');

//works
$foo = new Foo;
$bar = $foo->bar;
foreach($bar as $n) {
  echo "$n \n";
}

//doesn't work (bad argument to foreach)
foreach($foo->bar as $n) {
  echo "$n \n";
}
----------------------------------------------------------------------------------------------------------
__get() and __set() solo usan atributos que no existen (no estan definidos)
----------------------------------------------------------------------------------------------------------
Las propiedades no son heredables, se deben redefinir en las clases hijas !!!!!!
----------------------------------------------------------------------------------------------------------
Php 5 has a simple recursion system that stops you from using overloading within an overloading function, 
this means you cannot get an overloaded variable within the __get method, or within any functions/methods 
called by the _get method, you can however call __get manualy within itself to do the same thing. 
----------------------------------------------------------------------------------------------------------
Keep in mind that when your class has a __call() function, it will be used when PHP calls some other magic functions.
This can lead to unexpected errors:
class TestClass {
   public $someVar;

   public function __call($name, $args) {
       // handle the overloaded functions we know...
       // [...]

       // raise an error if the function is unknown, just like PHP would
       trigger_error(sprintf('Call to undefined function: %s::%s().', get_class($this), $name), E_USER_ERROR);
   }
}

$obj = new TestClass();
$obj->someVar = 'some value';

echo $obj; //Fatal error: Call to undefined function: TestClass::__tostring().
$serializedObj = serialize($obj); // Fatal error: Call to undefined function: TestClass::__sleep().
$unserializedObj = unserialize($someSerializedTestClassObject); // Fatal error: Call to undefined function: TestClass::__wakeup().
----------------------------------------------------------------------------------------------------------
== Best Practices for the overload functions

Ok... if we use the overload function.... there are a few recommedations...

- if you extends a class, allways check if the supperclass is overloaded (if
the API function is released). In other case, the program is not able to
determine if the superclass has changed and is overloaded after you wrote
the sub-class. If the API function is not released, allways overload the
class.
- Define different accessor functions for each property. The "__get" and
"__set" functions must redirect the call to the appropiate function. Using
separated functions, you can redefine these functions, and you can preserve
the accessor functions of the super-class.
- Don´t use arrays for define the set of properties of the class. Use
several attributes instead the array.
- Define a super-class with the "__set" and "__get" functions. Don't
redefine the "__set" and "__get" functions. These functions must be redirect
the call to the "get_property" and "set_property" functions, or access the
internal variables instead.
----------------------------------------------------------------------------------------------------------
If you are a perfectionist when it comes to your class interfaces, and you are unable to use overload(), 
there is another viable solution:
Use func_num_args() to determine how many arguments were sent to the function in order to create virtual polymorphism. 
You can create different scenarios by making logical assumptions about the parameters sent. 
From the outside the interface works just like an overloaded function.
The following shows an example of overloading a class constructor:

class Name
{
     var $FirstName;
     var $LastName;

     function Name($first, $last)
     {
           $numargs = func_num_args();
       
           if($numargs >= 2)
           {
                 $this->FirstName = $first;
                 $this->LastName = $last;
           }
           else
           {
                 $names = explode($first);
                 $this->FirstName = $names[0];
                 $this->LastName = $names[1]
           }
     }
   
} 
----------------------------------------------------------------------------------------------------------

*/
?>