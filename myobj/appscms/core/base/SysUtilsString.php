<?php
namespace MYOBJ\appscms\core\base;

class SysUtilsString
{
	public static function GUID()
	{
		if (function_exists('com_create_guid') === true) {
			return trim(com_create_guid(), '{}');
		}

		return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
	}

	public static function read_str($len) {
		return substr(str_shuffle('1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM'), 0, $len);
	}

	public static function transliterate($str) {
		$str = strtr($str, array(
			'а'=>'a', 'б'=>'b', 'в'=>'v', 'г'=>'g', 'д'=>'d', 'е'=>'e', 'ё'=>'yo', 'ж'=>'zh',
			'з'=>'z', 'и'=>'i', 'й'=>'i', 'к'=>'k', 'л'=>'l', 'м'=>'m', 'н'=>'n', 'о'=>'o',
			'п'=>'p', 'р'=>'r', 'с'=>'s', 'т'=>'t', 'у'=>'u', 'ф'=>'f', 'х'=>'h', 'ц'=>'ts',
			'ч'=>'ch', 'ш'=>'sh', 'щ'=>'sh', 'ъ'=>'', 'ы'=>'i', 'ь'=>'', 'э'=>'e', 'ю'=>'yu', 'я'=>'ya',
		));
		return $str;
	}
}