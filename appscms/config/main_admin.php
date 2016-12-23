<?php
use MYOBJ\appscms\core\base\SysUtils;

$tables_db_dump = array(
	//links
	'cmsplus_linksobjectsallmy',
	'cmsplus_linksobjectsallmy_links',
	'cmsplus_linksobjectsallsystem',
	'cmsplus_linksobjectsallsystem_links',
	//my obj
	'cmsplus_myobjheaders',
	'cmsplus_myobjlines',
	'cmsplus_myobjheaders_lines',
	//sys obj
	'cmsplus_systemobjheaders',
	'cmsplus_systemobjlines',
	'cmsplus_systemobjheaders_lines',
	//classes
	'cmsplus_uclasses',
	'cmsplus_objproperties',
	'cmsplus_uclasses_association',
	'cmsplus_uclasses_objproperties',
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

$main_admin = array(
	'controlui' => array(
		'objects' => array( //columns header type model, controller inside myobj/controllers/cms
			'conf_ui_classes' => $objects,
			'models' => $models,
		),
		'ui' => $ui,
		'menu' => $menu,
	),

	'path_db_dump_files' => 'dbdump',
	'path_db_dump_tables' => $tables_db_dump,

	'countelements' => 20,
	'countpage' => 10,
);

return $main_admin;
