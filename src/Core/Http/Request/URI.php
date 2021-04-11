<?php
namespace Jawan\Core\Http\Request;

use Jawan\Core\App;

/**
 * URI class
 * 
 */
class URI {

	/**
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
	}
	
	/**
	* Get requested uri
	*
	* @return string	Requested uri or empty string otherwise.
	*/
	public function getURI()
	{
		return $this->app->request->getURI();
	}
	
	/**
	* Get requested uri segment
	*
	* @param int $index Segment index OR null for all segments, index count starts from 1
	*
	* @return string	Requested uri or empty string otherwise.
	*/
	public function segments($index = null)
	{
		$segments = explode('/', trim($this->getURI(), '/'));
		
		if ($index === null) 
			return $segments;
			
		$index = (int) $index - 1;
		
		return $segments[$index] ?? null;
	}
	
}