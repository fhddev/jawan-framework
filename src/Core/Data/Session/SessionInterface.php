<?php
namespace Jawan\Core\Data\Session;

/**
 * SessionFactory interface
 */
interface SessionInterface {
	
	/**
	 * Init the session driver
	 * 
	 * @return void
	 */
	function start();

	/**
	 * Get a value form session
	 * 
	 * @param string $key
	 * 
	 * @return string
	 */
	function fetch($key);

	/**
	 * Set new value to the session
	 * 
	 * @param string $key
	 * @param string $value
	 * 
	 * @return void
	 */
	function attach($key, $value);

	/**
	 * Regenerate session ID
	 * 
	 * @return void
	 */
	function regenerateID();

	/**
	 * Delete value from session
	 * 
	 * @param string $key
	 * 
	 * @return void
	 */
	function drop($key);

	/**
	 * Check whether a value is exists
	 * 
	 * @param string $key
	 * 
	 * @return boolean
	 */
	function exists($key);

	/**
	 * Delete session
	 * 
	 * @return void
	 */
	function destroy();

	/**
	 * Get all session values
	 * 
	 * @return array
	 */
	function fetchAll();
	
}