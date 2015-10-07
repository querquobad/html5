<?php

class element {
	private $tag;
	private $atributos = array();
	private $id;
	static $num_element;
	private $clases = array();
	private $styles = array();

	function __construct($atts = array()) {
		foreach($atts as $att => $valor) {
			if ($att == 'tag') {
				$this->tag = $valor;
				continue;
			}
			$this->addAtributo($att,$valor);
		}
		self::$num_element++;
		if (!isset($this->atributos['id'])) $this->atributos['id'] = $this->tag.'_'.self::$num_element;
	}

	public function addAtributo($att,$valor) {
		if ($att == 'class') $this->addClass($valor);
		else if ($att == 'style') $this->addStyle($valor);
		else {
			if (array_key_exists($att,$this->atributos))
				error_log("Sobreescribiendo el valor del atributo $att"); //throw new RuntimeException('Ese atributo ya existe');
			$this->atributos[$att] = $valor;
		}
	}

	private function addClass($valor) {
		if (is_string($valor)) $valor = explode(' ',$valor);
		if (!is_array($valor)) throw new RuntimeException('El argumento de addClass no es un arreglo, o cadena delimitada por espacios');
		foreach ($valor as $el_valor) if (!in_array($el_valor,$this->clases)) $this->clases[] = $el_valor;
	}

	private function addStyle($valor) {
		if (!is_array($valor)) throw new RuntimeException('El argumento de addStyle no es un arreglo');
		foreach ($valor as $llave => $value) {
			if (array_key_exists($llave,$this->styles)) error_log('Sobreescribiendo el valor del estilo '.$llave);
			$this->styles[$llave] = $value;
		}
	}

	public function render() {
		$retval = '<'.$this->tag;
		foreach ($this->atributos as $att => $valor) {
			if (is_bool($valor)) {
				if ($valor === true) $retval .= ' '.$att;
			} else {
				$retval .= ' '.$att.'="'.$valor.'"';
			}
		}
		$retval .= '>';
		//Aqui se deben renderear todos los que estan contenidos
		$retval .= '</'.$this->tag.'>';
		return $retval;
	}
}

?>