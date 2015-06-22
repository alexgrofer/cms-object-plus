<?php

return array(
	'modules'=>array(
		"myobj",
	),

	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
			'class' => 'application.modules.myobj.components.WebUser',
		),
		'authManager'=>array(
			'class' => 'application.modules.myobj.components.PhpAuthManager',
			'defaultRoles' => array('guest'),
		),
	),
	'urlManager'=>array(
		'urlFormat'=>'path',
		'showScriptName'=>false,
		'rules'=>array(
			//cms
			'myobj/admin/<url:.*>'=>'myobj/admin/<url>',
			//tests
			'myobj/tests/<url:.*>'=>'myobj/tests/<url>',
			//project is in folder
			//'<controller>/<action>'=>'myobj/nameProjectFolder/<controller>/<action>',
			'showScriptName'=>false,
		),
	),
);
