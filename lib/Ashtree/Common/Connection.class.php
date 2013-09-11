<?php
/**
 * Adapter for multiple connection types
 * 
 * @author andrew.nash
 */

class Ashtree_Common_Connection 
{
	/**
	 * @access private
	 * @var Object $_instance
	 */
	private static $_instance = array();
	
	private static $_connection = 'none';
	
	private $_params = array();
	
	/**
	 * @access private
	 * @var Object $_debug 
	 */
	private $_debug;
	
	/**
	 * @access private
	 * @var Objec $_sql
	 */
	private $_sql;
	
	/**
	 * 
	 */
	public static $_defined = array();
	
	/**
	 * 
	 */
	public $recordset;
	
	/**
	 * Placeholder for the last query performed
	 * @access public
	 * @var String $query
	 */
	public $query;
	
	/**
	 * Indictaes the number of rows
	 * affected by SQL queries
	 * i.e. select, insert, update, delete, etc
	 * @access public
	 * @var int $affected
	 */
	public $affected;
	
	/**
	 * 
	 */
    public $error;
	
	/**
	 * Makes the name of the database available for queries,
	 * but does not select it so that multiple databases can be used
	 * @access public
	 * @var int $affected
	 */
	public $db;

	
	/**
	 * @access public
	 * @param String $connection
	 */
	public function __construct($connection=NULL)
	{
	    if (isset($connection))
	    {
	        $this->prefix = (isset(self::$_defined[$connection]['snow_conn_dbprefix'])) ? self::$_defined[$connection]['snow_conn_dbprefix'] : '';
	        $this->_sql = new Ashtree_Resource_Mysql(self::$_defined[$connection]);
    	    self::$_connection = $connection;
	    }
	}
	
	/**
	 * 
	 */
	public function __get($key)
	{
		return array_key_exists($key, $this->_params) ? $this->_params[$key] : "''";
	}
	
	public function invoke($query=NULL)
	{
	    if (isset($query)) $this->query = $query;
	    
	    $this->recordset = $this->_sql->invoke($this->query);
	    $this->affected  = $this->_sql->affected;
	    $this->error     = $this->_sql->error;
	}
	
	/**
	 * Singleton instance
	 * Used when wanting to maintain the same instance of the class 
	 * and share variables no matter how many functions may call it
	 * @param Mixed $connection
	 * @return Object
	 */
    public static function instance($connection=NULL)
	{
	    // Define the connection
	    // if it is a new one
	    if (is_array($connection)) {
	        self::define($connection);
	        $connname = $connection = $connection['snow_conn_connname'];
	    } else if ($connection) {
	        $connname = $connection;
	    } else {
	        $connname = self::$_connection;
	    }
	    
	    if (!isset(self::$_instance[$connname])) 
		{
			$class = __CLASS__;
			self::$_instance[$connname] = new $class($connection);
		}

		return self::$_instance[$connname];
	}
	
	/**
	 * Define a connection
	 * @param Array $connection
	 */
	public static function define($connection)
	{
		$connname = $connection['snow_conn_connname'];
		unset($connection['snow_conn_connname']);
		
		self::$_defined[$connname] = $connection;
		
		$dbg = Ashtree_Common_Debug::instance();
		$dbg->log("INFO", "Defining new '{$connname}' {$connection['snow_conn_datatype']} connection: {$connection['snow_conn_username']}@{$connection['snow_conn_database']}");
        $dbg->status("OK");
	}
	
	/**
	 * 
	 * @access
	 * @param
	 * @return
	 */
	public function getAllRows()
	{
		return $this->recordset;

	}
	
	/**
	 * 
	 * @access
	 * @param
	 * @return
	 */
	public function getFirstRow()
	{
		$result = reset($this->recordset);
		next($this->recordset);
		return $result;
	}
	
	/**
	 * 
	 * @access
	 * @param
	 * @return
	 */
	public function getNextRow()
	{
		$result = current($this->recordset);
		next($this->recordset);
		return $result;
	}
	
	/**
	 * Rearranges the recordset
	 * so that the two field parameters are mapped
	 * to each other
	 * @access public
	 * @param String $fieldname
	 * @param [Boolean $unique)
	 * @return Array
	 */
	public function mapAllRowsInFields($key, $value)
	{
		$tmp_recordset = array();
		if (@array_key_exists($key, $this->recordset[0]) && @array_key_exists($value, $this->recordset[0])) {
			foreach($this->recordset as $row) {
				$tmp_recordset[$row[$key]] = $row[$value];
			}//foreach
		}
		
		return $tmp_recordset;
	}
			
	/**
	 * Rearranges the recordset
	 * so that the numerical key of each row
	 * becomes a specified unique field from the row 
	 * @access public
	 * @param String $fieldname
	 * @param [Boolean $unique)
	 * @return Array
	 */
	public function getAllRowsByField($fieldname, $unique=TRUE)
	{
		$tmp_recordset = array();
		if (@array_key_exists($fieldname, $this->recordset[0])) {
			foreach($this->recordset as $row) {
				$key = $row[$fieldname];
				unset($row[$fieldname]);
				if ($unique) $tmp_recordset[$key] = $row;
				else $tmp_recordset[$key][] = $row;
			}//foreach
		}
		
		return $tmp_recordset;
	}
	
	/**
	 * getAllRowsInField rearranges the array
	 * so that the a list of values is returned
	 * inside a 1 dimensional array
	 * @access public
	 * @param string $fieldname
	 * @return array
	 */
	public function getAllRowsInField($fieldname)
	{
		$tmp_recordset = array();	
		if (@array_key_exists($fieldname, $this->recordset[0])) {
			foreach($this->recordset as $row) {
				$tmp_recordset[] = $row[$fieldname];
			}//foreach
		}
		
		return $tmp_recordset;
		
	}
	
	/**
	 * Output a comma searated list
	 * that can be used in another SQL statement
	 * @example WHERE something IN ('something', 'in', 'list', 'of', 'items')
	 * @access public
	 * @param string $fieldname
	 * @return string
	 */
	public function listAllRowsInField($fieldname)
	{
		$tmp_recordset = array();
		if (@array_key_exists($fieldname, $this->recordset[0])) {
			foreach($this->recordset as $row) {
				$tmp_recordset[] = "'{$row[$fieldname]}'";
			}//foreach
		}
		
		return implode(", ", $tmp_recordset);
	} 
	
	/**
	 * Verifies no SQL injection attempt
	 * before inserting into query
	 * @access public
	 * @param String $value
	 * @param [String $key]
	 * @return String
	 */
	public function sanitize($value, $key=NULL)
	{	
		if (is_array($value))
		{
			foreach ($value as $k=>$v)
			{
				if (is_array($v))
				{
				       foreach($v as $i=>$j)
				       {
				           $result = mysql_real_escape_string(urldecode($j));
				           $this->_params[$k][$i] = is_numeric($result) ? $result : "'{$result}'";
				       }//foreach
				}
				else 
				{
			        $result = mysql_real_escape_string(urldecode($v));
				    $this->$k = is_numeric($result) ? $result : "'{$result}'";
				}
			}//foreach
		}
		else if (isset($key) && ($key != ''))
		{
			$result = mysql_real_escape_string(urldecode($value));
			$this->$key = is_numeric($result) ? $result : "'{$result}'";
		}
		else
		{
			$result = mysql_real_escape_string($value);
			return is_numeric($result) ? $result : "'{$result}'";
		}
	}
	
	
}