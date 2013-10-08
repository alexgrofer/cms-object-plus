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

$tables_db_dump = array(
	//links
	'setcms_linksobjectsallmy',
	'setcms_linksobjectsallmy_links',
	'setcms_linksobjectsallsystem',
	'setcms_linksobjectsallsystem_links',
	//my obj
	'setcms_myobjheaders',
	'setcms_myobjlines',
	'setcms_myobjheaders_lines',
	//sys obj
	'setcms_systemobjheaders',
	'setcms_systemobjlines',
	'setcms_systemobjheaders_lines',
	//classes
	'setcms_uclasses',
	'setcms_objproperties',
	'setcms_uclasses_association',
	'setcms_uclasses_objproperties',
);

list($objects,$set_spaces) = require(dirname(__FILE__).'/objects.php');
$models = apicms\utils\importRecursName('application.modules.myobj.appscms.config','models.php',true,true);
$ui = apicms\utils\importRecursName('application.modules.myobj.appscms.config','UI.php',true,true);
$menu = apicms\utils\importRecursName('application.modules.myobj.appscms.config','menu.php',true,true);
$none_del = apicms\utils\importRecursName('application.modules.myobj.appscms.config','nonedel.php',true,true);

$main = array(
	'controlui' => array(
		'objects' => array( //columns header type model, controller inside myobj/controllers/cms
			'conf_ui_classes' => $objects,
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
	'sys_db_type_InnoDB'=>true, //в случае true не будет делать лишний запрос для удаления ссылок MTM при работе с AbsModel::clearMTMLink
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
'path_db_dump_tables' => $tables_db_dump,
'classes_system' => $classes_system,
'language_def' => 'en',
'languages' => array('ru', 'en', 'th', 'vi', 'de'),
'countelements' => 10,
'countpage' => 10,
'bdcms_pref' => 'setcms_',
'objindexname' => 'index', //id or codename nac obj class
);
Yii::app()->params['api_conf_main'] = $main;
//возможно дополнение конфигурации $main дополнительными параметрами
apicms\utils\importRecursName('application.modules.myobj.appscms.config.user','main_*',true);
$main = Yii::app()->params['api_conf_main'];
unset(Yii::app()->params['api_conf_main']);
return $main;
