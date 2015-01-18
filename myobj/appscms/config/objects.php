<?php
$objects = array(
	'handle_sys' => array(
		//'cols' => array('id'=>'id','name'=>'namehandle','vp1'=>'id view','vp2'=>'id template'),
		//'AttributeLabels' => array('vp1'=>'id view', 'vp2'=>'id template'),
		'relation' => array(
			'template'=>array('templates_sys', 'handles', true),
			'view'=>array('views_sys', 'handles', true),
		),
		'namemodel' => 'HandleSystemObjHeaders',
		'group_read' => 'administrator',
		'group_write' => 'administrator',
	),
	'navigation_sys' => array(
		//'cols' => array('id'=>'id','name'=>'name','vp2'=>'codename','vp3'=>'action','vp1'=>'top','bp1'=>'visible','sort'=>'sort'),
		//'AttributeLabels' => array('vp2'=>'codename','content'=>'description','vp1'=>'top','vp3'=>'controller action','bp1'=>'visible'),
		'controller' => array('usernav'=>'nav_sys.php','default'=>''),
		//'relation' => array('test_relat_objcts'=>array('testtablehm','myobjheader')),
		'relation' => array(
			'templateDefault'=>array('templates_sys', 'navigationsDef', true),
			'templateMobileDefault'=>array('templates_sys', 'navigationsMobileDef', true),
			'params'=>array('param_sys', 'navigate', true),
		),
		//array('название в реляции'=>array('псевдоним название модели(или codename класса)', 'название обратной реляции', true или ничего -ссылка не на модель а другой клас)
		'namemodel' => 'NavigateSystemObjHeaders',
		'group_read' => 'administrator',
		'group_write' => 'administrator',
	),
	'groups_sys' => array(
		'namemodel' => 'GroupSystemObjHeaders',
		'group_read' => 'administrator',
		'group_write' => 'administrator',
	),
	'templates_sys' => array(
		//'cols' => array('id'=>'id','name'=>'name template','vp1'=>'patch'),
		//'AttributeLabels' => array('vp1'=>'patch_template', 'content'=>'description'),
		//'editForm' => array('name','vp1','content'), //в админке видим только поля
		//'relation' => array('myobjheader'=>array('news_example','test_relat_objcts')),
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
		//'cols' => array('id'=>'id','name'=>'name view','content'=>'description','vp1'=>'patch',),
		//'AttributeLabels' => array('vp1'=>'patch_view', 'content'=>'description'),
		//'editForm' => array('name','vp1','content','edit_file_template'),
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
/*
 * namemodel -название модели таблицы в которой лежат объекты
 * --обобщенные классы
 * Класс в пространстве Header которого могут храниться разные классы (т.е в одной таблице)
 * nameModelLinks - модель типов ссылок, может быть несколько, по умолчанию AbsBaseHeaders::NAME_TYPE_LINK_BASE
 */
$set_spaces['1'] = array('namemodel'=>'MyObjHeaders','nameModelLinks'=>['base'=>'linksObjectsMy']); //пользовательские обобщенные классы
$set_spaces['2'] = array('namemodel'=>'SystemObjHeaders','nameModelLinks'=>['base'=>'linksObjectsSystem']); //системные обобщенные классы
/*
 * модели можно хранить в отдельных таблицах при необходимости
 */
$set_spaces['3'] = array('namemodel'=>'NavigateSystemObjHeaders','nameModelLinks'=>['base'=>'linksObjectsSystem', 'handle' => 'linksObjectsSystemHandle']);
$set_spaces['4'] = array('namemodel'=>'ParamSystemObjHeaders',   'nameModelLinks'=>['base'=>'linksObjectsSystem']);
$set_spaces['5'] = array('namemodel'=>'HandleSystemObjHeaders',  'nameModelLinks'=>['base'=>'linksObjectsSystem']);
$set_spaces['6'] = array('namemodel'=>'ViewSystemObjHeaders',    'nameModelLinks'=>['base'=>'linksObjectsSystem']);
$set_spaces['7'] = array('namemodel'=>'TemplateSystemObjHeaders','nameModelLinks'=>['base'=>'linksObjectsSystem']);
$set_spaces['8'] = array('namemodel'=>'GroupSystemObjHeaders',   'nameModelLinks'=>['base'=>'linksObjectsSystem']);

//test
$set_spaces['9'] = array('namemodel'=>'TestObjHeaders', 'nameModelLinks'=>['base'=>'linksObjectsMy']);

Yii::app()->params['api_conf_objects'] = $objects;
Yii::app()->params['api_conf_spaces'] = $set_spaces;

apicms\utils\importRecursName('MYOBJ.appscms.config.user','objects_*',true);
$objects = Yii::app()->params['api_conf_objects'];
$set_spaces = Yii::app()->params['api_conf_spaces'];
unset(Yii::app()->params['api_conf_objects']);
return array($objects,$set_spaces);