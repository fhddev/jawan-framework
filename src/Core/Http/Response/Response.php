<?php
namespace Jawan\Core\Http\Response;

use Jawan\Core\App;

/**
 * Response class
 * 
 */
class Response {
	
	/**
	* 
	*
	* @var \Jawan\Core\App
	*/
	private $app;
	
	/**
	 * Http-response headers
	 * 
	 * @var array
	 */
	private $headers = array();
	
	/**
	 * Response output
	 * 
	 * @var array
	 */
	private $outputs = array();
	
	// --------------------------------------------------------------------

	/**
	* Constructor
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
	 * Send the http response
	 * 
	 * Compiles the response and echo the output
	 * 
	 * @return void
	 */
	public function send()
	{
		$this->sendHeaders();
		$this->sendOutputs();

		exit;
	}
	
	/**
	 * Send http-headers
	 * 
	 * @return void
	 */
	protected function sendHeaders()
	{
		foreach ($this->headers as $k => $v) 
			header($k.':'.$v);
	}
	
	/**
	 * Set an http-header
	 * 
	 * Cache http-header to output it when sendign the response to the client
	 * 
	 * @param string $key The header index name
	 * @param string $value The actual header
	 * 
	 * @return void
	 */
	public function setHeader($key, $value)
	{
		$this->headers[$k] = $value;
	}
	
	/**
	 * Send http output string
	 * 
	 * @return void
	 */
	protected function sendOutputs()
	{
		foreach ($this->outputs as $output)
			echo $output;
	}
	
	/**
	 * Set http output string
	 * 
	 * @param string $output The output string
	 * 
	 * @return void
	 */
	public function setOutput(string $output)
	{
		$this->outputs[] = $output;
	}
	 
}