<?php

namespace Jawan\Core\Data\Database;

use Jawan\Core\App;
use \PDO;
use \PDOException;

/**
 * Database and Query Builder class
 * 
 * This is the base Jawan framework database class.
 * It includes the database connection, sql execuation
 * and query builder methods.
 */
class Database {
	
	/**
	 * @var Jawan\Core\App
	 */
	private $app;
	
	/**
	 * @var PDO
	 */
	private static $connection;
	
	/**
	 * @var array<string>
	 */
	private $_set = array();
	
	/**
	 * @var array<string>
	 */
	private $_where = array();
	
	/**
	 * @var string
	 */
	private $_last_query = '';
	
	/**
	 * @var string
	 */
	private $_insert_pattern = 'INSERT INTO %s (%s) VALUES(%s)';
	
	/**
	 * @var string
	 */
	private $_update_pattern = 'UPDATE %s SET %s WHERE %s'; 
	
	/**
	 * @var string
	 */
	private $_delete_pattern = 'DELETE FROM %s WHERE %s'; 
	
	/**
	 * @var string
	 */
	private $_select_pattern = 'SELECT %s FROM %s %s'; 
	
	/**
	 * @var string
	 */
	private $_table_name = '';
	
	/**
	 * @var object
	 */
	private $_last_result = false;
	
	/**
	 * @var array<string>
	 */
	private $_binding = [];
	
	/**
	 * @var array<string>
	 */
	private $_columns = [];
	
	/**
	 * @var array<string>
	 */
	private $_order_by = [];
	
	/**
	 * @var string
	 */
	private $_extra = ';';
	
	/**
	 * Class constructor
	 * 
	 * @param Jawan\Core\App $app
	 * 
	 * @return void
	 */
	public function __construct(App $app)
	{
		$this->app = $app;
		$this->startConnection();
	}
	
	/**
	 * Connect to database
	 * 
	 * @return void
	 * 
	 * @throws PDOException
	 */
	protected function startConnection()
	{
		if (static::$connection instanceof PDO) {
			return;
		}
		
		try {
				static::$connection = new PDO(
					$this->app->config->read('database.dsn'), 
					$this->app->config->read('database.user'), 
					$this->app->config->read('database.password'), 
					array(PDO::MYSQL_ATTR_INIT_COMMAND 	=> 'SET NAMES \'UTF8\'',
							  PDO::ATTR_ERRMODE 						=> PDO::ERRMODE_EXCEPTION,
								PDO::ATTR_DEFAULT_FETCH_MODE  => PDO::FETCH_OBJ,));
		} catch (PDOException $e) {
				throw $e;
		}
	}
	
	/**
	 * Disconnect from the database
	 * 
	 * @return void
	 */
	public function disconnect()
	{
		static::$connection = null;
	}
	
	/**
	 * Return current PDO instance
	 * 
	 * @return PDO
	 */
	public function getConnection()
	{
		return static::$connection;
	}
	
	
	//==================================================================
	// QUERY BUILDER
	//==================================================================
	
	/**
	 * Set field and its value to the query
	 * 
	 * @param mixed $key
	 * @param string|NULL $value
	 * 
	 * @return Jawan\Core\Data\Database\Database
	 */
	public function set($key, $value = null)
	{
		if (is_array($key)) {
			foreach ($key as $k => $v)
			{
				$key[$k] = jf_encode_special_char($v);
			}
			
			$this->_set = array_merge($this->_set, $key);
			return $this;
		}
		
		$this->_set[$key] = jf_encode_special_char($value);
		return $this;
	}
	
	/**
	 * Set order by column
	 * 
	 * @param mixed $order
	 * 
	 * @return Jawan\Core\Data\Database\Database
	 */
	public function order_by($order)
	{
		if (is_array($order)) {
			$this->_order_by = array_merge($this->_order_by, $order);
			return $this;
		}
		
		$this->_order_by[] = $order;
		return $this;
	}
	
	/**
	 * Set extra sql to the end of the query
	 * 
	 * @param string $extra
	 * 
	 * @return Jawan\Core\Data\Database\Database
	 */
	public function extra($extra)
	{
		$this->_extra = $extra;
		return $this;
	}
	
	/**
	 * Set where conditions fields
	 * 
	 * @param mixed $key
	 * @param string|NULL $value
	 * 
	 * @return Jawan\Core\Data\Database\Database
	 */
	public function where($key, $value = null)
	{
		if (is_array($key)) {
			foreach($key as $k => $v)
			{
				$key[$k] = jf_encode_special_char($v);
			}
			
			$this->_where = array_merge($this->_where, $key);
			return $this;
		}
		
		$this->_where[$key] = jf_encode_special_char($value);
		return $this;
	}
	
	/**
	 * @param mixed $value
	 * 
	 * @return void
	 */
	public function setBindValue($value)
	{
		if (is_array($value)) {
			$this->_bindings = array_merge($this->_bindings, $value);
		}
		
		$this->_bindings[] = $value;
	}
	
	/**
	 * Compile order by values
	 * 
	 * @return string Compiled order by string
	 */
	public function compileOrderBy()
	{
		$val = '';
		
		foreach ($this->_order_by as $k => $v)
		{
			$val .= '`'.$k.'` '.$v.', ';
		}
		
		$val = rtrim($val, ', ');
		
		
		return $val;
	}
	
	/**
	 * Compile columns
	 * 
	 * @return string
	 */
	public function compileColumns($columns = null)
	{
		if ($columns === null) $columns = $this->_columns;
		
		if(count($columns) === 0) {
			return '*';
		}
		
		foreach( $columns as $k => $v)
		{
			$columns[$k] = '`'.$v.'`';
		}
		
		return implode(', ', $columns);
	}
	
	/**
	 * Compile where fields and values
	 * 
	 * @param array|NULL @where
	 * 
	 * @return string
	 */
	public function compileWhere($where = null)
	{
		if ($where === null) $where = $this->_where;
		
		$keys = '';
		
		foreach($where as $k => $v){
			$keys .= '`'.$k.'`=? AND ';
			$this->setBindValue($v);
		}
			
		$keys = rtrim($keys,' AND ');
		
		return $keys;
	}
	
	/**
	 * Compile set values
	 * 
	 * @param array|NULL $set
	 * 
	 * @return string
	 */
	public function compileSet($set = null)
	{
		if ($set === null) $set = $this->_set;
		
		$keys = '';
		
		foreach($this->_set as $k => $v){
			$keys .= '`'.$k.'`=? , ';
			$this->setBindValue($v);
		}
		$keys = rtrim($keys,' , ');
		
		return $keys;
	}
	
	/**
	 * Set columns
	 * 
	 * @param array|string $columns
	 * 
	 * @return Jawan\Core\Data\Database\Database
	 */
	public function columns($columns)
	{
		if (is_array($columns)) {
			$this->_columns = array_merge($this->_columns, $columns);
			return $this;
		}
		
		$this->_columns[] = $columns;
		return $this;
	}
	
	/**
	 * Scape and set table name
	 * 
	 * @param string $table
	 * 
	 * @return Jawan\Core\Data\Database\Database
	 */
	public function table($table)
	{
		$this->_table_name = '`'.$table.'`';
		return $this;
	}
	
	/**
	 * Alias of table()
	 * 
	 * @param string $table
	 * 
	 * @return Jawan\Core\Data\Database\Database
	 */
	public function from($table)
	{
		return $this->table($table);
	}
	
	/**
	 * Execute insert command
	 * 
	 * @param string|NULL $table
	 * 
	 * @return int
	 */
	public function insert($table = null)
	{
		$this->_bindings = [];
		
		if ($table !== null) $this->table($table);
		
		$columns = implode(', ', array_keys($this->_set));
	
		$values = rtrim(str_repeat('?,', count($this->_set)), ',');
		
		$query = sprintf($this->_insert_pattern, $this->_table_name, $columns, $values);
		
		$this->_last_query = $query;
		
		$query = $this->query($query, array_values($this->_set));

		return $query === false ? 0 : $query->rowCount();
	}
	
	/**
	 * Execute query
	 * 
	 * @param mixed $params
	 * 
	 * @return string
	 * 
	 * @throws PDOException
	 */
	public function query(...$params)
	{
		$sql = array_shift($params);
		
		if (count($params) == 1 && is_array($params)) {
			$params = $params[0];
		}
		
		try {
			
			$query = $this->app->db->getConnection()->prepare($sql);
			
			foreach ($params as $k => $v) {
				$query->bindValue($k+1, $v);
			}
			
			$query->execute();
			
			return $query;
			
		} catch (PDOException $e) {
			throw $e;
		}
	}
	
	/**
	 * Execute update command
	 * 
	 * @param string|NULL $table
	 * 
	 * @return int
	 */
	public function update($table = null)
	{
		$this->_bindings = [];
		
		if ($table !== null) $this->table($table);
		
		//$columns = implode(', ', array_keys($this->_set));
	
		$set_values = $this->compileSet();
	
	// where =====================
		$where = $this->compileWhere();
		
		$query = sprintf($this->_update_pattern, $this->_table_name, $set_values, $where);
		
		$this->_last_query = $query;

		$query = $this->query($query, $this->_bindings);

		return $query === false ? 0 : $query->rowCount();
	}
	
	/**
	 * Execute delete command
	 * 
	 * @param string|NULL $table
	 * 
	 * @return int
	 */
	public function delete($table = null)
	{
		$this->_bindings = [];
		
		if ($table !== null) $this->table($table);
		
		
	// where =====================
		//$where = $this->compileWhere();
		
		$query = sprintf($this->_delete_pattern, $this->_table_name, $this->compileWhere());
		
		$this->_last_query = $query;
		
		$query = $this->query($query, array_values($this->_where));

		return $query === false ? 0 : $query->rowCount();
	}
	
	/**
	 * Execute select query
	 * 
	 * @param string|NULL $table
	 * 
	 * @return array
	 */
	public function select($table = null)
	{
		$this->_bindings = [];
		
		if ($table !== null) $this->table($table);
		
		$query = sprintf($this->_select_pattern, $this->compileColumns(), $this->_table_name, $this->_extra);
		
		$this->_last_query = $query;
		
		//return $query;
		$query = $this->query($query);

		$rows = $query->fetchAll();
		
		return $rows === false ? [] : $rows;
	}
	
	/**
	 * Return last executed query
	 * 
	 * @return string
	 */
	public function getLastQuery()
	{
		return $this->_last_query;
	}
	
}