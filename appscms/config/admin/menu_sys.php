<?php
return array(
	'classes'=>array('label'=>'classes', 'url'=>array('admin/objects/models/classes'),
		'items'=>array(
			'properties'=>array('label'=>'properties', 'url'=>array('admin/objects/models/properties')),
		),
	),
	'mvc'=>array(
		'label'=>'mvc','url'=>array('#'),'items'=>array(
			'templates'=>array('label'=>'templates','url'=>array('admin/objects/class/templates_sys')),
			'views'=>array('label'=>'views_handles','url'=>array('admin/objects/class/views_sys/')),
			'nav'=>array('label'=>'nav','url'=>array('admin/objects/class/navigation_sys', 'usercontroller'=>'usernav')),
		),
	),
	'conf'=>array(
		'label'=>'conf','url'=>array('#'),'items'=>array(
			'user'=>array(
				'label'=>'user','url'=>array('admin/objects/models/user'),'items'=>array(
					'group'=>array('label'=>'group','url'=>array('admin/objects/models/group')),
				),
			),
			'db damps'=>array('label'=>'db damps','url'=>array('admin/objects/class/db_dump_sys','usercontroller'=>'userdbdamp')),
		),
	),
	'logout'=>array('label'=>'logout ('.Yii::app()->user->name.')', 'url'=>array('admin/logout/')),
);