<?php
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

$ui = apicms\utils\importRecursName('MYOBJ.appscms.config','UI.php',true,true);
$menu = apicms\utils\importRecursName('MYOBJ.appscms.config','menu.php',true,true);
$none_del = apicms\utils\importRecursName('MYOBJ.appscms.config','nonedel.php',true,true);

$main_admin = array(
	'controlui' => array(
		'ui' => $ui,
		'menu' => $menu,
		'none_del' => $none_del,
	),

	'sys_db_type_InnoDB'=>true, //в случае true не будет делать лишний запрос для удаления ссылок MTM при работе с AbsModel::clearMTMLink
	'path_db_dump_files' => 'dbdump',
	'path_db_dump_tables' => $tables_db_dump,

	'countelements' => 50,
	'countpage' => 10,
);

//возможно дополнение конфигурации $main_admin дополнительными пользовательскими параметрами
Yii::app()->params['api_conf_main_user_admin'] = array();
apicms\utils\importRecursName('MYOBJ.appscms.config.user','main_admin_*',true);
$main_user = Yii::app()->params['api_conf_main_user_admin'];
$main_admin['user_conf_admin'] = $main_user;
unset(Yii::app()->params['api_conf_main_user_admin']);
return $main_admin;
