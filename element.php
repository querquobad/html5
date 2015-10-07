<?php

const SELF_CLOSE_ELEMENTS = array('meta','link');

class element {
	private $tag;
	private $atributos = array();
	static $num_element;
	private $clases = array();
	private $styles = array();
	private $elementos = array();
	private $self_close = false;

	function __construct($atts = array()) {
		foreach($atts as $att => $valor) {
			if ($att == 'tag') {
				$this->tag = $valor;
				continue;
			}
			$this->addAtributo($att,$valor);
		}
		if (!isset($this->tag)) throw new RuntimeException('No se definió el TAG');
		if (in_array($this->tag,SELF_CLOSE_ELEMENTS)) $this->self_close = true;
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
		if ($this->self_close) $retval .= '/';
		$retval .= '>';
		if (!$this->self_close) {
			foreach ($this->elementos as $elemento_actual) $retval .= $elemento_actual->render();
			$retval .= '</'.$this->tag.'>';
		}
		return $retval;
	}

	public function addElement($element) {
		if (!is_a($element,'element')) throw new RuntimeException('El elemento agregado no es un \'element\'');
		if ($this->self_close) throw new RuntimeException('Este elemento no puede tener elementos anidados');
		$this->elementos[] = $element;
	}
}

?>
