<?php 
/**
 * 
 */
class Ashtree_Form_Widget_Lat extends Ashtree_Form_Widget {

	public $placeholder;

	public function __invoke($print=TRUE) {
		$dom = $this->dom;

		// Field
		$div = $dom->createElement('div');
		$div->setAttribute('class', 'field-part-wrapper ' . $this->class);

		// Label
		$lbl_wrp = $dom->createElement('div');
		$lbl_wrp->setAttribute('class', 'label-part-wrapper');

		$lbl = $dom->createElement('label');
		$lbl->setAttribute('for', $this->name);
		$lbl->setAttribute('class', 'label');
		$lbl->nodeValue = $this->label;
		
		// Required
		if ($this->validate->required) {
			$lbl_req = $dom->createElement('span');
			$lbl_req->setAttribute('class', 'required');
			$lbl_req->nodeValue = ' *';
			$lbl->appendChild($lbl_req);
		}

		// Value
		$inp_wrp = $dom->createElement('div');
		$inp_wrp->setAttribute('class', 'value-part-wrapper');

		$inp = $dom->createElement('input');
		$inp->setAttribute('type', 'text');
		if (isset($this->id) || isset($this->name)) $inp->setAttribute('id', (isset($this->id)) ? $this->id : $this->name);
		if (isset($this->name)) $inp->setAttribute('name', $this->name);
		if (isset($this->placeholder)) $inp->setAttribute('placeholder', $this->placeholder);
		if (isset($this->readonly)) $inp->setAttribute('readonly', $this->readonly = 'readonly');
		if (isset($this->disabled)) $inp->setAttribute('disabled', $this->disabled = 'disabled');
		if (isset($this->value)) $inp->setAttribute('value', $this->value);
		if (isset($this->help)) $inp->setAttribute('title', $this->help);
		$inp->setAttribute('class', "text value {$this->readonly} {$this->disabled}");
		
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
	
	/**
	 *
	 */
	public function invoke($print=NULL){
		return $this->__invoke($print);
	}
}

/**
 *
*/
class Ashtree_Form_Widget_Lon extends Ashtree_Form_Widget {

	public $placeholder;

	public function __invoke($print=TRUE) {
		$dom = $this->dom;

		// Field
		$div = $dom->createElement('div');
		$div->setAttribute('class', 'field-part-wrapper ' . $this->class);

		// Label
		$lbl_wrp = $dom->createElement('div');
		$lbl_wrp->setAttribute('class', 'label-part-wrapper');

		$lbl = $dom->createElement('label');
		$lbl->setAttribute('for', $this->name);
		$lbl->setAttribute('class', 'label');
		$lbl->nodeValue = $this->label;

		// Required
		if ($this->validate->required) {
			$lbl_req = $dom->createElement('span');
			$lbl_req->setAttribute('class', 'required');
			$lbl_req->nodeValue = ' *';
			$lbl->appendChild($lbl_req);
		}

		// Value
		$inp_wrp = $dom->createElement('div');
		$inp_wrp->setAttribute('class', 'value-part-wrapper');

		$inp = $dom->createElement('input');
		$inp->setAttribute('type', 'text');
		if (isset($this->id) || isset($this->name)) $inp->setAttribute('id', (isset($this->id)) ? $this->id : $this->name);
		if (isset($this->name)) $inp->setAttribute('name', $this->name);
		if (isset($this->placeholder)) $inp->setAttribute('placeholder', $this->placeholder);
		if (isset($this->readonly)) $inp->setAttribute('readonly', $this->readonly = 'readonly');
		if (isset($this->disabled)) $inp->setAttribute('disabled', $this->disabled = 'disabled');
		if (isset($this->value)) $inp->setAttribute('value', $this->value);
		if (isset($this->help)) $inp->setAttribute('title', $this->help);
		$inp->setAttribute('class', "text value {$this->readonly} {$this->disabled}");
		
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
	
	/**
	 * 
	 */
	public function invoke($print=NULL){
		return $this->__invoke($print);
	}
}

/**
 *
*/
class Ashtree_Form_Widget_Geolocation extends Ashtree_Form_Widget {

	public $lat;
	public $lon;
	public $map;
	public $href;
	
	public function __construct($parent=NULL) {
		$this->dom = isset($parent) ? $parent : new DOMDocument();
		
		$this->lat = new Ashtree_Form_Widget_Lat($this->dom);
		$this->lon = new Ashtree_Form_Widget_Lon($this->dom);
	}

	public function __invoke($print=TRUE) {
		$dom = $this->dom;
		
		// Parts
		$this->lat->readonly = $this->readonly;
		$this->lat->disabled = $this->disabled;
		$this->lon->readonly = $this->readonly;
		$this->lon->disabled = $this->disabled;
		
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
		
		if ($this->map) {
			$inp_div = $dom->createElement('div');
			$inp_div->setAttribute('class', 'field-part-wrapper');
			$inp_lbl = $dom->createElement('div');
			$inp_lbl->setAttribute('class', 'label-part-wrapper');
			$inp_lbl->nodeValue = '&nbsp;';
			$inp_map = $dom->createElement('a');
			$inp_map->setAttribute('href', "{$this->href}&lat={$this->lat->name}&lon={$this->lon->name}");
			$inp_map->setAttribute('class', 'dialog modal');
			$inp_map->nodeValue = 'Find on Map';
			$inp_div->appendChild($inp_lbl);
			$inp_div->appendChild($inp_map);
		}

		// Output
		$lbl_wrp->appendChild($lbl);
		$inp_wrp->appendChild($this->lat->invoke());
		$inp_wrp->appendChild($this->lon->invoke());
		if ($this->map) $inp_wrp->appendChild($inp_div);
		$div->appendChild($lbl_wrp);
		$div->appendChild($inp_wrp);
		return $print ? $dom->saveXML($div) : $div;
	}
}