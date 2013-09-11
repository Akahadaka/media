<?php
/**
 * 
 */
class Ashtree_Form {
	
	protected $dom;
	protected $form = array();
	
	public $name;
	public $method       = 'post';
	public $action       = '';
	public $enctype      = '';
	public $format       = 'standard';
	public $semicolons   = false;
	public $autocomplete = true;
	
	public function __construct($parent=NULL) {
		$this->dom = isset($parent) ? $parent : new DOMDocument();
		
		$this->name = str_replace(array('-', '.'), '_', pathinfo($_SERVER['REQUEST_URI'], PATHINFO_FILENAME));
		if (is_numeric($this->name)) $this->name = "item{$this->name}";
		$htm = Ashtree_Html_Page::instance();
		$htm->jss = ASH_LIB . 'Ashtree/Javascript/jquery.validate.min.js';
		$htm->jquery = "
			var {$this->name}_rules = {};
			var {$this->name}_msgs = {};
			
			//New rule for select boxes
			$.validator.addMethod('notEqualTo', function(value, element, arg) {
	            return arg.toLowerCase() != value.toLowerCase();
	        }, 'Value can not be the same');
	        
		";
		
	}
	
	public function __destruct() {
		$htm = Ashtree_Html_Page::instance();
		$htm->jquery = "
		$('form').validate({
			rules: {$this->name}_rules,
			messages: {$this->name}_msgs,
			submitHandler: function(form) {
				$('input[type=submit]').attr('disabled', true);
   				form.submit();
  			}
		});
		";
	}

	public function createFieldset($title) {
		return new Widget_Fieldset($this->dom);
	}
	
	public function createField($field_type) {
		
		$Widget = "Ashtree_Form_Widget_" . ucfirst($field_type);
		return new $Widget($this->dom);
	}
	
	public function createControl($control_type) {
		$Widget = "Ashtree_Form_Widget_" . ucfirst($control_type);
		return new $Widget($this->dom);
	}
	
	public function createEmail(){
		return new Ashtree_Form_Email();
	}
	
	public function addFieldset($fieldset) {
		$this->form['field'][$fieldset->legend] = $field;
	}
	
	public function addField($field) {
		//$this->form['field'][$field->name] = $field;
		$this->form['field'][] = $field;
	}
	
	public function addControl($control) {
		$this->form['control'][] = $control;
	}
	
	public function addEmail() {
		
	}
	
	public function __invoke($print=TRUE) {
		$dom = $this->dom;
		$dom->formatOutput = true;
		
		$frm = $dom->createElement('form');
		if ($this->name) $frm->setAttribute('id', $this->name);
		if ($this->method) $frm->setAttribute('method', $this->method);
		if ($this->action) $frm->setAttribute('action', $this->action);
		if ($this->enctype) $frm->setAttribute('enctype', $this->enctype);
		if (!$this->autocomplete) $frm->setAttribute('autocomplete', 'off');
		foreach($this->form['field'] as $field) {
			$frm->appendChild($field(FALSE));
		}
		
		$ctl = $dom->createElement('div');
		$ctl->setAttribute('class', 'form-controls');
		foreach((array)$this->form['control'] as $control) {
			$ctl->appendChild($control(FALSE));
		}
		
		$frm->appendChild($ctl);
		return $print ? $dom->saveXML($frm) : $frm;
	}
	
}