<?php
$objects = array(
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
Yii::app()->params['api_conf_objects'] =  array_merge(Yii::app()->params['api_conf_objects'],$objects);

//$set_spaces['30'] = array('namemodel'=>'exampleObjHeaders','namelinksmodel'=>'linksExampleObjHeaders');
//Yii::app()->params['api_conf_spaces'] =  array_merge(Yii::app()->params['api_conf_spaces'],$set_spaces);