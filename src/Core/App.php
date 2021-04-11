<?php
namespace Jawan\Core;

use Jawan\CoreRegistries\CoreObjectsRegistry;
use Jawan\CoreRegistries\CoreServicesRegistry;

/**
 * App class
 * 
 */
class App {
	
	/**
	* Class instance 
	*
	* @var \Jawan\Core\App 
	*/
	private static $instance;
	
	/**
	* Object of a registry class that handle all core classes, Except [FileSystem] object, 
	* it is registered here in the App object as a private property,
	* loading file is handled in the App::get() method.
	*
	* @todo = find a way to set the FileSystem object($file) load from the core classes list 
	* like every other core class.
	*
	* @var \Jawan\CoreRegistries\CoreObjectsRegistry
	*/
	private $core_objects_registry;
	
	/**
	* Run multi-processes before start up the application.
	* e.g : start_session - validate_token - check_user_authentication - etc...
	*
	* @var \Jawan\CoreRegistries\CoreServicesRegistry
	*/
	private $core_services_registry;
	
	/**
	* FileSystem object 
	*
	* @var \Jawan\Core\FileSystem
	*/
	private $file;
	
	// --------------------------------------------------------------------

	/**
	* Constructor
	*
	* - Assign $file param to $file property.
	* - Register autoload function.
	* - Register core object registry.
	* - Include file that assigned to be auto include.
	*
	* @param \Jawan\Core\FileSystem $file
	*
	* @return void
	*/
	private function __construct(FileSystem $file)
	{
		$this->file = $file;
		
		// $this->registerAutoload();
		
		$this->registerCoreObjectsRegistry();

		$this->registerCoreServicesRegistry();
		
		$this->includeFiles();
	}

	// --------------------------------------------------------------------

	/**
	* Check if this Class instance is initialized or not,
	* if class initialized then the object will be return,
	* otherwise a new object of this class will be created and then returned.
	* 
	* @param \Jawan\Core\FileSystem $fileHandler FileSystem object if the clsas initialized for the first time.
	* 
	* @return \Jawan\Core\App
	*/
	public static function getInstance($fileHandler = null)
	{
		if ((static::$instance instanceof static) === false)
		{
			static::$instance = new static($fileHandler);
		}
		
		return static::$instance;
	}

	// --------------------------------------------------------------------

	/**
	* Register autoload function using PHP built-in function called (spl_autoload_register).
	*
	* @return void
	*/
	private function registerAutoload()
	{
		spl_autoload_register([$this, 'autoload']);
		//
	}

	// --------------------------------------------------------------------

	/**
	* Autoload function will be registered as the main Jawan-Framework autoload function.
	*
	* @param string $class Will passed automatically by the __autoload function.
	*/
	private function autoload($class)
	{
		$file = $this->file->path($class);
		
		if ( ! file_exists($file) ) 
				throw new \RuntimeException('[' . $class . '] Class file not found in [' . $file . '] .');
			
		$this->file->frequire($file);
		//
	}

	// --------------------------------------------------------------------

	/**
	* Create new object of the CoreObjectsRegistry, then will be assigned to the core_objects_registry property.
	*
	* @return void
	*/
	private function registerCoreObjectsRegistry()
	{
		$this->core_objects_registry =  new CoreObjectsRegistry(require $this->file->path('App\includes\core_classes_list'));
		//
	}

	// --------------------------------------------------------------------

	/**
	* Attach all core services.
	*
	* @return void
	*/
	private function registerCoreServicesRegistry()
	{
		$this->core_services_registry =  new CoreServicesRegistry(require $this->file->path('App\includes\core_services'));
		//
	}

	// --------------------------------------------------------------------

	/**
	* Include Files That Assigned As Auto-include.
	*
	* @return void
	*/
	private function includeFiles()
	{
		//$this->includeFilesFromPath($this->file->path('Jawan\configs\auto_include_files'));
		
		require $this->file->path('App\configs\auto_include_files');
		
		foreach ($auto_include_files as $file) {
			
			$file = $this->file->path(  $file );
			
			if ( ! file_exists($file) ) 
				throw new \RuntimeException('File registered to be auto include is not found at '.$file);
				
			require $file;
			//
			
		} // end foreach
		
		unset ($auto_include_files);
		//
	}

	// --------------------------------------------------------------------

	/**
	* Core object loading handler
	*
	* @param mixed $key The alias name for the core class
	* 
	* @throws RuntimeException Sub-methods may throw an exception if a file not exists.
	*
	* @return object
	*/
	protected function get($key)
	{
		if ($key === 'file') {
			return $this->file;
		}
		
		//if ($key === 'locale') {
		//	$this->core_objects_registry->registerCoreClassObject($this, $key);
		//}
		
		if ($key === 'session') 
		{
			if (isset($this->session))
			{
				return $this->session;
			}

			$this->file->frequire( $this->file->path('Jawan\Core\Data\Session\SessionFactory') );

			$this->session = \Jawan\Core\Data\Session\SessionFactory::make($this->config->read('session.driver'));

			return $this->session;
		}
		
		if ($this->core_objects_registry->active($key) === false) {
			$this->core_objects_registry->registerCoreClassObject($this, $key);
		}

		return $this->core_objects_registry->fetch($key);
	}

	// --------------------------------------------------------------------

	/**
	* The application start point.
	*
	* @return void
	*/
	public function run()
	{
		$this->parseLocaleID();
		
		$this->lang->setLocaleCode($this->locale->getLocaleID());
		
		$this->core_services_registry->runServices();

		//echo $this->lang->read('welcome').LINE;
		$this->response->setOutput($this->loader->load()->getOutput());
		$this->response->send();
		
	}

	/**
	 * Parse locale id
	 * 
	 * @return void
	 */
	public function parseLocaleID()
	{
		if ($this->locale->matchLocaleCode($this->uri->segments(1))) {
			$segments = $this->uri->segments();
			$this->locale->setMatchedLocaleCode();
		
			array_shift($segments);

			$this->request->overrideURI(implode('/', $segments));
		} else {
			$this->locale->setLocaleID($this->config->read('locale.fallback_locale_id'));
		}
		//
	}

	// --------------------------------------------------------------------

	/**
	 * PHP magic method __get
	 * 
	 * @param string $key Property name
	 * 
	 * @return mixed
	 */
	public function __get($key)
	{
		return $this->get($key);
	}

	// --------------------------------------------------------------------

}