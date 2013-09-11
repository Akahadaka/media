<?php
/**
 * A class to do a get or a post to a web site and retrieve the results
 * 
 * Sample Usage:
 * $curl = new Ashtree_Common_Curl("http://www.Ashtree.co.za/");
 *
 * @package Ashtree_Common_Curl
 * @author andrew.nash
 *
 */
class Ashtree_Common_Curl
{
	
	/**
	 * private
	 */
	
	private $_params = array();
	
	private $_debug;
	
	private $_channel;
	
	
	/**
	 * public
	 */
	public $url;
	
	public $username;
	 
	public $password;
	 
	public $result;
	
	/**
	 * 
	 * @param $url
	 */
	public function __construct($url=NULL)
	{
		$this->_debug = Ashtree_Common_Debug::instance();
		
		$this->_channel = curl_init();
		curl_setopt($this->_channel, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($this->_channel, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($this->_channel, CURLOPT_HEADER, FALSE);
        curl_setopt($this->_channel, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($this->_channel, CURLOPT_FOLLOWLOCATION, FALSE);
        curl_setopt($this->_channel, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($this->_channel, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($this->_channel, CURLOPT_COOKIEFILE, 'cookie.txt');
		
		$this->url = ($url) ? $url : 'Undefined URL';
	}

	/**
	 * 
	 */
	public function __get($key)
	{
		return array_key_exists($key, $this->_params) ? $this->_params[$key] : 0;
	}

	/**
	 * 
	 */
	public function __set($key, $value)
	{
		$this->_params[$key] = $value;
	}

	/**
	 * 
	 */
	public function __isset($key)
	{
		return isset($this->_params[$key]);
	}

	/**
	 * 
	 */
	public function __unset($key)
	{
		unset($this->_params[$key]);
	}
	
	/**
	 * 
	 */
	public function __toString()
	{
		return $this->result;
	}
	

	/**
	 * 
	 */
	public function __destruct()
	{
		curl_close($this->_channel);
	}
	
	/**
	 * 
	 * @access public
	 * @param [Mixed $postdata]
	 * @return String
	 */
	public function post($postdata=NULL)
	{
		$post_string = (is_array($postdata)) ? http_build_query($postdata) : $postdata;
		
		$this->_debug->log("INFO", "cURL POST request: {$this->url}?".htmlentities($post_string));
		
		                     curl_setopt($this->_channel, CURLOPT_URL, $this->url);
		if ($this->username) curl_setopt($this->_channel, CURLOPT_USERPWD, "{$this->username}:{$this->password}");
		if ($this->header)   curl_setopt($this->_channel, CURLOPT_HEADER, TRUE);	
		                     curl_setopt($this->_channel, CURLOPT_POST, TRUE);
		                     curl_setopt($this->_channel, CURLOPT_POSTFIELDS, $post_string);
		
		$this->result = curl_exec($this->_channel);
		if ($this->result === FALSE)
		{
		    $this->_debug->status("FAILURE");
		    $this->_debug->log("ERROR", "cURL POST error: " . curl_error($this->_channel));
		    return FALSE;
		}
		
		$this->_debug->status("OK");
		return $this->result;
	}


	/**
	 * 
	 * @access public
	 * @param [Mixed $getdata]
	 * @return String
	 */
	public function get($getdata=NULL)
	{
		$get_string = (!substr_count($this->url, '?')) ? '?' . http_build_query($getdata) : "";
		
		$this->_debug->log("INFO", "cURL GET request: {$this->url}".htmlentities($get_string));
		
		                     curl_setopt($this->_channel, CURLOPT_URL, "{$this->url}{$get_string}");
		if ($this->username) curl_setopt($this->_channel, CURLOPT_USERPWD, "{$this->username}:{$this->password}");
		
		$this->result = curl_exec($this->_channel);
		if ($this->result === FALSE)
		{
		    $this->_debug->status("FAILURE");
		    $this->_debug->log("ERROR", "cURL GET error: " . curl_error($this->_channel));
		    return FALSE;
		}
		
		$this->_debug->status("OK");
		return $this->result;
	}

}