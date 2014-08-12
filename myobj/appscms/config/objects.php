<?php
$classes_system = array(
	'group'=>'groups_sys',
	'template'=>'templates_sys',
	'view'=>'views_sys',
	'handle'=>'handle_sys',
	'navigation'=>'navigation_sys',
	'params'=>'param_sys',
	'controllersnav'=>'controllersnav_sys',
	'db_dump'=>'db_dump_sys',
);

$objects = array(
	$classes_system['handle'] => array(
		'cols' => array('id'=>'id','name'=>'namehandle','vp1'=>'id view'),
		'AttributeLabels' => array('vp1'=>'idview'),
		'groups_read' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
		'groups_write' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
	),
	$classes_system['navigation'] => array(
		'cols' => array('id'=>'id','name'=>'name','vp2'=>'codename','vp1'=>'top','bp1'=>'visible','sort'=>'sort'),
		'AttributeLabels' => array('vp2'=>'codename','content'=>'description','vp1'=>'top','bp1'=>'visible'),
		'controller' => array('usernav'=>'nav_sys.php','default'=>''),
		'groups_read' => null,
		'groups_write' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
	),
	$classes_system['group'] => array(
		'cols' => array('id'=>'id','name'=>'name','vp1'=>'outside identifier','vp2'=>'codename',),
		'AttributeLabels' => array('vp1'=>'outside-id_group', 'content'=>'description', 'vp2'=>'codename'),
		'groups_read' => null,
		'groups_write' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
	),
	$classes_system['template'] => array(
		'cols' => array('id'=>'id','name'=>'name template','vp1'=>'patch'),
		'AttributeLabels' => array('vp1'=>'patch_template', 'content'=>'description'),
		'editForm' => array('name','vp1','content'), //в админке видим только поля
		'groups_read' => null,
		'groups_write' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
	),
	$classes_system['view'] => array(
		'cols' => array('id'=>'id','name'=>'name view','content'=>'description','vp1'=>'patch',),
		'AttributeLabels' => array('vp1'=>'patch_view', 'content'=>'description'),
		'editForm' => array('name','vp1','content','edit_file_template'),
		'groups_read' => null,
		'groups_write' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
	),
	$classes_system['params'] => array(
		'cols' => array('id'=>'id','name'=>'name','vp1'=>'codename'),
		'AttributeLabels' => array('vp1'=>'codename'),
		'groups_read' => null,
		'groups_write' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
	),
	$classes_system['controllersnav'] => array(
		'cols' => array('id'=>'id','name'=>'name'),
		'AttributeLabels' => array('vp1'=>'patch', 'content'=>'description'),
		'groups_read' => null,
		'groups_write' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
	),
	$classes_system['db_dump'] => array(
		'cols' => array('id'=>'id','name'=>'name'),
		'AttributeLabels' => array('vp1'=>'patch'),
		'groups_read' => null,
		'groups_write' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
	),
);
/*
 * namemodel -название модели таблицы в которой лежат объекты
 * namelinksmodel - (модель для ссылок) если в классе будут предусмотренны ссылки на другие объекты,
 * если null ссылки для объектов этого табличного пространства не предусмотренны
 */
$set_spaces['1'] = array('namemodel'=>'MyObjHeaders','namelinksmodel'=>'linksObjectsAllMy'); //пользовательские классы
$set_spaces['2'] = array('namemodel'=>'SystemObjHeaders','namelinksmodel'=>'linksObjectsAllSystem'); //системные классы

//на данный момент такое решение, так как тесты нужно делать, ??? возможно нужко как то перенести все конфиги в папку tests
if(true) {
	$set_spaces['777'] = array('namemodel'=>'TestAbsBaseObjHeaders','namelinksmodel'=>'linksObjectsAllTestAbsBase');
}

Yii::app()->params['api_conf_objects'] = $objects;
Yii::app()->params['api_conf_spaces'] = $set_spaces;

apicms\utils\importRecursName('MYOBJ.appscms.config.user','objects_*',true);
$objects = Yii::app()->params['api_conf_objects'];
$set_spaces = Yii::app()->params['api_conf_spaces'];
unset(Yii::app()->params['api_conf_objects']);
return array($objects,$set_spaces,$classes_system);