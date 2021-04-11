<?php
namespace Jawan\Core\Http\Request;

use Jawan\Core\App;

/**
 * Input class
 */
class Input {
	
	
	/**
	* @var \Jawan\Core\App
	*/
	private $app;
	
	
	// --------------------------------------------------------------------

	/**
	* Class constructor
	*
	* @param \Jawan\Core\App $app
	*
	* @return void
	*/
	public function __construct(App $app)
	{
		$this->app = $app;
	}
	 
	// --------------------------------------------------------------------
	
	/**
	* Fetch given array.
	*
	* @param &$array 	Array to fetch data from.
	* @param $index 	The item index in the array or NULL to return all items in the array.
	* @return mixed		NULL if no value exists.
	*/
	protected function fetchArray(&$array, $index)
	{
		if ($index === null)
			return $array;
		
		return $array[$index] ?? null;
	}
	  
	// --------------------------------------------------------------------
	
	/**
		* Fetch $_GET array.
		*
		* @param $index 	The item index in the array or NULL to return all items in the array.
		*/
	public function get($index = null)
	{
		return $this->fetchArray($_GET, $index);
	}
	 
	// --------------------------------------------------------------------
	
	/**
		* Fetch $_POST array.
		*
		* @param $index 	The item index in the array or NULL to return all items in the array.
		*/
	public function post($index = null)
	{
		return $this->fetchArray($_POST, $index);
	}
	 
	// --------------------------------------------------------------------
	
	/**
		* Fetch $_COOKIE array.
		*
		* @param $index 	The item index in the array or NULL to return all items in the array.
		*/
	public function cookie($index = null)
	{
		return $this->fetchArray($_COOKIE, $index);
	}
	 
	// --------------------------------------------------------------------
	
	/**
		* Fetch $_SERVER array.
		*
		* @param $index 	The item index in the array or NULL to return all items in the array.
		*/
	public function server($index = null)
	{
		return $this->fetchArray($_SERVER, $index);
	}
	 
	// --------------------------------------------------------------------
	
	/**
		* Fetch $_REQUEST array.
		*
		* @param $index 	The item index in the array or NULL to return all items in the array.
		*/
	public function request($index = null)
	{
		return $this->fetchArray($_REQUEST, $index);
	}
	 
	// --------------------------------------------------------------------
	
}