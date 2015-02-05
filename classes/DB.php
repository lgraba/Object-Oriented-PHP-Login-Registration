<?php
/* Database Wrapper using PDO */

class DB {
	private static $_instance = null;
	private $_pdo,
			$_query,
			$_error = false,
			$_results,
			$_count = 0;

	// This is run when the class is instantiated, and actually connects to the database.
	private function __construct() {
		try {
			// Creation of PDO using configuration parameters in init.php called through config.php
			$this->_pdo = new PDO('mysql:host=' . Config::get('mysql/host') . ';dbname=' . Config::get('mysql/db'), Config::get('mysql/username'), Config::get('mysql/password'));
			// echo 'Connected';
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}
	
	// Checks to see if instance is defined. If not, it instantiates a new DB object. If so, it just returns the instance.
	public static function getInstance() {
		if(!isset(self::$_instance)) {
			self::$_instance = new DB();
		}
		return self::$_instance;
	}
	
	// Generic query method for easy, secure SQL queries.
	public function query($sql, $params = array()) {
		$this->_error = false;
		if($this->_query = $this->_pdo->prepare($sql)) {
			$x = 1;
			if(count($params)) {
				foreach($params as $param) {
					$this->_query->bindValue($x, $param);
					$x++;
				}
			}
			
			if($this->_query->execute()) {
				$this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
				$this->_count = $this->_query->rowCount();
			} else {
				$this->_error = true;
			}
		}
		
		return $this;
	}
	
	// Allows generalized SQL queries using a standard set of operators and parameters for the action, table, and where in the table to look for and return results.
	public function action($action, $table, $where = array()){
		if(count($where) === 0) {	// Added to allow for a null $where value to select all results in a table.
			$sql = "{$action} FROM {$table}";
			if(!$this->query($sql)->error()) {
				return $this;
			}
		} else if(count($where) === 3) {
			$operators = array('=', '>', '<', '>=', '<=');
			
			$field = $where[0];
			$operator = $where[1];
			$value = $where[2];
			
			if(in_array($operator, $operators)) {
				$sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";
				if(!$this->query($sql, array($value))->error()) {
					return $this;
				}
			}
		}
		return false;
	}
	
	public function get($table, $where) {
		return $this->action('SELECT *', $table, $where);
	}
	
	public function delete($table, $where) {
		return $this->action('DELETE', $table, $where);
	}
	
	public function insert($table, $fields = array()) {
		$keys = array_keys($fields);
		$values = '';
		$x = 1;
		
		foreach($fields as $field) {
			$values .= '?';
			if($x < count($fields)) {
				$values .= ', ';
			}
			$x++;
		}
		
		$sql = "INSERT INTO {$table} (`" . implode('`, `', $keys) . "`) VALUES ({$values})";
		
		if(!$this->query($sql, $fields)->error()) {
			return true;
		}
		return false;
	}
	
	public function update($table, $id, $fields) {
		$set = '';
		$x = 1;
		
		foreach($fields as $name => $value) {
			$set .= "{$name} = ?";
			if($x < count($fields)) {
				$set .= ', ';
			}
			$x++;
		}
		
		$sql = "UPDATE {$table} SET {$set} WHERE id = {$id}";
		
		if(!$this->query($sql, $fields)->error()) {
			return true;
		}
		return false;		
	}
	
	public function results() {
		return $this->_results;
	}
	
	public function first_result() {
		return $this->results()[0];
	}
	
	// Error method
	public function error() {
		return $this->_error;
	}
	
	// Count method to count resultant rows.
	public function count() {
		return $this->_count;
	}
}

?>