<?php
/**
 * Validate based on rues defined in a JSON object
 * The rules can and should also be used for jquery.validate.js
 * This is just a fall-through check
 *
 * @package Ashtree_Common_Validate
 * @author andrew.nash
 * @version 1.0
 * 
 * @change 2011-08-30 andrew.nash checnged equalTo to handle id's
 */


class Ashtree_Form_Validate {

	private $_message;

	// Validation options
	public $email;
	public $number;
	public $minLength;
	public $equalTo;
	public $notEqualTo;
	public $required;
	public $remote;
	
	// Fields
	private $messages;
	
	/**
	 * 
	 * @param
	 */
	public function __construct() {
		
	}
	
	/**
	 * 
	 * @param
	 */
	public function __invoke() {
		$this->validate();
	}
	
	/**
	 * 
	 * @param
	 */
	public function invoke() {
		$this->__invoke();
	}
	
	public function getVars() {
		$vars = array();
		
		if ($this->email)      $vars['email'] = $this->email;
		if ($this->number)     $vars['number'] = $this->number;
		if ($this->minLength)  $vars['minLength'] = $this->minLength;
		if ($this->equalTo)    $vars['equalTo'] = $this->equalTo;
		if ($this->notEqualTo) $vars['notEqualTo'] = $this->notEqualTo;
		if ($this->required)   $vars['required'] = $this->required;
		if ($this->remote)     $vars['remote'] = $this->remote;
		
		return $vars;
	}
	
	public function setMessage($type, $message) {
		$this->messages[$type] = $message;
	}
	
	public function getMessages() {
		return $this->messages;
	}
	
	/**
	 * 
	 * @param
	 */
	public function validate($fields, $rules=NULL, $messages=NULL) {
		$this->_message = new Ashtree_Common_Message();
		
		if ($rules) $this->setRules($rules);
		
		//echo dump($this->rules, 1);
		if (empty($fields)) {
			$this->_debug->log("ERROR", "Calling validate without fields");
			return FALSE;
		}
		
		$this->fields = $fields;
		echo dump($this->rules, 1);
		
		if (empty($this->rules)) {
			$this->_debug->log("ERROR", "Calling validate without rules");
			return FALSE;
		}
		$success = TRUE;
		
		foreach($this->fields as $fieldname=>$submitted) {
			if (isset($this->rules->$fieldname)) {
    		    if (is_object($this->rules->$fieldname)) {
    				foreach($this->rules->$fieldname as $key=>$value) {
    					$success = ($this->is_valid($fieldname, $key, $value)) ? $success : FALSE;
    					#echo dump(array($success, $fieldname, $this->fields[$fieldname], $key, $value), 1);
    				}//foreach
    			} else {
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
	 * @param
	 */
	public function is_valid($fieldname, $rule, $value=TRUE) {	
		
		if (method_exists($this, $rule)) {
			return $this->$rule($fieldname, $value);
		} else {
			$value = (($value == 'true') || ($value == 1)) ? "" : "of '{$value}'";
			$this->_debug->log("WARNING", "'{$fieldname}' should be '{$rule}' {$value} could not be validated. Ignoring...");
			return TRUE;
		}
	}
	
	
	/**
	 * Check if a field is REQUIRED
	 * @param
	 */
	private function required($fieldname, $value) {
		
		if ($this->fields[$fieldname] == '') {
			Ashtree_Common_Message::message('error', "{$fieldname} is required");
			return FALSE;
		}
		
		return TRUE;
	}


	/**
	 * Check if a field is an EMAIL ADDRESS
	 * @param
	 */
	private function email($fieldname, $value) {
		if(!filter_var($this->fields[$fieldname], FILTER_VALIDATE_EMAIL)) {
			Ashtree_Common_Message::message('error', "{$fieldname} field does not contain a valid email address");
			return FALSE;
		}
		
		return TRUE;
	}

	
	/**
	 * Check if a field is a NUMBER
	 * @param
	 */
	private function number($fieldname, $value) {
		if(!is_numeric($this->fields[$fieldname])) {
			Ashtree_Common_Message::message('error', "{$fieldname} field is not a valid number");
			return FALSE;
		}
		
		return TRUE;
	}
	
	
	/**
	 * Check if a field is a MINIMUM LENGTH
	 * @param
	 */
	private function minLength($fieldname, $value) {
		if(strlen($this->fields[$fieldname]) < $value) {
			Ashtree_Common_Message::message('error', "{$fieldname} should have a length of at least {$value}");
			return FALSE;
		}
		
		return TRUE;
	}
	
	
	/**
	 * Check if a field is an EQUAL to value
	 * @param
	 */
	private function equalTo($fieldname, $value) {
	    switch (substr_count($value, '#')) {
		    case 0  : $value = $value; break;
		    case 1  : $value = $this->fields[str_replace('#', '', $value)]; break;
		    default : $value = $this->fields[str_replace('#', '', end(explode(' ', $value)))]; break;
		}//switch
		
		if($this->fields[$fieldname] != $value)
		{
			Ashtree_Common_Message::message('error', "{$fieldname} is not the same as {$value}");
			return FALSE;
		}
		
		return TRUE;
	}
	
	
	/**
	 * Check if a field is NOT EQUAL to value
	 * @param
	 */
	private function notEqualTo($fieldname, $value) {
		switch (substr_count($value, '#')) {
		    case 0  : $value = $value; break;
		    case 1  : $value = $this->fields[str_replace('#', '', $value)]; break;
		    default : $value = $this->fields[str_replace('#', '', end(explode(' ', $value)))]; break;
		}//switch
		
		if($this->fields[$fieldname] == $value) {
			Ashtree_Common_Message::message('error', "{$fieldname} should not be {$value}");
			return FALSE;
		}
		
		return TRUE;
	}


	/**
	 * Check if a field is REMOTELY validated
	 * @param
	 */
	private function remote($fieldname, $value) {
		$curl = new Ashtree_Common_Curl($value);
		$curl->get(array($fieldname => $this->fields[$fieldname]));

		if($curl->result != 'true') {
			if (strpos(strip_tags($curl->result), '404 Not Found') !== FALSE) {
				//$this->_message->message = "WARNING:: '{$fieldname}' could not be remotely validated. Ignoring...";
				return TRUE;
			} else {	   
				if ($curl->result != 'false') Ashtree_Common_Message::message('error', strip_tags($curl->result));
				else Ashtree_Common_Message::message('error', "{$fieldname} needs to be fixed");
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
	public function setRules($rules) {
		$rules = (is_array($rules)) ? array($rules) : json_decode("[{$rules}]", TRUE);
		$this->rules = $rules[0];
		
	}
	
	
	/**
	 * Get the validation rules from PHP Array to JSON
	 * We use JSON because validating more often than not goes hand in hand with friendlier Javascript validation 
	 * @return json $rules
	 */
	public function getRules() {
		return json_encode($this->rules);
		
	}
		
}
