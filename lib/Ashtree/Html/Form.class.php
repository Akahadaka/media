<?php
/**
 * An extensive class for dynamically 
 * creating on-the-fly forms from SQL results
 * 
 * The idea here is that a form is generated 
 * automatically from the array results of a 
 * sql statement. The form object can then be 
 * manipulated to customise the form before 
 * being printed in various ways to a template
 *
 * @package Ashtree_Html_Form
 * @author andrew.nash
 * @version 1.2
 * 
 * @param
 *
 * @change 2011-03-09 andrew.nash added confirmation to delete buttons and its variants
 * @change 2011-02-08 andrew.nash separated form controls into its own method
 * @change 2011-02-08 andrew.nash separated the $this->field from the magic param array
 */

/**
 * constants
 */

class Ashtree_Html_Form 
{	
	/**
	 * private param
	 */
	private $param = array();
	
	
	/**
	 * private debug
	 */
	private $debug;

	
	/**
	 * private field
	 */
	private $field = array();	
	
	
	/**
	 * protected
	 */
	
	
	/**
	 * public field
	 */
	public $fields = array();
	
	
	/**
	 * public action
	 */
	 public $controls = array();

	
	/**
	 * public action
	 */
	 public $action;
	 
	 
	/**
	 * public method
	 */
	 public $method = 'post';
	 
	 
	/**
	 * public enctype
	 */
	 public $enctype = 'multipart/form-data';
	
	
	/**
	 * public connection
	 */
	 public $connection = NULL;
	
	
	/**
	 * Comments go here
	 *
	 * @param
	 * 
	 * 
	 *
	 */
	public function __construct()               //Line comments
	{
		$this->debug = new Ashtree_Common_Debug();
		
		$this->action = $_SERVER['REQUEST_URI'];
		
	} //method __construct
	

	/**
	 * Magic get with $field private array
	 *
	 * @param $key
	 * @return $field[$key]
	 * 
	 * 
	 *
	 */
	public function __get($key)                  //Line comments
	{
		return (array_key_exists($key, $this->param)) ? $this->param[$key] : FALSE;
	} //method __get
	

	/**
	 * Magic set with $field private array
	 *
	 * @param $key
	 * @param $value
	 * 
	 * 
	 *
	 */
	public function __set($key, $value)           //Line comments
	{
		switch($key)
		{
			default:
				$this->param[$key] = $value;
				break;
		}//switch
	} //method __set
	

	/**
	 * Magic isset to see if $key is in array and has a value
	 *
	 * @param $key
	 * @return Boolean
	 * 
	 * 
	 *
	 */
	public function __isset($key)                  //Line comments
	{
		return isset($this->param[$key]);
	} //method __isset
	

	/**
	 * Magig unset removes the $key and corresponding value from the array
	 *
	 * @param $key
	 * 
	 * 
	 *
	 */
	public function __unset($key)                  //Line comments
	{
		unset($this->param[$key]);
	} //method __unset
	
	
	/**
	 * Magic object to string returns some string value defined within the object
	 *
	 * @example $obj = new Object(); echo $obj;
	 *
	 * @return String
	 * 
	 *
	 */
	public function __toString()                     //Line comments
	{
		return "";
	} //method __toString
	

	/**
	 * Comments go here
	 *
	 * @param
	 * 
	 * 
	 *
	 */
	public function __destruct()                      //Line comments
	{

	} //method __destruct

	
	/**
	 * Comments go here
	 *
	 * @param
	 * 
	 * 
	 *
	 */
	public function __invoke($formfields=NULL)                      //Line comments
	{	
		$fields = (isset($formfields)) ? $formfields : $this->fields;
		$result = $this->_print_form($fields);

		if (isset($_REQUEST['create'])) $this->create();
		if (isset($_REQUEST['update'])) $this->update();
		if (isset($_REQUEST['delete'])) $this->delete();
		
		return $result;
	} //method __invoke
	
	
	/**
	 * Comments go here
	 *
	 * @param
	 * 
	 * 
	 *
	 */
	public function invoke($formfields=NULL)                      //Line comments
	{
		return $this->__invoke($formfields);
	} //method invoke


	/**
	 * Copy and paste me
	 *
	 * @access
	 * @param
	 * @return
	 *
	 * @todo create hidden field for defined Primary-Key
	 *
	 */
	public function build($formfields=NULL)
	{
		return $this->__invoke($formfields);
	}//function build
	
	
	/**
	 * This returns a string of options for a select
	 *
	 * @access public
	 * @param array
	 * @return string
	 *
	 * 
	 *
	 */
	public static function array_to_radio($options_array, $select, $usekey=FALSE)                   //Line comments
	{
		//Create an unolrered list to contain the radio buttons nicely
		$ul = new Ashtree_Html_Tag('ul');
		$ul->style = 'list-style:none';
		$ul->style = 'padding:0';
		$ul->style = 'margin:0';
		
		//Choose selected item or default to first item in list
		$select = ($select == '') ? $options_array[0] : $select;
		
		//Build all of the list items
		foreach($options_array as $key=>$value)
		{	
			//Build the input tag
			$input = new Ashtree_Html_Tag('input');
			$input->type = 'radio';
			$input->name = isset($group) ? $group : key($options_array);
			$input->value = ($usekey) ? $key : $value;
			if ($select == $input->value) $input->checked = 'checked';
			
			//Wrap input with a text label
			$label = new Ashtree_Html_Tag('label');
			$label->text = $input->build() . $value;
			
			//Arrange nicely with list-items
			$li = new Ashtree_Html_Tag('li');
			$li->text = $label->build();
			
			$ul->text .= $li->build();
		}//foreach
		
		return $ul->build();
	}//method array_to_radio
	
	
	
	/**
	 * This returns a string of options for a select
	 *
	 * @access public
	 * @param array
	 * @return string
	 *
	 * 
	 *
	 */
	public static function array_to_option($options_array, $select, $usekey=FALSE)                   //Line comments
	{
		//Choose selected item or default to first item in list
		if ($usekey && ($select == '')) $select = key($options_array);
		else if ($select == '') $select = current($options_array);
		
		//Initialise result
		$result = '';
		
		//Build all of the select options
		foreach ($options_array as $key=>$value)
		{
			//Check if options need to be grouped
			if (is_array($value))
			{
				$optgroup = new Ashtree_Html_Tag('optgroup');
				$optgroup->label = $key;
				foreach($value as $k=>$v)
				{
					$option = new Ashtree_Html_Tag('option');
					
					$option->value = ($usekey) ? $k : $v;
					if ($select == $option->value) $option->selected = 'selected';
					$option->html = $v;
					$optgroup->html .= $option->build();
				}//foreach
				
				$result .= $optgroup->build();
			}
			//Otherwise output options as they are
			else
			{
				$option = new Ashtree_Html_Tag('option');
				$option->value = ($usekey) ? $key : $value;
				if ($select == $option->value) $option->selected = 'selected';
				$option->html = $value;
				$result .= $option->build();
			}
		}//foreach
		
		return $result;
	} //method array_to_option

	
	/**
	 * This returns a string of list items for a list
	 *
	 * @access public
	 * @param array
	 * @return string
	 *
	 * @todo
	 *
	 */
	public static function array_to_listitem($list_array)                   //Line comments
	{
		$result = '';
		
		//Build all of the select options
		foreach ($list_array as $key=>$value)
		{
			$li = new Ashtree_Html_Tag('li');
			$li->text = $value;
			$result .= $li->build();
		}//foreach
		
		return $result;
	}//method array_to_listitem
	
	/**
	 * This returns a table of list items for organised output
	 *
	 * @access public
	 * @param array
	 * @return string
	 *
	 * @todo
	 *
	 */
	public static function array_to_table($user_options)                   //Line comments
	{
		//Setup the parameters with defaults
		$options = array(
			'data'=>array(),
			'group'=>'',
			'width'=>'100%',
			'columns'=>'10'
		);
		Ashtree_Common::extend($options, $user_options);
		
		$table = new Ashtree_Html_Tag('table');
		$table->width = $options['width'];
		
		$tbody = new Ashtree_Html_Tag('tbody');
		
		$count = 0;
		$tr = new Ashtree_Html_Tag('tr');
		
		foreach($options['data'] as $key=>$value)
		{
			if (is_array($value))
			{
				$options['group'] = count($value);
				foreach($value as $k=>$v)
				{
					$td = new Ashtree_Html_Tag('td');
					$td->text = $v;
					$td->class = "group{$k}";
					$tr->text .= $td->build();
				}//foreach
			}
			else
			{
				$td = new Ashtree_Html_Tag('td');
				$td->text = $value;
				$tr->text .= $td->build();
			}
			
			$count++;
			if ($count >= $options['columns']) 
			{
				$tbody->text .= $tr->build();
				$tr = new Ashtree_Html_Tag('tr');
				$count = 0;
			}
		}//foreach
		
		if ($count < $options['columns'])
		{
			for ($i = $count; $i < $options['columns']; $i++)
			{
				$td = new Ashtree_Html_Tag('td');
				$td->text = '&nbsp;';
				$td->colspan = $options['group'];
				$tr->text .= $td->build();
			}//for
			$tbody->text .= $tr->build();
		}
		$table->text = $tbody->build();
		
		return $table->build();
	}//method array_to_table
	

	/**
	 * Copy and paste me
	 *
	 * @access
	 * @param
	 * @return
	 *
	 * 
	 *
	 */
	private function _print_form_control_panel()                   //Line comments
	{
		//Save the effort of working the controls layout out twice
		if (isset($this->form_controls)) return $this->form_controls;
		
		//Go ahead and create the controls layout
		$result = "";
		
		//$this->controls = (isset($this->controls)) ? (array)$this->controls : array('create', 'update', 'delete');
		
		foreach ($this->controls as $button)
		{
			$div = new Ashtree_Html_Tag('div');
			if (isset($button['position']))
			{
				$div->style = "float:{$button['position']}";
			}
			$div->text = $button['input'];
			$result .= $div->build();
		}//foreach
		
		return $this->form_controls = $result;
	}//_print_form_control_panel
	

	/**
	 * Copy and paste me
	 *
	 * @access
	 * @param
	 * @return
	 *
	 * 
	 *
	 */
	private function _print_form_field()                   //Line comments
	{
		$inline_controls_assigned = FALSE;
		
		$table = new Ashtree_Html_Tag('table');
		$table->class = 'form-table';
		$table->cellpadding = 1;
		$table->cellspacing = 1;
		#$table->width = '700px';
		
		$tbody = new Ashtree_Html_Tag('tbody');
		
		//Control placement if required
		if (($this->controls_position == 'top') || ($this->controls_position) == 'both')
		{
			$tr = new Ashtree_Html_Tag('tr');
			$tr->class = 'controls';
			
			//CONTROLS
			$td = new Ashtree_Html_Tag('td');
			$td->colspan = 3;
			$td->text = $this->_print_form_control_panel();
			$tr->text .= $td->build();
			
			$tbody->text .= $tr->build();
		}
		
		//Place all the fields
		foreach($this->field as $value)
		{
			$tr = new Ashtree_Html_Tag('tr');
			if ($value['hidden']) $tr->style = 'display:none';
			
			if ($value['control'])
			{
				if (!$inline_controls_assigned)
				{
					$inline_controls_assigned = TRUE;
					$tr->class = 'controls';
					
					//CONTROLS
					$td = new Ashtree_Html_Tag('td');
					$td->colspan = 3;
					$td->text = $this->_print_form_control_panel();
					$tr->text .= $td->build();
				}
			}
			else
			{
				//LABEL
				$td = new Ashtree_Html_Tag('td');
				$td->class = 'label';
				#$td->width = '100px';
				$td->text = $value['label'];
				$tr->text .= $td->build();
				
				//VALUE
				$td = new Ashtree_Html_Tag('td');
				$td->class = 'input';
				$td->text = $value['input'];
				$tr->text .= $td->build();
				
				//HELP
				$td = new Ashtree_Html_Tag('td');
				$td->class = 'help';
				$td->text = '&nbsp;';
				$tr->text .= $td->build();
			}
			
			$tbody->text .= $tr->build();
		}//foreach
		
		//Control placement if required
		if (($this->controls_position == 'bottom') || ($this->controls_position) == 'both')
		{
			$tr = new Ashtree_Html_Tag('tr');
			$tr->class = 'controls';
			
			//CONTROLS
			$td = new Ashtree_Html_Tag('td');
			$td->colspan = 3;
			$td->text = $this->_print_form_control_panel();
			$tr->text .= $td->build();
			
			$tbody->text .= $tr->build();
		}
		
		$table->text = $tbody->build();
		return $table->build();
	}//method _print_form_table
	

	/**
	 * Copy and paste me
	 *
	 * @access
	 * @param
	 * @return
	 *
	 */
	private function _get_form_input_type($type)
	{
		return ($type != '') ? explode(' ', $type) : (array)'text';
	}//function _get_form_input_type
	
	
	/**
	 * Copy and paste me
	 *
	 * @access
	 * @param
	 * @return
	 *
	 */
	private function _get_form_field_value($row, $id=NULL)
	{	
		$input = new Ashtree_Html_Tag('input');
		$input->id = (isset($row['id'])) ? $row['id'] : strtolower(str_replace(array('[', ']'), array('_', ''), $id));
		$input->name = $id;

		if (isset($row['value'])) $input->value = $row['value'];
		if (isset($row['class'])) $input->class = $row['class'];
		if (isset($row['text']))  $input->text  = $row['text'];
		if (isset($row['onclick']))  $input->onclick  = $row['onclick'];
		if (isset($row['onmouseover']))  $input->onmouseover  = $row['onmouseover'];
		if (isset($row['onmouseout']))  $input->onmouseout  = $row['onmouseout'];
		
		$types = $this->_get_form_input_type($row['type']);
		foreach($types as $type)
		{
			switch($type)
			{
				case 'hyperlink':
					$input->dom = 'a';
					if (isset($row['href'])) $input->href = $row['href'];			
					break;
				case 'select':
					$input->dom = 'select';
					$usekey = (isset($row['value']) && ($row['value'] == 'key'));
					$selected = (isset($row['selected'])) ? $row['selected'] : NULL;
					$options = (isset($row['options'])) ? $row['options'] : array();
					$options = (isset($row['default'])) ? array_merge((array)$row['default'], $options) : $options;
					$input->text = $this->array_to_option($options, $selected, $usekey);
					unset($input->value);
					break;
				case 'textarea':
					$input->dom = 'textarea';
					$input->text = ($input->text) ? $input->text : (($input->value) ? $input->value : ' ');
					unset($input->value);
					break;
				case 'checkbox':
					$checkbox = new Ashtree_Html_Tag('input');
					$checkbox->name = $input->name;
					$checkbox->type = 'hidden';
					$checkbox->value = '0';
					$checkbox->id = "{$input->id}_value";
					if ($row['value']) {
					    $input->checked = 'checked';
					    $checkbox->value = '1';
					}
					$input->type = $type;
					$input->onchange = "$('#{$input->id}_value').val(($(this).attr('checked')) ? 1 : 0)";
					unset($input->value);
					unset($input->name);
					break;
				case 'hidden':
					$input->type = $type;
					$this->field[$id]['hidden'] = TRUE;
					break;
				case 'disabled':
					unset($input->name);
					$input->disabled = 'disabled';
				case 'readonly':
					$input->style = 'background:#EEE';
					$input->style = 'color:#666';
					$input->style = 'padding:2px';
					$input->readonly = 'readonly';
					break;
				case 'submit':
				case 'button':
				case 'file':
				case 'text':
				default:
					$input->type = $type;
					break;
			}//switch
		}//foreach
		$result = $input->build();
		if ($checkbox) $result .= $checkbox->build();
		
		return $result;
	}//function _get_form_field_value


	/**
	 * Copy and paste me
	 *
	 * @access
	 * @param
	 * @return
	 *
	 */
	private function _get_form_fields($row, $parent=NULL)
	{
		$result = '';
		
		//Loop through all the defined fields
		foreach($row as $key=>$value)
	    {
			$tag = new Ashtree_Html_Tag();
			
			// Identify Fieldsets, Controls, or Hidden
			if (isset($value['type']))
			{
				
			    switch($value['type'])
				{
					case 'fieldset':
						$legend = new Ashtree_Html_Tag('legend');
						if (isset($value['legend'])) $legend->text = $value['legend'];
						$tag->text .= $legend->build();
						
						$tag->dom = 'fieldset';
						$tag->class = 'form-fieldset';
						$tag->text .= $this->_get_form_fields($value['text']);
						$result .= '<br />';
						break;
					case 'controls':
					    $tag->dom = 'div';
						$tag->class = 'form-controls';
						$tag->text .= $this->_get_form_fields($value['text'], 'controls');
						break;
					case 'hidden':
						$tag->dom = 'div';
						$tag->style = 'display:none';
						$tag->text .= $this->_get_form_field_value($value, $key);
						break;
					default:
						if ($parent != 'controls') 
						{
							$label = new Ashtree_Html_Tag('label');
							$label->for = strtolower(str_replace(array('[', ']'), array('_', ''), $key));
							$label->text = (isset($value['label'])) ? $value['label'] : '&nbsp;';
							$tag->text .= $label->build();
							$this->field[$key] = TRUE;
						}
						$tag->dom = 'div';
						$tag->class = 'form-fieldrow';
						$tag->text .= $this->_get_form_field_value($value, $key);
						if (isset($value['caption'])) $tag->text .= $value['caption'];
						break;
				}//switch
			}
			$result .= $tag->build();
			
		}//foreach
		return $result;
	}//_get_form_fields
	
	

	/**
	 * Outputs a form structure to be filled with rows of array data
	 *
	 * @access private
	 * @param
	 * @return
	 *
	 */
	private function _print_form($fields)                   //Line comments
	{
		$form = new Ashtree_Html_Tag('form');
		$form->action = $this->action;
		$form->method = $this->method;
		$form->enctype = $this->enctype;
		$form->class = $this->class;
		$form->onsubmit = "$('input,select,textarea').attr('readonly', true); this.form.submit();";
		
		$form->text = $this->_get_form_fields($fields);
		if (isset($this->controls)) 
		{
			$tag = new Ashtree_Html_Tag('div');
			$tag->class = 'form-controls';
			$tag->text = $this->_get_form_fields($this->controls, 'controls');
			$form->text .= $tag->build();
		}
		//$form->text = $this->_print_form_table(); 
		
		return $this->form = $form->build();
		
	} //method _print_form
	
	
	/**
	 * Copy and paste me
	 *
	 * @access
	 * @param
	 * @return
	 *
	 * 
	 *
	 */
	public function create()                   //Line comments
	{
		$mysql = new Ashtree_Common_Connection($this->connection);

		if (!isset($_REQUEST['id']) || ($_REQUEST['id'] == ''))
		{
			$this->field['id']['value'] = $_REQUEST['id'] = strtolower(substr_replace(uniqid(), Ashtree_Common_Core::random_alphanumeric(5), 0, 3));
		}	
		
		foreach($_REQUEST as $key=>$value)
		{
			if (isset($this->field[$key]))
			{
				if (is_array($value))
				{
					if (!empty($value) && ($value[0] != '')) $mysql->sanitize(json_encode($value), $key);
					else $mysql->$key = MYSQL_NULL;
				}
				else
				{
					$mysql->sanitize(str_replace('"', "'", $value), $key);
				}
			
				$fields_array[] = "`{$key}`";
				$values_array[] = "{$mysql->$key}";
				$update_array[] = "`{$key}` = VALUES(`{$key}`)";
			}
		}//foreach

		$fields_list = implode(",\n\t\t\t\t", $fields_array);
		$values_list = implode(",\n\t\t\t\t", $values_array);
		$update_list = implode(",\n\t\t\t\t", $update_array);
		$mysql->query = <<<HEREDOC
			INSERT INTO {$this->table} (
				{$fields_list}
			) VALUES (
				{$values_list}
			)
			ON DUPLICATE KEY UPDATE
				{$update_list}
HEREDOC;

		if (isset($this->table))
		{
			$mysql->invoke();
			$this->session = (isset($this->session)) ? $this->session : $this->connection;
			$msg = new Ashtree_Common_Message($this->session);
			if ($mysql->num_rows) 
			{
				$msg->message = "SUCCESS:: FORM CREATE was completed successfully.";
			
				if (isset($this->redirect))
				{
					if (strpos($this->redirect, '{$') !== FALSE)
					{
						preg_match_all('/{\$(.*?)}/i', $this->redirect, $matches);
						$id = $matches[1][0];
						$this->redirect = str_replace("{\${$id}}", $_REQUEST[$id], $this->redirect);
					}
				
					$this->debug->title = "INFO:: redirecting to '{$this->redirect}'";
					redirect($this->redirect);
				}
				$this->debug->title = "WARN:: Redirect recommended after FORM CREATE";
			}
			else
			{
				$msg->message = "FAILURE:: FORM CREATE could not be completed.";
				redirect($_SERVER['REQUEST_URI']);
			}
		}
		
	} //method create
	
	
	/**
	 * Copy and paste me
	 *
	 * @access
	 * @param
	 * @return
	 *
	 * 
	 *
	 */
	public function update()                   //Line comments
	{
		$mysql = new Ashtree_Common_Connection($this->connection);
		$primary_key = 'id';
		if (isset($_REQUEST['primary_key'])) 
		{
		    $primary_key = $_REQUEST['primary_key'];
		    unset($_REQUEST['primary_key']);
		}
		
		foreach($_REQUEST as $key=>$value)
		{
			
			if (isset($this->field[$key]))
			{
				if (is_array($value))
				{
					if (!empty($value) && ($value[0] != '')) $mysql->sanitize(json_encode($value), $key);
					else $mysql->$key = MYSQL_NULL;
				}
				else
				{
					$mysql->sanitize(str_replace('"', "'", $value), $key);
				}
			
				$values_array[] = "`{$key}` = {$mysql->$key}";
			}
		}//foreach
		$values_array[] = "`updated` = '" . date('Y-m-d H:i:s') . "'";
		
		$values_list = implode(",\n\t\t\t\t", $values_array);
		$mysql->query = <<<HEREDOC
			UPDATE {$this->table} 
			SET
				{$values_list}
			WHERE `{$primary_key}` = {$mysql->$primary_key}
HEREDOC;

		if (isset($this->table))
		{
			$mysql->invoke();
			$this->session = (isset($this->session)) ? $this->session : $this->connection;
			$msg = new Ashtree_Common_Message($this->session);
			if ($mysql->num_rows) 
			{
				$msg->message = "SUCCESS:: FORM UPDATE completed successfully.";
			
				if (isset($this->redirect))
				{
					if (strpos($this->redirect, '{$') !== FALSE)
					{
						preg_match_all('/{\$(.*?)}/i', $this->redirect, $matches);
						$id = $matches[1][0];
						$this->redirect = str_replace("{\${$id}}", $_REQUEST[$id], $this->redirect);
					}
				
					$this->debug->title = "INFO:: redirecting to '{$this->redirect}'";
					redirect($this->redirect);
				}
				$this->debug->title = "WARN:: Redirect recommended after FORM UPDATE";
			}
			else
			{
				$msg->message = "FAILURE:: FORM UPDATE could not be completed or no changes were made.";
				#redirect($_SERVER['REQUEST_URI']);
			}
		}	   
	} //method update
	
	
	/**
	 * Copy and paste me
	 *
	 * @access
	 * @param
	 * @return
	 *
	 * 
	 *
	 */
	public function delete()                   //Line comments
	{
		$msg = new Ashtree_Common_Message($this->session);
		$mysql = new Ashtree_Common_Connection($this->connection);
		$primary_key = 'id';
		$this->session = (isset($this->session)) ? $this->session : $this->connection;
	    $fields = current($this->fields);
	    $success = FALSE;
	    
		if (isset($_REQUEST['pk'])) 
		{
		    $primary_key = $_REQUEST['pk'];
		    unset($_REQUEST['pk']);
		}
	   
	    if (isset($this->table) && is_array($this->table)) 
	    {
	    	$tables = $this->table;
	    }
	    else if ($fields['type'] == 'fieldset') 
	    {
	    	foreach($this->fields as $key=>$val)
	    	{
	    		if ($val['type'] == 'fieldset')
	    		{
	    			if (isset($val['table'])) $tables[] = $val['table'];
	    			else $tables[] = $key; 
	    		}
	    	}
	    }
	    else 
	    {
	    	$tables = (array)$this->table;
	    }
	    
	    $mysql->sanitize($_REQUEST[$primary_key], $primary_key);
	    foreach($tables as $table)
	    {
	    	$mysql->query = "
				DELETE FROM {$table} 
				WHERE {$primary_key} = {$mysql->$primary_key}
			";
			$mysql->invoke();
			if ($mysql->num_rows) $success = TRUE;
	    }

	    if (is_array($this->related))
	    {
	    	foreach($this->related as $key=>$val)
	    	{
	    		if ($key != 'file')
	    		{
	    			$mysql->sanitize($_REQUEST[$val[1]], 'related');
	    			$mysql->query = "
						DELETE FROM {$key} 
						WHERE {$val[0]} = {$mysql->related}
					";
	    			$mysql->invoke();
					if ($mysql->num_rows) $success = TRUE;
	    		}
	    	}
	    }
	    
		if ($success)
		{
			$msg->message = "SUCCESS:: FORM DELETE completed successfully.";
			$this->redirect_delete = (isset($this->redirect_delete)) ? $this->redirect_delete : substr_replace($_SERVER['REQUEST_URI'], 'create', strpos($_SERVER['REQUEST_URI'], 'update'), strlen($_SERVER['REQUEST_URI']));	
			redirect($this->redirect_delete);
		}
		else
		{
			$msg->message = "FAILURE:: FORM DELETE did not do anything.";
			redirect($_SERVER['REQUEST_URI']);
		}
		
	} //method delete
	
	
	/**
	 * Copy and paste me
	 *
	 * @access
	 * @param
	 * @return
	 *
	 * 
	 *
	 */
	public function copyAndPasteMe()                   //Line comments
	{
	
	} //method copyAndPasteMe


} //class Ashtree_Html_Form

?>

