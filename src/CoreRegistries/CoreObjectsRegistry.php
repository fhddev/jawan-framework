<?php
namespace Jawan\CoreRegistries;


use Jawan\Core\App;

/**
 * Core objects registry class
 * 
 */
class CoreObjectsRegistry {
	
	/**
		* Every core class object will be stored in this array [Will be stored after core class is initialized].
		*
		* @type array
		*/
	private $objects = array();
	
	/**
		* A list of core classes formated as pairs like :
		* [alias => Full\Class\Name]
		*
		*	@type array
		*/
	private $core_classes_list = array();
		
	// --------------------------------------------------------------------

	/**
		* Constructor
		*
		* @param array $coreClassesList = A list of core classes formated as pairs like :
		* [alias => Full\Class\Name]
		*/
	public function __construct(array $coreClassesList = [])
	{
		$this->core_classes_list = $coreClassesList;
	}
	
	// --------------------------------------------------------------------

	/**
		* Check if the given key(alias) is set in the $core_classes_list keys,
		* if found then a new object will be created using the value at that index,
		* then the new object will assigned to the $coreClassesList.
		*
		* @param Jawan\Core\App $app = Object of app class.
		* @param mixed $key = will be used to search the $core_classes_list array.
		*
		* @throw RuntimeException = If the given key(alias) is not in the $core_classes_list keys then 
		* a RuntimeException will be thrown.
		* 
		* @return void
		*/
	public function registerCoreClassObject (App $app, $key)
	{
		if (array_key_exists($key, $this->core_classes_list['class'])) {
			$this->attach($key, new $this->core_classes_list['class'][$key]($app));
		}
		elseif (array_key_exists($key, $this->core_classes_list['factory'])) {
			$this->attach($key, $this->core_classes_list['factory'][$key]::make($app));
		}
		else {
			throw new \RuntimeException($key.' is not a core class or not registered as a core class.');
		}
		//
	}
	
	// --------------------------------------------------------------------

	/**
		* Return the object from the $coreClassesList array. 
		*
		* @param mixed $key = The alias name.
		* @return object
		*/
	public function fetch($key)
	{
		return $this->objects[$key];
	}
	
	// --------------------------------------------------------------------

	/**
		* Addigned a key and object to the $coreClassesList array.
		*
		* @return void
		*/
	private function attach($key, $value)
	{
		$this->objects[$key] = $value;
	}
	
	// --------------------------------------------------------------------

	/**
		* Unset core object from $coreClassesList.
		*
		* @return void
		*/
	private function drop($key)
	{
		unset( $this->objects[$key] );
	}
	
	// --------------------------------------------------------------------

	/**
		* Check if a core object initialized and set in the $coreObjectList array or not.
		*
		* @param mixed $key = Alias name.
		* @return bool True if object found, false otherwise.
		*/
	public function active($key)
	{
		return array_key_exists($key, $this->objects);
	}
	
	// --------------------------------------------------------------------

}