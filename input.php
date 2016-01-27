<?php

class input extends element {
	private $label;
	private $div;
	public function __construct($att) {
		if (!is_a($this,'input') && isset($att['tag']) && $att['tag'] !== 'input')
			throw new RuntimeException('No se puede crear un elemento '.$att['tag'].' con la clase input');
		// Solo sobreescribimos el tag en caso que no exista puede ser una clase hija como select que ya pone un TAG
		if (!isset($att['tag'])) $att['tag'] = 'input'; 
		$this->addAtributo('class','form-control');
		parent::__construct($att);
		if (isset($att['label']) && is_string($att['label'])) {
			$this->addLabel($att['label']);
		} else if (isset($att['label']) && is_a($att['label'],'element')) {
			$this->label = $att['label'];
		}
		unset($att['label']);
		$this->delAtributo('label');
		$this->div = new element(array(
			'tag' => 'div',
			'class' => 'form-group',
			'id' => false, //quiza debamos de ponerle algo?
		));
	}
	protected function getDiv() {
		return $this->div;
	}
	protected function addLabel($label) {
		if ($this->getAtributo('id') == null || $this->getAtributo('id') === false) throw new RuntimeException('No se puede agregar una etiqueta a un elemento sin ID');
		if ($this->label != null) error_log('Sobreescribiendo la etiqueta del elemento '.$this->getAtributo('id'));
		$this->label = new element(array(
			'tag' => 'label',
			'_text' => $label,
			'for' => $this->getAtributo('id')
		));
	}
	protected function getLabel() {
		if(is_a($this->label,'element')) return $this->label;
	}
	public function render() {
		$this->div->addElement($this->label);
		$this->div->addElement($this);
		return $this->div->render();
	}
}

?>
