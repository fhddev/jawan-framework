<?php
namespace Jawan\Core;

/**
 * File system class
 */
class FileSystem {
	
	/**
	* Application root path
	*
	* @var string 
	*/
	private $root;
	
	/**
	* Constructor
	*
	* Set the application root path in the $root property.
	*
	* @param string $root Application root path
	*
	* @return void
	*/
	public function __construct(string $root)
	{
		$this->root = $root . DIRECTORY_SEPARATOR;
	}
	
	/**
	* Concatnate the applciation root path wiht the given path.
	*
	* @param string $path Path string.
	* @note : 
	* - The $path param can be with backslash or forwardslash.
	* - If $path param start with [Jawan\] OR [Jawan/] (The framework name) then [vendor\] will be added to the $path param..
	* - for example : if $path = 'Jawan\Path\To\The\Class', it will be 'vendor\Jawan\Path\To\The\Class'.
	*
	* @param bool $addFileExtension Flag to enable or disable auto-add file extension to the $path param.
	* @param string $fileExtension File extension to be added to the $path param IF $addFileExtension is set to TRUE.
	*
	* @return string
	*/
	public function path($path, $addFileExtension = true, $fileExtension = '.php')
	{
		if (strpos($path, 'Jawan\\') === 0 || strpos($path, 'Jawan/') === 0) {
			$path = 'vendor' . DIRECTORY_SEPARATOR . JAWAN_FW_VENDOR_NAME . DIRECTORY_SEPARATOR . substr_replace($path, JAWAN_FW_VENDOR_PACKAGE_NAME . DIRECTORY_SEPARATOR . 'src', 0, strlen('Jawan'));
		}
		
		$compiled_path = $this->root . str_replace(['\\' , '/'], DIRECTORY_SEPARATOR, $path);
		
		return $addFileExtension ? ($compiled_path . $fileExtension) : $compiled_path;
	}
	
	/**
	* Requiring file and can be required once or just require by changing the $requireOnce flag.
	*
	* @param string $filePath = Full path to the file.
	*
	* @return mixed
	*/
	public function frequire(string $filePath)
	{
		if (!file_exists($filePath)) {
			throw new \Exception('['.$filePath.'] file not exists.');
		}
		
		return require $filePath;
	}
	
}