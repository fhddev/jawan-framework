<?php
namespace Jawan\Core\Routing;

use Jawan\Core\App;

/**
 * Router class
 */
class Router {
	
	/**
	 * App instance
	 * 
	 * @var \Jawan\Core\App
	 */
	private $app;
	
	/**
	 * Registered rotues
	 * 
	 * @var array
	 */
	private static $routes = array();
	
	/**
	 * Matched route
	 * 
	 * @var string
	 */
	private $matched_route;
	
	/**
	 * Matches
	 * 
	 * @var array
	 */
	private $matches = array();
	
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
	 * Parse requested uri and try to find the matched route
	 * 
	 * @return string
	 */
	public function findRoute()
	{
		if ($this->matched_route !== null) {
			return $this->matched_route;
		}
		
		foreach (static::$routes as $route)
		{
			if ((bool)preg_match($route->compilePattern(), $this->app->request->getURI(), $matches))
			{
				array_shift($matches);

				$this->matches = $this->concatinatePlaceholdersWithMatches($route->getPlaceholders(), $matches);
				
				$this->matched_route = $route;
				return $this->matched_route;
			}
		}
		
		$this->matched_route = $this->error404Route();
		return $this->matched_route;
	}
	
	/**
	 * Concatinate placeholders with matches
	 * 
	 * @param array $placeholders
	 * @param array $matches
	 * 
	 * @return array
	 */
	public function concatinatePlaceholdersWithMatches($placeholders, $matches)
	{
		$retval = array();
		foreach ($placeholders as $k => $ph) 
		{
			if(array_key_exists($k, $matches)) $retval[$ph] = $matches[$k];
			else $retval[$ph] = NULL;
		}
		
		return $retval;
	}
	
	/**
	 * Get 404 error route
	 * 
	 * @return \Jawan\Core\Routing\Route
	 */
	public function error404Route()
	{
		return Route::get('/404', 'AppInternal/Errors/ErrorController@show404Error');
	}
	
	/**
	 * Get matches
	 * 
	 * @return array
	 */
	public function getMatches()
	{
		return $this->matches;
	}
	
	/**
	 * Attach route
	 * 
	 * @param \Jawan\Core\Routing\Route
	 * 
	 * @return void
	 */
	public static function attach($p1)
	{
		if (is_array($p1)) 
		{
			static::$routes = array_merge(static::$routes, $p1);
		}
		else
		{
			static::$routes[] = $p1;
		}
	}
	
	/**
	 * Fetch route
	 * 
	 * @param string $routeName
	 * 
	 * @return \Jawan\Core\Routing\Route|bool
	 */
	public function fetch($routeName)
	{
		foreach (static::$routes as $route)
		{
			if ((bool)preg_match('#^'.$route->getName().'$#', $routeName))
			{
				return $route;
			}
		}
		
		return false;
	}
	
	/**
	 * Fetch all routes
	 * 
	 * @return array
	 */
	public function fetchAll()
	{
		return static::$routes;
	}
	
}