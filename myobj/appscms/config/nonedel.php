<?php
//запрет на удаление определенных классов и объектов, свойств
$nonedel = array(
	'classes' => array(
		'groups_sys',
		'templates_sys',
		'views_sys',
		'handle_sys',
		'navigation_sys',
		'param_sys',
		'controllersnav_sys',
		'db_dump_sys',
	),
	'objects' => array(
		'groups_sys' => array(
			'vp2'=>'admincms',
			'vp2'=>'guestsys',
			'vp2'=>'authorizedsys',
		),
	),
	'prop' => array(),
);

Yii::app()->params['api_conf_nonedel'] = $nonedel;

apicms\utils\importRecursName('MYOBJ.appscms.config.user','nonedel_*',true);
$nonedel = Yii::app()->params['api_conf_nonedel'];
unset(Yii::app()->params['api_conf_nonedel']);
return $nonedel;