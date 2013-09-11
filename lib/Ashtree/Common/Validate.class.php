<?php
/**
 * Validate based on rues defined in a JSON object
 * The rules can and should also be used for jquery.validate.js
 * This is just a fall-through check
 *
 * @package Snowball_Common_Validate
 * @author andrew.nash
 * @version 1.0
 * 
 * @change 2011-08-30 andrew.nash checnged equalTo to handle id's
 */


class Snowball_Common_Validate
{
	/**
	 * @access
	 * @var
	 */
	private $_params = array();
	
	/**
	 * @access
	 * @var
	 */
	private $_debug;
	
	/**
	 * @access
	 * @var
	 */
	private $_message;
	
	/**
	 * @access
	 * @param
	 */
	public function __construct()    
	{
		$this->_debug = Snowball_Common_Debug::instance();
		
		$this->session = "login";
		
		
	}
	
	/**
	 * @access
	 * @param
	 */
	public function __get($key)
	{
		return array_key_exists($key, $this->_params) ? $this->_params[$key] : 0;
	}
	
	/**
	 * @access
	 * @param
	 */
	public function __set($key, $value)
	{
		$this->_params[$key] = $value;
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
	 * @access
	 * @param
	 */
	public function __toString()
	{
		return "";
	}
	
	/**
	 * @access
	 * @param
	 */
	public function __destruct()
	{

	}
	
	/**
	 * @access
	 * @param
	 */
	public function __invoke()
	{
		$this->validate();
	}
	
	/**
	 * @access
	 * @param
	 */
	public function invoke()
	{
		$this->__invoke();
	}
	
	/**
	 * @access
	 * @param
	 */
	public function validate($fields, $rules=NULL, $messages=NULL)
	{
		$this->_message = new Snowball_Common_Message();
		
		if ($rules) $this->setRules($rules);
		
		//echo dump($this->rules, 1);
		if (empty($fields))
		{
			$this->_debug->log("ERROR", "Calling validate without fields");
			return FALSE;
		}
		
		$this->fields = $fields;
		echo dump($this->rules, 1);
		
		if (empty($this->rules))
		{
			$this->_debug->log("ERROR", "Calling validate without rules");
			return FALSE;
		}
		
		$success = TRUE;
		
		
		
		foreach($this->fields as $fieldname=>$submitted)
		{
			if (isset($this->rules->$fieldname))
			{
    		    if (is_object($this->rules->$fieldname))
    			{
    				foreach($this->rules->$fieldname as $key=>$value)
    				{
    					$success = ($this->is_valid($fieldname, $key, $value)) ? $success : FALSE;
    					#echo dump(array($success, $fieldname, $this->fields[$fieldname], $key, $value), 1);
    				}//foreach
    			}
    			else
    			{
    				$success = ($this->is_valid($fieldname, $this->rules->$fieldname)) ? $success : FALSE;
    				#echo dump(array($success, $fieldname, $this->fields[$fieldname], $rule), 1);
    			}
			}
			
		}//foreach
        
		#if (!$success && $this->redirect_failure) redirect($this->redirect_failure);
		return $success;
	}
	


	/**
	 * Check if a field is valid
	 * @access
	 * @param
	 */
	public function is_valid($fieldname, $rule, $value=TRUE)
	{	
		
		if (method_exists($this, $rule))
		{
			return $this->$rule($fieldname, $value);
		}
		else
		{
			$value = (($value == 'true') || ($value == 1)) ? "" : "of '{$value}'";
			$this->_debug->log("WARNING", "'{$fieldname}' should be '{$rule}' {$value} could not be validated. Ignoring...");
			return TRUE;
		}
	}
	
	
	/**
	 * Check if a field is REQUIRED
	 * @access
	 * @param
	 */
	private function required($fieldname, $value)
	{
		
		if ($this->fields[$fieldname] == '')
		{
			$this->_message->message = "FAILURE:: {$fieldname} is required";
			return FALSE;
		}
		
		return TRUE;
	}


	/**
	 * Check if a field is an EMAIL ADDRESS
	 * @access
	 * @param
	 */
	private function email($fieldname, $value)
	{
		if(!filter_var($this->fields[$fieldname], FILTER_VALIDATE_EMAIL))
		{
			$this->_message->message = "FAILURE:: {$fieldname} field does not contain a valid email address";
			return FALSE;
		}
		
		return TRUE;
	}

	
	/**
	 * Check if a field is a NUMBER
	 * @access
	 * @param
	 */
	private function number($fieldname, $value)
	{
		if(!is_numeric($this->fields[$fieldname]))
		{
			$this->_message->message = "FAILURE:: {$fieldname} field is not a valid number";
			return FALSE;
		}
		
		return TRUE;
	}
	
	
	/**
	 * Check if a field is a MINIMUM LENGTH
	 * @access
	 * @param
	 */
	private function minlength($fieldname, $value)
	{
		if(strlen($this->fields[$fieldname]) < $value)
		{
			$this->_message->message = "FAILURE:: {$fieldname} should have a length of at least {$value}";
			return FALSE;
		}
		
		return TRUE;
	}
	
	
	/**
	 * Check if a field is an EQUAL to value
	 * @access
	 * @param
	 */
	private function equalTo($fieldname, $value)
	{
	    switch (substr_count($value, '#'))
		{
		    case 0  : $value = $value; break;
		    case 1  : $value = $this->fields[str_replace('#', '', $value)]; break;
		    default : $value = $this->fields[str_replace('#', '', end(explode(' ', $value)))]; break;
		}//switch
		
		if($this->fields[$fieldname] != $value)
		{
			$this->_message->message = "FAILURE:: {$fieldname} is not the same as {$value}";
			return FALSE;
		}
		
		return TRUE;
	}
	
	
	/**
	 * Check if a field is NOT EQUAL to value
	 * @access
	 * @param
	 */
	private function notEqualTo($fieldname, $value)
	{
		switch (substr_count($value, '#'))
		{
		    case 0  : $value = $value; break;
		    case 1  : $value = $this->fields[str_replace('#', '', $value)]; break;
		    default : $value = $this->fields[str_replace('#', '', end(explode(' ', $value)))]; break;
		}//switch
		
		if($this->fields[$fieldname] == $value)
		{
			$this->_message->message = "FAILURE:: {$fieldname} should not be {$value}";
			return FALSE;
		}
		
		return TRUE;
	}


	/**
	 * Check if a field is REMOTELY validated
	 * @access
	 * @param
	 */
	private function remote($fieldname, $value)
	{
		$curl = new Snowball_Common_Curl($value);
		$curl->get(array($fieldname => $this->fields[$fieldname]));

		if($curl->result != 'true')
		{
			if (strpos(strip_tags($curl->result), '404 Not Found') !== FALSE)
			{
				//$this->_message->message = "WARNING:: '{$fieldname}' could not be remotely validated. Ignoring...";
				return TRUE;
			}
			else
			{
			   
				$this->_message->message = ($curl->result != 'false') ? "FAILURE:: " . strip_tags($curl->result) : "FAILURE:: {$fieldname} needs to be fixed";
				return FALSE;
			}
		}
		
		return TRUE;
	}
	
	
	/**
	 * Set the validation rules from JSON to PHP Array
	 * We use JSON because validating more often than not goes hand in hand with friendlier Javascript validation
	 * @param json $rules
	 */
	public function setRules($rules)
	{
		$rules = (is_array($rules)) ? array($rules) : json_decode("[{$rules}]", TRUE);
		$this->rules = $rules[0];
		
	}
	
	
	/**
	 * Get the validation rules from PHP Array to JSON
	 * We use JSON because validating more often than not goes hand in hand with friendlier Javascript validation 
	 * @return json $rules
	 */
	public function getRules()
	{
		return json_encode($this->rules);
		
	}
		
}
