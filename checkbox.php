<?php

class checkbox extends element {
	private $inline = false;
	private $div;
	private $opts = array();

	public function __construct($att) {
		if (isset($att['tag']) && $att['tag'] !== 'input')
			throw new RuntimeException('No se puede crear un elemento '.$att['tag'].' con la clase checkbox');
		if (isset($att['type']) && $att['type'] !== 'checkbox')
			throw new RuntimeException('No se puede crear un input del tipo '.$att['type'].' con la clase checkbox');
		if (!isset($att['options']) || !is_array($att['options'])) throw new RuntimeException('Opciones inválidas para checkbox');
		foreach($att['options'] as $key => $val) {
			$this->opts[] = new element(array(
				'tag' => 'label',
				'_textAfter' => $val,
				new element(array(
					'tag' => 'input',
					'type' => 'checkbox',
					'value' => $key,
				))
			));
		}
		unset($att['options']);
		if (isset($att['inline'])) $this->inline = (bool) $att['inline'];
		unset($att['inline']);
		foreach ($this->opts as $opt_actual) {
			foreach($att as $k => $v) {
				$opt_actual->getElementByTag('input')->addAtributo($k,$v);
			}
		}
	}

	public function render() {
		if ($this->inline || count($this->opts) == 1) {
			/*
			 * Si nada mas es una opción o especificamos que es inline le agregamos la clase checkbox-inline
			 * a los labels (que en bootstrap son los contenedores de los checkboxes)
			 */
			foreach($this->opts as $opt_actual) $opt_actual->addAtributo('class','checkbox-inline');
		}
		$this->div = new element(array(
			'tag' => 'div',
			'class' => 'checkbox',
			'id' => false
		));
		foreach($this->opts as $op_actual){
			$this->div->addElement($op_actual);
		}
		return $this->div->render();
	}
}

?>
