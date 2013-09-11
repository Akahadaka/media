<?php 

/**
 * 
 */
class Ashtree_Html_Table {
	
	private $dom;
	private $toolb;
	private $title;
	private $headc;
	
	private $thead;
	private $tbody;
	private $tfoot;
	
	public $id;
	public $width = '100%';
	public $class;
	public $border = 1;
	public $padding = 2;
	public $spacing = 4;
	
	/**
	 * 
	 */
	public function __construct() {
		$this->dom = new DOMDocument();
		
		$this->toolb = $this->dom->createElement('tr');
		$this->toolb->setAttribute('class', 'toolbar');
		
		$this->thead = $this->dom->createElement('thead');
		$this->tbody = $this->dom->createElement('tbody');
		$this->tfoot = $this->dom->createElement('tfoot');
		
	}
	
	/**
	 *
	 */
	public function setHead($rowdata) {
		$dom = $this->dom;
		$this->title = $dom->createElement('tr');
		$this->title->setAttribute('class', 'titlebar');
		foreach($rowdata as $celldata) {
			$th = $dom->createElement('th');
			$th->nodeValue = $celldata;
			$this->title->appendChild($th);
		}
		$this->headc = count($rowdata);
	}
	
	/**
	 *
	 */
	public function setBody($rowdata) {
		$dom = $this->dom;
		$tr = $dom->createElement('tr');
		foreach($rowdata as $celldata) {
			$td = $dom->createElement('td');
			if (is_object($celldata)) {
				$td->appendChild($celldata);
			} else {
				$td->nodeValue = $celldata;
			}
			$tr->appendChild($td);
		}
		$this->tbody->appendChild($tr);
	}
	
	/**
	 *
	 */
	public function setFoot($rowdata) {
		$dom = $this->dom;
		$tr = $dom->createElement('tr');
		foreach($rowdata as $celldata) {
			$th = $dom->createElement('th');
			$th->nodeValue = $celldata;
			$tr->appendChild($th);
		}
		$this->tfoot->appendChild($tr);
	}
	
	/**
	 * 
	 */
	public function __invoke() {
		$table = $this->dom->createElement('table');
		$table->setAttribute('id', $this->id);
		$table->setAttribute('width', $this->width);
		$table->setAttribute('class', $this->class);
		$table->setAttribute('border', $this->border);
		$table->setAttribute('cellpadding', $this->padding);
		$table->setAttribute('cellspacing', $this->spacing);
		$this->thead->appendChild($this->toolb);
		$this->thead->appendChild($this->title);
		$table->appendChild($this->thead);
		$table->appendChild($this->tbody);
		$table->appendChild($this->tfoot);
		$this->dom->appendChild($table);
		return $this->dom->saveXML();
	}
	
	/**
	 * 
	 */
	public function setFilter($default=NULL, $colspan=NULL) {
		if (!isset($this->id)) die('Error, must set $table->id before using Ashtree_Html_Table::setFilter()');
		
		$dom = $this->dom;
		
		$htm = Ashtree_Html_Page::instance();
		$htm->jss = 'lib/Ashtree/Javascript/jquery.quickfilter.js';
		$htm->jquery = "
			$('#{$this->id}-filter').quickfilter('#{$this->id}', 2, 0);
		";
		
		$form = new Ashtree_Form($dom);
		
		$filter = $form->createField('text');
		//$filter->label = 'Quick Filter';
		$filter->class = "{$this->id}-filter";
		$filter->id    = "{$this->id}-filter";
		if (isset($default)) $filter->placeholder = $default;
		
		$td = $dom->createElement('td');
		$td->setAttribute('colspan', (isset($colspan)) ? $colspan : $this->headc);
		$td->appendChild($filter(FALSE));
		$this->toolb->appendChild($td);
	}
	
	/**
	 *
	 */
	public function setButton($url, $title=NULL) {
		$dom = $this->dom;
		
		$td = $dom->createElement('td');
		$td->setAttribute('class', 'table-button');
		$td->appendChild($this->a($url, $title, NULL, FALSE));
		$this->toolb->appendChild($td);
	}
	
	/**
	 * 
	 */
	public function setSortable($options=NULL) {
		$htm = Ashtree_Html_Page::instance();
		
		$options = (isset($options)) ? ", {$options}" : "";
		
		$htm->jss = 'lib/Ashtree/Javascript/jquery.tablesorter.min.js';
		$htm->jquery = "
			$('#{$this->id}').tablesorter({widgets: ['zebra']{$options}});
		";
	}
	
	/**
	 * Make a quick link
	 * @access public
	 * @param String $url
	 * @param String $title
	 * @param String $target
	 * @param Boolean $absolute
	 */
	public function a($url, $title=NULL, $target=NULL, $absolute=TRUE) {
		$dom = $this->dom;
		$a = $dom->createElement('a');
		$a->setAttribute('href', Ashtree_Common::http($url, $absolute));
		$a->setAttribute('target', $target);
		$a->setAttribute('style', 'display:block;text-align:center');
		$a->nodeValue = isset($title) ? $title : Ashtree_Common::http($url, FALSE);
		 
		return $a;
	}
	
}