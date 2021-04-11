<?php
namespace Jawan\Core\Locale;

use Jawan\Core\App;

/**
 * Locale Dispatcher class
 */
class LocaleDispatcher {
	
	/**
	 * Locale id
	 * 
	 * @var string
	 */
	private $locale_id;
	
	/**
	 * App instance
	 * 
	 * @var \Jawan\Core\App
	 */
	private $app;
	
	/**
	 * Locale data
	 * 
	 * @var array
	 */
	private $locale_data = array();
	
	/**
	 * Cache matched locale code
	 * 
	 * @var string
	 */
	private $_match_locale_code;
	
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
		$this->loadLocaleData();
	}
	
	/**
	 * Set locale id
	 * 
	 * @param string $localeID
	 * 
	 * @return void
	 */
	public function setLocaleID($localeID)
	{
		$this->locale_id = $localeID;
	}
	
	/**
	 * Get locale id
	 * 
	 * @return string
	 */
	public function getLocaleID()
	{
		return $this->prepareLocaleCode($this->locale_id);
	}
	
	/**
	 * Set matched locale id
	 * 
	 * @return void
	 */
	public function setMatchedLocaleCode()
	{
		$this->setLocaleID($this->_match_locale_code);
	}
	
	/**
	 * Parse locale code
	 * 
	 * @param string $localeCode
	 * 
	 * @return string
	 */
	public function prepareLocaleCode($localeCode)
	{
		$arr = explode('-', $localeCode);
		if (count($arr) === 1)
		{
			$arr = explode('_', $arr[0]);
		}
		
		$arr[0] = strtolower($arr[0]);
		if (isset($arr[1]))
		{
			$arr[1] = strtoupper($arr[1]);
		}
		
		return implode('-', $arr);
	}
	
	/**
	 * Check locale code validity
	 * 
	 * @param string $localeID
	 * 
	 * @return bool
	 */
	public function matchLocaleCode($localeID)
	{
		$localeID = $this->prepareLocaleCode($localeID);

		foreach (array_keys($this->locale_data) as $k) 
		{
			// ar-sa
			$separated_k = explode('-', $k);
			if (in_array($localeID, $separated_k) || $localeID === $k)
			{
				//return $k;
				$this->_match_locale_code = $k;
				return true;
			} 
		}
	
		return false;
	}
	
	/**
	 * Get locale data
	 * 
	 * @return void
	 */
	public function loadLocaleData()
	{
		$file = $this->app->file->path('App\includes\locale_data');
		
		if ( ! file_exists($file) ) {
			throw new \Exception('Locale data file not found at ['.$file.']');
		}
		
		$this->locale_data = require $file;
	}
	
	/**
	 * PHP magic method __get
	 * 
	 * @param string $key Property name
	 * 
	 * @return mixed
	 */
	public function __get($key)
	{
		return $this->locale_data[$this->locale_id][$key] ?? $key;
	}
	
}