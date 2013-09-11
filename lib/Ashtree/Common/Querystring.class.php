<?php
/**
 * A class for handling and manipulating querystrings
 * Reserved: url
 * 			 string
 * 
 * @package Ashtree_Common_Querystring
 * @author andrew.nash
 * @version 2.0
 * 
 * @change 
 *
 */
 
class Ashtree_Common_Querystring
{
	
	private $_params = array ();
	
	private $_debug;
	
	/**
	 * @param [String $url]
	 */
	public function __construct($url=NULL)
	{
		if ($url) $this->url = $url;
	}
	

	/**
	 *
	 */
	public function __get($key)
	{
		switch($key)
		{
			case 'url':
				return array_key_exists('url', $this->_params) ? $this->_params['url'] : '';		
				break;
				
			case 'string':
				if (!empty($this->_params['string']))
				{
					foreach ($this->_params['string'] as $key=>$value)
					{
						if ($key != '')
						{
							$keyvaluestring[] = (is_array($value)) ? $key . '=' . implode(',', $value) : $key . '=' . $value;
						}
					}//foreach
					return '?' . implode('&', $keyvaluestring);
				}
				return '';
				break;
				
			default:
				return array_key_exists($key, $this->_params['string']) ? ((sizeof($this->_params['string'][$key]) > 1) ? $this->_params['string'][$key] : $this->_params['string'][$key][0]) : '';
				break;
		}
	}
	

	/**
	 *
	 */
	public function __set($key, $value)
	{
		switch($key)
		{
			case 'url':
				$url_string = explode('?', $value);
				$this->_params['url'] = $url_string[0];
				$value = (isset($url_string[1])) ? $url_string[1] : "";
				$this->_params['string'] = array();
				
			case 'string':
				$url_string = explode('?', $value);
				$url_string = (isset($url_string[1])) ? $url_string[1] : $url_string[0];
				$this->_params['string'] = array();
				$url_string = explode('&', $url_string);
				foreach ($url_string as $string)
				{ 
					parse_str($string, $result);
					$this->_params['string'] = array_merge($this->_params['string'], $result);
				}//foreach
				break;
				
			default:
				$this->_params['string'][$key] = (strpos($value, ",") !== FALSE) ? explode(",", $value) : $value;
				break;
		}
	}
	

	/**
	 *
	 */
	public function __isset($key)
	{
		switch($key)
		{
			case 'url':
				return isset($this->_params['url']);	
				break;
				
			case 'string':
				return isset($this->_params['string']);
				break;
				
			default:
				return isset($this->_params['string'][$key]);
				break;
		}
	}
	

	/**
	 *
	 */
	public function __unset($key)
	{
		switch($key)
		{
			case 'url':
				unset($this->_params['url']);	
				break;
				
			case 'string':
				unset($this->_params['string']);
				break;
				
			default:
				unset($this->_params['string'][$key]);
				break;
		}
	}
	
	
	/**
	 *
	 */
	public function __toString()
	{
		return $this->url . $this->string;
	}
	
	/**
	 * Similar to magic get and set
	 * but can handle additional characters
	 * such as @ and $
	 * @param [String $name]
	 * @param [Mixed $value]
	 * @param return Mixed
	 */
	public function param($name, $value)
	{
		if (isset($value))
		{
			if ($value == '') unset($this->_params['string'][$name]);
			else $this->_params[$name] = $value;
		}
		else
		{
			return $this->_params[$name];
		}
	}
	
	/**
     * This function will convert a query_string into an array 
     * more useful array of params['name'] = "value";
     * @param String $query_string
     * @return Array
 	 */
	public function toArray($query_string)
	{
		parse_url($query_string, $result);
		return $result;
	}

}
