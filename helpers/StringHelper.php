<?php

namespace mata\helpers;

class StringHelper
{
	public static function startsWith($haystack, $needle) 
	{
	    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
	}

	public static function endsWith($haystack, $needle) 
	{
	    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
	}
}