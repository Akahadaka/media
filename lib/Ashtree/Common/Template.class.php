<?php
/**
 * Use to include template files
 *
 * @package Ashtree_Common_Template
 * @author andrew.nash
 * @version 1.0
 *
 * @change 2012-03-05 andrew.nash Modified included templates to use Ashtree_Common::get_real_path()
 */
class Ashtree_Common_Template
{
	/**
	 * @access private
	 * @var Object $_instance
	 */
	private static $_instance;
	
	/**
	 * @access private
	 * @var Array $_params
	 */ 
	private $_params = array(); 
	
	/**
	 * @access private
	 * @var Object $_debug
	 */
	private $_debug;
	
	/**
	 * @access private
	 * @var String $_template
	 */
	private $_template;

	/**
	 * 
	 */
	public function __construct()
	{
		$this->_debug = Ashtree_Common_Debug::instance();
		
		$this->rootname  = ASH_ROOTNAME;
		$this->rootpath  = ASH_ROOTPATH;
		$this->roothttp  = ASH_ROOTHTTP;
		$this->roothttps = ASH_ROOTHTTPS;
		
		$this->basename  = ASH_BASENAME;
		$this->basepath  = ASH_BASEPATH;
		$this->basehttp  = ASH_BASEHTTP;
		$this->basehttps = ASH_BASEHTTPS;
		
		$this->get = @$_GET;
		$this->post = @_POST;
		$this->files = @_FILES;
		$this->server = @$_SERVER;
		$this->cookie = @$_COOKIE;
		$this->session = @$_SESSION;
		
	}
	
	/**
	 * @access
	 * @param
	 */
	public function __get($key)
	{
		return array_key_exists($key, $this->_params) ? $this->_params[$key] : '';
	}
	
	/**
	 * @access
	 * @param
	 */
	public function __set($key, $value)
	{
		switch($key)
		{
			case 'theme':
				if ($value != '')
				{
					$this->_params['theme'] = (strpos($value, '/') !== FALSE) ? (($value[strlen($value)-1] == '/') ? $value : "{$value}/") : "themes/{$value}/";
					#$this->_debug->title = "INFO:: Template theme defined '" . $this->_params['theme'] . "'";
				}
				break;
			default:
				$this->_params[$key] = $value;
				break;
		}//switch
	}
	
	/**
	 * @access
	 * @param
	 */
	public function __isset($key)
	{
		return isset($this->_params[$key]);
	}
	
	/**
	 * @access
	 * @param
	 */
	public function __unset($key)
	{
		unset($this->_params[$key]);
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
	 * Get_user_defined_vars
	 * @access
	 * @param
	 * @return
	 */
	public function get_user_defined_vars()
	{	
		$vars = (isset($this->defined_variables)) ? $this->defined_variables : $this->_params;
		
		return $vars;
	}


	/**
	 * Set_defined_variables
	 * @access
	 * @param
	 * @return
	 */
	public function set_defined_variables($vars)
	{
		$this->defined_variables = $vars;
	}

	/**
	 * Looks for template variables
	 * inside a given string
	 * and returns the new string
	 * @access public
	 * @param String $text
	 * @param [Array $var_values]
	 * @return String
	 */
	public function string_get_template($text, $var_values=FALSE)
	{
	    // Determine all variables to be replaced with values
	    // Call this before including a file
	    // for the variables to be accessible in the php tags
	    if (!$var_values)
	    {
    		foreach((array)$this->get_user_defined_vars() as $var123=>$val456)
    		{
    		    $$var123 = $val456;
    		    if (is_array($val456)) {
    				foreach($val456 as $k123=>$v456) $var_values["{\${$var123}.{$k123}}"] = $v456;
    			} else {
    				$var_values["{\${$var123}}"] = $val456;
    			}
    		}//foreach
	    }
		
		// Replace any variables in the template with the existing user defined vars
		preg_match_all('/{\$(.*?)}/', $text, $matches);
		foreach((array)$matches[0] as $key=>$value){
			if (isset($var_values[$value])) {
			    $text = str_replace($value, $var_values[$value], $text);
			} else {
			    // Clean up variable where value could not be found
			    $text = str_replace($value, '', $text);
                $this->_debug->log("WARN", "Template variable {$value} is unused");
			}
		}//foreach
		
		// Replace any CMS variables with content from the database
		preg_match_all('/{@(.*?)}/', $text, $matches);
		if (!empty($matches[0]))
		{
		    $cms = new Ashtree_Common_Cms(ASH_SITE_NAME);
		    $var = $cms->get_content($this->_template);
		    foreach((array)$matches[1] as $key=>$value){
		        $template = str_replace(ASH_ROOTPATH, '', $this->_template);
		        $text = str_replace("{@{$value}}", $cms->get_content($template, $value), $text);
		    }//foreach
		}
		
		
		
		// Replace all references to other template files
		preg_match_all('/{(\/.*?)}/', $text, $matches);
		foreach((array)$matches[1] as $template) {
			$text = str_replace("{{$template}}", $this->file_get_template($template), $text);
		}//foreach
		
		#$text = preg_replace('/{\$.*}/i', '', $text);
		
		// When the site goes live, then we clean up the page
		// by removing any links to manually included stylesheets
		// and removing links to manually included javascript files
		if (ASH_SITE_MODE != 'development')
		{
		    $text = preg_replace('/<link.*\/>/i', '', $text);
		    $text = preg_replace('/<script.*src.*\/script>/i', '', $text);
		    $text = preg_replace('/\n+|\t+/', '', $text);
		    $text = preg_replace('/>\s+</', '><', $text);
		}
		
		return $text;
	}

	/**
	 * include_database_template
	 * @access
	 * @param
	 * @return
	 */
	public function db_get_template($nodename=NULL, $connection=NULL, $language=NULL)
	{

	}

	/**
	 * Get the contents of the template file
	 * replacing all the template variables along the way
	 * @access public
	 * @param String $filename
	 * @return String
	 */
	public function file_get_template($filename)
	{	
	    //Check the filename root
	    $fullname = Ashtree_Common::get_real_path($filename, TRUE);
	    
	    // Fallback for missing templates
	    if (!file_exists($fullname)) $fullname = str_replace(ASH_BASENAME . "bin/templates/", ASH_ROOTNAME . "{$this->theme}bin/templates/", $filename);
	    if (!file_exists($fullname)) $fullname = Ashtree_Common::get_real_path(ASH_ROOTPATH . "bin/templates/404.tpl.php", TRUE);
	    if (!file_exists($fullname)) $fullname = Ashtree_Common::get_real_path(ASH_ROOTNAME . "{$this->theme}bin/templates/404.tpl.php", TRUE);
	    
	    $this->_debug->log("INFO", "Retrieving template data from '{$fullname}'");
	    
	    // Determine all variables to be replaced with values
	    // Call this before including a file
	    // for the variables to be accessible in the php tags
	    foreach((array)$this->get_user_defined_vars() as $var123=>$val456)
		{
		    $$var123 = $val456;
		    if (is_array($val456)) {
				foreach($val456 as $k123=>$v456) $var_values["{\${$var123}.{$k123}}"] = $v456;
			} else {
				$var_values["{\${$var123}}"] = $val456;
			}
		}//foreach
		
	    // Get the contents of the template file
	    ob_start();
	    
	    //Check that the file can be included
	    // Not necessary to have 2 checks to see if file exists
	    // as include will perform that
		if (!@include_once($fullname)) {
		    // No text was retrieved from the template
		    $this->_debug->status("ABORT");
		    return FALSE;
		}
		// Continue, everthing is ok
		$this->_debug->status("OK");
		
		//Save contents to buffer
		$text = ob_get_clean();
		
		return $this->string_get_template($text, $var_values);
	}


	/**
	 * This function attempts different methods 
	 * for obtaining the most relevant template
	 * by checking language, bin, tpl, and database
	 * @access public
	 * @param $filename
	 * @return String
	 */
	public function include_template($filename, $database=NULL, $language=NULL)
	{
		// Modify the filename intelligently
		// and save the current template
		$this->_template = (substr_count($filename, '.tpl.php')) ? $filename : ASH_BASEPATH . "bin/templates/{$filename}.tpl.php";

	    // Always check phyical filesystem first
	    $text = $this->file_get_template($this->_template);
	    
	    // Then check for matching node in the database
		
		return $text;
	}
	
	
	/**
	 * An simple and obvious method to output a template
	 * when no parameters need to be specified
	 * apart from an ideifying name
	 * @access public
	 * @param $filename
	 */
	public static function write($filename)
	{
		$tpl = Ashtree_Common_Template::instance();
		echo $tpl->include_template($filename);
	}
	
}