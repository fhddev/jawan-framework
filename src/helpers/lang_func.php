<?php


if ( !function_exists('lang') )
{
	/**
		* Read language item.
		*
		* @param string $item Language item name.
		* @param string $default Fallback value.
		* @return string Language value or fallback value.
		*/
	function lang(string $item, string $default = NULL)
	{
		return Jawan\Core\App::getInstance()->lang->read($item, $default);
	}
	//
}