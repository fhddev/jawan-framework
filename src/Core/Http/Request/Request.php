<?php
namespace Jawan\Core\Http\Request;

use Jawan\Core\App;

/**
 * Request class
 * 
 */
class Request {
	
	/**
	* App class instance
	*
	* @var \Jawan\Core\App
	*/
	private $app;
	
	/**
	* URI string (decoded)
	*
	* @var string
	*/
	private $override_uri;
	
	
	// --------------------------------------------------------------------

	/**
	* Constructor
	*
	* @param \Jawan\Core\App $app
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

	}
	 
	// --------------------------------------------------------------------

	/**
	* Get requested uri
	*
	* @return string	Requested uri or empty string otherwise.
	*/
	public function getURI()
	{
		$webroot_path = dirname($this->app->input->server('SCRIPT_NAME'));
		$request_uri = str_replace($webroot_path, '', $this->app->input->server('REQUEST_URI') ?? '');

		if ($this->override_uri === null) {
			$this->override_uri = $request_uri;
		}
		
		return $this->override_uri;
	}
	 
	// --------------------------------------------------------------------

	/**
	* Get http method
	*
	* @return string	Http method
	*/
	public function getMethod()
	{
		return $this->app->input->server('REQUEST_METHOD');
	}

	// --------------------------------------------------------------------

	/**
	* Get request file name
	*
	* @return string 	Request file name, empty string '' otherwise.
	*/
	public function getFileName() 
	{
		return $this->app->input->server('SCRIPT_NAME');
	}

	// --------------------------------------------------------------------

	/**
	 * Get client IP Address
	 *
	 * Determines and validates the visitor's IP address.
	 *
	 * @return	string	IP address
	 */
	public function getIP()
	{
		return $this->isValidIP($this->app->input->server('REMOTE_ADDR'), 'ipv4');
	}

	// --------------------------------------------------------------------

	/**
	 * Validate IP Address
	 *
	 * @param	string	$ip	IP address
	 * @param	string	$which	IP protocol: 'ipv4' or 'ipv6'
	 * @return	bool
	 */
	public function isValidIP($ip, $which = '')
	{
		switch (strtolower($which))
		{
			case 'ipv4':
				$which = FILTER_FLAG_IPV4;
				break;
			case 'ipv6':
				$which = FILTER_FLAG_IPV6;
				break;
			default:
				$which = NULL;
				break;
		}

		return (bool) filter_var($ip, FILTER_VALIDATE_IP, $which);
	}

	// --------------------------------------------------------------------

	/**
	* Get request query string
	*
	* @return string		Requested query string, empty string '' otherwise.
	*/
	public function getQueryString() 
	{
		return $this->app->input->server('QUERY_STRING') ?? '';
	}

	// --------------------------------------------------------------------

	/**
	* Get request time
	*
	* @return int 	Unix time.
	*/
	public function getRequestTimestamp() 
	{
		return (int) $this->app->input->server('REQUEST_TIME');
	}

	// --------------------------------------------------------------------

	/**
	* Rewrite uri string
	*
	* @param string $URI The new uri
	*
	* @return bool 	true on success, false otherwise
	*/
	public function overrideURI($URI) 
	{
		$this->override_uri = $URI;
	}

	// --------------------------------------------------------------------

}