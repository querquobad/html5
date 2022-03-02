<?php

const SELF_CLOSE_ELEMENTS = array('meta','link','input');

class element {
	private $tag;
	private $attributes = array();
	static $num_element;
	private $classes = array();
	private $styles = array();
	private $elements = array();
	private $self_close = false;
	private $text = '';
	private $text_after = '';

	function __construct($atts = array()) {
		foreach($atts as $att => $valor) {
			if (is_a($valor,'element')) {
				$this->addElement($valor);
			} else if (is_string($att)) {
				if ($att === 'tag') { //Loose checking hace que un att => 0 califique aqui
					$this->tag = $valor;
					continue;
				}
				$this->addAttribute($att,$valor);
			}
		}
		if (!isset($this->tag)) throw new RuntimeException('No se definió el TAG');
		if (in_array($this->tag,SELF_CLOSE_ELEMENTS)) $this->self_close = true;
		self::$num_element++;
		if (!isset($this->attributes['id'])) $this->attributes['id'] = $this->tag.'_'.self::$num_element;
	}

	public function addAttribute($att,$valor) {
		if ($att == 'class') $this->addClass($valor);
		else if ($att == 'style') $this->addStyle($valor);
		else if ($att == 'text') $this->addText($valor);
		else if ($att == 'textAfter') $this->appendText($valor);
		else {
			if (array_key_exists($att,$this->attributes))
				error_log("Sobreescribiendo el valor del atributo $att"); //throw new RuntimeException('Ese atributo ya existe');
			$this->attributes[$att] = $valor;
		}
	}

	protected function getAttribute($at) {
		if (!is_string($at)) throw new RuntimeException ('El parámetro de getAttribute no es un string');
		if (isset($this->attributes[$at])) return $this->attributes[$at];
	}

	protected function delAttribute($valor) {
		if(isset($this->attributes[$valor])) {
			unset($this->attributes[$valor]);
		} else {
			throw new RuntimeException('Este elemento no tiene ese atributo');
		}
	}

	private function addClass($valor) {
		if (is_string($valor)) $valor = explode(' ',$valor);
		if (!is_array($valor)) throw new RuntimeException('El argumento de addClass no es un arreglo, o cadena delimitada por espacios');
		foreach ($valor as $el_valor) if (!in_array($el_valor,$this->classes)) $this->classes[] = $el_valor;
	}

	protected function delClass($valor) {
		if(in_array($valor,$this->classes)) {
			foreach($this->classes as $key => $value) {
				if($value === $valor) unset($this->classes[$key]);
			}
		}
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
		foreach ($this->attributes as $att => $valor) {
			if (is_bool($valor)) {
				if ($valor === true) $retval .= ' '.$att;
			} else {
				$retval .= ' '.$att.'="'.$valor.'"';
			}
		}
		if (count($this->classes) > 0) $retval .= ' class="'.implode(' ',$this->classes).'"';
		if (count($this->styles) > 0) {
			$retval .= ' style="';
			foreach ($this->styles as $llave => $estilo_actual) {
				$retval .= $llave.' : '.$estilo_actual.';';
			}
			$retval .= '"';
		}
		if ($this->self_close) $retval .= '/';
		$retval .= '>';
		if (!$this->self_close) {
			/*
			 * Algunas versiones de php antes de la 5.6 no tienen este valor haciendo que htmlentities()
			 * regrese un vacío con caracteres acentuados
			 */
			if (ini_get('default_charset') == "") ini_set('default_charset','UTF-8');
			if (htmlentities($this->text,ENT_HTML5) == "") {
				$retval .= htmlentities(utf8_encode($this->text),ENT_HTML5);
			} else {
				$retval .= htmlentities($this->text,ENT_HTML5);
			}
			foreach ($this->elements as $elemento_actual) $retval .= $elemento_actual->render();
			$retval .= '</'.$this->tag.'>';
		}
		if (htmlentities($this->text_after,ENT_HTML5) == "") {
			$retval .= htmlentities(utf8_encode($this->text_after),ENT_HTML5);
		} else {
			$retval .= htmlentities($this->text_after,ENT_HTML5);
		}
		return $retval;
	}

	public function addElement($element) {
		if (!is_a($element,'element')) throw new RuntimeException('El elemento agregado no es un \'element\'');
		if ($this->self_close) throw new RuntimeException('Este elemento no puede tener elementos anidados');
		$this->elements[] = $element;
		return $this;
	}

	private function addText($text) {// OJO para agregar texto invoca $obj->addAttribute('_text','El texto a ser agregado')
		if (!is_string($text)) throw new RuntimeException('No se puede agregar un texto que no es un string');
		if (is_a($this,'input') && $this->self_close) {
			$this->addLabel($text);
		} else {
			if($this->self_close) throw new RuntimeException('Este elemento no puede tener texto. ('.$this-getAttribute('tag').')');
			$this->text .= $text;
		}
	}

	private function appendText($text) {// OJO para agregar texto invoca $obj->addAttribute('_textAfter','El texto a ser agregado')
		if (!is_string($text)) throw new RuntimeException('No se puede agregar un texto que no es un string');
		if ($this->self_close) throw new RuntimeException('Este elemento no puede tener texto. ('.$this-getAttribute('tag').')');
		$this->text_after .= $text;
	}

	public function getElementByTag($tag) {
		if (!is_string($tag)) throw new RuntimeException('El argumento de getElementByTag no es un string');
		if ($this->tag === $tag) return $this;
		foreach ($this->elements as $elemento_actual) {
			$busqueda = $elemento_actual->getElementByTag($tag);
			if (is_a($busqueda,'element')) return $busqueda;
		}
	}
	public function getElementByID($id) {
		if (!is_string($id)) throw new RuntimeException('El argumento de getElementById no es un string');
		if ($this->attributes['id'] === $id) return $this;
		foreach ($this->elements as $elemento_actual) {
			$busqueda = $elemento_actual->getElementByID($id);
			if (is_a($busqueda,'element')) return $busqueda;
		}
	}
}

?>
