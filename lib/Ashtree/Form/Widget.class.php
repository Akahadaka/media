<?php
/**
 * 
 */
class Ashtree_Form_Widget {
	protected $dom;
	protected $field;
	
	// Input attributes
	public $label;
	public $name;
	public $tag;
	public $value;
	public $class;
	public $help;
	public $readonly;
	public $disabled;
	public $required;
	public $limit = 1;
	
	// Widget extensions
	public $validate;
	public $form;
	
	/**
	 * 
	 */
	public function __construct($parent=NULL) {
		$this->dom = isset($parent) ? $parent : new DOMDocument();
		$this->validate = new Ashtree_Form_Validate();
		$this->form = str_replace(array('-', '.'), '_', pathinfo($_SERVER['REQUEST_URI'], PATHINFO_FILENAME));
		if (is_numeric($this->form)) $this->form = "item{$this->form}";
	}
	
	public function validate($name) {
		$htm = Ashtree_Html_Page::instance();
		$validate = $this->validate->getVars();
		if ($validate) {
			$object = array();
			foreach($validate as $key=>$val) {
				$val = ($val == 1) ? "true" : "\"{$val}\"";
				if ($val) $object[] = "{$key}: {$val}";
			}
			$object_sav = implode(",\n", $object);
				
			$htm->jquery = "
				$.extend({$this->form}_rules, {
					{$name}: {
						{$object_sav}
					}
				});
			";
		}
		
		$messages = $this->validate->getMessages();
		if ($messages) {
			$object = array();
			foreach($messages as $key=>$val) {
				if ($val) $object[] = "{$key}: \"{$val}\"";
			}
			$object_sav = implode(",\n", $object);
			
			$htm->jquery = "
				$.extend({$this->form}_msgs, {
					{$name}: {
						{$object_sav}
					}
				});
			";
		}
		
		
	}
	
	/**
	 * 
	 */
	public function __invoke() {
		$this->dom->saveXML($this->field);
	}
}