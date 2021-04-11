<?php
namespace Jawan\Core\MVC;

/**
 * Abstract Jawan controller base class
 * 
 */
abstract class JF_Controller {
	
	/**
	 * App instance
	 * 
	 * @var \Jawan\Core\App
	 */
	protected $app;

	/**
	 * User role
	 * 
	 * @var string
	 */
	protected $role = '*';

	/**
	 * Do not check these class methods with user role
	 * 
	 * @var array
	 */
	protected $except = array();
	
	/**
	 * Class constructor
	 * 
	 * @return void
	 */
	public function __construct()
	{
		$this->app = \Jawan\Core\App::getInstance();
		$this->init();
	}
	
	/**
	 * Class initializer
	 * 
	 * @return void
	 */
	private function init()
	{
		if ($this->role === '*') // || array_key_exists('*', $this->except)
		{
			return;
		}

		if (isset( $this->except[$this->app->auth->getRole()] ) && 
			in_array($this->app->loader->getActionName(), $this->except[$this->app->auth->getRole()]))
		{
			return;
		}

		if ($this->app->auth->getRole() === $this->role)
		{
			return;
		}

		// load access denied page
		$this->app->response->setOutput($this->app->loader->view('errors::access_denied'));
		//$this->app->response->setHeader();
		$this->app->response->send();
		//
	}

}