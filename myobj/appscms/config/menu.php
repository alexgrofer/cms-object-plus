<?php
//sys
$menu = array(
	'classes'=>array('label'=>'classes', 'url'=>array('admin/objects/models/classes')),
	'properties'=>array('label'=>'properties', 'url'=>array('admin/objects/models/properties')),
	'mvc'=>array(
		'label'=>'mvc','items'=>array(
			'templates'=>array('label'=>'templates','url'=>'admin/objects/class/templates_sys/'),
			'views'=>array('label'=>'views','url'=>'admin/objects/class/views_sys/'),
			'nav'=>array('label'=>'nav','url'=>'admin/objects/class/navigation_sys/&usercontroller=usernav'),
			'controllers'=>array('label'=>'controllers','url'=>'admin/objects/class/controllersnav_sys/'),
		),
	),
	'groups'=>array('label'=>'groups', 'url'=>'admin/objects/class/groups_sys/'),
	'ui'=>array(
		'label'=>'UI','items'=>array(
			'user'=>array(
					'label'=>'user','items'=>array(
						'users'=>array('label'=>'users','url'=>'admin/objects/models/user'),
						'group'=>array('label'=>'group','url'=>'admin/objects/models/group'),
						'userpasport'=>array('label'=>'userpasport','url'=>'admin/objects/models/userpasport'),
					),
			),
		),
	),
	'logout'=>array('label'=>'logout ('.Yii::app()->user->name.')', 'url'=>'admin/logout/'),
);


Yii::app()->params['api_conf_menu'] = $menu;
apicms\utils\importRecursName('application.modules.myobj.appscms.config.user','menu_*',true);
$menu = Yii::app()->params['api_conf_menu'];
unset(Yii::app()->params['api_conf_menu']);
return $menu;