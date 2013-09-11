<?php

class Plugin_Jquery_Anymenu {
	
	private $menu;
	private $ul;
	
	private static $active_base;
	private static $active_item;

	/**
	 * Turn all list item menus into jquery menus
	 */
	public static function Activate(){
		$htm = Ashtree_Html_Page::instance();
		$tpl = Ashtree_Common_Template::instance();

		$htm->jss = ASH_PLUGINS . 'jquery.anymenu/jquery.anymenu.js';
		$htm->css = ASH_PLUGINS . 'jquery.anymenu/jquery.anymenu.css';
		$htm->jquery = "
			$('.menu > ul').anymenu({
				'type'    : 'dropdown',
				'effects' : 'slide',
				'duration': 'fast'
			});
		";
		self::$active_base = (ASH_BASENAME != ASH_ROOTNAME) ? str_replace(array(ASH_ROOTNAME, '/'), array('', '_'), ASH_BASENAME) : "";
		self::$active_item = 'anymenu_' . self::$active_base . $htm->template;
	}

	/**
	 *
	 */
	public function __construct($name){
		$this->menu = new DOMDocument();
		$this->ul = $this->menu->createElement('ul');
		$this->ul->setAttribute('class', "menu anymenu {$name} clearfix");
		$this->ul->setAttribute('id', $name);
		return $this->menu;
	}

	/**
	 *
	 */
	public function createItem($title, $href=null, $template=null, $subpage=null){
		if (isset($subpage)) {
			$template = 'anymenu_' . (($subpage == '') ? ((($template == '') ? (($href == '') ? 'home' : str_replace('/', '_', $href)) : str_replace('/', '_', $template))) : str_replace('/', '_', $template.'_'.$subpage));
			$request_uri = 'anymenu_' . str_replace('/', '_', substr_replace($_SERVER['REQUEST_URI'], '', 0, strlen(ASH_ROOTNAME)));
			$active = ($request_uri == $template) ? ' active' : '';
			//echo dump($template . ' : ' . $request_uri . ' : ' . $active);
		} else {
			$template = 'anymenu_' . (($template == '') ? (($href == '') ? 'home' : str_replace('/', '_', $href)) : str_replace('/', '_', $template));
			$active = (self::$active_item == $template) ? ' active' : '';
			//echo dump(self::$active_item . ' : ' . $template . ' : ' . $active);
		}
		
		
		$li = $this->menu->createElement('li');
		$a = $this->menu->createElement('a');
		$a->setAttribute('href', ASH_ROOTNAME.$href);
		$a->setAttribute('class', $template.$active);
		$a->nodeValue = $title;
		$li->appendChild($a);
		return $li;
	}

	/**
	 *
	 */
	public function addItem($item){
		#$item = new Plugin_Jquery_Anymenu($this->menu);
		$this->ul->appendChild($item);
	}

	/**
	 *
	 */
	public function __invoke(){
		$this->menu->appendChild($this->ul);
		return $this->menu->saveXML();
	}
}