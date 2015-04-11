<?php
return array(
	/*
	 * namemodel -название модели таблицы в которой лежат объекты
	 * --обобщенные классы
	 * Класс в пространстве Header которого могут храниться разные классы (т.е в одной таблице)
	 * nameModelLinks - модель типов ссылок, может быть несколько, по умолчанию AbsBaseHeaders::NAME_TYPE_LINK_BASE
	 */
	'1' => array('namemodel'=>'MyObjHeaders','nameModelLinks'=>['base'=>'linksObjectsMy']), //пользовательские обобщенные классы
	'2' => array('namemodel'=>'SystemObjHeaders','nameModelLinks'=>['base'=>'linksObjectsSystem']), //системные обобщенные классы
	//модели можно хранить в отдельных таблицах при необходимости
	'3' => array('namemodel'=>'NavigateSystemObjHeaders','nameModelLinks'=>['base'=>'linksObjectsSystem', 'handle' => 'linksObjectsSystemHandle']),
	'4' => array('namemodel'=>'ParamSystemObjHeaders',   'nameModelLinks'=>['base'=>'linksObjectsSystem']),
	'5' => array('namemodel'=>'HandleSystemObjHeaders',  'nameModelLinks'=>['base'=>'linksObjectsSystem']),
	'6' => array('namemodel'=>'ViewSystemObjHeaders',    'nameModelLinks'=>['base'=>'linksObjectsSystem']),
	'7' => array('namemodel'=>'TemplateSystemObjHeaders','nameModelLinks'=>['base'=>'linksObjectsSystem']),
	//test
	'9' => array('namemodel'=>'TestObjHeaders', 'nameModelLinks'=>['base'=>'linksObjectsMy']),
);