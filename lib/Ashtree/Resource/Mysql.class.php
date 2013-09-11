<?php
       
class Ashtree_Resource_Mysql
{
	/**
	 * @access private
	 * @var Object $_debug 
	 */
	private $_debug;
	
	/**
	 * 
	 */
	private $_connection;
	
	/**
	 * 
	 */
	private $_database = FALSE;
	
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
	 * @param Array $connection
	 */
	public function __construct($conn)
	{
	   
	   $this->_debug = Ashtree_Common_Debug::instance();
       $this->_debug->log("INFO", "Connecting {$conn['ASH_conn_username']}@{$conn['ASH_conn_hostname']} to {$conn['ASH_conn_database']}");
	   
       if ($this->_connection = @mysql_connect($conn['ASH_conn_hostname'], $conn['ASH_conn_username'], $conn['ASH_conn_password']))
	   {
	       
	       #if (@mysql_select_db($conn['ASH_conn_database'], $this->_connection)) $this->_debug->status('W/DB');
           #else $this->_debug->status('WO/DB');
           if (!defined('MYSQL_NULL')) define('MYSQL_NULL', mysql_real_escape_string("NULL", $this->_connection), 1);
           
           if ($conn['ASH_conn_database'])
           {
               $this->_database = TRUE;
               @mysql_select_db($conn['ASH_conn_database'], $this->_connection);  
           } 
           
           $this->_debug->status('OK');
	   }
	   
	}
	
	/**
	 * @param String $query
	 */
    public function invoke($query)
	{
	    $resource       = FALSE;
	    $result         = FALSE;
	    $this->affected = 0;
	    $this->error    = FALSE;
	    
	    if (isset($query) && ($query != ''))
	    {
	        $this->_debug->log("INFO", "Performing query: " . dump($query, 1));
	        if ($resource = @mysql_query($query, $this->_connection))
			{			
				switch(substr(strtolower(trim($query)), 0, 6))
				{
					case 'insert':
					case 'update':
					case 'replac':
					case 'delete':
						$this->affected = @mysql_affected_rows($this->_connection);
						break;
					
					case 'select':
						$this->affected = @mysql_num_rows($resource);
					    $result = array();
						$row = mysql_fetch_assoc($resource);
						do {
							$result[] = $row;
						} while($row = mysql_fetch_assoc($resource));
						break;
					
					default:
						$this->affected = 1;
				}
				$this->_debug->status("{$this->affected} ROWS");
			}
			else
			{
			    $this->_debug->status("FAILURE");
				$error_number = mysql_errno($this->_connection);
				$this->error  = mysql_error($this->_connection);
				$this->_debug->log("FAILURE", "Err no. {$error_number}: {$this->error}");
			}
	    }

	    return $result;
	}
	
}