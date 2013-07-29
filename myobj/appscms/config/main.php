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

$arrayClassesFilesStorageProc = array(
	'classFilesStorageDefault' => 'Default',
);

require(dirname(__FILE__).'/objects.php');
require(dirname(__FILE__).'/models.php');
require(dirname(__FILE__).'/UI.php');
require(dirname(__FILE__).'/menu.php');
require(dirname(__FILE__).'/none_del.php');

$main = array(
	'controlui' => array(
		'objects' => array( //columns header type model, controller inside myobj/controllers/cms
			'headers_spaces' => $objects,
			'models' => $models,
		),
		'ui' => $ui,
		'menu' => $menu,
		'none_del' => $none_del,
	),
	'spacescl' => $set_spaces,
	'TYPES_MYFIELDS_CHOICES' => array(
		'1'=>'str',
		'2'=>'text',
		'3'=>'html',
		'4'=>'datetime',
		'5'=>'url',
		'6'=>'email',
		'7'=>'bool',
	),
	'TYPES_COLUMNS' => array(
		'1'=>'upcharfield',
		'2'=>'uptextfield',
		'3'=>'uptextfield',
		'4'=>'updatetimefield',
		'5'=>'uptextfield',
		'6'=>'upcharfield',
		'7'=>'upcharfield',
	),
	'TYPES_MYFIELDS' => array(
		'str'=>'text',
		'text'=>'textarea',
		'html'=>'textarea',
		'datetime'=>'text',
		'url'=>'text',
		'ip'=>'text',
		'email'=>'text',
		'bool'=>'checkbox',
	),
	'homeDirStoreFile' => 'media/upload/storefile',
	'ClassesFilesStorageProc'=> $arrayClassesFilesStorageProc,
	'rulesvalidatedef' => array( //Valid values include 'string', 'integer', 'float', 'array', 'date', 'time' and 'datetime'.
		'str' => '
type
type=>string
',
		'datetime' => '
type
type=>datetime
datetimeFormat=>yyyy-MM-dd hh:mm:ss
',
	),
'path_db_dump_files' => 'dbdump',
'classes_system' => $classes_system,
'language_def' => 'en',
'languages' => array('ru', 'en', 'th', 'vi', 'de'),
'countelements' => 10,
'countpage' => 10,
'bdcms_pref' => 'setcms_',
'objindexname' => 'index',
);
require(dirname(__FILE__).'/user/main.php');
$main = array_merge($main,$main_user);
return $main;
