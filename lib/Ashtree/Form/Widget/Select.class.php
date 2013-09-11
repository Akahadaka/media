<?php 
/**
 * 
 */
class Ashtree_Form_Widget_Select extends Ashtree_Form_Widget {
	public $options;
	public $options_keys;
	
	public function __invoke($print=TRUE) {
		$dom = $this->dom;
		$this->id = (isset($this->id)) ? $this->id : $this->name;
	
		// Field
		$div = $dom->createElement('div');
		$div->setAttribute('class', 'field-wrapper ' . $this->class);
	
		// Label
		$lbl_wrp = $dom->createElement('div');
		$lbl_wrp->setAttribute('class', 'label-wrapper');
	
		$lbl = $dom->createElement('label');
		$lbl->setAttribute('for', $this->name);
		$lbl->appendChild($dom->createTextNode($this->label));
		
		// Required
		if ($this->validate->required) {
			$lbl_req = $dom->createElement('span');
			$lbl_req->setAttribute('class', 'required');
			$lbl_req->nodeValue = ' *';
			$lbl->appendChild($lbl_req);
		}
	
		// Value
		$inp_wrp = $dom->createElement('div');
		$inp_wrp->setAttribute('class', 'value-wrapper');
	
		$inp = $dom->createElement('select');
		if (isset($this->id)) $inp->setAttribute('id', $this->id);
		if (isset($this->name)) $inp->setAttribute('name', $this->name);
		if (isset($this->readonly)) $inp->setAttribute('readonly', $this->readonly = 'readonly');
		if (isset($this->disabled)) $inp->setAttribute('disabled', $this->disabled = 'disabled');
		if (isset($this->help)) $inp->setAttribute('title', $this->help);
		$inp->setAttribute('class', "select value {$this->readonly} {$this->disabled}");
	
		foreach($this->options as $option=>$text) {
			$key = ($this->options_keys) ? $option : $text;
			$opt = $dom->createElement('option');
			$opt->setAttribute('value', $key);
			$opt->appendChild($dom->createTextNode($text));
			if ($this->value == $key) $opt->setAttribute('selected', 'selected');
			$inp->appendChild($opt);
		}
	
		// Help
		#$hlp_wrp = $dom->createElement('div');
		#$hlp_wrp->setAttribute('class', 'help-wrapper');
	
		#$hlp = $dom->createElement('span');
		#$hlp->setAttribute('for', $this->name);
		#$hlp->setAttribute('class', 'help');
		#$hlp->appendChild($dom->createTextNode($this->help));
		
		// Add validation
		$this->validate($this->name);
		
		$vld_wrp = $dom->createElement('div');
		$vld_wrp->setAttribute('class', 'validation-wrapper');
		
		$vld = $dom->createElement('label');
		$vld->setAttribute('for', $this->id);
		$vld->setAttribute('generated', 'true');
		$vld->setAttribute('class', 'error');
	
		// Output
		$lbl_wrp->appendChild($lbl);
		$inp_wrp->appendChild($inp);
		$vld_wrp->appendChild($vld);
		$div->appendChild($lbl_wrp);
		$div->appendChild($inp_wrp);
		$div->appendChild($vld_wrp);
		return $print ? $dom->saveXML($div) : $div;
	}
}