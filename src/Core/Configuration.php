<?php 
namespace Jawan\Core;

use Jawan\Core\App;

/**
 * Configuration class
 * 
 */
class Configuration {
	
	/**
	 * Error message for config file not found
	 * 
	 * @const
	 */
	const ERROR_CONFIG_FILE_NOT_FOUND = 'Config file not found.';
	
	/**
	 * Loadded config files
	 * 
	 * @var array
	 */
	private $loadded_files = array();
	
	/**
	 * Config directory path
	 * 
	 * @var string
	 */
	private $config_path = '';
	
	/**
	 * App instance
	 * 
	 * @var \Jawan\Core\App
	 */
	private $app;
	
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
		$this->config_path = 'App\configs';
	}
	
	/**
	 * Read form file
	 * 
	 * @param string $Index file_name.item_name
	 * 
	 * @return mixed
	 */
	public function read($Index)
	{
		$params = $this->extractValues($Index);
		
		if (!$this->isLoadded($params['file_name']))
		{
			$this->loadConfigFile($params['file_name']);
		}
		
		return $this->loadded_files[$params['file_name']][$params['item_name']] ?? null;
	}
	
	/**
	 * Write config item
	 * 
	 * @param string $key Config item key name
	 * @param mixed $value Config item value
	 * 
	 * @return void
	 */
	public function write($key, $value)
	{
		$params = $this->extractValues($key);
		
		if (!$this->isLoadded($params['file_name']))
		{
			$this->loadConfigFile($params['file_name']);
		}
		
		$this->loadded_files[$params['file_name']][$params['item_name']] = $value;
	}
	
	/**
	 * Read config item from all config files
	 * 
	 * @param string $key
	 * 
	 * @return string
	 */
	public function all($key)
	{
		$params = $this->extractValues($key);
		
		if (!$this->isLoadded($params['file_name']))
		{
			$this->loadConfigFile($params['file_name']);
		}
		
		return $this->loadded_files[$params['file_name']];
	}
	
	/**
	 * Check whether file config is loadded or not
	 * 
	 * @param string $ConfigFileName Config file name
	 * 
	 * @return bool
	 */
	protected function isLoadded($ConfigFileName)
	{
		return array_key_exists($ConfigFileName, $this->loadded_files);
	}
	
	/**
	 * Laod config file
	 * 
	 * @param string $ConfigFileName
	 * 
	 * @throws \Exception Throws exception if config file not found
	 * 
	 * @return void
	 */
	public function loadConfigFile($ConfigFileName)
	{
		$file = $this->app->file->path($this->config_path.'/'.$ConfigFileName);
		if (!file_exists($file))
		{
			throw new \Exception(self::ERROR_CONFIG_FILE_NOT_FOUND);
		}
		
		require ($file);
		
		$ConfigFileName;
		
		if ( isset($$ConfigFileName) && is_array($$ConfigFileName) )
		{
			$this->loadded_files[$ConfigFileName] = $$ConfigFileName;
		}
		
		unset($$ConfigFileName);
	}
	
	/**
	 * Parse config file name from a string pattern
	 * 
	 * Pattern is (file_name.item_name)
	 * 
	 * @param string File name pattern
	 * 
	 * @return array
	 */
	protected function extractValues($Value)
	{
		$return = explode('.', $Value);
		$return['file_name'] = $return[0];
		$return['item_name'] = $return[1] ?? '';
		unset($return[0]);
		unset($return[1]);
		return $return;
	}
	
}