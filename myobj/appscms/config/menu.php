<?php
//sys
$menu = array(
	'classes'=>array('label'=>'classes', 'url'=>array('admin/objects/models/classes')),
	'properties'=>array('label'=>'properties', 'url'=>array('admin/objects/models/properties')),
	'mvc'=>array(
		'label'=>'mvc','items'=>array(
			'templates'=>array('label'=>'templates','url'=>array('admin/objects/class/templates_sys')),
			'views'=>array('label'=>'views','url'=>array('admin/objects/class/views_sys/')),
			'nav'=>array('label'=>'nav','url'=>array('admin/objects/class/navigation_sys/&usercontroller=usernav')),
			'controllers'=>array('label'=>'controllers','url'=>array('admin/objects/class/controllersnav_sys/')),
		),
	),
	'groups'=>array('label'=>'groups', 'url'=>array('admin/objects/class/groups_sys/')),
	'ui'=>array(
		'label'=>'UI','items'=>array(
			'user'=>array(
					'label'=>'user','url'=>array('admin/objects/models/user'),'items'=>array(
						'group'=>array('label'=>'group','url'=>array('admin/objects/models/group')),
						'userpasport'=>array('label'=>'userpasport','url'=>array('admin/objects/models/userpasport')),
					),
			),
			'storage files'=>array('label'=>'storage files','url'=>array('admin/objects/models/storagef')),
			'db damps'=>array('label'=>'db damps','url'=>array('admin/objects/class/db_dump_sys','usercontroller'=>'userdbdamp')),
		),
	),
	'logout'=>array('label'=>'logout ('.Yii::app()->user->name.')', 'url'=>array('admin/logout/')),
);


Yii::app()->params['api_conf_menu'] = $menu;
apicms\utils\importRecursName('application.modules.myobj.appscms.config.user','menu_*',true);
$menu = Yii::app()->params['api_conf_menu'];
unset(Yii::app()->params['api_conf_menu']);
return $menu;