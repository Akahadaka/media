<?php 
/**
 * 
 */
class Ashtree_Form_Widget_Date extends Ashtree_Form_Widget {

	public function __invoke($print=TRUE) {
		$dom = $this->dom;
		
		// Javascript
		$htm = Ashtree_Html_Page::instance();
		$htm->jquery = "
			$('.datepicker').datepicker();
      		$('.datepicker').datepicker('option', 'dateFormat', 'yy-mm-dd');
      		$('.datepicker').datepicker('option', 'showAnim', 'slideDown');
		";

		// Field
		$div = $dom->createElement('div');
		$div->setAttribute('class', 'field-wrapper ' . $this->class);

		// Label
		$lbl_wrp = $dom->createElement('div');
		$lbl_wrp->setAttribute('class', 'label-wrapper');

		$lbl = $dom->createElement('label');
		$lbl->setAttribute('for', $this->name);
		$lbl->setAttribute('class', 'label');
		$lbl->nodeValue = $this->label;
		
		// Required
		if ($this->required) {
			$lbl_req = $dom->createElement('span');
			$lbl_req->setAttribute('class', 'required');
			$lbl_req->nodeValue = ' *';
			$lbl->appendChild($lbl_req);
		}

		// Value
		$inp_wrp = $dom->createElement('div');
		$inp_wrp->setAttribute('class', 'value-wrapper');

		$inp = $dom->createElement('input');
		$inp->setAttribute('type', 'text');
		$inp->setAttribute('id', (isset($this->id)) ? $this->id : $this->name);
		$inp->setAttribute('name', $this->name);
		$inp->setAttribute('class', 'date value datepicker');
		$inp->setAttribute('value', $this->value);
		$inp->setAttribute('title', $this->help);

		// Output
		$lbl_wrp->appendChild($lbl);
		$inp_wrp->appendChild($inp);
		$div->appendChild($lbl_wrp);
		$div->appendChild($inp_wrp);
		return $print ? $dom->saveXML($div) : $div;
	}
}