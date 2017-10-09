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

	public static function read_str($len, $only_string=false) {
		$all = array(
			'qwertyuipasdfghjklzxcvbnm',
			'QWERTYUIPASDFGHJKLZXCVBNM',
		);
		if($only_string==false) {
			$all[] = '123456789';
		}
		$count = count($all);
		$str = '';
		foreach($all as $line) {
			$str .= substr(str_shuffle($line), 0, ceil($len/$count));
		}
		return substr(str_shuffle($str),0,$len);
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

	public static function trimAll($str) {
		$func = function($matches) {
			if($matches[1]=="\n" && $matches[0]!="\r\n") {
				return "\r\n\r\n";
			}
			elseif($matches[1]=="\n") {
				return "\r\n";
			}

			return ' ';
		};

		return trim(preg_replace_callback('/(\s)+/', $func, $str));
	}
}