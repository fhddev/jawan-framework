<?php
namespace Jawan\Core\Routing;

/**
 * Route class
 * 
 */
class Route {
	
	/**
	 * Http method for this route
	 * 
	 * @var string
	 */
	private $method;
	
	/**
	 * Regex pattern
	 * 
	 * @var string
	 */
	private $pattern;
	
	/**
	 * Route string
	 * 
	 * @var string
	 */
	private $route_string;
	
	/**
	 * Pattern regex
	 * 
	 * @var array
	 */
	private $pattern_regex = array();
	
	/**
	 * Route name
	 * 
	 * @var string
	 */
	private $name;
	
	/**
	 * Class constructor
	 * 
	 * @param string $method Http method for this route
	 * @param string $pattern Requler expression for this route
	 * @param string $routeString Route string contains controller, method and params
	 * 
	 * @return void
	 */
	private function __construct($method, $pattern, $routeString)
	{
		$this->method = $method;
		$this->pattern = $pattern;
		$this->route_string = $routeString;
		$this->name = '';
	}
	
	/**
	 * Create new instance of Route class with GET http method
	 * 
	 * @param string $p1 Http method for this route
	 * @param string $p2 Requler expression for this route
	 * 
	 * @return Jawan\Core\Routing\Route
	 */
	public static function get($p1, $p2)
	{
		return new Route('GET', $p1, $p2);
	}
	
	/**
	 * Create new instance of Route class with POST http method
	 * 
	 * @param string $p1 Http method for this route
	 * @param string $p2 Requler expression for this route
	 * 
	 * @return Jawan\Core\Routing\Route
	 */
	public static function post($p1, $p2)
	{
		return new Route('POST', $p1, $p2);
	}
	
	/**
	 * Set route name
	 * 
	 * @param string $name
	 * 
	 * @return Jawan\Core\Routing\Route
	 */
	public function name($name)
	{
		$this->name = $name;
		return $this;
	}
	
	/**
	 * Set regex placeholder pattern
	 * 
	 * @param array $args
	 * 
	 * @return Jawan\Core\Routing\Route
	 */
	public function where(array $args)
	{
		$this->pattern_regex = $args;
		return $this;
	}
	
	/**
	 * Get route name
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * Get route string
	 * 
	 * @return string
	 */
	public function getRouteString()
	{
		return $this->route_string;
	}
	
	/**
	 * Get http method
	 * 
	 * @return string
	 */
	public function getMethod()
	{
		return $this->method;
	}
	
	/**
	 * Get all regex placeholders
	 * 
	 * @return array
	 */
	public function getPlaceholders()
	{
		return array_keys($this->pattern_regex);
	}
	
	/**
	 * Parse regex placeholders
	 * 
	 * @return string
	 */
	public function compilePattern()
	{
		$pattern = $this->pattern;
		
		foreach ($this->pattern_regex as $pholder => $pat) 
		{
			if (($pos = stripos($pattern, $pholder.'?')) !== false) {
				//echo $pos;
				$temp = substr($pattern, $pos, strlen($pholder.'?') + 1);
				
				if ($temp !== false) {
					if (strrpos($temp, '/', 0)) {
						$pholder = ($pholder.'?'.'/');
						$pattern = str_replace($pholder, "($pat/)?", $pattern);
					} else {
						$pattern = str_replace($pholder.'?', "($pat)?", $pattern);
					}
				} else {
					$pattern = str_replace($pholder.'?', "($pat)?", $pattern);
				}
			} else {
				$pattern = str_replace($pholder, $pat, $pattern);
			}
		}
		
		return sprintf('#^%s$#', $pattern);
	}
	
}