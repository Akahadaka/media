<?php
/**
 * This is a class that will help to setup an HTML page
 * @author andrew.nash
 * @version 3.0
 */

class Ashtree_Html_Page
{
	
	/**
	 * @access
	 * @param
	 */
	private static $_instance;
	
	/**
	 * @access
	 * @param
	 */
	private $_params = array();
	
	/**
	 * @access
	 * @param
	 */
	private $_debug;
	
	 
	/**
	 * @access
	 * @param
	 */
	public $doctype = 'html';
	
	/**
	 * @access
	 * @param
	 */
	public $xhtml = 'strict';
	
	/**
	 * @access
	 * @param
	 */
	#public $charset = 'iso-8859-1';
	public $charset = 'utf-8';

	/**
	 * @access public
	 * @param String $title
	 */
	public $title;
	
	/**
	 * @access public
	 * @param String $template
	 */
	public $template;
	
	/**
	 * @access public
	 * @param Array $urlparts
	 */
	public $urlparts;
	
	/**
	 * @access
	 * @param
	 */
	public function __construct($title=NULL)
	{
		$this->_debug = Ashtree_Common_Debug::instance();
		
		$this->invoked = FALSE;
		
		$this->_params['jss'][]      = 'lib/jQuery/jquery.min.js';
		$this->_params['jss'][]      = 'lib/jQuery.UI/ui/minified/jquery-ui.min.js';
		$this->_params['javascript'] = array();
		$this->_params['jquery']     = array();
		$this->_params['css'][]      = 'lib/jQuery.UI/themes/base/minified/jquery-ui.min.css';
		$this->_params['style']      = array();
		$this->_params['element']    = array();
		
        $this->jquery = "$('.message-discard').bind('click', function(){ $(this).closest('li').slideUp(); });";
		
		$this->set_template();
		
		$this->title = ($title) ? $title : "{$this->template} | {$_SERVER['HTTP_HOST']}";
		
	}
	
	/**
	 * @param $key
	 * @return $_params[$key]
	 */
	public function __get($key)
	{
		return array_key_exists($key, $this->_params) ? $this->_params[$key] : FALSE;
	}
	
	/**
	 * @param $key
	 * @param $value
	 */
	public function __set($key, $value)
	{
		switch($key)
		{
			case 'css':
			case 'style':
			case 'jss':
			case 'jquery':
			case 'javascript':
			case 'keyword':
				if (!in_array($value, $this->_params[$key]))
				{
					$this->_params[$key][] = $value;
				}
				break;
			
			case 'theme':
				if ($value != '')
				{
					$this->_params['theme'] = (strpos($value, '/') !== FALSE) ? (($value[strlen($value)-1] == '/') ? $value : "{$value}/") : "themes/{$value}/";
					#$this->_debug->title = "INFO:: Page theme defined '" . $this->_params['theme'] . "'";
					
					//Set the template system to use the same theme
					$tpl = Ashtree_Common_Template::instance();
					$tpl->theme = $this->_params['theme'];
				}
				break;
			
			default:
				$this->_params[$key] = $value;
				break;
		}//switch
	}
	
	/**
	 * @param $key
	 * @return Boolean
	 */
	public function __isset($key)
	{
		return isset($this->_params[$key]);
	}
	
	/**
	 * @param $key
	 */
	public function __unset($key)
	{
		unset($this->_params[$key]);
	}
	
	/**
	 * @return String
	 */
	public function __toString()
	{
		return "";
	}

	/**
	 * @param
	 */
	public function __destruct()
	{
		switch($this->doctype)
		{
			case 'html':
				$this->_print_html_close();
				break;
			case 'xml':
				$this->_print_xml_close();
				break;
			default:
				break;
		}//switch
	}
	
	/**
	 * @param
	 */
	public function __invoke()
	{
		$this->invoked = TRUE;
		switch($this->doctype)
		{
			case 'html':
				$this->_print_html();
				break;
			case 'xml':
				$this->_print_xml();
				break;
			default:
				break;
		}//switch
	}
	
	/**
	 * @param
	 */
	public function invoke()
	{
		return $this->__invoke();
	}

	/**
	 * Singleton instance
	 *
	 * This is used when wanting to maintain the same instance of the class 
	 * and share variables no matter how many functions may call it
	 *
	 * @return new instance
	 */
    public static function instance()
	{
		if (!isset(self::$_instance)) 
		{
			$class = __CLASS__;
			self::$_instance = new $class;
		}

		return self::$_instance;
	}

	/**
	 * This returns the closing tags of an html document
	 * @access private
	 * @param this
	 * @return string
	 */
	private function _print_html_close()
	{
		echo "</body>\n</html>";
	}

	/**
	 * This returns the closing tags of an xml document
	 * @access private
	 * @param this
	 * @return string
	 */
	private function _print_xml_close()
	{
		echo "</document>";
	}
	
	/**
	 * This returns the head of an html document
	 * @access private
	 * @param this
	 * @return string
	 */
	private function _print_html_head_metatags()
	{
		return "<meta http-equiv=\"Content-Type\" content=\"text/html; charset={$this->charset}\" />\n";
	}
	
	/**
	 * This returns the head of an html document
	 * @access private
	 * @param this
	 * @return string
	 */
	private function _print_html_head_javascript()
	{
		$output = "";
		
		//Check if theme defined
		$theme = ($this->theme) ? $this->theme : '';
		
		foreach($this->jss as $script) {
			$skip = FALSE;
			
			$fullpath = $script;
			
			if (!substr_count($fullpath, '//')) {
    			$partpath = (strpos($script, '.js') === FALSE) ? "{$theme}js/{$script}.js" : $script;
    			$fullpath =  Ashtree_Common::get_real_path($partpath, TRUE);
    			$this->_debug->log("INFO", "Including script '{$fullpath}'");
			
			
    			if (!file_exists($fullpath)) {
    				$this->_debug->status('ABORT');
    				
    				$partpath = "js/{$script}.js";
    				$fullpath = ASH_BASEPATH . $partpath;
    				$this->_debug->log("INFO", "Including script '{$fullpath}'");
    			
    				if (!file_exists($fullpath)) {
    				    $skip = TRUE;
    				} else {
    				    $this->_debug->clear();
    				}
    			}
			}
			
			if ($skip) {
				$this->_debug->status('ABORT');
			} else {
				$this->_debug->status('OK');
				$fullpath = (substr_count($fullpath, '//')) ? $fullpath : Ashtree_Common::get_real_path($fullpath);
			    $output .= "<script type=\"text/javascript\" src=\"{$fullpath}\"></script>\n";
			}
		}//foreach
		
				
		foreach($this->element as $script) {
			$output .= "{$script}\n";
		}//foreach
		
		$output .= "<script type=\"text/javascript\">\n";
		$output .= "\t\$(document).ready(function(){\n";
		foreach($this->jquery as $script) {
			$output .= "\t\t{$script}\n";
		}//foreach
		
		$output .= "\t});\n\n";
		
		foreach($this->javascript as $script) {
			$output .= "\t{$script}\n";
		}//foreach
		
		$output .= "</script>\n";
		
        return $output;
	}
		
	/**
	 * This returns the head of an html document
	 * @access private
	 * @param this
	 * @return string
	 */
	private function _print_html_head_css()
	{
		$output = "";
		
		//Check if theme defined
		$theme = ($this->theme) ? $this->theme : '';
		
		foreach($this->css as $style) {
			$skip = FALSE;
			
			$partpath = (strpos($style, '.css') === FALSE) ? "{$theme}css/{$style}.css" : "/{$style}";
			$fullpath = Ashtree_Common::get_real_path($partpath, TRUE);
			$this->_debug->log("INFO", "Including stylesheet '{$fullpath}'");
			
			if (!file_exists($fullpath)) {
				$this->_debug->status('ABORT');
				
				$partpath = "css/{$style}.css";
				$fullpath = ASH_BASEPATH . $partpath;
				$this->_debug->log("INFO", "Including stylesheet '{$fullpath}'");
			
				if (!file_exists($partpath)) {
				    $skip = TRUE;
				} else {
				    $this->_debug->clear();
				}
			}
			
			if ($skip) {
				$this->_debug->status('ABORT');
			} else {
				$this->_debug->status('OK');
				$fullpath = Ashtree_Common::get_real_path($fullpath);
				$output .= "<link type=\"text/css\" media=\"screen\" rel=\"stylesheet\" href=\"{$fullpath}\" />\n";
			}
			
		}//foreach
		
		$output .= "<style type=\"text/css\">\n";
		
		foreach($this->style as $style) {
			$output .= "\t{$style}\n";
		}//foreach
		
		$output .= "</style>\n";
		
		return $output;
	}
		
	/**
	 * This returns the head of an html document
	 * @access private
	 * @param this
	 * @return string
	 */
	private function _print_html_head()
	{
		$output = "<head>\n";
		$output .= "<title>{$this->title}</title>\n";
		$output .= $this->_print_html_head_metatags();
		$output .= $this->_print_html_head_javascript();
		$output .= $this->_print_html_head_css();
		$output .= "</head>\n<body>\n";
		
		return $output;
	}
	
	/**
	 * This outputs the layout of the skeleton for an HTML document
	 * @access private
	 * @param this
	 * @return echo string
	 */
	private function _print_html()
	{
		switch($this->xhtml)
		{
			case 'strict':
				$this->output = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
				break;
			case 'transitional':
				$this->output = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
				break;
			default:
				break;
		}//switch
		
		$this->output .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
		$this->output .= $this->_print_html_head();
		
		echo $this->output;
	}
	
	
	/**
	 * This outputs the layout of the skeleton for an XML document
	 * @access private
	 * @param this
	 * @return echo string
	 */
	private function _print_xml()
	{
	
	}
	
	/**
	 * print_template
	 * @access
	 * @param
	 * @return
	 */
	public function print_template($filename)
	{
		Ashtree_Common_Template::write($filename);
	}
	
	/**
	 * get_template
	 * @access
	 * @param
	 * @return
	 */
	public function get_template($filename)
	{
		$tpl = Ashtree_Common_Template::instance();
		
		return $tpl->include_template($this->set_template($filename));
		
	}

	/**
	 * set_template
	 * @access
	 * @param
	 * @return
	 */
	public function set_template($template=NULL)
	{
	    
		if ($template) {
		    
		    $this->template = $template;
		} else if (isset($_GET['$page']) && ($_GET['$page'] != '')) {
		    $url = @explode('/', $_GET['$page']);
		    $this->template = array_shift($url);
		    $this->urlparts = $url;
		} else {
		    $this->template = 'home';
		}

		// Create a template name
		// that can be used for convenience in templates
		$tpl = Ashtree_Common_Template::instance();
		$tpl->template = strtolower(str_replace('-', '_', $this->template));
		$tpl->pagename = ucwords(str_replace('-', ' ', $this->template));
		
		return $this->template;
	}
	
	/**
	 * set_template
	 * @access public
	 * @param [String $theme]
	 * @return String
	 */
	public function set_theme($theme=NULL)
	{	
		$tpl = Ashtree_Common_Template::instance();
		
		// First check if a theme has been specified
		// by the user in some way
		$this->theme = 'wireframe';
		if (isset($_GET['$theme']) && ($_GET['$theme'] != '')) $this->theme = $_GET['$theme'];
		else if (isset($theme))                                $this->theme = $theme;
		else if (defined('ASH_SITE_THEME'))                   $this->theme = ASH_SITE_THEME;
		$this->_debug->log("INFO", "Setting theme to {$this->theme}");

		// Once a theme has been established 
		// check the the files exist
		// otherwise fall back on the wireframe
		if (is_dir(ASH_ROOTPATH . $this->theme)) {
		    $tpl->theme = $this->theme;
			$this->_debug->status("OK");
		} else {
		    $tpl->theme = $this->theme = 'wireframe';
			$this->_debug->status("FAILURE");
			$this->_debug->log("INFO", "Reverting theme to {$this->theme}");
			$this->_debug->status("OK");
		}

		include(ASH_ROOTPATH . $this->theme . 'index.php');

		return $this->theme;
	}
	
	/**
	 * This returns the year
	 * based on the the development of the sites first year
	 * so if the site has been around for 3 years it returns
	 * 20xx-20yy
	 * @access public
	 * @param [int $from]
	 * @return String
	 */
	public function set_year($from=NULL)
	{
	    $tpl = Ashtree_Common_Template::instance();
	    
	    $this_year = date('Y');
	    
		return $tpl->year = (isset($from) && ($from <  $this_year)) ? "{$from}-{$this_year}" : $this_year;
	}

	/**
	 * include_binaries
	 * @access
	 * @param
	 * @return
	 */
	public function include_binaries($filename)
	{
		$filename = (substr_count($filename, '.php')) ? Ashtree_Common::get_real_path($filename, TRUE) : ASH_BASEPATH . "bin/{$filename}.php";
		$this->_debug->log("INFO", "Include binary '{$filename}'");
		
		if (file_exists($filename))
		{
		    $this->_debug->status('OK');
			include($filename);
			return TRUE;
		}
		$this->_debug->status('FAILURE');
		return FALSE;
	}
	
	
	/**
	 * include_database_binaries
	 * @access
	 * @param
	 * @return
	 */
	public function include_database_binaries($from_connection=NULL, $from_nodename=NULL)                   //Line comments
	{
		$connection = ($from_connection) ? $from_connection : ((isset($this->session)) ? $this->session : NULL);
		$nodename = ($from_nodename) ? $from_nodename : ((isset($this->css[1])) ? $this->css[1] : 'index');
		
		$base = explode('/', getcwd());
		
		$mysql = new Ashtree_Common_Connection($connection);
		$mysql->sanitize($from_nodename, 'page');
		$mysql->sanitize(array_pop($base), 'base');
		$mysql->query = "
			SELECT 
				data.data
			FROM {$mysql->prefix}nodedata data
				LEFT JOIN {$mysql->prefix}nodeinfo info ON info.id = data.id
			WHERE info.name = {$mysql->page}
			AND info.base = {$mysql->base}
		";
		$mysql->invoke();
		if ($mysql)
		{
			#$this->_debug->title = "INFO:: Including binaries from database: {$nodename}";
			$code = 'echo "';
			$code .= str_replace(array('<?php', '?>'), array('";','echo "'), $mysql->recordset[0]['data']);
			$code .= '";';
			eval($code);
			return TRUE;
		}
		#$this->_debug->title = "FAILURE:: No database binaries named '{$nodename}' found in '{$connection}'";
		return FALSE;
	}
	
	/**
	 * Copy and paste me
	 * @access
	 * @param
	 * @return
	 */
	public function element($tag, $type, $content, $id=NULL)
	{
		$id = (isset($id) && ($id != "")) ? " id=\"{$id}\"" : "";
		$this->_params['element'][] = <<<SCRIPT
	   		<{$tag}{$id} type="{$type}">
				{$content}
	   		</{$tag}>
SCRIPT;
	}

	/**
	 * Copy and paste me
	 * @access
	 * @param
	 * @return
	 */
	public function copyAndPasteMe()
	{
	
	}


}