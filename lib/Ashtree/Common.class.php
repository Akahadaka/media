<?php
/**
 * Contains all commonly used functions in the Ashtree CMS
 * 
 * Somewhat an extension of PHP common functions except 
 * namepaced in this Ashtree Common class, hence the
 * underscore naming convetion
 * 
 * @author andrew.nash
 * @version 2.0
 */
class Ashtree_Common
{
    /**
     * 
     */
    public function __construct()
    {
        
    }
    
    
    /**
     * Converts a path in the site root to the application root
     */
    public static function get_real_path($path, $absolute=FALSE)
    {
        if ($absolute) return ASH_ROOTPATH . preg_replace('/^\//', '', str_replace(array(ASH_ROOTPATH, ASH_ROOTNAME), '', $path));
        else           return ASH_ROOTNAME . preg_replace('/^\//', '', str_replace(array(ASH_ROOTPATH, ASH_ROOTNAME), '', $path));  
    }
    
    /**
     * Auto includes all "*.inc.php" files in specified directory
     * @access public
     * @param String $dir
     * @return Boolean
     */
    public static function get_all_includes($dir)
    {
        $directory = Ashtree_Common::get_real_path($dir, TRUE);
        if (is_dir($directory) && ($handler = opendir($directory)))
		{
			#$debug = Ashtree_Common_Debug::instance();	
			while (($filename = readdir($handler)) !== FALSE) 
			{
				if (substr($filename, -8) == '.inc.php')
				{
					#$debug->title = "INFO:: Including '{$directory}/{$filename}'";
					include_once($directory . '/' . $filename);
				}
			}//while
			closedir($handler);
			return TRUE;
		}
		
		return FALSE;
    }
    
    /**
     * 
     */
    function redirect($url=NULL)
    {
        $destination = (isset($url)) ? $url : $_SERVER['REQUEST_URI'];
        header("Location: {$destination}");
        exit;
    }
    
    /**
     * Returns absolute TRUE or FALSE for any variable type
     * Use 2nd parameter to define if absolute (i.e. -1 = FALSE; 0, 1 = TRUE)
     * @access public
     * @param Mixed $value
     * @param Boolean $absolute
     * @return Boolean
     * @example Ashtree_Common::is('yes') //returns TRUE
     * @example Ashtree_Common::is(DEFINED_CONSTANT) //'ON' returns TRUE
     * @example Ashtree_Common::is(strpos('abc', 'a'), TRUE) //0 returns TRUE
     */
    public static function is($value, $absolute=FALSE)
    {
    	if ($absolute && is_integer($value))
    	{
    		$result = ($value !== FALSE) ? TRUE : FALSE;
    	}
    	else if (is_bool($value))
    	{
    		$result = $value;
    	}
    	else if (is_string($value))
    	{
    		switch(trim(strtolower($value)))
    		{
    			case '1':
    			case 'on':
    			case 'yes':
    			case 'true':
    			case 'checked':
    				$result = TRUE; 
    				break;
    			case '':
    			case ' ':
    			case '0':
    			case 'off':
    			case 'no':
    			case 'false':
    			case 'none':
    				$result = FALSE; 
    				break;
    			default:
    				$result = TRUE;
    				break;
    		}//switch
    	}
    	else 
    	{
    		$result = (boolean)$value;
    	}
    	
    	return $result;
    }
    
    /**
	 *
	 * @access
	 * @param
	 * @return
	 */
	public static function extend(&$arr1, $arr2)
	{
		//This won't work for auto indexing
		//$arr1 = array_merge($arr1, $arr2);
		
		//Defaults with the same key get overwritten
		foreach((array)$arr1 as $key=>$value)
		{
			if (isset($arr2[$key])) 
			{
				$arr1[$key] = $arr2[$key];
				unset($arr2[$key]);
			}
		}//foreach
		
		//Additional values get appended
		(array)$arr1 += (array)$arr2;
		
		//Not necessary as we passing by reference
		//return $arr1;
		
	}
	
		/**
	 * file_put_contents and create directory if it does not exist
	 *
	 * @access
	 * @param
	 * @return
	 *
	 * TODO
	 *
	 */
	public static function file_force_contents($dir, $contents=NULL)
	{
		$dbg = Ashtree_Common_Debug::instance();
		
		// Can't create folders outside the root anyway
		$subdir = str_replace(ASH_ROOTPATH, '', $dir);
		$parts  = explode('/', $subdir);
        $file   = array_pop($parts);
        $subdir = ASH_ROOTPATH;
        foreach($parts as $part) {
            if(!is_dir($subdir .= "$part/")) {
				mkdir($subdir);
				if (is_dir($subdir)) $dbg->title = "INFO:: Created directory '{$subdir}'";
				else $dbg->title = "ERROR:: Could not Create directory '{$subdir}'";
			}
		}
		if ($contents) file_put_contents($subdir . $file, $contents);
	} //method file_force_contents	

	
	/**
	 * char_limit
	 * @access
	 * @param
	 * @return
	 */
	public static function char_limit($string, $size, $words=FALSE)
	{
	    if ($words)
	    {
	        $chunks = explode(' ', $string);
	        $result = array_slice($chunks, 0, $size);
	        if ($size < sizeof($chunks))
	        {
	            return implode(' ', $result) . "...";
	        }
	    } 
	    else if ($size < strlen($string))
	    {
    	    $subset = trim(substr($string, 0, $size));
    	    $result = preg_replace('/\s[^\s]$/i', '', $subset);
    	    return $result . "...";
	    }
	    return $string;
	}
	

	/**
	 * Copy and paste me
	 * @access
	 * @param
	 * @return
	 */
	public static function byte_suffix($value, $suffix='all')
	{
		$last = strtolower($value[strlen($value)-1]);
		
		if (!is_numeric($last))
		{
			switch(strtolower($last)) {
		        case 'g': $value *= 1024;
		        case 'm': $value *= 1024;
		        case 'k': $value *= 1024;
		    }//switch
		}
		
		$sfx = strtolower($suffix);
		switch($value)
		{
			case ($value > 1073741824 && $sfx == 'all') || ($sfx == 'gb') : $value = number_format($value/1073741824, 2, '.', '') . ' GB'; break;
			case ($value > 1048576 && $sfx == 'all') || ($sfx == 'mb') : $value = number_format($value/1048576, 0, '.', '') . ' MB'; break;
			case ($value > 1024 && $sfx == 'all') || ($sfx == 'kb') : $value = number_format($value/1024, 0, '.', '') . ' KB'; break;
		}//switch
		return $value;

	} 
	
	/**
	 * Returns a list of files in the specified directory
	 * filtered if necessary
	 * @param String $directory
	 * @param String $filter
	 * @return Array
	 */
	public static function readdir($directory, $filter=NULL)
	{
    	if ($dir_handler = opendir(self::get_real_path($directory, TRUE)))
    	{
    	    $files = array();
    		while (($filename = readdir($dir_handler)) !== FALSE) 
    		{
    		    $continue = ($filter && !substr_count($filename, $filter)) ? FALSE : TRUE;
    			if ($continue)
    			{
    				$files[] = $filename;
    			}
    		}//while
    		closedir($dir_handler);
    		return $files;
    	}
    	
    	return FALSE;
	}
	
	/**
	 * Capitalizes the first letter of every sentence 
	 * and adds a fullstop at the end if missing 
	 * @access public
	 * @param String $string
	 * @return String
	 */
	public static function capitalize($string)
	{
		//first we make everything lowercase, and then make the first letter if the entire string capitalized
		#$string = ucfirst(strtolower($string));
		 
		//now we run the function to capitalize every letter AFTER a full-stop (period).
		#$string = preg_replace_callback('/[.!?]\s*.?\w/', create_function('$matches', 'return strtoupper($matches[0]);'), $string);
		
	    //return $string;
	    
	    //return preg_replace('/([.!?])\s*(\w)/e', "strtoupper('\\1 \\2')", ucfirst(strtolower($string)));
	    if (!function_exists('_ucfirston'))
	    {
    	    function _ucfirston($char, $str)
    	    {
    	        if (substr_count($str, $char))
    	        {
        	        $result = array();
            	    $sentences = explode("{$char} ", $str);
            	    foreach ($sentences as $sentence)
            	    {
            	        $result[] = ucfirst($sentence);
            	    }
            	    return implode("{$char} ", $result);
    	        }
    	        
    	        return ucfirst($str);
    	    
    	    }//function _ucfirston
	    }
	    
	    
	   $string = _ucfirston('.', $string);
	   $string = _ucfirston('!', $string);
	   $string = _ucfirston('?', $string);
	    
	   return $string;
		
	}
	
	/**
	 * Returns a random number or letter
	 * @access public
	 * @param Int $num
	 * @return String
	 */
	public static function random_alphanumeric($num=1)
	{
		if (!function_exists('pick'))
		{
			function pick($from)
			{
				return $from[rand(0, strlen($from)-1)];
			}//function pick
		}
	
		#$lower_alphabet = "abcdefghijklmnopqrstuvwxyz";
		#$upper_alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$lower_alphabet = "abcdefghijkmnpqrtuvwxyz";
		$upper_alphabet = "ABCDEFGHJKLMNPQRTUVWXYZ";
		#$numeric = "1234567890";
		$numeric = "2346789";
		$special = "_.:?|!@#$%*&+=-";
	
		$string = '';
		for($i = 0; $i < $num; $i++)
		{
		$string .= pick($lower_alphabet.$upper_alphabet.$numeric);
		}//for
	
		return $string;
	}
	
	/**
	 * Returns a figure in a currency friendly output value
	 * @param float $amount
	 * @param String $symbol
	 * @param [String $position='left|right']
	 * @return String
	 */
	public static function currency($amount, $symbol='R', $position='left')
	{
	    $sympos = array('left'=>'', 'right'=>'');
	    $sympos[$position] = $symbol;
	    
	    return $sympos['left'] . " " . number_format($amount, 2, '.', ',') . " " . $sympos['right'];
	}
	
	/**
	 * Takes a currency formatted figure and returns in float
	 * @param String $figure
	 * @return float
	 */
	public static function uncurrency($figure) 
	{
	    $amount = str_replace(',', '', $figure);
	    return preg_replace('/.*?(\d+\.\d+).*|.*?(\d+).*/i', '${1}${2}', $amount);
	}
	
	/**
	 * Add or remove the http from the beginning of an url
	 * @param String $url
	 * @param [Boolean $prepend=TRUE]
	 * @param [String $protocol=http]
	 * @return String
	 */
	public static function http($url, $prepend=TRUE, $protocol='http')
	{
		if ($prepend) {
			if (!substr_count($url, "://", 0, 8)) {
				return "{$protocol}://{$url}";
			} else {
				return preg_replace('/^(.*):\/\//', "{$protocol}://", $url);
			}
		} else {
			return preg_replace('/^.*:\/\//', '', $url);
		}
	}

	/**
	 * Add or remove the https from the beginning of an url
	 * @param String $url
	 * @param [Boolean $prepend=TRUE]
	 * @param [String $protocol=http]
	 * @return String
	 */
	public static function https($url, $prepend=TRUE)
	{
		return self::http($url, $prepend, $protocol='https');
	}
    
	/**
	 * Returns a given time in "days ago"
	 * @param String $time
	 * @param [String $tense='ago']
	 * @return String
	 */
	public static function time_ago($time, $tense='ago')
	{
		$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		$lengths = array("60","60","24","7","4.35","12","10");
	
		$now  = time();
		$diff = $now - strtotime($time);
	
		for($j = 0; $diff >= $lengths[$j] && $j < count($lengths)-1; $j++) {
			$diff /= $lengths[$j];
		}
	
		$diff = round($diff);
	
		if($diff != 1) {
			$periods[$j].= "s";
		}
	
		return "{$diff} {$periods[$j]} {$tense}";
	}
	
	/**
	 * Includes a file only if it exists
	 * @param String $filepath
	 * @return Boolean
	 */
	public static function include_exists($filepath) {
		  
		if (is_file($filepath)) {
			return include($filepath);
		}
		echo dump("<strong>Error:</strong> {$filepath} does not exists");
		return FALSE;
	}
}