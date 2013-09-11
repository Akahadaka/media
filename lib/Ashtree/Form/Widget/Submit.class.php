<?php
/**
 * 
 */
class Ashtree_Form_Widget_Submit extends Ashtree_Form_Widget {

	public function __invoke($print=TRUE) {
		$dom = $this->dom;

		$inp = $dom->createElement('input');
		$inp->setAttribute('type', 'submit');
		if ($this->name) $inp->setAttribute('id', $this->name);
		if ($this->name) $inp->setAttribute('name', $this->name);
		$inp->setAttribute('class', 'form-control ' . $this->class);
		$inp->setAttribute('value', $this->value);

		return $print ? $dom->saveXML($inp) : $inp;
	}
}