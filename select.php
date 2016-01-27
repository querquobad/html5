<?php

class select extends input {
	private $elementos;

	public function __construct($att) {
		if (isset($att['tag']) && $att['tag'] !== 'select')
			throw new RuntimeException('No se puede crear un elemento '.$att['tag'].' con la clase select');
		$att['tag'] = 'select';
		parent::__construct($att);
		if (isset($att['options']) && is_array($att['options'])) {
			try {
				foreach($att['options'] as $key_actual => $option_actual) $this->addElement(new element(array(
					'tag' => 'option',
					'value' => $key_actual,
					'_text' => $option_actual['pais'],
					'selected' => $option_actual['selected']
				)));
			} catch (Exception $e) {
				error_log($key_actual.' => '.$option_actual);
			}
			unset($att['options']);
		}
	}
}

?>
