<?php 

class Ashtree_Database_Pdo {
	
	private $_connection;
	
	public function __construct($conn) {
		$this->_debug = Ashtree_Common_Debug::instance();
		$this->_debug->log("INFO", "Connecting {$conn['ash_conn_username']}@{$conn['ash_conn_hostname']} to {$conn['ash_conn_database']}");
		
		try {
			$this->_connection = new PDO(
				"{$conn['ash_conn_datatype']}:host={$conn['ash_conn_hostname']};dbname={$conn['ash_conn_database']}", 
				$conn['ash_conn_username'], 
				$conn['ash_conn_password'],
				array(PDO::ATTR_PERSISTENT => true)
			);
			$this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->_debug->status('OK');
		} catch(PDOException $e) {
			$this->_debug->status('ERROR', $e->getMessage());
			echo 'ERROR: ' . $e->getMessage();
		}
		
	}
	
	public function getConnection() {
		return $this->_connection;
	}
}