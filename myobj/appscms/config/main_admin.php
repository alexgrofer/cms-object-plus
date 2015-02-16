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

$objects = SysUtils::array_array_merge(
	SysUtils::importRecursName('MYOBJ.appscms.config.admin','objects_*',true)
);

$models = SysUtils::array_array_merge(
	SysUtils::importRecursName('MYOBJ.appscms.config.admin','models_*',true)
);

$ui = SysUtils::array_array_merge(
	SysUtils::importRecursName('MYOBJ.appscms.config.admin','UI_*',true)
);

$menu = SysUtils::array_array_merge(
	SysUtils::importRecursName('MYOBJ.appscms.config.admin','menu_*',true)
);

$none_del = SysUtils::array_array_merge(
	SysUtils::importRecursName('MYOBJ.appscms.config.admin','nonedel_*',true)
);

$main_admin = array(
	'controlui' => array(
		'objects' => array( //columns header type model, controller inside myobj/controllers/cms
			'conf_ui_classes' => $objects,
			'models' => $models,
		),
		'ui' => $ui,
		'menu' => $menu,
		'none_del' => $none_del,
	),

	'path_db_dump_files' => 'dbdump',
	'path_db_dump_tables' => $tables_db_dump,

	'countelements' => 20,
	'countpage' => 10,
);

return $main_admin;
