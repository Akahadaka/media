<?php 
/**
 * 
 */
class Ashtree_Form_Widget_Custom extends Ashtree_Form_Widget {

	public $tag;

	public function __invoke($print=TRUE) {
		$dom = $this->dom;
		$this->name = (isset($this->name)) ? $this->name : $this->id;
		$this->tag  = (isset($this->tag)) ? $this->tag : 'div';
		
		// Field
		$div = $dom->createElement('div');
		$div->setAttribute('class', 'field-wrapper ' . $this->class);

		// Label
		$lbl_wrp = $dom->createElement('div');
		$lbl_wrp->setAttribute('class', 'label-wrapper');

		$lbl = $dom->createElement('label');
		$lbl->setAttribute('for', $this->id);
		$lbl->setAttribute('class', 'label');
		$lbl->nodeValue = $this->label;
		
		// Value
		$inp_wrp = $dom->createElement('div');
		$inp_wrp->setAttribute('class', 'value-wrapper');

		$inp = $dom->createElement($this->tag);
		$inp->nodeValue = '';
		if (isset($this->id)) $inp->setAttribute('id', $this->id);
		if (isset($this->value)) {
			$val = $dom->createDocumentFragment();
			$val->appendXML($this->value);
			$inp->appendChild($val);
		}
		$inp->setAttribute('class', "custom value");
		
		// Output
		$lbl_wrp->appendChild($lbl);
		$inp_wrp->appendChild($inp);
		if (isset($this->label)) $div->appendChild($lbl_wrp);
		$div->appendChild($inp_wrp);
		return $print ? $dom->saveXML($div) : $div;
	}
}