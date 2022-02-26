<?php

class form extends element {
	private $tipo = 'vertical';

	public function __construct($att=array()) {
		if (isset($att['tag']) && $att['tag'] !== 'form') throw new RuntimeException('No se puede crear un elemento '.$att['tag'].' con la clase form');
		$att['tag'] = 'form';
		if (isset($att['tipo']) && ($att['tipo'] === 'horizontal' || $att['tipo'] === 'inline')) {
			$this->tipo = $att['tipo'];
			$this->addAttribute('class','form-'.$att['tipo']);
		}
		if(isset($att['tipo'])) unset($att['tipo']);
		$this->addAttribute('role','form');
		parent::__construct($att);
	}
}

?>
