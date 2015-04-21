<?php

namespace mata\helpers;

class StringHelper extends \yii\helpers\StringHelper {

	/**
	 * Works like ::truncate, but it will then get the last 
	 * occurence of the $character and truncate further. 
	 * 
	 * Useful when truncating to a whole word. 
	 */ 
	public static function truncateToCharacter($string, $length, $character=' ', $suffix = '...', $encoding = null, $asHtml = false) {
		$truncated = self::truncate($string, $length, $suffix, $encoding, $asHtml);
		return substr($truncated, 0, strrpos($truncated, $character, -1));

	}

	public static function renderStringWithoutOuterParagraph($text) {
		$text = preg_replace('/<p>/i','', $text, 1);
		$text = preg_replace('/<\/p>/i','', $text, -1);
		echo $text;
	}

	public static function replaceTag($text, $from, $to) {
		$text = preg_replace("~<(/)?$from>~", "<\\1$to>", $text);
		echo $text;
	}

	public static function removeHtmlTags($string) {
		$text = preg_replace('/(<\/[a-z]+>)/', '$1 ', $string);
		$text = trim(preg_replace('/\s+/', ' ', strip_tags($string)));
		return $text;
	}


}