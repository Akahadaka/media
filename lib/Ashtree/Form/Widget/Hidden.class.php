<?php 
/**
 * 
 */
class Ashtree_Form_Widget_Hidden extends Ashtree_Form_Widget {

	public function __invoke($print=TRUE) {
		$dom = $this->dom;

		// Value
		$inp = $dom->createElement('input');
		$inp->setAttribute('type', 'hidden');
		$inp->setAttribute('id', $this->name);
		$inp->setAttribute('name', $this->name);
		$inp->setAttribute('class', 'hidden value');
		$inp->setAttribute('value', $this->value);

		// Output
		return $print ? $dom->saveXML($inp) : $inp;
	}
}