<?php
namespace MYOBJ\appscms\core\base;

class SysUtilsArray
{
	public static function array_diff_assoc_recursive($array1, $array2) {
		$difference=array();
		foreach($array1 as $key => $value) {
			if( is_array($value) ) {
				if( !isset($array2[$key]) || !is_array($array2[$key]) ) {
					$difference[$key] = $value;
				} else {
					$new_diff = static::array_diff_assoc_recursive($value, $array2[$key]);
					if( !empty($new_diff) )
						$difference[$key] = $new_diff;
				}
			} else if( !array_key_exists($key,$array2) || $array2[$key] !== $value ) {
				$difference[$key] = $value;
			}
		}
		return $difference;
	}

	public static function recursive_array_search($needle,$haystack) {
		foreach($haystack as $key=>$value) {
			$current_key=$key;
			if($needle===$value OR (is_array($value) && static::recursive_array_search($needle,$value) !== false)) {
				return $current_key;
			}
		}
		return false;
	}

	public static function find_arr_params($array, $elemFind) {
		$count = count($elemFind);
		foreach($array as $e) {
			$i=0;
			foreach($elemFind as $findKey => $valKey) {
				if(strcasecmp($e[$findKey], $valKey) == 0) {
					$i++;
					if($count==$i) {
						return $e;
					}
				}
			}
		}
		return false;
	}
}