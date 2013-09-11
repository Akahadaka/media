<?php 
/**
 * 
 */
class Ashtree_Form_Widget_Checkbox extends Ashtree_Form_Widget {
	
	public $checked;
	
	public function __invoke($print=TRUE) {
		$dom = $this->dom;
	
		// Field
		$div = $dom->createElement('div');
		$div->setAttribute('class', 'field-wrapper ' . $this->class);
	
		// Value
		$inp_wrp = $dom->createElement('span');
		$inp_wrp->setAttribute('class', 'value-wrapper');
	
		$inp = $dom->createElement('input');
		$inp->setAttribute('type', 'checkbox');
		$inp->setAttribute('id', $this->name);
		$inp->setAttribute('name', $this->name);
		$inp->setAttribute('class', 'checkbox value');
		$inp->setAttribute('value', $this->value);
		if ($this->checked) $inp->setAttribute('checked', 'checked');
	
		// Label
		$lbl_wrp = $dom->createElement('span');
		$lbl_wrp->setAttribute('class', 'label-wrapper');
	
		$lbl = $dom->createElement('label');
		$lbl->setAttribute('for', $this->name);
		$lbl->setAttribute('class', 'checkbox');
		$lbl->appendChild($dom->createTextNode($this->label));
	
		// Help
		$hlp_wrp = $dom->createElement('div');
		$hlp_wrp->setAttribute('class', 'help-wrapper');
	
		$hlp = $dom->createElement('span');
		$hlp->setAttribute('for', $this->name);
		$hlp->setAttribute('class', 'help');
		$hlp->appendChild($dom->createTextNode($this->help));
	
		// Output
		$inp_wrp->appendChild($inp);
		$lbl_wrp->appendChild($lbl);
		$hlp_wrp->appendChild($hlp);
		$div->appendChild($inp_wrp);
		$div->appendChild($lbl_wrp);
		$div->appendChild($hlp_wrp);
		return $print ? $dom->saveXML($div) : $div;
	}
}