<?php
return array(
	/*
	// как для моделей так и для объектов одинаково
	array(
		'кодовое название класса или алиас модели' => array(
		'cols' => array('id'=>'id','name'=>'namehandle','vp1'=>'id view','vp2'=>'id template'),
		'AttributeLabels' => array('vp1'=>'id view', 'vp2'=>'id template'),
		'relation' => array(
			'название в реляции'=>array('псевдоним название модели(или codename класса)',
			'название обратной реляции' null если ее нет,
			true или ничего - если true это будет ссылка не на модель а другой клас),
		),
		'namemodel' => 'название модели даже если это заголовок',
		'group_read' => 'группа для чтения',
		'group_write' => 'группа для записи',
		'controller' => array('usernav'=>'nav_sys.php','default'=>''), //возмодные контроллеры require
		'editForm' => array('name','vp1','content'), //в админке видим только эти поля
		//название дополнительных колонок в дочерней таблице MANY_MANY которые можем править при редактировании связанного объекта
		'selfobjrelationElements' => array('properties'=>array('test', 'test2')),
		'witch' => array('relattest', 'relattest.relattest2'), @todo сделать возможность witch
		'cols_props' => array('text_news_example'=>'text_news_example','annotation_news_example'=>'annotation_news_example'), //колонки-свойства объектов
		'find' => array('id', 'text_news_example__prop', 'annotation_news_example__prop'), //поиска в меню
		'order_by' => array('id','name', 'text_news_example__prop', 'annotation_news_example__prop'), //сортировка в меню
		'order_by_def' => array('text_news_example__prop desc'), //сортировка по умолчанию
	),
	*/
	'handle_sys' => array(
		'relation' => array(
			'template'=>array('templates_sys', 'handles', true),
			'view'=>array('views_sys', 'handles', true),
		),
		'namemodel' => 'HandleSystemObjHeaders',
		'group_read' => 'administrator',
		'group_write' => 'administrator',
	),
	'navigation_sys' => array(
		'controller' => array('usernav'=>'nav_sys.php','default'=>''),
		'relation' => array(
			'templateDefault'=>array('templates_sys', 'navigationsDef', true),
			'templateMobileDefault'=>array('templates_sys', 'navigationsMobileDef', true),
			'params'=>array('param_sys', 'navigate', true),
		),
		'namemodel' => 'NavigateSystemObjHeaders',
		'group_read' => 'administrator',
		'group_write' => 'administrator',
	),
	'templates_sys' => array(
		'relation' => array(
			'navigationsDef'=>array('navigation_sys','templateDefault', true),
			'navigationsMobileDef'=>array('navigation_sys','templateMobileDefault', true),
			'handles'=>array('handle_sys','template', true),
		),
		'namemodel' => 'TemplateSystemObjHeaders',
		'group_read' => 'administrator',
		'group_write' => 'administrator',
	),
	'views_sys' => array(
		'relation' => array(
			'handles'=>array('handle_sys','view', true),
		),
		'namemodel' => 'ViewSystemObjHeaders',
		'group_read' => 'administrator',
		'group_write' => 'administrator',
	),
	'param_sys' => array(
		'namemodel' => 'ParamSystemObjHeaders',
		'relation' => array('navigate'=>array('navigation_sys', 'params', true)),
		'group_read' => 'administrator',
		'group_write' => 'administrator',
	),
	'db_dump_sys' => array(
		'cols' => array('id'=>'id','name'=>'name'),
		'AttributeLabels' => array('vp1'=>'patch'),
		'group_read' => 'administrator',
		'group_write' => 'administrator',
	),
);