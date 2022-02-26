<?php

class input extends element {
	private $label;
	private $div;
	private $input;

	public function __construct($att) {
		if (!is_a($this,'input') && isset($att['tag']) && $att['tag'] !== 'input')
			throw new RuntimeException('No se puede crear un elemento '.$att['tag'].' con la clase input');
		// Solo sobreescribimos el tag en caso que no exista puede ser una clase hija como select que ya pone un TAG
		if (!isset($att['tag'])) $att['tag'] = 'input'; 
		/*
		 * Creamos el input "real" y le quitamos el atributo label en caso que lo tenga
		 */
		$this->input = new element($att);
		$this->input->addAttribute('class','form-control');
		if($this->input->getAttribute('label')) $this->input->delAttribute('label');
		if (isset($att['label']) && is_string($att['label'])) {
			$this->addLabel($att['label']);
		} else if (isset($att['label']) && is_a($att['label'],'element')) {
			$this->label = $att['label'];
		}
		unset($att['label']);
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
		if ($this->input->getAttribute('id') == null || $this->input->getAttribute('id') === false)
			throw new RuntimeException('No se puede agregar una etiqueta a un elemento sin ID');
		if ($this->label != null) error_log('Sobreescribiendo la etiqueta del elemento '.$this->input->getAttribute('id'));
		$this->label = new element(array(
			'tag' => 'label',
			'_text' => $label,
			'for' => $this->input->getAttribute('id')
		));
	}

	protected function getLabel() {
		if(is_a($this->label,'element')) return $this->label;
	}

	public function render() {
		$this->div->addElement($this->label);
		$this->div->addElement($this->input);
		return $this->div->render();
	}

	public function addElement($element) {
		/*
		 * Aunque por regla los inputs no tienen elementos anidados en el caso de los selects, si aplica y en caso en que en el futuro exista otro
		 * Aqui lo mandamos para el input ya que esta clase realmente no sabe como tener objetos anidados.
		 */
		$this->input->addElement($element);
	}
}

?>
