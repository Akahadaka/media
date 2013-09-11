<?php 

class Ashtree_Database_Connection {
	
	public static $_defined = array();
	
	public $query;
	public $affected;
	
	private static $_instance   = array();
	private static $_connection = 'none';
	
	private $_DBH;
	private $_STH;
	
	/**
	 * 
	 */
	public function __construct($connection=NULL) {
		if (isset($connection)) {
	        $this->prefix = (isset(self::$_defined[$connection]['ash_conn_dbprefix'])) ? self::$_defined[$connection]['ash_conn_dbprefix'] : '';
	        $this->_DBH = new Ashtree_Database_Pdo(self::$_defined[$connection]);
    	    self::$_connection = $connection;
	    }
	}
	
	/**
	 * Define a connection
	 * @param Array $connection
	 */
	public static function define($connection) {
		$connname = $connection['ash_conn_connname'];
		unset($connection['ash_conn_connname']);
	
		self::$_defined[$connname] = $connection;
	
		$dbg = Ashtree_Common_Debug::instance();
		$dbg->log("INFO", "Defining new '{$connname}' {$connection['ash_conn_datatype']} connection: {$connection['ash_conn_username']}@{$connection['ash_conn_database']}");
		$dbg->status("OK");
	}
	
	/**
	 * Singleton instance
	 * Used when wanting to maintain the same instance of the class
	 * and share variables no matter how many functions may call it
	 * @param Mixed $connection
	 * @return Object
	 */
	public static function instance($connection=NULL) {
		// Define the connection
		// if it is a new one
		if (is_array($connection)) {
			self::define($connection);
			$connname = $connection = $connection['ash_conn_connname'];
		} else if ($connection) {
			$connname = $connection;
		} else {
			$connname = self::$_connection;
		}
		 
		if (!isset(self::$_instance[$connname])) {
			$class = __CLASS__;
			self::$_instance[$connname] = new $class($connection);
		}
	
		return self::$_instance[$connname];
	}
	
	/**
	 *
	 */
	public function query($query=NULL) {
		if (isset($query)) $this->query = $query;
		if (!isset($this->query)) die('A SQL statement is required before executing.');
		$conn = $this->_DBH->getConnection();
		$stmnt = $conn->prepare($this->query);
		$this->_STH = $stmnt;
	}
	
	public function escape($string) {
		$conn = $this->_DBH->getConnection();
		return $conn->quote($string);
	}
	
	/**
	 * 
	 */
	public function bind($model, $value=NULL) {
		if (!isset($this->query)) die('A SQL statement is required for binding parameters to it.');
		$conn = $this->_DBH->getConnection();
		$stmnt = $conn->prepare($this->query);
		if (isset($value)) {
			$stmnt->bindValue($model, $value);
			$this->query = str_replace($model, "'{$value}'", $this->query);
		} else {
			foreach((array)$model as $key=>$value) {
				if (strpos($this->query, ":{$key}")) {
					$stmnt->bindValue(":{$key}", $value);
					$this->query = str_replace(":{$key}", "'{$value}'", $this->query);
				}
			}
		}
		$this->_STH = $stmnt;
	}
	
	/**
	 * 
	 */
	public function invoke() {
		return $this->__invoke();
	}
	
	/**
	 * 
	 */
	public function __invoke() {
		$dbg = Ashtree_Common_Debug::instance();
		try {
			$this->_STH->execute();
			$this->affected = $this->_STH->rowCount();
			$dbg->log('INFO', 'Running query: <pre>' . $this->query . '</pre>');
			$dbg->status("{$this->affected} ROWS");
		} catch(PDOException $e) {
			$dbg->log('WARN', 'Running query: <pre>' . $this->query . '</pre>');
			$dbg->log('ERROR', $e->getMessage());
			$dbg->status("FAILURE");
		}
	}
	
	/**
	 *
	 */
	public function getStatement() {
		return $this->_STH;
	}
	
	/**
	 * 
	 */
	public function getInsertId() {
		return $this->_DBH->getConnection()->lastInsertId();
	}
	
	/**
	 *
	 */
	public function getFirstRow() {
		return $this->_STH->fetch(PDO::FETCH_ASSOC);
	}
	
	/**
	 * 
	 */
	public function getAllRows() {
		return $this->_STH->fetchAll(PDO::FETCH_ASSOC);
	}
	
	/**
	 *
	 */
	public function getAllRowsInField($fieldname, $unique=TRUE) {
		$results = $this->_STH->fetchAll(PDO::FETCH_ASSOC);
		
		$tmp_recordset = array();
		if (@array_key_exists($fieldname, $results[0])) {
			foreach($results as $row) {
				$key = $row[$fieldname];
				unset($row[$fieldname]);
				if ($unique) $tmp_recordset[$key] = $row;
				else $tmp_recordset[$key][] = $row;
			}//foreach
		}
		
		return $tmp_recordset;
	}
	
	/**
	 *
	 */
	public function getAllRowsByField($fieldname) {
		$results = $this->_STH->fetchAll(PDO::FETCH_ASSOC);
	
		$tmp_recordset = array();	
		if (@array_key_exists($fieldname, $results[0])) {
			foreach($results as $row) {
				$tmp_recordset[] = $row[$fieldname];
			}//foreach
		}
		
		return $tmp_recordset;
	}
	
}