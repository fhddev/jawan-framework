<?php
namespace Jawan\Core\Data\Session\Drivers;

use Jawan\Core\Data\Session\SessionDriver;

/**
 * MemcachedSessionDriver class
 */
class MemcachedSessionDriver extends SessionDriver {
	
	/**
	 * {@inheritDoc}
	 */
	function start()
	{
		session_start();
	}

	/**
	 * {@inheritDoc}
	 */
	function fetch($key)
	{
		return $_SESSION[$key] ?? null;
	}

	/**
	 * {@inheritDoc}
	 */
	function attach($key, $value)
	{
		$_SESSION[$key] = $value;
	}

	/**
	 * {@inheritDoc}
	 */
	function regenerateID()
	{
		session_regenerate_id();
	}

	/**
	 * {@inheritDoc}
	 */
	function drop($key)
	{
		$_SESSION[$key] = null;
		unset($_SESSION[$key]);
	}

	/**
	 * {@inheritDoc}
	 */
	function exists($key)
	{
		return isset($_SESSION[$key]);
	}

	/**
	 * {@inheritDoc}
	 */
	function destroy()
	{
		$_SESSION = null;
		unset($_SESSION);
		session_destroy();
	}

	/**
	 * {@inheritDoc}
	 */
	function fetchAll()
	{
		return $_SESSION;
	}

}