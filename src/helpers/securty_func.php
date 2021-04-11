<?php


if (! function_exists('jf_encode_special_char') )
{
	/**
		* Encode Special chars
		*
		* @param string|array(string) $value 	The value string.
		*/
	function jf_encode_special_char(string $value)
	{
		if (is_array($value))
		{
			foreach($value as $k => $v)
			{
				$value[$k] = jf_encode_special_char($v);
			}
			return $value;
		}
		return htmlspecialchars($value);
	}
}

if (! function_exists('jf_validate_func') )
{
	/**
		* Sets of annonymous functions helps to validate data.
		*
		* @return callback
		*/
	function jf_validate_func()
	{
		return [
			'alpha'			=> function($item){return (bool)preg_match("#^[a-zA-Z]+$#", $item);},
			'alphanum'	=> function($item){return (bool)preg_match("#^[a-zA-Z0-9]+$#", $item);},
			'num'	=> function($item){return (bool)preg_match("#^[0-9]+$#", $item);},
			
			'min_length'	=> function($value, $length){return strlen($value) >= $length;},
			'max_length'	=> function($value, $length){return strlen($value) <= $length;},
			'length'	=> function($value, $length){return strlen($value) === $length;},
			
		];
	}
}