<?php

namespace Jawan\CoreRegistries;


use Jawan\Core\App;

/**
 * Core services registry class
 */
class CoreServicesRegistry {
	
	/**
		* Core Services Container
		*
		* @type array
		*/
	private $container = array();
	
	// --------------------------------------------------------------------

	/**
		* Constructor
		*
		* @param array $coreClassesList = A list of core classes formated as pairs like :
		* [alias => Full\Class\Name]
		*/
	public function __construct(array $coreServices = [])
	{
		$this->container = $coreServices;
	}
	
	// --------------------------------------------------------------------

	/**
		* Attach service class to the container.
		*
		* @return void
		*/
	private function attach($key, $value)
	{
		$this->container[$key] = $value;
	}
	
	// --------------------------------------------------------------------

	/**
		* Unset service class from the list.
		*
		* @return void
		*/
	private function drop($key)
	{
		unset( $this->container[$key] );
	}
	
	// --------------------------------------------------------------------

	/**
		* Check if serivce class exists.
		*
		* @param mixed $key = Alias name.
		* @return bool True if object found, false otherwise.
		*/
	public function active($key)
	{
		return array_key_exists($key, $this->objects);
	}
	
	// --------------------------------------------------------------------

	/**
		* Run all services.
		*
		* @return void
		* @todo Check if implements CoreServiceAbstract class before call run method.
		*/
	public function runServices()
	{
		foreach ($this->container as $alias => $service) 
		{
			// check if implements CoreServiceAbstract class.
			$service::run();
		}
	}
	
	// --------------------------------------------------------------------

}