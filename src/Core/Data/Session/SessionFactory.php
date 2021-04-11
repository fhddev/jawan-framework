<?php
namespace Jawan\Core\Data\Session;

/**
 * SessionFactory class
 */
class SessionFactory {
	
	/**
	 * Create and return session driver instance
	 * 
	 * @param string $driver
	 * 
	 * @return Jawan\Core\Data\Session\SessionDriver
	 */
	public static function make($driver)
	{
		switch ($driver)
		{
			// ----------------------------------------------------------------------------------
			case 'memcached':
				return new \Jawan\Core\Data\Session\Drivers\MemcachedSessionDriver();
			break;
			// ----------------------------------------------------------------------------------
			case 'database':
				return new \Jawan\Core\Data\Session\Drivers\DatabaseSessionDriver();
			break;
			// ----------------------------------------------------------------------------------
			case 'file':
				return new \Jawan\Core\Data\Session\Drivers\FileSessionDriver();
			break;
			// ----------------------------------------------------------------------------------
			case 'cookie':
				return new \Jawan\Core\Data\Session\Drivers\CookieSessionDriver();
			break;
			// ----------------------------------------------------------------------------------
			default :
				throw new \Exception('Unkown Session Driver.');
			break;
			// ----------------------------------------------------------------------------------
		}
		//
	}

}