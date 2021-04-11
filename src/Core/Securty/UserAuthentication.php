<?php
namespace Jawan\Core\Securty;

use Jawan\Core\App;

/**
 * User authentication class
 */
class UserAuthentication
{

	/**
	 * App instance
	 * 
	 * @var \Jawan\Core\App
	 */
	private $app;

	/**
	 * User role
	 * 
	 * @var string
	 */
	protected $role;

	/**
	 * Get user role
	 * 
	 * @return string
	 */
	public function getRole()
	{
		return $this->role;
	}

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
		$this->init();
	}

	/**
	 * Class initializer
	 * 
	 * @return void
	 */
	protected function init()
	{
		$role = $this->app->session->fetch('role');
		$role = $role ?? 'guest';
		$this->role = $role;
	}

}