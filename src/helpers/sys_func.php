<?php


if ( !function_exists('dirsep') )
{
	/**
		* Convert backslashes and forwardslashes if found to DIRECTORY_SEPARATOR.
		*
		*	@param string $path = Path to convert it's slashes.
		* @return string = The given path after change all it's slashes to DIRECTORY_SEPARATOR.
		*/
	function dirsep(string $path)
	{
		return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
	}
	//
}



// -----------------------------------------------------------------------------------------------------------------


if ( !function_exists('def') )
{
	/**
		* Define constant
		*/
	function def($key, $value)
	{
		defined($key) || define($key, $value);
	}
	//
}



// -----------------------------------------------------------------------------------------------------------------



if ( !function_exists('vd') )
{ 
	/**
		* Variable Dump
		*
		* echo <pre> tag and then dump the given $value using (var_dump) function.
		*
		* @param mixed $value = a value to be dumped.
		*/
	function vd($value)
	{
		echo '<pre>';
		var_dump($value);
		echo '</pre>';
	}
	//
}



// -----------------------------------------------------------------------------------------------------------------




if ( !function_exists('arrd') )
{
	/**
		* Array Dump
		*
		* echo <pre> tag and then dump the given array $value using (print_r) function.
		*
		* @param array $value = a value to be dumped.
		*/
	function arrd(array $value)
	{
		echo '<pre>';
		print_r($value);
		echo '</pre>';
	}
	//
}