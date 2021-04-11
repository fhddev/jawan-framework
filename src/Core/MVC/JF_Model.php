<?php
namespace Jawan\Core\MVC;

/**
 * Abstract Jawan model base class
 * 
 */
abstract class JF_Model {
	
	/**
	 * Get last sql statement
	 * 
	 * @return string
	 */
	public function getLastSqlStatment()
	{
		return $this->last_statment_object;
	}
	
}