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

		$content = $tamplate['pagination'];
		$countlinks = (int)(ceil($countlinks / (float)($count_elems)));
		$finc = $indexpage % $count_pages;
		$idnext = $indexpage + 2;
		$idprev = $indexpage;
		$idrightnext = $indexpage + $count_pages + 1 - $finc;
		$idleftnext = $indexpage - $finc;

		if ($finc == 0) $startslice = $indexpage;
		else           $startslice = $indexpage - $finc;
		if ($flagpro == true && $indexpage > $countleft) $startslice = $indexpage - $countleft;

		$rangenorm = array_slice(range(0, ($countlinks - 1)), $startslice, $count_pages);

		$in_start = '';
		if(isset($tamplate['in_start']) && $startslice > 0) {
			$in_start = $func_getlink(sprintf($tamplate['in_start'], $urlp), $idleftnext);
		}
		$content = str_replace('{{IN_START}}', $in_start, $content);

		$in_left = '';
		if(isset($tamplate['in_left']) && $indexpage != 0) {
			$in_left = $func_getlink(sprintf($tamplate['in_left'], $urlp), $idprev);
		}
		$content = str_replace('{{IN_LEFT}}', $in_left, $content);

		$elements = '';
		foreach($rangenorm as $i) {
			$actclassl = '';
			if ($indexpage == $i) $actclassl = $tamplate['action'];

			$idthisutl = ($i + 1);

			$elements .= $func_getlink(sprintf($tamplate['elements'], $actclassl, $idthisutl), $idthisutl);
		}
		$content = str_replace('{{ELEMENTS}}', $elements, $content);

		$in_right = '';
		if(isset($tamplate['in_right']) && $indexpage != $countlinks - 1) {
			$in_right = $func_getlink(sprintf($tamplate['in_right'], $urlp), $idnext);
		}
		$content = str_replace('{{IN_RIGHT}}', $in_right, $content);

		$in_end = '';
		if(isset($tamplate['in_end']) && $idthisutl < $countlinks) {
			$in_end = $func_getlink(sprintf($tamplate['in_end'], $urlp), $idrightnext);
		}
		$content = str_replace('{{IN_END}}', $in_end, $content);


		return $content;
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
				self::treelem($aArr, $indexObjId, $keyId, $keyTop, $func, $tmpLeft);
			}
		}
		return $tmpArr;
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