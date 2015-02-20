<?php
namespace MYOBJ\appscms\core\base;

class SysUtils
{
	public static function arrvaluesmodel($listobjects, $namekey)
	{
		if (!is_array($listobjects)) $listobjects = array($listobjects);
		$arrv = array();
		foreach ($listobjects as $object) {
			$arrv[] = $object->$namekey;
		}
		return $arrv;
	}

	public static function pagination($indexpage, $countlinks, $count_elems, $count_pages, $urlp, $flagpro, $tamplate, $countleft = 5)
	{
		$func_getlink = function ($linkstr, $index) {
			$str = str_replace('T_ID_PAGE', $index, $linkstr);
			return $str;
		};

		$countlinks = (int)(ceil($countlinks / (float)($count_elems)));
		$linksp = '';
		$linkspt = '';
		$finc = $indexpage % $count_pages;
		$idnext = $indexpage + 2;
		$idprev = $indexpage;
		$idrightnext = $indexpage + $count_pages + 1 - $finc;
		$idleftnext = $indexpage - $finc;

		if ($finc == 0) $startslice = $indexpage;
		else           $startslice = $indexpage - $finc;
		if ($flagpro == true && $indexpage > $countleft) $startslice = $indexpage - $countleft;

		$rangenorm = array_slice(range(0, ($countlinks - 1)), $startslice, $count_pages);

		if ($startslice > 0) {
			$linksp .= $func_getlink(sprintf($tamplate['nextleft'], $urlp), $idleftnext);
		}

		if ($indexpage != 0) {
			$linkspt .= $func_getlink(sprintf($tamplate['prevpg'], $urlp), $idprev);
		}

		foreach ($rangenorm as $i) {
			$actclassl = '';
			if ($indexpage == $i) $actclassl = $tamplate['action'];

			$idthisutl = ($i + 1);

			$linksp .= $func_getlink(sprintf($tamplate['elem'], $actclassl, $idthisutl), $idthisutl);
		}

		if ($indexpage != $countlinks - 1) {
			$linkspt .= $func_getlink(sprintf($tamplate['nextpg'], $urlp), $idnext);
		}
		if ($idthisutl < $countlinks) {
			$linksp .= $func_getlink(sprintf($tamplate['nextright'], $urlp), $idrightnext);
		}


		$pagination = sprintf($tamplate['pagination'], $linkspt, $linksp);
		return $pagination;
	}

	public static function treelem($aArr, $atop, $keyId, $keyTop, $func, $tmpLeft = null)
	{
		$atop = (string)$atop;
		static $tmpArr = array();
		static $saveTopIndex = null;
		if ($saveTopIndex === null) {
			$saveTopIndex = $atop;
		}
		$topTmp = null;
		foreach ($aArr as $obj) {
			$indexObjId = (string)is_array($obj) ? $obj[$keyId] : $obj->$keyId;
			$indexObjTop = (string)is_array($obj) ? $obj[$keyTop] : $obj->$keyTop;
			if ($indexObjTop == $atop) {
				if ($atop != $saveTopIndex && $topTmp != $atop) {
					$tmpLeft = $func($tmpLeft);
				}
				$topTmp = $atop;
				$tmpArr[] = array('obj' => $obj, 'left' => $tmpLeft);
				treelem($aArr, $indexObjId, $keyId, $keyTop, $func, $tmpLeft);
			}
		}
		return $tmpArr;
	}

	public static function GUID()
	{
		if (function_exists('com_create_guid') === true) {
			return trim(com_create_guid(), '{}');
		}

		return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
	}

	public static function normalAliasModel($name_ModelORClass)
	{
		$arrConfObj = \Yii::app()->appcms->config['controlui']['objects'];
		if (isset($arrConfObj['models'][$name_ModelORClass])) {
			return $arrConfObj['models'][$name_ModelORClass];
		}
		if (isset($arrConfObj['conf_ui_classes'][$name_ModelORClass])) {
			return $arrConfObj['conf_ui_classes'][$name_ModelORClass];
		}
		return array();
	}

	/**
	 * @param $alias алиас директория в которой будет находиться искомый файл-ы
	 * @param bool $fileName название файла file.php или маска для файлов inc_*
	 * @param bool $isReturn необходимо получить из файла данные в нем должно быть return
	 * @param bool $one если это не маска а один файл нужно установить это значение в true иначе вернет вложенный массив
	 * @return array вернет массив если был установлен параметр $isReturn, если $one то только return контент
	 */
	public static function importRecursName($alias, $fileName = false, $isReturn = false, $one=false) {
		$path = \yii::getPathOfAlias($alias);
		$arr_glob_files = glob($path . DIRECTORY_SEPARATOR . $fileName);
		$r_a = array();
		foreach ($arr_glob_files as $file) {
			$out = require($file);
			if ($isReturn) $r_a[] = $out;
		}
		if (count($r_a)) return ($one)?$r_a[0]:$r_a;
	}

	public static function array_array_merge($arrays) {
		$array_merge = [];
		foreach($arrays as $array) {
			$array_merge += $array;
		}
		return $array_merge;
	}
}