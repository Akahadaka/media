<?php
/**
 * Stores all messages to be output to the user
 * in session data and wipes the message data
 * after they have been displayed
 *
 * @package Ashtree_Common_Message
 * @author andrew.nash
 * @version 2.0
 * 
 * @change
 */
class Ashtree_Common_Message
{
	private $params = array();
	
	/**
	 * Debug object used for logging messages
	 * @var Object $_debug
	 * @access private
	 */
	private $_debug; 
	
	/**
	 * Session group for the user information
	 * @var session string
	 * @access private
	 */
	 private static $_session = 'Ashtree_Message';

	/**
	 *
	 */
	public function __construct($message=NULL)
	{  
		$this->_debug = Ashtree_Common_Debug::instance();
		
		if ($message) self::message($message);
		
	}
	
	/**
	 *
	 */
	public function __get($key)
	{
		switch($key)
		{
			case 'message':
				$result = array_shift($_SESSION[self::$_session]);
				break;
			
			case 'messages':
				$result = (isset($_SESSION[self::$_session])) ? $_SESSION[self::$_session] : array();
				self::clear_messages();
				break;
			
			default;
				$result = array_key_exists($key, $this->params) ? $this->params[$key] : 0;
				break;
		}//switch
		return $result;
	}
	
	/**
	 *
	 */
	public function __set($key, $value)
	{
		switch($key)
		{
			case 'message':
				self::message($value);
				break;
			
			default:
				$this->params[$key] = $value;
				break;
		}//switch
	}
	
	/**
	 *
	 */
	public function __isset($key)
	{
		return isset($this->params[$key]);
	}
		
	/**
	 *
	 */	
	public function __unset($key)
	{
		unset($this->params[$key]);
	}
		
	/**
	 * Removes all message data from the session
	 * @access public
	 */
	public static function clear_messages()
	{
		foreach($_SESSION[self::$_session] as $key=>$message) 
		{
		    if (!isset($message['stick']) || !$message['stick']) unset($_SESSION[self::$_session][$key]);    
		}//foreach
	    
	}
	
	/**
	 * list_messages
	 * @access public
	 * @return String
	 */
	public static function message($class, $message=NULL)
	{
	    if (!isset($message)) {
	        if (substr_count($class, '::')) {
	            $string  = explode('::', $class);
	            $class   = strtolower(trim($string[0]));
	            $message = strtolower(trim($string[1]));
	        } else {
	            $class = '';
	            $message = $class;
	        }
	    }
	    
	    $key = md5($message);
	    
	    $_SESSION[self::$_session][$key]['class'] = $class;
		$_SESSION[self::$_session][$key]['title'] = $message;
	    
	    //$this->_debug->log("MESSAGE", $message);
	    
	    return $key;

	}
	
	/**
	 * list_messages
	 * @access public
	 * @return String
	 */
	public static function sticky($class, $message=NULL, $expires=10)
	{
	    $key = self::message($class, $message);
	    $_SESSION[self::$_session][$key]['stick'] = $expires;
	    
	    return $key;
	}
	
	/**
	 * get_messages
	 * @access public
	 * @return String
	 */
	public static function get_messages($class=NULL)
	{
	    $result = array();
	    if (isset($_SESSION[self::$_session])) {
    	    foreach($_SESSION[self::$_session] as $key=>$message) 
    		{
    		    if (!isset($class) || ($message['class'] == $class)) {
    		        $result[] = $_SESSION[self::$_session][$key];
    		        if (!isset($message['stick']) || !$message['stick']) {
    		            unset($_SESSION[self::$_session][$key]);   
    		        } else {
    		            $_SESSION[self::$_session][$key]['stick'] -= 1;
    		        }
    		    } 
    		        
    		}//foreach
	    }

		return $result;
	}
	
	/**
	 * get_messages
	 * @access public
	 * @return String
	 */
	public static function normalize($message)
	{
	    $string = Ashtree_Common::capitalize($message);
	    $period = (substr($message, -1) != '.') ? '.' : '';
	    
	    return $string.$period;
	}
	
	
	/**
	 * list_messages
	 * @access public
	 * @return String
	 */
	public static function list_messages()
	{
	    // Check for failures first
	    $messages = self::get_messages('failure');
	    if (empty($messages)) {
	         $messages = self::get_messages();
	    }
	    
	     #echo dump($messages, 1);
	     #if (!empty($_POST)) exit;
	     
	    $dom = new DOMDocument();
	    $ul = $dom->createElement('ul');
	    $ul->setAttribute('class', 'messages');
	    
	    foreach($messages as $message) {
	    	$span = $dom->createElement('span');
	    	$span->setAttribute('class', 'message-discard');
	    	$span->setAttribute('style', 'float:right;cursor:pointer');
	    	$span->nodeValue = 'x';
	    	
	    	$li = $dom->createElement('li');
	    	$li->setAttribute('class', $message['class']);
	    	$li->appendChild($span);
	    	$li->nodeValue = self::normalize($message['title']);
	    	
	    	$ul->appendChild($li);
	    }//foreach
	    
	    $dom->appendChild($ul);
	    
	    return $dom->saveXML();
	}
	
	/**
	 * Deletes a specific message from the list
	 * @access public
	 * @param String $message
	 */
	public static function unset_message($message)
	{
		unset($_SESSION[self::$_session][md5($message)]);
	}
	
}
