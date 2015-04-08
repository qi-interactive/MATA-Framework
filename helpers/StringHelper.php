<?php

namespace mata\helpers;

class StringHelper extends \yii\helpers\StringHelper {

	/**
	 * Works like ::truncate, but it will then get the last 
	 * occurence of the $character and truncate further. 
	 * 
	 * Useful when truncating to a whole word. 
	 */ 
	public function truncateToCharacter($string, $length, $character=' ', $suffix = '...', $encoding = null, $asHtml = false) {
		$truncated = self::truncate($string, $length, $suffix, $encoding, $asHtml);
		return substr($truncated, 0, strrpos($truncated, $character, -1));

	}


}