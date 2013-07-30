<?php
$objects = array(
	'systemObjHeaders' => array(
		'default' => array(
			'cols' => array('id'=>'id','name'=>'name'),
			'order_by_def' => array('id desc'),
		),
		$classes_system['handle'] => array(
			'cols' => array('id'=>'id','name'=>'namehandle','vp1'=>'id view'),
			'edit' => array('name', 'sort', array('vp1','idview')),
			'groups_read' => null,
			'groups_write' => null,
		),
		$classes_system['navigation'] => array(
			'cols' => array('id'=>'id','vp1'=>'top','name'=>'name','vp2'=>'codename','bp1'=>'visible','sort'=>'sort'),
			'edit' => array('name', 'sort', array('vp2','codename'),array('content','description'),array('vp1','top'),array('bp1','visible')),
			'controller' => array('usernav'=>'admin/nav_sys.php','default'=>''),
			'groups_read' => null,
			'groups_write' => null,
		),
		$classes_system['group'] => array(
			'cols' => array('id'=>'id','name'=>'name','vp1'=>'outside identifier','vp2'=>'codename',),
			'edit' => array('name', array('vp1','outside-id_group'), array('content','description'), array('vp2','codename')),
			'groups_read' => null,
			'groups_write' => null,
		),
		$classes_system['template'] => array(
			'cols' => array('id'=>'id','name'=>'name template'),
			'edit' => array('name', array('vp1','patch_template'), array('content','description')),
			'groups_read' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
			'groups_write' => null,
		),
		$classes_system['view'] => array(
			'cols' => array('id'=>'id','name'=>'name view'),
			'edit' => array('name', array('vp1','patch_view'), array('content','description')),
			'groups_read' => null,
			'groups_write' => null,
		),
		$classes_system['params'] => array(
			'cols' => array('id'=>'id','name'=>'name','vp1'=>'codename'),
			'edit' => array('name', array('vp1','codename'),'content'),
			'groups_read' => null,
			'groups_write' => null,
		),
		$classes_system['controllersnav'] => array(
			'cols' => array('id'=>'id','name'=>'name'),
			'edit' => array('name', array('vp1','patch'), array('content','description')),
			'groups_read' => null,
			'groups_write' => null,
		),
		$classes_system['db_dump'] => array(
			'cols' => array('id'=>'id','name'=>'name'),
			'edit' => array('name', array('vp1','patch')),
			'groups_read' => null,
			'groups_write' => null,
		),
	),
	'myObjHeaders' => array(
		'default' => array(
			'cols' => array('id'=>'id','name'=>'name'), //колонки в списке
			//названия гуппы outside identifier
			'groups_read' => null,
			'groups_write' => null,
			'order_by_def' => array('id desc'), //сортировка по умолчанию
		),
		//headers links example news
		'news' => array(
			'cols' => array('id'=>'id','name'=>'name'),
			'cols_props' => array('text_news'=>'text_news','annotation_news'=>'annotation_news'), //колонки-свойства объектов
			'find' => array('id', 'text_news__prop'), //разрешенные для поиска
			'order_by' => array('id','name', 'text_news__prop', 'annotation_news__prop'), //сортирова в меню
			'order_by_def' => array('annotation_news__prop desc'),
		),
		'news_section' => array(
			'cols' => array('id'=>'id','name'=>'name'),
			'cols_props' => array('codename_news_section'=>'codename_news_section'),
		),
	),
);
/*
 * namemodel -название модели таблицы в которой лежат объекты
 * namelinksmodel - (модель для ссылок) если в классе будут предусмотренны ссылки на другие объекты,
 * если null ссылки для объектов этого табличного пространства не предусмотренны
 */
$set_spaces['1'] = array('namemodel'=>'myObjHeaders','namelinksmodel'=>'linksObjectsAllMy'); //пользовательские классы
$set_spaces['2'] = array('namemodel'=>'systemObjHeaders','namelinksmodel'=>'linksObjectsAllSystem'); //системные классы
require(dirname(__FILE__).'/user/objects.php');
$objects = array_merge($objects,$objects_user);