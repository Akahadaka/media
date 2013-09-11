<?php
/**
 * Dynamically build up an XHTML compatible tag
 * by creating a tag object
 * and applying attributes
 * and content
 * 
 * @author andrew.nash
 * @version 2.0
 */

class Ashtree_Html_Tag
{
	
	/**
	 * @access private
	 * @param $_params
	 */
	private $_params = array();
	
	/**
	 * @access private
	 * @param $_debug
	 */
	private $_debug;
	
	/**
	 * @access public
	 * $param String $dom
	 */
	public $dom;
	
	/**
	 * @access public
	 * $param String $text
	 */
	public $text;
	
	/**
	 * @access public
	 * $param String $html
	 */
	public $html;

	/**
	 * @param String $dom
	 */
	public function __construct($dom=NULL)               //Line comments
	{
		//$this->_debug = Ashtree_Common_Debug::instance();
		
		if (isset($dom)) {
		    if (!substr_count($dom, '<')) {
		        $this->dom = $dom;
		    } else {
		        $this->_split($dom);
		    }    
		}	
	}
	
	/**
	 */
	public function __get($key)
	{
		switch($key)
		{
			case 'style':
			    $result = '';
				foreach ($this->_params[$key] as $attr=>$value)
				{
					$result .= "{$attr}:{$value};";
				}//foreach
				return $result;
				break;
			case 'class':
				return implode(' ', $this->_params[$key]);
				break;
			default:
				return array_key_exists($key, $this->_params) ? $this->_params[$key] : '';
				break;
		}//switch
			
	}
	
	/**
	 * Magic set has been modified slightly
	 * to allow for continued adding of paramaters
	 * in this case style and class
	 */
	public function __set($key, $value)
	{
		switch($key)
		{
			case 'style':
			    if (substr_count($value, ';')) {
			        foreach(explode(';', $value) as $style) {
			            $this->style = $style;
			        }
			    } else {
    				$attr = explode(':', trim($value));
    				$this->_params['style'][$attr[0]] = $attr[1];
			    }
				break;
				
			case 'class':
				if (!isset($this->_params['class']) || !in_array($value, (array)$this->_params['class']))
				{
					$this->_params['class'][] = $value;
				}
				break;
				
			default:
				if (($value == '') && (isset($this->_params[$key]))) {
				    unset($this->_params[$key]);
				} else if ($value != '') {
				    $this->_params[$key] = $value;   
				}
				break;
		}//switch
	}
	
	/**
	 */
	public function __isset($key)
	{
		return isset($this->_params[$key]);
	}
	
	/**
	 */
	public function __unset($key)
	{
		unset($this->_params[$key]);
	}
	
	/**
	 */
	public function __toString()
	{
		return $this->build();
	}

	/**
	 * Disassemble a DOM element
	 * into its components
	 * namely tag, text/html, attributes
	 * @access private
	 * @param String $dom
	 */
	private function _split($dom)
	{
	    preg_match('/<(\w+)\s*?(.*?)(?:\/>|>)(.*)/s', $dom, $part);
	    
	    $this->dom = $part[1];
	    if ($part[2]) 
	    {
	        preg_match_all('/(\w+=(?:"|\').*?(?:"|\'))/', $part[2], $group);
	        foreach($group[1] as $attr) 
	        {
	            $a = explode('=', str_replace(array('"', "'"), '', $attr));
	            $this->$a[0] = $a[1];
	        }//foreach
        }
        
        if ($part[3]) {
	        $this->html = str_replace("</{$part[1]}>", "", $part[3]);
        }
	}
	
	/**
	 * Build up an HTML Tag from its components
	 * @access public
	 * @return String
	 */
	public function build()
	{
        $dom = (isset($this->dom)) ? $this->dom : 'div';
		$txt = (isset($this->text)) ? strip_tags($this->text) : ((isset($this->html)) ? $this->html : NULL);	
		
		$result = "<{$dom}";
		foreach($this->_params as $attr=>$val) {
		    $result .= " {$attr}=\"{$this->$attr}\"";
		}
		$result .= (isset($txt)) ? ">{$txt}</{$dom}>" : " />";
				
		return $result;
	}
	
	/**
	 * Wrap a tag around some content
	 * @access public
	 * @param String $tagname
	 * @param String $content
	 */
	public static function wrap($content, $tagname)
	{
	    $tag = new Ashtree_Html_Tag($tagname);
	    $tag->html = $content;
	    
	    return $tag->build();
	}
	
	/**
	 * Add a class to an existing tag
	 * @access public
	 * @param String $content
	 * @param String $classname
	 */
	public static function addClass(&$content, $classname)
	{
	    $tag = new Ashtree_Html_Tag($content);
        $tag->class = $classname;
        
	    return $content = $tag->build();
	}
	
	/**
	 * Add some content 
	 * at the end of the inside of the parent-most tag 
	 * @access public
	 * @param String $content
	 * @param String $source
	 */
	public static function append($content, $source)
	{
	    return preg_replace('/(<\/\w+>)$/', $content . '${1}', $source);
	}
	
	/**
	 * Add some content 
	 * at the beginning of the inside of the parent-most tag 
	 * @access public
	 * @param String $content
	 * @param String $source
	 */
	public static function prepend($content, $source)
	{
	    return preg_replace('/^(<.*?>)/', '${1}' . $content, $source);
	}
	
	/**
	 * Make a quick link
	 * @access public
	 * @param String $url
	 * @param String $title
	 * @param String $target
	 * @param Boolean $absolute
	 */
	public static function a($url, $title=NULL, $target=NULL, $absolute=TRUE)
	{
	    $tag = new Ashtree_Html_Tag('a');
	    $tag->href = Ashtree_Common::http($url, $absolute);
	    $tag->text = isset($title) ? $title : Ashtree_Common::http($url, FALSE);
	    $tag->target = $target;
	    
	    return $tag->build();
	}
	
	
}