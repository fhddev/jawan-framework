<?php

/*
* REMEBER TO SAVE LANGUAGE FILE AS UTF-8.
*
* LANGUAGE FILE NAME CONVENSION :
* -------------------------------
* 
* langtag.php
*/

namespace Jawan\Core;

/**
 * Language Class
 *
 * Handle translation.		
 */
class Language {
	
	/**
	* Languages files directory.
	*
	* @var string
	*/
	private $lang_files_dir;
	
	/**
	* key|value pairs from the loaded language files.
	*
	* @var array
	*/
	private $loadded_files = array();
	
	/**
	* Error message if a language file not found.
	*
	* @const string 
	*/
	const ERROR_FILE_NOT_FOUND = 'Language file not found.'; 
	
	/**
	* Main App object.
	*
	* @var \Jawan\Core\App 
	*/
	private $app;
	
	/**
	* Locale code used to load the correct language file.
	*
	* @var string 
	*/
	private $locale_code;
	
	
	/**
	* Class constructor
	*
	* @param \Jawan\Core\App $app		Reference to App class 
	*
	* @return void
	*/
	public function __construct(App $app)
	{
		$this->app = $app;
		$this->lang_files_dir = 'App\languages';
		
		$file = $this->app->file->path(  dirsep('Jawan/helpers/lang_func') );
		require_once($file);
	}
	
	/**
	* Set locale code
	*
	* @param string $localeCode		Locale code to load it's language file.
	*
	* @return void
	*/
	public function setLocaleCode($localeCode)
	{
		$this->locale_code = $localeCode;
	}
	
	/**
	* Fetch value by key from the language array that loaded from language file
	*
	* @param mixed $Index		Translated item key
	*
	* @return string
	*/
	public function read($Index, $default = NULL)
	{
		$params = $this->extractValues($Index);
		
		if (!$this->isLoadded($params['file_name']))
		{
			$this->loadFile($params['file_name']);
		}
		
		return $this->loadded_files[$params['file_name']][$params['item_name']] ?? $default;
	}
	
	/**
	* Set key|value in language array 
	*
	* @param mixed $Index 	The key in the array 
	* @param string $value	The translated value 
	* @return void 
	*/
	public function write($Index, $Value)
	{
		$params = $this->extractValues($key);
		
		if (!$this->isLoadded($params['file_name']))
		{
			$this->loadFile($params['file_name']);
		}
		
		$this->loadded_files[$params['file_name']][$params['item_name']] = $value;
	}
	
	
	/**
	 * Extract values 
	 * 
	 * @param string $Index
	 * 
	 * @return array
	 */
	public function extractValues($Index)
	{
		if ($this->locale_code === null) {
			throw new \Exception('Set language tag first using this command : $locale->setLocaleID($localeID);');
		}
		
		$Index = $this->locale_code.'.'.$Index;
		$return = explode('.', $Index);
		$return['file_name'] = $return[0];
		$return['item_name'] = strtolower($return[1]) ?? '';
		unset($return[0]);
		unset($return[1]);
		return $return;
	}
	
	/**
	 * Check whether a file loadded or not
	 * 
	 * @param string $FileName
	 * 
	 * @return bool
	 */
	protected function isLoadded($FileName)
	{
		return isset($this->loadded_files[$FileName]);
	}
	
	/**
	 * Load file
	 * 
	 * @param string $FileName
	 * 
	 * @throws \Exception Throws exception if file not found
	 * 
	 * @return void
	 */
	public function loadFile($FileName)
	{
		$file = $this->app->file->path($this->lang_files_dir.'/'.$FileName);
		
		if (!file_exists($file))
		{
			throw new \Exception(self::ERROR_FILE_NOT_FOUND);
		}
		
		
		$this->loadded_files[$FileName] = require ($file);
	}
	
}