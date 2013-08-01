<?php

$objects_user = array(
	//codename class
	//headers links example news
	'news_example' => array(
		'cols' => array('id'=>'id','name'=>'name'),
		'cols_props' => array('text_news_example'=>'text_news_example','annotation_news_example'=>'annotation_news_example'), //колонки-свойства объектов
		'find' => array('id', 'text_news_example__prop'), //разрешенные для поиска
		'order_by' => array('id','name', 'text_news_example__prop', 'annotation_news_example__prop'), //сортирова в меню
		'order_by_def' => array('annotation_news_example__prop desc'),
	),
	'news_section_example' => array(
		'cols' => array('id'=>'id','name'=>'name'),
		'cols_props' => array('codename_news_section_example'=>'codename_news_section_example'),
	),
);
/*
 * namemodel -название модели таблицы в которой лежат объекты
 * namelinksmodel - (модель для ссылок) если в классе будут предусмотренны ссылки на другие объекты,
 * если null ссылки для объектов этого табличного пространства не предусмотренны
 */
$set_spaces['1'] = array('namemodel'=>'myObjHeaders','namelinksmodel'=>'linksObjectsAllMy');