<?php 

class Ashtree_Database_Model extends Ashtree_Model {
	
	private $connection;
	private $tablename;
	private $join;
	
	public function __construct() {
		
	}
	
	public function setConnection($connection) {
		$this->connection = $connection;
	}
	
	public function setModel() {
	
	}
	
	public function setTable($tablename) {
		$this->tablename = $tablename;
	}
	
	public function setRelationship($field_a, $field_b, $operator="=") {
		
		$join_a = explode('.', $field_a);
		$join_b = explode('.', $field_b);
		if (!isset($this->tablename)) die('Must use setTable() to continue');
		$join_table = ($join_a[0] != $this->tablename) ? $join_a[0] : $join_b[0];
		$this->join[$join_table][] = $join_a[1];
		$this->join[$join_table][] = $join_b[1];
		$this->join[$join_table][] = $operator;
	}
	
	public function loadModel() {
		
		$sql = new Ashtree_Database_Connection($this->connection);
		$sql->query = "SELECT * FROM {$this->tablename}";
		foreach($this->join as $tablename=>$fieldname) {
			$operator = $fieldname[2];
			$sql->query .= "LEFT JOIN {$tablename} ON {$tablename}.{$fieldname[0]} {$operator} {$this->tablename}.{$fieldname[1]}";
		}
		$sql->invoke();
		
	}
	
	public function __invoke() {
	
	}
}

