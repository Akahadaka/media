<?php
/**
 * 
 */
class Ashtree_Form_Widget_Button extends Ashtree_Form_Widget {

	public $href;
	
	public function __invoke($print=TRUE) {
		$dom = $this->dom;

		$inp = $dom->createElement('input');
		$inp->setAttribute('type', 'button');
		if ($this->name) $inp->setAttribute('id', $this->name);
		if ($this->name) $inp->setAttribute('name', $this->name);
		if ($this->value) $inp->setAttribute('value', $this->value);
		if ($this->href) $inp->setAttribute('onclick', "document.location.href='{$this->href}'");
		$inp->setAttribute('class', 'form-control ' . $this->class);

		return $print ? $dom->saveXML($inp) : $inp;
	}
}