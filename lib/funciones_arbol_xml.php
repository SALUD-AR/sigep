<?
require_once ("../../config.php");

class Tree extends DomDocument {
    private $xpath;
    function __construct($doc, $ver = "1.0", $encode = "iso-8859-1") {
        parent::__construct($ver, $encode);
        $this->preserveWhiteSpace = false;
//        $this->loadXML($doc);//para leer de un string
        $this->load($doc);//para leer de un archivo
        $this->xpath = new DOMXPath($this);
    }
    
    function AgregarItem($padre,$id,$titulo){
			if ($nodo = $this->BuscarItem($padre)){
				$item = $this->createElement("item");
				$item->setAttribute("id",utf8_encode($id));
				$item->setAttribute("text",utf8_encode($titulo));
				$item->setAttribute("im0",utf8_encode("folderClosed.gif"));
				$item->setAttribute("im1",utf8_encode("folderOpen.gif"));
				$item->setAttribute("im2",utf8_encode("folderClosed.gif"));
				$nodo->appendChild($item);
			}
			else {
				$item = $this->createElement("item");
				$item->setAttribute("id","error");
				$item->setAttribute("text",utf8_encode("ERROR: No se pudo agregar el item '$titulo' a '$padre'"));
				$this->documentElement->appendChild($item);
			}
    }
    
    function ModificarItem($id,$titulo){
			if ($nodo = $this->BuscarItem($id)){				
				$nodo->setAttribute("text",utf8_encode($titulo));				
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
?>