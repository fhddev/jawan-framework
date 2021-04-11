<?php
namespace Jawan\Core\Routing;

use Jawan\Core\App;

/**
 * Route loader class
 */
class RouteLoader {
	
	/**
	 * Controller namespace
	 * 
	 * @const
	 */
	const CONTROLLERS_NAMESPACE = 'App\Controllers\\';
	
	/**
	 * Model namespace
	 * 
	 * @const
	 */
	const MODEL_NAMESPACE = 'App\Models\\';
	
	/**
	 * App instance
	 *
	 * @var \Jawan\Core\App
	 */
	private $app;
	
	/**
	 * Route
	 * 
	 * @var string
	 */
	private $route;
	
	/**
	 * Controllers
	 * 
	 * @var array
	 */
	private $controllers = array();
	
	/**
	 * Models
	 * 
	 * @var array
	 */
	private $models = array();
	
	/**
	 * Outout string 
	 * 
	 * @var string
	 */
	private $output;

	/**
	 * Action name
	 * 
	 * @var string
	 */
	protected $action;

	/**
	 * Constroller name
	 * 
	 * @var string
	 */
	protected $controller;
	
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
		$this->route = $this->app->router->findRoute();
	}
	
	// return The return value from the action in the controlller
	/**
	 * Load request route
	 * 
	 * @param string|null $route_string
	 * @param array|null $args Method arguments
	 * 
	 * @return \Jawan\Core\Routing\RouteLoader
	 */
	public function load($route_string = null, $args = null)
	{
		if ($route_string === null) $route_string = $this->route->getRouteString();
		if ($args === null) $args = $this->getParams($this->app->router->getMatches(), $this->parseParamsPlaceholders($route_string));
		
		// var_dump($args);
		
		// load action 
		$this->action = $this->parseAction($route_string);
		
		// load controller 
		$this->controller = $this->fetchController($this->parseController($route_string));
		
		if ( substr($this->action, 0, 1) === '_' || ! method_exists($this->controller, $this->action)) {
			throw new \Exception('Method is protected with "_" or Method not exists in controller class ['.get_class($this->controller).'-'.$this->action.']');
		}
		
		$this->output = call_user_func_array([$this->controller, $this->action], $args);
		
		return $this;
	}
	
	/**
	 * Get route parameters
	 * 
	 * @param array $matchesWithPlaceholders
	 * @param array $routeStringParamsPlaceholders
	 * 
	 * @return array
	 */
	public function getParams(array $matchesWithPlaceholders, array $routeStringParamsPlaceholders)
	{
		$retarr = array();

		foreach ($routeStringParamsPlaceholders as $ph) 
		{
			if (array_key_exists($ph, $matchesWithPlaceholders))
			{
				$retarr[$ph] = $matchesWithPlaceholders[$ph];
			}
			else 
			{
				throw new \Exception('A placeholder in route string has no pattern in URI pattern.');
			}
		}
		
		return $retarr;
	}
	
	/**
	 * Parse requler expression placeholders
	 * 
	 * @param string $routeString
	 * 
	 * @return array
	 */
	public function parseParamsPlaceholders($routeString)
	{
		$retval = explode('@', $routeString);
		if ( ! isset($retval[1]) ) {
			return array();
		}
		
		$retval = explode('|', $retval[1]);
		
		array_shift($retval);
		
		return $retval;
	}
	
	/**
	 * Get output
	 * 
	 * @return string
	 */
	public function getOutput()
	{
		return $this->output ?? '';
	}
	
	/**
	 * Get parsed view output
	 * 
	 * @param strign $file
	 * @param array $params
	 * 
	 * @return string
	 */
	public function view($file, array $params = [])
	{
		$file = $this->app->file->path('App\Views\\'.implode(DIRECTORY_SEPARATOR, explode('::', $file)));
		if (! file_exists($file) ) {
			throw new \Exception('View file not exists in ['.$file.']');
		}
		
		extract($params);
		ob_start();
		require $file;
		return ob_get_clean();
	}

	/**
	 * Get action name
	 * 
	 * @return string
	 */
	public function getActionName()
	{
		return $this->action ?? '';
	}

	//-------------------------------------------------------------
	// CONTROLLERS SECTION
	//-------------------------------------------------------------
	
	/**
	 * Parse controller
	 * 
	 * @param string $routeString
	 * 
	 * @return array
	 */
	protected function parseController($routeString)
	{
		return explode('@', $routeString)[0];
	}
	
	/**
	 * Attach controller
	 * 
	 * @param string $controller Controller name
	 * 
	 * @return void
	 */
	protected function attachController($controller)
	{
		if ($this->hasController($controller))
		{
			return;
		}

		$controller_object = static::CONTROLLERS_NAMESPACE.$controller; //$this->prepareController($controller);
		$object = new $controller_object();
		$this->controllers[$controller] = $object;
	}
	
	/**
	 * Check whether controller loadded OR not
	 * 
	 * @param string $controller Controller name
	 * 
	 * @return bool
	 */
	protected function hasController($controller)
	{
		return array_key_exists($controller, $this->controllers);
	}
	
	/**
	 * Get loadded controller
	 * 
	 * @param string $controller Controller name
	 * 
	 * @return object
	 */
	protected function getController($controller)
	{
		return $this->controllers[$controller];
	}
	
	/**
	 * Fetch controller
	 * 
	 * Check whether a controller is loadded or not and
	 * loadded it if has not loadded
	 * 
	 * @param string $controller Controller name
	 * 
	 * @return object
	 */
	public function fetchController($controller)
	{
		$controller = str_replace('/', DIRECTORY_SEPARATOR, $controller);

		if ($this->hasController($controller) == false) {
			$this->attachController($controller);
		}
		
		return $this->getController($controller);
	}
	
	//-------------------------------------------------------------
	// ACTION SECTION
	//-------------------------------------------------------------
	
	/**
	 * Parse action
	 * 
	 * @param string $routeString
	 * 
	 * @return string
	 */
	protected function parseAction($routeString)
	{
		$retval = explode('@', $routeString);
		if ( ! isset($retval[1]) ) {
			return 'index';
		}
		
		$retval = explode('|', $retval[1])[0];
		return $retval === '' ? 'index' : $retval;
	}
	
	//-------------------------------------------------------------
	// MODELS SECTION
	//-------------------------------------------------------------
	
	/**
	 * Parse model
	 * 
	 * @param string $routeString 
	 * 
	 * @return string
	 */
	protected function parseModel($routeString)
	{
		$model = explode('@', $routeString);
		if ( ! isset($model[1]) ) {
			return 'index';
		}
		
		return explode('|', $model[1])[0];
	}
	
	/**
	 * Attach model
	 * 
	 * @param string $model Model name
	 * 
	 * @return void
	 */
	protected function attachModel($model)
	{
		if ($this->hasModel($model))
		{
			return;
		}
		
		$model_object = $this->prepareModel($model);
		$object = new $model_object();
		$this->models[$model] = $object;
	}
	
	/**
	 * Return full model name with namespace
	 * 
	 * @param string $model Model name
	 * 
	 * @return string
	 */
	protected function prepareModel($model)
	{
		return static::MODEL_NAMESPACE.$model;
	}
	
	/**
	 * Check whether a model object has been loadded or not
	 * 
	 * @param string $model Model name
	 * 
	 * @return bool
	 */
	protected function hasModel($model)
	{
		return array_key_exists($model, $this->models);
	}
	
	/**
	 * Get loadded model
	 * 
	 * @param string $model Model name
	 * 
	 * @return object
	 */
	protected function getModel($model)
	{
		return $this->models[$model];
	}
	
	/**
	 * Fetch loadded model
	 * 
	 * @param string $model Model name
	 * 
	 * @return object
	 */
	public function fetchModel($model)
	{
		if ($this->hasModel($model) == false) {
			$this->attachModel($model);
		}
		
		return $this->getModel($model);
	}
	
}